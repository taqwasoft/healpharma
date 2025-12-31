<div class="modal fade" id="product-list-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header ">
                <h1 class="modal-title fs-5">{{ __('Stock List') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="my-3 mt-0 position-relative search-input-container">
                    <input type="text" name="search" class="form-control sale-purchase-input stock-search"
                           placeholder="{{ __('Search') }}">
                    <img src="{{ asset('assets/images/icons/search.svg') }}" alt="">
                </div>
                <div class="table-responsive save-order-table">
                    <table id="lowStock" class="table text-center">
                        <thead class="table-light">
                        <tr>
                            <th> {{__('SL.')}} </th>
                            <th class="text-start"> {{__('Product')}} </th>
                            <th> {{__('Cost')}} </th>
                            <th> {{__('Qty')}} </th>
                            <th> {{__('Sale')}} </th>
                            <th> {{__('Stock Value')}} </th>
                        </tr>
                        </thead>
                        <tbody class="table-stock">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
