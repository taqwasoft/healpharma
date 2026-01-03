@extends('business::layouts.master')

@section('title')
    {{ __('Pos Sale') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
@endpush

@section('main_content')
    <div class="custome-container-fluid custom-min-height">
        <div class="grid sales-main-container w-auto p-lr">
            <div class="main-container w-auto">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="container-fluid p-0 m-0">
                        <div class="row g-2 w-100 align-items-center ">
                            <div class="w-100">
                                <!-- Search Input and Add Button -->
                                <form action="{{ route('business.sales.product-filter') }}" method="post"
                                      class="product-filter product-filter-form w-100" table="#products-list">
                                    @csrf
                                    <div class="search-product">
                                        <div class="category-brand-select align-items-center justify-content-end gap-2 ">
                                            <div>
                                                <select name="category_id" id="category_id" class="form-control sale-purchase-input sale-purchase-select">
                                                    <option value="">{{ __('Select a category') }}</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <select name="manufacturer_id" id="manufacturer_id" class="form-control sale-purchase-input sale-purchase-select col-lg-6">
                                                    <option value="">{{ __('Select brand') }}</option>
                                                    @foreach($manufacturers as $manufacturer)
                                                        <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" name="search" id="sale_product_search" class="form-control search-input sale-purchase-input " placeholder="{{ __('Scan / search product by code or name') }}">
                                            @if (auth()->user()->role != 'staff' || visible_permission('productPermission'))
                                            <a href="{{ route('business.products.create') }}" class="btn btn-search">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.8333 2.5H4.16667C3.24619 2.5 2.5 3.24619 2.5 4.16667V15.8333C2.5 16.7538 3.24619 17.5 4.16667 17.5H15.8333C16.7538 17.5 17.5 16.7538 17.5 15.8333V4.16667C17.5 3.24619 16.7538 2.5 15.8333 2.5Z" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>--}}
                                                    <path d="M10 6.66699V13.3337" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M6.66602 10H13.3327" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="products-container">
                    <div class="scroll-card">
                        <div class="products product-list-container" id="products-list">
                            @include('business::sales.product-list')
                        </div>
                    </div>
                </div>
            </div>
            <div class="sales-container w-auto">
                <!-- Quick Action Section -->
                <div class="quick-act-header">
                    <div class="d-flex flex-sm-row justify-content-between align-items-center">
                        <div class="mb-2 mb-sm-0">
                            <h4 class='quick-act-title'>{{ __('Quick Action') }}</h4>
                        </div>
                        <div class="quick-actions-container">
                            <a class="sale-stock-btn stock-get" href="#product-list-modal" data-bs-toggle="modal">
                                {{ __('Stock List') }}
                            </a>
                            <a href="{{ route('business.sales.index', ['today' => true]) }}" class="today-sale-btn">
                                {{ __('Today Sales') }}
                            </a>
                                       <button data-bs-toggle="modal" data-bs-target="#calculatorModal" class="sale-calculator-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 2V6M8 2V6" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13 4H11C7.22876 4 5.34315 4 4.17157 5.17157C3 6.34315 3 8.22876 3 12V14C3 17.7712 3 19.6569 4.17157 20.8284C5.34315 22 7.22876 22 11 22H13C16.7712 22 18.6569 22 19.8284 20.8284C21 19.6569 21 17.7712 21 14V12C21 8.22876 21 6.34315 19.8284 5.17157C18.6569 4 16.7712 4 13 4Z" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 10H21" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.9955 14H12.0045M11.9955 18H12.0045M15.991 14H16M8 14H8.00897M8 18H8.00897" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="calculator-tooltip-container">
                                <div class="calculator-tooltip-text"> {{__('Calculator')}} </div>
                                <button data-bs-toggle="modal" data-bs-target="#calculatorModal" class="sale-calculator-btn">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 5C4 4.46957 4.21071 3.96086 4.58579 3.58579C4.96086 3.21071 5.46957 3 6 3H18C18.5304 3 19.0391 3.21071 19.4142 3.58579C19.7893 3.96086 20 4.46957 20 5V19C20 19.5304 19.7893 20.0391 19.4142 20.4142C19.0391 20.7893 18.5304 21 18 21H6C5.46957 21 4.96086 20.7893 4.58579 20.4142C4.21071 20.0391 4 19.5304 4 19V5Z" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 14V14.01M12 14V14.01M16 14V14.01M8 17V17.01M12 17V17.01M16 17V17.01M8 8C8 7.73478 8.10536 7.48043 8.29289 7.29289C8.48043 7.10536 8.73478 7 9 7H15C15.2652 7 15.5196 7.10536 15.7071 7.29289C15.8946 7.48043 16 7.73478 16 8V9C16 9.26522 15.8946 9.51957 15.7071 9.70711C15.5196 9.89464 15.2652 10 15 10H9C8.73478 10 8.48043 9.89464 8.29289 9.70711C8.10536 9.51957 8 9.26522 8 9V8Z" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                            <a class="sale-power-btn" href="{{ route('business.dashboard.index') }}">
                                <img src="{{ asset('assets/images/icons/power.svg') }}" alt="">
                            </a>
                            <a href="" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideModal" aria-controls="sideModal" class="burger-menu">
                                <img src="{{ asset('assets/images/icons/burger-menu.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('business.sales.update', $sale->id) }}" method="post" enctype="multipart/form-data" class="ajaxform_redirect_invoice">
                    @csrf
                    @method('PUT')
                    <div class="mt-2 mb-2">
                        <div class="row g-3">
                            <div class="col-12 ">
                                <div class="d-flex align-items-center">
                                    <select required name="party_id" id="party_id" class="form-select sale-purchase-input customer-select choices-select" aria-label="Select Customer">
                                        <option value="">{{ __('Select Customer') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-type="{{ $customer->type }}" data-phone="{{ $customer->phone }}" @selected($sale->party_id == $customer->id)>
                                                {{ $customer->name }}({{ $customer->type }}{{ $customer->due ? ' ' . currency_format($customer->due, currency: business_currency()) : '' }})
                                                {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a type="button" href="#customer-create-modal" data-bs-toggle="modal" class="btn  square-btn d-flex justify-content-center align-items-center"><img src="{{ asset('assets/images/icons/plus-square.svg') }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cart-payment">
                        <div class="table-responsive-custom">
                            <table class="table table-bordered  align-middle text-center">
                                <thead class="table-light">
                                <tr>
                                    <th class="sales-purchase-th">{{ __('Image') }}</th>
                                    <th class="sales-purchase-th text-start">{{ __('Items') }}</th>
                                    <th class="sales-purchase-th">{{ __('Batch') }}</th>
                                    <th class="sales-purchase-th">{{ __('Unit Price') }}</th>
                                    <th class="sales-purchase-th">{{ __('Qty') }}</th>
                                    <th class="sales-purchase-th">{{ __('Sub Total') }}</th>
                                    <th class="sales-purchase-th">{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody id="cart-list">
                                @include('business::sales.cart-list')
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Make Payment Section start -->
                    <div class="make-payment-with-btn">
                        <div class="grid g-3 payment-section">
                            <div>
                                <div class="amount-info-container">
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Receive Amount') }}</h6>
                                        <input name="receive_amount" type="number" step="any" id="receive_amount"  value="{{ $sale->change_amount + $sale->paidAmount }}" min="0" class="form-control amount-container" placeholder="0">
                                    </div>
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Change Amount') }}</h6>
                                        <input type="number" step="any" id="change_amount" value="{{ $sale->change_amount }}" class="form-control amount-container" placeholder="0" readonly>
                                    </div>
                                    <div class="row amount-container  align-items-center mb-2">
                                        <h6 class="payment-title">{{ __('Due Amount') }}</h6>
                                        <input type="number" step="any" id="due_amount" class="form-control amount-container" placeholder="0" readonly>
                                    </div>

                                    <div class="row amount-container align-items-start mb-2">
                                        <h6 class="payment-title">{{ __('Payment Type') }}</h6>
                                        <div class="p-0">
                                            {{-- Single select (only shown if no multiple pivot payments) --}}
                                            @if($sale->paymentTypes->isEmpty())
                                                <select name="payment_type_id" class="form-select" id="payment_type">
                                                    @foreach ($payment_types as $type)
                                                        <option value="{{ $type->id }}"
                                                            @selected(
                                                                $sale->payment_type_id == $type->id ||
                                                                ($sale->payment_type_id === null && $sale->paymentType == $type->name)
                                                            )>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select name="payment_type_id" class="form-select d-none" id="payment_type">
                                                    @foreach ($payment_types as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            {{-- Dynamic payment grids --}}
                                            <div class="payment-main-container">
                                                @if($sale->paymentTypes->isNotEmpty())
                                                    @foreach($sale->paymentTypes as $pt)
                                                        <div class="payment-grid">
                                                            <select name="payment_types[][payment_type_id]" class="form-select">
                                                                @foreach ($payment_types as $type)
                                                                    <option value="{{ $type->id }}" @selected($pt->id == $type->id)>{{ $type->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <input name="payment_types[][amount]" class="amount form-control"
                                                                   type="number" step="any" min="0"
                                                                   value="{{ $pt->pivot->amount ?? 0 }}">
                                                            {{-- Only show delete button if not first grid --}}
                                                            @if(!$loop->first)
                                                                <button type="button" class="delete-btn">
                                                                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M15.4519 4.12476L14.9633 11.6436C14.8384 13.5646 14.7761 14.5251 14.2699 15.2157C14.0195 15.5571 13.6974 15.8452 13.3236 16.0618C12.5678 16.4998 11.5561 16.4998 9.53271 16.4998C7.50669 16.4998 6.49366 16.4998 5.73733 16.0609C5.3634 15.844 5.04108 15.5554 4.79088 15.2134C4.28485 14.5217 4.2238 13.5598 4.10171 11.6362L3.625 4.12476" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
                                                                        <path d="M7.1731 8.80115H11.9039" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
                                                                        <path d="M8.35571 11.7405H10.7211" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
                                                                        <path d="M2.44238 4.12524H16.6347M12.7361 4.12524L12.1979 3.06904C11.8404 2.36744 11.6615 2.01663 11.3532 1.79785C11.2848 1.74932 11.2124 1.70615 11.1366 1.66877C10.7951 1.50024 10.3853 1.50024 9.56558 1.50024C8.72532 1.50024 8.30522 1.50024 7.95806 1.67583C7.88112 1.71475 7.8077 1.75967 7.73856 1.81012C7.4266 2.03777 7.25234 2.40141 6.90383 3.12869L6.42627 4.12524" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <button type="button" class="add-payment-btn">+ Add Payment</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div>
                                <div class="payment-container mb-2 amount-info-container">

                                     <div class="row save-amount-container  align-items-center mb-2">
                                                <h6 class="total-text col-5 w-100">{{ __('Total') }}</h6>
                                                <div class="col-7 w-100">
                                                    <div class='total-amount  w-100' id="total_amount">{{ currency_format($sale->actual_total_amount, currency: business_currency()) }}</div>
                                                </div>
                                            </div>
                                    <div class="row save-amount-container  align-items-center mb-2">
                                        <h6 class="payment-title col-6">{{ __('Tax') }}</h6>
                                        <div class="col-6 w-100 d-flex justify-content-between gap-2">
                                            <div class="d-flex d-flex align-items-center gap-2">
                                                <select name="tax_id" class="form-select amount-container tax_select" id='form-ware'>
                                                    <option value="">{{ __('Select') }}</option>
                                                    @foreach($taxes as $tax)
                                                        <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}" @selected($sale->tax_id == $tax->id)>{{ $tax->name }} ({{ $tax->rate }}%)</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="number" step="any" name="tax_amount" id="tax_amount"
                                                   value="{{ ($sale->tax_amount ?? 0) != 0 ? $sale->tax_amount : (($sale->tax_percent ?? 0) != 0 ? $sale->tax_percent : 0) }}"
                                                   min="0" class="form-control amount-container right-start-input" placeholder="{{ __('0') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row save-amount-container  align-items-center mb-2">
                                        <h6 class="payment-title col-6">{{ __('Discount') }}</h6>
                                        <div class="col-6 w-100 d-flex justify-content-between gap-2">
                                            <div class="d-flex d-flex align-items-center gap-2">
                                                <select name="discount_type" class="form-select amount-container discount_type" id='form-ware'>
                                                    <option value="flat" @selected($sale->discount_type == 'flat')>{{ __('Flat') }}</option>
                                                    <option value="percent" @selected($sale->discount_type == 'percent')>{{ __('Percent') }}</option>
                                                </select>
                                            </div>

                                            <input type="number" step="any" name="discountAmount"
                                                   value="{{ $sale->discount_type == 'percent' ? $sale->discount_percent : $sale->discountAmount }}"
                                                   id="discount_amount" min="0" class="form-control amount-container right-start-input" placeholder="{{ __('0') }}">
                                        </div>
                                    </div>

                                    <div class="row save-amount-container  align-items-center mb-2">
                                        <h6 class="payment-title col-6">{{ __('Delivery Cost') }}</h6>
                                        <div class="col-12 ">
                                            <input type="number" step="any" name="shipping_charge" id="shipping_charge" value="{{ $sale->shipping_charge }}" class="form-control amount-container right-start-input" placeholder="0">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="payment-btn-container-sm payment-section-3">
                           <button type="submit" class="submit-btn payment-btn" name="status" value="final">
                               {{ __('Save & Print') }}
                           </button>
                       </div>
                   <div class="sales-purchase-btn-container payment-section-3">
                       <div class="cancel-sale-btn-container">
                           <button class="save-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">{{ __('Reset') }}</button>
                       </div>
                       <div class="sale-purchase-save-container">
                           <button type="submit" class="submit-btn payment-btn sale-purchase-save" name="status" value="draft">
                               {{ __('Save') }}
                           </button>
                       </div>
                       <div class="payment-btn-container-lg">
                           <button type="submit" class="submit-btn payment-btn" name="status" value="final">
                               {{ __('Save & Print') }}
                           </button>
                       </div>
                   </div>
                    <!-- Make Payment Section end -->
                </form>
            </div>

            <!-- Side Modal (Offcanvas) -->
            <div
                class="offcanvas offcanvas-start"
                tabindex="-1"
                id="sideModal"
                aria-labelledby="sideModalLabel"
            >
                <div class="offcanvas-header">
                    <h5 id="sideModalLabel">{{ __('Quick Action') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="offcanvas-body">
                    <div class="d-flex align-items-center justify-content-center flex-column gap-3">
                        <a class="today-sale-btn-2 w-100 d-flex align-items-center justify-content-center gap-2" href="">
                            <img src="{{ asset('assets/images/icons/sales-dash.svg') }}" alt="">
                            {{ __('Today Sales') }}
                        </a>
                        <a class="sale-stock-btn-2 w-100 d-flex align-items-center justify-content-center gap-2 stock-get" href="#product-list-modal" data-bs-toggle="modal">
                            <img src="{{ asset('assets/images/icons/stock.svg') }}" alt="">
                            {{ __('Stock List') }}
                        </a>
                        <a class="sale-save-btn-2  w-100 d-flex align-items-center justify-content-center gap-2" href="#save-order-modal" data-bs-toggle="modal">
                            <img src="{{ asset('assets/images/icons/save-icon.svg') }}" alt="">
                            {{ __('Save') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>

        @php
            $currency = business_currency();
            $rounding_amount_option = sale_rounding();
        @endphp
        {{-- Hidden input fields to store currency details --}}
        <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
        <input type="hidden" id="currency_position" value="{{ $currency->position }}">
        <input type="hidden" id="currency_code" value="{{ $currency->code }}">

        <input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
        <input type="hidden" value="{{ route('business.carts.index') }}" id="get-cart">
        <input type="hidden" value="{{ route('business.sales.cart-data') }}" id="get-cart-data">
        <input type="hidden" value="{{ route('business.carts.remove-all') }}" id="clear-cart">
        <input type="hidden" value="{{ route('business.sales.stock-list') }}" id="stock-data-get">

        <input type="hidden" id="rounding_amount_option" value="{{ $rounding_amount_option }}">
        <input type="hidden" id="get_stock_prices" value="{{ route('business.products.stocks-prices') }}">

        @endsection

        @push('modal')
            @include('business::sales.calculator')
            @include('business::sales.customer-create')
            @include('business::sales.stock-list')
        @endpush

        @push('js')
            <script src="{{ asset('assets/js/choices.min.js') }}"></script>
            <script src="{{ asset('assets/js/custom/sale.js') }}?v={{ time() }}"></script>
            <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
            <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
        @endpush
