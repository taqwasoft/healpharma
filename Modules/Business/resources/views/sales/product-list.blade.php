@forelse ($products as $product)
    @php
        $firstStock = $product->stocks->first();
        $purchaseWithoutTax = $firstStock->purchase_without_tax ?? 0;
        $purchaseWithTax = $firstStock->purchase_with_tax ?? 0;
        $salePrice = $firstStock->sales_price ?? 0;
    @endphp
    <div id="single-product" class="single-product {{ $product->id }}"
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

        <div class="pro-img w-100">
            <img src="{{ asset($product->images[0] ?? 'assets/images/products/box.svg') }}" alt="">
        </div>
        <div class="product-con">
            <h6 class="pro-title product_name">{{ Str::words($product->productName, 2, '.') }}</h6>

            <p class="pro-category">{{ $product->category->name ?? '' }}</p>
            <div class="price">
                <h6 class="pro-price product_price">
                    {{ currency_format($salePrice, 'icon', 2, business_currency()) }}</h6>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-danger not-found mt-1" role="alert">
        {{ __('No product found') }}
    </div>
@endforelse
