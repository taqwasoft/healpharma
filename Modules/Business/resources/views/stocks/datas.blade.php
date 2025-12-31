@php
    $total_value = 0;
@endphp

@foreach ($stocks as $stock)
    @php
        $firstStock = $stock->stocks->first();
        $stock_value = $stock->stocks->sum('productStock') * ($firstStock?->purchase_with_tax ?? 0);
        $total_value += $stock_value;
    @endphp
    <tr>
        <td class="table-single-content">{{ ($stocks->currentPage() - 1) * $stocks->perPage() + $loop->iteration }}</td>
        <td class="table-single-content d-print-none">
            @php
                $all_stocks = $stock->stocks->map(function ($batch){
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
            <a href="javascript:void(0);" class="stock-view-data text-primary" data-stocks='@json($all_stocks)'>
                {{ $stock->productName }}
            </a>
        </td>
        <td class="text-start table-single-content">{{ currency_format($stock->stocks->first()->purchase_with_tax, currency : business_currency()) }}</td>
        <td class="table-single-content {{ $stock->stocks->sum('productStock') <= $stock->alert_qty ? 'text-danger' : 'text-success' }} text-start">
            {{ $stock->stocks->sum('productStock') }}
        </td>
        <td class="text-center table-single-content">{{ currency_format($stock->stocks->first()->sales_price, currency : business_currency()) }}</td>
        <td class="text-end table-single-content">
            {{ currency_format($stock_value, currency: business_currency()) }}
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="5" class="text-end"><strong>{{ __('Total Stock Value') }} :</strong></td>
    <td class="text-end"><strong>{{ currency_format($total_value, currency : business_currency()) }}</strong></td>
</tr>
