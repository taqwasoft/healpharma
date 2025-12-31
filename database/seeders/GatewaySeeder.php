<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = array(
            array('name' => 'Stripe', 'currency_id' => '4', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"stripe_key":"pk_test_51S5fq4AXO57xnUA46TNFoQcoCxCWSFj8NkQ6832Ir6vH2EYFJYEIyCFEF9o7hD9Ep8l9XIDkgygq9jExoAP64zDe00CeCZjJ48","stripe_secret":"sk_test_51S5fq4AXO57xnUA4E84K5MQjv2e5IV0mVALKaJDbR4jL4TUm4msUv0i2ZznvHSuaNzjAZ5WUNc5pOMN1XsGSn4wN00Ks89xiN0"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\StripeGateway', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Mollie', 'currency_id' => '4', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"api_key":"test_WqUGsP9qywy3eRVvWMRayxmVB5dx2r"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Mollie', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Paystack', 'currency_id' => '98', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"public_key":"pk_test_84d91b79433a648f2cd0cb69287527f1cb81b53d","secret_key":"sk_test_cf3a234b923f32194fb5163c9d0ab706b864cc3e"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Paystack', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Razorpay', 'currency_id' => '60', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"key_id":"rzp_test_siWkeZjPLsYGSi","key_secret":"jmIzYyrRVMLkC9BwqCJ0wbmt"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Razorpay', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Instamojo', 'currency_id' => '60', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"x_api_key":"test_0027bc9da0a955f6d33a33d4a5d","x_auth_token":"test_211beaba149075c9268a47f26c6"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Instamojo', 'phone_required' => '1', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Toyyibpay', 'currency_id' => '82', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"user_secret_key":"v4nm8x50-bfb4-7f8y-evrs-85flcysx5b9p","cateogry_code":"5cc45t69"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Toyyibpay', 'phone_required' => '1', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Flutterwave', 'currency_id' => '98', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"public_key":"FLWPUBK_TEST-f448f625c416f69a7c08fc6028ebebbf-X","secret_key":"FLWSECK_TEST-561fa94f45fc758339b1e54b393f3178-X","encryption_key":"FLWSECK_TEST498417c2cc01","payment_options":"card"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Flutterwave', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Thawani', 'currency_id' => '101', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"secret_key":"rRQ26GcsZzoEhbrP2HZvLYDbn9C9et","publishable_key":"HGvTMLDssJghr9tlN9gr4DVYt0qyBy"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Thawani', 'phone_required' => '1', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Paytm', 'currency_id' => '60', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"merchant_id":"MhjqFc42556626519745","merchant_key":"0dC_Dq!nif6e1Kie","channel":"WEB","industry_type":"Retail","website":"WEBSTAGING"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\Paytm', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Tap Payment', 'currency_id' => '116', 'mode' => '1', 'status' => '1', 'charge' => '2', 'image' => NULL, 'data' => '{"secret_key":"sk_test_KbfoWyw7tzhDHQSF62ICavZx","currency":"SAR"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\TapPayment', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:54:44'),
            array('name' => 'Sslcommerz', 'currency_id' => '14', 'mode' => '1', 'status' => '1', 'charge' => '1', 'image' => NULL, 'data' => '{"store_id":"maant62a8633caf4a3","store_password":"maant62a8633caf4a3@ssl"}', 'manual_data' => NULL, 'is_manual' => '0', 'accept_img' => '0', 'namespace' => 'App\\Library\\SslCommerz', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-18 23:45:52'),
            array('name' => 'Manual', 'currency_id' => '4', 'mode' => '1', 'status' => '1', 'charge' => '0', 'image' => NULL, 'data' => '', 'manual_data' => '{"label":["Bank Name","Transaction ID"],"is_required":["1","1"]}', 'is_manual' => '1', 'accept_img' => '1', 'namespace' => 'App\\Library\\StripeGateway', 'phone_required' => '0', 'instructions' => NULL, 'created_at' => '2024-02-18 23:45:52', 'updated_at' => '2024-02-19 00:24:39')
        );

        Gateway::insert($gateways);
    }
}
