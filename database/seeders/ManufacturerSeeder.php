<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
        $manufacturers = array(
            array('id' => '1', 'business_id' => '1', 'name' => 'Square Pharmaceuticals Ltd.', 'description' => 'One of the leading pharmaceutical companies in Bangladesh, producing high-quality medicines.', 'status' => '1', 'created_at' => '2025-05-20 15:53:00', 'updated_at' => '2025-05-20 15:53:00'),
            array('id' => '2', 'business_id' => '1', 'name' => 'Beximco Pharmaceuticals Ltd.', 'description' => 'A prominent Bangladeshi manufacturer with a strong global presence in generic drugs.', 'status' => '1', 'created_at' => '2025-05-20 15:54:00', 'updated_at' => '2025-05-20 15:54:00'),
            array('id' => '3', 'business_id' => '1', 'name' => 'ACI Limited', 'description' => 'Advanced Chemical Industries (ACI) is a trusted name in healthcare products in Bangladesh.', 'status' => '1', 'created_at' => '2025-05-20 15:55:00', 'updated_at' => '2025-05-20 15:55:00'),
            array('id' => '4', 'business_id' => '1', 'name' => 'Novartis AG', 'description' => 'A Swiss multinational pharma company known for research-driven innovation and global distribution.', 'status' => '1', 'created_at' => '2025-05-20 15:56:00', 'updated_at' => '2025-05-20 15:56:00'),
            array('id' => '5', 'business_id' => '1', 'name' => 'Incepta Pharmaceuticals Ltd.', 'description' => 'A fast-growing Bangladeshi pharmaceutical company producing branded generics and vaccines.', 'status' => '1', 'created_at' => '2025-05-20 15:57:00', 'updated_at' => '2025-05-20 15:57:00')
        );

        Manufacturer::insert($manufacturers);
    }
}
