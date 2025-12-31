@extends('business::layouts.master')

@section('title')
    {{ __('Taxes') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm taxlist-body taxlist-container">
                <div>
                    <div class="table-header p-16">
                        <h4>{{ __('Tax List') }}</h4>
                        <div>
                            <a href="#tax-add-modal" class="theme-btn print-btn text-light active custom-a" data-bs-toggle="modal"> <i class="fas fa-plus-circle me-1"></i> {{ __('Add New Tax') }}</a>
                        </div>
                    </div>
                    <div class="table-top-form">
                        <form action="{{ route('business.taxes.filter') }}" method="post" class="filter-form" table="#tax-data">
                        @csrf
                        <div class="table-search position-relative d-print-none ">
                            <input class="form-control" type="text" name="search" placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
                            <span class="position-absolute">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                    </svg>

                            </span>
                        </div>
                    </form>
                    </div>
                    <div class="delete-item delete-show d-none mx-3">
                        <div class="delete-item-show m-0 mt-3">
                            <p class="fw-bold"><span class="selected-count"></span> {{ __('items show') }}</p>
                            <button data-bs-toggle="modal" class="trigger-modal" data-bs-target="#multi-delete-modal" data-url="{{ route('business.taxes.deleteAll') }}">{{ __('Delete') }}</button>
                        </div>
                    </div>
                </div>
                <div class="responsive-table taxlist-body table-container tax-list-table">
                    <table class="table" id="datatable">
                        <thead>
                            <tr>
                                <th class="w-60 table-header-content">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="checkbox" class="select-all-delete multi-delete">
                                    </div>
                                </th>
                                <th class="w-60 table-header-content">{{ __('SL') }}.</th>
                                <th class="table-header-content">{{ __('Name') }}</th>
                                <th class="table-header-content">{{ __('Tax Rate') }}</th>
                                <th class="table-header-content">{{ __('Status') }}</th>
                                <th class="text-center table-header-content">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="tax-data">
                            @include('business::taxes.datas')
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $taxes->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>

        @include('business::tax-groups.index')
    </div>
@endsection

@push('modal')
    @include('business::component.delete-modal')
    @include('business::taxes.create')
    @include('business::taxes.edit')
@endpush

