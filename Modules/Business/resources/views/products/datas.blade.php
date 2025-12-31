@foreach ($products as $product)
    @php
        $nonEmptyStock = $product->stocks->firstWhere('productStock', '>', 0);
        $fallbackStock = $product->stocks->first(); // fallback if no stock > 0
        $stock = $nonEmptyStock ?? $fallbackStock;

        $latestPurchasePrice = $stock?->purchase_with_tax ?? 0;
        $latestSalePrice = $stock?->sales_price ?? 0;
        $latestWholeSalePrice = $stock?->wholesale_price ?? 0;
        $latestDealerPrice = $stock?->dealer_price ?? 0;
    @endphp
    <tr>
        <td class="w-60 checkbox d-print-none table-single-content">
            <label class="table-custom-checkbox">
                <input type="checkbox" name="ids[]" class="table-hidden-checkbox checkbox-item delete-checkbox-item multi-delete"
                    value="{{ $product->id }}">
                <span class="table-custom-checkmark custom-checkmark"></span>
            </label>

        </td>
        <td class="table-single-content">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>

        <td class="table-single-content">
            <img src="{{ asset($product->images[0] ?? 'assets/images/logo/default-img.jpg') }}" alt="Img" class="table-product-img table-img">
        </td>
        <td class="table-single-content d-print-none">
            @php
                $stocks = $product->stocks->map(function ($batch) use ($product) {
                    return [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expire_date' => $batch->expire_date ? formatted_date($batch->expire_date) : 'N/A',
                        'productStock' => $batch->productStock ?? 0,
                        'sales_price' => $batch->sales_price ?? 0,
                        'purchase_without_tax' => $batch->purchase_without_tax ?? 0,
                        'purchase_with_tax' => $batch->purchase_with_tax ?? 0,
                        'wholesale_price' => $batch->wholesale_price ?? 0,
                        'dealer_price' => $batch->dealer_price ?? 0,
                    ];
                });
            @endphp
            <a href="javascript:void(0);" class="stock-view-data text-primary" data-stocks='@json($stocks)'>
                {{ $product->productName }}
            </a>
        </td>
        <td class="table-single-content">{{ $product->productCode }}</td>
        <td class="table-single-content">{{ $product->category->categoryName ?? '' }}</td>
        <td class="table-single-content">{{ $product->unit->unitName ?? '' }}</td>
        <td class="table-single-content">{{ currency_format($latestPurchasePrice, 'icon', 2, business_currency()) }}</td>
        <td class="table-single-content">{{ currency_format($latestSalePrice, 'icon', 2, business_currency()) }}</td>
        <td class="table-single-content">{{ currency_format($latestDealerPrice, 'icon', 2, business_currency()) }}</td>
        <td class="{{ $product->stocks->sum('productStock') <= $product->alert_qty ? 'text-danger' : 'text-success' }} table-single-content">
            {{ $product->stocks->sum('productStock') }}
        </td>

        <td class="d-print-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#product-view" class="product-view" data-bs-toggle="modal"
                            data-name="{{ $product->productName }}"
                            data-image="{{ asset($product->images[0] ?? 'assets/images/logo/default-img.jpg') }}"
                            data-code="{{ $product->productCode }}"
                            data-category="{{ $product->category->categoryName ?? '' }}"
                            data-unit="{{ $product->unit->unitName ?? '' }}"
                            data-purchase-price="{{ currency_format($latestPurchasePrice, 'icon', 2, business_currency()) }}"
                            data-sale-price="{{ currency_format($latestSalePrice, 'icon', 2, business_currency()) }}"
                            data-wholesale-price="{{ currency_format($latestWholeSalePrice, 'icon', 2, business_currency()) }}"
                            data-dealer-price="{{ currency_format($latestDealerPrice, 'icon', 2, business_currency()) }}"
                            data-stock="{{ $product->stocks->sum('productStock') }}"
                            data-low-stock="{{ $product->alert_qty }}"
                            data-expire-date="{{ formatted_date(optional($product->stocks->first())->expire_date) }}"
                            data-manufacturer="{{ $product->manufacterer?->name }}">
                            <i class="fal fa-eye"></i>
                            {{ __('View') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.products.show', $product->id) }}">
                            <i class="fal fa-plus-circle"></i>
                            {{ __('Add Stock') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.products.edit', $product->id) }}">
                            <i class="fal fa-edit"></i>
                            {{ __('Edit') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.products.destroy', $product->id) }}" class="confirm-action"
                            data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
