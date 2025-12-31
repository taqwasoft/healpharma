<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetails;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchases = array(
            array('party_id' => '11', 'business_id' => '1', 'user_id' => '4', 'tax_id' => '3', 'discountAmount' => '3.75', 'shipping_charge' => '40', 'discount_percent' => '0', 'discount_type' => 'flat', 'tax_amount' => '244.67', 'dueAmount' => '0.00', 'paidAmount' => '2115.95', 'change_amount' => '0', 'totalAmount' => '2115.95', 'invoiceNumber' => '1', 'isPaid' => '1', 'paymentType' => NULL, 'payment_type_id' => '1', 'purchaseDate' => '2025-08-13 09:10:56', 'purchase_data' => '{"id":1,"party_id":11,"business_id":1,"user_id":4,"tax_id":3,"discountAmount":3.75,"shipping_charge":40,"discount_percent":0,"discount_type":"flat","tax_amount":244.67,"dueAmount":0,"paidAmount":2115.9449999999997,"change_amount":0,"totalAmount":2115.9449999999997,"invoiceNumber":"1","isPaid":true,"paymentType":null,"payment_type_id":1,"purchaseDate":"2025-08-13 09:10:56","purchase_data":null,"status":"draft","note":null,"created_at":"2025-08-13T03:10:56.000000Z","updated_at":"2025-08-13T03:10:56.000000Z","details":[{"id":1,"purchase_id":1,"product_id":13,"purchase_without_tax":569,"purchase_with_tax":611.675,"profit_percent":14,"sales_price":648.66,"wholesale_price":659,"dealer_price":623,"quantities":3,"batch_no":"OZ453","expire_date":"2028-03-12","product":{"id":13,"productName":"Bizoran"}}]}', 'status' => 'draft', 'note' => NULL, 'created_at' => '2025-08-13 09:10:56', 'updated_at' => '2025-08-13 09:11:22'),
            array('party_id' => '7', 'business_id' => '1', 'user_id' => '4', 'tax_id' => '1', 'discountAmount' => '4.00', 'shipping_charge' => '45', 'discount_percent' => '0', 'discount_type' => 'flat', 'tax_amount' => '213.75', 'dueAmount' => '3104.75', 'paidAmount' => '3104.75', 'change_amount' => '0', 'totalAmount' => '3104.75', 'invoiceNumber' => '2', 'isPaid' => '0', 'paymentType' => NULL, 'payment_type_id' => '5', 'purchaseDate' => '2025-08-13 09:12:00', 'purchase_data' => '{"id":2,"party_id":7,"business_id":1,"user_id":4,"tax_id":1,"discountAmount":4,"shipping_charge":45,"discount_percent":0,"discount_type":"flat","tax_amount":213.75,"dueAmount":3104.75,"paidAmount":3104.75,"change_amount":0,"totalAmount":3104.75,"invoiceNumber":"2","isPaid":false,"paymentType":null,"payment_type_id":5,"purchaseDate":"2025-08-13 09:12:00","purchase_data":null,"status":"draft","note":null,"created_at":"2025-08-13T03:12:00.000000Z","updated_at":"2025-08-13T03:12:00.000000Z","details":[{"id":2,"purchase_id":2,"product_id":6,"purchase_without_tax":570,"purchase_with_tax":712.5,"profit_percent":130,"sales_price":1638.75,"wholesale_price":550,"dealer_price":560,"quantities":4,"batch_no":"PL454","expire_date":"2027-01-07","product":{"id":6,"productName":"Paracetamol"}}]}', 'status' => 'draft', 'note' => NULL, 'created_at' => '2025-08-13 09:12:00', 'updated_at' => '2025-08-13 09:12:12'),
            array('party_id' => '2', 'business_id' => '1', 'user_id' => '4', 'tax_id' => '6', 'discountAmount' => '120.22', 'shipping_charge' => '37', 'discount_percent' => '5', 'discount_type' => 'percent', 'tax_amount' => '692.50', 'dueAmount' => '1513.78', 'paidAmount' => '3013.78', 'change_amount' => '0', 'totalAmount' => '3013.78', 'invoiceNumber' => '3', 'isPaid' => '0', 'paymentType' => NULL, 'payment_type_id' => '3', 'purchaseDate' => '2025-08-13 09:15:04', 'purchase_data' => '{"id":3,"party_id":2,"business_id":1,"user_id":4,"tax_id":6,"discountAmount":120.22500000000001,"shipping_charge":37,"discount_percent":5,"discount_type":"percent","tax_amount":692.5,"dueAmount":1513.7750000000003,"paidAmount":3013.7750000000005,"change_amount":0,"totalAmount":3013.7750000000005,"invoiceNumber":"3","isPaid":false,"paymentType":null,"payment_type_id":3,"purchaseDate":"2025-08-13 09:15:04","purchase_data":null,"status":"draft","note":null,"created_at":"2025-08-13T03:15:04.000000Z","updated_at":"2025-08-13T03:15:04.000000Z","details":[{"id":3,"purchase_id":3,"product_id":10,"purchase_without_tax":458,"purchase_with_tax":480.9,"profit_percent":120,"sales_price":1007.6,"wholesale_price":769,"dealer_price":780,"quantities":5,"batch_no":"OZ456","expire_date":"2028-03-12","product":{"id":10,"productName":"Omeprazole"}}]}', 'status' => 'draft', 'note' => NULL, 'created_at' => '2025-08-13 09:15:04', 'updated_at' => '2025-08-13 09:15:27')
        );

        Purchase::insert($purchases);

        $purchase_details = array(
            array('purchase_id' => '1', 'product_id' => '13', 'purchase_without_tax' => '569', 'purchase_with_tax' => '611.675', 'profit_percent' => '14', 'sales_price' => '648.66', 'wholesale_price' => '659', 'dealer_price' => '623', 'quantities' => '3.00', 'batch_no' => 'OZ453', 'expire_date' => '2028-03-12'),
            array('purchase_id' => '2', 'product_id' => '6', 'purchase_without_tax' => '570', 'purchase_with_tax' => '712.5', 'profit_percent' => '130', 'sales_price' => '1638.75', 'wholesale_price' => '550', 'dealer_price' => '560', 'quantities' => '4.00', 'batch_no' => 'PL454', 'expire_date' => '2027-01-07'),
            array('purchase_id' => '3', 'product_id' => '10', 'purchase_without_tax' => '458', 'purchase_with_tax' => '480.9', 'profit_percent' => '120', 'sales_price' => '1007.6', 'wholesale_price' => '769', 'dealer_price' => '780', 'quantities' => '5.00', 'batch_no' => 'OZ456', 'expire_date' => '2028-03-12')
        );

        PurchaseDetails::insert($purchase_details);
    }
}
