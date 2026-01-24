<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;

class DatabaseBackupController extends Controller
{
    /**
     * Display the backup page
     */
    public function index()
    {
        if (!auth()->user()) {
            return redirect()->back()->with('error', 'You have no permission to access.');
        }

        return view('business::backup.index');
    }

    /**
     * Download database backup
     */
    public function download(Request $request)
    {
        try {
            if (!auth()->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            // Get database configuration
            $dbHost = Config::get('database.connections.mysql.host');
            $dbName = Config::get('database.connections.mysql.database');
            $dbUser = Config::get('database.connections.mysql.username');
            $dbPass = Config::get('database.connections.mysql.password');
            $dbPort = Config::get('database.connections.mysql.port', '3306');

            // Create backup filename with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$dbName}_{$timestamp}.sql";
            $relativePath = "backups/{$filename}";
            $backupPath = storage_path('app/' . $relativePath);

            // Ensure directory exists
            Storage::disk('local')->makeDirectory('backups');

            // Prefer Process when proc_open is available; otherwise fallback to exec
            $success = false;
            if ($this->isProcOpenAvailable()) {
                $mysqldump = $this->findMysqldump();
                $command = [
                    $mysqldump,
                    "--host={$dbHost}",
                    "--port={$dbPort}",
                    "--user={$dbUser}",
                    '--single-transaction',
                    '--quick',
                    '--lock-tables=false',
                    "--result-file={$backupPath}",
                    $dbName,
                ];

                $process = new Process($command);
                $process->setTimeout(300); // 5 minutes
                $process->run(null, [
                    'MYSQL_PWD' => $dbPass, // safer than putting password on the CLI
                ]);

                $success = $process->isSuccessful();
            } elseif ($this->isExecAvailable()) {
                $mysqldump = $this->findMysqldump();
                $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

                if ($isWindows) {
                    // Windows: include password inline; quoting via escapeshellarg
                    $command = sprintf(
                        '"%s" --host=%s --port=%s --user=%s --password=%s --single-transaction --quick --lock-tables=false %s > %s',
                        $mysqldump,
                        escapeshellarg($dbHost),
                        escapeshellarg($dbPort),
                        escapeshellarg($dbUser),
                        escapeshellarg($dbPass),
                        escapeshellarg($dbName),
                        escapeshellarg($backupPath)
                    );
                } else {
                    // *nix: rely on MYSQL_PWD env variable via inline export
                    $command = sprintf(
                        'MYSQL_PWD=%s %s --host=%s --port=%s --user=%s --single-transaction --quick --lock-tables=false %s > %s',
                        escapeshellarg($dbPass),
                        escapeshellcmd($mysqldump),
                        escapeshellarg($dbHost),
                        escapeshellarg($dbPort),
                        escapeshellarg($dbUser),
                        escapeshellarg($dbName),
                        escapeshellarg($backupPath)
                    );
                }

                exec($command . ' 2>&1', $output, $returnVar);
                $success = ($returnVar === 0);
            } else {
                // No proc_open or exec: use PHP fallback immediately
                return $this->createBackupWithPHP($dbName, $filename);
            }

            if (!$success || !file_exists($backupPath) || filesize($backupPath) === 0) {
                // Fallback: Use PHP to create backup
                return $this->createBackupWithPHP($dbName, $filename);
            }

            // Return the file for download
            return response()->download($backupPath, $filename, [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fallback method to create backup using PHP when mysqldump is not available
     */
    private function createBackupWithPHP($dbName, $filename)
    {
        try {
            $relativePath = "backups/{$filename}";
            $backupPath = storage_path('app/' . $relativePath);
            Storage::disk('local')->makeDirectory('backups');
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;
            
            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: {$dbName}\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "SET time_zone = \"+00:00\";\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get CREATE TABLE statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "\n-- Table structure for table `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // Get table data
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $sql .= "-- Dumping data for table `{$tableName}`\n";
                    
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            return "'" . addslashes($value) . "'";
                        }, (array) $row);
                        
                        $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    
                    $sql .= "\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Write to file
            file_put_contents($backupPath, $sql);
            
            // Return the file for download
            return response()->download($backupPath, $filename, [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PHP Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attempt to locate mysqldump executable (handles Windows paths too).
     */
    private function findMysqldump(): string
    {
        $candidates = ['mysqldump'];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $candidates = array_merge($candidates, [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.27\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            ]);
        }

        foreach ($candidates as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        // Fallback to default; Process will surface an error if missing
        return 'mysqldump';
    }

    /**
     * Check if proc_open is available (not disabled in php.ini).
     */
    private function isProcOpenAvailable(): bool
    {
        if (!function_exists('proc_open')) {
            return false;
        }

        $disabled = ini_get('disable_functions');
        if ($disabled) {
            $list = array_map('trim', explode(',', $disabled));
            if (in_array('proc_open', $list, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if exec is available (not disabled in php.ini).
     */
    private function isExecAvailable(): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $disabled = ini_get('disable_functions');
        if ($disabled) {
            $list = array_map('trim', explode(',', $disabled));
            if (in_array('exec', $list, true)) {
                return false;
            }
        }

        return true;
    }
}
