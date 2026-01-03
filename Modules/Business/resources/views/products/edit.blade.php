@extends('business::layouts.master')

@section('title')
    {{ __('Edit Product') }}
@endsection

@php
    $modules = product_setting()->modules ?? [];
    $showSingle  = !isset($modules['show_product_type_single']) || $modules['show_product_type_single'];
    $showVariant = isset($modules['show_product_type_variant']) && $modules['show_product_type_variant'];
    $hasVisibleColumn = is_module_enabled($modules, 'show_batch_no') || is_module_enabled($modules, 'show_product_stock') || is_module_enabled($modules, 'show_exclusive_price') ||is_module_enabled($modules, 'show_inclusive_price') ||is_module_enabled($modules, 'show_profit_percent') ||is_module_enabled($modules, 'show_product_sale_price') ||is_module_enabled($modules, 'show_product_wholesale_price') ||is_module_enabled($modules, 'show_product_dealer_price') ||is_module_enabled($modules, 'show_mfg_date') ||is_module_enabled($modules, 'show_expire_date') ||is_module_enabled($modules, 'show_action');
    $defaultPermissions = [
        'show_batch_no',
        'show_product_stock',
        'show_exclusive_price',
        'show_inclusive_price',
        'show_profit_percent',
        'show_product_sale_price',
        'show_product_wholesale_price',
        'show_product_dealer_price',
        'show_mfg_date',
        'show_expire_date',
        'show_action',
    ];
@endphp


