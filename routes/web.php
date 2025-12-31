<?php

use App\Models\Gateway;
use App\Models\PaymentType;
use App\Http\Controllers as Web;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

// Root route - redirect to login
Route::get('/', function () {
    return redirect('/login');
});

// Payment Routes Start
Route::get('/payments-gateways/{plan_id}/{business_id}', [Web\PaymentController::class, 'index'])->name('payments-gateways.index');
Route::post('/payments/{plan_id}/{gateway_id}', [Web\PaymentController::class, 'payment'])->name('payments-gateways.payment');
Route::get('/payment/success', [Web\PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [Web\PaymentController::class, 'failed'])->name('payment.failed');
Route::post('ssl-commerz/payment/success', [Web\PaymentController::class, 'sslCommerzSuccess']);
Route::post('ssl-commerz/payment/failed', [Web\PaymentController::class, 'sslCommerzFailed']);
Route::get('/order-status', [Web\PaymentController::class, 'orderStatus'])->name('order.status');

Route::group([
    'namespace' => 'App\Library',
], function () {
    Route::get('/payment/paypal', 'Paypal@status');
    Route::get('/payment/mollie', 'Mollie@status');
    Route::post('/payment/paystack', 'Paystack@status')->name('paystack.status');
    Route::get('/paystack', 'Paystack@view')->name('paystack.view');
    Route::get('/razorpay/payment', 'Razorpay@view')->name('razorpay.view');
    Route::post('/razorpay/status', 'Razorpay@status');
    Route::get('/mercadopago/pay', 'Mercado@status')->name('mercadopago.status');
    Route::get('/payment/flutterwave', 'Flutterwave@status');
    Route::get('/payment/thawani', 'Thawani@status');
    Route::get('/payment/instamojo', 'Instamojo@status');
    Route::get('/payment/toyyibpay', 'Toyyibpay@status');
    Route::post('/paytm/status', 'Paytm@status')->name('paytm.status');
    Route::get('/tap-payment/status', 'TapPayment@status')->name('tap-payment.status');
});
// Payment Routes End

Route::get('/demo-reset', function () {
    Artisan::call('demo:reset');
    return 'success';
});

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return back()->with('success', __('Cache has been cleared.'));
});

Route::get('/reset', function () {
    Artisan::call('migrate:fresh --seed');

    Artisan::call('module:seed', ['module' => 'Landing']);

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return 'Success.';
});

Route::get('/update', function () {

    // check product_type field before migration
    $productTypeExists = Schema::hasColumn('products', 'product_type');

    Artisan::call('migrate');

    if (file_exists(base_path('storage/installed'))) {
        touch(base_path('vendor/autoload1.php'));
    }

    Artisan::call('module:publish Landing');

    if (!PaymentType::exists()) {
        Artisan::call('db:seed', ['--class' => 'PaymentTypeSeeder']);

        Gateway::create([
            'name' => 'paypal',
            'currency_id' => 4,
            'mode' => 'Sandbox',
            'status' => '1',
            'charge' => '2',
            'data' => [
                "client_id" => "ARKsbdD1qRpl3WEV6XCLuTUsvE1_5NnQuazG2Rvw1NkMG3owPjCeAaia0SXSvoKPYNTrh55jZieVW7xv",
                "client_secret" => "EJed2cGACzB2SJFQwSannKAA1gyBjKkwlKh1o8G75zQHYzAgLQ3n7f9EfeNCZgtfPDMxyFzfp6oQWPia",
            ],
            'namespace' => 'App\\Library\\Paypal',
        ]);
    }

    if (!$productTypeExists) {
        Product::with('stocks:id,product_id')
        ->chunkById(500, function ($products) {
            $updates = [];

            foreach ($products as $product) {
                foreach ($product->stocks as $stock) {
                    $updates[] = [
                        'id' => $stock->id,
                        'product_id' => $product->id,
                        'business_id' => $product->business_id,
                        'purchase_without_tax' => $product->purchase_without_tax,
                        'purchase_with_tax' => $product->purchase_with_tax,
                        'sales_price' => $product->sales_price,
                        'wholesale_price' => $product->wholesale_price,
                        'dealer_price' => $product->dealer_price,
                        'profit_percent' => $product->profit_percent,
                        'mfg_date' => $product->mfg_date,
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($updates)) {
                Stock::upsert($updates, ['id'], ['purchase_without_tax', 'purchase_with_tax', 'sales_price', 'wholesale_price', 'dealer_price', 'profit_percent', 'mfg_date', 'updated_at',]);
            }
        });
    }


    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return redirect('/')->with('message', __('System updated successfully.'));
});

require __DIR__ . '/auth.php';
