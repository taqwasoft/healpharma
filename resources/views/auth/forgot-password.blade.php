@extends('layouts.auth.app', [
    'title' => __('Forget Password')
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
                    <h2 class="text-center">{{ __('Forgot ') }} <span>{{ __('Password') }}</span></h2>
                    <h6>{{ __('Enter the email address associated with your account') }}</h6>
                    <form method="POST" action="{{ route('password.email') }}" class="ajaxform">
                        @csrf
                        <div class="input-group">
                            <span><img src="{{ asset('assets/images/icons/email-icon.svg') }}" alt="img"></span>
                            <input type="email" name="email" class="form-control" placeholder="{{ __('Enter your Email') }}">
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

