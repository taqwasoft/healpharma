<table>
    <thead>
        <tr>
            <th>{{ __('SL') }}.</th>
            <th>{{ __('Product') }}</th>
            <th>{{ __('Cost') }}</th>
            <th>{{ __('Qty') }}</th>
            <th>{{ __('Sale') }}</th>
            <th>{{ __('Stock Value') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stocks as $stock)
        <tr>
            <td>{{ $loop->index+1 }}</td>
            <td>{{ $stock->productName }}</td>
            <td>{{ currency_format($stock->stocks->first()->purchase_with_tax, 'icon', 2, business_currency()) }}</td>
            <td>{{ $stock->stocks->sum('productStock') }}</td>
            <td>{{ currency_format($stock->stocks->first()->sales_price, 'icon', 2, business_currency()) }}</td>
            <td> {{ currency_format($stock->stocks->sum('productStock') * $stock->stocks->first()->purchase_with_tax, currency: business_currency()) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
