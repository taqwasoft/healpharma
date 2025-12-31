<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expense_categories = array(
            array('categoryName' => 'Purchase', 'business_id' => '1', 'categoryDescription' => 'Doctor-prescribed drugs for various health conditions', 'status' => '1', 'created_at' => '2025-08-12 16:35:49', 'updated_at' => '2025-08-12 16:35:49'),
            array('categoryName' => 'Supplement', 'business_id' => '1', 'categoryDescription' => 'Over-the-counter drugs without prescription', 'status' => '1', 'created_at' => '2025-08-12 16:36:07', 'updated_at' => '2025-08-12 16:36:07'),
            array('categoryName' => 'Baby Product Purchase', 'business_id' => '1', 'categoryDescription' => 'Multivitamins, minerals, and health boosters', 'status' => '1', 'created_at' => '2025-08-12 16:36:24', 'updated_at' => '2025-08-12 16:38:35'),
            array('categoryName' => 'Delivery Charges', 'business_id' => '1', 'categoryDescription' => 'Safety and immunity products during pandemic', 'status' => '1', 'created_at' => '2025-08-12 16:37:35', 'updated_at' => '2025-08-12 16:37:35'),
            array('categoryName' => 'Product Purchase', 'business_id' => '1', 'categoryDescription' => 'Buy Good Quality Products', 'status' => '1', 'created_at' => '2025-08-12 16:38:14', 'updated_at' => '2025-08-12 16:39:11')
        );

        ExpenseCategory::insert($expense_categories);

        $expenses = array(
            array('amount' => '370.00', 'expense_category_id' => '1', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Medicine Purchase', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'MPX5634', 'note' => 'Purchase Some Medicine', 'expenseDate' => '2024-08-22 00:00:00', 'created_at' => '2024-08-15 16:41:56', 'updated_at' => '2024-08-15 16:41:56'),
            array('amount' => '450.00', 'expense_category_id' => '2', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Supplement Purchase', 'paymentType' => NULL, 'payment_type_id' => '2', 'referenceNo' => 'SPX7845', 'note' => 'Bought some vitamin supplements', 'expenseDate' => '2025-08-02 00:00:00', 'created_at' => '2025-05-02 16:45:12', 'updated_at' => '2025-05-02 16:45:12'),
            array('amount' => '220.00', 'expense_category_id' => '3', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Baby Product Purchase', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'BPP9921', 'note' => 'Purchased baby care items', 'expenseDate' => '2025-07-18 00:00:00', 'created_at' => '2025-08-12 16:47:28', 'updated_at' => '2025-08-12 16:47:28'),
            array('amount' => '150.00', 'expense_category_id' => '4', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Delivery Charges', 'paymentType' => NULL, 'payment_type_id' => '3', 'referenceNo' => 'DCP4455', 'note' => 'Paid courier service charges', 'expenseDate' => '2025-08-15 00:00:00', 'created_at' => '2025-08-07 16:49:03', 'updated_at' => '2025-08-07 16:49:03'),
            array('amount' => '600.00', 'expense_category_id' => '5', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Product Purchase', 'paymentType' => NULL, 'payment_type_id' => '2', 'referenceNo' => 'PPX8832', 'note' => 'Purchased general health products', 'expenseDate' => '2025-08-12 00:00:00', 'created_at' => '2025-08-11 16:50:14', 'updated_at' => '2025-08-11 16:50:14')
        );

        Expense::insert($expenses);
    }
}
