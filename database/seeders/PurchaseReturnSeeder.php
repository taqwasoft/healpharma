<?php

namespace Database\Seeders;

use App\Models\PurchaseReturn;
use Illuminate\Database\Seeder;
use App\Models\PurchaseReturnDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PurchaseReturnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchase_returns = array(
            array('business_id' => '1', 'purchase_id' => '1', 'invoice_no' => 'PR-00001', 'return_date' => '2025-08-13 09:11:22', 'created_at' => '2025-08-13 09:11:22', 'updated_at' => '2025-08-13 09:11:22'),
            array('business_id' => '1', 'purchase_id' => '2', 'invoice_no' => 'PR-00002', 'return_date' => '2025-08-13 09:12:12', 'created_at' => '2025-08-13 09:12:12', 'updated_at' => '2025-08-13 09:12:12'),
            array('business_id' => '1', 'purchase_id' => '3', 'invoice_no' => 'PR-00003', 'return_date' => '2025-08-13 09:15:27', 'created_at' => '2025-08-13 09:15:27', 'updated_at' => '2025-08-13 09:15:27')
        );

        PurchaseReturn::insert($purchase_returns);

        $purchase_return_details = array(
            array('business_id' => '1', 'purchase_return_id' => '1', 'purchase_detail_id' => '1', 'return_amount' => '610.42', 'return_qty' => '1.00'),
            array('business_id' => '1', 'purchase_return_id' => '2', 'purchase_detail_id' => '2', 'return_amount' => '1423.00', 'return_qty' => '2.00'),
            array('business_id' => '1', 'purchase_return_id' => '3', 'purchase_detail_id' => '3', 'return_amount' => '1370.57', 'return_qty' => '3.00')
        );

        PurchaseReturnDetail::insert($purchase_return_details);
    }
}
