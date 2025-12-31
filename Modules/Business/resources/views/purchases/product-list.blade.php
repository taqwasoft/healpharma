@forelse ($products as $product)
    @php
        $firstStock = $product->all_stocks->first();
    @endphp
    <div id="single-product" class="single-product {{ $product->id }}"
         data-product_id="{{ $product->id }}"
         data-default_price="{{ $firstStock->purchase_with_tax }}"
         data-purchase_exclusive_price="{{ $firstStock->purchase_without_tax  }}"
         data-purchase_inclusive_price="{{ $firstStock->purchase_with_tax  }}"
         data-profit_percent="{{ $firstStock->profit_percent  }}"
         data-sales_price="{{ $firstStock->sales_price  }}"
         data-wholesale_price="{{ $firstStock->wholesale_price  }}"
         data-dealer_price="{{ $firstStock->dealer_price  }}"
         data-batch_no="{{ $firstStock->batch_no ?? null  }}"
         data-product_unit_name="{{ $product->unit->unitName ?? null  }}"
         data-product_image="{{ $product->images[0] ?? '' }}"
         data-stock="{{ $product->stocks_sum_product_stock ?? 0  }}"
         data-expire_date="{{ $firstStock->expire_date ?? null  }}"
         data-tax_rate="{{ $product->tax->rate ?? 0 }}"
         data-tax_type="{{ $product->tax_type ?? 'exclusive' }}"
         data-route="{{ route('business.carts.store') }}">

        <div class="pro-img">
            <img class='w-100 rounded' src="{{ asset($product->images[0] ?? 'assets/images/products/box.svg') }}" alt="">
        </div>
        <div class="product-con">
            <h6 class="pur-title product_name">{{ Str::words($product->productName, 2, '.') }}</h6>
            <p class="batch">{{ __('Batch') }}: {{ $firstStock->batch_no ?? null  }}</p>
            <div class="price">
                <h6 class="pro-price product_price">{{ currency_format($firstStock->purchase_with_tax, 'icon', 2, business_currency()) }}</h6>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-danger not-found mt-1" role="alert">
        {{ __('No product found') }}
    </div>
@endforelse
