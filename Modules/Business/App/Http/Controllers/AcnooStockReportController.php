<?php

namespace Modules\Business\App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportStock;

class AcnooStockReportController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;

        $baseQuery = Product::where('business_id', $businessId)
            ->whereHas('stocks', function ($q) {
                $q->where('productStock', '>', 0);
            });

        // Check for low stock filter
        if (request('alert_qty')) {
            $baseQuery->whereHas('stocks', function ($q) {
                $q->whereColumn('productStock', '<=', 'alert_qty');
            });
        }
        // Get full product list for totals
        $allProducts = (clone $baseQuery)->with(['stocks' => function ($q) {
            $q->where('productStock', '>', 0);
        }])->get();

        $total_qty = $allProducts->sum(fn($product) => $product->stocks->sum('productStock'));

        $total_stock_value = $allProducts->sum(
            fn($product) => $product->stocks->sum(fn($stock) => $stock->productStock * $product->purchase_with_tax)
        );

        $stocks = $baseQuery->latest()->paginate(10);

        return view('business::reports.stocks.stock-reports', compact('stocks', 'total_stock_value', 'total_qty'));
    }

    public function acnooFilter(Request $request)
    {
        $businessId = auth()->user()->business_id;

        $baseQuery = Product::where('business_id', $businessId)
            ->whereHas('stocks', function ($q) {
                $q->where('productStock', '>', 0);
            });

        // Search filter
        if ($request->search) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('productName', 'like', '%' . $request->search . '%')
                    ->orWhere('purchase_with_tax', 'like', '%' . $request->search . '%')
                    ->orWhere('sales_price', 'like', '%' . $request->search . '%')
                    ->orWhere('dealer_price', 'like', '%' . $request->search . '%');
            });
        }

        // Alert quantity filter
        if ($request->alert_qty) {
            $baseQuery->whereHas('stocks', function ($q) {
                $q->whereColumn('productStock', '<=', 'alert_qty');
            });
        }

        // Get full matching products for accurate totals
        $allProducts = (clone $baseQuery)->with(['stocks' => function ($q) {
            $q->where('productStock', '>', 0);
        }])->get();

        $total_stock_value = $allProducts->sum(
            fn($product) =>
            $product->stocks->sum(fn($stock) => $stock->productStock * $product->purchase_with_tax)
        );

        $total_qty = $allProducts->sum(fn($product) => $product->stocks->sum('productStock'));

        $perPage = $request->per_page ?? 20;
        $stocks = $baseQuery->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::reports.stocks.datas', compact('stocks', 'total_stock_value', 'total_qty'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function exportExcel()
    {
        return Excel::download(new ExportStock, 'stock-reports.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new ExportStock, 'stock-reports.csv');
    }
}
