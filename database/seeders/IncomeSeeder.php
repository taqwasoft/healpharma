<?php

namespace Database\Seeders;

use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $income_categories = array(
            array('categoryName' => 'Medicine Sales', 'business_id' => '1', 'categoryDescription' => 'Doctor-prescribed drugs for various health conditions', 'status' => '1', 'created_at' => '2025-08-12 16:12:07', 'updated_at' => '2025-08-12 16:12:07'),
            array('categoryName' => 'Health Supplement Sales', 'business_id' => '1', 'categoryDescription' => 'Over-the-counter drugs without prescription', 'status' => '1', 'created_at' => '2025-08-12 16:12:34', 'updated_at' => '2025-08-12 16:12:34'),
            array('categoryName' => 'Baby Product Sales', 'business_id' => '1', 'categoryDescription' => 'Multivitamins, minerals, and health boosters', 'status' => '1', 'created_at' => '2025-08-12 16:12:53', 'updated_at' => '2025-08-12 16:12:53'),
            array('categoryName' => 'Diagnostic Test Booking', 'business_id' => '1', 'categoryDescription' => 'Skincare, oral care, haircare, and hygiene products', 'status' => '1', 'created_at' => '2025-08-12 16:13:20', 'updated_at' => '2025-08-12 16:13:20'),
            array('categoryName' => 'Medical Device Sales', 'business_id' => '1', 'categoryDescription' => 'Feminine hygiene and women\'s specific health products', 'status' => '1', 'created_at' => '2025-08-12 16:13:41', 'updated_at' => '2025-08-12 16:13:41'),
            array('categoryName' => 'Delivery Charges', 'business_id' => '1', 'categoryDescription' => 'Safety and immunity products during pandemic', 'status' => '1', 'created_at' => '2025-08-12 16:14:00', 'updated_at' => '2025-08-12 16:14:00'),
            array('categoryName' => 'Product sale', 'business_id' => '1', 'categoryDescription' => 'Multivitamins, minerals, and health boosters', 'status' => '1', 'created_at' => '2025-08-12 16:14:23', 'updated_at' => '2025-08-12 16:14:23')
        );

        $incomes = array(
            array('amount' => '600.00', 'income_category_id' => '1', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Medicine Sales', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'SXD5621', 'note' => 'Medicine Sales', 'incomeDate' => '2025-08-12 00:00:00', 'created_at' => '2025-08-12 16:17:42', 'updated_at' => '2025-08-12 16:17:42'),
            array('amount' => '450.00', 'income_category_id' => '2', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Medicine Sales', 'paymentType' => NULL, 'payment_type_id' => '5', 'referenceNo' => 'EML4523', 'note' => 'Medicine Sales', 'incomeDate' => '2025-08-02 00:00:00', 'created_at' => '2025-08-12 16:18:57', 'updated_at' => '2025-08-12 16:19:26'),
            array('amount' => '55.00', 'income_category_id' => '3', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Baby Product', 'paymentType' => NULL, 'payment_type_id' => '3', 'referenceNo' => 'BPT7654', 'note' => 'Best For Baby', 'incomeDate' => '2025-08-23 00:00:00', 'created_at' => '2025-08-12 16:20:37', 'updated_at' => '2025-08-12 16:20:37'),
            array('amount' => '320.00', 'income_category_id' => '4', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Diagnostic Test', 'paymentType' => NULL, 'payment_type_id' => '4', 'referenceNo' => 'DIT0913', 'note' => 'Advance booked', 'incomeDate' => '2025-08-10 00:00:00', 'created_at' => '2025-08-12 16:22:26', 'updated_at' => '2025-08-12 16:22:26'),
            array('amount' => '1250.00', 'income_category_id' => '7', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Sales Product', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'SPC8734', 'note' => 'Product Seeling', 'incomeDate' => '2025-08-12 00:00:00', 'created_at' => '2025-08-12 16:23:51', 'updated_at' => '2025-08-12 16:23:51')
        );

        IncomeCategory::insert($income_categories);
        Income::insert($incomes);
    }
}
