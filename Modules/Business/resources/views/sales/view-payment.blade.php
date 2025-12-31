 <div class="modal fade p-0" id="view-sale-payment-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('View Payments') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body order-form-section">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Paid By') }}</th>
                            </tr>
                        </thead>
                        <tbody id="sale-payments-data">
                            {{-- Filled via jQuery --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
