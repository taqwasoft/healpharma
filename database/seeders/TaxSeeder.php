<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxs = array(
            array('id' => '1', 'name' => 'VAT 5%', 'business_id' => 1, 'rate' => 5, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '2', 'name' => 'VAT 7.5%', 'business_id' => 1, 'rate' => 7.5, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '3', 'name' => 'VAT 10%', 'business_id' => 1, 'rate' => 10, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '4', 'name' => 'Sales Tax 12%', 'business_id' => 1, 'rate' => 12, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '5', 'name' => 'Service Tax 15%', 'business_id' => 1, 'rate' => 15, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '6', 'name' => 'Import Tax 18%', 'business_id' => 1, 'rate' => 18, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '7', 'name' => 'Luxury Tax 25%', 'business_id' => 1, 'rate' => 25, 'sub_tax' => null, 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '8', 'name' => 'Standard Tax Group', 'business_id' => 1, 'rate' => 25, 'sub_tax' => '[{"id":1,"name":"VAT 5%","rate":5},{"id":5,"name":"Service Tax 15%","rate":15}]', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '9', 'name' => 'Premium Tax Group', 'business_id' => 1, 'rate' => 25, 'sub_tax' => '[{"id":3,"name":"VAT 10%","rate":10},{"id":7,"name":"Luxury Tax 25%","rate":25}]', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '10', 'name' => 'Import Duty Group', 'business_id' => 1, 'rate' => 25, 'sub_tax' => '[{"id":4,"name":"Sales Tax 12%","rate":12},{"id":6,"name":"Import Tax 18%","rate":18}]', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
        );

        Tax::insert($taxs);
    }
}
