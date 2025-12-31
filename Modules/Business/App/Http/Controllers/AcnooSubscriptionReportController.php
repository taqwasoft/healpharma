<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanSubscribe;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\App\Exports\ExportSubscription;

class AcnooSubscriptionReportController extends Controller
{
    public function index()
    {
        $subscribers = PlanSubscribe::with(['plan:id,subscriptionName', 'business:id,companyName,business_category_id,pictureUrl', 'business.category:id,name', 'gateway:id,name'])->where('business_id', auth()->user()->business_id)->latest()->paginate(10);

        return view('business::reports.subscription-reports.subscription-reports', compact('subscribers'));
    }

    public function acnooFilter(Request $request)
    {
        $search = $request->input('search');

        $subscribers = PlanSubscribe::with([
            'plan:id,subscriptionName',
            'business:id,companyName,business_category_id,pictureUrl',
            'business.category:id,name',
            'gateway:id,name'
        ])
            ->where('business_id', auth()->user()->business_id)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('duration', 'like', '%' . $search . '%')
                        ->orWhereHas('plan', function ($q) use ($search) {
                            $q->where('subscriptionName', 'like', '%' . $search . '%')
                                ->orwhere('payment_status', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('gateway', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::reports.subscription-reports.datas', compact('subscribers'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function exportExcel()
    {
        return Excel::download(new ExportSubscription, 'subscribers.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new ExportSubscription, 'subscribers.csv');
    }

    public function getInvoice($invoice_id)
    {
        $subscriber = PlanSubscribe::with(['plan:id,subscriptionName', 'business:id,companyName,business_category_id,pictureUrl,phoneNumber,address', 'business.category:id,name', 'gateway:id,name'])->where('business_id', auth()->user()->business_id)->findOrFail($invoice_id);
        return view('business::reports.subscription-reports.invoice', compact('subscriber'));
    }
}
