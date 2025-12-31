<div class="login-button-list">
    <ul>
        <li>
            <a class="theme-btn" href="javascript:void(0)" onclick="fillup('superadmin@acnoo.com','superadmin')">{{ __('Super Admin') }}</a>
        </li>
        <li>
            <a class="theme-btn" href="javascript:void(0)" onclick="fillup('admin@acnoo.com','admin')">{{ __('Admin') }}</a>
        </li>
        @php
            $module = Module::find('Business');
        @endphp
        @if ($module && $module->isEnabled())
            <li><a class="theme-btn position-relative" href="javascript:void(0)" onclick="fillup('shopowner@acnoo.com','123456')">{{ __('Bussiness') }}<div class="sup-addon">Add-on</div></a></li>
        @else
            <li><a class="theme-btn" href="javascript:void(0)" onclick="fillup('manager@acnoo.com','manager')">{{ __('Manager') }}</a></li>
        @endif
    </ul>
</div>
