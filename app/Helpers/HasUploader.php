<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HasUploader
{
    private function upload(Request $request, string $input, ?string $oldFile = null, ?string $disk = null): ?string
    {
        $file = $request->file($input);
        $ext = $file->getClientOriginalExtension();
        $filename = now()->timestamp . '-' . rand(1, 1000) . '.' . $ext;

        $path = 'uploads/' . date('y') . '/' . date('m') . '/';
        $filePath = $path . $filename;

        // Delete old file if it exists
        if ($oldFile && Storage::disk($disk ?? config('filesystems.default'))->exists($oldFile)) {
            Storage::disk($disk ?? config('filesystems.default'))->delete($oldFile);
        }

        Storage::disk($disk ?? config('filesystems.default'))->put($filePath, file_get_contents($file));
        return $filePath;
    }

    private function uploadWithFileName(Request $request, string $input, ?string $oldFile = null, ?string $disk = null): ?string
    {
        $file = $request->file($input);
        $filename = $file->getClientOriginalName();

        $path = 'files/';
        $filePath = $path . $filename;

        // Delete old file if it exists
        if ($oldFile && Storage::disk($disk ?? config('filesystems.default'))->exists($oldFile)) {
            Storage::disk($disk ?? config('filesystems.default'))->delete($oldFile);
        }

        Storage::disk($disk ?? config('filesystems.default'))->put($filePath, file_get_contents($file));
        return $filePath;
    }

    private function multipleUpload(Request $request, string $input, array $oldFiles = [], ?string $disk = null): array
    {
        $uploadedFiles = [];

        foreach ($request->file($input) as $file) {
            $ext = $file->getClientOriginalExtension();
            $filename = now()->timestamp . '_' . uniqid() . '.' . $ext;

            $path = 'uploads/' . date('y') . '/' . date('m') . '/';
            $filePath = $path . $filename;

            Storage::disk($disk ?? config('filesystems.default'))->put($filePath, file_get_contents($file));
            $uploadedFiles[] = $filePath;
        }

        // Delete old files after successful upload
        foreach ($oldFiles as $oldFile) {
            if (Storage::disk($disk ?? config('filesystems.default'))->exists($oldFile)) {
                Storage::disk($disk ?? config('filesystems.default'))->delete($oldFile);
            }
        }

        return $uploadedFiles;
    }
}
