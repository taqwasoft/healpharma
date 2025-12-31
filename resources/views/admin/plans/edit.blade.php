@extends('layouts.master')

@section('title')
    {{ __('Edit Subscription Plan') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card border-0">
                <div class="card-bodys shadow-sm">
                    <div class="table-header p-16">
                        <h4>{{__('Edit Package')}}</h4>
                        @can('plans-read')
                            <a href="{{ route('admin.plans.index') }}" class="add-order-btn rounded-2"><i class="far fa-list" aria-hidden="true"></i> {{ __('Package List') }}</a>
                        @endcan
                    </div>
                    <div class="order-form-section p-16">
                        <form action="{{ route('admin.plans.update',$plan->id) }}" method="POST" class="ajaxform_instant_reload">
                            @csrf
                            @method('put')
                            <div class="add-suplier-modal-wrapper d-block">
                                <div class="row">
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Package Name') }}</label>
                                        <input value="{{$plan->subscriptionName}}" type="text" name="subscriptionName" required class="form-control" placeholder="{{ __('Enter Package Name') }}">
                                    </div>

                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Duration in Days') }}</label>
                                        <input value="{{$plan->duration}}" type="number" step="any" name="duration" required class="form-control" placeholder="{{ __('Enter Duration Days') }}">
                                    </div>

                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Offer Price') }}</label>
                                        <input value="{{$plan->offerPrice}}" type="number" step="any" name="offerPrice" class="form-control price" placeholder="{{ __('Enter Plan Price') }}">
                                    </div>

                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Subscription Price') }}</label>
                                        <input value="{{$plan->subscriptionPrice}}" type="number" step="any" name="subscriptionPrice" required class="form-control" placeholder="{{ __('Enter Subscription Price') }}">
                                    </div>

                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Status') }}</label>
                                        <div class="form-control d-flex justify-content-between align-items-center radio-switcher">
                                            <p class="dynamic-text">{{ $plan->status == 1 ? 'Active' : 'Deactive' }}</p>
                                            <label class="switch m-0">
                                                <input type="checkbox" name="status" class="change-text" {{ $plan->status == 1 ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label>{{ __('Add New Features') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control add-feature border-0 bg-transparent" placeholder="{{ __('Enter features') }}">
                                            <button class="feature-btn" id="feature-btn">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="row feature-list">
                                            @foreach ($plan->features ?? [] as $key => $item)
                                            <div class="col-lg-6 mt-4 feature-item">
                                                <div class="form-control manage-plan d-flex justify-content-between align-items-center position-relative">
                                                    <input name="features[features_{{ $key }}][]" required class="form-control subscription-plan-edit-custom-input" type="text" value="{{ $item[0] ?? '' }}">
                                                  <div class="custom-manageswitch">
                                                    <div class="d-flex align-items-center gap-4">
                                                    <label class="switch m-0">
                                                        <input type="checkbox" name="features[features_{{ $key }}][]" @checked(isset($item[1])) value="1">
                                                        <span class="slider round"></span>
                                                    </label>

                                                    <svg class="delete-feature" width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M2.5 5.58342H17.5M13.3796 5.58342L12.8107 4.40986C12.4328 3.6303 12.2438 3.24051 11.9179 2.99742C11.8457 2.9435 11.7691 2.89553 11.689 2.854C11.3281 2.66675 10.8949 2.66675 10.0286 2.66675C9.1405 2.66675 8.6965 2.66675 8.32957 2.86185C8.24826 2.90509 8.17066 2.955 8.09758 3.01106C7.76787 3.264 7.5837 3.66804 7.21535 4.47613L6.71061 5.58342" stroke="#F54336" stroke-width="1.5" stroke-linecap="round" />
                                                            <path  d="M16.25 5.5835L15.7336 13.9377C15.6016 16.0722 15.5357 17.1394 15.0007 17.9067C14.7361 18.2861 14.3956 18.6062 14.0006 18.8468C13.2017 19.3335 12.1325 19.3335 9.99392 19.3335C7.8526 19.3335 6.78192 19.3335 5.98254 18.8459C5.58733 18.6049 5.24667 18.2842 4.98223 17.9042C4.4474 17.1357 4.38287 16.0669 4.25384 13.9295L3.75 5.5835" stroke="#F54336" stroke-width="1.5" stroke-linecap="round" />
                                                            <path d="M7.5 10.7717H12.5" stroke="#F54336" stroke-width="1.5" stroke-linecap="round" />
                                                            <path d="M8.75 14.1467H11.25" stroke="#F54336" stroke-width="1.5" stroke-linecap="round" />
                                                    </svg>
                                                    </div>

                                                  </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="button-group text-center mt-5">
                                            <a href="{{ route('admin.plans.index') }}" class="theme-btn border-btn m-2">{{ __('Cancel') }}</a>
                                            <button class="theme-btn m-2 submit-btn">{{ __('Update') }}</button>
                                        </div>
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
    <script src="{{ asset('assets/js/custom/custom.js') }}"></script>
@endpush
