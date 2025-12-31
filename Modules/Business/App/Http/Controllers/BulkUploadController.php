<?php

namespace Modules\Business\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class BulkUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('business::bulk-uploads.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $businessId = auth()->user()->business_id;

        Excel::import(new ProductImport($businessId), $request->file('file'));

        return response()->json([
            'message' => __('Bulk upload successfully.'),
            'redirect' => route('business.products.index')
        ]);
    }
}
