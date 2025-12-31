@extends('business::layouts.master')

@section('title')
    {{ __('Roles') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">

                    <div class="table-header p-16">
                        <h4>{{ __('Add New Role') }}</h4>

                        <a href="{{ route('business.roles.index') }}"
                            class="add-order-btn rounded-2"><i class="far fa-list me-1" aria-hidden="true"></i> {{ __('View List') }}
                        </a>
                    </div>

                    <div class="row justify-content-center mt-2 roles-permissions p-16">
                        <div class="col-md-12">
                            <form action="{{ route('business.roles.store') }}" method="post" class="row ajaxform_instant_reload">
                                @csrf

                                <div class="col-lg-4 form-group role-input-label">
                                    <label for="name" class="required">{{ __('Name') }}</label>
                                    <input type="text" name="name" class="form-control" placeholder="{{ __('Enter name') }}" required>
                                </div>

                                <div class="col-lg-4 form-group role-input-label">
                                    <label for="email" class="required">{{ __('Email') }}</label>
                                    <input type="email" name="email" class="form-control" placeholder="{{ __('Enter email') }}" required>
                                </div>

                                <div class="col-lg-4 form-group role-input-label">
                                    <label for="password" class="required">{{ __('Password') }}</label>
                                    <input type="password" name="password" class="form-control" placeholder="{{ __('*******') }}" required>
                                </div>

                                <div class="col-lg-12 mt-3">

                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th class="text-nowrap text-start ">
                                                        {{ __('SL') }}
                                                    </th>

                                                    <th class="text-nowrap  text-start">
                                                        {{ __('Features') }}
                                                    </th>

                                                    <th class="text-start">
                                                        <div class="custom-control custom-checkbox d-flex align-items-center gap-2">
                                                            <input type="checkbox" class="custom-control-input delete-checkbox-item multi-delete"
                                                                id="selectAll">
                                                            <label class="custom-control-label"
                                                                for="selectAll">{{ __('Select All') }}</label>
                                                        </div>
                                                    </th>
                                                </tr>

                                                @php
                                                    $permissions = [
                                                        'dashboard' => ['Read'],
                                                        'sales' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'sale-returns' => ['Read', 'Create'],
                                                        'purchases' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'purchase-returns' => ['Read', 'Create'],
                                                        'products' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'expired-products' => ['Read'],
                                                        'barcodes' => ['Read', 'Create'],
                                                        'bulk-uploads' => ['Read', 'Create'],
                                                        'categories' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'units' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'medicine-types' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'manufacturers' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'box-sizes' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'stocks' => ['Read'],
                                                        'parties' => ['Read', 'Create', 'Update', 'Delete',],
                                                        'incomes' => ['Read', 'Create', 'Update', 'Delete',],
                                                        'income-categories' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'expenses' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'expense-categories' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'taxes' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'dues' => ['Read',],
                                                        'subscriptions' => ['Read'],
                                                        'loss-profits' => ['Read'],
                                                        'manage-settings' => ['Read', 'Update'],
                                                        'settings' => ['Read', 'Update'],
                                                        'notifications' => ['Read'],
                                                        'currencies' => ['Read', 'Update'],
                                                        'roles' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'payment-types' => ['Read', 'Create', 'Update', 'Delete'],
                                                        'download-apk' => ['Read'],
                                                        'sale-reports' => ['Read'],
                                                        'sale-return-reports' => ['Read'],
                                                        'purchase-reports' => ['Read'],
                                                        'purchase-return-reports' => ['Read'],
                                                        'income-reports' => ['Read'],
                                                        'expense-reports' => ['Read'],
                                                        'stock-reports' => ['Read'],
                                                        'due-reports' => ['Read'],
                                                        'supplier-due-reports' => ['Read'],
                                                        'loss-profit-reports' => ['Read'],
                                                        'transaction-history-reports' => ['Read'],
                                                        'subscription-reports' => ['Read'],
                                                        'expired-product-reports' => ['Read'],
                                                    ];
                                                @endphp

                                                @foreach ($permissions as $module => $actions)
                                                    <tr>
                                                        <td class="text-start">{{ $loop->iteration }}</td>
                                                        <td class="text-nowrap text-start">
                                                            {{ ucfirst(str_replace('-', ' ', $module)) }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                @foreach ($actions as $action)
                                                                    @php
                                                                        $key = strtolower($action);
                                                                        $id = $module . '_' . $key;
                                                                        $name = 'permissions' . '[' . $module . ']' . '[' . $key . ']';
                                                                    @endphp

                                                                    <div
                                                                        class="custom-control custom-checkbox mr-3 me-lg-5 d-flex align-items-center gap-2">
                                                                        <input type="hidden" name="{{ $name }}" value="0">
                                                                        <input type="checkbox" name="{{ $name }}" value="1" class="custom-control-input delete-checkbox-item multi-delete" id="{{ $id }}">
                                                                        <label class="custom-control-label" for="{{ $id }}">
                                                                            {{ $action }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 text-center mt-2">
                                        <button type="reset" class="theme-btn border-btn m-2 ">{{ __('Reset') }}</button>
                                        <button type="submit" class="theme-btn m-2 submit-btn ">{{ __('Save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
