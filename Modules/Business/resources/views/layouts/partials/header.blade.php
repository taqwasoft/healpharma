<header class="main-header-section sticky-top">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="sidebar-opner menu-opener lg-device-menu"><i class="fal fa-bars" aria-hidden="true"></i></div>

             @if (request()->is('business/purchases/create'))
                <a class="bulk-upload-btn" data-bs-toggle="modal" data-bs-target="#bulk-upload-modal" href="{{ route('business.bulk-uploads.index') }}" class="bulk-upload-btn d-flex align-items-center gap-1">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.25 2.08301H3.75004C2.82957 2.08301 2.08337 2.8292 2.08337 3.74967V16.2497C2.08337 17.1702 2.82957 17.9163 3.75004 17.9163H16.25C17.1705 17.9163 17.9167 17.1702 17.9167 16.2497V3.74967C17.9167 2.8292 17.1705 2.08301 16.25 2.08301Z" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.08337 14.167H12.9167" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.9167 8.74967L10 5.83301L7.08337 8.74967M10 6.66634V11.6663" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('Bulk Upload') }}
                </a>
            @endif
            <div class="sm-device-logo">
                <a href="{{ route('business.dashboard.index') }}">
                    <img src="{{ asset(get_option('general')['admin_logo'] ?? 'assets/images/logo/backend_logo.png') }}"
                        alt="Logo">
                </a>
            </div>
        </div>
        <div class="header-middle"></div>
        <div class="header-right">
            <div class="notification-wrapper" id="notificationWrapper">
                <div class="custom-bell-icon" id="toggleNotification">
                    <img src="{{ asset('assets/images/icons/bel.svg') }}" alt="Notifications">
                    <span class="bg-red">{{ auth()->user()->unreadNotifications->count() }}</span>
                </div>

                <div class="notification-container" id="notificationContainer">
                    <div class="notification-header flex-wrap">
                        <p>
                            {{ __('You Have') }}
                            <strong>{{ auth()->user()->unreadNotifications->count() }}</strong>
                            {{ __('new Notifications') }}
                        </p>
                        <a href="{{ route('business.notifications.mtReadAll') }}" class="text-red">Mark all Read</a>
                    </div>

                    <ul>
                        @foreach (auth()->user()->unreadNotifications as $notification)
                            <li>
                                <a href="{{ route('business.notifications.mtView', $notification->id) }}">
                                    <strong>{{ __($notification->data['message'] ?? 'No message') }}</strong>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="notification-footer">
                        <a class="text-red" href="{{ route('business.notifications.index') }}">View all
                            notifications</a>
                    </div>
                </div>
            </div>

            <div class="language-change">
                <div class="dropdown">
                    <button class=" language-btn  dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}"
                            alt="" class="flag-icon">

                    </button>
                    <ul class="dropdown-menu dropdown-menu-scroll">
                        @foreach (languages() as $key => $language)
                            <li class="language-li">
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}">
                                    <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt=""
                                        class="flag-icon me-2">
                                    {{ $language['name'] }}
                                </a>
                                @if (app()->getLocale() == $key)
                                    <i class="fas fa-check language-check"></i>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>


            <div class="d-flex align-items-center justify-content-center profile-menu">
                <div class="profile-info dropdown">
                    <a href="#" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center justify-content-center gap-2 business-profile">
                            @php
                                $profileImage = auth()->user()->image ?: 'assets/images/icons/default-user.png';
                            @endphp

                            <img src="{{ asset($profileImage) }}" alt="Profile">

                            <div class="greet-name">
                                <h6 class="nav-name">
                                    {{ auth()->user()->name }}
                                </h6>
                            </div>
                            <svg class="profile-arrow" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.81063 6.75H13.1896C13.3379 6.75003 13.4829 6.79404 13.6062 6.87645C13.7295 6.95886 13.8256 7.07598 13.8824 7.21301C13.9391 7.35003 13.954 7.50081 13.9251 7.64627C13.8961 7.79174 13.8247 7.92536 13.7199 8.03025L9.53038 12.2197C9.38974 12.3603 9.199 12.4393 9.00013 12.4393C8.80126 12.4393 8.61053 12.3603 8.46988 12.2197L4.28038 8.03025C4.17552 7.92536 4.10412 7.79174 4.07519 7.64627C4.04627 7.50081 4.06112 7.35003 4.11787 7.21301C4.17463 7.07598 4.27073 6.95886 4.39404 6.87645C4.51734 6.79404 4.66232 6.75003 4.81063 6.75Z"
                                    fill="white" />
                            </svg>

                        </div>
                    </a>
                    <div class=" business-profile bg-success">

                        <ul class="dropdown-menu">
                            <li> <a href="{{ route('business.profiles.index') }}"> <i class="fal fa-user"></i>
                                    {{ __('My Profile') }}</a></li>
                            <li>
                                <a href="javascript:void(0)" class="logoutButton">
                                    <i class="far fa-sign-out"></i> {{ __('Logout') }}
                                    <form action="{{ route('logout') }}" method="post" id="logoutForm">
                                        @csrf
                                    </form>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="sidebar-opner menu-opener sm-device-menu"><i class="fal fa-bars" aria-hidden="true"></i>
                </div>

            </div>
        </div>
    </div>
</header>

@push('modal')
    @include('business::purchases.bulk-upload.index')
@endpush
