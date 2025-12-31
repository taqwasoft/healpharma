<div class="modal fade p-0" id="stock-modal-view" >
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('View Stock') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body order-form-section">
                <div class="costing-list">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Batch') }}</th>
                                    <th>{{ __('Stock') }}</th>
                                    <th>{{ __('Purchase Without TAX') }}</th>
                                    <th>{{ __('Purchase With TAX') }}</th>
                                    <th>{{ __('MRP') }}</th>
                                    <th>{{ __('WholeSale Price') }}</th>
                                    <th>{{ __('Dealer Price') }}</th>
                                    @if (is_module_enabled($modules, 'show_expire_date'))
                                    <th>{{ __('Expire Date') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="stocks-table-data">
                                 {{-- Filled via jQuery --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

