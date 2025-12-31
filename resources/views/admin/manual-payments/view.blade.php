<div class="modal fade" id="manual-view-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Subscriber View') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <div class="row mt-2">
                        <div class="col-12  costing-list">
                            <img width="100px" width="100px" class="rounded-circle border-2 shadow" src=""
                                id="image" alt="">
                        </div>
                    </div>
                    <div class="row align-items-center mt-4">
                        <div class="col-md-4">
                            <p>{{ __('Store Name') }}</p>
                        </div>
                        <div class="col-1">
                            <p>:</p>
                        </div>
                        <div class="col-md-7">
                            <p class="business_name"></p>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-4">
                            <p>{{ __('Category') }}</p>
                        </div>
                        <div class="col-1">
                            <p>:</p>
                        </div>
                        <div class="col-md-7">
                            <p id="category"></p>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-4">
                            <p>{{ __('Package') }}</p>
                        </div>
                        <div class="col-1">
                            <p>:</p>
                        </div>
                        <div class="col-md-7">
                            <p id="package"></p>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-md-4">
                            <p>{{ __('Gateway Name') }}</p>
                        </div>
                        <div class="col-1">
                            <p>:</p>
                        </div>
                        <div class="col-md-7">
                            <p id="gateway"></p>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-4">
                            <p>{{ __('Enroll Date') }}</p>
                        </div>
                        <div class="col-1">
                            <p>:</p>
                        </div>
                        <div class="col-md-7">
                            <p id="enroll_date"></p>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-4">
                            <p>{{ __('Expire date') }}</p>
                        </div>
                        <div class="col-1">
                            <p>:</p>
                        </div>
                        <div class="col-md-7">
                            <p id="expired_date"></p>
                        </div>
                    </div>

                    <div class="row mt-2" id="manual_img">
                        <img width="100px" src="" id="manul_attachment" alt="">
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
