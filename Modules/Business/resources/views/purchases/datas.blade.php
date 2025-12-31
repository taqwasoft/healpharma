@foreach($purchases as $purchase)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <label class="table-custom-checkbox">
                <input type="checkbox" name="ids[]" class="table-hidden-checkbox checkbox-item delete-checkbox-item multi-delete"
                     value="{{ $purchase->id }}">
                <span class="table-custom-checkmark custom-checkmark"></span>
            </label>
        </td>
        <td class="table-single-content">{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ formatted_date($purchase->purchaseDate) }}</td>
        <td class="text-start table-single-content">{{ $purchase->invoiceNumber }}</td>
        <td class="text-start table-single-content">{{ $purchase->party->name }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->totalAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->discountAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->paidAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($purchase->dueAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}</td>
        <td class="table-single-content">
            @if($purchase->details->sum('quantities') == 0)
                <div class="paid-badge">{{ __('Returned') }}</div>
            @elseif($purchase->dueAmount == 0)
                <div class="paid-badge">{{ __('Paid') }}</div>
            @elseif($purchase->dueAmount > 0 && $purchase->dueAmount < $purchase->totalAmount)
                <div class="unpaid-badge">{{ __('Partial Paid') }}</div>
            @else
                <div class="unpaid-badge-2">{{ __('Unpaid') }}</div>
            @endif
        </td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a target="_blank" href="{{ route('business.purchases.invoice', $purchase->id) }}">
                            <img src="{{ asset('assets/images/icons/Invoic.svg') }}" alt="" >
                            {{ __('Invoice') }}
                        </a>
                    </li>
                    @if($purchase->paidAmount > 0)
                    <li>
                        <a href="javascript:void(0);" class="purchase-payment-view" data-id="{{ $purchase->id }}">
                            <i class="fal fa-eye"></i>
                            {{__('View Payments')}}
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('business.purchase-returns.create', ['purchase_id' => $purchase->id]) }}">
                            <i class="fal fa-undo-alt"></i>
                            {{ __('Purchase Return') }}
                        </a>
                    </li>
                    @if(!in_array($purchase->id, $purchasesWithReturns))
                        <li>
                            <a href="{{ route('business.purchases.edit', $purchase->id) }}">
                                <i class="fal fa-edit"></i>
                                {{ __('Edit') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('business.purchases.destroy', $purchase->id) }}" class="confirm-action"
                               data-method="DELETE">
                                <i class="fal fa-trash-alt"></i>
                                {{ __('Delete') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
