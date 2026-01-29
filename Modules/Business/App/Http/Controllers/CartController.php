<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Gloudemans\Shoppingcart\Exceptions\InvalidRowIDException;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart_contents = Cart::content()->filter(fn($item) => $item->options->type == 'sale');
        $stockIds = $cart_contents->pluck('options.stock_id')->filter()->unique();
        $batchNos = Stock::whereIn('id', $stockIds)->pluck('batch_no', 'id');
        foreach ($cart_contents as $cartItem) {
            $stockId = $cartItem->options->stock_id ?? null;
            if ($stockId && isset($batchNos[$stockId])) {
                $newOptions = $cartItem->options->merge([
                    'batch_no' => $batchNos[$stockId],
                ]);
                Cart::update($cartItem->rowId, [
                    'qty' => $cartItem->qty,
                    'options' => $newOptions,
                ]);
            }
        }

        return view('business::sales.cart-list', compact('cart_contents'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'stock_id' => 'nullable|exists:stocks,id',
            'type' => 'nullable|string|in:sale,purchase',
            'id' => 'required|integer',
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'invoice_qty' => 'nullable|numeric|min:0',
            'bonus_qty' => 'nullable|numeric|min:0',
            'pack_size' => 'nullable|numeric|min:0',
            'pack_qty' => 'nullable|numeric|min:0',
            'gross_total_price' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'product_image' => 'nullable|string',
            'purchase_exclusive_price' => 'nullable|numeric|min:0',
            'purchase_inclusive_price' => 'nullable|numeric|min:0',
            'sales_price' => 'required_if:type,purchase|numeric|min:0',
            'profit_percent' => 'required_if:type,purchase|numeric',
            'wholesale_price' => 'required_if:type,purchase|numeric|min:0',
            'dealer_price' => 'required_if:type,purchase|numeric|min:0',
            'batch_no' => 'nullable|string|max:255',
            'expire_date' => 'nullable|date',
        ]);

        // Check for existing item in cart by type
        $existingCartItem = Cart::search(
            fn($item) => $item->id == $request->id &&
                $item->options->type == $request->type &&
                match ($request->type) {
                    'purchase' => $item->options->batch_no == $request->batch_no,
                    'sale' => $item->options->stock_id == $request->stock_id,
                    default => false,
                }
        )->first();

        if ($existingCartItem) {
            // Update the quantity of the existing item
            $newQuantity = $existingCartItem->qty + $request->quantity;
            Cart::update($existingCartItem->rowId, ['qty' => $newQuantity,]);
        } else {
            // Add new item to the cart
            $mainItemData = [
                'id' => $request->id,
                'name' => $request->name,
                'qty' => $request->quantity,
                'price' => $request->price, // sale or purchase price
                'options' => [
                    'type' => $request->type,
                    'stock_id' => $request->stock_id,
                    'cost' => $request->purchase_inclusive_price ?? $request->purchase_exclusive_price ?? 0,
                    'purchase_without_tax' => $request->purchase_exclusive_price,
                    'purchase_with_tax' => $request->purchase_inclusive_price,
                    'invoice_qty' => $request->invoice_qty ?? 0,
                    'bonus_qty' => $request->bonus_qty ?? 0,
                    'pack_size' => $request->pack_size ?? null,
                    'pack_qty' => $request->pack_qty ?? 0,
                    'gross_total_price' => $request->gross_total_price ?? 0,
                    'vat_amount' => $request->vat_amount ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'net_total' => ($request->gross_total_price + $request->vat_amount) - $request->discount_amount,
                    'batch_no' => $request->batch_no ?? null,
                    'expire_date' => $request->expire_date ?? null,
                    'product_unit_name' => $request->product_unit_name ?? null,
                    'product_image' => $request->product_image,
                    'wholesale_price' => $request->wholesale_price,
                    'dealer_price' => $request->dealer_price,
                    'sales_price' => $request->sales_price,
                    'profit_percent' => $request->profit_percent,
                ]
            ];

            Cart::add($mainItemData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully!'
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $cart = Cart::get($id);

            if ($cart) {
                $quantity = $request->input('qty');
                $price = $request->input('price'); // If sale

                if ($quantity >= 0) {
                    // Update the cart
                    Cart::update($id, [
                        'qty' => $request->qty ?? $cart->qty,
                        'price' => $request->purchase_inclusive_price ?? $cart->price,
                        'options' => [
                            'type' => $cart->options->type,
                            'stock_id' => $request->stock_id ?? $cart->options->stock_id,
                            'batch_no' => $request->batch_no ?? $cart->options->batch_no,
                            'expire_date' => $request->expire_date ?? $cart->options->expire_date,
                            'product_unit_name' => $cart->options->product_unit_name,
                            'product_image' => $cart->options->product_image,
                            'cost' => $request->purchase_inclusive_price ?? $request->purchase_exclusive_price ?? $cart->options->cost ?? 0,
                            'purchase_without_tax' => $request->purchase_exclusive_price ?? $cart->options->purchase_without_tax ?? 0,
                            'purchase_with_tax' => $request->purchase_inclusive_price ?? $cart->options->purchase_with_tax ?? 0,
                            'invoice_qty' => $request->invoice_qty ?? $cart->options->invoice_qty ?? 0,
                            'bonus_qty' => $request->bonus_qty ?? $cart->options->bonus_qty ?? 0,
                            'pack_size' => $request->pack_size ?? $cart->options->pack_size ?? null,
                            'pack_qty' => $request->pack_qty ?? $cart->options->pack_qty ?? 0,
                            'gross_total_price' => $request->gross_total_price ?? $cart->options->gross_total_price ?? 0,
                            'vat_amount' => $request->vat_amount ?? $cart->options->vat_amount ?? 0,
                            'discount_amount' => $request->discount_amount ?? $cart->options->discount_amount ?? 0,
                            'net_total' => (($request->gross_total_price ?? 0) + ($request->vat_amount ?? 0)) - ($request->discount_amount ?? 0),
                            'wholesale_price' => $request->wholesale_price ?? $cart->options->wholesale_price ?? 0,
                            'dealer_price' => $request->dealer_price ?? $cart->options->dealer_price ?? 0,
                            'sales_price' => $request->sales_price ?? $cart->options->sales_price ?? 0,
                            'profit_percent' => $request->profit_percent ?? $cart->options->profit_percent ?? 0,
                        ]
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => __('Cart item updated successfully')
                    ]);
                } else {
                    return response()->json(['success' => false, 'message' => __('Enter a valid quantity')]);
                }
            } else {
                return response()->json(['success' => false, 'message' => __('Item not found in the cart')]);
            }
        } catch (InvalidRowIDException $e) {
            return response()->json(['success' => false, 'message' => __('The cart does not contain this item')]);
        }
    }

    public function destroy($id)
    {
        try {
            Cart::remove($id);
            return response()->json(['success' => true, 'message' => __('Item removed from cart')]);
        } catch (InvalidRowIDException $e) {
            return response()->json(['success' => false, 'message' => __('The cart does not contain this item')]);
        }
    }

    public function removeAllCart(Request $request)
    {
        $carts = Cart::content();

        if ($carts->count() < 1) {
            return response()->json(['message' => __('Cart is empty. Add items first!')]);
        }

        Cart::destroy();

        $response = [
            'success' => true,
            'message' => __('All cart removed successfully!'),
        ];

        return response()->json($response);
    }
}
