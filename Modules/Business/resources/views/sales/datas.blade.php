@foreach ($sales as $sale)
    <tr>
        <td class="w-60 checkbox table-single-content">
              <label class="table-custom-checkbox">
                <input type="checkbox" name="ids[]" class="table-hidden-checkbox checkbox-item delete-checkbox-item multi-delete" value="{{ $sale->id }}">
                <span class="table-custom-checkmark custom-checkmark"></span>
            </label>
        </td>
        <td class="table-single-content">{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ formatted_date($sale->saleDate) }}</td>
        <td class="text-start table-single-content">{{ $sale->invoiceNumber }}</td>
        <td class="text-start table-single-content">{{ $sale->party->name ?? 'Cash' }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->totalAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->discountAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->paidAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ currency_format($sale->dueAmount, 'icon', 2, business_currency()) }}</td>
        <td class="text-start table-single-content">{{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}</td>
        <td class="table-single-content">
            @if($sale->details->sum('quantities') == 0)
                <div class="paid-badge">{{ __('Returned') }}</div>
            @elseif($sale->dueAmount == 0)
                <div class="paid-badge">{{ __('Paid') }}</div>
            @elseif($sale->dueAmount > 0 && $sale->dueAmount < $sale->totalAmount)
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
                        <a target="_blank" href="{{ route('business.sales.invoice', $sale->id) }}">
                            <img src="{{ asset('assets/images/icons/Invoic.svg') }}" alt="">
                            {{ __('Invoice') }}
                        </a>
                    </li>
                    @if($sale->paidAmount > 0)
                    <li>
                        <a href="javascript:void(0);" class="sale-payment-view" data-id="{{ $sale->id }}">
                            <i class="fal fa-eye"></i>
                            {{__('View Payments')}}
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('business.sale-returns.create', ['sale_id' => $sale->id]) }}">
                            <i class="fal fa-undo-alt"></i>
                            {{ __('Sales Return') }}
                        </a>
                    </li>
                    @if(!in_array($sale->id, $salesWithReturns))
                        <li>
                            <a href="{{ route('business.sales.edit', $sale->id) }}">
                                <i class="fal fa-edit"></i>
                                {{ __('Edit') }}
                            </a>
                        </li>
                    <li>
                        <a href="{{ route('business.sales.destroy', $sale->id) }}" class="confirm-action"
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
