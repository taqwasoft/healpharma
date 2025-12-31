<div class="modal fade p-0" id="product-view">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('View') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body order-form-section">
                <div class="view-modal">
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                           <span>{{ __('Product Image') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                            <span> <img class="table-img" src="" alt="" id="product_image"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                           <span>{{ __('Product Name') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                            <span id="product_name"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Code') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                           <span id="product_code"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Category') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                           <span id="product_category"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Unit') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                           <span id="product_unit"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Purchase price') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                          <span
                                id="product_purchase_price"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Sale price') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                          <span id="product_sale_price"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Wholesale Price') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                          <span
                                id="product_wholesale_price"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                          <span>{{ __('Dealer Price') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                         <span
                                id="product_dealer_price"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                            <span>{{ __('Stock') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                            <span id="product_stock"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                            <span>{{ __('Low Stock') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                            <span id="product_low_stock"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                            <span>{{ __('Expire Date') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                            <span id="expire_date"></span>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                            <span>{{ __('Manufacturer') }}</span>
                        </div>
                        <div class="col-1">
                            <span>:</span>
                        </div>
                        <div class="col-6">
                            <span id="product_manufacturer"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
