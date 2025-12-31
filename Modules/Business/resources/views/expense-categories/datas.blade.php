@foreach($expense_categories as $expense_category)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $expense_category->id }}">
        </td>
        <td class="table-single-content">{{ ($expense_categories->currentPage() - 1) * $expense_categories->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $expense_category->categoryName }}</td>
        <td class="text-start table-single-content">{{ $expense_category->categoryDescription }}</td>
        <td class="table-single-content">
            <label class="switch">
                <input type="checkbox" {{ $expense_category->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.expense-categories.status', $expense_category->id) }}">
                <span class="slider round"></span>
            </label>
        </td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#expense-categories-edit-modal" data-bs-toggle="modal" class="expense-categories-edit-btn"
                        data-url="{{ route('business.expense-categories.update', $expense_category->id) }}"
                        data-expense-categories-name="{{ $expense_category->categoryName }}" data-expense-categories-description="{{ $expense_category->categoryDescription }}"><i class="fal fa-edit"></i>{{__('Edit')}}</a>
                    </li>
                    <li>
                        <a href="{{ route('business.expense-categories.destroy', $expense_category->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
