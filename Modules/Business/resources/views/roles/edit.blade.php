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
                        <h4>{{ __('Edit Role') }}</h4>

                        <a href="{{ route('business.roles.index') }}"
                            class="add-order-btn rounded-2"><i class="far fa-list me-1" aria-hidden="true"></i> {{ __('View List') }}
                        </a>
                    </div>
                    <div class="row justify-content-center mt-2 roles-permissions p-16">
                        <div class="col-md-12">
                            <form action="{{ route('business.roles.update', $user->id) }}" method="post" class="row ajaxform_instant_reload">
                                @method('put')
                                @csrf

                                <div class="col-lg-4 form-group role-input-label">
                                    <label for="name" class="required">{{ __('Name') }}</label>
                                    <input type="text" name="name" value="{{ $user->name }}"class="form-control" placeholder="{{ __('Enter name') }}" required>
                                </div>

                                <div class="col-lg-4 form-group role-input-label">
                                    <label for="email" class="required">{{ __('Email') }}</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="{{ __('Enter email') }}" required>
                                </div>

                                <div class="col-lg-4 form-group role-input-label">
                                    <label for="password" class="required">{{ __('Update Password') }}</label>
                                    <input type="password" name="password" class="form-control" placeholder="{{ __('******') }}">
                                </div>

                                <div class="col-lg-12 mt-3">

                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th class="text-nowrap text-start">
                                                        {{ __('SL') }}
                                                    </th>

                                                    <th class="text-nowrap  text-start">
                                                        {{ __('Features') }}
                                                    </th>

                                                    <th class="text-start">
                                                        <div class="custom-control custom-checkbox d-flex align-items-center gap-2">
                                                            <input type="checkbox" class="custom-control-input delete-checkbox-item multi-delete"
                                                                id="selectAll">
                                                            <label class="custom-control-label "
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
                                                            {{ ucfirst(str_replace('-', ' ', $module)) }}
                                                        </td>
                                                        <td class="text-start">
                                                            <div class="d-flex">
                                                                @foreach ($actions as $action)
                                                                    @php
                                                                        $key = strtolower($action);
                                                                        $id = $module . '_' . $key;
                                                                        
                                                                        // Map nested keys to flat keys used by visible_permission()
                                                                        $permissionMap = [
                                                                            'sales.create' => 'salePermission',
                                                                            'sales.read' => 'salesListPermission',
                                                                            'sales.update' => 'salePermission',
                                                                            'sales.delete' => 'salePermission',
                                                                            'purchases.create' => 'purchasePermission',
                                                                            'purchases.read' => 'purchaseListPermission',
                                                                            'purchases.update' => 'purchasePermission',
                                                                            'purchases.delete' => 'purchasePermission',
                                                                            'products.create' => 'productPermission',
                                                                            'products.read' => 'productPermission',
                                                                            'products.update' => 'productPermission',
                                                                            'products.delete' => 'productPermission',
                                                                            'stocks.create' => 'stockPermission',
                                                                            'stocks.read' => 'stockPermission',
                                                                            'stocks.update' => 'stockPermission',
                                                                            'stocks.delete' => 'stockPermission',
                                                                            'parties.create' => 'partiesPermission',
                                                                            'parties.read' => 'partiesPermission',
                                                                            'parties.update' => 'partiesPermission',
                                                                            'parties.delete' => 'partiesPermission',
                                                                            'income.create' => 'addIncomePermission',
                                                                            'income.read' => 'addIncomePermission',
                                                                            'income.update' => 'addIncomePermission',
                                                                            'income.delete' => 'addIncomePermission',
                                                                            'expense.create' => 'addExpensePermission',
                                                                            'expense.read' => 'addExpensePermission',
                                                                            'expense.update' => 'addExpensePermission',
                                                                            'expense.delete' => 'addExpensePermission',
                                                                            'dues.read' => 'dueListPermission',
                                                                            'supplier-dues.read' => 'dueListPermission',
                                                                            'loss-profit.read' => 'lossProfitPermission',
                                                                            'transaction-history.read' => 'lossProfitPermission',
                                                                            'sale-reports.read' => 'reportsPermission',
                                                                            'sale-return-reports.read' => 'reportsPermission',
                                                                            'purchase-reports.read' => 'reportsPermission',
                                                                            'purchase-return-reports.read' => 'reportsPermission',
                                                                            'income-reports.read' => 'reportsPermission',
                                                                            'expense-reports.read' => 'reportsPermission',
                                                                            'stock-reports.read' => 'reportsPermission',
                                                                            'due-reports.read' => 'reportsPermission',
                                                                            'supplier-due-reports.read' => 'reportsPermission',
                                                                            'loss-profit-reports.read' => 'reportsPermission',
                                                                            'transaction-history-reports.read' => 'reportsPermission',
                                                                            'subscription-reports.read' => 'reportsPermission',
                                                                            'expired-product-reports.read' => 'reportsPermission',
                                                                        ];
                                                                        
                                                                        $mapKey = $module . '.' . $key;
                                                                        $flatKey = $permissionMap[$mapKey] ?? null;
                                                                        
                                                                        // Check both old nested format and new flat format for backward compatibility
                                                                        $is_checked = false;
                                                                        if ($flatKey && isset($user->visibility[$flatKey])) {
                                                                            $is_checked = $user->visibility[$flatKey] == 1;
                                                                        } elseif (isset($user->visibility[$module][$key])) {
                                                                            $is_checked = $user->visibility[$module][$key] == 1;
                                                                        }
                                                                        
                                                                        $name = $flatKey ? 'permissions[' . $flatKey . ']' : 'permissions[' . $module . '][' . $key . ']';
                                                                    @endphp

                                                                    <div class="custom-control custom-checkbox mr-3 me-lg-5 d-flex align-items-center gap-2">
                                                                        <input type="hidden" name="{{ $name }}" value="0">
                                                                        <input type="checkbox" name="{{ $name }}" value="1" class="custom-control-input delete-checkbox-item multi-delete" id="{{ $id }}" @checked($is_checked)>
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
                                        <button type="submit" class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
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
