@foreach ($expired_products as $stock)
    <tr>
        <td class=" table-single-content">{{ ($expired_products->currentPage() - 1) * $expired_products->perPage() + $loop->iteration }}</td>

        <td class=" table-single-content">
            <img src="{{ asset($stock->product?->images[0] ?? 'assets/images/logo/default-img.jpg') }}" alt="Img" class="table-img">
        </td>

        <td class=" table-single-content">{{ $stock->product?->productName }}</td>
        <td class=" table-single-content">{{ $stock->product?->productCode }}</td>
        <td class=" table-single-content">{{ $stock->product?->category->categoryName ?? '' }}</td>
        <td class=" table-single-content">{{ $stock->product?->unit->unitName ?? '' }}</td>
        <td class=" table-single-content">{{ currency_format($stock->purchase_with_tax, 'icon', 2, business_currency()) }}</td>
        <td class=" table-single-content">{{ currency_format($stock->sales_price, 'icon', 2, business_currency()) }}</td>
        <td class=" table-single-content">{{ currency_format($stock->dealer_price, 'icon', 2, business_currency()) }}</td>
        <td class=" table-single-content {{ $stock->productStock <= $stock->product?->alert_qty ? 'text-danger' : 'text-success' }}">{{ $stock->productStock }}</td>
        <td class=" table-single-content {{ $stock->expire_date < now()->toDateString() ? 'text-danger' : '' }}">
            {{ formatted_date($stock->expire_date) }}
        </td>

        <td class="print-d-none  table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#expire-product-report-view" class="product-view" data-bs-toggle="modal"
                            data-name="{{ $stock->product?->productName }}"
                            data-image="{{ asset($stock->product?->images[0] ?? 'assets/images/logo/default-img.jpg') }}"
                            data-code="{{ $stock->product?->productCode }}"
                            data-category="{{ $stock->product?->category?->categoryName ?? ''  }}"
                            data-unit="{{ $stock->product?->unit?->unitName ?? '' }}"
                            data-purchase-price="{{ currency_format($stock->purchase_with_tax, 'icon', 2, business_currency()) }}"
                            data-sale-price="{{ currency_format($stock->sales_price, 'icon', 2, business_currency()) }}"
                            data-wholesale-price="{{ currency_format($stock->dealer_price, 'icon', 2, business_currency()) }}"
                            data-dealer-price="{{ currency_format($stock->wholesale_price, 'icon', 2, business_currency()) }}"
                            data-stock="{{ $stock->productStock }}"
                            data-low-stock="{{ $stock->product?->alert_qty }}"
                            data-expire-date="{{ formatted_date($stock->expire_date) }}"
                            data-manufacturer="{{ $stock->product?->manufacterer?->name }}">
                            <i class="fal fa-eye"></i>
                            {{ __('View') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
