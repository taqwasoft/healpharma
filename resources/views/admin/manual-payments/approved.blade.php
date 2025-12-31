<div class="modal fade" id="approve-modal">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Are you sure?') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <form action="" method="post" enctype="multipart/form-data"
                        class="ajaxform_instant_reload manualPaymentForm">
                        @csrf
                        <div class="row">
                            <div class="mt-0">
                                <label class="custom-top-label">{{ __('Enter Reason') }}</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('Enter Reason') }}"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="button-group text-center mt-4">
                                <button type="button" class="theme-btn border-btn m-2" data-bs-dismiss="modal" aria-label="Close">{{ __('Close') }}</button>
                                <button class="theme-btn m-2 submit-btn">{{ __('Accept') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
