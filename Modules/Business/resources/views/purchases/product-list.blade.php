@forelse ($products as $product)
    @php
        $firstStock = $product->all_stocks->first();
        $totalStock = $product->stocks_sum_product_stock ?? 0;
    @endphp
    <div id="single-product" class="single-product {{ $product->id }} d-flex align-items-center justify-content-between gap-3 p-2"
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
         data-stock="{{ $totalStock }}"
         data-expire_date="{{ $firstStock->expire_date ?? null  }}"
         data-tax_rate="{{ $product->tax->rate ?? 0 }}"
         data-tax_type="{{ $product->tax_type ?? 'exclusive' }}"
         data-route="{{ route('business.carts.store') }}">

        <div class="product-con" style="flex: 1; min-width: 0;">
            <h6 class="pur-title product_name mb-0" style="font-size: 0.9rem; white-space: normal; overflow: visible;">{{ $product->productName }}</h6>
            {{-- <small class="text-muted" style="font-size: 0.75rem;">{{ __('Stock') }}: {{ $totalStock }}</small> --}}
        </div>
        <div class="price" style="flex-shrink: 0;">
            <h6 class="pro-price product_price mb-0" style="font-size: 0.85rem;">{{ currency_format($firstStock->purchase_with_tax, 'icon', 2, business_currency()) }}</h6>
        </div>
    </div>
@empty
    <div class="alert alert-danger not-found mt-1" role="alert">
        {{ __('No product found') }}
    </div>
@endforelse
