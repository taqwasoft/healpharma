<?php

namespace Modules\Business\App\Http\Controllers;

use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manufacturer;

class AcnooManufacturerController extends Controller
{
    use HasUploader;

    public function index(Request $request)
    {
        $manufacturers = Manufacturer::where('business_id', auth()->user()->business_id)->latest()->paginate(10);
        return view('business::manufacturers.index', compact('manufacturers'));
    }

    public function acnooFilter(Request $request)
    {
        $manufacturers = Manufacturer::where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . request('search') . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::manufacturers.datas', compact('manufacturers'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'name' => 'required|unique:manufacturers,name,NULL,id,business_id,' . $business_id,
            'description' => 'nullable|string|max:255',
        ]);

        Manufacturer::create($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Manufacturer created successfully'),
            'redirect' => route('business.manufacturers.index'),
        ]);
    }

    public function update(Request $request, Manufacturer $manufacturer)
    {
        $request->validate([
            'name' => 'required|unique:manufacturers,name,' . $manufacturer->id . ',id,business_id,' . auth()->user()->business_id,
            'description' => 'nullable|string|max:255',
        ]);

        $manufacturer->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Manufacturer updated successfully'),
            'redirect' => route('business.manufacturers.index'),
        ]);
    }

    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();

        return response()->json([
            'message' => __('Manufacturer deleted successfully'),
            'redirect' => route('business.manufacturers.index'),
        ]);
    }

    public function status(Request $request, $id)
    {
        $type = Manufacturer::findOrFail($id);
        $type->update(['status' => $request->status]);
        return response()->json(['message' => __('Manufacturer')]);
    }

    public function deleteAll(Request $request)
    {
        Manufacturer::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => __('Selected Manufacturer deleted successfully'),
            'redirect' => route('business.manufacturers.index')
        ]);
    }
}
