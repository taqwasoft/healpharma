<?php

namespace App\Http\Controllers\Api;

use App\Models\Party;
use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PartyController extends Controller
{
    use HasUploader;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Party::where('business_id', auth()->user()->business_id)->latest()->get();

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|max:20|unique:parties,phone',
        ]);

        $data = Party::create($request->except('image') + [
                    'opening_balance' => $request->due,
                    'business_id' => auth()->user()->business_id,
                    'image' => $request->image ? $this->upload($request, 'image') : NULL,
                ]);

        return response()->json([
            'message' => __('Data saved successfully.'),
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Party $party)
    {
        $request->validate([
            'phone' => 'required|max:20|unique:parties,phone,' . $party->id,
        ]);

        // Check if due is being changed and there are pending sales/purchases
        if ($party->due != $request->due && ($party->sales_dues()->exists() || $party->purchases_dues()->exists())) {
            return response()->json([
                'message' => __('Cannot update due while there are pending sales or purchases.')
            ], 400);
        }

        $party = $party->update($request->except('image') + [
                        'opening_balance' => $request->due,
                        'image' => $request->image ? $this->upload($request, 'image', $party->image) : $party->image,
                    ]);

        return response()->json([
            'message' => __('Data saved successfully.'),
            'data' => $party,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Party $party)
    {
        $party->delete();
        return response()->json([
            'message' => __('Data deleted successfully.'),
        ]);
    }
}
