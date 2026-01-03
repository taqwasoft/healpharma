<?php

namespace Modules\Business\App\Http\Controllers;

use App\Models\BoxSize;
use App\Models\Manufacturer;
use App\Models\MedicineType;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Helpers\HasUploader;
use App\Models\Tax;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Modules\Business\App\Exports\ExportProduct;
use Illuminate\Support\Facades\File;

class AcnooProductController extends Controller
{
    use HasUploader;

    public function index()
    {
        $query = Product::with(['unit:id,unitName', 'category:id,categoryName', 'stocks', 'manufacterer'])
            ->where('business_id', auth()->user()->business_id);

        if (request('expired')) {
            $query->whereHas('all_stocks', function ($q) {
                $q->whereDate('expire_date', '<', today());
            });
        }

        $products = $query->latest()->paginate(10);
        return view('business::products.index', compact('products'));
    }

    public function acnooFilter(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with(['unit:id,unitName', 'category:id,categoryName', 'stocks',])
            ->where('business_id', auth()->user()->business_id)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('productName', 'like', '%' . $search . '%')
                        ->orWhere('productCode', 'like', '%' . $search . '%')
                        ->orWhereHas('stocks', function ($q) use ($search) {
                            $q->where('sales_price', 'like', '%' . $search . '%')
                                ->orWhere('productStock', 'like', '%' . $search . '%')
                                ->orWhere('dealer_price', 'like', '%' . $search . '%')
                                ->orWhere('purchase_with_tax', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('categoryName', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('unit', function ($q) use ($search) {
                            $q->where('unitName', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($search) {
                            $q->where('productStock', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::products.datas', compact('products'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function create()
    {
        $categories = Category::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $units = Unit::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $medicine_types = MedicineType::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $box_sizes = BoxSize::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $manufactures = Manufacturer::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $taxes = Tax::where('business_id', auth()->user()->business_id)->latest()->get();
        $product_id = (Product::max('id') ?? 0) + 1;
        $code = str_pad($product_id, 4, '0', STR_PAD_LEFT);

        return view('business::products.create', compact('categories', 'units', 'medicine_types', 'box_sizes', 'manufactures', 'code', 'taxes'));
    }

    public function store(Request $request)
    {
        $business_id = auth()->user()->business_id;

        $request->validate([
            'tax_id' => 'nullable|exists:taxes,id',
            'unit_id' => 'nullable|exists:units,id',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'model_id' => 'nullable|exists:product_models,id',
            'tax_type' => 'nullable|in:inclusive,exclusive',
            'productName' => 'required|string|max:255',
            'productCode' => [
                'nullable',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('business_id', auth()->user()->business_id);
                }),
            ],
            'alert_qty' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'capacity' => 'nullable|string|max:255',
            'productManufacturer' => 'nullable|string|max:255',
            'product_type' => 'required|in:single,variant',
            'stocks' => 'nullable|array',
            'stocks.*.batch_no' => [
                'nullable',
                Rule::unique('stocks', 'batch_no')->where(function ($query) use ($business_id) {
                    return $query->where('business_id', $business_id);
                }),
            ],
            'stocks.*.productStock' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.exclusive_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.inclusive_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.profit_percent' => 'nullable|numeric|max:99999999.99',
            'stocks.*.sales_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.wholesale_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.dealer_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.mfg_date' => 'nullable|date',
            'stocks.*.expire_date' => 'nullable|date|after_or_equal:stocks.*.mfg_date',
        ]);

        DB::beginTransaction();
        try {

        $tax = Tax::find($request->tax_id);
        $tax_rate = $tax->rate ?? 0;

        // Get purchase price from first stock item
        $lastStock = collect($request->stocks)->last();
        $basePrice = $lastStock['exclusive_price'] ?? 0;
        $tax_amount = 0;

        // Prepare meta array - merge existing meta with generic_name
        $meta = $request->meta ?? [];
        if ($request->generic_name) {
            $meta['generic_name'] = $request->generic_name;
        }
        if ($request->has('meta.strength')) {
            $meta['strength'] = $request->input('meta.strength');
        }

        $product = Product::create($request->except(['images', 'profit_percent', 'purchase_without_tax', 'purchase_with_tax', 'sales_price', 'dealer_price', 'wholesale_price', 'alert_qty','tax_amount', 'meta']) + [
            'business_id' => $business_id,
            'alert_qty' => $request->alert_qty ?? 0,
            'images' => $request->images ? $this->multipleUpload($request, 'images') : NULL,
            'meta' => !empty($meta) ? $meta : NULL,
        ]);

        // Create all stocks
        $stockData = [];
        if (!empty($request->stocks)) {
            foreach ($request->stocks as $stock) {
                $base_price = $stock['exclusive_price'] ?? 0;
                $purchasePrice = $request->tax_type === 'inclusive' ? $base_price + ($base_price * $tax_rate / 100) : $base_price;
                $purchaseWithTaxPrice = $base_price + ($base_price * $tax_rate / 100);

                // Calculate VAT for this stock and sum
                $tax_amount += ($base_price * $tax_rate / 100);

                $stockData[] = [
                    'business_id' => $business_id,
                    'product_id' => $product->id,
                    'batch_no' => $stock['batch_no'] ?? null,
                    'productStock' => $stock['productStock'] ?? 0,
                    'purchase_without_tax' => $purchasePrice,
                    'purchase_with_tax' => $purchaseWithTaxPrice,
                    'profit_percent' => $stock['profit_percent'] ?? 0,
                    'sales_price' => $stock['sales_price'] ?? 0,
                    'wholesale_price' => $stock['wholesale_price'] ?? 0,
                    'dealer_price' => $stock['dealer_price'] ?? 0,
                    'mfg_date' => $stock['mfg_date'] ?? null,
                    'expire_date' => $stock['expire_date'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        } else {
            // default stock if no stock found
            $stockData[] = [
                'business_id' => $business_id,
                'product_id' => $product->id,
                'batch_no' => null,
                'productStock' => 0,
                'purchase_without_tax' => $basePrice,
                'profit_percent' => 0,
                'sales_price' => 0,
                'wholesale_price' => 0,
                'dealer_price' => 0,
                'mfg_date' => null,
                'expire_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Stock::insert($stockData);

        // Update product with total vat amount
        $product->update(['tax_amount' => $tax_amount]);

        DB::commit();
        return response()->json([
            'message' => __('Product saved successfully.'),
            'redirect' => route('business.products.index')
        ]);
        } catch (\Exception) {
            DB::rollback();
            return response()->json([
                'message' => __('Something went wrong.'),
            ], 406);
        }
    }

    public function edit($id)
    {
        $product = Product::with('stocks')->withSum('stocks', 'productStock')->where('business_id', auth()->user()->business_id)->findOrFail($id);
        $categories = Category::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $units = Unit::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $medicine_types = MedicineType::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $box_sizes = BoxSize::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $manufactures = Manufacturer::where('business_id', auth()->user()->business_id)->whereStatus(1)->latest()->get();
        $taxes = Tax::where('business_id', auth()->user()->business_id)->latest()->get();

        return view('business::products.edit', compact('product', 'categories', 'units', 'medicine_types', 'box_sizes', 'manufactures', 'taxes'));
    }

     public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $business_id = auth()->user()->business_id;

        $request->validate([
            'tax_id' => 'nullable|exists:taxes,id',
            'unit_id' => 'nullable|exists:units,id',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'model_id' => 'nullable|exists:product_models,id',
            'tax_type' => 'nullable|in:inclusive,exclusive',
            'productName' => 'required|string|max:255',
            'productCode' => [
                'nullable',
                Rule::unique('products', 'productCode')->ignore($product->id)->where(function ($query) use ($business_id) {
                    return $query->where('business_id', $business_id);
                }),
            ],
            'alert_qty' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'capacity' => 'nullable|string|max:255',
            'productManufacturer' => 'nullable|string|max:255',
            'product_type' => 'required|in:single,variant',
            'stocks' => 'nullable|array',
            'stocks.*.productStock' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.exclusive_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.inclusive_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.profit_percent' => 'nullable|numeric|max:99999999.99',
            'stocks.*.sales_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.wholesale_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.dealer_price' => 'nullable|numeric|min:0|max:99999999.99',
            'stocks.*.mfg_date' => 'nullable|date',
            'stocks.*.expire_date' => 'nullable|date|after_or_equal:stocks.*.mfg_date',
        ]);

        DB::beginTransaction();
        try {
            // Tax calculation

            $existingImages = $product->images ?? [];

            if ($request->has('deleted_images')) {
                $deletedImages = $request->deleted_images;

                // Remove from disk & image list
                foreach ($deletedImages as $imgUrl) {
                    $relativePath = str_replace(asset(''), '', $imgUrl);
                    $fullPath = public_path($relativePath);

                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }

                    $existingImages = array_filter($existingImages, fn($img) => asset($img) !== $imgUrl);
                }

                $existingImages = array_values($existingImages);
            }

            // Handle file uploads (if any new added)
            $updatedImages = $request->hasFile('images') ? $this->multipleUpload($request, 'images', $existingImages) : $existingImages;

            $tax = Tax::find($request->tax_id);
            $tax_rate = $tax->rate ?? 0;
            $lastStock = collect($request->stocks)->last();
            $basePrice = $lastStock['exclusive_price'] ?? 0;
            $tax_amount = 0;

            // Delete existing stocks after saving the product
            Stock::where('product_id', $product->id)->delete();

            // Insert new stocks
            $stockData = [];
            if (!empty($request->stocks)) {
                foreach ($request->stocks as $key => $stock) {
                    $base_price = $stock['exclusive_price'] ?? 0;
                    $purchasePrice = $request->tax_type === 'inclusive' ? $base_price + ($base_price * $tax_rate / 100) : $base_price;
                    $purchaseWithTaxPrice = $base_price + ($base_price * $tax_rate / 100);

                    // Calculate VAT for this stock and sum
                    $tax_amount += ($base_price * $tax_rate / 100);

                    $stockData[] = [
                        'business_id' => $business_id,
                        'product_id' => $product->id,
                        'batch_no' => $stock['batch_no'] ?? null,
                        'productStock' => $stock['productStock'] ?? 0,
                        'purchase_without_tax' => $purchasePrice,
                        'purchase_with_tax' => $purchaseWithTaxPrice,
                        'profit_percent' => $stock['profit_percent'] ?? 0,
                        'sales_price' => $stock['sales_price'] ?? 0,
                        'wholesale_price' => $stock['wholesale_price'] ?? 0,
                        'dealer_price' => $stock['dealer_price'] ?? 0,
                        'mfg_date' => $stock['mfg_date'] ?? null,
                        'expire_date' => $stock['expire_date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            } else {
                // default stock if no stock provided
                $purchasePrice = $request->tax_type === 'inclusive' ? $basePrice + ($basePrice * $tax_rate / 100) : $basePrice;
                $stockData[] = [
                    'business_id' => $business_id,
                    'product_id' => $product->id,
                    'batch_no' => null,
                    'productStock' => 0,
                    'purchase_without_tax' => $purchasePrice,
                    'profit_percent' => 0,
                    'sales_price' => 0,
                    'wholesale_price' => 0,
                    'dealer_price' => 0,
                    'mfg_date' => null,
                    'expire_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Stock::insert($stockData);

            // Prepare meta array - merge existing meta with generic_name
            $meta = $request->meta ?? $product->meta ?? [];
            if ($request->generic_name) {
                $meta['generic_name'] = $request->generic_name;
            }
            if ($request->has('meta.strength')) {
                $meta['strength'] = $request->input('meta.strength');
            }

            // Update product
            $product->update($request->except(['images', 'profit_percent', 'purchase_without_tax', 'purchase_with_tax', 'sales_price', 'dealer_price', 'wholesale_price', 'alert_qty','tax_amount', 'meta']) + [
                    'business_id' => $business_id,
                    'alert_qty' => $request->alert_qty ?? 0,
                    'tax_amount' => $tax_amount,
                    'images' => $updatedImages,
                    'meta' => !empty($meta) ? $meta : NULL,
                ]);

            DB::commit();

            return response()->json([
                'message' => __('Product updated successfully.'),
                'redirect' => route('business.products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => __('Something went wrong.'),
                'error' => $e->getMessage()
            ], 406);
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if (file_exists($product->images)) {
            Storage::delete($product->images);
        }
        $product->delete();

        return response()->json([
            'message' => __('Product deleted successfully'),
            'redirect' => route('business.products.index')
        ]);
    }

    public function deleteAll(Request $request)
    {
        $products = Product::whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            if (file_exists($product->images)) {
                Storage::delete($product->images);
            }
        }
        Product::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message'   => __('Selected product deleted successfully'),
            'redirect'  => route('business.products.index')
        ]);
    }

    public function show($id)
    {
        $business_id = auth()->user()->business_id;
        $product = Product::with('stocks')->where('business_id', $business_id)->findOrFail($id);
        $taxes = Tax::where('business_id', $business_id)->latest()->get();

        return view('business::products.create-stock', compact('product', 'taxes'));
    }

    public function CreateStock(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $business_id = auth()->user()->business_id;

        $request->validate([
            'tax_id' => 'nullable|exists:taxes,id',
            'tax_type' => 'nullable|in:inclusive,exclusive',
            'dealer_price' => 'nullable|numeric|min:0',
            'exclusive_price' => 'required|numeric|min:0',
            'inclusive_price' => 'required|numeric|min:0',
            'profit_percent' => 'nullable|numeric',
            'sales_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'productStock' => 'required|numeric|min:0',
            'expire_date' => 'nullable|date',
            'batch_no' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calculate purchase price including tax if applicable
            $tax = Tax::find($request->tax_id);
            $tax_rate = $tax->rate ?? 0;
            $exclusive_price = $request->exclusive_price ?? 0;

            $purchase_without_tax = $request->tax_type === 'inclusive' ? $exclusive_price + ($exclusive_price * $tax_rate / 100) : $exclusive_price;
            $purchase_with_tax = $exclusive_price + ($exclusive_price * $tax_rate / 100);

            $batchNo = $request->batch_no ?? null;
            $stock = Stock::where(['batch_no' => $batchNo, 'product_id' => $product->id])->first();

            if ($stock) {
                $stock->update($request->except('productStock', 'purchase_without_tax', 'purchase_with_tax', 'sales_price', 'dealer_price', 'wholesale_price') + [
                        'productStock' => $stock->productStock + $request->productStock,
                        'purchase_without_tax' => $purchase_without_tax,
                        'purchase_with_tax' => $purchase_with_tax,
                        'sales_price' => $request->sales_price,
                        'dealer_price' => $request->dealer_price ?? 0,
                        'wholesale_price' => $request->wholesale_price ?? 0,
                    ]
                );
            } else {
                Stock::create($request->except('productStock', 'purchase_without_tax', 'purchase_with_tax', 'sales_price', 'dealer_price', 'wholesale_price') + [
                        'product_id' => $product->id,
                        'business_id' => $business_id,
                        'productStock' => $request->productStock ?? 0,
                        'purchase_without_tax' => $purchase_without_tax,
                        'purchase_with_tax' => $purchase_with_tax,
                        'sales_price' => $request->sales_price,
                        'dealer_price' => $request->dealer_price ?? 0,
                        'wholesale_price' => $request->wholesale_price ?? 0,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => __('Stock saved successfully.'),
                'redirect' => route('business.products.index'),
            ]);
        } catch (\Exception) {
            DB::rollBack();
            return response()->json([
                'message' => __('Something went wrong.'),
            ], 406);
        }
    }

    public function exportExcel()
    {
        return Excel::download(new ExportProduct, 'product.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new ExportProduct, 'product.csv');
    }
}
