@extends('business::layouts.master')

@section('title')
    {{ __('Purchase') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
@endpush

@section('main_content')
    <div class="custome-container-fluid custom-min-height">
        <div class="grid sales-main-container p-lr">
            <div class=" main-container pr-0 w-auto">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="container-fluid p-0 m-0">
                        <div class="g-2 w-100 align-items-center search-product">
                            <div class="w-100">
                                <!-- Search Input and Add Button -->
                                <form action="{{ route('business.purchases.product-filter') }}" method="post"
                                      class="product-filter product-filter-form w-100" table="#products-list">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-lg-12">
                                            <select name="category_id" id="category_id" class="form-control sale-purchase-input sale-purchase-select">
                                                <option value="">{{ __('Select a category') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-flex col-lg-12">
                                            <input type="text" name="search" id="purchase_product_search" class="form-control search-input sale-purchase-input" placeholder="{{ __('Search product...') }}">
                                            @if (auth()->user()->role != 'staff' || visible_permission('productPermission'))
                                            <a href="{{ route('business.products.create') }}" class="btn btn-search">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.8333 2.5H4.16667C3.24619 2.5 2.5 3.24619 2.5 4.16667V15.8333C2.5 16.7538 3.24619 17.5 4.16667 17.5H15.8333C16.7538 17.5 17.5 16.7538 17.5 15.8333V4.16667C17.5 3.24619 16.7538 2.5 15.8333 2.5Z" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
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
                            @include('business::purchases.product-list')
                        </div>
                    </div>
                </div>
            </div>
            <div class="sales-container">
                <!-- Quick Action Section -->
                <div class="quick-act-header">
                    <div class="d-flex  justify-content-between align-items-center">
                        <div class="mb-2 mb-sm-0">
                            <h4 class='quick-act-title'>{{ __('Quick Action') }}</h4>
                        </div>
                        <div class="quick-actions-container">
                            <a class="sale-stock-btn stock-get" href="#stock-list-modal" data-bs-toggle="modal">
                                {{__('Stock List')}}
                            </a>
                            <a class="today-sale-btn" href="{{ route('business.purchases.index', ['today' => true]) }}">
                                {{__('Today Purchases')}}
                            </a>

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
                <form action="{{ route('business.purchases.store') }}" method="post" enctype="multipart/form-data" class="ajaxform_redirect_invoice">
                    @csrf
                    <div class="mt-2 mb-2">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <select name="party_id" id="supplier_id" class="form-select sale-purchase-input choices-select" aria-label="Select Supplier" required>
                                        <option value="">{{ __('Select Supplier') }}</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" data-type="{{ $supplier->type }}" data-phone="{{ $supplier->phone }}">
                                                {{ $supplier->name }}({{ $supplier->type }}{{ $supplier->due ? ' ' . currency_format($supplier->due, currency: business_currency()) : '' }}){{ $supplier->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a type="button" href="#supplier-create-modal" data-bs-toggle="modal"
                                       class="btn  square-btn d-flex justify-content-center align-items-center"> <img src="{{ asset('assets/images/icons/plus-square.svg') }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cart-payment">
                       <div class="table-responsive-custom">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                <tr>
                                    <th class="sales-purchase-th">{{ __('Image') }}</th>
                                    <th class="sales-purchase-th">{{ __('Item') }}</th>
                                    <th class="sales-purchase-th">{{ __('Batch') }}</th>
                                    <th class="sales-purchase-th">{{ __('Unit Price') }}</th>
                                    <th class="sales-purchase-th">{{ __('Qty') }}</th>
                                    <th class="sales-purchase-th">{{ __('Sub Total') }}</th>
                                    <th class="sales-purchase-th">{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody class="text-start" id="purchase_cart_list">
                                @include('business::purchases.cart-list')
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="make-payment-with-btn">
                        <!-- Make Payment Section start -->
                        <div >
                            <div class="grid payment-section g-3">
                                <div>
                                    <div class="amount-info-container">
                                        <div class="row amount-container  align-items-center mb-2">
                                            <h6 class="payment-title">{{ __('Receive Amount') }}</h6>
                                            <input name="receive_amount" type="number" step="any" id="receive_amount"
                                                   min="0" class="form-control amount-container"
                                                   placeholder="{{ currency_format(0, 'icon', 2, business_currency()) }}">
                                        </div>
                                        <div class="row amount-container  align-items-center mb-2">
                                            <h6 class="payment-title">{{ __('Change Amount') }}</h6>
                                            <input type="number" step="any" id="change_amount" class="form-control amount-container"
                                                   placeholder="{{ currency_format(0, 'icon', 2, business_currency()) }}"
                                                   readonly>
                                        </div>
                                        <div class="row amount-container  align-items-center mb-2">
                                            <h6 class="payment-title">{{ __('Due Amount') }}</h6>
                                            <input type="number" step="any" id="due_amount" class="form-control amount-container"
                                                   placeholder="{{ currency_format(0, 'icon', 2, business_currency()) }}"
                                                   readonly>
                                        </div>

                                        <div class="row amount-container align-items-center mb-2">
                                            <h6 class="payment-title">{{ __('Payment Type') }}</h6>
                                            <div class="p-0">
                                                <!-- Original select for single payment -->
                                                <select name="payment_type_id" class="form-select" id='payment_type'>
                                                    @foreach($payment_types as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>

                                                <div class="payment-main-container">
                                                    {{-- Dynamic grids will be appended here --}}
                                                </div>

                                                {{-- <button class="add-payment-btn">+ {{__('Add Payment')}}</button> --}}
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div>
                                    <div class="payment-container amount-info-container">
                                        <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="total-text col-5 w-100">{{ __('Total') }}</h6>
                                            <div class="col-7 w-100">
                                                <div class='total-amount  w-100' id="total_amount"> {{ currency_format(0, 'icon', 2, business_currency()) }}</div>
                                            </div>
                                        </div>
                                        {{-- <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="payment-title col-6">{{ __('Vat') }}</h6>
                                            <div class="col-6 w-100 d-flex justify-content-between gap-2">
                                                <div class="d-flex d-flex align-items-center gap-2">
                                                    <select name="tax_id" class="form-select amount-container tax_select null_by_reset" id='form-ware'>
                                                        <option value="">{{ __('Select') }}</option>
                                                        @foreach($taxes as $tax)
                                                            <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->name }} ({{ $tax->rate }}%)</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input type="number" step="any" name="tax_amount" id="tax_amount" min="0" class="amount-container form-control right-start-input" placeholder="{{ __('0') }}" readonly>
                                            </div>
                                        </div> --}}
                                        <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="payment-title col-6">{{ __('Discount') }}</h6>
                                            <div class="col-6 w-100 d-flex justify-content-between gap-2">
                                                <div class="d-flex d-flex align-items-center gap-2">
                                                    <select name="discount_type" class="form-select discount_type amount-container" id='form-ware'>
                                                        {{-- <option value="">{{ __('Select') }}</option>    --}}
                                                        <option value="flat">{{ __('Flat') }}</option>
                                                        <option value="percent">{{ __('Percent') }}</option>
                                                    </select>
                                                </div>
                                                <input type="number" step="any" name="discountAmount" id="discount_amount" min="0" class="form-control amount-container right-start-input" placeholder="{{ __('0') }}">
                                            </div>
                                        </div>
                                        <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="payment-title col-6">{{ __('Total Discount') }}</h6>
                                            <div class="col-6 w-100">
                                                <input type="number" step="any" id="total_discount_display" class="amount-container form-control right-start-input" placeholder="{{ __('0') }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="payment-title col-6 text-muted small">{{ __('Item VAT') }}</h6>
                                            <div class="col-6 w-100">
                                                <input type="number" step="any" id="product_wise_vat_display" class="amount-container form-control right-start-input bg-light" placeholder="{{ __('0') }}" readonly title="Sum of product-wise VAT (info only)">
                                            </div>
                                        </div>
                                        {{-- <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="payment-title col-6 text-muted small">{{ __('Item Discount') }}</h6>
                                            <div class="col-6 w-100">
                                                <input type="number" step="any" id="product_wise_discount_display" class="amount-container form-control right-start-input bg-light" placeholder="{{ __('0') }}" readonly title="Sum of product-wise Discount (info only)">
                                            </div>
                                        </div> --}}
                                        {{-- <div class="row save-amount-container  align-items-center mb-2">
                                            <h6 class="payment-title col-6">{{ __('Delivery Cost') }}</h6>
                                            <div class="col-12">
                                                <input type="number" step="any" name="shipping_charge" id="shipping_charge" class="form-control amount-container right-start-input" placeholder="0">
                                            </div>
                                        </div> --}}
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
                               <button class="save-btn cancel-sale-btn"
                                       data-route="{{ route('business.carts.remove-all') }}">
                                   {{ __('Reset') }}
                               </button>
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
                    </div>
                </form>
            </div>
        </div>
        <div
            class="offcanvas offcanvas-start"
            tabindex="-1"
            id="sideModal"
            aria-labelledby="sideModalLabel"
        >
            <div class="offcanvas-header">
                <h5 id="sideModalLabel"> {{__('Quick Action')}} </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    aria-label="Close"
                ></button>
            </div>
            <div class="offcanvas-body">
                <div class="d-flex align-items-center justify-content-center flex-column gap-3">
                    <a class="today-sale-btn-2 w-100 d-flex align-items-center justify-content-center gap-2" href="{{ route('business.purchases.index', ['today' => true]) }}">
                        <img src="{{ asset('assets/images/icons/sales-dash.svg') }}" alt="">
                        {{__('Today Purchases')}}
                    </a>
                    <a class="sale-stock-btn-2 w-100 d-flex align-items-center justify-content-center gap-2 stock-get" href="#stock-list-modal" data-bs-toggle="modal">
                        <img src="{{ asset('assets/images/icons/stock.svg') }}" alt="">
                        {{__('Stock List')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('business::purchases.product-modal')

    @php
        $currency = business_currency();
    @endphp
    {{-- Hidden input fields to store currency details --}}
    <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
    <input type="hidden" id="currency_position" value="{{ $currency->position }}">
    <input type="hidden" id="currency_code" value="{{ $currency->code }}">

    <input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
    <input type="hidden" value="{{ route('business.purchases.cart') }}" id="purchase-cart">
    <input type="hidden" value="{{ route('business.carts.remove-all') }}" id="clear-cart">
    <input type="hidden" value="{{ route('business.sales.stock-list') }}" id="stock-data-get">
@endsection

@push('modal')
    @include('business::purchases.calculator')
    @include('business::purchases.supplier-create')
    @include('business::purchases.stock-list')
    @include('business::purchases.bulk-upload.index')
@endpush

@push('js')
    <script>
        // Make box sizes available globally with name and value
        window.allBoxSizes = @json($box_sizes->map(function($item) {
            return ['name' => $item->name, 'value' => $item->value];
        })->toArray());
    </script>
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/purchase.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
@endpush
