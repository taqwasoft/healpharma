@foreach($dues as $due)
    <tr>
        <td class="table-single-content">{{ ($dues->currentPage() - 1) * $dues->perPage() + $loop->iteration }}</td>
        <td class="table-single-content">{{ $due->name }}</td>
        <td class="table-single-content">{{ $due->email }}</td>
        <td class="table-single-content">{{ $due->phone }}</td>
        <td class="table-single-content">{{ $due->type }}</td>
        <td class="text-danger table-single-content">{{ currency_format($due->due, 'icon', 2, business_currency()) }}</td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('business.collect.dues', $due->id) }}">
                            <i class="fal fa-edit"></i>
                            {{ $due->type === 'Supplier' ? __('Pay Due') : __('Collect Due') }}
                        </a>
                    </li>
                    @if($due->dueCollect)
                        <li>
                            <a href="{{ route('business.collect.dues.invoice', $due->id) }}" target="_blank">
                                <img src="{{ asset('assets/images/icons/Invoic.svg') }}" alt="" >
                                {{ __('Invoice') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
