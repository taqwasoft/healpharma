<?php

namespace Database\Seeders;

use App\Models\BoxSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoxSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $box_sizes = array(
            array('id' => '1', 'business_id' => 1, 'name' => '1 Strip', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '2', 'business_id' => 1, 'name' => '10 Strips Box', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '3', 'business_id' => 1, 'name' => '100 ml Bottle', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '4', 'business_id' => 1, 'name' => '200 ml Bottle', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '5', 'business_id' => 1, 'name' => '1 Vial', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '6', 'business_id' => 1, 'name' => 'Tube 15 gm', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
            array('id' => '7', 'business_id' => 1, 'name' => 'Box of 100 Tablets', 'status' => 1, 'created_at' => '2025-06-29 00:00:00', 'updated_at' => '2025-06-29 00:00:00'),
        );

        BoxSize::insert($box_sizes);
    }
}
