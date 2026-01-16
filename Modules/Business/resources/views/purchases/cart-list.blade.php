@forelse($cart_contents as $cart)
    @php
        // Get box size from product and cart
        $product = \App\Models\Product::with('box_size:id,name,value')->find($cart->id);
        $packSizes = [];
        if ($product && $product->box_size && $product->box_size->value) {
            $packSizes[] = $product->box_size->value;
        }
        
        // Get box size name for display
        $boxSizeName = 'N/A';
        if ($cart->options->pack_size) {
            $boxSize = \App\Models\BoxSize::where('value', $cart->options->pack_size)->first();
            $boxSizeName = $boxSize ? $boxSize->name : $cart->options->pack_size;
        }
    @endphp
    <tr class="product-cart-tr" 
        data-row_id="{{ $cart->rowId }}" 
        data-update_route="{{ route('business.carts.update', $cart->rowId) }}" 
        data-destroy_route="{{ route('business.carts.destroy', $cart->rowId) }}"
        data-product_id="{{ $cart->id }}"
        data-product_name="{{ $cart->name }}"
        data-product_image="{{ $cart->options->product_image ?? '' }}"
        data-stock="{{ $cart->options->stock ?? 0 }}"
        data-pack_sizes="{{ json_encode($packSizes) }}"
        data-pack_size="{{ $cart->options->pack_size ?? null }}"
        data-pack_qty="{{ $cart->options->pack_qty ?? 0 }}"
        data-invoice_qty="{{ $cart->options->invoice_qty ?? 0 }}"
        data-bonus_qty="{{ $cart->options->bonus_qty ?? 0 }}"
        data-product_qty="{{ $cart->qty }}"
        data-gross_total_price="{{ $cart->options->gross_total_price ?? 0 }}"
        data-vat_amount="{{ $cart->options->vat_amount ?? 0 }}"
        data-discount_amount="{{ $cart->options->discount_amount ?? 0 }}"
        data-purchase_exclusive_price="{{ $cart->options->purchase_without_tax ?? 0 }}"
        data-purchase_inclusive_price="{{ $cart->options->purchase_with_tax ?? 0 }}"
        data-profit_percent="{{ $cart->options->profit_percent ?? 0 }}"
        data-sales_price="{{ $cart->options->sales_price ?? 0 }}"
        data-wholesale_price="{{ $cart->options->wholesale_price ?? 0 }}"
        data-dealer_price="{{ $cart->options->dealer_price ?? 0 }}"
        data-batch_no="{{ $cart->options->batch_no ?? '' }}"
        data-expire_date="{{ $cart->options->expire_date ?? '' }}">
        <td>
            <img src="{{ asset($cart->options->product_image ?? 'assets/images/logo/default-img.jpg') }}" alt="Img" class="table-product-img table-img">
        </td>
        <td class='text-start sales-purchase-td'>
            {{ $cart->name }}
            @if($cart->options->pack_size || $cart->options->pack_qty)
                <br><small style="color: #666;">Pack Size: {{ $boxSizeName }}, Pack Qty: {{ $cart->options->pack_qty ?? 'N/A' }}</small>
            @endif
        </td>
        <td class='text-center sales-purchase-td'>{{ $cart->options->batch_no ?? 'N/A' }}</td>
        <td class='text-center sales-purchase-td'>{{ currency_format($cart->price, 'icon', 2, business_currency()) }}</td>
        <td class='text-start sales-purchase-td'>
            <div class="d-flex align-items-center justify-content-center gap-2">
                <button class="incre-decre minus-btn">
                    <i class="fas fa-minus icon"></i>
                </button>
                <input type="number" step="any" value="{{ $cart->qty }}" class="custom-number-input cart-qty" placeholder="{{ __('0') }}">
                <button class="incre-decre plus-btn">
                    <i class="fas fa-plus icon"></i>
                </button>
            </div>
        </td>
        <td class="cart-subtotal sales-purchase-td">{{ currency_format($cart->subtotal, 'icon', 2, business_currency()) }}</td>
        <td class="sales-purchase-td">
            <button class='edit-cart-btn' style="border: none; background: none; cursor: pointer; margin-right: 10px;" title="Edit">
                <i class="fas fa-edit" style="color: #4CAF50; font-size: 18px;"></i>
            </button>
            <button class='x-btn remove-btn'>
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.5 6L6.5 18" stroke="#E13F3D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6.5 6L18.5 18" stroke="#E13F3D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="border-0 py-5">
            <div class="no-item-container">
                <img src="{{ asset('assets/images/icons/cart.svg') }}" alt="icon">
                <h3>{{ __('No items found') }}</h3>
            </div>
        </td>
    </tr>
@endforelse
