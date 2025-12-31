<?php

namespace Database\Seeders;

use App\Models\SaleReturn;
use Illuminate\Database\Seeder;
use App\Models\SaleReturnDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SaleReturnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sale_returns = array(
            array('business_id' => '1', 'sale_id' => '2', 'invoice_no' => 'SR-00001', 'return_date' => '2025-08-13 08:39:26', 'created_at' => '2025-08-13 08:39:26', 'updated_at' => '2025-08-13 08:39:26'),
            array('business_id' => '1', 'sale_id' => '3', 'invoice_no' => 'SR-00002', 'return_date' => '2025-08-13 08:41:02', 'created_at' => '2025-08-13 08:41:02', 'updated_at' => '2025-08-13 08:41:02'),
            array('business_id' => '1', 'sale_id' => '4', 'invoice_no' => 'SR-00003', 'return_date' => '2025-08-13 08:43:40', 'created_at' => '2025-08-13 08:43:40', 'updated_at' => '2025-08-13 08:43:40')
        );

        SaleReturn::insert($sale_returns);

        $sale_return_details = array(
            array('business_id' => '1', 'sale_return_id' => '1', 'sale_detail_id' => '2', 'return_amount' => '380.00', 'return_qty' => '1.00'),
            array('business_id' => '1', 'sale_return_id' => '2', 'sale_detail_id' => '3', 'return_amount' => '830.00', 'return_qty' => '2.00'),
            array('business_id' => '1', 'sale_return_id' => '3', 'sale_detail_id' => '4', 'return_amount' => '420.00', 'return_qty' => '1.00')
        );

        SaleReturnDetails::insert($sale_return_details);
    }
}
