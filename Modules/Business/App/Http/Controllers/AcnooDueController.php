<?php

namespace Modules\Business\App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Party;
use App\Models\Business;
use App\Models\Purchase;
use App\Models\DueCollect;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AcnooDueController extends Controller
{
    public function index()
    {
        $total_supplier_due = Party::where('business_id', auth()->user()->business_id)
            ->where('type', 'Supplier')
            ->sum('due');

        $total_customer_due = Party::where('business_id', auth()->user()->business_id)
            ->where('type', '!=', 'Supplier')
            ->sum('due');

        $dues = Party::with('dueCollect')
            ->where('business_id', auth()->user()->business_id)
            ->where('due', '>', 0)
            ->latest()->paginate(10);
        $walk_in_customers = Sale::whereNull('party_id')->where('dueAmount', '>', 0)->latest()->paginate(10);

        return view('business::dues.index', compact('dues', 'total_supplier_due', 'total_customer_due', 'walk_in_customers'));
    }

    public function acnooFilter(Request $request)
    {
        $dues = Party::where('business_id', auth()->user()->business_id)
            ->where('due', '>', 0)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('type', 'like', '%' . $request->search . '%')
                        ->orwhere('name', 'like', '%' . $request->search . '%')
                        ->orwhere('phone', 'like', '%' . $request->search . '%')
                        ->orwhere('due', 'like', '%' . $request->search . '%')
                        ->orwhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::dues.datas', compact('dues'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function walk_dues()
    {
        $walk_in_customers = Sale::with('dueCollect')->whereNull('party_id')->where('business_id', auth()->user()->business_id)->where('dueAmount', '>', 0)->latest()->paginate(10);

        return view('business::walk-dues.index', compact('walk_in_customers'));
    }

    public function walk_dues_filter(Request $request)
    {
        $walk_in_customers = Sale::where('business_id', auth()->user()->business_id)
            ->where('dueAmount', '>', 0)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('invoiceNumber', 'like', '%' . $request->search . '%')
                        ->orwhere('dueAmount', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::walk-dues.datas', compact('walk_in_customers'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function collectDue(Request $request, $id)
    {
        $business_id = auth()->user()->business_id;
        $payment_types = PaymentType::where('business_id', $business_id)->whereStatus(1)->latest()->get();

        if ($request->source === 'walk-in') {
            $walk_due = Sale::where('business_id', $business_id)
                ->whereNull('party_id')
                ->where('dueAmount', '>', 0)
                ->findOrFail($id);
            $is_walk_in = true;

            return view('business::dues.collect-due', compact('walk_due', 'payment_types', 'is_walk_in'));
        }

        // Regular party
        $party = Party::where('business_id', $business_id)
            ->with(['sales_dues', 'purchases_dues'])
            ->findOrFail($id);

        $is_walk_in = false;

        return view('business::dues.collect-due', compact('party', 'payment_types', 'is_walk_in'));
    }

    public function collectDueStore(Request $request)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'paymentDate' => 'required|string',
            'payDueAmount' => 'required|numeric',
            'party_id' => 'nullable|exists:parties,id',
            'invoiceNumber' => 'required_without:party_id|nullable|string',
        ]);

        $party = $request->filled('party_id') ? Party::find($request->party_id) : null;

        // when request invoice
        if ($request->invoiceNumber) {
            if ($party) {
                $request->validate(
                    [
                        'invoiceNumber' => 'nullable|exists:' . ($party->type == 'Supplier' ? 'purchases' : 'sales') . ',invoiceNumber',
                    ]
                );
                if ($party->type == 'Supplier') {
                    $invoice = Purchase::where('invoiceNumber', $request->invoiceNumber)->where('party_id', $request->party_id)->first();
                } else {
                    $invoice = Sale::where('invoiceNumber', $request->invoiceNumber)->where('party_id', $request->party_id)->first();
                }
            } else {
                $invoice = Sale::where('invoiceNumber', $request->invoiceNumber)->whereNull('party_id')->first();
            }

            if (!$invoice) {
                return response()->json([
                    'message' => 'Invoice Not Found.'
                ], 404);
            }

            if ($invoice->dueAmount < $request->payDueAmount) {
                return response()->json([
                    'message' => 'Invoice due is ' . $invoice->dueAmount . '. You cannot pay more than the invoice due amount.'
                ], 400);
            }
        }

        // No invoice, but party exists: check against party opening balance
        if (!$request->invoiceNumber) {
            if ($request->payDueAmount > $party->opening_balance) {
                return response()->json([
                    'message' => __('You can pay only ' . $party->opening_balance . ', without selecting an invoice.')
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            $data = DueCollect::create([
                'user_id' => auth()->id(),
                'business_id' => $business_id,
                'party_id' => $party?->id,
                'sale_id' => isset($invoice) && ((isset($party) && $party->type !== 'Supplier') || !isset($party)) ? $invoice->id : null,
                'purchase_id' => $party && $party->type === 'Supplier' && isset($invoice) ? $invoice->id : null,
                'invoiceNumber' => $request->invoiceNumber,
                'totalDue' => isset($invoice) ? $invoice->dueAmount : ($party?->due ?? 0),
                'dueAmountAfterPay' => isset($invoice)
                    ? ($invoice->dueAmount - $request->payDueAmount)
                    : (($party?->due ?? 0) - $request->payDueAmount),
                'payDueAmount' => $request->payDueAmount ?? 0,
                'payment_type_id' => $request->payment_type_id,
                'paymentDate' => Carbon::parse($request->paymentDate)->setTimeFrom(Carbon::now()),
            ]);

            // Update invoice due
            if (isset($invoice)) {
                $invoice->update([
                    'dueAmount' => $invoice->dueAmount - $request->payDueAmount
                ]);
            }

            // Update shop balance
            $business = Business::findOrFail($business_id);
            if ($party) {
                $business->update([
                    'remainingShopBalance' => $party->type === 'Supplier'
                        ? ($business->remainingShopBalance - $request->payDueAmount)
                        : ($business->remainingShopBalance + $request->payDueAmount),
                ]);

                // Update party dues
                $party->update([
                    'due' => $party->due - $request->payDueAmount,
                    'opening_balance' => $request->invoiceNumber
                        ? $party->opening_balance
                        : $party->opening_balance - $request->payDueAmount,
                ]);
            } else {
                $business->update([
                    'remainingShopBalance' => $business->remainingShopBalance - $request->payDueAmount,
                ]);
            }

            sendNotifyToUser($data->id, route('business.dues.index', ['id' => $data->id]), __('Due Collection has been created.'), $business_id);

            DB::commit();

            return response()->json([
                'message' => __('Collect Due saved successfully'),
                'redirect' => $party ? route('business.dues.index') : route('business.walk-dues.index'),
                'secondary_redirect_url' => $party ? route('business.collect.dues.invoice', $party->id) : route('business.collect.walk-dues.invoice', $invoice->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong!'], 404);
        }
    }

    public function getInvoice($id)
    {
        $due_collect = DueCollect::with('user:id,name,role', 'party:id,name,email,phone,type', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->where('party_id', $id)
            ->latest()
            ->first();

        $party = Party::with('dueCollect.business')->find($id);

        return view('business::dues.invoice', compact('due_collect', 'party'));
    }

    public function walkDuesGetInvoice($id)
    {
        $due_collect = DueCollect::with('user:id,name,role', 'payment_type:id,name')
            ->where('business_id', auth()->user()->business_id)
            ->whereNull('party_id')
            ->latest()
            ->first();

        $walk_in_customer = Sale::with('dueCollect.business')->find($id);

        return view('business::walk-dues.invoice', compact('due_collect', 'walk_in_customer'));
    }
}
