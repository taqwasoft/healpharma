@foreach($sales as $sale)

    @php
        $total_return_amount = $sale->saleReturns->sum('total_return_amount');
    @endphp

    <tr>
        <td class="table-single-content">{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
        <td class="table-single-content">
            <a href="{{ route('business.sales.invoice', $sale->id) }}" target="_blank" class="text-primary">
                {{ $sale->invoiceNumber }}
            </a>
        </td>
        <td class="table-single-content">{{ formatted_date($sale->saleDate) }}</td>
        <td class="table-single-content">{{ $sale->party->name ?? 'Guest' }}</td>

        <td class="table-single-content">{{ currency_format($sale->totalAmount ?? 0, 'icon', 2, business_currency()) }}</td>
        <td class="table-single-content">{{ currency_format($sale->paidAmount ?? 0, 'icon', 2, business_currency()) }}</td>
        <td class="table-single-content">{{ currency_format($total_return_amount ?? 0, 'icon', 2, business_currency()) }}</td>
    </tr>
@endforeach
