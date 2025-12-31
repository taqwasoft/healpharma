<nav class="side-bar">
    <div class="side-bar-logo sticky-top">
        <a href="{{ route('business.dashboard.index') }}" class="d-flex align-items-center">
            {{-- <img src="{{ asset(get_option('general')['admin_logo'] ?? 'assets/images/logo/backend_logo.png') }}"
                alt="Logo"> --}}
            <h2 class="title" style="font-size: 1.5rem; font-weight: bold; color: #fff; margin: 0; white-space: nowrap;">Heal Pharma</h2>
            <h2 class="logo-collapsed" style="font-size: 1.5rem; font-weight: bold; color: #fff; margin: 0; display: none;">HP</h2>
        </a>
        <button class="close-btn"><i class="fal fa-times"></i></button>
    </div>
    <div class="side-bar-manu">
        <ul>

            <li class="{{ Request::routeIs('business.dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('business.dashboard.index') }}" class="active">
                    <span class="sidebar-icon">
                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_4092_7102)">
                        <path d="M12.5001 14.1665C11.8339 14.6852 10.9586 14.9998 10.0001 14.9998C9.04156 14.9998 8.1664 14.6852 7.50012 14.1665" stroke="#00987F" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M1.9597 11.0111C1.66551 9.09667 1.51842 8.13953 1.88035 7.29099C2.24226 6.44244 3.04523 5.86186 4.65115 4.70072L5.85103 3.83317C7.84878 2.38873 8.84764 1.6665 10.0002 1.6665C11.1527 1.6665 12.1516 2.38873 14.1494 3.83317L15.3492 4.70072C16.9552 5.86186 17.7581 6.44244 18.1201 7.29099C18.482 8.13953 18.3349 9.09667 18.0407 11.0111L17.7899 12.6435C17.3728 15.3573 17.1643 16.7142 16.1911 17.5237C15.2178 18.3332 13.7949 18.3332 10.9492 18.3332H9.05122C6.20549 18.3332 4.78263 18.3332 3.80937 17.5237C2.83611 16.7142 2.6276 15.3573 2.21056 12.6435L1.9597 11.0111Z" stroke="#00987F" stroke-width="1.5" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_4092_7102">
                        <rect width="20" height="20" fill="white"/>
                        </clipPath>
                        </defs>
                        </svg>
                    </span>
                    {{ __('Dashboard') }}
                </a>
            </li>

            @if (auth()->user()->role != 'staff' ||
                    visible_permission('salePermission') ||
                    visible_permission('salesListPermission'))
                <li
                    class="dropdown {{ Request::routeIs('business.sales.index', 'business.sales.create', 'business.sales.edit', 'business.sale-returns.create', 'business.sale-returns.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.5834 4.1665C15.2737 4.1665 15.8334 4.72615 15.8334 5.4165C15.8334 6.10686 15.2737 6.6665 14.5834 6.6665C13.893 6.6665 13.3334 6.10686 13.3334 5.4165C13.3334 4.72615 13.893 4.1665 14.5834 4.1665Z" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.31182 9.28642C1.47586 10.2201 1.45788 11.6287 2.2251 12.6196C3.74755 14.5859 5.41391 16.2523 7.38024 17.7747C8.37113 18.5419 9.77971 18.5239 10.7134 17.688C13.2482 15.4183 15.5695 13.0464 17.8099 10.4398C18.0314 10.1821 18.1699 9.86625 18.201 9.52784C18.3385 8.03149 18.621 3.7204 17.4502 2.54962C16.2794 1.37885 11.9683 1.66131 10.472 1.7988C10.1335 1.8299 9.81771 1.96845 9.55996 2.18993C6.9534 4.43022 4.58147 6.7516 2.31182 9.28642Z" stroke="white" stroke-width="1.3"/>
                            <path d="M11.4904 10.3053C11.5081 9.97112 11.6019 9.35979 11.0938 8.8952M11.0938 8.8952C10.9365 8.75145 10.7217 8.6217 10.4291 8.51854C9.38196 8.14949 8.09574 9.38479 9.00563 10.5155C9.49471 11.1233 9.87179 11.3103 9.83629 12.0005C9.81129 12.486 9.33438 12.9933 8.70579 13.1865C8.15971 13.3544 7.55734 13.1321 7.17634 12.7064C6.71114 12.1866 6.75813 11.6965 6.75414 11.483M11.0938 8.8952L11.6672 8.32178M7.2178 12.7712L6.67322 13.3158" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Sales') }}</a>
                    <ul>
                        @if (auth()->user()->role != 'staff' || visible_permission('salePermission'))
                            <li><a class="{{ Request::routeIs('business.sales.create') ? 'active' : '' }}"
                                    href="{{ route('business.sales.create') }}">{{ __('Sale New') }}</a></li>
                        @endif
                        @if (auth()->user()->role != 'staff' || visible_permission('salesListPermission'))
                            <li><a class="{{ Request::routeIs('business.sales.index', 'business.sale-returns.create') ? 'active' : '' }}" href="{{ route('business.sales.index') }}">{{ __('Sale List') }}</a></li>
                            <li><a class="{{ Request::routeIs('business.sale-returns.index') ? 'active' : '' }}" href="{{ route('business.sale-returns.index') }}">{{ __('Sales Return') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role != 'staff' ||
                    visible_permission('purchasePermission') ||
                    visible_permission('purchaseListPermission'))
                <li class="dropdown {{ Request::routeIs('business.purchases.index', 'business.purchases.create', 'business.purchases.edit', 'business.purchase-returns.create', 'business.purchase-returns.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 1.6665H3.5843C4.57227 1.6665 5.06625 1.6665 5.40117 1.97602C5.73609 2.28555 5.80137 2.8024 5.93195 3.8361L6.87144 11.2738C7.04553 12.652 7.13258 13.3411 7.57914 13.7538C8.02571 14.1665 8.68433 14.1665 10.0017 14.1665H18.3333" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            <path d="M9.58337 17.5C10.2737 17.5 10.8334 16.9404 10.8334 16.25C10.8334 15.5596 10.2737 15 9.58337 15C8.89302 15 8.33337 15.5596 8.33337 16.25C8.33337 16.9404 8.89302 17.5 9.58337 17.5Z" stroke="white" stroke-width="1.3"/>
                            <path d="M15.4166 17.5C16.107 17.5 16.6666 16.9404 16.6666 16.25C16.6666 15.5596 16.107 15 15.4166 15C14.7263 15 14.1666 15.5596 14.1666 16.25C14.1666 16.9404 14.7263 17.5 15.4166 17.5Z" stroke="white" stroke-width="1.3"/>
                            <path d="M15 11.6668H13.3333C11.762 11.6668 10.9763 11.6668 10.4882 11.1787C10 10.6905 10 9.90483 10 8.3335V6.66683C10 5.09548 10 4.3098 10.4882 3.82165C10.9763 3.3335 11.762 3.3335 13.3333 3.3335H15C16.5713 3.3335 17.357 3.3335 17.8452 3.82165C18.3333 4.3098 18.3333 5.09548 18.3333 6.66683V8.3335C18.3333 9.90483 18.3333 10.6905 17.8452 11.1787C17.357 11.6668 16.5713 11.6668 15 11.6668Z" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.75 5.8335H14.5833" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Purchases') }}</a>
                    <ul>
                        @if (auth()->user()->role != 'staff' || visible_permission('purchasePermission'))
                            <li><a class="{{ Request::routeIs('business.purchases.create') ? 'active' : '' }}"
                                    href="{{ route('business.purchases.create') }}">{{ __('Purchase New') }}</a></li>
                        @endif
                        @if (auth()->user()->role != 'staff' || visible_permission('purchaseListPermission'))
                            <li><a class="{{ Request::routeIs('business.purchases.index', 'business.purchase-returns.create') ? 'active' : '' }}"
                                    href="{{ route('business.purchases.index') }}">{{ __('Purchase List') }}</a></li>
                            <li><a class="{{ Request::routeIs('business.purchase-returns.index') ? 'active' : '' }}"
                                    href="{{ route('business.purchase-returns.index') }}">{{ __('Purchase Return') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            @if (auth()->user()->role != 'staff' || visible_permission('productPermission'))
                <li class="dropdown {{ Request::routeIs('business.products.index', 'business.products.create', 'business.products.edit', 'business.expired-products.index', 'business.categories.index', 'business.units.index', 'business.barcodes.index', 'business.medicine-types.index', 'business.manufacturers.index', 'business.box-sizes.index','business.bulk-uploads.index', 'business.products.show') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.55708 1.6665H12.443C13.2108 1.6665 13.5946 1.6665 13.8331 1.91058C14.2779 2.36573 14.2779 4.30061 13.8331 4.75576C13.5946 4.99984 13.2108 4.99984 12.443 4.99984H7.55708C6.78933 4.99984 6.40546 4.99984 6.16695 4.75576C5.72218 4.30061 5.72218 2.36573 6.16695 1.91058C6.40546 1.6665 6.78933 1.6665 7.55708 1.6665Z" stroke="white" stroke-width="1.5"/>
                            <path d="M6.66667 5C6.80411 5.27488 6.87284 5.41234 6.92172 5.54642C7.17676 6.24596 7.10671 7.02277 6.73065 7.6654C6.65858 7.78857 6.56637 7.91151 6.38197 8.15737L6.04577 8.60567C5.67072 9.10575 5.48319 9.35575 5.34728 9.62967C5.21 9.90642 5.11184 10.2008 5.05564 10.5046C5 10.8053 5 11.1178 5 11.7429V13.3333C5 15.6903 5 16.8688 5.73223 17.6011C6.46447 18.3333 7.64297 18.3333 10 18.3333C12.357 18.3333 13.5355 18.3333 14.2677 17.6011C15 16.8688 15 15.6903 15 13.3333V11.7429C15 11.1178 15 10.8053 14.9443 10.5046C14.8882 10.2008 14.79 9.90642 14.6527 9.62967C14.5168 9.35575 14.3292 9.10575 13.9542 8.60567L13.618 8.15737C13.4337 7.91151 13.3414 7.78857 13.2693 7.6654C12.8932 7.02277 12.8232 6.24596 13.0782 5.54642C13.1272 5.41234 13.1959 5.27489 13.3333 5" stroke="white" stroke-width="1.5"/>
                            <path d="M9.99996 10.8335V15.0002M7.91663 12.9168H12.0833" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        {{ __('Products') }}
                    </a>
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('business.products.index', 'business.products.edit', 'business.products.show') ? 'active' : '' }}" href="{{ route('business.products.index') }}">{{ __('All Product') }}</a>
                        </li>
                        <li><a class="{{ Request::routeIs('business.products.create') ? 'active' : '' }}" href="{{ route('business.products.create') }}">{{ __('Add Product') }}</a>
                        </li>
                        <li><a class="{{ Request::routeIs('business.expired-products.index') ? 'active' : '' }}" href="{{ route('business.expired-products.index') }}">{{ __('Expired Products') }}</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('business.barcodes.index') ? 'active' : '' }}" href="{{ route('business.barcodes.index') }}">{{ __('Print Labels') }}</a>
                        </li>
                        <li>
                          <a class="{{ Request::routeIs('business.bulk-uploads.index') ? 'active' : '' }}" href="{{ route('business.bulk-uploads.index') }}">{{ __('Bulk Upload') }}</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('business.categories.index') ? 'active' : '' }}" href="{{ route('business.categories.index') }}">{{ __('Category') }}</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('business.units.index') ? 'active' : '' }}" href="{{ route('business.units.index') }}">{{ __('Unit') }}</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('business.medicine-types.index') ? 'active' : '' }}" href="{{ route('business.medicine-types.index') }}">{{ __('Medicine Types') }}</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('business.manufacturers.index') ? 'active' : '' }}" href="{{ route('business.manufacturers.index') }}">{{ __('Manufacturers') }}</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('business.box-sizes.index') ? 'active' : '' }}" href="{{ route('business.box-sizes.index') }}">{{ __('Box Sizes') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role != 'staff' || visible_permission('stockPermission'))
                <li
                    class="dropdown {{ Request::routeIs('business.stocks.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.66663 11.1903V6.6665H18.3333V11.1903C18.3333 14.5575 18.3333 16.2411 17.2485 17.2871C16.1637 18.3332 14.4178 18.3332 10.9259 18.3332H9.07404C5.58215 18.3332 3.83621 18.3332 2.75142 17.2871C1.66663 16.2411 1.66663 14.5575 1.66663 11.1903Z" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1.66663 6.6665L2.46791 4.74343C3.08934 3.25198 3.40007 2.50625 4.02988 2.08638C4.65968 1.6665 5.46756 1.6665 7.08329 1.6665H12.9166C14.5324 1.6665 15.3402 1.6665 15.97 2.08638C16.5999 2.50625 16.9105 3.25198 17.532 4.74343L18.3333 6.6665" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            <path d="M10 6.6665V1.6665" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            <path d="M8.33337 10H11.6667" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            </svg>

                        </span>
                        {{ __('Stock List') }}
                    </a>
                    <ul>
                        <li><a class="{{ Request::routeIs('business.stocks.index') && !request('alert_qty') ? 'active' : '' }}"
                                href="{{ route('business.stocks.index') }}">{{ __('All Stock') }}</a></li>
                        <li><a class="{{ Request::routeIs('business.stocks.index') && request('alert_qty') ? 'active' : '' }}"
                                href="{{ route('business.stocks.index', ['alert_qty' => true]) }}">{{ __('Low Stock') }}</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (auth()->user()->role != 'staff' || visible_permission('partiesPermission'))
                <li
                    class="dropdown {{ (Request::routeIs('business.parties.index') && request('type') == 'Customer') || (Request::routeIs('business.parties.create') && request('type') == 'Customer') || (Request::routeIs('business.parties.edit') && request('type') == 'Customer') || Request::routeIs('business.parties.bulk-upload') && request('type') == 'Customer' ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5 6.6665C12.5 8.04721 11.3807 9.1665 10 9.1665C8.61925 9.1665 7.5 8.04721 7.5 6.6665C7.5 5.2858 8.61925 4.1665 10 4.1665C11.3807 4.1665 12.5 5.2858 12.5 6.6665Z" stroke="white" stroke-width="1.3"/>
                            <path d="M13.3334 3.3335C14.714 3.3335 15.8334 4.45279 15.8334 5.8335C15.8334 6.85274 15.2234 7.72952 14.3486 8.11875" stroke="white" stroke-width="1.3"/>
                            <path d="M11.4286 11.6665H8.57143C6.59898 11.6665 5 13.2655 5 15.2379C5 16.0269 5.63959 16.6665 6.42857 16.6665H13.5714C14.3604 16.6665 15 16.0269 15 15.2379C15 13.2655 13.401 11.6665 11.4286 11.6665Z" stroke="white" stroke-width="1.3"/>
                            <path d="M14.762 10.8335C16.7344 10.8335 18.3334 12.4325 18.3334 14.4049C18.3334 15.1939 17.6938 15.8335 16.9048 15.8335" stroke="white" stroke-width="1.3"/>
                            <path d="M6.66663 3.3335C5.28592 3.3335 4.16663 4.45279 4.16663 5.8335C4.16663 6.85274 4.77657 7.72952 5.65136 8.11875" stroke="white" stroke-width="1.3"/>
                            <path d="M3.0952 15.8335C2.30622 15.8335 1.66663 15.1939 1.66663 14.4049C1.66663 12.4325 3.26561 10.8335 5.23805 10.8335" stroke="white" stroke-width="1.3"/>
                            </svg>
                        </span>
                        {{ __('Customers') }}
                    </a>
                    <ul>
                        <li><a class="{{ Request::routeIs('business.parties.index') && request('type') == 'Customer' ? 'active' : '' }}"
                                href="{{ route('business.parties.index', ['type' => 'Customer']) }}">{{ __('All Customers') }}</a>
                        </li>
                        <li><a class="{{ Request::routeIs('business.parties.create') && request('type') == 'Customer' ? 'active' : '' }}"
                                href="{{ route('business.parties.create', ['type' => 'Customer']) }}">{{ __('Add Customer') }}</a>
                        </li>
                        <li><a class="{{ Request::routeIs('business.parties.bulk-upload') && request('type') == 'Customer' ? 'active' : '' }}"
                               href="{{ route('business.parties.bulk-upload', ['type' => 'Customer']) }}">{{ __('Bulk Upload') }}</a>
                        </li>
                    </ul>
                </li>
                <li
                    class="dropdown {{ (Request::routeIs('business.parties.index') && request('type') == 'Supplier') || (Request::routeIs('business.parties.create') && request('type') == 'Supplier') || (Request::routeIs('business.parties.edit') && request('type') == 'Supplier') || Request::routeIs('business.parties.bulk-upload') && request('type') == 'Supplier' ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path d="M16.6667 18.3335V14.1668C16.6667 12.5955 16.6667 11.8098 16.1785 11.3217C15.6904 10.8335 14.9047 10.8335 13.3334 10.8335L10 18.3335L6.66671 10.8335C5.09536 10.8335 4.30968 10.8335 3.82153 11.3217C3.33337 11.8098 3.33337 12.5955 3.33337 14.1668V18.3335" stroke="white" stroke-width="1.3"/>
                             <path d="M9.99996 12.5002L9.58329 15.8335L9.99996 17.0835L10.4166 15.8335L9.99996 12.5002ZM9.99996 12.5002L9.16663 10.8335H10.8333L9.99996 12.5002Z" stroke="white" stroke-width="1.3"/>
                             <path d="M12.9167 5.41699V4.58366C12.9167 2.97283 11.6109 1.66699 10 1.66699C8.38921 1.66699 7.08337 2.97283 7.08337 4.58366V5.41699C7.08337 7.02783 8.38921 8.33364 10 8.33364C11.6109 8.33364 12.9167 7.02783 12.9167 5.41699Z" stroke="white" stroke-width="1.3"/>
                             </svg>
                        </span>
                        {{ __('Suppliers') }}
                    </a>
                    <ul>
                        <li><a class="{{ Request::routeIs('business.parties.index') && request('type') == 'Supplier' ? 'active' : '' }}"
                                href="{{ route('business.parties.index', ['type' => 'Supplier']) }}">{{ __('All Suppliers') }}</a>
                        </li>
                        <li><a class="{{ Request::routeIs('business.parties.create') && request('type') == 'Supplier' ? 'active' : '' }}"
                                href="{{ route('business.parties.create', ['type' => 'Supplier']) }}">{{ __('Add Supplier') }}</a>
                        </li>
                        <li><a class="{{ Request::routeIs('business.parties.bulk-upload') && request('type') == 'Supplier' ? 'active' : '' }}"
                               href="{{ route('business.parties.bulk-upload', ['type' => 'Supplier']) }}">{{ __('Bulk Upload') }}</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (auth()->user()->role != 'staff' || visible_permission('addIncomePermission'))
                <li
                    class="dropdown {{ Request::routeIs('business.incomes.index', 'business.income-categories.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.4545 10.8332C17.1135 9.88834 17.5 8.73917 17.5 7.49984C17.5 4.27818 14.8884 1.6665 11.6667 1.6665C8.44504 1.6665 5.83337 4.27817 5.83337 7.49984C5.83337 8.3945 6.03478 9.24209 6.39473 9.99984" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.6667 4.99984C10.7462 4.99984 10 5.55948 10 6.24984C10 6.9402 10.7462 7.49984 11.6667 7.49984C12.5872 7.49984 13.3333 8.05948 13.3333 8.74984C13.3333 9.44017 12.5872 9.99984 11.6667 9.99984M11.6667 4.99984C12.3923 4.99984 13.0097 5.34767 13.2385 5.83317M11.6667 4.99984V4.1665M11.6667 9.99984C10.941 9.99984 10.3237 9.652 10.0948 9.1665M11.6667 9.99984V10.8332" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            <path d="M2.5 11.6665H4.49568C4.74081 11.6665 4.98257 11.7218 5.20181 11.8278L6.90346 12.6512C7.1227 12.7573 7.36446 12.8124 7.60958 12.8124H8.47842C9.31875 12.8124 10 13.4717 10 14.2848C10 14.3177 9.9775 14.3466 9.94483 14.3556L7.82739 14.9411C7.44756 15.0461 7.04083 15.0095 6.6875 14.8385L4.86843 13.9584M10 13.7498L13.8273 12.5739C14.5058 12.3625 15.2392 12.6132 15.6642 13.2018C15.9716 13.6273 15.8464 14.2367 15.3987 14.495L9.13575 18.1086C8.73742 18.3384 8.26745 18.3945 7.8293 18.2645L2.5 16.6831" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>


                        </span>
                        {{ __('Incomes') }}</a>
                    <ul>
                        <li><a class="{{ Request::routeIs('business.incomes.index') ? 'active' : '' }}"
                                href="{{ route('business.incomes.index') }}">{{ __('Income') }}</a></li>

                        <li><a class="{{ Request::routeIs('business.income-categories.index') ? 'active' : '' }}"
                                href="{{ route('business.income-categories.index') }}">{{ __('Income Category') }}</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (auth()->user()->role != 'staff' || visible_permission('addExpensePermission'))
                <li
                    class="dropdown {{ Request::routeIs('business.expense-categories.index', 'business.expenses.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.3334 11.6665C13.3334 12.3568 13.893 12.9165 14.5834 12.9165C15.2737 12.9165 15.8334 12.3568 15.8334 11.6665C15.8334 10.9762 15.2737 10.4165 14.5834 10.4165C13.893 10.4165 13.3334 10.9762 13.3334 11.6665Z" stroke="white" stroke-width="1.3"/>
                            <path d="M15.75 6.6665C15.8047 6.39725 15.8333 6.11856 15.8333 5.83317C15.8333 3.53199 13.9678 1.6665 11.6667 1.6665C9.3655 1.6665 7.5 3.53199 7.5 5.83317C7.5 6.11856 7.52869 6.39725 7.58335 6.6665" stroke="white" stroke-width="1.3"/>
                            <path d="M5.83329 6.6612H13.3333C15.6903 6.6612 16.8688 6.6612 17.601 7.39378C18.3333 8.12637 18.3333 9.30541 18.3333 11.6636V13.3311C18.3333 15.6892 18.3333 16.8683 17.601 17.6009C16.8688 18.3335 15.6903 18.3335 13.3333 18.3335H8.33329C5.19059 18.3335 3.61925 18.3335 2.64293 17.3567C1.66663 16.3799 1.66663 14.8078 1.66663 11.6636V9.99616C1.66663 6.85192 1.66663 5.27981 2.64293 4.30303C3.42884 3.51675 4.60032 3.3634 6.66663 3.3335H8.33329" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            </svg>


                        </span>
                        {{ __('Expenses') }}</a>
                    <ul>
                        <li><a class="{{ Request::routeIs('business.expenses.index') ? 'active' : '' }}"
                                href="{{ route('business.expenses.index') }}">{{ __('Expense') }}</a></li>

                        <li><a class="{{ Request::routeIs('business.expense-categories.index') ? 'active' : '' }}"
                                href="{{ route('business.expense-categories.index') }}">{{ __('Expense Category') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role != 'staff')
            <li class="{{ Request::routeIs('business.taxes.index', 'business.taxes.create', 'business.taxes.edit') ? 'active' : '' }}">
                <a href="{{ route('business.taxes.index') }}" class="active">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.08398 10.0002C2.08398 6.26821 2.08398 4.40224 3.24335 3.24286C4.40273 2.0835 6.2687 2.0835 10.0007 2.0835C13.7326 2.0835 15.5986 2.0835 16.758 3.24286C17.9173 4.40224 17.9173 6.26821 17.9173 10.0002C17.9173 13.7321 17.9173 15.5981 16.758 16.7575C15.5986 17.9168 13.7326 17.9168 10.0007 17.9168C6.2687 17.9168 4.40273 17.9168 3.24335 16.7575C2.08398 15.5981 2.08398 13.7321 2.08398 10.0002Z" stroke="white" stroke-width="1.25" stroke-linejoin="round"/>
                        <path d="M6.66602 13.3332L13.3327 6.6665M8.33268 7.49984C8.33268 7.96007 7.95958 8.33317 7.49935 8.33317C7.03912 8.33317 6.66602 7.96007 6.66602 7.49984C6.66602 7.0396 7.03912 6.6665 7.49935 6.6665C7.95958 6.6665 8.33268 7.0396 8.33268 7.49984ZM13.3327 12.3568C13.3327 12.8171 12.9596 13.1902 12.4993 13.1902C12.0391 13.1902 11.666 12.8171 11.666 12.3568C11.666 11.8966 12.0391 11.5235 12.4993 11.5235C12.9596 11.5235 13.3327 11.8966 13.3327 12.3568Z" stroke="white" stroke-width="1.25" stroke-linecap="round"/>
                        </svg>

                    </span>
                    {{ __('Tax') }}
                </a>
            </li>
            @endif

            @if (auth()->user()->role != 'staff' || visible_permission('dueListPermission'))
                <li
                    class="dropdown {{ Request::routeIs('business.dues.index', 'business.collect.dues', 'business.walk-dues.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.25004 2.9165C4.95354 2.9554 4.18055 3.0997 3.64569 3.63506C2.91345 4.36798 2.91345 5.54759 2.91345 7.9068V13.3285C2.91345 15.6878 2.91345 16.8673 3.64569 17.6003C4.37792 18.3332 5.55644 18.3332 7.91345 18.3332H12.0801C14.4371 18.3332 15.6156 18.3332 16.3479 17.6003C17.0801 16.8673 17.0801 15.6878 17.0801 13.3285V7.9068C17.0801 5.54759 17.0801 4.36798 16.3479 3.63507C15.813 3.0997 15.04 2.9554 13.7435 2.9165" stroke="white" stroke-width="1.3"/>
                            <path d="M6.2467 3.12484C6.2467 2.31942 6.89963 1.6665 7.70504 1.6665H12.2884C13.0938 1.6665 13.7467 2.31942 13.7467 3.12484C13.7467 3.93025 13.0938 4.58317 12.2884 4.58317H7.70504C6.89963 4.58317 6.2467 3.93025 6.2467 3.12484Z" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                            <path d="M11.25 9.1665H14.1667" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            <path d="M5.83337 10.0002C5.83337 10.0002 6.25004 10.0002 6.66671 10.8335C6.66671 10.8335 7.99024 8.75016 9.16671 8.3335" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.25 14.1665H14.1667" stroke="white" stroke-width="1.3" stroke-linecap="round"/>
                            <path d="M6.66663 14.1665H7.49996" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Due List') }}</a>
                        <ul>
                            <li>
                                <a class="{{ Request::routeIs('business.dues.index') || (Request::routeIs('business.collect.dues') && request('source') != 'walk-in') ? 'active' : '' }}"
                                   href="{{ route('business.dues.index') }}">
                                    {{ __('All Due') }}
                                </a>
                            </li>

                            <li>
                                <a class="{{ Request::routeIs('business.walk-dues.index') || (Request::routeIs('business.collect.dues') && request('source') == 'walk-in') ? 'active' : '' }}"
                                   href="{{ route('business.walk-dues.index') }}">
                                    {{ __('Cash Due') }}
                                </a>
                            </li>
                        </ul>
                </li>
            @endif

            @if (auth()->user()->role != 'staff')
            <li class="{{ Request::routeIs('business.subscriptions.index') ? 'active' : '' }}">
                <a href="{{ route('business.subscriptions.index') }}" class="active">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.81849 3.48493C5.35835 3.06933 5.62829 2.86153 5.93986 2.72769C6.08114 2.66701 6.22802 2.61798 6.37879 2.58117C6.71129 2.5 7.06388 2.5 7.76904 2.5H12.2309C12.936 2.5 13.2886 2.5 13.6211 2.58117C13.7719 2.61798 13.9188 2.66701 14.06 2.72769C14.3716 2.86153 14.6415 3.06933 15.1815 3.48493C16.9703 4.86207 17.8648 5.55064 18.1714 6.44232C18.3078 6.83894 18.3587 7.25607 18.3215 7.67051C18.2376 8.60225 17.5315 9.455 16.1194 11.1604L12.7915 15.1794C11.5105 16.7265 10.87 17.5 9.99996 17.5C9.12996 17.5 8.48946 16.7265 7.20844 15.1794L3.88055 11.1604C2.46836 9.455 1.76226 8.60225 1.67846 7.67051C1.64119 7.25607 1.69216 6.83894 1.82854 6.44232C2.13516 5.55064 3.0296 4.86207 4.81849 3.48493Z" stroke="white" stroke-width="1.3"/>
                        <path d="M8.33337 7.0835H11.6667" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Subscriptions') }}
                </a>
            </li>
            @endif

            @if (auth()->user()->role != 'staff' || visible_permission('lossProfitPermission'))
                <li class="{{ Request::routeIs('business.loss-profits.index') ? 'active' : '' }}">
                    <a href="{{ route('business.loss-profits.index') }}" class="active">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.83337 15.0002V13.3335M10 15.0002V12.5002M14.1667 15.0002V10.8335M2.08337 10.0002C2.08337 6.26821 2.08337 4.40224 3.24274 3.24286C4.40212 2.0835 6.26809 2.0835 10 2.0835C13.732 2.0835 15.598 2.0835 16.7574 3.24286C17.9167 4.40224 17.9167 6.26821 17.9167 10.0002C17.9167 13.7321 17.9167 15.5981 16.7574 16.7575C15.598 17.9168 13.732 17.9168 10 17.9168C6.26809 17.9168 4.40212 17.9168 3.24274 16.7575C2.08337 15.5981 2.08337 13.7321 2.08337 10.0002Z" stroke="white" stroke-width="1.3"/>
                            <path d="M4.99353 9.57208C6.78945 9.63192 10.8618 9.36083 13.1781 5.6846M11.6603 5.24046L13.2232 4.98891C13.4137 4.96465 13.6934 5.11504 13.7621 5.29431L14.1754 6.65968" stroke="white" stroke-width="1.3"/>
                            </svg>
                        </span>
                        {{ __('Profit & Loss List') }}
                    </a>
                </li>
            @endif

            @if (auth()->user()->role != 'staff' || visible_permission('reportsPermission'))
                <li
                    class="dropdown {{ Request::routeIs('business.income-reports.index', 'business.expense-reports.index', 'business.stock-reports.index', 'business.loss-profit-reports.index', 'business.sale-reports.index', 'business.purchase-reports.index', 'business.due-reports.index', 'business.sale-return-reports.index', 'business.purchase-return-reports.index', 'business.supplier-due-reports.index', 'business.transaction-history-reports.index', 'business.subscription-reports.index', 'business.expired-product-reports.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                           <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.66602 13.3332H9.99931M6.66602 9.1665H13.3326" stroke="white" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M6.25065 2.9165C4.95415 2.9554 4.18116 3.0997 3.6463 3.63506C2.91406 4.36798 2.91406 5.54759 2.91406 7.9068V13.3285C2.91406 15.6878 2.91406 16.8673 3.6463 17.6003C4.37853 18.3332 5.55705 18.3332 7.91406 18.3332H12.0807C14.4377 18.3332 15.6162 18.3332 16.3485 17.6003C17.0807 16.8673 17.0807 15.6878 17.0807 13.3285V7.9068C17.0807 5.54759 17.0807 4.36798 16.3485 3.63507C15.8137 3.0997 15.0407 2.9554 13.7442 2.9165" stroke="white" stroke-width="1.25"/>
                            <path d="M6.24609 3.12484C6.24609 2.31942 6.89902 1.6665 7.70443 1.6665H12.2878C13.0932 1.6665 13.7461 2.31942 13.7461 3.12484C13.7461 3.93025 13.0932 4.58317 12.2878 4.58317H7.70443C6.89902 4.58317 6.24609 3.93025 6.24609 3.12484Z" stroke="white" stroke-width="1.25" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Reports') }}</a>
                    <ul>
                        <li><a class="{{ Request::routeIs('business.sale-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.sale-reports.index') }}">{{ __('Sale') }}</a></li>

                        <li><a class="{{ Request::routeIs('business.sale-return-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.sale-return-reports.index') }}">{{ __('Sale Return') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.purchase-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.purchase-reports.index') }}">{{ __('Purchase') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.purchase-return-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.purchase-return-reports.index') }}">{{ __('Purchase Return') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.income-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.income-reports.index') }}">{{ __('All Income') }}</a></li>

                        <li><a class="{{ Request::routeIs('business.expense-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.expense-reports.index') }}">{{ __('All Expense') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.stock-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.stock-reports.index') }}">{{ __('Current Stock') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.due-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.due-reports.index') }}">{{ __('Customer Due') }}</a></li>

                        <li><a class="{{ Request::routeIs('business.supplier-due-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.supplier-due-reports.index') }}">{{ __('Supplier Due') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.loss-profit-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.loss-profit-reports.index') }}">{{ __('Loss & Profit') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.transaction-history-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.transaction-history-reports.index') }}">{{ __('Transaction') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.subscription-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.subscription-reports.index') }}">{{ __('Subscription Report') }}</a>
                        </li>

                        <li><a class="{{ Request::routeIs('business.expired-product-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.expired-product-reports.index') }}">{{ __('Expired Product') }}</a>
                        </li>

                    </ul>
                </li>
            @endif

            @if (auth()->user()->role != 'staff')
                <li class="{{ Request::routeIs('business.manage-settings.index', 'business.settings.index', 'business.roles.index', 'business.roles.edit', 'business.roles.create', 'business.currencies.index', 'business.notifications.index', 'business.payment-types.index') ? 'active' : '' }}">
                    <a href="{{ route('business.manage-settings.index') }}" class="active">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.9173 10.0002C12.9173 11.611 11.6115 12.9168 10.0007 12.9168C8.38982 12.9168 7.08398 11.611 7.08398 10.0002C7.08398 8.38933 8.38982 7.0835 10.0007 7.0835C11.6115 7.0835 12.9173 8.38933 12.9173 10.0002Z" stroke="white" stroke-width="1.25"/>
                                <path d="M17.5085 11.7469C17.9434 11.6296 18.1609 11.5709 18.2468 11.4588C18.3327 11.3467 18.3327 11.1663 18.3327 10.8055V9.1941C18.3327 8.83335 18.3327 8.65294 18.2468 8.54085C18.1608 8.42869 17.9434 8.37002 17.5085 8.25275C15.8832 7.81443 14.8659 6.1152 15.2854 4.5005C15.4008 4.05643 15.4584 3.8344 15.4033 3.70418C15.3483 3.57395 15.1903 3.48422 14.8741 3.30474L13.4368 2.48871C13.1267 2.3126 12.9716 2.22454 12.8324 2.24329C12.6932 2.26204 12.5362 2.41871 12.222 2.73205C11.006 3.94517 8.99402 3.94512 7.77797 2.73197C7.46387 2.41863 7.30683 2.26196 7.16762 2.2432C7.02842 2.22445 6.87332 2.31251 6.56312 2.48863L5.12588 3.30466C4.80979 3.48413 4.65174 3.57386 4.59667 3.70406C4.54158 3.83427 4.59924 4.05633 4.71456 4.50044C5.13382 6.11519 4.11578 7.81446 2.4902 8.25277C2.05528 8.37002 1.83782 8.42869 1.75192 8.54077C1.66602 8.65294 1.66602 8.83335 1.66602 9.1941V10.8055C1.66602 11.1663 1.66602 11.3467 1.75192 11.4588C1.83781 11.5709 2.05527 11.6296 2.4902 11.7469C4.11552 12.1852 5.13275 13.8844 4.71328 15.4991C4.59792 15.9432 4.54024 16.1652 4.59532 16.2954C4.6504 16.4257 4.80845 16.5154 5.12456 16.6949L6.56181 17.5109C6.87202 17.687 7.02713 17.7751 7.16635 17.7564C7.30557 17.7376 7.46258 17.5809 7.77661 17.2675C8.99327 16.0534 11.0067 16.0534 12.2234 17.2674C12.5374 17.5809 12.6944 17.7375 12.8337 17.7563C12.9728 17.775 13.128 17.6869 13.4382 17.5109L14.8754 16.6948C15.1916 16.5154 15.3497 16.4256 15.4047 16.2954C15.4598 16.1651 15.4021 15.9431 15.2867 15.499C14.867 13.8844 15.8834 12.1853 17.5085 11.7469Z" stroke="white" stroke-width="1.25" stroke-linecap="round"/>
                            </svg>
                        </span>
                        {{ __('Settings') }}
                    </a>
                </li>
            @endif

            @if (auth()->user()->role != 'staff')
            <li>
                <a href="{{ get_option('general')['app_link'] ?? '' }}" target="_blank" class="active">
                    <span class="sidebar-icon">
                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path d="M5.41663 7.91683C5.41663 5.38552 7.46865 3.3335 9.99996 3.3335C12.5313 3.3335 14.5833 5.38552 14.5833 7.91683V13.3335C14.5833 14.512 14.5833 15.1012 14.2172 15.4674C13.851 15.8335 13.2618 15.8335 12.0833 15.8335H7.91663C6.73812 15.8335 6.14886 15.8335 5.78274 15.4674C5.41663 15.1012 5.41663 14.512 5.41663 13.3335V7.91683Z" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                       <path d="M16.6666 9.1665V14.1665" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                       <path d="M12.5 15.8335V18.3335" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                       <path d="M7.5 15.8335V18.3335" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                       <path d="M3.33337 9.1665V14.1665" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                       <path d="M8.33337 3.33317L7.08337 1.6665M11.6667 3.33317L12.9167 1.6665" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                       <path d="M5.41663 8.3335H14.5833" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                       </svg>
                    </span>
                    {{ __('Download Apk') }}
                </a>
            </li>
            @endif

            @if (auth()->user()->role != 'staff')
            <li>
                <div class="sub-plan ">
                    <img src="{{ asset('assets/images/sidebar/plan-icon.svg') }}">
                </div>
            </li>
            <li>
                <div class="lg-sub-plan">
                    <div id="sidebar_plan"
                        class=" sidebar-free-plan d-flex align-items-center justify-content-between p-3 flex-column">
                        <div class="text-center">
                            @if (plan_data() ?? false)
                                <h3>
                                    {{ plan_data()['plan']['subscriptionName'] ?? '' }}
                                </h3>
                                <h5>
                                    {{ __('Expired') }}: {{ formatted_date(plan_data()['will_expire'] ?? '') }}
                                </h5>
                            @else
                                <h3>{{ __('No Active Plan') }}</h3>
                                <h5>{{ __('Please subscribe to a plan') }}</h5>
                            @endif

                        </div>
                        <a href="{{ route('business.subscriptions.index') }}" class=" upgrate-btn fw-bold">{{ __('Upgrade Now') }}</a>
                    </div>
                </div>
            </li>
            @endif
        </ul>
    </div>
</nav>
