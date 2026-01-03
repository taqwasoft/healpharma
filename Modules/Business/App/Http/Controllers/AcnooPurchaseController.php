<?php

namespace Modules\Business\App\Http\Controllers;

use App\Helpers\HasUploader;
use App\Imports\PurchaseProductImport;
use App\Models\Manufacturer;
use App\Models\PaymentType;
use App\Models\Stock;
use App\Models\Tax;
use Carbon\Carbon;
use App\Models\Party;
use App\Models\Product;
use App\Models\Business;
use App\Models\Category;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchaseDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AcnooPurchaseController extends Controller
{
    use HasUploader;

    public function index(Request $request)
    {
        if (!auth()->user()) {
            return redirect()->back()->with('error', 'You have no permission to access.');
        }

        $purchasesWithReturns = PurchaseReturn::where('business_id', auth()->user()->business_id)
            ->pluck('purchase_id')
            ->toArray();

        $query = Purchase::with('details', 'party', 'details.product', 'details.product.category', 'payment_type:id,name')->where('business_id', auth()->user()->business_id);

        if ($request->today) {
            $query->whereDate('created_at', Carbon::today());
        }

        $purchases = $query->latest()->paginate(10);

        return view('business::purchases.index', compact('purchases', 'purchasesWithReturns'));
    }

    public function create()
    {
        // Clears all cart items
        Cart::destroy();

        $business_id = auth()->user()->business_id;

        $suppliers = Party::where('type', 'Supplier')
            ->where('business_id', $business_id)
            ->latest()
            ->get();

        // Load only top 50 products initially - rest will load via AJAX search
        $products = Product::with('category:id,categoryName', 'unit:id,unitName', 'stocks:id,product_id,batch_no,expire_date,purchase_with_tax,purchase_without_tax,profit_percent,sales_price,wholesale_price,dealer_price')
            ->where('business_id', $business_id)
            ->withSum('stocks', 'productStock')
            ->latest()
            ->limit(20)
            ->get();

        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'purchase');
        $categories = Category::where('business_id', $business_id)->latest()->get();
        $manufacturers = Manufacturer::where('business_id', $business_id)->latest()->get();
        $taxes = Tax::where('business_id', $business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', $business_id)->whereStatus(1)->latest()->get();

        // Generate a unique invoice number
        $invoice_no = Purchase::where('business_id', $business_id)->count() + 1;

        return view('business::purchases.create', compact('suppliers', 'products', 'cart_contents', 'invoice_no', 'categories', 'manufacturers', 'taxes', 'payment_types'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'party_id' => 'required|exists:parties,id',
            'receive_amount' => 'nullable|numeric',
            'payment_type_id' => 'required|exists:payment_types,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discountAmount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percent',
            'shipping_charge' => 'nullable|numeric',
            'purchaseDate' => 'nullable|date',
            'isPaid' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $business_id = auth()->user()->business_id;

            $carts = Cart::content()->filter(fn($item) => $item->options->type == 'purchase');

            if ($carts->count() < 1) {
                return response()->json(['message' => __('Cart is empty. Add items first!')], 400);
            }

            // Subtotal
            $subtotal = $carts->sum(function ($cartItem) {
                return (float)$cartItem->subtotal;
            });

            // TAX
            $tax = Tax::find($request->tax_id);
            $taxAmount = 0;
            if ($tax) {
                $taxAmount = ($subtotal * $tax->rate) / 100;
            }

            //Discount
            $discountAmount = $request->discountAmount ?? 0;
            if ($request->discount_type == 'percent') {
                $discountAmount = ($subtotal * $discountAmount) / 100;
            }
            if ($discountAmount > $subtotal) {
                return response()->json([
                    'message' => __('Discount cannot be more than subtotal!')
                ], 400);
            }

            // Shipping Charge
            $shippingCharge = $request->shipping_charge ?? 0;

            // Total Amount
            $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingCharge;

            // Handle payment types
            $paymentTypes = $request->input('payment_types', []);

            if (!empty($paymentTypes)) {
                // multiple payment types
                $receiveAmount = array_sum(array_map(fn($pt) => floatval($pt['amount'] ?? 0), $paymentTypes));
            } else {
                // single payment
                $paymentTypes = [
                    [
                        'payment_type_id' => $request->input('payment_type_id'),
                        'amount' => floatval($request->input('receive_amount', 0)),
                    ]
                ];
                $receiveAmount = $paymentTypes[0]['amount'];
            }

            // Receive, Change, Due Amount Calculation
            $changeAmount = $receiveAmount > $totalAmount ? $receiveAmount - $totalAmount : 0;
            $dueAmount = max($totalAmount - $receiveAmount, 0);
            $paidAmount = $receiveAmount - $changeAmount;

            if ($dueAmount > 0) {
                $party = Party::findOrFail($request->party_id);
                $party->update([
                    'due' => $party->due + $dueAmount
                ]);
            }

            // Update business balance
            $business = Business::findOrFail($business_id);
            $business->update([
                'remainingShopBalance' => $business->remainingShopBalance - $paidAmount,
            ]);

            $purchase = Purchase::create($request->except('discountAmount', 'discount_type', 'discount_percent', 'shipping_charge') + [
                    'business_id' => $business_id,
                    'user_id' => auth()->id(),
                    'tax_id' => $request->tax_id,
                    'tax_amount' => $taxAmount,
                    'discountAmount' => $discountAmount,
                    'discount_type' => $request->discount_type ?? 'flat',
                    'discount_percent' => $request->discount_type == 'percent' ? $request->discountAmount : 0,
                    'totalAmount' => $totalAmount,
                    'paidAmount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'dueAmount' => $dueAmount,
                    'payment_type_id' => $request->payment_type_id,
                    'shipping_charge' => $shippingCharge,
                    'purchaseDate' => $request->purchaseDate ?? now(),
                    'status' => $request->status == 'draft' ? 'draft' : 'final',
                    'isPaid' => $dueAmount > 0 ? 0 : 1,
                ]);

            // Sync payment types with pivot table
            $syncData = [];
            foreach ($paymentTypes as $index => $pt) {
                if (!empty($pt['payment_type_id'])) {
                    $refNumber = $purchase->id + $index;
                    $syncData[$pt['payment_type_id']] = [
                        'amount' => floatval($pt['amount'] ?? 0),
                        'ref_code' => 'P-' . $refNumber,
                    ];
                }
            }

            if (!empty($syncData)) {
                $purchase->paymentTypes()->sync($syncData);
            }

            $purchaseDetails = [];

            foreach ($carts  as $key => $cartItem) {

                $batch_no = $cartItem->options['batch_no'] ?? NULL;
                $existingStock = Stock::where(['batch_no' => $batch_no, 'product_id' => $cartItem->id])->first();

                // update or create stock
                $stock = Stock::updateOrCreate(
                    ['batch_no' => $batch_no, 'business_id' => $business_id, 'product_id' => $cartItem->id],
                    [
                        'product_id' => $cartItem->id,
                        'mfg_date' => $cartItem->options['mfg_date'] ?? NULL,
                        'expire_date' => $cartItem->options['expire_date'] ?? NULL,
                        'profit_percent' => $cartItem->options['profit_percent'] ?? 0,
                        'sales_price' => $cartItem->options['sales_price'] ?? 0,
                        'dealer_price' => $cartItem->options['dealer_price'] ?? 0,
                        'purchase_with_tax' => $cartItem->options['purchase_with_tax'] ?? 0,
                        'purchase_without_tax' => $cartItem->options['purchase_without_tax'] ?? 0,
                        'wholesale_price' => $cartItem->options['wholesale_price'] ?? 0,
                        'productStock' => ($cartItem->qty ?? 0) + ($existingStock->productStock ?? 0),
                    ]
                );

                $purchaseDetails[$key] = [
                    'stock_id' => $stock->id,
                    'purchase_id' => $purchase->id,
                    'product_id' => $cartItem->id,
                    'batch_no' => $batch_no,
                    'quantities' => $cartItem->qty ?? 0,
                    'sales_price' => $cartItem->options['sales_price'] ?? 0,
                    'dealer_price' => $cartItem->options['dealer_price'] ?? 0,
                    'purchase_with_tax' => $cartItem->options['purchase_without_tax'] ?? 0,
                    'purchase_without_tax' => $cartItem->options['purchase_without_tax'] ?? 0,
                    'wholesale_price' => $cartItem->options['wholesale_price'] ?? 0,
                    'profit_percent' => $cartItem->options['profit_percent'] ?? 0,
                    'expire_date' => $cartItem->options['expire_date'] ?? NULL,
                    'mfg_date' => $cartItem->options['mfg_date'] ?? NULL,
                ];
            }

            PurchaseDetails::insert($purchaseDetails);

            // Remove of all Purchases cart
            foreach ($carts as $cartItem) {
                Cart::remove($cartItem->rowId);
            }

            sendNotifyToUser($purchase->id, route('business.purchases.index', ['id' => $purchase->id]), __('New Purchase created.'), $business_id);

            DB::commit();

            if ($request->status == 'draft') {
                return response()->json([
                    'message' => __('Purchases saved successfully.'),
                    'is_draft' => true,
                ]);
            }

            return response()->json([
                'message' => __('Purchases saved successfully.'),
                'redirect' => route('business.purchases.index'),
                'secondary_redirect_url' => route('business.purchases.invoice', $purchase->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Somethings went wrong!')], 404);
        }
    }

    public function edit($id)
    {
        // Clears all cart items
        Cart::destroy();

        $purchase = Purchase::with('details', 'details.product', 'details.product.unit', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $suppliers = Party::where('type', 'Supplier')
            ->where('business_id', auth()->user()->business_id)
            ->latest()
            ->get();

        // Load only top 50 products initially - rest will load via AJAX search
        $products = Product::with('stocks', 'category:id,categoryName', 'unit:id,unitName',)
            ->where('business_id', auth()->user()->business_id)
            ->withSum('stocks', 'productStock')
            ->latest()
            ->limit(20)
            ->get();

        $categories = Category::where('business_id', auth()->user()->business_id)->latest()->get();
        $taxes = Tax::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();

        // Add purchase details to the cart
        foreach ($purchase->details as $detail) {
            // Add to cart
            Cart::add([
                'id' => $detail->product_id,
                'name' => $detail->product->productName ?? '',
                'qty' => $detail->quantities,
                'price' => $detail->purchase_with_tax,
                'options' => [
                    'type' => 'purchase',
                    'purchase_without_tax' => $detail->purchase_without_tax,
                    'purchase_with_tax' => $detail->purchase_with_tax,
                    'batch_no' => $detail->batch_no,
                    'expire_date' => $detail->expire_date,
                    'wholesale_price' => $detail->wholesale_price,
                    'dealer_price' => $detail->dealer_price,
                    'sales_price' => $detail->sales_price,
                    'profit_percent' => $detail->product->profit_percent,
                    'product_unit_name' => $detail->product->unit->unitName ?? '',
                    'product_image' => $detail->product->images[0] ?? null,
                ],
            ]);
        }

        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'purchase');

        return view('business::purchases.edit', compact('purchase', 'suppliers', 'products', 'cart_contents', 'categories', 'taxes', 'payment_types'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'party_id' => 'required|exists:parties,id',
            'receive_amount' => 'nullable|numeric',
            'payment_type_id' => 'required|exists:payment_types,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discountAmount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percent',
            'shipping_charge' => 'nullable|numeric',
            'purchaseDate' => 'nullable|date',
            'isPaid' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $business_id = auth()->user()->business_id;
            $carts = Cart::content()->filter(fn($item) => $item->options->type === 'purchase');

            if ($carts->count() < 1) {
                return response()->json(['message' => __('Cart is empty. Add items first!')], 400);
            }
            $purchase = Purchase::with('details')->findOrFail($id);

            $batch_numbers = $carts->pluck('options.batch_no'); // Get the batch numbers from the request data
            $prev_stocks = Stock::whereIn('batch_no', $batch_numbers)->get();
            $prev_purchase_details = PurchaseDetails::whereIn('batch_no', $batch_numbers)->get();

            foreach ($carts as $cartItem) {
                $prev_stock = $prev_stocks->firstWhere('batch_no', $cartItem->options['batch_no']);
                $prev_purchase_detail = $prev_purchase_details->firstWhere('batch_no', $cartItem->options['batch_no']);

                if (!empty($prev_purchase_detail) && $prev_purchase_detail->quantities > $cartItem->qty) {
                    if ($prev_stock->productStock < $cartItem->qty) {
                        return response()->json([
                            'message' => 'The purchase quantity and stock quantity is not matched for batch no [' . $prev_stock->batch_no . ']'
                        ], 406);
                    }
                }
            }

            // Calculate amounts
            $subtotal = $carts->sum(fn($cartItem) => (float)$cartItem->subtotal);

            // TAX
            $tax = Tax::find($request->tax_id);
            $taxAmount = 0;
            if ($tax) {
                $taxAmount = ($subtotal * $tax->rate) / 100;
            }

            //Discount
            $discountAmount = $request->discountAmount ?? 0;
            if ($request->discount_type == 'percent') {
                $discountAmount = ($subtotal * $discountAmount) / 100;
            }
            if ($discountAmount > $subtotal) {
                return response()->json([
                    'message' => __('Discount cannot be more than subtotal!')
                ], 400);
            }

            // Shipping Charge
            $shippingCharge = $request->shipping_charge ?? 0;

            // Total Amount
            $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingCharge;

            // Handle payment types
            $paymentTypes = $request->input('payment_types', []);
            if (!empty($paymentTypes)) {
                $receiveAmount = array_sum(array_map(fn($pt) => floatval($pt['amount'] ?? 0), $paymentTypes));
            } else {
                $paymentTypes = [
                    [
                        'payment_type_id' => $request->input('payment_type_id'),
                        'amount' => floatval($request->input('receive_amount', 0)),
                    ]
                ];
                $receiveAmount = $paymentTypes[0]['amount'];
            }

            // Receive, Change, Due Amount Calculation
            $changeAmount = $receiveAmount > $totalAmount ? $receiveAmount - $totalAmount : 0;
            $dueAmount = max($totalAmount - $receiveAmount, 0);
            $paidAmount = $receiveAmount - $changeAmount;

            // Revert previous stock changes
            foreach ($purchase->details as $detail) {
                Stock::where('id', $detail->stock_id)->decrement('productStock', $detail->quantities);
            }

            // Delete existing purchase details
            $purchase->details()->delete();

            $purchaseDetails = [];
            foreach ($carts as $key => $cartItem) {

                $batch_no = $cartItem->options['batch_no'] ?? NULL;
                $existingStock = Stock::where(['batch_no' => $batch_no, 'product_id' => $cartItem->id])->first();

                // update or create stock
                $stock = Stock::updateOrCreate(
                    ['batch_no' => $batch_no, 'business_id' => $business_id, 'product_id' => $cartItem->id],
                    [
                        'product_id' => $cartItem->id,
                        'mfg_date' => $cartItem->options['mfg_date'] ?? NULL,
                        'expire_date' => $cartItem->options['expire_date'] ?? NULL,
                        'profit_percent' => $cartItem->options['profit_percent'] ?? 0,
                        'sales_price' => $cartItem->options['sales_price'] ?? 0,
                        'dealer_price' => $cartItem->options['dealer_price'] ?? 0,
                        'purchase_with_tax' => $cartItem->options['purchase_with_tax'] ?? 0,
                        'purchase_without_tax' => $cartItem->options['purchase_without_tax'] ?? 0,
                        'wholesale_price' => $cartItem->options['wholesale_price'] ?? 0,
                        'productStock' => ($cartItem->qty ?? 0) + ($existingStock->productStock ?? 0),
                    ]
                );

                $purchaseDetails[$key] = [
                    'stock_id' => $stock->id,
                    'purchase_id' => $purchase->id,
                    'product_id' => $cartItem->id,
                    'batch_no' => $batch_no,
                    'quantities' => $cartItem->qty ?? 0,
                    'sales_price' => $cartItem->options['sales_price'] ?? 0,
                    'dealer_price' => $cartItem->options['dealer_price'] ?? 0,
                    'purchase_with_tax' => $cartItem->options['purchase_without_tax'] ?? 0,
                    'purchase_without_tax' => $cartItem->options['purchase_without_tax'] ?? 0,
                    'wholesale_price' => $cartItem->options['wholesale_price'] ?? 0,
                    'profit_percent' => $cartItem->options['profit_percent'] ?? 0,
                    'expire_date' => $cartItem->options['expire_date'] ?? NULL,
                    'mfg_date' => $cartItem->options['mfg_date'] ?? NULL,
                ];
            }

            PurchaseDetails::insert($purchaseDetails);

            if ($purchase->dueAmount || $dueAmount) {
                $party = Party::findOrFail($request->party_id);
                $party->update([
                    'due' => $request->party_id == $purchase->party_id ? ($party->due - $purchase->dueAmount) + $dueAmount : $party->due + $dueAmount
                ]);

                if ($request->party_id != $purchase->party_id) {
                    $prev_party = Party::findOrFail($purchase->party_id);
                    $prev_party->update([
                        'due' => $prev_party->due - $purchase->dueAmount
                    ]);
                }
            }

            $business = Business::findOrFail($business_id);
            $business->update([
                'remainingShopBalance' => ($business->remainingShopBalance + $purchase->paidAmount) - $request->paidAmount
            ]);

            // Update purchase
            $purchase->update($request->except('discountAmount', 'discount_type', 'discount_percent', 'shipping_charge') + [
                    'business_id' => auth()->user()->business_id,
                    'user_id' => auth()->id(),
                    'tax_id' => $request->tax_id,
                    'tax_amount' => $taxAmount,
                    'discountAmount' => $discountAmount,
                    'discount_type' => $request->discount_type ?? 'flat',
                    'discount_percent' => $request->discount_type == 'percent' ? $request->discountAmount : 0,
                    'totalAmount' => $totalAmount,
                    'paidAmount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'dueAmount' => $dueAmount,
                    'payment_type_id' => $request->payment_type_id,
                    'purchaseDate' => $request->purchaseDate ?? now(),
                    'shipping_charge' => $shippingCharge,
                    'isPaid' => $dueAmount > 0 ? 0 : 1,
                ]);

            // Sync payment types with pivot table
            $syncData = [];
            foreach ($request->input('payment_types', []) as $index => $pt) {
                $ptId = intval($pt['payment_type_id'] ?? 0);
                $ptAmount = floatval($pt['amount'] ?? 0);
                if ($ptId > 0) {
                    $refNumber = $purchase->id + $index;
                    $syncData[$ptId] = [
                        'amount' => $ptAmount,
                        'ref_code' => 'P-' . $refNumber,
                    ];
                }
            }
            $purchase->paymentTypes()->sync($syncData);


            // Clear the cart
            foreach ($carts as $cartItem) {
                Cart::remove($cartItem->rowId);
            }

            sendNotifyToUser($purchase->id, route('business.purchases.index', ['id' => $purchase->id]), __('Purchase has been updated.'), $business_id);

            DB::commit();

            if ($request->status == 'draft') {
                return response()->json([
                    'message' => __('Purchases saved successfully.'),
                    'is_draft' => true,
                ]);
            }

            return response()->json([
                'message' => __('Purchases saved successfully.'),
                'redirect' => route('business.purchases.index'),
                'secondary_redirect_url' => route('business.purchases.invoice', $purchase->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);
            $purchase_details = PurchaseDetails::where('purchase_id', $purchase->id)->get();
            $prev_stocks = Stock::whereIn('batch_no', $purchase_details->pluck('batch_no'))->get();

            foreach ($purchase_details as $purchase_detail) {
                $prev_stock = $prev_stocks->where('batch_no', $purchase_detail->batch_no)->first();

                if ($prev_stock->productStock < $purchase_detail->quantities) {
                    return response()->json([
                        'message' => 'The purchase quantity and stock quantity is not matched for batch no [' . $prev_stock->batch_no . ']'
                    ], 406);
                }
            }

            if ($purchase->dueAmount) {
                $party = Party::findOrFail($purchase->party_id);
                $party->update([
                    'due' => $party->due - $purchase->dueAmount
                ]);
            }

            $business = Business::findOrFail(auth()->user()->business_id);
            $business->update([
                'remainingShopBalance' => $business->remainingShopBalance + $purchase->paidAmount
            ]);

            $purchase->delete();

            Cart::destroy();
            DB::commit();

            return response()->json([
                'message' => __('Purchase deleted successfully.'),
                'redirect' => route('business.purchases.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 404);
        }
    }

    public function deleteAll(Request $request)
    {
        DB::beginTransaction();

        try {
            $purchases = Purchase::whereIn('id', $request->ids)->get();
            $business = Business::findOrFail(auth()->user()->business_id);

            foreach ($purchases as $purchase) {
                foreach ($purchase->details as $detail) {
                    Stock::where('id', $detail->stock_id)->decrement('productStock', $detail->quantities);
                }

                if ($purchase->party_id) {
                    $party = Party::findOrFail($purchase->party_id);
                    $party->update([
                        'due' => $party->due - $purchase->dueAmount
                    ]);
                }

                // Adjust business balance
                $business->update([
                    'remainingShopBalance' => $business->remainingShopBalance - $purchases->paidAmount
                ]);

                sendNotifyToUser($purchase->id, route('business.purchases.index', ['id' => $purchase->id]), __('Purchases has been deleted.'), $purchase->business_id);

                $purchase->delete();
            }

            // Clears all cart items
            Cart::destroy();

            DB::commit();

            return response()->json([
                'message' => __('Selected purchases deleted successfully.'),
                'redirect' => route('business.purchases.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 404);
        }
    }

    public function acnooFilter(Request $request)
    {
        $purchasesWithReturns = PurchaseReturn::where('business_id', auth()->user()->business_id)
            ->pluck('purchase_id')
            ->toArray();

        $query = Purchase::with('details', 'party', 'details.product', 'details.product.category', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id);

        if ($request->has('today')) {
            $query->whereDate('created_at', Carbon::today());
        }

        $query->when($request->search, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('paymentType', 'like', '%' . $request->search . '%')
                    ->orWhere('invoiceNumber', 'like', '%' . $request->search . '%')
                    ->orWhereHas('party', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('payment_type', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        });

        $purchases = $query->latest()->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::purchases.datas', compact('purchases', 'purchasesWithReturns'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function productFilter(Request $request)
    {
        $query = Product::where('business_id', auth()->user()->business_id)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('productName', 'like', '%' . $request->search . '%')
                        ->orWhere('productCode', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });

        // Get total count before limiting results
        $total_products = $query->count();

        // Limit results to improve performance
        // Load more with eager loading only needed relationships
        $products = $query
            ->with([
                'category:id,categoryName',
                'unit:id,unitName',
                'stocks' => function($query) {
                    $query->select('id', 'product_id', 'batch_no', 'expire_date', 'purchase_with_tax', 'purchase_without_tax', 'profit_percent', 'sales_price', 'wholesale_price', 'dealer_price', 'productStock');
                }
            ])
            ->withSum('stocks', 'productStock')
            ->latest()
            ->limit(20) // Limit to 20 products for better performance
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'total_products' => $total_products,
                'product_id' => $total_products == 1 ? $products->first()->id : null,
                'data' => view('business::purchases.product-list', compact('products'))->render(),
            ]);
        }

        return redirect(url()->previous());
    }

    public function getInvoice($purchase_id)
    {
        $purchase = Purchase::with('user:id,name,role', 'party:id,name,phone', 'business:id,phoneNumber,companyName,tax_name,tax_no,address', 'details:id,purchase_with_tax,quantities,product_id,purchase_id', 'details.product:id,productName', 'payment_type:id,name')
            ->findOrFail($purchase_id);

        $purchase_returns = PurchaseReturn::with('purchase:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'purchase.party:id,name', 'details', 'details.purchaseDetail.product:id,productName')
            ->where('business_id', auth()->user()->business_id)
            ->where('purchase_id', $purchase_id)
            ->latest()
            ->get();

        // sum of  return_qty
        $purchase->details = $purchase->details->map(function ($detail) use ($purchase_returns) {
            $return_qty_sum = $purchase_returns->flatMap(function ($return) use ($detail) {
                return $return->details->where('purchaseDetail.id', $detail->id)->pluck('return_qty');
            })->sum();

            $detail->quantities = $detail->quantities + $return_qty_sum;

            return $detail;
        });

        // Calculate total discount based on return quantities and amounts
        $total_discount = 0;
        $product_discounts = [];

        foreach ($purchase_returns as $return) {
            foreach ($return->details as $detail) {
                // Initialize discount tracking for the first occurrence of each purchase_detail_id
                if (!isset($product_discounts[$detail->purchase_detail_id])) {
                    $product_discounts[$detail->purchase_detail_id] = [
                        'return_qty' => 0,
                        'return_amount' => 0,
                        'price' => $detail->purchaseDetail->purchase_with_tax,
                    ];
                }

                // Accumulate return quantities and return amounts
                $product_discounts[$detail->purchase_detail_id]['return_qty'] += $detail->return_qty;
                $product_discounts[$detail->purchase_detail_id]['return_amount'] += $detail->return_amount;
            }
        }

        // Calculate the total discount for each returned product
        foreach ($product_discounts as $data) {
            $product_price = $data['price'] * $data['return_qty'];
            $discount = $product_price - $data['return_amount'];

            $total_discount += $discount;
        }

        return view('business::purchases.invoice', compact('purchase', 'purchase_returns', 'total_discount'));
    }

    public function showPurchaseCart()
    {
        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'purchase');
        return view('business::purchases.cart-list', compact('cart_contents'));
    }

    public function getCartData()
    {
        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'purchase');
        $data['sub_total'] = 0;

        foreach ($cart_contents as $cart) {
            $data['sub_total'] += $cart->price;
        }
        $data['sub_total'] = currency_format($data['sub_total'], 'icon', 2, business_currency());

        return response()->json($data);
    }

    public function createSupplier(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|max:20|' . Rule::unique('parties')->where('business_id', auth()->user()->business_id),
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Retailer,Dealer,Wholesaler,Supplier',
            'email' => 'nullable|email',
            'image' => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif,svg',
            'address' => 'nullable|string|max:255',
            'due' => 'nullable|numeric|min:0',
        ]);

        Party::create($request->except('image', 'due') + [
            'due' => $request->due ?? 0,
            'image' => $request->image ? $this->upload($request, 'image') : NULL,
            'business_id' => auth()->user()->business_id
        ]);

        return response()->json([
            'message'   => __('Supplier created successfully'),
            'redirect'  => route('business.purchases.create')
        ]);
    }

    public function bulkCartStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $businessId = auth()->user()->business_id;
        $import = new PurchaseProductImport($businessId);

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('Bulk upload successfully.'),
            'redirect' => route('business.purchases.create')
        ]);
    }

    public function viewPayments($id)
    {
        $purchase = Purchase::with(['paymentTypes', 'payment_type'])->findOrFail($id);

        if ($purchase->paymentTypes->isNotEmpty()) {
            $payments = $purchase->paymentTypes->map(function ($paymentType) {
                return [
                    'created_at'   => formatted_date($paymentType->pivot->created_at),
                    'ref_code'     => $paymentType->pivot->ref_code,
                    'amount'       => currency_format($paymentType->pivot->amount),
                    'payment_type' => $paymentType->name,
                ];
            });
        } else {
            $payments = collect([
                [
                    'created_at'   => formatted_date($purchase->created_at),
                    'ref_code'     => '-',
                    'amount'       => currency_format($purchase->paidAmount),
                    'payment_type' => $purchase->payment_type_id
                        ? ($purchase->payment_type->name ?? '')
                        : ($purchase->paymentType ?? ''),
                ]
            ]);
        }

        return response()->json(['payments' => $payments]);
    }

}
