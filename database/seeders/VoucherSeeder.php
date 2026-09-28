<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'REMMOI50K',
                'description' => 'Mã giảm giá 50.000đ chào mừng khách hàng mới',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'max_discount_amount' => null,
                'min_order_amount' => 500000,
                'usage_limit' => 500,
                'usage_per_user' => 2,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'VIPREM10',
                'description' => 'Chiết khấu 10% (Tối đa 300.000đ) cho đơn may rèm trọn gói từ 2 triệu',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'max_discount_amount' => 300000,
                'min_order_amount' => 2000000,
                'usage_limit' => 200,
                'usage_per_user' => 1,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'GIAM100K',
                'description' => 'Giảm ngay 100.000đ cho đơn hàng rèm từ 1.500.000đ',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'max_discount_amount' => null,
                'min_order_amount' => 1500000,
                'usage_limit' => 100,
                'usage_per_user' => 1,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'SIEUSALE20',
                'description' => 'Đại tiệc siêu sale giảm 20% (Tối đa 500.000đ) cho đơn từ 3 triệu',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'max_discount_amount' => 500000,
                'min_order_amount' => 3000000,
                'usage_limit' => 50,
                'usage_per_user' => 1,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addMonths(1),
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::updateOrCreate(['code' => $voucher['code']], $voucher);
        }
    }
}
