@extends('layouts.web.blank')

@section('main_content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <a class="theme-btn d-block" href="{{ route('order.status', ['status' => 'failed']) }}"><i class="fas fa-arrow-left"></i> {{ __('Go Back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="post" class="status" action="{{ route('paystack.status') }}">
        @csrf
        <input type="hidden" name="ref_id" id="ref_id">
        <input type="hidden" value="{{ $Info['currency'] }}" id="currency">
        <input type="hidden" value="{{ $Info['amount'] }}" id="amount">
        <input type="hidden" value="{{ $Info['public_key'] }}" id="public_key">
        <input type="hidden" value="{{ $Info['email'] ?? Auth::user()->email }}" id="email">
    </form>
@endsection

@push('js')
    <script src="{{ asset('assets/js/paystack/inline.js') }}"></script>
    <script src="{{ asset('assets/js/paystack/custom.js') }}"></script>
@endpush
