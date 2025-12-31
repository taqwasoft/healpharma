@foreach($medicine_types as $type)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $type->id }}">
        </td>
        <td class="table-single-content">{{ ($medicine_types->currentPage() - 1) * $medicine_types->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $type->name }}</td>
        <td class="text-center table-single-content">
            <label class="switch">
                <input type="checkbox" {{ $type->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.medicine-types.status', $type->id) }}">
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
                        <a href="#medicine-types-edit-modal" class="medicine-types-edit-btn" data-bs-toggle="modal"
                           data-url="{{ route('business.medicine-types.update', $type->id) }}"
                           data-medicine-types-name="{{ $type->name }}"
                           >
                            <i class="fal fa-edit"></i>{{__('Edit')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.medicine-types.destroy', $type->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
