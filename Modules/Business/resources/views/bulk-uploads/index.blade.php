@extends('business::layouts.master')

@section('title')
    {{ __('Bulk Upload') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="border-0">
                <div class="card-bodys">
                    <form action="{{ route('business.bulk-uploads.store') }}" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload">
                        @csrf
                        <div class="bulk-upload-container">
                            <div class="d-flex justify-content-between align-items-center ">
                                <div class="bulk-input">
                                    <input class="form-control" type="file" name="file" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <button type="submit" class="add-order-btn rounded-2 border-0 submit-btn mt-3">{{__('Submit')}}</button>
                                <a href="{{ asset('assets/products_bulk_upload.xlsx') }}" download="products_bulk_upload.xlsx" class="download-file-btn mt-3"><i class="fas fa-download"></i>{{__('Download File')}}</a>
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
                                        <li><b>3.</b> {{__('After adding all your products, please save the file and then upload the updated version.')}}</li>
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
                                            <td class="text-start">{{__('Product Name')}} <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start"> {{__('The name of the product you are adding (e.g., "Banana").')}}</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td class="text-start">{{__('Product Category')}} <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">
                                                {{__('The category under which the product falls (e.g., Beverages, Electronics).')}}
                                                <br>
                                                <small>{{__('If the system does not find this, it will automatically create a new one.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td class="text-start">{{__('Unit Name')}}</td>
                                            <td class="text-start">
                                                {{__('The measurement unit used for the product (e.g., Piece, Kg, Litre, Box).')}}
                                                <br>
                                                <small>{{__('If the system does not find this, it will automatically create a new one.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td class="text-start">{{__('Medicine Type')}}</td>
                                            <td class="text-start">
                                                {{__('The type of medicine (e.g., Tablet, Syrup, Injection).')}}
                                                <br>
                                                <small>{{__('If the system does not find this, it will automatically create a new one.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td class="text-start">{{__('Strength')}}</td>
                                            <td class="text-start">
                                                {{__('The strength or dosage of the medicine (e.g., 500mg, 10ml, 250mg).')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td class="text-start">{{__('Generic Name')}}</td>
                                            <td class="text-start">
                                                {{__('The generic or chemical name of the medicine (e.g., Paracetamol, Amoxicillin).')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td class="text-start">{{__('Manufacturer Name')}}</td>
                                            <td class="text-start">
                                                {{__('The brand or manufacturer associated with the product (e.g., Samsung, Nestlé).')}}
                                                <br>
                                                <small>{{__('If the system does not find this, it will automatically create a new one.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td class="text-start">{{__('Box Size')}}</td>
                                            <td class="text-start">
                                                {{__('The size or quantity of items per box (e.g., 10 pieces, 20 strips).')}}
                                                <br>
                                                <small>{{__('If the system does not find this, it will automatically create a new one.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td class="text-start">{{__('Stock Quantity')}}</td>
                                            <td class="text-start"> {{__('The available quantity of the product currently in inventory.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>10</td>
                                            <td class="text-start">{{__('Product Code')}} <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">
                                                {{__('A unique code for the product, often used for barcodes or easy search.')}}
                                                <br>
                                                <small>{{__('Product codes must be unique. If a code is already in use and you include a non-unique product code in the file, the system will ignore that product.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>11</td>
                                            <td class="text-start">{{__('Purchase Price')}} <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">{{__('The cost price of the product without including VAT.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>12</td>
                                            <td class="text-start">{{__('Sale Price')}}(MRP) <span class="text-danger fw-bold">*</span></td>
                                            <td class="text-start">{{__('Maximum Retail Price — the highest price allowed to sell the product to customers.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>13</td>
                                            <td class="text-start">{{__('Dealer Price')}}</td>
                                            <td class="text-start">{{__('Special discounted price for resellers or dealers who buy in large quantities.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>14</td>
                                            <td class="text-start">{{__('Wholesale Price')}}</td>
                                            <td class="text-start">{{__('The price offered for bulk purchases, typically lower than the MRP.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>15</td>
                                            <td class="text-start">{{__('VAT Name')}}</td>
                                            <td>14</td>
                                            <td class="text-start">{{__('VAT Name')}}</td>
                                            <td class="text-start">
                                                {{__('The name of the VAT (Value Added Tax) applied to the product.')}}
                                                <br>
                                                <small>{{__('If the system does not find an existing VAT entry, it will automatically create a new one and set the rate based on the VAT (%) value you have provided in the column.')}}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>16</td>
                                            <td class="text-start">{{__('VAT')}} (%)</td>
                                            <td class="text-start">{{__('The percentage of VAT (Value Added Tax) applied to the product.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>17</td>
                                            <td class="text-start">{{__('Vat Type (exclusive / inclusive)')}}</td>
                                            <td class="text-start">{{__('Whether the VAT is added on top of the price (exclusive) or already included in the price (inclusive).')}}</td>
                                        </tr>
                                        <tr>
                                            <td>18</td>
                                            <td class="text-start">{{__('Low Stock Qty')}}</td>
                                            <td class="text-start">{{__('The quantity threshold to alert when stock is low (e.g., if stock drops below 10 units).')}}</td>
                                        </tr>
                                        <tr>
                                            <td>19</td>
                                            <td class="text-start">{{__('Expire Date')}}</td>
                                            <td class="text-start">{{__('The expiry date for the product, useful especially for perishable goods.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>20</td>
                                            <td class="text-start">{{__('Manufacturing Date')}}</td>
                                            <td class="text-start">{{__('The date when the product was manufactured.')}}</td>
                                        </tr>
                                        <tr>
                                            <td>21</td>
                                            <td class="text-start">{{__('Batch Number')}}</td>
                                            <td class="text-start">{{__('The batch number for tracking product batches.')}}</td>
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
