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
            <h4  class="company-name">{{ $purchase->business->companyName ?? '' }}</h4>
            <div class="company-info">
                <p>{{ __('Address') }}: {{ $purchase->business->address ?? '' }}</p>
                <p>{{ __('Mobile') }}: {{ $purchase->business->phoneNumber ?? '' }}</p>
                <p>{{ __('Email') }}: {{ get_business_option('business-settings')['email'] ?? '' }}</p>
                @if (!empty($purchase->business->tax_name))
                    <p>{{ $purchase->business->tax_name }} : {{ $purchase->business->tax_no ?? '' }}</p>
                @endif
            </div>
        </div>
        <h3 class="invoice-title my-1">
            {{ __('purchase') }}
        </h3>

        <div class="invoice-info ">
            <div>
                <p>{{ __('Invoice') }} :  {{ $purchase->invoiceNumber ?? '' }}</p>
                <p>{{ __('Name') }}: {{ $purchase->party->name ?? '' }}</p>
                <p>{{ __('Mobile') }}: {{ $purchase->party->phone ?? '' }}</p>
            </div>
            <div>
                <p class="text-end date">{{ __('Date') }} : {{ formatted_date($purchase->purchaseDate ?? '') }}</p>
                <p class="text-end time">{{ __('Time') }}: {{ formatted_time($purchase->purchaseDate ?? '') }}</p>
                <p class="text-end">{{ __('Purchase By') }}: {{ $purchase->user->role != 'staff' ? 'Admin' : $purchase->user->name ?? '' }}</p>
            </div>
        </div>
        @if (!$purchase_returns->isEmpty())
        <table class="ph-invoice-table">
            <thead>
            <tr>
                <th class="text-center invoice-sl">{{ __('SL') }}</th>
                <th>{{ __('Product') }}</th>
                <th>{{ __('QTY') }}</th>
                <th>{{ __('U.Price') }}</th>
                <th class="text-end">{{ __('Amount') }}</th>
            </tr>
            </thead>
            @php
             $subtotal = 0;
            @endphp
            <tbody>
            @foreach ($purchase->details as $detail)
            @php
                $productTotal = ($detail->purchase_with_tax ?? 0) * ($detail->quantities ?? 0);
                $subtotal += $productTotal;
            @endphp
            <tr>
                <td class="text-center invoice-sl">{{ $loop->iteration }}</td>
                <td>
                    {{ $detail->product->formatted_name ?? $detail->product->productName ?? '' }}
                    @if($detail->pack_size || $detail->pack_qty)
                        <br><small style="color: #666;">Pack Size: {{ $detail->pack_size ?? 'N/A' }}, Pack Qty: {{ $detail->pack_qty ?? 'N/A' }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $detail->quantities ?? '' }}</td>
                <td class="text-center"> {{ currency_format($detail->purchase_with_tax ?? 0, 'icon', 2, business_currency()) }}</td>
                <td class="text-end">{{ currency_format($productTotal, 'icon', 2, business_currency()) }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="total-due" colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{ __('Payment Type') }}:
                            @if($purchase->paymentTypes && $purchase->paymentTypes->isNotEmpty())
                                {{ $purchase->paymentTypes->pluck('name')->implode(', ') }}
                            @else
                                {{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}
                            @endif
                        </h6>
                    </div>
                </td>
                <td colspan="3">
                    <div class="calculate-amount">
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Sub-Total') }}:</p>
                            <p>{{ currency_format($subtotal, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Vat') }}:</p>
                            <p> {{ currency_format($purchase->tax_amount, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between ">
                            <p>{{ __('Delivery charge') }}:</p>
                            <p>{{ currency_format($purchase->shipping_charge, currency: business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between discount-container">
                            <p>{{ __('Discount') }}
                                @if ($purchase->discount_type == 'percent')
                                ({{ $purchase->discount_percent }}%)
                               @endif
                                :</p>
                            <p> {{ currency_format($purchase->discountAmount + $total_discount, currency: business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between total-amount">
                            <p>{{ __('Net Payable') }}:</p>
                            <p>{{ currency_format($subtotal + $purchase->tax_amount - ($purchase->discountAmount + $total_discount) + $purchase->shipping_charge, 'icon', 2, business_currency()) }}</p>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <table class="ph-invoice-table mt-2">
            <thead>
            <tr>

                <th>{{ __('Date') }}</th>
                <th>{{ __('Return Product') }}</th>
                <th>{{ __('QTY') }}</th>
                <th class="text-end">{{ __('Amount') }}</th>
            </tr>
            </thead>
            @php $total_return_amount = 0; @endphp
            <tbody>
            @foreach ($purchase_returns as $key => $return)
            @foreach ($return->details as $detail)
                @php
                    $total_return_amount += $detail->return_amount ?? 0;
                @endphp
            <tr>

                <td class="text-start">{{ formatted_date($return->return_date) }}</td>
                <td>{{ $detail->purchaseDetail->product->formatted_name ?? $detail->purchaseDetail->product->productName ?? '' }}</td>
                <td class="text-center">{{ $detail->return_qty ?? 0 }}</td>
                <td class="text-end"> {{ currency_format($detail->return_amount ?? 0, 'icon', 2, business_currency()) }}</td>
            </tr>
            @endforeach
           @endforeach
            <tr>
                <td class="total-due" colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{ __('Payment Type') }}:
                            @if($purchase->paymentTypes && $purchase->paymentTypes->isNotEmpty())
                                {{ $purchase->paymentTypes->pluck('name')->implode(', ') }}
                            @else
                                {{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}
                            @endif
                        </h6>

                    </div>
                </td>
                <td colspan="3">
                    <div class="calculate-amount">
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Total Return') }}:</p>
                            <p>{{ currency_format($total_return_amount, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="in-border"></div>

                           <div class="d-flex justify-content-between total-amount">
                            <p>{{ __('Payable') }}:</p>
                            <p>{{ currency_format($purchase->totalAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between paid">
                            <p>{{ __('Paid') }}:</p>
                            <p>{{ currency_format($purchase->paidAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between">
                            <p>{{ __('Due') }}:</p>
                            <p>{{ currency_format($purchase->dueAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        @else
        <table class="ph-invoice-table">
            <thead>
            <tr>
                <th class="text-center invoice-sl">{{ __('SL') }}</th>
                <th>{{ __('Product') }}</th>
                <th>{{ __('QTY') }}</th>
                <th>{{ __('U.Price') }}</th>
                <th class="text-end">{{ __('Amount') }}</th>
            </tr>
            </thead>
            @php $subtotal = 0; @endphp
            <tbody>
            @foreach ($purchase->details as $detail)
            @php
                $productTotal = ($detail->purchase_with_tax ?? 0) * ($detail->quantities ?? 0);
                $subtotal += $productTotal;
            @endphp
            <tr>
                <td class="text-center invoice-sl">{{ $loop->iteration }}</td>
                <td>
                    {{ $detail->product->formatted_name ?? $detail->product->productName ?? '' }}
                    @if($detail->pack_size || $detail->pack_qty)
                        <br><small style="color: #666;">Pack Size: {{ $detail->pack_size ?? 'N/A' }}, Pack Qty: {{ $detail->pack_qty ?? 'N/A' }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $detail->quantities ?? '' }}</td>
                <td class="text-center"> {{ currency_format($detail->purchase_with_tax ?? 0, 'icon', 2, business_currency()) }}</td>
                <td class="text-end">{{ currency_format($productTotal, 'icon', 2, business_currency()) }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="total-due" colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{ __('Payment Type') }}:
                            @if($purchase->paymentTypes && $purchase->paymentTypes->isNotEmpty())
                                {{ $purchase->paymentTypes->pluck('name')->implode(', ') }}
                            @else
                                {{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}
                            @endif
                        </h6>
                    </div>
                </td>
                <td colspan="3">
                    <div class="calculate-amount">
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Sub-Total') }}:</p>
                            <p>{{ currency_format($subtotal, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p>{{ __("Vat") }}:</p>
                            <p> {{ currency_format($purchase->tax_amount, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between ">
                            <p>{{ __('Delivery charge') }}:</p>
                            <p>{{ currency_format($purchase->shipping_charge, currency: business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between discount-container">
                            <p>{{ __('Discount') }}
                                @if ($purchase->discount_type == 'percent')
                                ({{ $purchase->discount_percent }}%)
                                @endif
                                :</p>
                            <p> {{ currency_format($purchase->discountAmount, currency: business_currency()) }}</p>
                        </div>


                           <div class="d-flex justify-content-between total-amount">
                            <p>{{ __('Net Payable') }}:</p>
                            <p>{{ currency_format($purchase->totalAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between paid">
                            <p>{{ __('Paid') }}:</p>
                            <p>{{ currency_format($purchase->paidAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                        @if($purchase->change_amount > 0)
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Change Amount') }}</p>
                            <p> {{ currency_format($purchase->change_amount, currency: business_currency()) }}</p>
                        </div>
                        @else
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Due') }}:</p>
                            <p> {{ currency_format($purchase->dueAmount, currency: business_currency()) }}</p>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        @endif

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
