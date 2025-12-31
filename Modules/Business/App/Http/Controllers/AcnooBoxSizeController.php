<?php

namespace Modules\Business\App\Http\Controllers;


use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BoxSize;

class AcnooBoxSizeController extends Controller
{
    use HasUploader;

    public function index(Request $request)
    {
        $box_sizes = BoxSize::where('business_id', auth()->user()->business_id)->latest()->paginate(10);
        return view('business::box-sizes.index', compact('box_sizes'));
    }

    public function acnooFilter(Request $request)
    {
        $box_sizes = BoxSize::where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::box-sizes.datas', compact('box_sizes'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'name' => 'required|unique:box_sizes,name,NULL,id,business_id,' . $business_id,
        ]);

        BoxSize::create($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Box Size created successfully'),
            'redirect' => route('business.box-sizes.index'),
        ]);
    }

    public function update(Request $request, BoxSize $boxSize)
    {
        $request->validate([
            'name' => 'required|unique:box_sizes,name,' . $boxSize->id . ',id,business_id,' . auth()->user()->business_id,
        ]);

        $boxSize->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Box Size updated successfully'),
            'redirect' => route('business.box-sizes.index'),
        ]);
    }

    public function destroy(BoxSize $boxSize)
    {
        $boxSize->delete();

        return response()->json([
            'message' => __('Box Size deleted successfully'),
            'redirect' => route('business.box-sizes.index'),
        ]);
    }

    public function status(Request $request, $id)
    {
        $box_size = BoxSize::findOrFail($id);
        $box_size->update(['status' => $request->status]);
        return response()->json(['message' => __('Box Size')]);
    }

    public function deleteAll(Request $request)
    {
        BoxSize::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message' => __('Selected Box Size deleted successfully'),
            'redirect' => route('business.box-sizes.index')
        ]);
    }
}
