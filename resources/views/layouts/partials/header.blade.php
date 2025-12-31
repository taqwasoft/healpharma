<div class="header-bg sticky-top">
    <header class="main-header-section ">
        <div class="header-wrapper">
            <div class="header-left">
                <div class="sidebar-opner me-3"><i class="fal fa-bars" aria-hidden="true"></i></div>
                <a target="_blank" class=" view-website" href="{{ route('home') }}">
                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C6.47715 22 2 17.5228 2 12C2 9.20746 3.14465 6.68227 4.99037 4.86802M12 22C11.037 21.2864 11.1907 20.4555 11.6738 19.6247C12.4166 18.3474 12.4166 18.3474 12.4166 16.6444C12.4166 14.9414 13.4286 14.1429 17 14.8571C18.6047 15.1781 19.7741 12.9609 21.8573 13.693M12 22C16.9458 22 21.053 18.4096 21.8573 13.693M4.99037 4.86802C5.83966 4.95765 6.31517 5.41264 7.10496 6.24716C8.6044 7.83152 10.1038 7.96372 11.1035 7.4356C12.6029 6.64343 11.3429 5.3603 13.1027 4.66298C14.1816 4.23551 14.3872 3.11599 13.8766 2.17579M4.99037 4.86802C6.79495 3.09421 9.26969 2 12 2C12.6414 2 13.2687 2.06039 13.8766 2.17579M21.8573 13.693C21.9511 13.1427 22 12.5771 22 12C22 7.11857 18.5024 3.05405 13.8766 2.17579" stroke="#fff" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="header-middle"></div>
            <div class="header-right ">
                <div class="language-change admin-lang p-0">

                    <div class="dropdown">
                        <button class="btn language-dropdown p-0 dropdown-toggle language-btn border-0" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}" alt="" class="flag-icon ">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-scroll">
                            @foreach (languages() as $key => $language)
                                <li class="language-li">
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}">
                                        <div class="language-img-container">
                                            <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt=""
                                                class="flag-icon me-2">
                                            {{ $language['name'] }}
                                        </div>
                                    </a>
                                    @if (app()->getLocale() == $key)
                                        <i class="fas fa-check language-check"></i>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                 @if (auth()->user()->role == 'superadmin')
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
                                <a href="{{ route('admin.notifications.mtReadAll') }}" class="text-red">{{ __('Mark all Read') }}</a>
                            </div>

                            <ul>
                                @foreach (auth()->user()->unreadNotifications as $notification)
                                    <li>
                                        <a href="{{ route('admin.notifications.mtView', $notification->id) }}">
                                            <strong>{{ __($notification->data['message'] ?? 'No message') }}</strong>
                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="notification-footer">
                                <a class="text-red" href="{{ route('admin.notifications.index') }}">
                                    {{ __('View all notifications') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif


                <div class="profile-info dropdown d-flex align-items-center justify-content-center">
                    <a href="#" data-bs-toggle="dropdown" class="d-flex align-items-center justify-content-center gap-2">
                        <img src="{{ asset(Auth::user()->image ?? 'assets/images/icons/default-user.png') }}"
                            alt="Profile">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <p class="text-white admin-name">{{ Str::limit(Auth::user()->name, 15, '.') }}</p>
                            <i class="fas fa-chevron-down profile-arrow text-white"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('cache-clear') }}"> <i class="far fa-undo"></i> {{ __('Clear cache') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profiles.index') }}"> <i class="fal fa-user"></i>
                                {{ __('My Profile') }}
                            </a>
                        </li>
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
        </div>
    </header>
</div>