@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <form action="{{ route('business.products.update', $product->id) }}" method="POST" class="ajaxform_instant_reload" enctype="multipart/form-data">
                @csrf
                @method('put')

                @php
                    $isSingle = $product->product_type == 'single';
                    $singleStock = $isSingle ? $product->stocks->first() : null;
                @endphp

                <div class="row g-2">
                    <div class="col-lg-12">
                        <div class="card product-card border-0 ">
                            <div class="card-bodys ">
                                <div class="table-header p-16">
                                    <h4>{{ __('Edit Product') }}</h4>
                                    <div class="d-flex align-items-center gap-3">
                                            <button class="save-publish-btn submit-btn" href="">
                                                {{__('Save & Published')}}
                                            </button>
                                        <div class="position-relative">
                                            <a class="product-settings" href={{ route('business.manage-settings.index') }}#product>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.3083 4.38394C15.7173 4.38394 15.4217 4.38394 15.1525 4.28405C15.1151 4.27017 15.0783 4.25491 15.042 4.23828C14.781 4.11855 14.5721 3.90959 14.1541 3.49167C13.1922 2.52977 12.7113 2.04882 12.1195 2.00447C12.04 1.99851 11.96 1.99851 11.8805 2.00447C11.2887 2.04882 10.8077 2.52977 9.84585 3.49166C9.42793 3.90959 9.21897 4.11855 8.95797 4.23828C8.92172 4.25491 8.88486 4.27017 8.84747 4.28405C8.57825 4.38394 8.28273 4.38394 7.69171 4.38394H7.58269C6.07478 4.38394 5.32083 4.38394 4.85239 4.85239C4.38394 5.32083 4.38394 6.07478 4.38394 7.58269V7.69171C4.38394 8.28273 4.38394 8.57825 4.28405 8.84747C4.27017 8.88486 4.25491 8.92172 4.23828 8.95797C4.11855 9.21897 3.90959 9.42793 3.49166 9.84585C2.52977 10.8077 2.04882 11.2887 2.00447 11.8805C1.99851 11.96 1.99851 12.04 2.00447 12.1195C2.04882 12.7113 2.52977 13.1922 3.49166 14.1541C3.90959 14.5721 4.11855 14.781 4.23828 15.042C4.25491 15.0783 4.27017 15.1151 4.28405 15.1525C4.38394 15.4217 4.38394 15.7173 4.38394 16.3083V16.4173C4.38394 17.9252 4.38394 18.6792 4.85239 19.1476C5.32083 19.6161 6.07478 19.6161 7.58269 19.6161H7.69171C8.28273 19.6161 8.57825 19.6161 8.84747 19.716C8.88486 19.7298 8.92172 19.7451 8.95797 19.7617C9.21897 19.8815 9.42793 20.0904 9.84585 20.5083C10.8077 21.4702 11.2887 21.9512 11.8805 21.9955C11.96 22.0015 12.0399 22.0015 12.1195 21.9955C12.7113 21.9512 13.1922 21.4702 14.1541 20.5083C14.5721 20.0904 14.781 19.8815 15.042 19.7617C15.0783 19.7451 15.1151 19.7298 15.1525 19.716C15.4217 19.6161 15.7173 19.6161 16.3083 19.6161H16.4173C17.9252 19.6161 18.6792 19.6161 19.1476 19.1476C19.6161 18.6792 19.6161 17.9252 19.6161 16.4173V16.3083C19.6161 15.7173 19.6161 15.4217 19.716 15.1525C19.7298 15.1151 19.7451 15.0783 19.7617 15.042C19.8815 14.781 20.0904 14.5721 20.5083 14.1541C21.4702 13.1922 21.9512 12.7113 21.9955 12.1195C22.0015 12.0399 22.0015 11.96 21.9955 11.8805C21.9512 11.2887 21.4702 10.8077 20.5083 9.84585C20.0904 9.42793 19.8815 9.21897 19.7617 8.95797C19.7451 8.92172 19.7298 8.88486 19.716 8.84747C19.6161 8.57825 19.6161 8.28273 19.6161 7.69171V7.58269C19.6161 6.07478 19.6161 5.32083 19.1476 4.85239C18.6792 4.38394 17.9252 4.38394 16.4173 4.38394H16.3083Z" stroke="#4B5563" stroke-width="1.5"/>
                                                    <path d="M15.5 12C15.5 13.933 13.933 15.5 12 15.5C10.067 15.5 8.5 13.933 8.5 12C8.5 10.067 10.067 8.5 12 8.5C13.933 8.5 15.5 10.067 15.5 12Z" stroke="#4B5563" stroke-width="1.5"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-form-section p-16">

                                    <div class="add-suplier-modal-wrapper d-block">
                                        <div class="row">
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Product Name') }}</label>
                                                <input type="text" name="productName" value="{{ $product->productName }}" required class="form-control" placeholder="{{ __('Enter Product Name') }}">
                                            </div>

                                            @if (is_module_enabled($modules, 'show_product_code'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Product Code') }}</label>
                                                <input type="text" value="{{ $product->productCode }}" name="productCode" class="form-control" placeholder="{{ __('Enter Product Code') }}">
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_category'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Category') }}</label>
                                                <div class="gpt-up-down-arrow position-relative">
                                                    <select name="category_id" class="form-control table-select w-100 role">
                                                        <option value=""> {{ __('Select Category') }}</option>
                                                        @foreach ($categories as $category)
                                                            <option @selected($category->id == $product->category_id) value="{{ $category->id }}">{{ ucfirst($category->categoryName) }} </option>
                                                        @endforeach
                                                    </select>
                                                    <span></span>
                                                </div>
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_medicine_type'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Medicine Type') }}</label>
                                                <div class="gpt-up-down-arrow position-relative">
                                                    <select name="type_id" class="form-control table-select w-100 role">
                                                        <option value=""> {{ __('Select one') }}</option>
                                                        @foreach ($medicine_types as $type)
                                                            <option @selected($type->id == $product->type_id) value="{{ $type->id }}"> {{ ucfirst($type->name) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span></span>
                                                </div>
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_box_size'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Box Size') }}</label>
                                                <div class="gpt-up-down-arrow position-relative">
                                                    <select name="box_size_id" class="form-control table-select w-100">
                                                        <option value=""> {{ __('Select one') }}</option>
                                                        @foreach ($box_sizes as $size)
                                                            <option @selected($size->id == $product->box_size_id) value="{{ $size->id }}"> {{ ucfirst($size->name) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span></span>
                                                </div>
                                            </div>
                                            @endif


                                            @if (is_module_enabled($modules, 'show_product_unit'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Product Unit') }}</label>
                                                <div class="gpt-up-down-arrow position-relative">
                                                    <select name="unit_id" class="form-control table-select w-100 role">
                                                        <option value=""> {{ __('Select one') }}</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ $unit->id }}" @selected($unit->id == $product->unit_id)> {{ ucfirst($unit->unitName) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span></span>
                                                </div>
                                            </div>
                                            @endif

                                             @if (is_module_enabled($modules, 'show_product_manufacturer'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Manufacturer') }}</label>
                                                <div class="gpt-up-down-arrow position-relative">
                                                    <select name="manufacturer_id" class="form-control table-select w-100">
                                                        <option value=""> {{ __('Select one') }}</option>
                                                        @foreach ($manufactures as $manufacture)
                                                            <option @selected($manufacture->id == $product->manufacturer_id) value="{{ $manufacture->id }}"> {{ ucfirst($manufacture->name) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span></span>
                                                </div>
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_strength'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Strength') }}</label>
                                                <input type="text" name="meta[strength]" value="{{ $product->meta['strength'] ?? '' }}" class="form-control" placeholder="{{ __('Enter Strength') }}">
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_generic_name'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Generic Name') }}</label>
                                                <input type="text" name="generic_name" value="{{ $product->generic_name ?? $product->meta['generic_name'] ?? '' }}" class="form-control" placeholder="{{ __('Enter Generic Name') }}">
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_shelf'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Shelf') }}</label>
                                                <input type="text" name="meta[shelf]" value="{{ $product->meta['shelf'] ?? '' }}" class="form-control" placeholder="{{ __('Enter Shelf') }}">
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_alert_qty'))
                                            <div class="col-lg-4 mb-2">
                                                <label>{{ __('Low Stock Alert') }}</label>
                                                <input type="number" step="any" name="alert_qty" value="{{ $product->alert_qty ?? '' }}" class="form-control" placeholder="{{ __('EX: 5') }}">
                                            </div>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_medicine_details'))
                                            <div class="col-lg-6 mb-2">
                                                <label>{{ __('Medicine Details') }}</label>
                                                <textarea name="meta[medicine_details]" class="form-control" placeholder="{{ __('Enter Medicine Details') }}">{{ $product->meta['medicine_details'] ?? '' }}</textarea>
                                            </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="product-price-container">
                            <div class="product-price-header">
                                <h3>{{__('Product Price, Stock')}}</h3>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    @if ($showSingle && $showVariant)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="product_type" id="singleOption" value="single" {{ $product->product_type == 'single' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="singleOption">{{__('Single')}}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="product_type" id="variantOption" value="variant" {{ $product->product_type == 'variant' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="variantOption">{{__('Batch')}}</label>
                                    </div>
                                    @elseif ($showSingle)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="product_type" id="singleOption" value="single" {{ $product->product_type == 'single' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="singleOption">{{__('Single')}}</label>
                                    </div>
                                    @elseif ($showVariant)
                                     <div class="form-check">
                                        <input class="form-check-input" type="radio" name="product_type" id="variantOption" value="variant" {{ $product->product_type == 'variant' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="variantOption">{{__('Batch')}}</label>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Multiple Variant --}}
                            <div class="variant-container">
                                <div class="main-variant-content">
                                    <div class="variant-content mt-3">
                                        @if (is_module_enabled($modules, 'show_tax_id'))
                                        <h5>{{ __('Tax') }}</h5>
                                        @endif
                                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                                            @if (is_module_enabled($modules, 'show_tax_id'))
                                            <div class="dual-dropdown">
                                                <select id="vat_id" name="tax_id">
                                                    <option value="">{{ __('Select one') }}</option>
                                                    @foreach ($taxes as $tax)
                                                        <option value="{{ $tax->id }}" data-vat_rate="{{ $tax->rate }}" {{ $product->tax_id == $tax->id ? 'selected' : '' }}>
                                                            {{ $tax->name }} ({{ $tax->rate }}%)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select id="vat_type" name="tax_type">
                                                    <option value="exclusive" {{ $product->tax_type == 'exclusive' ? 'selected' : '' }}>{{__('Exclusive')}}</option>
                                                    <option value="inclusive" {{ $product->tax_type == 'inclusive' ? 'selected' : '' }}>{{__('Inclusive')}}</option>
                                                </select>
                                            </div>
                                            @endif
                                            @if ($hasVisibleColumn)
                                            <div class="d-flex align-items-center gap-2">
                                                <a class="save-publish-btn add-variant-btn"> + {{__('Add')}} </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="responsive-table product-table m-0 mt-3">
                                    <table class="table" id="datatable">
                                        <thead>
                                        <tr>
                                            @if (is_module_enabled($modules, 'show_batch_no'))
                                            <th>{{ __('Batch No') }}.</th>
                                            @endif

                                            @if (is_module_enabled($modules, 'show_product_stock'))
                                            <th>{{ __('Qty') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_exclusive_price'))
                                            <th>{{ __('Cost exc. tax') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_inclusive_price'))
                                            <th>{{ __('Cost inc. tax') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_profit_percent'))
                                            <th>{{ __('Profit') }} (%)</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_product_sale_price'))
                                            <th>{{ __('Sales Price') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_product_wholesale_price'))
                                            <th>{{ __('Wholesale') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_product_dealer_price'))
                                            <th>{{ __('Dealer') }}</th>
                                            @endif
                                             @if (is_module_enabled($modules, 'show_mfg_date'))
                                            <th>{{ __('Manufacture') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_expire_date'))
                                            <th>{{ __('Expired') }}</th>
                                            @endif
                                            @if (is_module_enabled($modules, 'show_action'))
                                            <th>{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody id="product-data">
                                        @if ($product->product_type == 'variant')
                                            @foreach ($product->stocks as $index => $stock)
                                            <tr>
                                                @if (is_module_enabled($modules, 'show_batch_no'))
                                                <td>
                                                    <input type="text" name="stocks[{{ $index }}][batch_no]" value="{{ $stock->batch_no }}" class="form-control form-control-sm custom-table-input" placeholder="{{__('25632')}}">
                                                </td>
                                                @endif

                                                @if (is_module_enabled($modules, 'show_product_stock'))
                                                <td>
                                                    <input type="number" step="any" min="0" name="stocks[{{ $index }}][productStock]" value="{{ $stock->productStock }}" class="form-control form-control-sm custom-table-input" placeholder="{{__('Ex: 3')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_exclusive_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control form-control-sm custom-table-input exclusive_price" name="stocks[{{ $index }}][exclusive_price]"
                                                           value="{{ $stock->tax_type == 'exclusive' ? $stock->purchase_without_tax : $stock->purchase_without_tax - $stock->tax_amount }}"
                                                           placeholder="{{__('Ex: 50')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_inclusive_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control form-control-sm custom-table-input inclusive_price" name="stocks[{{ $index }}][inclusive_price]" value="{{ $stock->tax_type == 'inclusive' ? $stock->purchase_without_tax : $stock->purchase_without_tax + $stock->tax_amount }}" placeholder="{{__('Ex: 50')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_profit_percent'))
                                                <td>
                                                    <input type="number" class="form-control form-control-sm custom-table-input profit_percent" name="stocks[{{ $index }}][profit_percent]" value="{{ $stock->profit_percent }}" placeholder="{{__('25%')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_product_sale_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control form-control-sm custom-table-input productSalePrice" name="stocks[{{ $index }}][sales_price]" value="{{ $stock->sales_price }}" placeholder="{{__('Ex: 200')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_product_wholesale_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" name="stocks[{{ $index }}][wholesale_price]" value="{{ $stock->wholesale_price }}" class="form-control form-control-sm custom-table-input" placeholder="{{__('Ex: 200')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_product_dealer_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" name="stocks[{{ $index }}][dealer_price]" value="{{ $stock->dealer_price }}" class="form-control form-control-sm custom-table-input" placeholder="{{__('Ex: 200')}}">
                                                </td>
                                                @endif

                                                @if (is_module_enabled($modules, 'show_mfg_date'))
                                                @if(isset($modules['mfg_date_type']) && ($modules['mfg_date_type'] == 'dmy' || is_null($modules['mfg_date_type'])))
                                                    <td><input type="month" name="stocks[{{ $index }}][mfg_date]" value="{{ $stock->mfg_date }}" class="form-control"></td>
                                                 @else
                                                    <td><input type="date" name="stocks[{{ $index }}][mfg_date]" value="{{ $stock->mfg_date }}" class="form-control"></td>
                                                 @endif
                                                @endif

                                                @if (is_module_enabled($modules, 'show_expire_date'))
                                                @if(isset($modules['expire_date_type']) && ($modules['expire_date_type'] == 'dmy' || is_null($modules['expire_date_type'])))
                                                    <td><input type="month" name="stocks[{{ $index }}][expire_date]" value="{{ $stock->expire_date }}" class="form-control"></td>
                                                @else
                                                    <td><input type="date" name="stocks[{{ $index }}][expire_date]" value="{{ $stock->expire_date }}" class="form-control"></td>
                                                @endif
                                                @endif

                                                @if (is_module_enabled($modules, 'show_action'))
                                                <td>
                                                    <a href="#" class="text-danger remove-row">
                                                        <i class="fal fa-times fa-lg"></i>
                                                    </a>
                                                </td>
                                                @endif
                                            </tr>
                                           @endforeach
                                        @else
                                            <tr>
                                                @if (is_module_enabled($modules, 'show_batch_no'))
                                                <td><input type="text" name="stocks[0][batch_no]" class="form-control form-control-sm custom-table-input" placeholder="{{__('25632')}}"></td>
                                                @endif

                                                @if (is_module_enabled($modules, 'show_product_stock'))
                                                <td><input type="number" step="any" min="0" name="stocks[0][productStock]" class="form-control form-control-sm custom-table-input" placeholder="{{__('Ex: 3')}}"></td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_exclusive_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control form-control-sm custom-table-input exclusive_price" name="stocks[0][exclusive_price]" placeholder="{{__('Ex: 50')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_inclusive_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control form-control-sm custom-table-input inclusive_price" name="stocks[0][inclusive_price]" placeholder="{{__('Ex: 50')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_profit_percent'))
                                                <td>
                                                    <input type="number" class="form-control form-control-sm custom-table-input profit_percent" name="stocks[0][profit_percent]" placeholder="{{__('25%')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_product_sale_price'))
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control form-control-sm custom-table-input productSalePrice" name="stocks[0][sales_price]" placeholder="{{__('Ex: 200')}}">
                                                </td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_product_wholesale_price'))
                                                <td><input type="number" step="any" min="0" name="stocks[0][wholesale_price]" class="form-control form-control-sm custom-table-input" placeholder="{{__('Ex: 200')}}"></td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_product_dealer_price'))
                                                <td><input type="number" step="any" min="0" name="stocks[0][dealer_price]" class="form-control form-control-sm custom-table-input" placeholder="{{__('Ex: 200')}}"></td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_mfg_date'))
                                                <td><input type="date" name="stocks[0][mfg_date]" class="form-control"></td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_expire_date'))
                                                <td><input type="date" name="stocks[0][expire_date]" class="form-control"></td>
                                                @endif
                                                @if (is_module_enabled($modules, 'show_action'))
                                                <td>
                                                    <a href="#" class="text-danger remove-row">
                                                        <i class="fal fa-times fa-lg"></i>
                                                    </a>
                                                </td>
                                                @endif
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Single Variant --}}
                            <div class="single-container order-form-section m-3 pb-3 mt-0">
                                <div class="row">
                                    @if (is_module_enabled($modules, 'show_tax_type'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('Tax Type')}}</label>
                                            <select id="vat_type" name="tax_type" class="form-control">
                                                <option value="exclusive" {{ $product->tax_type == 'exclusive' ? 'selected' : '' }}>{{__('Exclusive')}}</option>
                                                <option value="inclusive" {{ $product->tax_type == 'inclusive' ? 'selected' : '' }}>{{__('Inclusive')}}</option>
                                            </select>
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_tax_id'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('Select Tax')}}</label>
                                            <select id="vat_id" name="tax_id" class="form-control">
                                                <option value="">{{ __('Select one') }}</option>
                                                @foreach ($taxes as $tax)
                                                    <option value="{{ $tax->id }}" data-vat_rate="{{ $tax->rate }}" {{ $product->tax_id == $tax->id ? 'selected' : '' }}>
                                                        {{ $tax->name }} ({{ $tax->rate }}%)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_product_stock'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('Quantity')}}</label>
                                            <input type="number" name="stocks[0][productStock]" class="form-control" value="{{ $isSingle && $singleStock ? $singleStock->productStock : 0 }}">
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_exclusive_price'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{ __('Cost exc. tax') }}</label>
                                            <input type="number" class="form-control exclusive_price" name="stocks[0][exclusive_price]" value="{{ $isSingle && $singleStock ? ($singleStock->tax_type == 'exclusive' ? $singleStock->purchase_without_tax : $singleStock->purchase_without_tax - $singleStock->tax_amount) : 0 }}" placeholder="{{__('Enter Purchase Price')}}" required>
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_inclusive_price'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{ __('Cost inc. tax') }}</label>
                                            <input type="number" class="form-control inclusive_price" name="stocks[0][inclusive_price]" value="{{ $isSingle && $singleStock ? ($singleStock->tax_type == 'inclusive' ? $singleStock->purchase_without_tax : $singleStock->purchase_without_tax + $singleStock->tax_amount) : 0 }}" placeholder="{{__('Enter Purchase Price')}}" required>
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_profit_percent'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('Profit')}} (%)</label>
                                            <input type="number" class="form-control profit_percent" name="stocks[0][profit_percent]" value="{{ $isSingle && $singleStock ? $singleStock->profit_percent : 0 }}">
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_product_sale_price'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('MRP/Sales Price')}}</label>
                                            <input type="number" class="form-control productSalePrice" name="stocks[0][sales_price]"
                                                   value="{{ $isSingle && $singleStock ? $singleStock->sales_price : 0 }}">
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_product_wholesale_price'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('Wholesale Price')}}</label>
                                            <input type="number" name="stocks[0][wholesale_price]" class="form-control"
                                                   value="{{ $isSingle && $singleStock ? $singleStock->wholesale_price : 0 }}">
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_product_dealer_price'))
                                        <div class="col-lg-6 mb-2">
                                            <label>{{__('Dealer Price')}}</label>
                                            <input type="number" name="stocks[0][dealer_price]" class="form-control"
                                                   value="{{ $isSingle && $singleStock ? $singleStock->dealer_price : 0 }}">
                                        </div>
                                    @endif

                                    @if (is_module_enabled($modules, 'show_mfg_date'))
                                        @if(isset($modules['mfg_date_type']) && ($modules['mfg_date_type'] == 'dmy' || is_null($modules['mfg_date_type'])))
                                            <div class="col-lg-6 mb-2">
                                                <label>{{ __('Manufacturing Date') }}</label>
                                                <input type="month" name="stocks[0][mfg_date]" value="{{ $isSingle && $singleStock ? $singleStock->mfg_date : '' }}" class="form-control">
                                            </div>
                                        @else
                                            <div class="col-lg-6 mb-2">
                                                <label>{{ __('Manufacturing Date') }}</label>
                                                <input type="date" name="stocks[0][mfg_date]" value="{{ $isSingle && $singleStock ? $singleStock->mfg_date : '' }}" class="form-control">
                                            </div>
                                        @endif
                                    @endif

                                    @if (is_module_enabled($modules, 'show_expire_date'))
                                        @if(isset($modules['expire_date_type']) && ($modules['expire_date_type'] == 'dmy' || is_null($modules['expire_date_type'])))
                                            <div class="col-lg-6 mb-2">
                                                <label>{{ __('Expire Date') }}</label>
                                                <input type="month" name="stocks[0][expire_date]" value="{{ $isSingle && $singleStock ? $singleStock->expire_date : '' }}" class="form-control">
                                            </div>
                                        @else
                                            <div class="col-lg-6 mb-2">
                                                <label>{{ __('Expire Date') }}</label>
                                                <input type="date" name="stocks[0][expire_date]" value="{{ $isSingle && $singleStock ? $singleStock->expire_date : '' }}" class="form-control">
                                            </div>
                                        @endif
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-12">
                        @if (is_module_enabled($modules, 'show_product_image'))

                              <div class="col-lg-6 mt-4">

                                        <div class="multiple-image-upload border rounded p-2">
                                            <div class="multiple-preview-container">
                                                @foreach($product->images ?? [] as $image)
                                                    @if(!empty($image))
                                                    <div class="image-item">
                                                        <img src="{{ asset($image) }}" alt="img">
                                                        <button type="button" class="delete-button">X</button>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <button type="button" class="add-image btn btn-outline-primary">
                                                + {{ __('Add Image') }}
                                                <input type="file" name="images[]" class="multiple-file-input" multiple accept="image/*">
                                            </button>
                                        </div>
                                    </div>

                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- module permission as hidden--}}
    @foreach($defaultPermissions as $key)
        @php
            // if $modules is not set, default to true (1)
            $value = isset($modules[$key]) ? ($modules[$key] ? 1 : 0) : 1;
        @endphp
        <input type="hidden" class="module-permission" data-key="{{ $key }}" value="{{ $value }}">
    @endforeach

    <input type="hidden" id="canSeePrice" value="1">

@endsection

@push('js')
    <script src="{{ asset('assets/js/custom/product.js') }}"></script>
@endpush
