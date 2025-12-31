@foreach($expense_reports as $expense_report)
    <tr>
        <td class="table-single-content">{{ ($expense_reports->currentPage() - 1) * $expense_reports->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ currency_format($expense_report->amount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ $expense_report->category->categoryName }}</td>
        <td class="text-start table-single-content">{{ $expense_report->expanseFor }}</td>
        <td class="text-start table-single-content">{{ $expense_report->payment_type_id != null ? $expense_report->payment_type->name ?? '' : $expense_report->paymentType }}</td>
        <td class="text-start table-single-content">{{ $expense_report->referenceNo }}</td>
        <td class="text-start table-single-content">{{ formatted_date($expense_report->expenseDate) }}</td>
    </tr>
@endforeach
