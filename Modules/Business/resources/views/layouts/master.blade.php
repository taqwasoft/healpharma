<!DOCTYPE html>
@if (in_array(app()->getLocale(), ['ar', 'arbh', 'eg-ar', 'fa', 'prs', 'ps', 'ur']))
    <html lang="{{ app()->getLocale() }}" dir="rtl">
    @else
    <html lang="{{ app()->getLocale() }}" dir="auto">
@endif
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="{{__('IE=edge')}}">
    <meta name="viewport" content="{{__('width=device-width, initial-scale=1.0')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title') @yield('title') | @endif {{ get_option('general')['title'] ?? config('app.name') }}</title>
    @include('business::layouts.partials.css')
</head>
@php
    $isSalePage = request()->routeIs('business.sales.create', 'business.sales.edit', 'business.purchases.create', 'business.purchases.edit');
@endphp
<body class="{{ $isSalePage ? 'sale-page-bg' : '' }}">

<!-- Side Bar Start -->
@include('business::layouts.partials.siqde-bar')
<!-- Side Bar End -->
<div class="section-container">
    <!-- header start -->
    @include('business::layouts.partials.header')
    <!-- header end -->
    <!-- erp-state-overview-section start -->
    <div class="custom-min-h">
        @yield('main_content')
    </div>
    <!-- erp-state-overview-section end -->
    <!-- footer start -->
    @if (!$isSalePage)
      @include('business::layouts.partials.footer')
    @endif
    <!-- footer end -->
    @stack('modal')
</div>

@include('business::layouts.partials.script')
</body>
</html>
