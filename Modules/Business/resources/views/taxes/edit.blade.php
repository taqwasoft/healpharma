<div class="modal fade" id="tax-edit-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header ">
                <h1 class="modal-title fs-5">{{ __('Edit Tax') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="personal-info">
                    <form action="" method="post" enctype="multipart/form-data" class="add-brand-form pt-0 ajaxform_instant_reload updateTaxForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mt-2 col-lg-12">
                                <label class="custom-top-label">{{ __('Name') }}</label>
                                <input type="text" name="name" id="tax_name" placeholder="{{ __('Enter Name') }}" class="form-control" />
                            </div>

                            <div class="col-lg-12 mt-2">
                                <label class="custom-top-label">{{ __('Rate') }}</label>
                                <input type="number" name="rate" id="new_tax_rate" required class="form-control" placeholder="{{ __('Enter rate - %') }}">
                            </div>
                            <div class="mt-2 col-lg-12">
                                <label class="custom-top-label">{{ __('Status') }}</label>
                                <div class="gpt-up-down-arrow position-relative">
                                    <select class="form-control form-selected" name="status" id="tax_status">
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Deactive') }}</option>
                                    </select>
                                    <span></span>
                                </div>
                            </div>
                        </div>

                        <div class="offcanvas-footer mt-5 mb-3 d-flex align-items-center justify-content-center">
                            <button type="button" data-bs-dismiss="modal" class="cancel-btn btn btn-outline-danger" data-bs-dismiss="offcanvas" aria-label="Close">{{ __('Cancel') }}</button>
                            <button class="submit-btn btn btn-primary cus-save-btn text-white" type="submit">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
