<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function index()
    {
        $business_id = auth()->user()->business_id;

        $products_count = Product::where('business_id', $business_id)->count();

        $low_stock_count = DB::table('products')
            ->where('products.business_id', $business_id)
            ->join('stocks', 'products.id', '=', 'stocks.product_id')
            ->select('products.id', 'products.alert_qty', DB::raw('SUM(stocks.productStock) as totalStock'))
            ->groupBy('products.id', 'products.alert_qty')
            ->havingRaw('totalStock < alert_qty')
            ->count();

        $total_stock_value = DB::table('stocks')
            ->where('stocks.business_id', $business_id)
            ->select(DB::raw('SUM(stocks.productStock * stocks.purchase_without_tax) as totalStockValue'))
            ->value('totalStockValue');

        $stocks = Product::select('id', 'productName', 'product_type')
            ->when(request('search'), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('productName', 'like', '%' . request('search') . '%')
                        ->orWhere('productCode', 'like', '%' . request('search') . '%')
                        ->orWhereHas('stocks', function ($query) {
                            $query->where('batch_no', 'like', '%' . request('search') . '%');
                        });
                });
            })
            ->withSum('stocks', 'productStock')
            ->where('business_id', $business_id)
            ->with('stocks:id,business_id,product_id,batch_no,expire_date,productStock,purchase_without_tax,sales_price')
            ->latest()
            ->paginate(10);

        // Attach latest stock fields to each product
        $stocks->getCollection()->transform(function ($product) {
            $latestStock = $product->stocks->sortByDesc('created_at')->first();

            $product->purchase_with_tax = $latestStock->purchase_with_tax ?? 0;
            $product->sales_price = $latestStock->sales_price ?? 0;

            return $product;
        });

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'total_products' => (int) $products_count,
            'low_stock_count' => $low_stock_count,
            'total_stock_value' => (float) $total_stock_value,
            'stocks' => $stocks,
        ]);
    }

    public function destroy($id)
    {
        $stock = Stock::findOrfail($id);
        $stock->delete();

        return response()->json([
            'message' => __('Data deleted successfully.'),
        ]);
    }

}
