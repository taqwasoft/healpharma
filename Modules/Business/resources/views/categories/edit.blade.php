<div class="modal fade common-validation-modal" id="category-edit-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Edit Category') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <form action="" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload categoryEditForm">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="mt-3 col-lg-12">
                                <label class="custom-top-label required">{{ __('Name') }}</label>
                                <input type="text" name="categoryName" id="category_name" required placeholder="{{ __('Enter Category Name') }}" class="form-control" />
                            </div>
                            <div class="col-lg-12 mt-3">
                                <label>{{ __('Description') }}</label>
                                <textarea name="description" class="form-control" id="category_description" placeholder="{{ __('Enter Description') }}"></textarea>
                            </div>
                        </div>
                        <div class="offcanvas-footer mt-3">
                            <div class="button-group text-center mt-5">
                                <a href="{{ route('business.categories.index') }}" class="theme-btn border-btn m-2">{{ __('Cancel') }}</a>
                                <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
