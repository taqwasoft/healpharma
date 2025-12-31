<?php

namespace Modules\Business\App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Party;
use App\Models\Stock;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()) {
            return redirect()->back()->with('error', 'You have no permission to access.');
        }

        $stocks = Stock::where('business_id', auth()->user()->business_id)
                            ->whereHas('product', function ($query) {
                                $query->whereColumn('productStock', '<=', 'alert_qty');
                            })
                            ->with('product:id,productName,alert_qty')
                            ->latest()
                            ->take(5)
                            ->get();
        $top_products = DB::table('sale_details')
                            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
                            ->join('products', 'products.id', '=', 'sale_details.product_id')
                            ->where('sales.business_id', auth()->user()->business_id)
                            ->select(
                                'products.id',
                                'products.productName',
                                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(products.images, '$[0]')) as image"),
                                'sale_details.batch_no',
                                'sale_details.price',
                                DB::raw('SUM(sale_details.quantities) as total_quantity')
                            )
                            ->groupBy(
                                'products.id',
                                'products.productName',
                                'products.images',
                                'sale_details.batch_no',
                                'sale_details.price'
                            )
                            ->orderByDesc('total_quantity')
                            ->limit(5)
                            ->get();
        $top_customers = DB::table('sales')
                            ->join('parties', 'sales.party_id', '=', 'parties.id')
                            ->select(
                                'parties.id',
                                'parties.name',
                                'parties.phone',
                                'parties.image',
                                DB::raw('COUNT(sales.id) as total_sales'),
                                DB::raw('SUM(sales.actual_total_amount) as total_amount')
                            )
                            ->where('sales.business_id', auth()->user()->business_id)
                            ->whereMonth('sales.saleDate', Carbon::now()->month)
                            ->whereYear('sales.saleDate', Carbon::now()->year)
                            ->groupBy('parties.id',
                                       'parties.name',
                                       'parties.phone',
                                       'parties.image',
                             )
                            ->orderByDesc('total_sales', 'total_amount')
                            ->limit(5)
                            ->get();
        $expired_products = Stock::select('id', 'product_id', 'expire_date', 'batch_no')
                            ->with([
                                'product' => function ($query) {
                                    $query->select('id', 'productName', 'images');
                                }
                            ])
                            ->where('business_id', auth()->user()->business_id)
                            ->whereNotNull('expire_date')
                            ->where('expire_date', '<', Carbon::today())
                            ->where('productStock', '>', 0)
                            ->latest()
                            ->take(5)
                            ->get();

        return view('business::dashboard.index', compact('stocks', 'top_products', 'top_customers', 'expired_products'));
    }

    public function getDashboardData()
    {
        $businessId = auth()->user()->business_id;

        $today_customer = Party::where('business_id', $businessId)
                        ->where('type', '!=', 'Supplier')
                        ->whereDate('created_at', today())
                        ->count();
        $yesterday_customer = Party::where('business_id', $businessId)
                        ->where('type', '!=', 'Supplier')
                        ->whereDate('created_at', Carbon::yesterday())
                        ->count();
        $customer_percentage = (abs($today_customer - $yesterday_customer) / max($yesterday_customer, 1)) * 100;
        $customer_percentage =  min($customer_percentage, 100);
        $customer_percentage = intval(round($customer_percentage)) . '%';

        $total_customer = Party::where('business_id', $businessId)->where('type', '!=', 'Supplier')->count();

        $today_supplier = Party::where('business_id', $businessId)
                                ->whereType('Supplier')
                                ->whereDate('created_at', today())
                                ->count();
        $yesterday_supplier = Party::where('business_id', $businessId)
                                    ->whereType('Supplier')
                                    ->whereDate('created_at', Carbon::yesterday())
                                    ->count();
        $supplier_percentage = (abs($today_supplier - $yesterday_supplier) / max($yesterday_supplier, 1)) * 100;
        $supplier_percentage = min($supplier_percentage, 100);
        $supplier_percentage = intval(round($supplier_percentage)) . '%';

        $total_supplier = Party::where('business_id', $businessId)->whereType('Supplier')->count();

        $today_stock = Stock::where('business_id', $businessId)
                                ->whereDate('updated_at', today())
                                ->sum('productStock');
        $yesterday_stock = Stock::where('business_id', $businessId)
                                ->whereDate('updated_at', Carbon::yesterday())
                                ->sum('productStock');
        $stock_percentage = (abs($today_stock - $yesterday_stock) / max($yesterday_stock, 1)) * 100;
        $stock_percentage =  min($stock_percentage, 100);
        $stock_percentage = intval(round($stock_percentage)) . '%';

        $total_stock = Stock::where('business_id', $businessId)
                                ->sum('productStock');


        $total_expire = Stock::where('business_id', $businessId)
                                    ->whereNotNull('expire_date')
                                    ->where('expire_date', '<', today())
                                    ->where('productStock', '>', 0)
                                    ->count();
        $today_expire = Stock::where('business_id', $businessId)
                                    ->whereDate('created_at', today())
                                    ->whereNotNull('expire_date')
                                    ->where('expire_date', '<', today())
                                    ->where('productStock', '>', 0)
                                    ->count();
        $yesterday_expire = Stock::where('business_id', $businessId)
                                    ->whereDate('created_at', Carbon::yesterday())
                                    ->whereNotNull('expire_date')
                                    ->where('expire_date', '<', today())
                                    ->where('productStock', '>', 0)
                                    ->count();
        $expire_percentage = (abs($today_expire - $yesterday_expire) / max($yesterday_expire, 1)) * 100;
        $expire_percentage = min($expire_percentage, 100);
        $expire_percentage = intval(round($expire_percentage)) . '%';

        $customer_arrow = getArrow($yesterday_customer, $today_customer);
        $supplier_arrow = getArrow($yesterday_supplier, $today_supplier);
        $stock_arrow = getArrow($yesterday_stock, $today_stock);
        $expire_arrow = getArrow($yesterday_expire, $today_expire);

        $data = [
            'total_customer' => $total_customer,
            'today_customer' => $today_customer,
            'customer_percentage' => $customer_percentage,
            'customer_arrow' => $customer_arrow,

            'total_supplier' => $total_supplier,
            'today_supplier' => $today_supplier,
            'supplier_percentage' => $supplier_percentage,
            'supplier_arrow' => $supplier_arrow,

            'total_stock' => $total_stock,
            'today_stock' => $today_stock,
            'stock_percentage' => $stock_percentage,
            'stock_arrow' => $stock_arrow,

            'total_expire' => $total_expire,
            'today_expire' => $today_expire,
            'expire_percentage' => $expire_percentage,
            'expire_arrow' => $expire_arrow,
        ];

        return response()->json($data);
    }

    public function overall_report() {
        $businessId = auth()->user()->business_id;

        // Calculate overall values
        $overall_purchase = Purchase::where('business_id', $businessId)
            ->whereYear('created_at', request('year') ?? date('Y'))
            ->sum('totalAmount');

        $overall_sale = Sale::where('business_id', $businessId)
            ->whereYear('created_at', request('year') ?? date('Y'))
            ->sum('totalAmount');

        $overall_income = Income::where('business_id', $businessId)
            ->whereYear('incomeDate', request('year') ?? date('Y'))
            ->sum('amount');

        $overall_expense = Expense::where('business_id', $businessId)
            ->whereYear('expenseDate', request('year') ?? date('Y'))
            ->sum('amount');

        $sale_loss_profit = Sale::where('business_id', $businessId)
            ->whereYear('created_at', request('year') ?? date('Y'))
            ->sum('lossProfit');

        $today_loss_profit = Sale::where('business_id', $businessId)
            ->whereYear('created_at', today())
            ->sum('lossProfit');

        // Update income and expense based on lossProfit value
        $overall_income += $sale_loss_profit > 0 ? $sale_loss_profit : 0;
        $overall_expense += $sale_loss_profit < 0 ? abs($sale_loss_profit) : 0;

        $data = [
            'overall_purchase' => $overall_purchase,
            'overall_sale' => $overall_sale,
            'overall_income' => $overall_income,
            'overall_expense' => $overall_expense,
            'today_loss_profit' =>  $today_loss_profit > 0 ? $today_loss_profit : 0
        ];

        return response()->json($data);
    }


    public function lossProfit(){
        $data['loss'] = Sale::where('business_id', auth()->user()->business_id)
                                ->whereYear('created_at', request('year') ?? date('Y'))
                                ->where('lossProfit', '<', 0)
                                ->selectRaw('MONTHNAME(created_at) as month, SUM(ABS(lossProfit)) as total')
                                ->orderBy('created_at')
                                ->groupBy('created_at')
                                ->get();

        $data['profit'] = Sale::where('business_id', auth()->user()->business_id)
                                ->whereYear('created_at', request('year') ?? date('Y'))
                                ->where('lossProfit', '>=', 0)
                                ->selectRaw('MONTHNAME(created_at) as month, SUM(ABS(lossProfit)) as total')
                                ->orderBy('created_at')
                                ->groupBy('created_at')
                                ->get();

        return response()->json($data);

    }

    public function salePurchase(){
        $data['sale'] = Sale::where('business_id', auth()->user()->business_id)
                                ->whereYear('created_at', request('year') ?? date('Y'))
                                ->selectRaw('MONTHNAME(created_at) as month, SUM(totalAmount) as total')
                                ->orderBy('created_at')
                                ->groupBy('created_at')
                                ->get();

        $data['purchase'] = Purchase::where('business_id', auth()->user()->business_id)
                                ->whereYear('created_at', request('year') ?? date('Y'))
                                ->selectRaw('MONTHNAME(created_at) as month, SUM(totalAmount) as total')
                                ->orderBy('created_at')
                                ->groupBy('created_at')
                                ->get();

        return response()->json($data);

    }
}
