@foreach($walk_in_customers as $walk_in_customer)
    <tr>
        <td class="table-single-content">{{ ($walk_in_customers->currentPage() - 1) * $walk_in_customers->perPage() + $loop->iteration }}</td>
        <td class="table-single-content">{{ $walk_in_customer->invoiceNumber }}</td>
        <td class="text-danger table-single-content">{{ currency_format($walk_in_customer->dueAmount, 'icon', 2, business_currency()) }}</td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('business.collect.dues', ['id' => $walk_in_customer->id]) }}?source=walk-in">
                            <i class="fal fa-edit"></i>
                            {{ __('Collect Due') }}
                        </a>
                    </li>
                    @if($walk_in_customer->dueCollect)
                        <li>
                            <a href="{{ route('business.collect.walk-dues.invoice', $walk_in_customer->id) }}" target="_blank">
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
