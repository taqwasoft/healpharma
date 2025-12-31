
<link rel="shortcut icon" type="image/x-icon" href="{{ asset(get_option('general')['favicon'] ?? 'assets/images/logo/favicon.png')}}">
<!-- Bootstrap -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<!-- Fontawesome -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/css/fontawesome-all.min.css') }}">
{{-- jquery-confirm --}}
<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-confirm/jquery-confirm.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/summernote-lite.css') }}">
<!-- Lily -->
<link rel="stylesheet" href="{{ asset('assets/css/lity.css') }}">
<!-- Style -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom-business.css') }}?v={{ time() }}">
<!-- Toaster -->
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
@stack('css')

@php
    $rtlLocales = ['ar', 'ur', 'ps', 'prs', 'eg-ar', 'arbh', 'fa'];
@endphp

@if (in_array(app()->getLocale(), $rtlLocales))
    <link rel="stylesheet" href="{{ asset('assets/css/arabic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.rtl.min.css') }}">
@endif
