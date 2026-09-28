<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Lấy hoặc tạo giỏ hàng hiện tại (Database cho user đã đăng nhập, Session cho khách)
     */
    public function getCart()
    {
        try {
            if (Auth::check()) {
                return Cart::firstOrCreate(['user_id' => Auth::id()]);
            }

            // Với khách vãng lai, lưu session_id
            $sessionId = Session::getId();
            if (empty($sessionId)) {
                return null;
            }
            return Cart::firstOrCreate(['session_id' => $sessionId]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Thêm sản phẩm rèm vào giỏ hàng
     */
    public function addToCart(array $data)
    {
        $product = Product::findOrFail($data['product_id']);
        $color = isset($data['color_id']) ? Color::find($data['color_id']) : null;
        
        $width = (float) $data['width'];
        $height = (float) $data['height'];
        $quantity = (int) ($data['quantity'] ?? 1);

        // Backend tự tính toán diện tích và lấy đơn giá từ DB để bảo mật
        $area = PriceCalculator::calculateArea($width, $height);
        $unitPrice = (float) $product->unit_price;
        $saleUnitPrice = $product->sale_price ? (float) $product->sale_price : null;

        $cart = $this->getCart();

        // Kiểm tra xem đã có item tương tự trong giỏ chưa (cùng product, color, width, height)
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('color_id', $color ? $color->id : null)
            ->where('width', $width)
            ->where('height', $height)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->save();
            return $existingItem;
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'color_id' => $color ? $color->id : null,
            'width' => $width,
            'height' => $height,
            'area' => $area,
            'unit_price' => $unitPrice,
            'sale_unit_price' => $saleUnitPrice,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Cập nhật số lượng của một item trong giỏ
     */
    public function updateQuantity(int $cartItemId, int $quantity)
    {
        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->firstOrFail();

        if ($quantity <= 0) {
            $item->delete();
            return null;
        }

        $item->quantity = $quantity;
        $item->save();
        return $item;
    }

    /**
     * Xóa một item khỏi giỏ
     */
    public function removeItem(int $cartItemId)
    {
        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->firstOrFail();
        return $item->delete();
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        $cart = $this->getCart();
        return CartItem::where('cart_id', $cart->id)->delete();
    }

    /**
     * Lấy tổng số lượng sản phẩm trong giỏ (dùng cho badge Header)
     */
    public function getItemCount(): int
    {
        try {
            $cart = $this->getCart();
            if (!$cart) {
                return 0;
            }
            return (int) CartItem::where('cart_id', $cart->id)->sum('quantity');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Tính tổng diện tích toàn bộ giỏ hàng (m²)
     */
    public function getTotalArea(): float
    {
        $cart = $this->getCart();
        $items = CartItem::where('cart_id', $cart->id)->get();
        
        $totalArea = 0;
        foreach ($items as $item) {
            $totalArea += $item->area * $item->quantity;
        }

        return round($totalArea, 2);
    }

    /**
     * Tính tổng tiền tạm tính của giỏ hàng (VNĐ)
     */
    public function getSubtotal(): float
    {
        $cart = $this->getCart();
        $items = CartItem::where('cart_id', $cart->id)->get();

        $subtotal = 0;
        foreach ($items as $item) {
            $effectivePrice = $item->sale_unit_price ?? $item->unit_price;
            $subtotal += $item->area * $effectivePrice * $item->quantity;
        }

        return round($subtotal, 2);
    }

    /**
     * Gộp giỏ hàng session của khách vào giỏ hàng user khi đăng nhập
     */
    public function mergeSessionCartToUser($user)
    {
        $sessionId = Session::getId();
        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if (! $sessionCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        $sessionItems = CartItem::where('cart_id', $sessionCart->id)->get();

        foreach ($sessionItems as $item) {
            $existingItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $item->product_id)
                ->where('color_id', $item->color_id)
                ->where('width', $item->width)
                ->where('height', $item->height)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $item->quantity;
                $existingItem->save();
            } else {
                $item->cart_id = $userCart->id;
                $item->save();
            }
        }

        $sessionCart->delete();
    }
}
