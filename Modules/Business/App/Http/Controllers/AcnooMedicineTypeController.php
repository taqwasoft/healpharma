<?php

namespace Modules\Business\App\Http\Controllers;

use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MedicineType;

class AcnooMedicineTypeController extends Controller
{
    use HasUploader;

    public function index(Request $request)
    {
        $medicine_types = MedicineType::where('business_id', auth()->user()->business_id)->latest()->paginate(10);
        return view('business::medicine-types.index', compact('medicine_types'));
    }

    public function acnooFilter(Request $request)
    {
        $medicine_types = MedicineType::where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::medicine-types.datas', compact('medicine_types'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'name' => 'required|unique:medicine_types,name,NULL,id,business_id,' . $business_id,
        ]);

        MedicineType::create($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Medicine Types created successfully'),
            'redirect' => route('business.medicine-types.index'),
        ]);
    }

    public function update(Request $request, MedicineType $medicineType)
    {
        $request->validate([
            'name' => 'required|unique:medicine_types,name,' . $medicineType->id . ',id,business_id,' . auth()->user()->business_id,
        ]);

        $medicineType->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Medicine Types updated successfully'),
            'redirect' => route('business.medicine-types.index'),
        ]);
    }

    public function destroy(MedicineType $medicineType)
    {
        $medicineType->delete();

        return response()->json([
            'message' => __('Medicine Type deleted successfully'),
            'redirect' => route('business.medicine-types.index'),
        ]);
    }

    public function status(Request $request, $id)
    {
        $type = MedicineType::findOrFail($id);
        $type->update(['status' => $request->status]);
        return response()->json(['message' => __('Medicine Type')]);
    }

    public function deleteAll(Request $request)
    {
        MedicineType::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => __('Selected Medicine Type deleted successfully'),
            'redirect' => route('business.medicine-types.index')
        ]);
    }
}
