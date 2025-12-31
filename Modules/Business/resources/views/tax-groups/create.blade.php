@extends('business::layouts.master')

@section('title')
    {{ __('Tax Group') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endpush

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="table-header p-16">
                    <h4>{{ __('Add New Tax Group') }}</h4>
                    <div>
                        <a href="{{ route('business.taxes.index') }}" class="theme-btn print-btn text-light active"><i class="fas fa-list me-1"></i>{{ __('Tax Group List') }}</a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="order-form-section p-16">
                        {{-- form start --}}
                        <form action="{{ route('business.taxes.store') }}" method="post" enctype="multipart/form-data"
                        class="ajaxform_instant_reload">
                        @csrf

                        <div class="add-suplier-modal-wrapper">
                            <div class="row">
                                <div class="col-lg-6 mt-2">
                                    <label>{{ __('Tax Group Name') }}</label>
                                    <input type="text" name="name" id="name" required class="form-control"
                                        placeholder="{{ __('Enter Name') }}">
                                </div>


                                <div class="col-md-6 mt-2">
                                    <label>{{ __('Select taxes') }}</label>
                                    <div class="input-group">
                                        <select id="sub_tax" name="tax_ids[]" class="form-control" multiple>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-2 col-lg-6">
                                    <label class="custom-top-label">{{ __('Status') }}</label>
                                    <div class="gpt-up-down-arrow position-relative">
                                        <select class="form-control form-selected" name="status">
                                            <option value="1">{{ __('Active') }}</option>
                                            <option value="0">{{ __('Deactive') }}</option>
                                        </select>
                                        <span></span>
                                    </div>
                                </div>

                                <div class="offcanvas-footer mt-3 d-flex justify-content-center">
                                    <a href="{{ route('business.taxes.index') }}" class="cancel-btn btn btn-outline-danger">{{ __('Cancel') }}</a>
                                    <button class="submit-btn btn btn-primary cus-save-btn  text-white" type="submit">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script>
        $('#sub_tax').select2({
            placeholder: 'Select taxes',
        });
    </script>
@endpush
