@foreach($sales as $sale)
    <tr>
        <td class="table-single-content">{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $sale->invoiceNumber }}</td>
        <td class="text-start table-single-content">{{ $sale->party?->name ?? 'Guest' }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->totalAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->discountAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->paidAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->dueAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}</td>
        <td class="text-start table-single-content">{{ formatted_date($sale->saleDate) }}</td>
    </tr>
@endforeach
