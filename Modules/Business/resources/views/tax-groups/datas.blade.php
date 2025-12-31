@foreach ($tax_groups as $tax)
    <tr>
        <td class="table-single-content">{{ $loop->iteration }}</td>
        <td class="table-single-content">{{ $tax->name }}</td>
        <td class="text-dark fw-bold table-single-content">{{ $tax->rate }}%</td>
        <td class="table-single-content">
            @if(!empty($tax->sub_tax))
                {{ collect($tax->sub_tax)->pluck('name')->implode(', ') }}
            @else
                N/A
            @endif
        </td>
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
                        <a href="{{ route('business.taxes.edit', $tax->id) }}">
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
