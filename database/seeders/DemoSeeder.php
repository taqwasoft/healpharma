<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Party;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\SaleReturn;
use App\Models\SaleDetails;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\PurchaseDetails;
use Illuminate\Database\Seeder;
use App\Models\SaleReturnDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $current_date = now();
        $expire_date = now()->addDays(30);

        $suppliers = array(
            array('name' => 'Sahidul Islam', 'business_id' => '1', 'email' => 'hasyl@mailinator.com', 'type' => 'Supplier', 'phone' => '01346546132', 'due' => '2100.00', 'opening_balance' => '2100.00', 'address' => 'America', 'image' => 'uploads/25/08/1755051983-165.png', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('name' => 'Shahriar Kabir', 'business_id' => '1', 'email' => 'shahriar@mail.com', 'type' => 'Supplier', 'phone' => '01700011223', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Barisal', 'image' => 'uploads/25/08/1755051954-180.png', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('name' => 'Kamrul Hasan', 'business_id' => '1', 'email' => 'kamrul@mail.com', 'type' => 'Supplier', 'phone' => '01677889900', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Mymensingh', 'image' => 'uploads/25/08/1755052000-25.jpg', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        $customers = array(
            array('name' => 'John Doe', 'business_id' => '1', 'email' => 'johndoe@gmail.com', 'type' => 'Dealer', 'phone' => '01564654666', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Dhaka', 'image' => 'uploads/25/08/1755051817-838.jpg', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('name' => 'Aminul Hoque', 'business_id' => '1', 'email' => 'aminul@mail.com', 'type' => 'Retailer', 'phone' => '01745612345', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Chittagong', 'image' => 'uploads/25/08/1755051805-791.jpg', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('name' => 'Karim Uddin', 'business_id' => '1', 'email' => 'karim@mail.com', 'type' => 'Wholesaler', 'phone' => '01987654321', 'due' => '2000.00', 'opening_balance' => '2000.00', 'address' => 'Sylhet', 'image' => 'uploads/25/08/1755051793-280.jpg', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        $sales = array(
            array('business_id' => '1', 'user_id' => '4', 'tax_id' => '1', 'discountAmount' => '5.00', 'shipping_charge' => '40', 'discount_percent' => '0', 'discount_type' => 'flat', 'dueAmount' => '0.00', 'isPaid' => '1', 'tax_amount' => '17.25', 'paidAmount' => '397.25', 'change_amount' => '102.75', 'totalAmount' => '397.25', 'actual_total_amount' => '397.25', 'rounding_amount' => '0.00', 'rounding_option' => 'none', 'lossProfit' => '-35.00', 'paymentType' => NULL, 'payment_type_id' => '1', 'invoiceNumber' => '1', 'saleDate' => '2025-08-13 08:35:27', 'image' => NULL, 'sale_data' => NULL, 'status' => 'draft', 'meta' => ['customer_phone' => null, 'note' => null], 'created_at' => $current_date, 'updated_at' => $current_date),
            array('business_id' => '1', 'user_id' => '4', 'tax_id' => '3', 'discountAmount' => '20.00', 'shipping_charge' => '0', 'discount_percent' => '5', 'discount_type' => 'percent', 'dueAmount' => '0.00', 'isPaid' => '1', 'tax_amount' => '80.00', 'paidAmount' => '175.00', 'change_amount' => '0', 'totalAmount' => '460.00', 'actual_total_amount' => '840.00', 'rounding_amount' => '0.00', 'rounding_option' => 'none', 'lossProfit' => '156.00', 'paymentType' => NULL, 'payment_type_id' => '3', 'invoiceNumber' => '2', 'saleDate' => '2025-08-13 08:39:04', 'image' => NULL, 'sale_data' => '{"business_id":null,"user_id":null,"tax_id":null,"discount_percent":0,"tax_amount":0,"payment_type_id":null}', 'status' => 'draft', 'meta' => ['customer_phone' => null, 'note' => null], 'created_at' => $current_date, 'updated_at' => $current_date),
            array('business_id' => '1', 'user_id' => '4', 'tax_id' => '4', 'discountAmount' => '10.00', 'shipping_charge' => '50', 'discount_percent' => '0', 'discount_type' => 'flat', 'dueAmount' => '1081.60', 'isPaid' => '0', 'tax_amount' => '201.60', 'paidAmount' => '0.00', 'change_amount' => '0', 'totalAmount' => '1081.60', 'actual_total_amount' => '1911.60', 'rounding_amount' => '0.00', 'rounding_option' => 'none', 'lossProfit' => '186.00', 'paymentType' => NULL, 'payment_type_id' => '1', 'invoiceNumber' => '3', 'saleDate' => '2025-08-13 08:40:34', 'image' => NULL, 'sale_data' => '{"business_id":null,"user_id":null,"tax_id":null,"discount_percent":0,"tax_amount":0,"payment_type_id":null}', 'status' => 'draft', 'meta' => ['customer_phone' => null, 'note' => null], 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        $sale_details = array(
            array('product_id' => '12', 'price' => '345.00', 'purchase_price' => '375.00', 'lossProfit' => '-35.00', 'batch_no' => 'OZ452', 'expire_date' => $expire_date, 'quantities' => '1.00'),
            array('product_id' => '4', 'price' => '400.00', 'purchase_price' => '224.00', 'lossProfit' => '156.00', 'batch_no' => 'C110', 'expire_date' => $expire_date, 'quantities' => '1.00'),
            array('product_id' => '3', 'price' => '420.00', 'purchase_price' => '322.00', 'lossProfit' => '186.00', 'batch_no' => 'M321', 'expire_date' => $expire_date, 'quantities' => '2.00'),
        );

        $sale_returns = array(
            array('business_id' => '1', 'invoice_no' => 'SR-00001', 'return_date' => '2025-08-13 08:39:26', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('business_id' => '1', 'invoice_no' => 'SR-00002', 'return_date' => '2025-08-13 08:41:02', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('business_id' => '1', 'invoice_no' => 'SR-00003', 'return_date' => '2025-08-13 08:43:40', 'created_at' => $current_date, 'updated_at' => $current_date)
        );

        $sale_return_details = array(
            array('business_id' => '1', 'return_amount' => '380.00', 'return_qty' => '1.00'),
            array('business_id' => '1', 'return_amount' => '830.00', 'return_qty' => '2.00'),
            array('business_id' => '1', 'return_amount' => '420.00', 'return_qty' => '1.00')
        );

        $purchases = array(
            array('business_id' => '1', 'user_id' => '4', 'tax_id' => '3', 'discountAmount' => '3.75', 'shipping_charge' => '40', 'discount_percent' => '0', 'discount_type' => 'flat', 'tax_amount' => '244.67', 'dueAmount' => '0.00', 'paidAmount' => '2115.95', 'change_amount' => '0', 'totalAmount' => '2115.95', 'invoiceNumber' => '1', 'isPaid' => '1', 'paymentType' => NULL, 'payment_type_id' => '1', 'purchaseDate' => '2025-08-13 09:10:56', 'purchase_data' => '{"id":1,"party_id":11,"business_id":1,"user_id":4,"tax_id":3,"discountAmount":3.75,"shipping_charge":40,"discount_percent":0,"discount_type":"flat","tax_amount":244.67,"dueAmount":0,"paidAmount":2115.9449999999997,"change_amount":0,"totalAmount":2115.9449999999997,"invoiceNumber":"1","isPaid":true,"paymentType":null,"payment_type_id":1,"purchaseDate":"2025-08-13 09:10:56","purchase_data":null,"status":"draft","note":null,"created_at":"2025$current_datepdated_at":"2025-08-13T03:10:56.000000Z","details":[{"id":1,"purchase_id":1,"product_id":13,"purchase_without_tax":569,"purchase_with_tax":611.675,"profit_percent":14,"sales_price":648.66,"wholesale_price":659,"dealer_price":623,"quantities":3,"batch_no":"OZ453","expire_date":"2028-03-12","product":{"id":13,"productName":"Bizoran"}}]}', 'status' => 'draft', 'note' => NULL, 'created_at' => $current_date, 'updated_at' => $current_date),
            array('business_id' => '1', 'user_id' => '4', 'tax_id' => '1', 'discountAmount' => '4.00', 'shipping_charge' => '45', 'discount_percent' => '0', 'discount_type' => 'flat', 'tax_amount' => '213.75', 'dueAmount' => '3104.75', 'paidAmount' => '3104.75', 'change_amount' => '0', 'totalAmount' => '3104.75', 'invoiceNumber' => '2', 'isPaid' => '0', 'paymentType' => NULL, 'payment_type_id' => '5', 'purchaseDate' => '2025-08-13 09:12:00', 'purchase_data' => '{"id":2,"party_id":7,"business_id":1,"user_id":4,"tax_id":1,"discountAmount":4,"shipping_charge":45,"discount_percent":0,"discount_type":"flat","tax_amount":213.75,"dueAmount":3104.75,"paidAmount":3104.75,"change_amount":0,"totalAmount":3104.75,"invoiceNumber":"2","isPaid":false,"paymentType":null,"payment_type_id":5,"purchaseDate":"2025-08-13 09:12:00","purchase_data":null,"status":"draft","note":null,"created_at":"2025$current_datepdated_at":"2025-08-13T03:12:00.000000Z","details":[{"id":2,"purchase_id":2,"product_id":6,"purchase_without_tax":570,"purchase_with_tax":712.5,"profit_percent":130,"sales_price":1638.75,"wholesale_price":550,"dealer_price":560,"quantities":4,"batch_no":"PL454","expire_date":"2027-01-07","product":{"id":6,"productName":"Paracetamol"}}]}', 'status' => 'draft', 'note' => NULL, 'created_at' => $current_date, 'updated_at' => $current_date),
            array('business_id' => '1', 'user_id' => '4', 'tax_id' => '6', 'discountAmount' => '120.22', 'shipping_charge' => '37', 'discount_percent' => '5', 'discount_type' => 'percent', 'tax_amount' => '692.50', 'dueAmount' => '1513.78', 'paidAmount' => '3013.78', 'change_amount' => '0', 'totalAmount' => '3013.78', 'invoiceNumber' => '3', 'isPaid' => '0', 'paymentType' => NULL, 'payment_type_id' => '3', 'purchaseDate' => '2025-08-13 09:15:04', 'purchase_data' => '{"id":3,"party_id":2,"business_id":1,"user_id":4,"tax_id":6,"discountAmount":120.22500000000001,"shipping_charge":37,"discount_percent":5,"discount_type":"percent","tax_amount":692.5,"dueAmount":1513.7750000000003,"paidAmount":3013.7750000000005,"change_amount":0,"totalAmount":3013.7750000000005,"invoiceNumber":"3","isPaid":false,"paymentType":null,"payment_type_id":3,"purchaseDate":"2025-08-13 09:15:04","purchase_data":null,"status":"draft","note":null,"created_at":"2025$current_datepdated_at":"2025-08-13T03:15:04.000000Z","details":[{"id":3,"purchase_id":3,"product_id":10,"purchase_without_tax":458,"purchase_with_tax":480.9,"profit_percent":120,"sales_price":1007.6,"wholesale_price":769,"dealer_price":780,"quantities":5,"batch_no":"OZ456","expire_date":"2028-03-12","product":{"id":10,"productName":"Omeprazole"}}]}', 'status' => 'draft', 'note' => NULL, 'created_at' => $current_date, 'updated_at' => $current_date)
        );

        $purchase_details = array(
            array('product_id' => '13', 'purchase_without_tax' => '569', 'purchase_with_tax' => '611.675', 'profit_percent' => '14', 'sales_price' => '648.66', 'wholesale_price' => '659', 'dealer_price' => '623', 'quantities' => '3.00', 'batch_no' => 'OZ453', 'expire_date' => $expire_date),
            array('product_id' => '6', 'purchase_without_tax' => '570', 'purchase_with_tax' => '712.5', 'profit_percent' => '130', 'sales_price' => '1638.75', 'wholesale_price' => '550', 'dealer_price' => '560', 'quantities' => '4.00', 'batch_no' => 'PL454', 'expire_date' => $expire_date),
            array('product_id' => '10', 'purchase_without_tax' => '458', 'purchase_with_tax' => '480.9', 'profit_percent' => '120', 'sales_price' => '1007.6', 'wholesale_price' => '769', 'dealer_price' => '780', 'quantities' => '5.00', 'batch_no' => 'OZ456', 'expire_date' => $expire_date)
        );

        foreach ($sales as $key => $sale) {

            $customer = Party::create($customers[$key]);
            $supplier = Party::create($suppliers[$key]);

            $sale_data = Sale::create($sale + [
                'party_id' => $customer->id
            ]);

            $sale_detail = SaleDetails::create($sale_details[$key] + [
                'sale_id' => $sale_data->id
            ]);

            $sale_return_data = SaleReturn::create($sale_returns[$key] + [
                'sale_id' => $sale_data->id
            ]);

            SaleReturnDetails::create($sale_return_details[$key] + [
                'sale_return_id' => $sale_return_data->id,
                'sale_detail_id' => $sale_detail->id
            ]);

            $purchase_data = Purchase::create($purchases[$key] + [
                'party_id' => $supplier->id
            ]);
            PurchaseDetails::create($purchase_details[$key] + [
                'purchase_id' => $purchase_data->id
            ]);
        }

        $expense_categories = array(
            array('categoryName' => 'Purchase', 'business_id' => '1', 'categoryDescription' => 'Doctor-prescribed drugs for various health conditions', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('categoryName' => 'Supplement', 'business_id' => '1', 'categoryDescription' => 'Over-the-counter drugs without prescription', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('categoryName' => 'Baby Product Purchase', 'business_id' => '1', 'categoryDescription' => 'Multivitamins, minerals, and health boosters', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        $expenses = array(
            array('amount' => '370.00', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Medicine Purchase', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'MPX5634', 'note' => 'Purchase Some Medicine', 'expenseDate' => '2024-08-22 00:00:00', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('amount' => '450.00', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Supplement Purchase', 'paymentType' => NULL, 'payment_type_id' => '2', 'referenceNo' => 'SPX7845', 'note' => 'Bought some vitamin supplements', 'expenseDate' => '2025-08-02 00:00:00', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('amount' => '220.00', 'user_id' => '4', 'business_id' => '1', 'expanseFor' => 'Baby Product Purchase', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'BPP9921', 'note' => 'Purchased baby care items', 'expenseDate' => '2025-07-18 00:00:00', 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        foreach ($expense_categories as $key => $expense_category) {
            $expenses_category = ExpenseCategory::create($expense_category);
            Expense::create($expenses[$key] + [
                'expense_category_id' => $expenses_category->id
            ]);
        }

        $income_categories = array(
            array('categoryName' => 'Medicine Sales', 'business_id' => '1', 'categoryDescription' => 'Doctor-prescribed drugs for various health conditions', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('categoryName' => 'Health Supplement Sales', 'business_id' => '1', 'categoryDescription' => 'Over-the-counter drugs without prescription', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('categoryName' => 'Baby Product Sales', 'business_id' => '1', 'categoryDescription' => 'Multivitamins, minerals, and health boosters', 'status' => '1', 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        $incomes = array(
            array('amount' => '600.00', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Medicine Sales', 'paymentType' => NULL, 'payment_type_id' => '1', 'referenceNo' => 'SXD5621', 'note' => 'Medicine Sales', 'incomeDate' => '2025-08-12 00:00:00', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('amount' => '450.00', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Medicine Sales', 'paymentType' => NULL, 'payment_type_id' => '5', 'referenceNo' => 'EML4523', 'note' => 'Medicine Sales', 'incomeDate' => '2025-08-02 00:00:00', 'created_at' => $current_date, 'updated_at' => $current_date),
            array('amount' => '55.00', 'user_id' => '4', 'business_id' => '1', 'incomeFor' => 'Baby Product', 'paymentType' => NULL, 'payment_type_id' => '3', 'referenceNo' => 'BPT7654', 'note' => 'Best For Baby', 'incomeDate' => '2025-08-23 00:00:00', 'created_at' => $current_date, 'updated_at' => $current_date),
        );

        foreach ($income_categories as $key => $income_category) {
            $incomes_category = IncomeCategory::create($income_category);
            Income::create($incomes[$key] + [
                'income_category_id' => $incomes_category->id
            ]);
        }
    }
}
