<?php

namespace Modules\Business\App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportAllTypeStock;

class AcnooStockController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;

        $baseQuery = Product::where('business_id', $businessId)
            ->whereHas('stocks', function ($q) {
                $q->where('productStock', '>', 0);
            });

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

        $total_stock_value = $allProducts->sum(function ($product) {
            $firstStock = $product->stocks->first();
            return $product->stocks->sum('productStock') * ($firstStock?->purchase_with_tax ?? 0);
        });

        // Paginated product list
        $stocks = $baseQuery->with(['stocks' => function ($q) {
            $q->where('productStock', '>', 0);
        }])->latest()->paginate(10);

        return view('business::stocks.index', compact('stocks', 'total_qty', 'total_stock_value'));
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
                    ->orWhereHas('stocks', function ($q2) use ($request) {
                        $q2->where('sales_price', 'like', '%' . $request->search . '%')
                            ->orWhere('purchase_with_tax', 'like', '%' . $request->search . '%')
                            ->orWhere('productStock', 'like', '%' . $request->search . '%');
                    });
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

        $total_stock_value = $allProducts->sum(function ($product) {
            $firstStock = $product->stocks->first();
            return $product->stocks->sum('productStock') * ($firstStock?->purchase_with_tax ?? 0);
        });

        $total_qty = $allProducts->sum(fn($product) => $product->stocks->sum('productStock'));

        // Paginate result
        $perPage = $request->per_page ?? 20;
        $stocks = $baseQuery->with(['stocks' => function ($q) {
            $q->where('productStock', '>', 0);
        }])->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::stocks.datas', compact('stocks', 'total_stock_value', 'total_qty'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function exportExcel(Request $request)
    {
        $isAlert = $request->has('alert_qty') && $request->alert_qty;
        return Excel::download(new ExportAllTypeStock($isAlert), 'stocks.xlsx');
    }

    public function exportCsv(Request $request)
    {
        $isAlert = $request->has('alert_qty') && $request->alert_qty;
        return Excel::download(new ExportAllTypeStock($isAlert), 'stocks.csv');
    }
}
