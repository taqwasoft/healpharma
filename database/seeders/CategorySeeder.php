<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = array(
            array('business_id' => '1','categoryName' => 'Pain Relief','description' => 'Various pain relief medications for headaches, muscle pain, and joint discomfort, including over-the-counter and prescription options.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Vitamins & Supplements','description' => 'A range of vitamins and dietary supplements to support health, boost immunity, and maintain overall well-being.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Cold & Flu','description' => 'Medications to relieve symptoms of colds and flu, including decongestants, antihistamines, and cough syrups.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Skin Care','description' => 'Products to care for your skin, including moisturizers, acne treatments, and sun protection products.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Allergy Relief','description' => 'Over-the-counter medications and treatments for seasonal allergies, including antihistamines, nasal sprays, and eye drops.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Baby Care','description' => 'A selection of products for babies, including diapers, baby wipes, lotions, and other essential baby health items.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Heart Health','description' => 'Supplements and medications to support heart health, including blood pressure management, cholesterol control, and more.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Digestive Health','description' => 'Products for digestive wellness, including antacids, probiotics, and medications for nausea, diarrhea, and constipation.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'Diabetes Care','description' => 'Medications and products for managing diabetes, including glucose meters, insulin, and blood sugar control supplements.','status' => '1','created_at' => now(),'updated_at' => now()),
            array('business_id' => '1','categoryName' => 'First Aid','description' => 'Essential first aid products such as bandages, antiseptics, and pain relievers to treat minor injuries and accidents.','status' => '1','created_at' => now(),'updated_at' => now())
          );

        Category::insert($categories);
    }
}
