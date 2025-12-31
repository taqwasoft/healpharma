<?php

namespace Modules\Business\App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportAllTypeStock implements FromView
{
    protected $alertOnly;

    public function __construct($alertOnly = false)
    {
        $this->alertOnly = $alertOnly;
    }

    public function view(): View
    {
        $query = Product::with(['stocks' => function ($q) {
            $q->where('productStock', '>', 0);
        }])->where('business_id', auth()->user()->business_id);

        if ($this->alertOnly) {
            $query->whereHas('stocks', function ($q) {
                $q->whereColumn('productStock', '<=', 'alert_qty');
            });
        }

        $stocks = $query->latest()->get();

        return view('business::stocks.excel-csv', [
            'stocks' => $stocks
        ]);
    }
}
