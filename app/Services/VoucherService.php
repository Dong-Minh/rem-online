<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;

class VoucherService
{
    /**
     * Kiểm tra tính hợp lệ của mã Voucher
     *
     * @param string $code
     * @param float $subtotal
     * @param User|null $user
     * @return array
     */
    public function validateVoucher(string $code, float $subtotal, ?User $user = null): array
    {
        $code = strtoupper(trim($code));
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại trong hệ thống.',
            ];
        }

        if (!$voucher->is_active) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá này hiện đang tạm ngưng áp dụng.',
            ];
        }

        if (now()->lt($voucher->starts_at)) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá chưa đến đợt áp dụng (Bắt đầu từ: ' . $voucher->starts_at->format('d/m/Y H:i') . ').',
            ];
        }

        if (now()->gt($voucher->ends_at)) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá đã hết hạn sử dụng.',
            ];
        }

        if ($voucher->hasReachedLimit()) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá này đã hết lượt sử dụng trên hệ thống.',
            ];
        }

        if ($user && $voucher->hasUserReachedLimit($user)) {
            return [
                'valid' => false,
                'message' => 'Bạn đã sử dụng tối đa ' . $voucher->usage_per_user . ' lần cho mã giảm giá này.',
            ];
        }

        if ($subtotal < $voucher->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng rèm cần đạt tối thiểu ' . number_format($voucher->min_order_amount, 0, ',', '.') . 'đ để sử dụng mã này (Hiện tại: ' . number_format($subtotal, 0, ',', '.') . 'đ).',
            ];
        }

        $discount = $this->calculateDiscount($voucher, $subtotal);

        return [
            'valid' => true,
            'voucher' => $voucher,
            'discount_amount' => $discount,
            'formatted_discount' => number_format($discount, 0, ',', '.') . ' đ',
            'message' => 'Áp dụng mã ' . $voucher->code . ' thành công! ' . $voucher->discount_label,
        ];
    }

    /**
     * Tính toán số tiền thực tế được giảm giá
     *
     * @param Voucher $voucher
     * @param float $subtotal
     * @return float
     */
    public function calculateDiscount(Voucher $voucher, float $subtotal): float
    {
        $discount = 0;

        if ($voucher->discount_type === 'percent') {
            $discount = ($subtotal * (float) $voucher->discount_value) / 100;
            if ($voucher->max_discount_amount && $discount > (float) $voucher->max_discount_amount) {
                $discount = (float) $voucher->max_discount_amount;
            }
        } else {
            $discount = min((float) $voucher->discount_value, $subtotal);
        }

        return max(0, round($discount));
    }

    /**
     * Lấy thông tin voucher đang áp dụng trong session (nếu có)
     *
     * @param float $subtotal
     * @param User|null $user
     * @return array|null
     */
    public function getAppliedVoucher(float $subtotal, ?User $user = null): ?array
    {
        $code = session('applied_voucher_code');
        if (!$code) {
            return null;
        }

        $result = $this->validateVoucher($code, $subtotal, $user);
        if (!$result['valid']) {
            $this->removeVoucher();
            return null;
        }

        return $result;
    }

    /**
     * Lưu mã voucher vào session khi áp dụng hợp lệ
     *
     * @param string $code
     * @param float $subtotal
     * @param User|null $user
     * @return array
     */
    public function applyVoucher(string $code, float $subtotal, ?User $user = null): array
    {
        $result = $this->validateVoucher($code, $subtotal, $user);
        if ($result['valid']) {
            session(['applied_voucher_code' => $result['voucher']->code]);
        }
        return $result;
    }

    /**
     * Hủy voucher khỏi session
     */
    public function removeVoucher(): void
    {
        session()->forget('applied_voucher_code');
    }

    /**
     * Ghi nhận lịch sử sử dụng voucher khi tạo đơn hàng
     *
     * @param Voucher $voucher
     * @param User $user
     * @param Order $order
     * @param float $discountAmount
     * @return VoucherUsage
     */
    public function recordUsage(Voucher $voucher, User $user, Order $order, float $discountAmount): VoucherUsage
    {
        return VoucherUsage::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);
    }
}
