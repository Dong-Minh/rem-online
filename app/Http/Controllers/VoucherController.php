<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    protected VoucherService $voucherService;
    protected CartService $cartService;

    public function __construct(VoucherService $voucherService, CartService $cartService)
    {
        $this->voucherService = $voucherService;
        $this->cartService = $cartService;
    }

    /**
     * Áp dụng mã giảm giá vào giỏ hàng / checkout
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
        ]);

        $subtotal = $this->cartService->getSubtotal();
        if ($subtotal <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng của bạn đang trống hoặc chưa chọn sản phẩm.',
            ], 422);
        }

        $user = Auth::user();
        $result = $this->voucherService->applyVoucher($request->code, $subtotal, $user);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        $discountAmount = $result['discount_amount'];
        $newTotal = max(0, $subtotal - $discountAmount);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'voucher_code' => $result['voucher']->code,
            'discount_amount' => $discountAmount,
            'formatted_discount' => number_format($discountAmount, 0, ',', '.') . ' đ',
            'subtotal' => $subtotal,
            'formatted_subtotal' => number_format($subtotal, 0, ',', '.') . ' đ',
            'new_total' => $newTotal,
            'formatted_new_total' => number_format($newTotal, 0, ',', '.') . ' đ',
        ]);
    }

    /**
     * Gỡ bỏ mã giảm giá
     */
    public function remove(): JsonResponse
    {
        $this->voucherService->removeVoucher();
        $subtotal = $this->cartService->getSubtotal();

        return response()->json([
            'success' => true,
            'message' => 'Đã gỡ bỏ mã giảm giá thành công.',
            'discount_amount' => 0,
            'formatted_discount' => '0 đ',
            'subtotal' => $subtotal,
            'formatted_subtotal' => number_format($subtotal, 0, ',', '.') . ' đ',
            'new_total' => $subtotal,
            'formatted_new_total' => number_format($subtotal, 0, ',', '.') . ' đ',
        ]);
    }
}
