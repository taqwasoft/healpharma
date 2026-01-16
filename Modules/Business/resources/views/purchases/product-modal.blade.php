<div class="modal fade" id="product-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <h1 class="modal-title fs-5">{{ __('Add Items') }}</h1>

                <button type="button" class="btn-close custom-close-btn" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>

            <div class="modal-body">
                <div class="personal-info">
                        <form id="purchase_modal" data-route="{{ route('business.carts.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-2 mt-2">
                                <ul>
                                    <li><span class="fw-bold">{{ __('Product Name') }}</span> <span>:</span> <span id="product_name"></span></li>
                                </ul>
                            </div>
                            <div class="col-lg-3 mb-2 mt-2 text-end">
                                <ul>
                                    <li><span class="fw-bold">{{ __('Stock') }}</span> <span>:</span> <span id="stock"></span></li>
                                </ul>
                            </div>
                            <div class="col-lg-3 mb-2 mt-2 text-end">
                                <ul>
                                    <li><span class="fw-bold">{{ __('Net Total') }}</span> <span>:</span> <span id="net_total_display">0</span></li>
                                </ul>
                            </div>

                            <!-- Box Size and Box Qty in one row -->
                            <div class="col-lg-12 mb-2" id="pack_row_container" style="display: none;">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label>{{ __('Box Size') }}</label>
                                        <select name="pack_size" id="pack_size" class="form-control">
                                            <option value="">{{ __('Select Box Size') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>{{ __('Box Qty') }}</label>
                                        <input type="number" step="any" name="pack_qty" id="pack_qty" class="form-control" placeholder="{{ __('Enter Box Qty') }}" value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Qty, Bonus Qty, Product Qty (3 fields per row) -->
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Invoice Qty') }}</label>
                                <input type="number" step="any" name="invoice_qty" id="invoice_qty" required class="form-control" placeholder="{{ __('Enter Invoice Qty') }}" value="0">
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Bonus Qty') }}</label>
                                <input type="number" step="any" name="bonus_qty" id="bonus_qty" required class="form-control" placeholder="{{ __('Enter Bonus Qty') }}" value="0">
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Total Quantity') }}</label>
                                <input type="number" step="any" name="product_qty" id="product_qty" required class="form-control" placeholder="{{ __('Total Quantity') }}" readonly>
                            </div>

                            <!-- Gross Total, VAT Amount, Discount (3 fields per row) -->
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Gross Total Price(TP)') }}</label>
                                <input type="number" step="any" name="gross_total_price" id="gross_total_price" required class="form-control" placeholder="{{ __('Enter Gross Total') }}">
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label>{{ __('VAT Amount') }}</label>
                                <input type="number" step="any" name="vat_amount" id="vat_amount" class="form-control" placeholder="{{ __('Enter VAT Amount') }}" value="0">
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label>{{ __('Discount Amount') }}</label>
                                <input type="number" step="any" name="discount_amount" id="product_discount_amount" class="form-control" placeholder="{{ __('Enter Discount') }}" value="0">
                            </div>

                            <!-- Purchase Prices (3 fields per row) -->
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Unit Purchase Price') }}</label>
                                <input type="number" step="any" name="purchase_exclusive_price" id="purchase_exclusive_price" required class="form-control" placeholder="{{ __('Unit Price') }}" readonly>
                            </div>
                            {{-- <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Purchase Inclusive Price') }}</label> --}}
                                <input type="hidden" step="any" name="purchase_inclusive_price" id="purchase_inclusive_price" required class="form-control" placeholder="{{ __('Inclusive Price') }}" readonly>
                            {{-- </div> --}}
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Profit Percent') }}</label>
                                <input type="number" step="any" name="profit_percent" id="profit_percent" required class="form-control" placeholder="{{ __('Enter Profit %') }}">
                            </div>

                            <!-- Sale Price (3 fields per row) -->
                            <div class="col-lg-4 mb-2">
                                <label class="required">{{ __('Sale Price') }}</label>
                                <input type="number" step="any" name="sales_price" id="sales_price" required class="form-control" placeholder="{{ __('Enter Sales Price') }}">
                            </div>
                            {{-- <div class="col-lg-4 mb-2">
                                <label>{{ __('WholeSale Price') }}</label> --}}
                                <input type="hidden" step="any" name="wholesale_price" id="wholesale_price" class="form-control" placeholder="{{ __('Auto-calculated') }}" readonly style="background-color: #f0f0f0;">
                            {{-- </div> --}}
                            {{-- <div class="col-lg-4 mb-2">
                                <label>{{ __('Dealer Price') }}</label> --}}
                                <input type="hidden" step="any" name="dealer_price" id="dealer_price" class="form-control" placeholder="{{ __('Auto-calculated') }}" readonly style="background-color: #f0f0f0;">
                            {{-- </div> --}}

                            <!-- Batch and Expire Date (3 fields per row) -->
                            <div class="col-lg-6 mb-2">
                                <label>{{ __('Batch') }}</label>
                                <input type="text" name="batch_no" id="batch_no" class="form-control" placeholder="{{ __('Enter Batch No.') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>{{ __('Expire Date') }}</label>
                                <input type="date" name="expire_date" id="expire_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="button-group text-center mt-5">
                                <button type="reset" class="theme-btn border-btn m-2">{{ __('Reset') }}</button>
                                <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
