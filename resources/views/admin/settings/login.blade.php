@extends('layouts.master')

@section('title')
    {{ __('Login Page Settings') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('Login Page Settings') }}</h4>
                    </div>
                    <div class="order-form-section p-16">
                        <form action="{{ route('admin.login-pages.update', $login_page->id ?? '') }}" method="post"
                            enctype="multipart/form-data" class="ajaxform_instant_reload">
                            @csrf
                            @method('put')
                            <div class="add-suplier-modal-wrapper d-block">
                                <div class="row">

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Login Page Icon') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($login_page->value['login_page_icon'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="login_page_icon">
                                                </div>
                                                <input type="file" name="login_page_icon" class="d-none" accept="image/*" onchange="document.getElementById('login_page_icon').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>


                                        <div class="col-lg-12">
                                            <div class="text-center mt-5">
                                                <button type="submit" class="theme-btn m-2 submit-btn">{{ __('Update') }}</button>
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
