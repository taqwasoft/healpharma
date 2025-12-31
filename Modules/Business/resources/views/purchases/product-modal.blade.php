<div class="modal fade" id="product-modal">
    <div class="modal-dialog modal-dialog-centered modal-md">
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
                            <div class="col-lg-6 mb-2 mt-2 text-end">
                                <ul>
                                    <li><span class="fw-bold">{{ __('Stock') }}</span> <span>:</span> <span id="stock"></span></li>
                                </ul>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label class="required">{{ __('Quantity') }}</label>
                                <input type="number" step="any" name="product_qty" id="product_qty" required class="form-control" placeholder="{{ __('Enter Quantity') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="required">{{ __('Purchase Exclusive Price') }}</label>
                                <input type="number" step="any" name="purchase_exclusive_price" id="purchase_exclusive_price" required class="form-control" placeholder="{{ __('Enter Exclusive Purchase Price') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="required">{{ __('Purchase Inclusive Price') }}</label>
                                <input type="number" step="any" name="purchase_inclusive_price" id="purchase_inclusive_price" required class="form-control" placeholder="{{ __('Enter Inclusive Purchase Price') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="required">{{ __('Profit Percent') }}</label>
                                <input type="number" step="any" name="profit_percent" id="profit_percent" required class="form-control" placeholder="{{ __('Enter Profit Percent') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label class="required">{{ __('Sale Price') }}</label>
                                <input type="number" step="any" name="sales_price" id="sales_price" required class="form-control" placeholder="{{ __('Enter Sales Price') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>{{ __('WholeSale Price') }}</label>
                                <input type="number" step="any" name="wholesale_price" id="wholesale_price" class="form-control" placeholder="{{ __('Enter WholeSale Price') }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>{{ __('Dealer Price') }}</label>
                                <input type="number" step="any" name="dealer_price" id="dealer_price" class="form-control" placeholder="{{ __('Enter Dealer Price') }}">
                            </div>
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
