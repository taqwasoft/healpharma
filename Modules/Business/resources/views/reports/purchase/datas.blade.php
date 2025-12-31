@foreach($purchases as $purchase)
    <tr>
        <td class="table-single-content">{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $purchase->invoiceNumber }}</td>
        <td class="text-start table-single-content">{{ $purchase->party?->name }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->totalAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->discountAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->paidAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->dueAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}</td>
        <td class="text-start table-single-content">{{ formatted_date($purchase->purchaseDate) }}</td>
    </tr>
@endforeach
