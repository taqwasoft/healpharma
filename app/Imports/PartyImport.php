<?php

namespace App\Imports;

use App\Models\Party;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartyImport implements ToCollection
{
    protected $businessId;
    protected $existingPhones = [];
    protected $excelPhones = [];
    protected $errors = [];

    public function __construct($businessId)
    {
        $this->businessId = $businessId;

        // Cache existing phone numbers for this business
        $this->existingPhones = Party::where('business_id', $businessId)
            ->pluck('phone')
            ->filter()
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $name  = trim($row[0] ?? '');
                $type  = trim($row[1] ?? '');
                $phone = trim($row[2] ?? '');
                $email = trim($row[3] ?? '');
                $due   = (float)($row[4] ?? 0);

                // Validate required fields
                if (!$name || !$type) {
                    $this->errors[] = "Row " . ($index + 1) . ": Missing required field (name/Party type)";
                    continue;
                }

                // Validate and normalize type
                $validTypes = ['Retailer', 'Dealer', 'Wholesaler', 'Supplier'];
                if (!in_array($type, $validTypes)) {
                    $this->errors[] = "Row " . ($index + 1) . ": Invalid type '$type'. Defaulted to Retailer.";
                    $type = 'Retailer';
                }

                // Check duplicate phone for same business
                if ($phone && (
                        in_array($phone, $this->existingPhones) ||
                        in_array($phone, $this->excelPhones)
                    )) {
                    $this->errors[] = "Row " . ($index + 1) . ": Duplicate phone '$phone' skipped.";
                    continue;
                }

                if ($phone) {
                    $this->excelPhones[] = $phone;
                }

                Party::create([
                    'business_id' => $this->businessId,
                    'name' => $name,
                    'type' => $type,
                    'phone' => $phone,
                    'email' => $email,
                    'due' => $due,
                    'opening_balance' => $due,
                ]);
            }
        });
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
