@foreach($transactions as $transcation)
    <tr>
        <td class="table-single-content">{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
        <td class="text-start  table-single-content"><a href="{{ $transcation->party_id != null ? route('business.collect.dues.invoice', $transcation->party_id) : route('business.collect.walk-dues.invoice', $transcation->sale_id) }}" class="text-primary" target="_blank">{{ $transcation->invoiceNumber }}</a></td>
        <td class="text-start  table-single-content">{{ $transcation->party?->name ?? 'Cash' }}</td>
        <td class="text-start  table-single-content">{{ currency_format($transcation->totalDue, 'icon', 2, business_currency()) }}</td>
        <td class="text-start  table-single-content">{{ currency_format($transcation->payDueAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start  table-single-content">{{ $transcation->payment_type_id != null ? $transcation->payment_type->name ?? '' : $transcation->paymentType }}</td>
        <td class="text-start  table-single-content">{{ formatted_date($transcation->paymentDate) }}</td>
    </tr>
@endforeach
