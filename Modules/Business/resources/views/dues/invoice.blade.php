@extends('business::layouts.blank')

@section('title')
    {{ __('Invoice') }}
@endsection

@section('main_content')
 <div class="invoice-container-sm">
    <div class="invoice-content">
        <div class="invoice-logo">
            <img src="{{ asset(get_business_option('business-settings')['invoice_logo'] ?? 'assets/images/icons/invoice-logo.svg') }}" alt="">
        </div>
        <div>
            <h4  class="company-name">{{ $party->dueCollect->business->companyName ?? '' }}</h4>
            <div class="company-info">
                <p>{{ __('Address') }}: {{ $party->dueCollect->business->address ?? '' }}</p>
                <p>{{ __('Mobile') }}: {{ $party->dueCollect->business->phoneNumber ?? '' }}</p>
                <p>{{ __('Email') }}: {{ get_business_option('business-settings')['email'] ?? '' }}</p>
                @if (!empty($party->dueCollect->business->tax_name))
                    <p>{{ $party->dueCollect->business->tax_name }} : {{ $party->dueCollect->business->tax_no ?? '' }}</p>
                @endif
            </div>
        </div>
        <h3 class="invoice-title my-1">
            {{ __('receipt') }}
        </h3>

        <div class="invoice-info ">
            <div>
                <p>{{ __('Invoice') }} :  {{ $due_collect->invoiceNumber ?? '' }}</p>
                <p>{{ __('Name') }}: {{ $party->name ?? '' }}</p>
                <p>{{ __('Mobile') }}: {{ $party->phone ?? '' }}</p>
            </div>
            <div>
                <p class="text-end date">{{ __('Date') }} : {{ formatted_date($due_collect->paymentDate ?? '') }}</p>
                <p class="text-end time">{{ __('Time') }}: {{ formatted_time($due_collect->paymentDate ?? '') }}</p>
                <p class="text-end">{{ __('Collected By') }}: {{ $due_collect->user->role != 'staff' ? 'Admin' : $due_collect->user->name ?? '' }}</p>
            </div>
        </div>
        <table class="ph-invoice-table">
            <thead>
            <tr>
                <th class="text-center invoice-sl">{{ __('SL') }}</th>
                <th>{{ __('Total Due') }}</th>
                <th>{{ __('Pay Amt ') }}</th>
                <th class="text-end">{{ __('Due Amt ') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-center invoice-sl">1</td>
                <td>
                    {{ currency_format($due_collect->totalDue ?? 0, 'icon', 2, business_currency()) }}
                </td>
                <td class="text-center">
                    {{ currency_format($due_collect->payDueAmount ?? 0, 'icon', 2, business_currency()) }}
                </td>
                <td class="text-end">
                    {{ currency_format($due_collect->dueAmountAfterPay ?? 0, 'icon', 2, business_currency()) }}
                </td>
            </tr>
            <tr>
                <td class="total-due" colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{__('Payment Type')}}: {{ $due_collect->payment_type_id != null ? $due_collect->payment_type->name ?? '' : $due_collect->paymentType }}</h6>

                    </div>
                </td>
                <td colspan="3">
                    <div class="calculate-amount">
                           <div class="d-flex justify-content-between total-amount">
                            <p>{{ __('Payable') }}:</p>
                            <p>{{ currency_format($due_collect->totalDue ?? 0, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between paid">
                            <p>{{ __('Received') }}:</p>
                            <p>{{ currency_format($due_collect->payDueAmount ?? 0, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between">
                            <p>{{ __('Due') }}:</p>
                            <p>{{ currency_format($due_collect->dueAmountAfterPay ?? 0, 'icon', 2, business_currency()) }}</p>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="invoice-footer-sm mt-3">
            <h5>{{ get_business_option('business-settings')['invoice_note'] ?? ''}}</h5>
            <div class="scanner">
                <img src="{{ asset('uploads/qr-codes/qrcode.svg') }}" alt="">
            </div>
            <h6>{{ get_option('general')['admin_footer_text'] ?? ''}}  {{ get_option('general')['admin_footer_link_text'] ?? ''}}</h6>
        </div>
    </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/custom/onloadPrint.js') }}"></script>
@endpush
