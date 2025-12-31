<table>
    <thead>
        <tr>
            <th>{{ __('SL') }}. </th>
            <th>{{ __('Product Name') }} </th>
            <th>{{ __('Code') }} </th>
            <th>{{ __('Category') }} </th>
            <th>{{ __('Unit') }} </th>
            <th>{{ __('Purchase price') }}</th>
            <th>{{ __('Sale price') }}</th>
            <th>{{ __('Stock') }}</th>
            <th>{{ __('Expired Date') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expired_products as $stock)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $stock->product?->productName }}</td>
                <td>{{ $stock->product?->productCode }}</td>
                <td>{{ $stock->product?->category->categoryName ?? '' }}</td>
                <td>{{ $stock->product?->unit->unitName ?? '' }}</td>
                <td>{{ currency_format($stock->purchase_with_tax, 'icon', 2, business_currency()) }}</td>
                <td>{{ currency_format($stock->sales_price, 'icon', 2, business_currency()) }}</td>
                <td>{{ $stock->productStock }}</td>
                <td>{{ formatted_date($stock->expire_date ?? '') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
