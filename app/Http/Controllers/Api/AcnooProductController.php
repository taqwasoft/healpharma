<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use App\Models\Product;
use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AcnooProductController extends Controller
{
    use HasUploader;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_id = auth()->user()->business_id;

        $data = Product::select('id', 'business_id', 'productName', 'productCode', 'product_type')
            ->where('business_id', $business_id)
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->where('productName', 'like', '%' . request('search') . '%')
                        ->orWhere('productCode', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('expire_date'), function ($query) {
                $query->whereHas('stocks', function ($query) {
                    $query->whereBetween('expire_date', [today(), request('expire_date')]);
                });
            })
            ->when(request('expired') == 'true', function ($query) {
                $query->whereHas('stocks', function ($query) {
                    $query->where('expire_date', '<', today());
                });
            })
            ->withSum('stocks', 'productStock')
            ->with(['expiring_item' => function ($query) {
                $query->select('id', 'product_id', 'expire_date')
                    ->where('productStock', '>', 0)
                    ->whereNotNull('expire_date');
            }, 'all_stocks' => function ($query) {
                $query->select('id', 'product_id', 'purchase_with_tax', 'sales_price', 'created_at');
            }])
            ->latest()
            ->paginate(10);

        // Attach latest stock fields to product
        $data->getCollection()->transform(function ($product) {
            $latestStock = $product->all_stocks
                ->sortByDesc('created_at')
                ->first();

            $product->purchase_with_tax = $latestStock->purchase_with_tax ?? null;
            $product->sales_price = $latestStock->sales_price ?? null;

            unset($product->all_stocks);

            return $product;
        });

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
        $business_id = auth()->user()->business_id;

        $request->validate([
            'productName' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'type_id' => 'nullable|exists:medicine_types,id',
            'unit_id' => 'nullable|exists:units,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'box_size_id' => 'nullable|exists:box_sizes,id',
            'productCode' => [
                'nullable',
                Rule::unique('products')->where(function ($query) use ($business_id) {
                    return $query->where('business_id', $business_id);
                }),
            ],
            'batch_no' => [
                'nullable',
                Rule::unique('stocks')->where(function ($query) use ($business_id) {
                    return $query->where('business_id', $business_id);
                }),
            ],
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create($request->except('images', 'alert_qty', 'expire_date', 'productStock', 'profit_percent', 'purchase_without_tax', 'purchase_with_tax', 'sales_price', 'wholesale_price','dealer_price') + [
                    'business_id' => $business_id,
                    'alert_qty' => $request->alert_qty ?? 0,
                    'images' => $request->images ? $this->multipleUpload($request, 'images') : null,
                ]);

            if ($request->product_type == 'variant') {
                $stockData = [];

                foreach ($request->batch_no ?? [] as $key => $batch_no) {

                    $stockData[] = [
                        'business_id' => $business_id,
                        'product_id' => $product->id,
                        'batch_no' => $batch_no,
                        'productStock' =>  $request->productStock[$key] ?? 0,
                        'purchase_without_tax' => $request->purchase_without_tax[$key] ?? 0,
                        'purchase_with_tax' => $request->purchase_with_tax[$key] ?? 0,
                        'profit_percent' => $request->profit_percent[$key] ?? 0,
                        'sales_price' => $request->sales_price[$key] ?? 0,
                        'wholesale_price' => $request->wholesale_price[$key] ?? 0,
                        'dealer_price' => $request->dealer_price[$key] ?? 0,
                        'mfg_date' => $request->mfg_date[$key] ?? null,
                        'expire_date' => $request->expire_date[$key] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Stock::insert($stockData);
            } else {
                Stock::create([
                    'business_id' => $business_id,
                    'product_id' => $product->id,
                    'productStock' => $request->productStock ?? 0,
                    'purchase_without_tax' => $request->purchase_without_tax ?? 0,
                    'purchase_with_tax' => $request->purchase_with_tax ?? 0,
                    'profit_percent' => $request->profit_percent ?? 0,
                    'sales_price' => $request->sales_price ?? 0,
                    'wholesale_price' => $request->wholesale_price ?? 0,
                    'dealer_price' => $request->dealer_price ?? 0,
                    'mfg_date' => $request->mfg_date ?? null,
                    'expire_date' => $request->expire_date ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => __('Data saved successfully.'),
                'data' => $product,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 406);
        }
    }

    public function show(string $id)
    {
        $data = Product::query()
                    ->with('unit:id,unitName', 'medicine_type:id,name', 'manufacterer:id,name', 'box_size:id,name', 'category:id,categoryName', 'all_stocks', 'tax:id,rate')
                    ->withSum('stocks', 'productStock')
                    ->findOrFail($id);

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'productName' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'type_id' => 'nullable|exists:medicine_types,id',
            'unit_id' => 'nullable|exists:units,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'box_size_id' => 'nullable|exists:box_sizes,id',
            'productCode' => [
                'nullable',
                'unique:products,productCode,' . $product->id . ',id,business_id,' . $business_id,
            ],
        ]);


        DB::beginTransaction();
        try {

            if ($request->removed_images) {

                $prev_images = array_diff($product->images ?? [], $request->removed_images);
                foreach ($request->removed_images as $image) {
                    if (Storage::exists($image)) {
                        Storage::delete($image);
                    }
                }

                $prev_images = array_values($prev_images);
            } else {
                $prev_images = $product->images ?? [];
            }

            $new_images = $request->images ? $this->multipleUpload($request, 'images') : [];
            $merged_images = array_merge($prev_images, $new_images);

            $product->update($request->except('business_id','images', 'alert_qty', 'expire_date', 'productStock', 'profit_percent', 'purchase_without_tax', 'purchase_with_tax', 'sales_price', 'wholesale_price','dealer_price') + [
                    'alert_qty' => $request->alert_qty ?? 0,
                    'images' => $merged_images
                ]);

            Stock::where('product_id', $product->id)->delete();

            if ($request->product_type == 'variant') {

                $stockData = [];

                foreach ($request->batch_no ?? [] as $key => $batch_no) {

                    $stockData[] = [
                        'batch_no' => $batch_no,
                        'business_id' => $business_id,
                        'product_id' => $product->id,
                        'productStock' => $request->productStock[$key] ?? 0,
                        'purchase_without_tax' => $request->purchase_without_tax[$key] ?? 0,
                        'purchase_with_tax' => $request->purchase_with_tax[$key] ?? 0,
                        'profit_percent' => $request->profit_percent[$key] ?? 0,
                        'sales_price' => $request->sales_price[$key] ?? 0,
                        'wholesale_price' => $request->wholesale_price[$key] ?? 0,
                        'dealer_price' => $request->dealer_price[$key] ?? 0,
                        'mfg_date' => $request->mfg_date[$key] ?? null,
                        'expire_date' => $request->expire_date[$key] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Stock::insert($stockData);
            } else {
                Stock::create([
                    'business_id' => $business_id,
                    'product_id' => $product->id,
                    'productStock' => $request->productStock ?? 0,
                    'purchase_without_tax' => $request->purchase_without_tax ?? 0,
                    'purchase_with_tax' => $request->purchase_with_tax ?? 0,
                    'profit_percent' => $request->profit_percent ?? 0,
                    'sales_price' => $request->sales_price ?? 0,
                    'wholesale_price' => $request->wholesale_price ?? 0,
                    'dealer_price' => $request->dealer_price ?? 0,
                    'mfg_date' => $request->mfg_date ?? null,
                    'expire_date' => $request->expire_date ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => __('Data updated successfully.'),
                'data' => $product,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 406);
        }
    }

    public function updateStock(Request $request, string $id)
    {
        $request->validate([
            'batch_no' => 'nullable|string',
            'tax_type' => 'nullable|string',
            'expire_date' => 'nullable|date',
            'tax_id' => 'nullable|exists:taxes,id',
            'purchase_without_tax' => 'nullable|numeric',
            'purchase_with_tax' => 'nullable|numeric',
            'profit_percent' => 'nullable|numeric',
            'sales_price' => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
            'qty' => 'required|numeric|min:0',
        ]);


        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->update($request->all());

            $stock = Stock::where('product_id', $product->id)->where('batch_no', $request->batch_no)->first();

            if ($stock) {
                $stock->update($request->except('productStock', 'business_id') + [
                    'productStock' => $stock->productStock + $request->qty,
                ]);
            } else {
                Stock::create([
                    'business_id' => auth()->user()->business_id,
                    'product_id' => $product->id,
                    'batch_no' => $request->batch_no ?? null,
                    'productStock' => $request->qty ?? 0,
                    'purchase_without_tax' => $request->purchase_without_tax ?? 0,
                    'purchase_with_tax' => $request->purchase_with_tax ?? 0,
                    'profit_percent' => $request->profit_percent ?? 0,
                    'sales_price' => $request->sales_price ?? 0,
                    'wholesale_price' => $request->wholesale_price ?? 0,
                    'dealer_price' => $request->dealer_price ?? 0,
                    'mfg_date' => $request->mfg_date ?? null,
                    'expire_date' => $request->expire_date ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => __('Stock updated successfully.'),
                'data' => $product
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 406);
        }
    }

    public function destroy(Product $product)
    {
        foreach ($product->images ?? [] as $image) {
            if (Storage::exists($image)) {
                Storage::delete($image);
            }
        }

        $product->delete();

        return response()->json([
            'message' => __('Data deleted successfully.'),
        ]);
    }


    public function stocksWithProduct()
    {
        $data = Stock::select('id', 'product_id', 'batch_no', 'productStock', 'purchase_without_tax','purchase_with_tax', 'profit_percent', 'sales_price' , 'wholesale_price', 'dealer_price', 'mfg_date', 'expire_date')
            ->with([
                'product.tax:id,rate,name',
                'product:id,productName,tax_id,tax_type,productCode,product_type',
            ])
            ->where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('batch_no', 'like', '%' . request('search') . '%')
                        ->orWhereHas('product', function ($query) {
                            $query->where('productName', 'like', '%' . request('search') . '%')
                                ->orWhere('productCode', 'like', '%' . request('search') . '%');
                        });
                });
            })
            ->when(request('check_stock') == 'true', function ($query) {
                $query->where('productStock', '>', 0);
            })
            ->when(request('check_expire') == 'true', function ($query) {
                $query->where(function ($q) {
                    $q->where('expire_date', '>=', today())
                        ->orWhereNull('expire_date');
                });
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data
        ]);
    }

}
