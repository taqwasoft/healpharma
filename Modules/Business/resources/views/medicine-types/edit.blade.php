<div class="modal fade common-validation-modal" id="medicine-types-edit-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Edit Medicine Type') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <form action="" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload" id="medicineTypeUpdateForm">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="custom-top-label required">{{ __('Name') }}</label>
                                <input type="text" name="name" id="medicineTypeName" required placeholder="{{ __('Enter Name') }}" class="form-control" />
                            </div>
                        </div>

                        <div class="offcanvas-footer mt-3">
                            <div class="button-group text-center mt-5">
                                <a href="{{ route('business.medicine-types.index') }}" class="theme-btn border-btn m-2">{{ __('Cancel') }}</a>
                                <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
