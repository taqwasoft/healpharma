@foreach($box_sizes as $size)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $size->id }}">
        </td>
        <td class="table-single-content">{{ ($box_sizes->currentPage() - 1) * $box_sizes->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $size->name }}</td>
        <td class="text-center table-single-content">
            <label class="switch">
                <input type="checkbox" {{ $size->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.box-sizes.status', $size->id) }}">
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
                        <a href="#box-size-edit-modal" class="box-size-edit-btn" data-bs-toggle="modal"
                           data-url="{{ route('business.box-sizes.update', $size->id) }}"
                           data-name="{{ $size->name }}"
                           >
                            <i class="fal fa-edit"></i>{{__('Edit')}}
                        </a>

                    </li>
                    <li>
                        <a href="{{ route('business.box-sizes.destroy', $size->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
