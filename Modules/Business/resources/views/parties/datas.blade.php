@foreach ($parties as $party)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $party->id }}">
        </td>
        <td class="table-single-content">{{ ($parties->currentPage() - 1) * $parties->perPage() + $loop->iteration }}</td>
        <td class="table-single-content">
            <img src="{{ asset($party->image ?? 'assets/images/logo/default-img.jpg') }}" alt="Img" class="table-img">
        </td>
        <td class="table-single-content">{{ $party->name }}</td>
        <td class="table-single-content">{{ $party->email }}</td>
        <td class="table-single-content">{{ $party->type }}</td>
        <td class="table-single-content">{{ $party->phone }}</td>
        <td class="text-danger table-single-content">{{ currency_format($party->due, 'icon', 2, business_currency()) }}</td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#parties-view" class="parties-view-btn" data-bs-toggle="modal"
                            data-name="{{ $party->name }}" data-email="{{ $party->email }}"
                            data-phone="{{ $party->phone }}" data-type="{{ $party->type }}"
                            data-due="{{ currency_format($party->due, 'icon', 2, business_currency()) }}" data-address="{{ $party->address }}">
                            <i class="fal fa-eye"></i>
                            {{ __('View') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.parties.edit', [$party->id, 'type' => request('type')]) }}"><i class="fal fa-edit"></i>{{ __('Edit') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('business.parties.destroy', $party->id) }}" class="confirm-action"
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
