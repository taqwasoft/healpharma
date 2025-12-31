<?php

namespace Modules\Business\App\Http\Controllers;

use App\Models\Category;
use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AcnooCategoryController extends Controller
{
    use HasUploader;

    public function index(Request $request)
    {
        $categories = Category::where('business_id', auth()->user()->business_id)->latest()->paginate(10);
        return view('business::categories.index', compact('categories'));
    }

    public function acnooFilter(Request $request)
    {
        $categories = Category::where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('categoryName', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . request('search') . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::categories.datas', compact('categories'))->render()
            ]);
        }
        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'categoryName' => 'required|unique:categories,categoryName,NULL,id,business_id,' . $business_id,
            'description' => 'nullable|string|max:255',
        ]);

        Category::create($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Category created successfully'),
            'redirect' => route('business.categories.index'),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'categoryName' => ['required', 'unique:categories,categoryName,' . $category->id . ',id,business_id,' . auth()->user()->business_id],
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($request->except('business_id') + [
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('Category updated successfully'),
            'redirect' => route('business.categories.index'),
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => __('Category deleted successfully'),
            'redirect' => route('business.categories.index'),
        ]);
    }

    public function status(Request $request, $id)
    {
        $categoryStatus = Category::findOrFail($id);
        $categoryStatus->update(['status' => $request->status]);
        return response()->json(['message' => __('Category')]);
    }

    public function deleteAll(Request $request)
    {
        $idsToDelete = $request->input('ids');
        DB::beginTransaction();
        try {

            Category::whereIn('id', $idsToDelete)->delete();
            DB::commit();

            return response()->json([
                'message' => __('Selected Category deleted successfully'),
                'redirect' => route('business.categories.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(__('Something was wrong.'), 400);
        }
    }
}
