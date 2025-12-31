@foreach($expenses as $expense)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $expense->id }}">
        </td>
        <td class="table-single-content">{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ currency_format($expense->amount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ $expense->category?->categoryName }}</td>
        <td class="text-start table-single-content">{{ $expense->expanseFor }}</td>
        <td class="text-start table-single-content">{{ $expense->payment_type_id != null ? $expense->payment_type->name ?? '' : $expense->paymentType }}</td>
        <td class="text-start table-single-content">{{ $expense->referenceNo }}</td>
        <td class="text-start table-single-content">{{ formatted_date($expense->expenseDate) }}</td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#expenses-edit-modal" data-bs-toggle="modal" class="expense-edit-btn"
                           data-url="{{ route('business.expenses.update', $expense->id) }}"
                           data-expense-category-id="{{ $expense->expense_category_id }}"
                           data-expense-amount="{{ $expense->amount }}"
                           data-expense-for="{{ $expense->expanseFor }}"
                           data-expense-payment-type="{{ $expense->paymentType }}"
                           data-expense-payment-type-id="{{ $expense->payment_type_id }}"
                           data-expense-reference-no="{{ $expense->referenceNo }}"
                           data-expense-date="{{ \Carbon\Carbon::parse($expense->expenseDate)->format('Y-m-d') }}"
                           data-expense-note="{{ $expense->note }}">
                           <i class="fal fa-edit"></i>{{ __('Edit') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.expenses.destroy', $expense->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
