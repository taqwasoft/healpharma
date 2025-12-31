@extends('business::layouts.master')

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('main_content')
    <div class="container-fluid ">
        @php
            $notStaff = auth()->user()->role != 'staff';
            $SalePurchasePermission = (
                visible_permission('salesListPermission') ||
                visible_permission('purchaseListPermission')
            );
        @endphp

        @if (env('DEMO_MODE') && auth()->user()->email == 'shopowner@acnoo.com')
        <div id="demoAlert" class="custom-alert">
            <span><b class="text-white">Note:</b> This is a demo account — data resets every hour for this account only. Some of module are disabled in this account, to get full access please please create your own account.</span>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        @endif

        <div class="business-dashboard-container">
            <div>
                <div class="business-stat">
                    @if ($notStaff || visible_permission('partiesPermission'))
                    <div class="business-stat-content">
                        <div class="d-flex  justify-content-between">
                            <h6>{{ __("Total Customer") }}</h6>
                            <img src="{{ asset('assets/images/dashboard/Customer.svg') }}" alt="" >
                        </div>
                        <h4 id="total_customer"></h4>
                        <p>
                            <span class="dynamic-color" id="customer_percentage"></span><span id="customer_arrow"></span>+ <small id="today_customer"></small> {{ __('Today') }}
                        </p>
                    </div>
                    <div class="business-stat-content">
                        <div class="d-flex  justify-content-between">
                            <h6>{{ __("Total Supplier") }}</h6>
                            <img src="{{ asset('assets/images/dashboard/supplier.svg') }}" alt="" >
                        </div>
                        <h4 id="total_supplier"></h4>
                        <p>
                            <span class="dynamic-color" id="supplier_percentage"></span><span id="supplier_arrow"></span>+ <small id="today_supplier"></small> {{ __('Today') }}
                        </p>
                    </div>
                    @endif
                    @if ($notStaff || visible_permission('stockPermission'))
                    <div class="business-stat-content">
                        <div class="d-flex  justify-content-between">
                            <h6>{{ __('Stock Medicine') }}</h6>
                            <img src="{{ asset('assets/images/dashboard/stock.svg') }}" alt="" >
                        </div>
                        <h4 id="total_stock"></h4>
                        <p>
                            <span class="dynamic-color" id="stock_percentage"></span><span id="stock_arrow"></span>+ <small id="today_stock"></small> {{ __('Today') }}
                        </p>
                    </div>
                    @endif
                    @if ($notStaff || visible_permission('productPermission'))
                    <div class="business-stat-content">
                        <div class="d-flex  justify-content-between">
                            <h6>{{ __('Expired Medicine') }}</h6>
                            <img src="{{ asset('assets/images/dashboard/expired.svg') }}" alt="" >
                        </div>
                        <h4 id="total_expire"></h4>
                        <p>
                            <span class="dynamic-color" id="expire_percentage"></span><span id="expire_arrow"></span>+ <small id="today_expire"></small> {{ __('Today') }}
                        </p>
                    </div>
                    @endif
                </div>
                @if ($notStaff || visible_permission('salesListPermission'))
                <div class="business-chart-container mt-20">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{ __('Profit / Loss') }}</h4>
                          <div class="gpt-up-down-arrow position-relative">
                              <select class="form-control loss-profit-year">
                                  @for ($i = date('Y'); $i >= 2022; $i--)
                                      <option @selected($i == date('Y')) value="{{ $i }}">{{ $i }}
                                      </option>
                                  @endfor
                              </select>
                              <span></span>
                          </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2 mt-2">
                        <div class="d-flex align-items-center gap-2">
                              <div class="profit-circle"></div>
                              <p>{{ __('Profit') }} : <span class="profit-value"></span></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                              <div class="loss-circle"></div>
                              <p>{{ __('Loss') }}: <span class="loss-value"></span></p>
                        </div>
                    </div>
                    <div class="lossprofit-chart-container">
                     <canvas id="profitLossChart"></canvas>
                    </div>
                </div>
                @endif
                @if ($notStaff || $SalePurchasePermission)
                <div class="business-chart-container mt-20">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{ __('Sales & purchase') }}</h4>
                          <div class="gpt-up-down-arrow position-relative">
                              <select class="form-control sale-purchase-year" name="year">
                                  @for ($i = date('Y'); $i >= 2022; $i--)
                                      <option @selected($i == date('Y')) value="{{ $i }}">{{ $i }}</option>
                                  @endfor
                              </select>
                              <span></span>
                          </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2 mt-2">
                        <div class="d-flex align-items-center gap-2">
                              <div class="profit-circle"></div>
                              <p>{{ __('Purchase') }} : <span class="purchase-value"></span></p>
                          </div>
                        <div class="d-flex align-items-center gap-2">
                              <div class="loss-circle"></div>
                              <p>{{ __('Sales') }} : <span class="sale-value"></span></p>
                        </div>
                    </div>
                    <div class="lossprofit-chart-container">
                     <canvas id="salesChart"></canvas>
                    </div>
                </div>
                @endif
            </div>
            <div>
                @if ($notStaff || visible_permission('SalePurchasePermission')  || visible_permission('addIncomePermission') || visible_permission('addExpensePermission'))
                <div class="business-chart-container">
                    <div class="d-flex align-items-center justify-content-between">
                    <h4>{{ __("Overall Report") }}</h4>
                    <div class="gpt-up-down-arrow position-relative">
                        <select class="form-control overall-year" name="year">
                            @for ($i = date('Y'); $i >= 2022; $i--)
                                <option @selected($i == date('Y')) value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <span></span>
                    </div>
                    </div>
                    <div class="chart-container position-relative ">
                        <canvas id="todayReportChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle chart-middle-content">
                          <h2 id="today_loss_profit"></h2>
                          <p>{{ __("Today's Profit") }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="sales-circle"></div>
                                <p>{{ __('Sales') }}</p>
                            </div>
                            <h5 id="overall_sale"></h5>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="purchase-circle"></div>
                                <p>{{ __('Purchase') }}</p>
                            </div>
                            <h5 id="overall_purchase"></h5>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="income-circle"></div>
                                <p>{{ __('Income') }}</p>
                            </div>
                            <h5 id="overall_income"></h5>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="expense-circle"></div>
                                <p>{{ __('Expense') }}</p>
                            </div>
                            <h5 id="overall_expense"></h5>
                        </div>
                    </div>
                </div>
                @endif
                @if ($notStaff || visible_permission('stockPermission'))
                <div class="business-chart-container p-0 mt-20 low-stock-table-container">
                    <div class="d-flex align-items-center p-3 justify-content-between">
                        <h4>{{ __('Low Stock') }}</h4>
                        <div class="d-flex align-items-center justify-content-between">
                            <a class="view-all-btn" href="{{ route('business.stocks.index', ['alert_qty' => true]) }}">{{ __('View All') }}
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                               <path d="M6.5 12L10.5 8L6.5 4" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                               </svg>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive low-stock-table-content">
                      <table id="lowStock" class="table text-center">
                        <thead class="table-light">
                          <tr>
                            <th>{{ __('SL.') }}</th>
                            <th class="text-start">{{ __('Name') }}</th>
                            <th>{{ __('Alert Qty') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($stocks as $stock)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td class="text-start low-stock-name">{{ $stock->product->productName ?? '' }}<p class="batch-num">{{__('Batch')}}: {{ $stock->batch_no }}</p></td>
                                <td class="alert-qty">{{ $stock->productStock }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>

                </div>
                @endif
            </div>
        </div>

        <div class="top-expired-product-container mt-20">

                @if ($notStaff || visible_permission('productPermission'))
                <div class="business-chart-container ">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{ __('Top 5 Product') }}</h4>
                        <a class="view-all-btn" href="{{ route('business.products.index') }}">{{ __("View All") }}<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M6.5 12L10.5 8L6.5 4" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                           </svg>
                        </a>
                    </div>
                    @foreach ($top_products as $top_product)
                    <div class="d-flex align-items-center justify-content-between top-expired-product-content mt-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset($top_product->image ?? 'assets/images/logo/default-img.jpg') }}" alt="" >
                            <div>
                                <h6>{{ $top_product->productName }}</h6>
                                <p>{{ __('Batch') }}: {{ $top_product->batch_no }}</p>
                            </div>
                        </div>
                        <h5>{{ currency_format($top_product->price, 'icon', 2, business_currency()) }}</h5>
                    </div>
                    @endforeach
                </div>
                @endif
                @if ($notStaff || visible_permission('partiesPermission'))
                    <div class="business-chart-container ">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4>{{ __('Top 5 Customer') }}</h4>
                            <a class="view-all-btn" href="{{ route('business.parties.index',['type' => 'Customer']) }}">{{ __("View All") }}<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.5 12L10.5 8L6.5 4" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </a>
                        </div>
                        @foreach ($top_customers as $top_customer)
                        <div class="d-flex align-items-center justify-content-between top-expired-product-content mt-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($top_customer->image ?? 'assets/images/logo/default-img.jpg') }}" alt="" >
                                <div>
                                    <h6>{{ $top_customer->name }}</h6>
                                    <p>{{ $top_customer->phone }}</p>
                                </div>
                            </div>
                            <h5>{{ currency_format($top_customer->total_amount, 'icon', 2, business_currency()) }}</h5>
                        </div>
                        @endforeach
                    </div>
                @endif
                @if ($notStaff || visible_permission('productPermission'))
                    <div class="business-chart-container">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4>{{ __('Expired Product') }}</h4>
                            <a class="view-all-btn" href="{{ route('business.expired-products.index') }}">{{ __('View All') }} <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.5 12L10.5 8L6.5 4" stroke="#667085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </a>
                        </div>
                        @foreach ($expired_products as $expired_product)
                        <div class="d-flex align-items-center justify-content-between top-expired-product-content mt-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($expired_product->product->images[0] ?? 'assets/images/logo/default-img.jpg') }}" alt="" >
                                <div>
                                    <h6>{{ $expired_product->product->productName ?? '' }}</h6>
                                    <p>{{ __('Batch') }}: {{ $expired_product->batch_no }}</p>
                                </div>
                            </div>
                            <h5>{{ formatted_date($expired_product->expire_date) }}</h5>
                        </div>
                        @endforeach
                </div>
                @endif
        </div>
    </div>
    @php
        $currency = business_currency();
    @endphp
    {{-- Hidden input fields to store currency details --}}
    <input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
    <input type="hidden" id="currency_position" value="{{ $currency->position }}">
    <input type="hidden" id="currency_code" value="{{ $currency->code }}">

    <input type="hidden" value="{{ route('business.dashboard.data') }}" id="get-dashboard">
    <input type="hidden" value="{{ route('business.dashboard.overall-report') }}" id="get-overall-report">
    <input type="hidden" value="{{ route('business.dashboard.lossProfit') }}" id="loss-profit-data">
    <input type="hidden" value="{{ route('business.dashboard.salePurchase') }}" id="sale-purchase-data">
@endsection

@push('js')
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/business-dashboard.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/custom/custom-business.js') }}"></script>
@endpush




