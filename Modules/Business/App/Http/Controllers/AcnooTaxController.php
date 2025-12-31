<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcnooTaxController extends Controller
{

    public function index(Request $request)
    {
        $taxes = Tax::where('business_id', auth()->user()->business_id)->orderBy('status', 'desc')->whereNull('sub_tax')->latest()->paginate(10);
        $tax_groups = Tax::where('business_id', auth()->user()->business_id)->orderBy('status', 'desc')->whereNotNull('sub_tax')->latest()->paginate(10);
        return view('business::taxes.index', compact('taxes', 'tax_groups'));
    }

    public function acnooFilter(Request $request)
    {
        $taxes = Tax::where('business_id', auth()->user()->business_id)->whereNull('sub_tax')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $search = $request->search;
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::taxes.datas', compact('taxes'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function taxGroupFilter(Request $request)
    {
        $tax_groups = Tax::where('business_id', auth()->user()->business_id)->whereNotNull('sub_tax')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $search = $request->search;
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::tax-groups.datas', compact('tax_groups'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    // Tax Group Create
    public function create()
    {
        $taxes = Tax::where('business_id', auth()->user()->business_id)->where('status', '1')->whereNull('sub_tax')->latest()->get();
        return view('business::tax-groups.create', compact('taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_ids' => 'required_if:rate,null',
            'rate' => 'required_if:rate,null|numeric',
        ]);

        // single tax
        if ($request->rate && !$request->tax_ids) {
            Tax::create($request->all() + [
                'business_id' => auth()->user()->business_id,
            ]);
        }
        // group tax
        elseif (!$request->rate && $request->tax_ids) {

            $taxes = Tax::whereIn('id', $request->tax_ids)->select('id', 'name', 'rate')->get();

            $tax_rate = 0;
            $sub_taxes = [];

            foreach ($taxes as $tax) {
                $sub_taxes[] = [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'rate' => $tax->rate,
                ];
                $tax_rate += $tax->rate;
            }

            Tax::create([
                'rate' => $tax_rate,
                'sub_tax' => $sub_taxes,
                'name' => $request->name,
                'status' => $request->status,
                'business_id' => auth()->user()->business_id,
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid data format.',
            ], 406);
        }

        return response()->json([
            'message' => 'Tax created successfully',
            'redirect' => route('business.taxes.index'),
        ]);
    }

    // Tax Group Edit
    public function edit($id)
    {
        $tax = Tax::where('business_id', auth()->user()->business_id)->findOrFail($id);
        $taxes = Tax::where('business_id', auth()->user()->business_id)->where('status', '1')->whereNull('sub_tax')->latest()->paginate(10);
        return view('business::tax-groups.edit', compact('tax', 'taxes'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_ids' => 'required_if:rate,null',
            'rate' => 'required_if:rate,null|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Single TAX update
            if ($request->rate && !$request->tax_ids) {

                $tax->update($request->only(['name', 'rate', 'status']));

                $taxGroupExist = Tax::where('sub_tax', 'LIKE', '%"id":' . $tax->id . '%')->get();
                foreach ($taxGroupExist as $group) {
                    $subtaxes = collect($group->sub_tax)->map(function ($subtax) use ($tax) {
                        if ($subtax['id'] == $tax->id) {
                            $subtax['rate'] = $tax->rate;
                            $subtax['name'] = $tax->name;
                        }
                        return $subtax;
                    });

                    $group->update([
                        'rate' => $subtaxes->sum('rate'),
                        'sub_tax' => $subtaxes->toArray(),
                    ]);
                }
            }

            // Group TAX update
            elseif (!$request->rate && $request->tax_ids) {

                $taxes = Tax::whereIn('id', $request->tax_ids)->select('id', 'name', 'rate')->get();

                $tax_rate = 0;
                $sub_taxes = [];

                foreach ($taxes as $single_tax) {
                    $sub_taxes[] = [
                        'id' => $single_tax->id,
                        'name' => $single_tax->name,
                        'rate' => $single_tax->rate,
                    ];
                    $tax_rate += $single_tax->rate;
                }

                $tax->update([
                    'rate' => $tax_rate,
                    'sub_tax' => $sub_taxes,
                    'name' => $request->name,
                    'status' => $request->status ?? $tax->status,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Invalid data format.',
                ], 406);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tax updated successfully',
                'redirect' => route('business.taxes.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('Somethings went wrong!')], 404);
        }
    }

    public function destroy(Tax $tax)
    {
        // When sub_tax is null
        if (is_null($tax->sub_tax)) {
            // Check if this TAX exists in any other TAX's sub_tax
            $taxGroupExist = Tax::where('sub_tax', 'LIKE', '%"id":' . $tax->id . '%')->exists();

            if ($taxGroupExist) {
                return response()->json([
                    'message' => 'Cannot delete. This TAX is part of a TAX group.',
                ], 404);
            }
        }

        $tax->delete();

        return response()->json([
            'message' => 'TAX Deleted Successfully',
            'redirect' => route('business.taxes.index'),
        ]);
    }

    public function status(Request $request, $id)
    {
        $status = Tax::findOrFail($id);
        $status->update(['status' => $request->status]);
        return response()->json(['message' => 'Tax']);
    }

    public function deleteAll(Request $request)
    {
        $taxes = Tax::whereIn('id', $request->ids)->get();

        // Filter out taxes that are part of a TAX group when sub_tax is null
        $restrictedtaxes = $taxes->filter(function ($tax) {
            return is_null($tax->sub_tax) &&
                Tax::where('sub_tax', 'LIKE', '%"id":' . $tax->id . '%')->exists();
        });

        // If there are restricted taxes
        if ($restrictedtaxes->isNotEmpty()) {
            return response()->json([
                'message' => 'Some taxes cannot be deleted as they are part of a TAX group.',
            ], 404);
        }

        Tax::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => __('Selected items deleted successfully.'),
            'redirect' => route('business.taxes.index'),
        ]);
    }
}
