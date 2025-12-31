<?php

namespace Modules\Business\App\Exports;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportExpiredProductReport implements FromView
{

    public function view(): View
    {
        $expired_products = Stock::with([
            'product' => function ($query) {
                $query->select(
                    'id', 'productName', 'images', 'productCode',
                    'alert_qty', 'category_id', 'unit_id'
                );
            },
            'product.category:id,categoryName',
            'product.unit:id,unitName'
        ])
            ->where('business_id', auth()->user()->business_id)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', Carbon::today())
            ->where('productStock', '>', 0)
            ->latest()
            ->get();

        return view('business::reports.expired-products.excel-csv', compact('expired_products'));
    }
}
