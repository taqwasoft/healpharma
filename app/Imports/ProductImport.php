<?php

namespace App\Imports;

use App\Models\BoxSize;
use App\Models\Manufacturer;
use App\Models\MedicineType;
use App\Models\Tax;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ProductImport implements ToCollection
{
    protected $businessId;
    protected $categories = [];
    protected $medicineTypes = [];
    protected $manuFactures = [];
    protected $boxSizes = [];
    protected $units = [];
    protected $taxes = [];
    protected $models = [];
    protected $existingProductCodes = [];
    protected $excelProductCodes = [];

    public function __construct($businessId)
    {
        $this->businessId = $businessId;

        $this->existingProductCodes = Product::where('business_id', $businessId)
            ->pluck('productCode')
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip the header

                // Read Excel fields
                $productName = trim($row[0]);
                $categoryName = trim($row[1]);
                $unitName = trim($row[2]);
                $medicineType = trim($row[3]);
                $manufacturerName = trim($row[4]);
                $boxSize = trim($row[5]);
                $stockQty = $row[6] ?? 0;
                $productCode = trim($row[7]);
                $purchasePrice = (float)($row[8] ?? 0);
                $salePrice = (float)($row[9] ?? 0);
                $dealerPrice = (float)($row[10] ?? $salePrice);
                $wholesalePrice = (float)($row[11] ?? $salePrice);
                $taxName = trim($row[12]);
                $taxPercent = (float)($row[13] ?? 0);
                $taxType = $row[14] ?? 'exclusive';
                $alertQty = (int)($row[15] ?? 0);
                $expireDate = $this->parseExcelDate($row[16]);
                $manufacturingDate = $row[17] ?? null;
                $batchNo = $row[18] ?? null;

                if (!$productName || !$categoryName) {
                    continue;
                }

                // Skip duplicates only if productCode exists
                if ($productCode && in_array($productCode, $this->existingProductCodes)) continue;
                if ($productCode && in_array($productCode, $this->excelProductCodes)) continue;

                // Tax & profit calculation
                $taxAmount = ($purchasePrice * $taxPercent) / 100;
                $purchaseWithoutTax = $purchasePrice;
                $purchaseWithTax = $taxType === 'inclusive' ? $purchasePrice : $purchasePrice + $taxAmount;

                $profitPercent = $purchasePrice > 0
                    ? round((($salePrice - $purchasePrice) / $purchasePrice) * 100, 3)
                    : 0.0;

                if ($productCode) {
                    $this->excelProductCodes[] = $productCode;
                }
                // Create or get related IDs
                $categoryId = $this->categories[$categoryName] ??= Category::firstOrCreate(
                    ['categoryName' => $categoryName, 'business_id' => $this->businessId],
                    ['categoryName' => $categoryName]
                )->id;

                $medicineTypeId = $this->medicineTypes[$medicineType] ??= MedicineType::firstOrCreate(
                    ['name' => $medicineType, 'business_id' => $this->businessId],
                    ['name' => $medicineType]
                )->id;

                $manuFactureId = $this->manuFactures[$manufacturerName] ??= Manufacturer::firstOrCreate(
                    ['name' => $manufacturerName, 'business_id' => $this->businessId],
                    ['name' => $manufacturerName]
                )->id;

                $boxSizId = $this->boxSizes[$boxSize] ??= BoxSize::firstOrCreate(
                    ['name' => $boxSize, 'business_id' => $this->businessId],
                    ['name' => $boxSize]
                )->id;

                $unitId = $this->units[$unitName] ??= Unit::firstOrCreate(
                    ['unitName' => $unitName, 'business_id' => $this->businessId],
                    ['unitName' => $unitName]
                )->id;

                $taxId = $this->taxes[$taxName] ??= Tax::firstOrCreate(
                    ['name' => $taxName, 'business_id' => $this->businessId],
                    ['name' => $taxName, 'rate' => $taxPercent]
                )->id;

                // Create product
                $product = Product::create([
                    'productName' => $productName,
                    'business_id' => $this->businessId,
                    'unit_id' => $unitId,
                    'type_id' => $medicineTypeId,
                    'category_id' => $categoryId,
                    'manufacturer_id' => $manuFactureId,
                    'box_size_id' => $boxSizId,
                    'productCode' => $productCode,
                    'tax_id' => $taxId,
                    'tax_type' => $taxType,
                    'tax_amount' => $taxAmount,
                    'alert_qty' => $alertQty,
                    'expire_date' => $expireDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Stock::updateOrCreate(
                    [
                        'batch_no'    => $batchNo,
                        'business_id' => $this->businessId,
                        'product_id'  => $product->id,
                    ],
                    [
                        'expire_date' => $expireDate,
                        'productStock' => $stockQty,
                        'purchase_without_tax'  => $purchaseWithoutTax,
                        'purchase_with_tax'     => $purchaseWithTax,
                        'profit_percent' => $profitPercent,
                        'sales_price' => $salePrice,
                        'wholesale_price' => $wholesalePrice,
                        'dealer_price' => $dealerPrice,
                        'mfg_date' => $manufacturingDate,
                    ]
                );
            }
        });
    }

    function parseExcelDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // If it is numeric (Excel timestamp)
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Remove extra spaces
        $value = trim($value);

        // Try MM/DD/YYYY first
        try {
            return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            // Try DD/MM/YYYY
            try {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            } catch (\Exception $e2) {
                // Try default parse (YYYY-MM-DD etc.)
                try {
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e3) {
                    return null;
                }
            }
        }
    }
}
