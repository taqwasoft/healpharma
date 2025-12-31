<?php

namespace Database\Seeders;

use App\Models\Party;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parties = array(
            array('name' => 'John Doe', 'business_id' => '1', 'email' => 'johndoe@gmail.com', 'type' => 'Dealer', 'phone' => '01564654666', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Dhaka', 'image' => 'uploads/25/08/1755051817-838.jpg', 'status' => '1', 'created_at' => '2025-08-12 12:12:09', 'updated_at' => '2025-08-13 08:23:37'),
            array('name' => 'Sahidul Islam', 'business_id' => '1', 'email' => 'hasyl@mailinator.com', 'type' => 'Supplier', 'phone' => '01346546132', 'due' => '2100.00', 'opening_balance' => '2100.00', 'address' => 'America', 'image' => 'uploads/25/08/1755051983-165.png', 'status' => '1', 'created_at' => '2025-08-12 12:45:39', 'updated_at' => '2025-08-13 08:26:23'),
            array('name' => 'Aminul Hoque', 'business_id' => '1', 'email' => 'aminul@mail.com', 'type' => 'Retailer', 'phone' => '01745612345', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Chittagong', 'image' => 'uploads/25/08/1755051805-791.jpg', 'status' => '1', 'created_at' => '2025-08-12 13:10:21', 'updated_at' => '2025-08-13 08:23:25'),
            array('name' => 'Karim Uddin', 'business_id' => '1', 'email' => 'karim@mail.com', 'type' => 'Wholesaler', 'phone' => '01987654321', 'due' => '2000.00', 'opening_balance' => '2000.00', 'address' => 'Sylhet', 'image' => 'uploads/25/08/1755051793-280.jpg', 'status' => '1', 'created_at' => '2025-08-12 13:12:55', 'updated_at' => '2025-08-13 08:23:13'),
            array('name' => 'Mohammad Ali', 'business_id' => '1', 'email' => 'mohammad@mail.com', 'type' => 'Dealer', 'phone' => '01823456789', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Rajshahi', 'image' => 'uploads/25/08/1755051779-184.png', 'status' => '1', 'created_at' => '2025-08-12 13:15:01', 'updated_at' => '2025-08-13 08:23:00'),
            array('name' => 'Rakib Hasan', 'business_id' => '1', 'email' => 'rakibhasan@mail.com', 'type' => 'Retailer', 'phone' => '01333334444', 'due' => '2200.00', 'opening_balance' => '2200.00', 'address' => 'Khulna', 'image' => 'uploads/25/08/1755051769-345.png', 'status' => '1', 'created_at' => '2025-08-12 13:16:47', 'updated_at' => '2025-08-13 08:22:49'),
            array('name' => 'Shahriar Kabir', 'business_id' => '1', 'email' => 'shahriar@mail.com', 'type' => 'Supplier', 'phone' => '01700011223', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Barisal', 'image' => 'uploads/25/08/1755051954-180.png', 'status' => '1', 'created_at' => '2025-08-12 13:18:39', 'updated_at' => '2025-08-13 08:25:54'),
            array('name' => 'Nipa Islam', 'business_id' => '1', 'email' => 'nipa@mail.com', 'type' => 'Wholesaler', 'phone' => '01911223344', 'due' => '2500.00', 'opening_balance' => '2500.00', 'address' => 'Gazipur', 'image' => 'uploads/25/08/1755051730-128.png', 'status' => '1', 'created_at' => '2025-08-12 13:19:45', 'updated_at' => '2025-08-13 08:22:10'),
            array('name' => 'Fahim Rahman', 'business_id' => '1', 'email' => 'fahim@mail.com', 'type' => 'Dealer', 'phone' => '01522223333', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Comilla', 'image' => 'uploads/25/08/1755051696-101.png', 'status' => '1', 'created_at' => '2025-08-12 13:21:14', 'updated_at' => '2025-08-13 08:21:36'),
            array('name' => 'Samiul Haque', 'business_id' => '1', 'email' => 'samiul@mail.com', 'type' => 'Retailer', 'phone' => '01755667788', 'due' => '2000.00', 'opening_balance' => '2000.00', 'address' => 'Narayanganj', 'image' => 'uploads/25/08/1755051683-37.png', 'status' => '1', 'created_at' => '2025-08-12 13:22:37', 'updated_at' => '2025-08-13 08:21:23'),
            array('name' => 'Kamrul Hasan', 'business_id' => '1', 'email' => 'kamrul@mail.com', 'type' => 'Supplier', 'phone' => '01677889900', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Mymensingh', 'image' => 'uploads/25/08/1755052000-25.jpg', 'status' => '1', 'created_at' => '2025-08-12 13:23:59', 'updated_at' => '2025-08-13 08:26:40'),
            array('name' => 'Abdul Kader', 'business_id' => '1', 'email' => 'abdul@mail.com', 'type' => 'Wholesaler', 'phone' => '01899887766', 'due' => '2300.00', 'opening_balance' => '2300.00', 'address' => 'Rangpur', 'image' => 'uploads/25/08/1755051672-577.png', 'status' => '1', 'created_at' => '2025-08-12 13:25:43', 'updated_at' => '2025-08-13 08:21:12'),
            array('name' => 'Marzia Rahman', 'business_id' => '1', 'email' => 'marzia@mail.com', 'type' => 'Supplier', 'phone' => '01799001122', 'due' => '3300.00', 'opening_balance' => '3300.00', 'address' => 'Tangail', 'image' => 'uploads/25/08/1755051939-139.png', 'status' => '1', 'created_at' => '2025-08-12 13:32:12', 'updated_at' => '2025-08-13 08:25:39'),
            array('name' => 'Tariqul Islam', 'business_id' => '1', 'email' => 'tariqul@mail.com', 'type' => 'Supplier', 'phone' => '01811002233', 'due' => '2100.00', 'opening_balance' => '2100.00', 'address' => 'Pabna', 'image' => 'uploads/25/08/1755052016-434.png', 'status' => '1', 'created_at' => '2025-08-12 13:33:25', 'updated_at' => '2025-08-13 08:26:56'),
            array('name' => 'Jahirul Alam', 'business_id' => '1', 'email' => 'jahirul@mail.com', 'type' => 'Supplier', 'phone' => '01633004455', 'due' => '0.00', 'opening_balance' => '0.00', 'address' => 'Bogura', 'image' => 'uploads/25/08/1755051853-631.jpg', 'status' => '1', 'created_at' => '2025-08-12 13:34:40', 'updated_at' => '2025-08-13 08:24:13')
        );

        Party::insert($parties);
    }
}
