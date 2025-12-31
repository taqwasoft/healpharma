<?php

namespace App\Http\Controllers\Api;

use App\Models\Business;
use App\Models\Sale;
use App\Models\Party;
use App\Models\Stock;
use App\Models\SaleReturn;
use App\Models\SaleDetails;
use Illuminate\Http\Request;
use App\Models\SaleReturnDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = SaleReturn::with('sale:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'sale.party:id,name', 'details')
            ->whereBetween('return_date', [request()->start_date, request()->end_date])
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
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required',
            'sale_detail_id' => 'required|array',
            'return_amount' => 'required|array',
            'return_qty' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $business_id = auth()->user()->business_id;
            $sale_return = SaleReturn::create($request->all() + [
                'business_id' => $business_id,
            ]);

            $sale = Sale::findOrFail($request->sale_id);

            $prev_sale_data = $sale->sale_data ?? $sale->load([
                'details.product' => function ($query) {
                    $query->select('id', 'productName')
                        ->withSum('stocks', 'productStock');
                }
            ]);

            $party = Party::find($sale->party_id);
            $business = Business::findOrFail($business_id);
            $total_return_amount = array_sum($request->return_amount);
            $due  = $sale->dueAmount ?? 0;

            if ($party) {
                $party->update([
                    'due' => $party->due > $total_return_amount ? $party->due - $total_return_amount : 0,
                ]);
            }

            // Shop Balance: refund only if return > due
            if ($total_return_amount > $due) {
                $refund_amount = $total_return_amount - $due;

                // Refund can't exceed paid amount
                $refund_amount = min($refund_amount, $sale->paidAmount);

                if ($refund_amount > 0) {
                    $business->update([
                        'remainingShopBalance' => $business->remainingShopBalance - $refund_amount,
                    ]);
                }
            }

            // Update Sale record
            $sale->update([
                'sale_data' => $prev_sale_data,
                'dueAmount' => $request->dueAmount,
                'paidAmount' => $request->paidAmount,
                'totalAmount' => $request->totalAmount,
                'discountAmount' => $request->discountAmount,
                'lossProfit' => array_sum($request->lossProfit) - $request->discountAmount,
            ]);

            $data = [];
            foreach ($request->sale_detail_id as $key => $detail_id) {
                $sale_detail = SaleDetails::findOrFail($detail_id);

                // Update stock for the specific batch
                $batch = Stock::where('product_id', $sale_detail->product_id)
                    ->when($sale_detail->batch_no ?? false, function ($query) use ($sale_detail) {
                        return $query->where('batch_no', $sale_detail->batch_no);
                    })
                    ->first();

                if ($batch) {
                    $batch->increment('productStock', $request->return_qty[$key]);
                } else {
                    return response()->json(['error' => 'Batch not found.'], 404);
                }

                // Update SaleDetail record
                $sale_detail->update([
                    'lossProfit' => $request->lossProfit[$key],
                    'quantities' => $sale_detail->quantities - $request->return_qty[$key],
                ]);

                $data[] = [
                    'business_id' => $business_id,
                    'sale_detail_id' => $detail_id,
                    'sale_return_id' => $sale_return->id,
                    'return_qty' => $request->return_qty[$key],
                    'return_amount' => $request->return_amount[$key],
                ];
            }

            // Insert Sale Return Details
            SaleReturnDetails::insert($data);

            DB::commit();

            return response()->json([
                'message' => __('Data saved successfully.'),
                'data' => $sale_return,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $data = SaleReturn::with('sale:id,party_id,isPaid,totalAmount,dueAmount,paidAmount,invoiceNumber', 'sale.party:id,name', 'details')->findOrFail($id);

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data,
        ]);
    }
}
