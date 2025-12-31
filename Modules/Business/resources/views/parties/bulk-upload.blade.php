@extends('business::layouts.master')

@section('title')
    {{ __('Bulk Upload') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="border-0">
                <div class="card-bodys">
                    <form action="{{ route('business.parties.bulk-store') }}" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload">
                        @csrf

                        @php
                            $type = ucfirst(request('type'));
                        @endphp

                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="bulk-upload-container">
                            <div class="d-flex justify-content-between align-items-center ">
                                <div class="bulk-input">
                                    <input class="form-control" type="file" name="file" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <button type="submit" class="add-order-btn rounded-2 border-0 submit-btn mt-3">{{__('Submit')}}</button>
                                <a href="{{ asset('assets/parties_bulk_upload.xlsx') }}" download="parties_bulk_upload.xlsx" class="download-file-btn mt-3"><i class="fas fa-download"></i>{{__('Download File')}}</a>
                            </div>
                        </div>
                        <div class="bulk-upload-container mt-3">
                            <div class="instruction-header">
                                <h5>{{__('Instructions')}}</h5>
                                <div class="mt-3">
                                    <h6><strong>{{__('Note')}}: </strong> {{__('Please follow the instructions below to upload your file.')}}</h6>
                                    <ul>
                                        <li><b>1.</b> {{__('Download the sample file first and add all your products to it.')}}</li>
                                        <li><b>2.</b> <span class="text-danger">*</span> {{__('Indicates a required field. If you do not provide the required fields, the system will ignore the product.')}}</li>
                                        <li><b>3.</b> {{__('After adding all your parties, please save the file and then upload the updated version.')}}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="responsive-table mt-4">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th>{{ __('SL') }}.</th>
                                        <th class="text-start">{{ __('Field Name') }}</th>
                                        <th class="text-start">{{ __('Description') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="business-category-data">
                                    <tr>
                                        <td>1</td>
                                        <td class="text-start">{{__('Party Name')}} <span class="text-danger fw-bold">*</span></td>
                                        <td class="text-start">{{__('Enter the official name of the party you are adding.')}}</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td class="text-start">{{__('Party Type')}} <span class="text-danger fw-bold">*</span></td>
                                        <td class="text-start">{{__('Select the type of party. (For Customers: Retailer / Wholesaler / Dealer; For Supplier: Supplier)')}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td class="text-start">{{__('Phone')}}</td>
                                        <td class="text-start">{{__('Enter the phone number of the party (optional).')}}</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td class="text-start">{{__('Email')}}</td>
                                        <td class="text-start">{{__('Enter a valid email address for the party (optional).')}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td class="text-start">{{__('Due')}}</td>
                                        <td class="text-start">{{__('Enter the due amount for this party (optional).')}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
