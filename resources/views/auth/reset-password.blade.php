@extends('layouts.auth.app', [
    'title' => __('Reset Password')
])

@section('main_content')
<div class="footer">
    <div class="footer-logo w-100  d-flex align-items-center justify-content-center">
        <img src="{{ asset(get_option('login-page')['login_page_icon'] ?? 'assets/images/login/login-logo.svg') ?? '' }}" alt="Logo">
    </div>
    <div class="mybazar-login-section">
        <div class="mybazar-login-wrapper">
            <div class="login-wrapper">
                <div class="login-header">
                    <h4>{{ get_option('general')['name'] ?? '' }}</h4>
                </div>


                <div class="login-body w-100">
                    <h2>{{ __('Change Password') }}</h2>
                    <h6>{{ __('Create new password, it must be Strong password.') }}</h6>
                    <form action="{{ route('password.store') }}" method="post" class="ajaxform_instant_reload">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <input type="hidden" name="email" value="{{ $request->email }}">

                        <div class="input-group">
                            <span><img src="{{ asset('assets/images/icons/lock.png') }}" alt="img"></span>
                            <span class="hide-pass">
                                <img src="{{ asset('assets/images/icons/Hide.svg') }}" alt="img">
                                <img src="{{ asset('assets/images/icons/show.svg') }}" alt="img">
                            </span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="{{ __('New Password') }}">
                        </div>
                        <div class="input-group">
                            <span><img src="{{ asset('assets/images/icons/lock.png') }}" alt="img"></span>
                            <span class="hide-pass">
                                <img src="{{ asset('assets/images/icons/Hide.svg') }}" alt="img">
                                <img src="{{ asset('assets/images/icons/show.svg') }}" alt="img">
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="{{ __('Confirm Password') }}">
                        </div>
                        <button type="submit" class="btn login-btn submit-btn">{{ __('Continue') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <img class="position-absolute illistration-img1  bottom-0"
            src="{{ asset('assets/images/login/loginillustrator2.svg') }}" alt="" srcset="">
        <img class="position-absolute illistration-img2 bottom-0 "
            src="{{ asset('assets/images/login/loginillustrator1.svg') }}" alt="" srcset="">
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
