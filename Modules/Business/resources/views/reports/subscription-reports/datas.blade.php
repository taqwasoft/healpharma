@foreach ($subscribers as $subscriber)
    <tr>
        <td class="table-single-content">{{ ($subscribers->currentPage() - 1) * $subscribers->perPage() + $loop->iteration }}</td>
        <td class="table-single-content">{{ formatted_date($subscriber->created_at) }}</td>
        <td class="table-single-content">{{ $subscriber->plan->subscriptionName ?? 'N/A' }}</td>
        <td class="table-single-content">{{ formatted_date($subscriber->created_at) }}</td>
        <td class="table-single-content">{{ $subscriber->created_at ? formatted_date($subscriber->created_at->addDays($subscriber->duration)) : '' }}</td>
        <td class="table-single-content">{{ $subscriber->gateway->name ?? 'N/A' }}</td>
        <td class="table-single-content">
            <div class="badge bg-{{ $subscriber->payment_status == 'unpaid' ? 'danger' : 'primary' }}">
                {{ ucfirst($subscriber->payment_status) }}
            </div>
        </td>
        <td class=" table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a target="_blank" href="{{ route('business.subscription-reports.invoice', $subscriber->id) }}">
                            <img src="{{ asset('assets/images/icons/Invoic.svg') }}" alt="">
                            {{ __('Invoice') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>

    </tr>
@endforeach

