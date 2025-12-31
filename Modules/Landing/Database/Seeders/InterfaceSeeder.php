<?php

namespace Modules\Landing\Database\Seeders;

use Modules\Landing\App\Models\PosAppInterface;
use Illuminate\Database\Seeder;

class InterfaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pos_app_interfaces = array(
            array('image' => 'uploads/25/02/1738837202-296.svg', 'status' => '1', 'created_at' => '2024-04-16 22:18:51', 'updated_at' => '2025-02-06 22:20:02'),
            array('image' => 'uploads/25/02/1738837191-92.svg', 'status' => '1', 'created_at' => '2024-04-16 22:19:43', 'updated_at' => '2025-02-06 22:19:51'),
            array('image' => 'uploads/25/02/1738837179-166.svg', 'status' => '1', 'created_at' => '2024-04-18 20:56:07', 'updated_at' => '2025-02-06 22:19:39'),
            array('image' => 'uploads/25/02/1738837165-952.svg', 'status' => '1', 'created_at' => '2024-04-18 20:56:17', 'updated_at' => '2025-02-06 22:19:25'),
            array('image' => 'uploads/25/02/1739075653-36.svg', 'status' => '1', 'created_at' => '2025-02-09 16:34:14', 'updated_at' => '2025-02-09 16:34:14'),
            array('image' => 'uploads/25/02/1739075666-116.svg', 'status' => '1', 'created_at' => '2025-02-09 16:34:26', 'updated_at' => '2025-02-09 16:34:26'),
            array('image' => 'uploads/25/02/1739075674-934.svg', 'status' => '1', 'created_at' => '2025-02-09 16:34:34', 'updated_at' => '2025-02-09 16:34:34'),
            array('image' => 'uploads/25/02/1739075683-891.svg', 'status' => '1', 'created_at' => '2025-02-09 16:34:43', 'updated_at' => '2025-02-09 16:34:43')
        );

        PosAppInterface::insert($pos_app_interfaces);
    }
}
