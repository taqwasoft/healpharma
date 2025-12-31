<?php

namespace App\Http\Controllers\Api;

use App\Models\Party;
use App\Models\Stock;
use App\Models\Business;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchaseDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Purchase::select('id', 'party_id', 'invoiceNumber', 'purchaseDate', 'totalAmount', 'dueAmount', 'paidAmount', 'paymentType', 'note','payment_type_id')
                ->with('party:id,name,phone', 'payment_type:id,name', 'paymentTypes:id,name')
                ->when(request('search'), function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->where('paymentType', 'like', '%' . request('search') . '%')
                            ->orWhere('invoiceNumber', 'like', '%' . request('search') . '%')
                            ->orWhere('note', 'like', '%' . request('search') . '%')
                            ->orWhereHas('party', function ($query) {
                                $query->where('name', 'like', '%' . request('search') . '%')
                                    ->orWhere('phone', 'like', '%' . request('search') . '%');
                            });
                    });
                })
                ->withCount('purchaseReturns')
                ->where('business_id', auth()->user()->business_id)
                ->latest()
                ->paginate(10);

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'purchaseDate' => 'required|string',
            'party_id' => 'required|exists:parties,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discountAmount' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'totalAmount' => 'nullable|numeric',
            'dueAmount' => 'nullable|numeric',
            'paidAmount' => 'nullable|numeric',
            'isPaid' => 'nullable|boolean',
            'paymentType' => 'nullable|string',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.purchase_without_tax' => 'required|numeric',
            'products.*.purchase_with_tax' => 'required|numeric',
            'products.*.profit_percent' => 'required|numeric',
            'products.*.sales_price' => 'required|numeric',
            'products.*.wholesale_price' => 'required|numeric',
            'products.*.batch_no' => 'nullable|string',
            'products.*.expire_date' => 'nullable|string',
            'products.*.quantities' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {

            $business_id = auth()->user()->business_id;

            if ($request->dueAmount) {
                $party = Party::findOrFail($request->party_id);
                $party->update([
                    'due' => $party->due + $request->dueAmount
                ]);
            }

            $business = Business::findOrFail($business_id);
            $business->update([
                'remainingShopBalance' => $business->remainingShopBalance - $request->paidAmount
            ]);

            $purchase = Purchase::create($request->all() + [
                    'user_id' => auth()->id(),
                    'business_id' => $business_id,
                ]);

            $paymentTypes = $request->payment_types;

            // Ensure array format
            if (is_string($paymentTypes)) {
                $paymentTypes = json_decode($paymentTypes, true);
            }
            if (!empty($paymentTypes) && is_array($paymentTypes)) {
                $syncData = [];

                foreach ($paymentTypes as $index => $pt) {
                    if (!empty($pt['payment_type_id'])) {
                        $refNumber = $purchase->id + $index;
                        $syncData[$pt['payment_type_id']] = [
                            'amount'   => $pt['amount'],
                            'ref_code' => 'P-' . $refNumber,
                        ];
                    }
                }
                $purchase->paymentTypes()->sync($syncData);
            }

            $purchaseDetails = [];

            foreach ($request->products as $key => $product_data) {

                $batch_no = $product_data['batch_no'] ?? NULL;
                $existingStock = Stock::where(['batch_no' => $batch_no, 'product_id' => $product_data['product_id']])->first();

                // update or create stock
                $stock = Stock::updateOrCreate(
                    ['batch_no' => $batch_no, 'business_id' => $business_id, 'product_id' => $product_data['product_id']],
                    [
                        'product_id' => $product_data['product_id'],
                        'mfg_date' => $product_data['mfg_date'] ?? NULL,
                        'expire_date' => $product_data['expire_date'] ?? NULL,
                        'profit_percent' => $product_data['profit_percent'] ?? 0,
                        'sales_price' => $product_data['sales_price'] ?? 0,
                        'dealer_price' => $product_data['dealer_price'] ?? 0,
                        'purchase_with_tax' => $product_data['purchase_with_tax'] ?? 0,
                        'purchase_without_tax' => $product_data['purchase_without_tax'] ?? 0,
                        'wholesale_price' => $product_data['wholesale_price'] ?? 0,
                        'productStock' => ($product_data['quantities'] ?? 0) + ($existingStock->productStock ?? 0),
                    ]
                );

                $purchaseDetails[$key] = [
                    'stock_id' => $stock->id,
                    'batch_no' => $batch_no,
                    'purchase_id' => $purchase->id,
                    'product_id' => $product_data['product_id'],
                    'quantities' => $product_data['quantities'] ?? 0,
                    'sales_price' => $product_data['sales_price'] ?? 0,
                    'dealer_price' => $product_data['dealer_price'] ?? 0,
                    'purchase_with_tax' => $product_data['purchase_with_tax'] ?? 0,
                    'purchase_without_tax' => $product_data['purchase_without_tax'] ?? 0,
                    'wholesale_price' => $product_data['wholesale_price'] ?? 0,
                    'profit_percent' => $product_data['profit_percent'] ?? 0,
                    'expire_date' => $product_data['expire_date'] ?? NULL,
                    'mfg_date' => $product_data['mfg_date'] ?? NULL,
                ];
            }

            PurchaseDetails::insert($purchaseDetails);

            DB::commit();

            return response()->json([
                'message' => __('Data saved successfully.'),
                'data' => $purchase->load(['payment_type:id,name', 'tax:id,name,rate', 'party:id,name,phone', 'details.product:id,productName', 'details:id,purchase_id,product_id,purchase_with_tax,quantities', 'paymentTypes:id,name']),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    public function show(string $id)
    {
        $data = Purchase::with(['tax', 'user:id,name', 'payment_type:id,name', 'party:id,name,phone', 'purchaseReturns.details', 'details.product:id,productName,tax_type,product_type', 'details:id,purchase_id,product_id,purchase_with_tax,quantities,batch_no,purchase_without_tax,profit_percent,sales_price,wholesale_price,dealer_price,mfg_date,expire_date', 'paymentTypes:id,name'])
                ->findOrFail($id);

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'products' => 'required|array',
            'purchaseDate' => 'required|string',
            'party_id' => 'required|exists:parties,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discountAmount' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'totalAmount' => 'nullable|numeric',
            'dueAmount' => 'nullable|numeric',
            'paidAmount' => 'nullable|numeric',
            'isPaid' => 'nullable|boolean',
            'paymentType' => 'nullable|string',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.purchase_without_tax' => 'required|numeric',
            'products.*.purchase_with_tax' => 'required|numeric',
            'products.*.profit_percent' => 'required|numeric',
            'products.*.sales_price' => 'required|numeric',
            'products.*.wholesale_price' => 'required|numeric',
            'products.*.batch_no' => 'nullable|string',
            'products.*.expire_date' => 'nullable|string',
            'products.*.quantities' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {

            $business_id = auth()->user()->business_id;

            $batch_numbers = collect($request->products)->pluck('batch_no'); // Get the batch numbers from the request data
            $prev_stocks = Stock::whereIn('batch_no', $batch_numbers)->get();
            $prev_purchase_details = PurchaseDetails::whereIn('batch_no', $batch_numbers)->get();

            foreach ($request->products as $req_item) {
                $prev_stock = $prev_stocks->where('batch_no', $req_item['batch_no'])->first();
                $prev_purchase_detail = $prev_purchase_details->where('batch_no', $req_item['batch_no'])->first();

                if (!empty($prev_purchase_detail) && $prev_purchase_detail->quantities > $req_item['quantities']) {
                    if ($prev_stock->productStock < $req_item['quantities']) {
                        return response()->json([
                            'message' => 'The purchase quantity and stock quantity is not matched for batch no [' . $prev_stock->batch_no .']'
                        ], 406);
                    }
                }
            }

            // Revert previous stock changes
            foreach ($purchase->details as $detail) {
                Stock::where('id', $detail->stock_id)->decrement('productStock', $detail->quantities);
            }

            // Delete existing purchase details
            $purchase->details()->delete();

            $purchaseDetails = [];
            foreach ($request->products as $key => $product_data) {

                $batch_no = $product_data['batch_no'] ?? NULL;
                $existingStock = Stock::where(['batch_no' => $batch_no, 'product_id' => $product_data['product_id']])->first();

                // update or create stock
                $stock = Stock::updateOrCreate(
                    ['batch_no' => $batch_no, 'business_id' => $business_id, 'product_id' => $product_data['product_id']],
                    [
                        'product_id' => $product_data['product_id'],
                        'mfg_date' => $product_data['mfg_date'] ?? NULL,
                        'expire_date' => $product_data['expire_date'] ?? NULL,
                        'profit_percent' => $product_data['profit_percent'] ?? 0,
                        'sales_price' => $product_data['sales_price'] ?? 0,
                        'dealer_price' => $product_data['dealer_price'] ?? 0,
                        'purchase_with_tax' => $product_data['purchase_with_tax'] ?? 0,
                        'purchase_without_tax' => $product_data['purchase_without_tax'] ?? 0,
                        'wholesale_price' => $product_data['wholesale_price'] ?? 0,
                        'productStock' => ($product_data['quantities'] ?? 0) + ($existingStock->productStock ?? 0),
                    ]
                );

                $purchaseDetails[$key] = [
                    'stock_id' => $stock->id,
                    'purchase_id' => $purchase->id,
                    'batch_no' => $batch_no,
                    'product_id' => $product_data['product_id'],
                    'quantities' => $product_data['quantities'] ?? 0,
                    'sales_price' => $product_data['sales_price'] ?? 0,
                    'dealer_price' => $product_data['dealer_price'] ?? 0,
                    'purchase_with_tax' => $product_data['purchase_with_tax'] ?? 0,
                    'purchase_without_tax' => $product_data['purchase_without_tax'] ?? 0,
                    'wholesale_price' => $product_data['wholesale_price'] ?? 0,
                    'profit_percent' => $product_data['profit_percent'] ?? 0,
                    'expire_date' => $product_data['expire_date'] ?? NULL,
                    'mfg_date' => $product_data['mfg_date'] ?? NULL,
                ];
            }

            PurchaseDetails::insert($purchaseDetails);

            if ($purchase->dueAmount || $request->dueAmount) {
                $party = Party::findOrFail($request->party_id);
                $party->update([
                    'due' => $request->party_id == $purchase->party_id ? (($party->due - $purchase->dueAmount) + $request->dueAmount) : ($party->due + $request->dueAmount)
                ]);

                if ($request->party_id != $purchase->party_id) {
                    $prev_party = Party::findOrFail($purchase->party_id);
                    $prev_party->update([
                        'due' => $prev_party->due - $purchase->dueAmount
                    ]);
                }
            }

            $business = Business::findOrFail(auth()->user()->business_id);
            $business->update([
                'remainingShopBalance' => ($business->remainingShopBalance + $purchase->paidAmount) - $request->paidAmount
            ]);

            $purchase->update($request->all() + [
                'user_id' => auth()->id(),
            ]);

            $paymentTypes = $request->payment_types;
            if (is_string($paymentTypes)) {
                $paymentTypes = json_decode($paymentTypes, true);
            }
            if (!empty($paymentTypes) && is_array($paymentTypes)) {
                $syncData = [];
                foreach ($paymentTypes as $index => $pt) {
                    if (!empty($pt['payment_type_id'])) {
                        $refNumber = $purchase->id + $index;
                        $syncData[$pt['payment_type_id']] = [
                            'amount'   => $pt['amount'],
                            'ref_code' => 'P-' . $refNumber,
                        ];
                    }
                }
                $purchase->paymentTypes()->sync($syncData);
            }

            DB::commit();

            return response()->json([
                'message' => __('Data saved successfully.'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            $purchase_details = PurchaseDetails::where('purchase_id', $purchase->id)->get();
            $prev_stocks = Stock::whereIn('batch_no', $purchase_details->pluck('batch_no'))->get();

            foreach ($purchase_details as $purchase_detail) {
                $prev_stock = $prev_stocks->where('batch_no', $purchase_detail->batch_no)->first();

                if ($prev_stock->productStock < $purchase_detail->quantities) {
                    return response()->json([
                        'message' => 'The purchase quantity and stock quantity is not matched for batch no [' . $prev_stock->batch_no .']'
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
            DB::commit();

            return response()->json([
                'message' => __('Data deleted successfully.'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => __('Something was wrong.'),
            ], 406);
        }
    }
}
