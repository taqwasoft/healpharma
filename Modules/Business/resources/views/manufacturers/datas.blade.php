@foreach($manufacturers as $manufacturer)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $manufacturer->id }}">
        </td>
        <td class="table-single-content">{{ ($manufacturers->currentPage() - 1) * $manufacturers->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $manufacturer->name }}</td>
        <td class="table-single-content">{{ Str::limit($manufacturer->description, 15, '...') }}</td>
        <td class="text-center table-single-content">
            <label class="switch">
                <input type="checkbox" {{ $manufacturer->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.manufacturers.status', $manufacturer->id) }}">
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
                        <a href="#manufacturer-edit-modal" class="manufacturer-edit-btn" data-bs-toggle="modal"
                           data-url="{{ route('business.manufacturers.update', $manufacturer->id) }}"
                           data-name="{{ $manufacturer->name }}"
                           data-description="{{ $manufacturer->description }}"
                           >
                            <i class="fal fa-edit"></i>{{__('Edit')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.manufacturers.destroy', $manufacturer->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
