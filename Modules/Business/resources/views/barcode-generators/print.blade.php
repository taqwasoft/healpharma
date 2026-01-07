@extends('business::layouts.blank')

@section('title')
    {{ __('Invoice') }}
@endsection

@section('main_content')
    <div id="barcodePrintArea" class="barcode-container">
        <div class="barcode-print-wrapper">
            @foreach ($generatedBarcodes ?? [] as $barcode)
                <div class="barcode-item">
                    <div class="barcode-content">
                        @if (isset($barcode['show_product_name']) && $barcode['show_product_name'])
                            <p class="title">
                                {{ $barcode['product_name'] }}
                            </p>
                        @endif
                        @if (isset($barcode['show_product_price']) && $barcode['show_product_price'])
                            <p class="price">
                                {{__('Price')}}: {{ currency_format($barcode['product_price'], currency:business_currency()) }}
                            </p>
                        @endif
                        @if (isset($barcode['show_pack_date']) && $barcode['show_pack_date'])
                            <p class="date">
                                {{__('Packing Date')}}: {{ $barcode['packing_date'] }}
                            </p>
                        @endif
                        <div class="barcode-image">
                            {!! $barcode['barcode_svg'] !!}
                        </div>
                        @if (isset($barcode['show_product_code']) && $barcode['show_product_code'])
                            <p class="number">
                                {{ $barcode['product_code'] }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/custom/onloadPrint.js') }}"></script>
@endpush
