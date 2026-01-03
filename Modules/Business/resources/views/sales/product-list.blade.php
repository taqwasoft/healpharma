@forelse ($products as $product)
    @php
        $firstStock = $product->stocks->first();
        $purchaseWithoutTax = $firstStock->purchase_without_tax ?? 0;
        $purchaseWithTax = $firstStock->purchase_with_tax ?? 0;
        $salePrice = $firstStock->sales_price ?? 0;
    @endphp
    <div id="single-product" class="single-product {{ $product->id }} d-flex align-items-center justify-content-between gap-3 p-2"
         data-product_id="{{ $product->id }}"
         data-default_price="{{ $salePrice }}"
         data-product_unit_name="{{ $product->unit->unitName ?? '' }}"
         data-purchase_exclusive_price="{{ $purchaseWithoutTax  }}"
         data-purchase_inclusive_price="{{ $purchaseWithTax  }}"
         data-product_name="{{ $product->productName }}"
         data-product_image="{{ $product->images[0] ?? '' }}"
         data-expire_date="{{ $firstStock->expire_date ?? null  }}"
         data-batch_count="{{ $product->stocks->count() }}"
         data-stocks='@json($product->stocks)'
         data-route="{{ route('business.carts.store') }}">

        <div class="product-con" style="flex: 1; min-width: 0;">
            <h6 class="pro-title product_name mb-0" style="font-size: 0.9rem; white-space: normal; overflow: visible;">{{ $product->productName }}</h6>
        </div>
        <div class="price" style="flex-shrink: 0;">
            <h6 class="pro-price product_price mb-0" style="font-size: 0.85rem;">
                {{ currency_format($salePrice, 'icon', 2, business_currency()) }}</h6>
        </div>
    </div>
@empty
    <div class="alert alert-danger not-found mt-1" role="alert">
        {{ __('No product found') }}
    </div>
@endforelse
