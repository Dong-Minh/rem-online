<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\PriceCalculator;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected CartService $cartService;
    protected VoucherService $voucherService;

    public function __construct(CartService $cartService, VoucherService $voucherService)
    {
        $this->cartService = $cartService;
        $this->voucherService = $voucherService;
    }

    /**
     * Hiển thị trang giỏ hàng
     */
    public function index()
    {
        $cart = $this->cartService->getCart();
        $cartItems = $cart->items()->with(['product.categories', 'color'])->get();
        $subtotal = $this->cartService->getSubtotal();
        $totalArea = $this->cartService->getTotalArea();

        $appliedVoucherData = $this->voucherService->getAppliedVoucher($subtotal, Auth::user());
        $voucherDiscount = $appliedVoucherData ? $appliedVoucherData['discount_amount'] : 0;
        $appliedVoucher = $appliedVoucherData ? $appliedVoucherData['voucher'] : null;

        return view('cart.index', compact('cart', 'cartItems', 'subtotal', 'totalArea', 'appliedVoucher', 'voucherDiscount'));
    }

    /**
     * Thêm sản phẩm rèm vào giỏ hàng (hoặc Mua ngay)
     */
    public function store(Request $request)
    {
        $product = Product::findOrFail($request->input('product_id'));

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:colors,id',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'quantity' => 'nullable|integer|min:1|max:99',
            'action_type' => 'nullable|string|in:add_to_cart,buy_now',
        ], [
            'color_id.required' => 'Vui lòng chọn màu sắc rèm trước khi đặt.',
            'width.required' => 'Vui lòng nhập chiều rộng rèm.',
            'width.numeric' => 'Chiều rộng phải là số (mét).',
            'height.required' => 'Vui lòng nhập chiều cao rèm.',
            'height.numeric' => 'Chiều cao phải là số (mét).',
        ]);

        // Kiểm tra giới hạn kích thước theo xưởng may
        $dimensionErrors = PriceCalculator::validateDimensions($product, (float) $request->width, (float) $request->height);
        if (!empty($dimensionErrors)) {
            return back()->withErrors(['dimensions' => implode(' ', $dimensionErrors)])->withInput();
        }

        $this->cartService->addToCart($validated);

        // Nếu là Mua ngay -> Chuyển đến trang Giỏ hàng hoặc Thanh toán
        if ($request->input('action_type') === 'buy_now') {
            return redirect()->route('cart.index')->with('success', 'Đã chuyển rèm vào giỏ hàng để bạn kiểm tra và thanh toán!');
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm rèm vào giỏ hàng thành công!');
    }

    /**
     * Cập nhật số lượng của một bộ rèm trong giỏ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $this->cartService->updateQuantity((int) $id, (int) $request->quantity);

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật số lượng rèm.');
    }

    /**
     * Xóa 1 bộ rèm khỏi giỏ hàng
     */
    public function destroy($id)
    {
        $this->cartService->removeItem((int) $id);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        $this->cartService->clearCart();

        return redirect()->route('cart.index')->with('success', 'Đã làm trống giỏ hàng.');
    }
}
