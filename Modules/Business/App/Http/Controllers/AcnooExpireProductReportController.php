<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Models\Stock;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportExpiredProductReport;

class AcnooExpireProductReportController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;

        $expired_products = Stock::with([
            'product' => function ($query) {
                $query->select('id', 'productName', 'images', 'productCode', 'alert_qty', 'manufacturer_id', 'category_id', 'unit_id');
            },
            'product.category:id,categoryName',
            'product.unit:id,unitName',
            'product.manufacterer:id,name'
        ])
            ->where('business_id', $businessId)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', Carbon::today())
            ->where('productStock', '>', 0)
            ->latest()
            ->paginate(10);

        return view('business::reports.expired-products.index', compact('expired_products'));
    }


    public function acnooFilter(Request $request)
    {
        $search = $request->input('search');

        $expired_products = Stock::with([
            'product:id,productName,images,productCode,alert_qty,category_id,unit_id,manufacturer_id',
            'product.category:id,categoryName',
            'product.unit:id,unitName',
            'product.manufacterer:id,name'
        ])
            ->where('business_id', auth()->user()->business_id)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', Carbon::today())
            ->where('productStock', '>', 0)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('purchase_with_tax', 'like', '%' . $search . '%')
                        ->orWhere('productStock', 'like', '%' . $search . '%')
                        ->orWhere('sales_price', 'like', '%' . $search . '%')
                        ->orWhereHas('product', function ($q) use ($search) {
                            $q->where('productName', 'like', '%' . $search . '%')
                                ->orWhere('productCode', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('product.category', function ($q) use ($search) {
                            $q->where('categoryName', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('product.unit', function ($q) use ($search) {
                            $q->where('unitName', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::reports.expired-products.datas', compact('expired_products'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function exportExcel()
    {
        return Excel::download(new ExportExpiredProductReport, 'expired-product-reports.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new ExportExpiredProductReport, 'expired-product-reports.csv');
    }
}
