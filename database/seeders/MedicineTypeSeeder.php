<?php

namespace Database\Seeders;

use App\Models\MedicineType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicineTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicine_types = array(
            array('id' => '1', 'business_id' => 1, 'name' => 'Tablet', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '2', 'business_id' => 1, 'name' => 'Capsule', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '3', 'business_id' => 1, 'name' => 'Syrup', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '4', 'business_id' => 1, 'name' => 'Injection', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '5', 'business_id' => 1, 'name' => 'Ointment', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '6', 'business_id' => 1, 'name' => 'Inhaler', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '7', 'business_id' => 1, 'name' => 'Powder', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
        );

        MedicineType::insert($medicine_types);
    }
}
