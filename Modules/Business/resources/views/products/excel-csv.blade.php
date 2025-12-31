<table>
    <thead>
        <tr>
            <th> {{ __('SL') }}. </th>
            <th> {{ __('Product Name') }} </th>
            <th> {{ __('Code') }} </th>
            <th> {{ __('Category') }} </th>
            <th> {{ __('Unit') }} </th>
            <th> {{ __('Purchase price') }}</th>
            <th> {{ __('Sale price') }}</th>
            <th> {{ __('Stock') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($products as $product)
            @php
                $nonEmptyStock = $product->stocks->firstWhere('productStock', '>', 0);
                $fallbackStock = $product->stocks->first(); // fallback if no stock > 0
                $stock = $nonEmptyStock ?? $fallbackStock;

                $latestPurchasePrice = $stock?->purchase_with_tax ?? 0;
                $latestSalePrice = $stock?->sales_price ?? 0;
            @endphp
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $product->productName }}</td>
                <td>{{ $product->productCode }}</td>
                <td>{{ $product->category->categoryName ?? '' }}</td>
                <td>{{ $product->unit->unitName ?? '' }}</td>
                <td>{{ currency_format($latestPurchasePrice, 'icon', 2, business_currency()) }}</td>
                <td>{{ currency_format($latestSalePrice, 'icon', 2, business_currency()) }}</td>
                <td>{{ $product->stocks->sum('productStock') }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
