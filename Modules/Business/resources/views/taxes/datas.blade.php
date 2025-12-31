@foreach ($taxes as $tax)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $tax->id }}">
        </td>
        <td class="table-single-content">{{ $loop->iteration }}</td>
        <td class="table-single-content">{{ $tax->name }}</td>
        <td class="text-dark fw-bold table-single-content">{{ $tax->rate }}%</td>
        <td class="text-center w-150 table-single-content">
            <label class="switch">
                <input type="checkbox" {{ $tax->status == 1 ? 'checked' : '' }} class="status"
                    data-url="{{ route('business.taxes.status', $tax->id) }}">
                <span class="slider round"></span>
            </label>
        </td>
        <td class="table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown"><i class="far fa-ellipsis-v"></i></button>
                <ul class="dropdown-menu">


                        <li>
                            <a href="#tax-edit-modal" class="tax-edit-btn" data-bs-toggle="modal"
                               data-url="{{ route('business.taxes.update', $tax->id) }}"
                               data-tax-name="{{ $tax->name }}" data-tax-code="{{ $tax->code }}"
                               data-tax-rate="{{ $tax->tax_rate }}" data-new-tax-rate="{{ $tax->rate }}"
                               data-tax-status="{{ $tax->status == 1 ? '1' : '0' }}"
                            >
                                <i class="fal fa-edit"></i>
                                {{ __('Edit') }}
                            </a>
                        </li>


                        <li>
                            <a href="{{ route('business.taxes.destroy', $tax->id) }}" class="confirm-action"
                                data-method="DELETE">
                                    <i class="fal fa-trash-alt"></i>
                                    {{ __('Delete') }}
                            </a>
                        </li>

                </ul>
            </div>
        </td>
    </tr>
@endforeach

