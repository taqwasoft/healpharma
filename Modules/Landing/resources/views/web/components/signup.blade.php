<div class="modal fade" id="registration-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Create a') }} <span id="subscription_name"> {{ __('Free') }}</span>
                    {{ __('account') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <form id="registration-form" action="{{ route('register') }}" method="post"
                        enctype="multipart/form-data" class="add-brand-form pt-0 sign_up_form">
                        @csrf
                        <div class="row">
                            <div class="mt-3 col-lg-6">
                                <label class="custom-top-label">{{ __('Company/Business Name') }}</label>
                                <input type="text" name="companyName"
                                    placeholder="{{ __('Enter business/store Name') }}" class="form-control" required />
                            </div>
                            <div class="mt-3 col-lg-6">
                                <label class="custom-top-label">{{ __('Business Category') }}</label>
                                <div class="gpt-up-down-arrow position-relative">
                                    <select name="business_category_id"
                                        class="form-control form-selected business_category" required>
                                        @foreach ($business_categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span></span>
                                </div>
                            </div>

                            <div class="mt-3 col-lg-6">
                                <label class="custom-top-label">{{ __('Phone') }}</label>
                                <input type="number" name="phoneNumber" placeholder="{{ __('Enter Phone Number') }}"
                                    class="form-control" required />
                            </div>
                            <div class="mt-3 col-lg-6">
                                <label class="custom-top-label">{{ __('Email Address') }}</label>
                                <input type="email" name="email" placeholder="{{ __('Enter Email Address') }}"
                                    class="form-control" required />
                            </div>
                            <div class="mt-3 col-lg-6">
                                <label class="custom-top-label">{{ __('Password') }}</label>
                                <input type="password" name="password" placeholder="{{ __('Enter Password') }}"
                                    class="form-control" required />
                            </div>
                            <div class="mt-3 col-lg-6">
                                <label class="custom-top-label">{{ __('Company Address') }}</label>
                                <input type="text" name="address" placeholder="{{ __('Enter Company Address') }}"
                                    class="form-control" />
                            </div>
                            <div class="mt-3 col-lg-12">
                                <label class="custom-top-label">{{ __('Opening Balance') }}</label>
                                <input type="number" name="shopOpeningBalance"
                                    placeholder="{{ __('Enter Opening Balance') }}" class="form-control" />
                            </div>
                        </div>

                        <div class="offcanvas-footer mt-3 d-flex justify-content-center gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancel-btn btn btn-outline-danger"
                                data-bs-dismiss="offcanvas" aria-label="Close">
                                {{ __('Close') }}
                            </button>
                            <button class="ps-custom-btn py-2 px-4 submit-btn btn" type="submit">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Verify Modal Start -->
<div class="modal fade" id="verifymodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content verify-content">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body verify-modal-body  text-center">

                <h4 class="mb-0 verification-title">{{ __('Email Verification') }}</h4>
                <p class="des p-8-0 pb-3">{{ __('we sent an OTP in your email address') }} <br>
                    <span id="dynamicEmail"></span>
                </p>
                <form action="{{ route('otp-submit') }}" method="post" class="verify_form">
                    @csrf
                    <div class="code-input pin-container">
                        <input class="pin-input otp-input" id="pin-1" type="number" name="otp[]" maxlength="1">
                        <input class="pin-input otp-input" id="pin-2" type="number" name="otp[]" maxlength="1">
                        <input class="pin-input otp-input" id="pin-3" type="number" name="otp[]" maxlength="1">
                        <input class="pin-input otp-input" id="pin-4" type="number" name="otp[]" maxlength="1">
                        <input class="pin-input otp-input" id="pin-5" type="number" name="otp[]" maxlength="1">
                        <input class="pin-input otp-input" id="pin-6" type="number" name="otp[]" maxlength="1">
                    </div>

                    <p class="des p-24-0 pt-2">
                        {{ __('Code send in') }} <span id="countdown" class="countdown"></span>
                        <span class="reset text-primary cursor-pointer" id="otp-resend" data-route="{{ route('otp-resend') }}">{{ __('Resend code') }}</span>
                    </p>
                    <button class="verify-btn btn submit-btn ps-custom-btn mt-2">{{ __('Verify') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Verify Modal end -->

<!-- success Modal Start -->
<div class="modal fade" id="successmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content success-content">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body success-modal-body text-center">
                <div>
                    <img src="{{ asset(get_option('general')['common_header_logo'] ?? 'assets/img/icon/success-icon.svg') }}" alt="">
                    <h4>{{ __('Successfully!') }}</h4>
                    <p class="mb-3">{{ __('Congratulations, Your account has been') }} <br> {{ __('successfully created') }}</p>
                    <a href="{{ get_option('general')['app_link'] ?? '' }}" target="_blank" class="download-btn mb-4">{{ __('Download Apk') }} <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_3367_6659)">
                        <path d="M6.05718 8.26462L10.1305 13.6512C10.4134 14.0252 10.9753 14.0252 11.2581 13.6512L15.3314 8.26462C15.6836 7.799 15.3514 7.13138 14.7676 7.13138H13.1943V1.62029C13.1943 1.53098 13.1588 1.44534 13.0956 1.38219C13.0325 1.31904 12.9468 1.28357 12.8575 1.28357H8.53109C8.44179 1.28357 8.35614 1.31904 8.29299 1.38219C8.22985 1.44534 8.19437 1.53098 8.19437 1.62029V7.13142H6.62101C6.03722 7.13138 5.70507 7.799 6.05718 8.26462Z" fill="white"/>
                        <path d="M20.5 13.3248V17.5663C20.5 18.2014 19.9851 18.7163 19.35 18.7163H1.65C1.01488 18.7163 0.5 18.2014 0.5 17.5663V13.3248C0.5 12.6897 1.01488 12.1748 1.65 12.1748H5.54746C5.78399 12.1748 6.01476 12.2477 6.20834 12.3837C6.40191 12.5196 6.54887 12.7119 6.62918 12.9344L6.91086 13.7148C6.99116 13.9372 7.13812 14.1296 7.33169 14.2655C7.52527 14.4014 7.75605 14.4743 7.99258 14.4743H13.0075C13.2441 14.4743 13.4748 14.4014 13.6684 14.2655C13.862 14.1295 14.0089 13.9372 14.0893 13.7148L14.3709 12.9344C14.4512 12.7119 14.5982 12.5196 14.7918 12.3837C14.9853 12.2477 15.2161 12.1748 15.4527 12.1748H19.3501C19.9851 12.1748 20.5 12.6897 20.5 13.3248Z" fill="white"/>
                        <path d="M0.5 15.8559V17.5664C0.5 18.2015 1.01488 18.7164 1.65 18.7164H19.35C19.9851 18.7164 20.5 18.2015 20.5 17.5664V15.8559H0.5Z" fill="white"/>
                        <path d="M15.4364 15.7657C15.4119 15.7321 15.3798 15.7047 15.3427 15.6858C15.3056 15.667 15.2645 15.6572 15.2229 15.6572H5.77708C5.61778 15.6572 5.49462 15.797 5.51458 15.955L5.66161 17.1192C5.67102 17.1936 5.71137 17.2567 5.76805 17.2984C5.77829 17.3126 6.5938 18.127 7.18446 18.7164H18.387L15.4364 15.7657Z" fill="#08A48E" fill-opacity="0.15"/>
                        <path d="M15.0758 17.3506H5.92411C5.85968 17.3506 5.79747 17.3271 5.74914 17.2845C5.70082 17.2419 5.66969 17.1831 5.66161 17.1192L5.51458 15.955C5.49462 15.7969 5.61778 15.6572 5.77708 15.6572H15.2229C15.3822 15.6572 15.5054 15.797 15.4854 15.955L15.3384 17.1192C15.3303 17.1831 15.2992 17.2419 15.2508 17.2845C15.2025 17.3271 15.1403 17.3506 15.0758 17.3506Z" fill="#08A48E"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_3367_6659">
                        <rect width="20" height="20" fill="white" transform="translate(0.5)"/>
                        </clipPath>
                        </defs>
                        </svg>
                        </a>
                </div>
            </div>

        </div>
    </div>
</div>
<!--success Modal end -->
