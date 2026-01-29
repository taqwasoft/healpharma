<?php

namespace Modules\Business\App\Http\Controllers;

use App\Helpers\HasUploader;
use App\Models\Manufacturer;
use App\Models\PaymentType;
use App\Models\Stock;
use App\Models\Tax;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Party;
use App\Models\Product;
use App\Models\Business;
use App\Models\Category;
use App\Models\SaleReturn;
use App\Models\SaleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Validation\Rule;

class AcnooSaleController extends Controller
{
    use HasUploader;

    public function index(Request $request)
    {
        if (!auth()->user()) {
            return redirect()->back()->with('error', __('You have no permission to access.'));
        }

        $salesWithReturns = SaleReturn::where('business_id', auth()->user()->business_id)
            ->pluck('sale_id')
            ->toArray();

        $query = Sale::with('user:id,name', 'party:id,name,email,phone,type', 'details', 'details.product:id,productName,category_id', 'details.product.category:id,categoryName', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->latest();

        if ($request->has('today') && $request->today) {
            $query->whereDate('created_at', Carbon::today());
        }

        $sales = $query->paginate(10);

        return view('business::sales.index', compact('sales', 'salesWithReturns'));
    }

    public function acnooFilter(Request $request)
    {
        $salesWithReturns = SaleReturn::where('business_id', auth()->user()->business_id)
            ->pluck('sale_id')
            ->toArray();

        $query = Sale::with('user:id,name', 'party:id,name,email,phone,type', 'details', 'details.product:id,productName,category_id', 'details.product.category:id,categoryName', 'payment_type:id,name')
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

        $sales = $query->latest()->paginate($request->per_page ?? 10);
        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::sales.datas', compact('sales', 'salesWithReturns'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function productFilter(Request $request)
    {
        $businessId = auth()->user()->business_id;
        
        // Use select to limit columns
        $query = Product::select([
            'id', 'productName', 'productCode', 'sales_price', 
            'dealer_price', 'wholesale_price', 'category_id', 
            'unit_id', 'images', 'type_id'
        ])
        ->where('business_id', $businessId);
        
        // Add stock filter using EXISTS for better performance
        $query->whereHas('stocks', function ($q) {
            $q->where('productStock', '>', 0);
        });
        
        // Search optimization
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('productName', 'LIKE', $search . '%')  // Changed to prefix search
                ->orWhere('productCode', '=', $search);  // Exact match for codes
            });
        }
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Manufacturer filter
        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }
        
        // Eager load only necessary relationships
        $query->with([
            'medicine_type:id,name',
            'stocks' => function($q) {
                $q->select('id', 'product_id', 'productStock', 'batch_no', 'expire_date', 
                        'sales_price', 'dealer_price', 'wholesale_price', 
                        'purchase_without_tax', 'purchase_with_tax')
                ->where('productStock', '>', 0);
            }
        ]);
        
        // Limit results for barcode scan
        $limit = $request->filled('search') ? 50 : 20;
        $products = $query->latest('id')->limit($limit)->get();
        
        $total_products = $products->count();
        
        if ($request->ajax()) {
            return response()->json([
                'total_products' => $total_products,
                'total_products_count' => $total_products,
                'product_id' => $total_products == 1 ? $products->first()->id : null,
                'data' => view('business::sales.product-list', compact('products'))->render(),
            ]);
        }
        
        return redirect(url()->previous());
    }

    public function productFilterOld(Request $request)
    {
        $total_products_count = Product::where('business_id', auth()->user()->business_id)->count();
        $products = Product::where('business_id', auth()->user()->business_id)
            ->whereHas('stocks', function ($query) {
                $query->where('productStock', '>', 0);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('productName', 'like', '%' . $request->search . '%')
                        ->orWhere('productCode', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->manufacturer_id, function ($query) use ($request) {
                $query->where('manufacturer_id', $request->manufacturer_id);
            })
            ->with('medicine_type:id,name')
            ->latest()
            ->get();

        $total_products = $products->count();

        if ($request->ajax()) {
            return response()->json([
                'total_products' => $total_products,
                'total_products_count' => $total_products_count,
                'product_id' => $total_products == 1 ? $products->first()->id : null,
                'data' => view('business::sales.product-list', compact('products'))->render(),
            ]);
        }

        return redirect(url()->previous());
    }

    public function create()
    {
        // Clears all cart items
        Cart::destroy();

        $business_id = auth()->user()->business_id;

        $customers = Party::where('type', '!=', 'supplier')
            ->where('business_id', $business_id)
            ->latest()
            ->get();

        // fetch only non expired product
        $products = Product::with('stocks', 'category:id,categoryName', 'unit:id,unitName')
            ->where('business_id', $business_id)
            ->nonExpired()
            ->latest()
            ->limit(20)
            ->get();

        $categories = Category::where('business_id', $business_id)->latest()->get();
        $manufacturers = Manufacturer::where('business_id', $business_id)->latest()->get();
        $taxes = Tax::where('business_id', $business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', $business_id)->whereStatus(1)->latest()->get();

        // Generate a unique invoice number
        $invoice_no = Sale::where('business_id', $business_id)->count() + 1;

        return view('business::sales.create', compact('customers', 'products', 'invoice_no', 'categories', 'manufacturers', 'taxes', 'payment_types'));
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'saleDate' => 'nullable|date',
            'customer_phone' => 'nullable|string',
            'receive_amount' => 'nullable|numeric',
            'tax_id' => 'nullable|exists:taxes,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'discountAmount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percent',
            'shipping_charge' => 'nullable|numeric',
        ]);

        $business_id = auth()->user()->business_id;

        // Get only 'sale' type items from cart
        $carts = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        if ($carts->count() < 1) {
            return response()->json(['message' => __('Cart is empty. Add items first!')], 400);
        }

        DB::beginTransaction();
        try {

            // TAX
            $tax = Tax::find($request->tax_id);
            $subtotal = $carts->sum(fn($item) => (float) $item->subtotal);
            $taxAmount = $tax ? ($subtotal * $tax->rate) / 100 : 0;

            // Discount
            $discountAmount = $request->discountAmount ?? 0;
            $subtotalWithTax = $subtotal + $taxAmount;

            if ($request->discount_type === 'percent') {
                $discountAmount = ($subtotalWithTax * $discountAmount) / 100;
            }
            if ($discountAmount > $subtotalWithTax) {
                return response()->json(['message' => __('Discount cannot be more than subtotal with tax!')], 400);
            }
            // Shipping Charge
            $shippingCharge = $request->shipping_charge ?? 0;

            // Total Amount
            $actualTotalAmount = $subtotalWithTax - $discountAmount + $shippingCharge;
            $roundingTotalAmount = sale_rounding($actualTotalAmount);
            $rounding_amount = $roundingTotalAmount - $actualTotalAmount;
            $rounding_option = sale_rounding();

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
            $changeAmount = max($receiveAmount - $roundingTotalAmount, 0);
            $dueAmount = max($roundingTotalAmount - $receiveAmount, 0);
            $paidAmount = $receiveAmount - $changeAmount;

            // Update business balance
            $business = Business::findOrFail($business_id);
            $business->update([
                'remainingShopBalance' => $business->remainingShopBalance + $paidAmount,
            ]);

            // Create sale record
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'business_id' => $business_id,
                'type' => $request->type == 'inventory' ? 'inventory' : 'sale',
                'party_id' => $request->party_id == 'guest' ? null : $request->party_id,
                'invoiceNumber' => $request->invoiceNumber,
                'saleDate' => $request->saleDate ?? now(),
                'tax_id' => $request->tax_id,
                'tax_amount' => $taxAmount,
                'discountAmount' => $discountAmount,
                'discount_type' => $request->discount_type ?? 'flat',
                'discount_percent' => $request->discount_type == 'percent' ? $discountAmount : 0,
                'totalAmount' => $roundingTotalAmount,
                'actual_total_amount' => $actualTotalAmount,
                'rounding_amount' => $rounding_amount,
                'rounding_option' => $rounding_option,
                'paidAmount' => min($paidAmount, $roundingTotalAmount),
                'change_amount' => $changeAmount,
                'dueAmount' => $dueAmount,
                'payment_type_id' => $request->payment_type_id,
                'shipping_charge' => $shippingCharge,
                'isPaid' => $dueAmount > 0 ? 0 : 1,
                'meta' => [
                    'customer_phone' => $request->customer_phone,
                    'note' => $request->note,
                ]
            ]);

             // Sync payment types with pivot table
            $syncData = [];
            foreach ($paymentTypes as $index => $pt) {
                if (!empty($pt['payment_type_id'])) {
                    $refNumber = $sale->id + $index; // use sale id + index for unique ref_code
                    $syncData[$pt['payment_type_id']] = [
                        'amount' => floatval($pt['amount'] ?? 0),
                        'ref_code' => 'P-' . $refNumber,
                    ];
                }
            }

            if (!empty($syncData)) {
                $sale->paymentTypes()->sync($syncData);
            }

            $avgDiscount = $discountAmount / max($carts->count(), 1);
            $totalPurchaseAmount = 0;
            $saleDetailsData = [];

            foreach ($carts as $cartItem) {
                $qty = $cartItem->qty;
                $purchase_price = $cartItem->options->purchase_with_tax ?? 0;
                $stock = Stock::where('id', $cartItem->options->stock_id)->first();

                $lossProfit = (($cartItem->price - $purchase_price) * $cartItem->qty) - $avgDiscount;

                if ($stock->productStock < $qty) {
                    $batchText = $stock->batch_no ? " ($stock->batch_no)" : "";
                    return response()->json([
                        'message' => __($cartItem->name . $batchText . ' - stock not available. Available: ' . $stock->productStock)
                    ], 400);
                }

                $stock->decrement('productStock', $qty);

                $saleDetailsData[] = [
                    'sale_id' => $sale->id,
                    'stock_id' => $cartItem->options->stock_id,
                    'product_id' => $cartItem->id,
                    'batch_no' => $cartItem->options->batch_no,
                    'price' => $cartItem->price,
                    'lossProfit' => $lossProfit,
                    'quantities' => $cartItem->qty,
                    'purchase_price' => $purchase_price,
                    'expire_date' => $cartItem->options->expire_date ?? null,
                ];

                $totalPurchaseAmount += $purchase_price * $qty;
            }

            // Sync payment types with pivot table
            $syncData = [];
            foreach ($request->input('payment_types', []) as $index => $pt) {
                $ptId = intval($pt['payment_type_id'] ?? 0);
                $ptAmount = floatval($pt['amount'] ?? 0);
                if ($ptId > 0) {
                    $refNumber = $sale->id + $index;
                    $syncData[$ptId] = [
                        'amount' => $ptAmount,
                        'ref_code' => 'P-' . $refNumber,
                    ];
                }
            }
            $sale->paymentTypes()->sync($syncData);

            // Insert all sale details
            SaleDetails::insert($saleDetailsData);

            $sale->update([
                'lossProfit' => $subtotal - $totalPurchaseAmount - $discountAmount,
            ]);

            // Handle due tracking for non-guest customers
            if ($dueAmount > 0 && $request->party_id) {
                $party = Party::find($request->party_id);
                if ($party) {
                    $party->update(['due' => $party->due + $dueAmount]);

                    if ($party->phone && env('MESSAGE_ENABLED')) {
                        sendMessage($party->phone, saleMessage($sale, $party, $business->companyName));
                    }
                }
            }

            // Notify user
            sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('New sale created.'), $business_id);

            DB::commit();

            if ($request->status == 'draft') {
                return response()->json([
                    'message' => __('Sales saved successfully.'),
                    'is_draft' => true,
                ]);
            }

            // Clear all items from cart
            foreach ($carts as $cartItem) {
                Cart::remove($cartItem->rowId);
            }

            return response()->json([
                'message' => __('Sales saved successfully.'),
                'redirect' => route('business.sales.index'),
                'secondary_redirect_url' => route('business.sales.invoice', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // sale edit
    public function edit($id)
    {
        // Clears all cart items
        Cart::destroy();

        $business_id = auth()->user()->business_id;

        $sale = Sale::with('user:id,name', 'party:id,name,email,phone,type', 'details', 'details.product:id,productName,images,category_id,unit_id,productCode,sales_price', 'details.product.category:id,categoryName', 'details.product.unit:id,unitName', 'payment_type:id,name')
            ->where('business_id', $business_id)
            ->findOrFail($id);

        $customers = Party::where('type', '!=', 'supplier')
            ->where('business_id', $business_id)
            ->latest()
            ->get();

        // fetch only non expired product
        $products = Product::with('stocks', 'category:id,categoryName', 'unit:id,unitName')
            ->where('business_id', $business_id)
            ->nonExpired()
            ->latest()
            ->limit(20)
            ->get();

        $categories = Category::where('business_id', $business_id)->latest()->get();
        $manufacturers = Manufacturer::where('business_id', $business_id)->latest()->get();
        $taxes = Tax::where('business_id', $business_id)->whereStatus(1)->latest()->get();
        $payment_types = PaymentType::where('business_id', $business_id)->whereStatus(1)->latest()->get();

        // Add sale details to the cart
        foreach ($sale->details as $detail) {

            $stockId = $detail->stock_id;
            $batchNo = $detail->batch_no ?? null;

            if (is_null($stockId)) {
                $matchingStock = Stock::where('product_id', $detail->product_id)
                    ->where('business_id', $business_id)
                    ->where('batch_no', $detail->batch_no)
                    ->first();
                if ($matchingStock) {
                    $stockId = $matchingStock->id;
                }
            }

            // Add to cart
            Cart::add([
                'id' => $detail->product_id,
                'name' => $detail->product->productName ?? '',
                'qty' => $detail->quantities,
                'price' => $detail->price ?? 0,
                'options' => [
                    'type' => 'sale',
                    'stock_id' => $stockId,
                    'purchase_without_tax' => $detail->purchase_without_tax,
                    'purchase_with_tax' => $detail->purchase_with_tax,
                    'batch_no' => $batchNo,
                    'expire_date' => $detail->expire_date,
                    'product_image' => $detail->product->images[0] ?? null,
                ],
            ]);
        }

        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        return view('business::sales.edit', compact('sale', 'customers', 'products', 'cart_contents', 'categories', 'manufacturers', 'taxes', 'payment_types'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'saleDate' => 'nullable|date',
            'customer_phone' => 'nullable|string',
            'receive_amount' => 'nullable|numeric',
            'tax_id' => 'nullable|exists:taxes,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'discountAmount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percent',
            'shipping_charge' => 'nullable|numeric',
        ]);

        $business_id = auth()->user()->business_id;
        $carts = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        if ($carts->count() < 1) {
            return response()->json(['message' => __('Cart is empty. Add items first!')], 400);
        }

        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);
            $prevDetails = $sale->details;

            $totalPurchaseAmount = 0;
            $subtotal = 0;

            foreach ($carts as $cartItem) {
                $prevProduct = $prevDetails->firstWhere('product_id', $cartItem->id);
                $stock = Stock::where('id', $cartItem->options->stock_id ?? null)
                    ->first() ?? Stock::where('product_id', $cartItem->id)->orderBy('id', 'asc')->first();

                if (!$stock) {
                    return response()->json([
                        'message' => __($cartItem->name . ' - no stock found.')
                    ], 400);
                }

                // Adjust available stock by adding back old quantity
                $availableStock = $stock->productStock + ($prevProduct->quantities ?? 0);

                if ($availableStock < $cartItem->qty) {
                    return response()->json([
                        'message' => __($cartItem->name . ' - stock not available for this product. Available quantity is: ' . $availableStock)
                    ], 400);
                }
                $totalPurchaseAmount += $cartItem->options->purchase_price * $cartItem->qty;
                $subtotal += (float)$cartItem->subtotal;
            }

            $tax = Tax::find($request->tax_id);
            $taxAmount = $tax ? ($subtotal * $tax->rate) / 100 : 0;
            $subtotalWithVat = $subtotal + $taxAmount;

            $discountAmount = $request->discountAmount ?? 0;
            if ($request->discount_type == 'percent') {
                $discountAmount = ($subtotalWithVat * $discountAmount) / 100;
            }
            if ($discountAmount > $subtotalWithVat) {
                return response()->json(['message' => __('Discount cannot be more than subtotal with VAT!')], 400);
            }

            $shippingCharge = $request->shipping_charge ?? 0;
            $actualTotalAmount = $subtotalWithVat - $discountAmount + $shippingCharge;
            $roundingTotalAmount = sale_rounding($actualTotalAmount, $sale->rounding_option);
            $rounding_amount = $roundingTotalAmount - $actualTotalAmount;

            $receiveAmount = $request->receive_amount ?? 0;
            $changeAmount = $receiveAmount > $roundingTotalAmount ? $receiveAmount - $roundingTotalAmount : 0;
            $dueAmount = max($roundingTotalAmount - $receiveAmount, 0);
            $paidAmount = $receiveAmount - $changeAmount;

            $business = Business::findOrFail($business_id);
            $business->update([
                'shopOpeningBalance' => ($business->shopOpeningBalance - $sale->paidAmount) + $paidAmount
            ]);

            $sale->update([
                'user_id' => auth()->id(),
                'saleDate' => $request->saleDate ?? now(),
                'tax_id' => $request->tax_id,
                'tax_amount' => $taxAmount,
                'discountAmount' => $discountAmount,
                'discount_type' => $request->discount_type ?? 'flat',
                'discount_percent' => $request->discount_type == 'percent' ? $request->discountAmount : 0,
                'totalAmount' => $roundingTotalAmount,
                'actual_total_amount' => $actualTotalAmount,
                'rounding_amount' => $rounding_amount,
                'lossProfit' => $subtotal - $totalPurchaseAmount - $discountAmount,
                'paidAmount' => $paidAmount > $roundingTotalAmount ? $roundingTotalAmount : $paidAmount,
                'change_amount' => $changeAmount,
                'dueAmount' => $dueAmount,
                'payment_type_id' => $request->payment_type_id,
                'isPaid' => $dueAmount > 0 ? 0 : 1,
                'shipping_charge' => $shippingCharge,
                'meta' => [
                    'customer_phone' => $request->customer_phone,
                    'note' => $request->note,
                ]
            ]);

            // Sync payment types with pivot table
            $syncData = [];
            foreach ($request->input('payment_types', []) as $index => $pt) {
                $ptId = intval($pt['payment_type_id'] ?? 0);
                $ptAmount = floatval($pt['amount'] ?? 0);
                if ($ptId > 0) {
                    $refNumber = $sale->id + $index;
                    $syncData[$ptId] = [
                        'amount' => $ptAmount,
                        'ref_code' => 'P-' . $refNumber,
                    ];
                }
            }
            $sale->paymentTypes()->sync($syncData);

            SaleDetails::where('sale_id', $sale->id)->delete();

            $avgDiscount = $discountAmount / $carts->count();
            $saleDetailsData = [];

            foreach ($carts as $cartItem) {
                $prevProduct = $prevDetails->firstWhere('product_id', $cartItem->id);
                $oldQty = $prevProduct ? $prevProduct->quantities : 0;
                $newQty = $cartItem->qty;
                $diffQty = $newQty - $oldQty;

                $lossProfit = (($cartItem->price - $cartItem->options->purchase_price) * $newQty) - $avgDiscount;

                $saleDetailsData[] = [
                    'sale_id' => $sale->id,
                    'stock_id' => $cartItem->options->stock_id,
                    'product_id' => $cartItem->id,
                    'price' => $cartItem->price,
                    'batch_no' => $cartItem->options->batch_no ?? null,
                    'lossProfit' => $lossProfit,
                    'quantities' => $newQty,
                    'expire_date' => $cartItem->options->expire_date ?? null,
                    'purchase_price' => $cartItem->options->purchase_price ?? 0,
                ];

                $stock = Stock::where('id', $cartItem->options->stock_id ?? null)
                    ->first() ?? Stock::where('product_id', $cartItem->id)->orderBy('id', 'asc')->first();

                $stock->productStock += $diffQty;
            }

            SaleDetails::insert($saleDetailsData);

            if (($sale->dueAmount || $request->dueAmount) && $request->party_id != null) {
                $party = Party::findOrFail($request->party_id);
                $party->update([
                    'due' => $request->party_id == $sale->party_id ? (($party->due - $sale->dueAmount) + $dueAmount) : ($party->due + $dueAmount)
                ]);

                if ($request->party_id != $sale->party_id) {
                    $prevParty = Party::findOrFail($sale->party_id);
                    $prevParty->update([
                        'due' => $prevParty->due - $sale->dueAmount
                    ]);
                }

                if ($party->phone && env('MESSAGE_ENABLED')) {
                    sendMessage($party->phone, saleMessage($sale, $party, $business->companyName));
                }
            }

            sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('Sale has been updated.'), $business_id);

            DB::commit();

            if ($request->status == 'draft') {
                return response()->json([
                    'message' => __('Sales saved successfully.'),
                    'is_draft' => true,
                ]);
            }

            // Clear the cart
            foreach ($carts as $cartItem) {
                Cart::remove($cartItem->rowId);
            }

            return response()->json([
                'message' => __('Sales saved successfully.'),
                'redirect' => route('business.sales.index'),
                'secondary_redirect_url' => route('business.sales.invoice', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 404);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);

            foreach ($sale->details as $detail) {
                $stock = Stock::find($detail->stock_id);

                if (!$stock) {
                    $stock = Stock::where('product_id', $detail->product_id)->orderBy('id', 'asc')->first();
                }

                if ($stock) {
                    $stock->increment('productStock', $detail->quantities);
                }
            }

            if ($sale->party_id) {
                $party = Party::findOrFail($sale->party_id);
                $party->update(['due' => $party->due - $sale->dueAmount]);
            }

            $business = Business::findOrFail(auth()->user()->business_id);
            $business->update([
                'remainingShopBalance' => $business->remainingShopBalance - $sale->paidAmount
            ]);

            sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('Sale has been deleted.'), $sale->business_id);

            $sale->delete();

            // Clears all cart items
            Cart::destroy();

            DB::commit();

            return response()->json([
                'message' => __('Sale deleted successfully.'),
                'redirect' => route('business.sales.index')
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
            $sales = Sale::whereIn('id', $request->ids)->get();
            $business = Business::findOrFail(auth()->user()->business_id);

            foreach ($sales as $sale) {
                // Restore stock
                foreach ($sale->details as $detail) {
                    $stock = Stock::find($detail->stock_id);

                    if (!$stock) {
                        $stock = Stock::where('product_id', $detail->product_id)->orderBy('id', 'asc')->first();
                    }

                    if ($stock) {
                        $stock->increment('productStock', $detail->quantities);
                    }
                }

                // Adjust party due
                if ($sale->party_id) {
                    $party = Party::findOrFail($sale->party_id);
                    $party->update(['due' => $party->due - $sale->dueAmount]);
                }

                // Adjust business balance
                $business->update([
                    'remainingShopBalance' => $business->remainingShopBalance - $sale->paidAmount
                ]);

                sendNotifyToUser($sale->id, route('business.sales.index', ['id' => $sale->id]), __('Sale has been deleted.'), $sale->business_id);

                $sale->delete();
            }

            Cart::destroy();

            DB::commit();

            return response()->json([
                'message' => __('Selected sales deleted successfully.'),
                'redirect' => route('business.sales.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Something went wrong!')], 404);
        }
    }


    /** Get Product wise prices */
    public function getProductPrices(Request $request)
    {
        $type = $request->type;

        $stocks = Stock::with('product')
            ->whereHas('product', function ($query) {
                $query->where('business_id', auth()->user()->business_id);
            })
            ->where('productStock', '>', 0)
            ->get();

        $prices = [];

        foreach ($stocks as $stock) {
            $productId = $stock->product_id;

            if (!isset($prices[$productId])) {
                if ($type === 'Dealer') {
                    $prices[$productId] = currency_format($stock->dealer_price, currency: business_currency());
                } elseif ($type === 'Wholesaler') {
                    $prices[$productId] = currency_format($stock->wholesale_price, currency: business_currency());
                } else {
                    $prices[$productId] = currency_format($stock->sales_price, currency: business_currency());
                }
            }
        }

        return response()->json($prices);
    }

    /** Get customer wise stock prices */
    public function getStockPrices(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $customerType = $request->input('type');
        $cartStocks = $request->input('stocks', []); // optional, only cart rows

        // Fetch all stocks for product list prices
        $allStocks = Stock::where('business_id', $businessId)
            ->where('productStock', '>', 0)
            ->get();

        $productPrices = []; // Product-single prices
        foreach ($allStocks as $stock) {
            // Determine price based on customer type
            if ($customerType === 'Dealer') {
                $productPrices[$stock->product_id] = $stock->dealer_price;
            } elseif ($customerType === 'Wholesaler') {
                $productPrices[$stock->product_id] = $stock->wholesale_price;
            } else {
                $productPrices[$stock->product_id] = $stock->sales_price;
            }
        }

        // Fetch stocks for cart list prices
        $stockPrices = [];
        if (!empty($cartStocks)) {
            $cartStockIds = collect($cartStocks)->pluck('stock_id')->toArray();

            $cartStockQuery = Stock::where('business_id', $businessId)
                ->whereIn('id', $cartStockIds)
                ->where('productStock', '>', 0)
                ->get();

            foreach ($cartStockQuery as $stock) {
                $batchNo = $stock->batch_no ?? 'default';

                if ($customerType === 'Dealer') {
                    $stockPrices[$stock->id][$batchNo] = $stock->dealer_price;
                } elseif ($customerType === 'Wholesaler') {
                    $stockPrices[$stock->id][$batchNo] = $stock->wholesale_price;
                } else {
                    $stockPrices[$stock->id][$batchNo] = $stock->sales_price;
                }
            }
        }

        // Return both product and cart prices
        return response()->json([
            'products' => $productPrices,
            'stocks'   => $stockPrices,
        ]);
    }

    /** Get cart info */
    public function getCartData()
    {
        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'sale');

        $data['sub_total'] = 0;

        foreach ($cart_contents as $cart) {
            $data['sub_total'] += $cart->price;
        }
        $data['sub_total'] = currency_format($data['sub_total'], currency: business_currency());

        return response()->json($data);
    }

    public function getInvoice($sale_id)
    {
        $sale = Sale::where('business_id', auth()->user()->business_id)->with('user:id,name,role', 'party:id,name,phone,address', 'business:id,phoneNumber,companyName,address,tax_name,tax_no', 'details:id,price,quantities,product_id,sale_id', 'details.product:id,productName,type_id,meta', 'details.product.medicine_type:id,name', 'payment_type:id,name')->findOrFail($sale_id);

        $sale_returns = SaleReturn::with('sale:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'sale.party:id,name', 'details', 'details.saleDetail.product:id,productName,type_id,meta', 'details.saleDetail.product.medicine_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->where('sale_id', $sale_id)
            ->latest()
            ->get();

        // sum of  return_qty
        $sale->details = $sale->details->map(function ($detail) use ($sale_returns) {
            $return_qty_sum = $sale_returns->flatMap(function ($return) use ($detail) {
                return $return->details->where('saleDetail.id', $detail->id)->pluck('return_qty');
            })->sum();

            $detail->quantities = $detail->quantities + $return_qty_sum;
            return $detail;
        });

        // Calculate the initial discount for each product during sale returns
        $total_discount = 0;
        $product_discounts = [];

        foreach ($sale_returns as $return) {
            foreach ($return->details as $detail) {
                // Add the return quantities and return amounts for each sale_detail_id
                if (!isset($product_discounts[$detail->sale_detail_id])) {
                    // Initialize the first occurrence
                    $product_discounts[$detail->sale_detail_id] = [
                        'return_qty' => 0,
                        'return_amount' => 0,
                        'price' => $detail->saleDetail->price,
                    ];
                }

                // Accumulate quantities and return amounts for the same sale_detail_id
                $product_discounts[$detail->sale_detail_id]['return_qty'] += $detail->return_qty;
                $product_discounts[$detail->sale_detail_id]['return_amount'] += $detail->return_amount;
            }
        }

        // Calculate the total discount based on accumulated quantities and return amounts
        foreach ($product_discounts as $data) {
            $product_price = $data['price'] * $data['return_qty'];
            $discount = $product_price - $data['return_amount'];

            $total_discount += $discount;
        }

        return view('business::sales.invoice', compact('sale', 'sale_returns', 'total_discount',));
    }

    public function createCustomer(Request $request)
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
            'message'   => __('Customer created successfully'),
            'redirect'  => route('business.sales.create')
        ]);
    }

    public function stockList()
    {
        $businessId = auth()->user()->business_id;
        $stocks = Product::with(['stocks' => function ($query) {
            $query->where('productStock', '>', 0);
        }])
            ->where('business_id', $businessId)
            ->latest()
            ->get();

        return response()->json([
            'stocks' => $stocks
        ]);
    }

    public function viewPayments($id)
    {
        $sale = Sale::with(['paymentTypes', 'payment_type'])->findOrFail($id);

        if ($sale->paymentTypes->isNotEmpty()) {
            $payments = $sale->paymentTypes->map(function ($paymentType) {
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
                    'created_at'   => formatted_date($sale->created_at),
                    'ref_code'     => '-',
                    'amount'       => currency_format($sale->paidAmount),
                    'payment_type' => $sale->payment_type_id
                        ? ($sale->payment_type->name ?? '')
                        : ($sale->paymentType ?? ''),
                ]
            ]);
        }

        return response()->json(['payments' => $payments]);
    }
}
