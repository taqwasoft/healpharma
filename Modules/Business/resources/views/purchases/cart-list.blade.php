@forelse($cart_contents as $cart)
    <tr class="product-cart-tr" data-row_id="{{ $cart->rowId }}" data-update_route="{{ route('business.carts.update', $cart->rowId) }}" data-destroy_route="{{ route('business.carts.destroy', $cart->rowId) }}">
        <td>
            <img src="{{ asset($cart->options->product_image ?? 'assets/images/logo/default-img.jpg') }}" alt="Img" class="table-product-img table-img">
        </td>
        <td class='text-start sales-purchase-td'>{{ $cart->name }}</td>
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
