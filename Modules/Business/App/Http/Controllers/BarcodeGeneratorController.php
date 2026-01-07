<?php

namespace Modules\Business\App\Http\Controllers;

use AgeekDev\Barcode\Facades\Barcode;
use AgeekDev\Barcode\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeGeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barcode_types = array_map(
            fn($case) => ['value' => $case->value],
            Type::cases()
        );

        $products = Product::where('business_id', auth()->user()->business_id)->latest()->get();
        return view('business::barcode-generators.index', compact('products', 'barcode_types'));
    }

    public function fetchProducts(Request $request)
    {
        $products = Product::withSum('stocks', 'productStock')
            ->where('business_id', auth()->user()->business_id)
            ->whereNotNull('productCode') // Only show products with codes
            ->where('productCode', '!=', '') // Exclude empty codes
            ->when(!empty($request->search), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('productName', 'like', '%' . $request->search . '%')
                        ->orWhere('productCode', 'like', '%' . $request->search . '%');
                });
            }, function ($q) {
                $q->limit(5); // Limit 5 when search is empty
            })
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $barcodeType = $request->input('barcode_type', Type::TYPE_CODE_128->value);
        $productIds = $request->input('product_ids', []);
        $quantities = $request->input('qty', []);
        $previewDates = $request->input('preview_date', []);

        if (empty($productIds)) {
            return response()->json(['message' => __('Please select at least one product.')], 400);
        }

        $generatedBarcodes = [];
        $productsWithoutCode = [];
        $failedProducts = [];

        foreach ($productIds as $index => $productId) {
            $product = Product::with('stocks')->find($productId);

            if (!$product) {
                continue; // Skip if product not found
            }

            // Check if product has a valid code
            if (empty($product->productCode)) {
                $productsWithoutCode[] = $product->productName;
                continue; // Skip products without codes
            }

            // Get sale price from stock or product
            $salePrice = $product->sales_price ?? 0;
            
            // If product has stocks, get the latest stock price
            if ($product->stocks && $product->stocks->isNotEmpty()) {
                $latestStock = $product->stocks->first();
                $salePrice = $latestStock->sales_price ?? $salePrice;
            }

            $qty = $quantities[$index] ?? 1;
            $previewDate = $previewDates[$index] ?? null;

            for ($i = 0; $i < $qty; $i++) {
                try {
                    $barcodeSvg = Barcode::imageType("svg")
                        ->type(Type::from($barcodeType)) // Use the barcode type enum
                        ->generate($product->productCode);

                    // Truncate product name to 20 characters
                    $productName = mb_strlen($product->productName) > 20 
                        ? mb_substr($product->productName, 0, 20) . '...' 
                        : $product->productName;

                    $generatedBarcodes[] = [
                        'product_name' => $productName,
                        'product_code' => $product->productCode,
                        'product_price' => $salePrice,
                        'product_stock' => $product->productStock,
                        'packing_date' => $previewDate,
                        'barcode_svg' => $barcodeSvg,
                        'show_product_name' => $request->input('product_name') ? true : false,
                        'product_name_size' => $request->input('product_name_size', 15),
                        'show_product_price' => $request->input('product_price') ? true : false,
                        'product_price_size' => $request->input('product_price_size', 14),
                        'show_product_code' => $request->input('product_code') ? true : false,
                        'product_code_size' => $request->input('product_code_size', 14),
                        'show_pack_date' => $request->input('pack_date') ? true : false,
                        'pack_date_size' => $request->input('pack_date_size', 12),
                    ];
                } catch (\Exception $e) {
                    // Track failed products
                    if (!in_array($product->productName, $failedProducts)) {
                        $failedProducts[] = $product->productName . ' (Error: ' . $e->getMessage() . ')';
                    }
                    continue;
                }
            }
        }

        // Check if any barcodes were generated
        if (empty($generatedBarcodes)) {
            $errorMessages = [];
            
            if (!empty($productsWithoutCode)) {
                $errorMessages[] = __('Products without codes: ') . implode(', ', $productsWithoutCode);
            }
            
            if (!empty($failedProducts)) {
                $errorMessages[] = __('Failed to generate: ') . implode('; ', $failedProducts);
            }
            
            $finalMessage = !empty($errorMessages) 
                ? implode(' | ', $errorMessages)
                : __('Unable to generate barcodes. Please check that products have valid codes.');
            
            return response()->json(['message' => $finalMessage], 400);
        }

        // Show warning if some products were skipped
        $warnings = [];
        if (!empty($productsWithoutCode)) {
            $warnings[] = __('Products skipped (no code): ') . implode(', ', $productsWithoutCode);
        }
        if (!empty($failedProducts)) {
            $warnings[] = __('Products failed: ') . implode('; ', $failedProducts);
        }
        if (!empty($warnings)) {
            session()->flash('warning', implode(' | ', $warnings));
        }

        session(['generatedBarcodes' => $generatedBarcodes]);

        return response()->json([
            'redirect'  => route('business.barcodes.index'),
            'secondary_redirect_url'  => route('business.barcodes.preview'),
        ]);
    }

    public function preview()
    {
        $generatedBarcodes = session('generatedBarcodes');
        session()->forget('generatedBarcodes');

        return view('business::barcode-generators.print', compact('generatedBarcodes'));
    }
}
