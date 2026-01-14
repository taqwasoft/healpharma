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
            <h4  class="company-name">{{ $sale->business->companyName ?? '' }}</h4>
            <div class="company-info">
                <p>{{ __('Address') }}: {{ $sale->business->address ?? '' }}</p>
                <p>{{ __('Mobile') }}: {{ $sale->business->phoneNumber ?? '' }}</p>
                <p>{{ __('Email') }}: {{ get_business_option('business-settings')['email'] ?? '' }}</p>
                @if (!empty($sale->business->tax_name))
                    <p>{{ $sale->business->tax_name }} : {{ $sale->business->tax_no ?? '' }}</p>
                @endif
            </div>
        </div>
        <h3 class="invoice-title my-1">
            {{ __('invoice') }}
        </h3>

        <div class="invoice-info ">
            <div>
                <p>{{ __('Invoice') }} :  {{ $sale->invoiceNumber ?? '' }}</p>
                <p>{{ __('Name') }}: {{ $sale->party->name ?? 'Cash' }}</p>
                <p>{{ __('Mobile') }}: {{ $sale->party->phone ?? '' }}</p>
            </div>
            <div>
                <p class="text-end date">{{ __('Date') }} : {{ formatted_date($sale->saleDate ?? '') }}</p>
                <p class="text-end time">{{ __('Time') }}: {{ formatted_time($sale->saleDate ?? '') }}</p>
                <p class="text-end">{{ __('Sales By') }}: {{ $sale->user->role != 'staff' ? 'Admin' : $sale->user->name ?? '' }}</p>
            </div>
        </div>

        @if (!$sale_returns->isEmpty())
        <table class="ph-invoice-table">
            <thead>
            <tr>
                <th class="text-center invoice-sl" style="font-weight: bold;">{{ __('SL') }}</th>
                <th style="font-weight: bold;">{{ __('Product') }}</th>
                <th style="font-weight: bold;">{{ __('QTY') }}</th>
                <th style="font-weight: bold;">{{ __('U.Price') }}</th>
                <th class="text-end" style="font-weight: bold;">{{ __('Amount') }}</th>
            </tr>
            </thead>
            @php
            $subtotal = 0;
            @endphp
            <tbody>
            @foreach ($sale->details as $detail)
            @php
                $productTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                $subtotal += $productTotal;
            @endphp
            <tr>
                <td class="text-center invoice-sl" style="font-weight: bold;">{{ $loop->iteration }}</td>
                <td style="font-weight: bold;">{{ $detail->product->formatted_name ?? $detail->product->productName ?? '' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $detail->quantities ?? '' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ currency_format($detail->price ?? 0, 'icon', 2, business_currency()) }}</td>
                <td class="text-end" style="font-weight: bold;">{{ currency_format($productTotal, 'icon', 2, business_currency()) }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="total-due" colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{ __('Payment Type') }}:
                            @if($sale->paymentTypes && $sale->paymentTypes->isNotEmpty())
                                {{ $sale->paymentTypes->pluck('name')->implode(', ') }}
                            @else
                                {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}
                            @endif
                        </h6>
                        <p class="text-center">{{ $sale->meta['notes'] ?? $sale->meta['note'] ?? '' }}</p>
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
                            <p> {{ currency_format($sale->tax_amount, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between ">
                            <p>{{ __('Delivery charge') }}:</p>
                            <p>{{ currency_format($sale->shipping_charge, currency: business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Discount') }}
                                @if ($sale->discount_type == 'percent')
                                    ({{ $sale->discount_percent }}%)
                               @endif:</p>
                            <p> {{ currency_format($sale->discountAmount + $total_discount, currency: business_currency()) }}</p>
                        </div>
                        <div class="in-border"></div>

                           <div class="d-flex justify-content-between total-amount">
                            <p>{{ __('Net Payable') }}:</p>
                            <p> {{ currency_format($subtotal + $sale->tax_amount - ($sale->discountAmount + $total_discount) + $sale->shipping_charge + $sale->rounding_amount, 'icon', 2, business_currency()) }}</p>
                          </div>
                           <div class="d-flex justify-content-between paid">
                            <p>{{ __('Total Payable') }}:</p>
                            <p> {{ currency_format($subtotal + $sale->tax_amount - ($sale->discountAmount + $total_discount) + $sale->shipping_charge, 'icon', 2, business_currency()) }}</p>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <table class="ph-invoice-table invoice-return">
            <thead>
            <tr>

                <th class="invoice-th" style="font-weight: bold;">{{ __('Date') }}</th>
                <th class="invoice-th" style="font-weight: bold;">{{ __('Return Product') }}</th>
                <th class="invoice-th" style="font-weight: bold;">{{ __('QTY') }}</th>
                <th class="invoice-th text-end" style="font-weight: bold;">{{ __('Amount') }}</th>
            </tr>
            </thead>
            @php $total_return_amount = 0; @endphp
            <tbody>
            @foreach ($sale_returns as $key => $return)
            @foreach ($return->details as $detail)
                @php
                    $total_return_amount += $detail->return_amount ?? 0;
                @endphp
            <tr>

                <td class="text-start" style="font-weight: bold;">{{ formatted_date($return->return_date) }}</td>
                <td style="font-weight: bold;">{{ $detail->saleDetail->product->formatted_name ?? $detail->saleDetail->product->productName ?? '' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $detail->return_qty ?? 0 }}</td>
                <td class="text-end" style="font-weight: bold;">{{ currency_format($detail->return_amount ?? 0, 'icon', 2, business_currency()) }}</td>
            </tr>
            @endforeach
           @endforeach
            <tr>
                <td class="total-due" colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{ __('Payment Type') }}:
                            @if($sale->paymentTypes && $sale->paymentTypes->isNotEmpty())
                                {{ $sale->paymentTypes->pluck('name')->implode(', ') }}
                            @else
                                {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}
                            @endif
                        </h6>
                        <p class="text-center">{{ $sale->meta['notes'] ?? $sale->meta['note'] ?? '' }}</p>
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
                            <p>{{ currency_format($sale->totalAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between paid">
                            <p>{{ __('Paid') }}:</p>
                            <p>{{ currency_format($sale->paidAmount, 'icon', 2, business_currency()) }}</p>
                        </div>
                           <div class="d-flex justify-content-between">
                            <p>{{ __('Due') }}:</p>
                            <p>{{ currency_format($sale->dueAmount, 'icon', 2, business_currency()) }}</p>
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
                <th class="text-center invoice-sl" style="font-weight: bold;">{{ __('SL') }}</th>
                <th style="font-weight: bold;">{{ __('Product') }}</th>
                <th style="font-weight: bold;">{{ __('QTY') }}</th>
                <th style="font-weight: bold;">{{ __('U.Price') }}</th>
                <th class="text-end" style="font-weight: bold;">{{ __('Amount') }}</th>
            </tr>
            </thead>
            @php $subtotal = 0; @endphp
            <tbody>
            @foreach ($sale->details as $detail)
            @php
                $productTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                $subtotal += $productTotal;
            @endphp
            <tr>
                <td class="text-center invoice-sl" style="font-weight: bold;">{{ $loop->iteration }}</td>
                <td style="font-weight: bold;">{{ $detail->product->formatted_name ?? $detail->product->productName ?? '' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $detail->quantities ?? '' }}</td>
                <td class="text-center" style="font-weight: bold;"> {{ currency_format($detail->price ?? 0, 'icon', 2, business_currency()) }}</td>
                <td class="text-end" style="font-weight: bold;">{{ currency_format($productTotal, 'icon', 2, business_currency()) }}</td>
            </tr>
            @endforeach
            <tr>
                <td  colspan="2">
                    <div class="payment-type-container">
                        <h6 class="text-center">{{ __('Payment Type') }}:
                            @if($sale->paymentTypes && $sale->paymentTypes->isNotEmpty())
                                {{ $sale->paymentTypes->pluck('name')->implode(', ') }}
                            @else
                                {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}
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
                            <p> {{ currency_format($sale->tax_amount, 'icon', 2, business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between ">
                            <p>{{ __('Delivery charge') }}:</p>
                            <p>{{ currency_format($sale->shipping_charge, currency: business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between discount-container">
                            <p>{{ __('Discount') }}
                                @if ($sale->discount_type == 'percent')
                                    ({{ $sale->discount_percent }}%)
                               @endif:</p>
                            <p> {{ currency_format($sale->discountAmount, currency: business_currency()) }}</p>
                        </div>


                         <div class="d-flex justify-content-between total-amount">
                            <p>{{ __('Net Payable') }}:</p>
                            <p>{{ currency_format($sale->actual_total_amount, currency: business_currency()) }}</p>
                        </div>
                        <div class="d-flex justify-content-between paid">
                            <p>{{ __('Paid') }}:</p>
                            <p>{{ currency_format($sale->paidAmount, currency: business_currency()) }}</p>
                        </div>
                        @if($sale->change_amount > 0)
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Change Amount') }}</p>
                            <p> {{ currency_format($sale->change_amount, currency: business_currency()) }}</p>
                        </div>
                        @else
                        <div class="d-flex justify-content-between">
                            <p>{{ __('Due') }}:</p>
                            <p> {{ currency_format($sale->dueAmount, currency: business_currency()) }}</p>
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
