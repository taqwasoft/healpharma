@foreach ($stocks as $stock)
<tr>
    <td class="table-single-content">{{ ($stocks->currentPage() - 1) * $stocks->perPage() + $loop->iteration }}</td>
    <td class="text-start table-single-content">{{ $stock->productName }}</td>
    <td class="text-start table-single-content">{{ currency_format($stock->purchase_with_tax, currency : business_currency()) }}</td>
    <td class="table-single-content {{ $stock->stocks->sum('productStock') <= $stock->alert_qty ? 'text-danger' : 'text-success' }} text-start">
        {{ $stock->stocks->sum('productStock') }}
    </td>
    <td class=" table-single-content text-center">{{ currency_format($stock->sales_price, currency : business_currency()) }}</td>
    <td class=" table-single-content text-end">
        {{ currency_format($stock->stocks->sum('productStock') * $stock->purchase_with_tax, currency: business_currency()) }}
    </td>
</tr>
@endforeach


