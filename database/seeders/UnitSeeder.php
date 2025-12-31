<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = array(
            array('unitName' => 'Bottle','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Box','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Strip','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Tube','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Packet','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Vial','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Jar','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Ampoule','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Packet','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('unitName' => 'Carton','business_id' => '1','status' => '1','created_at' => now(),'updated_at' => now())
          );

        Unit::insert($units);
    }
}
