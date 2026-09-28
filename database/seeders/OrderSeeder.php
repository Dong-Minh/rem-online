<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        if (!$customer) return;

        $products = Product::all();
        $colors = Color::all();
        $voucher10 = Voucher::where('code', 'VIPREM10')->first();
        $voucher50 = Voucher::where('code', 'REMMOI50K')->first();

        // Đơn 1: Đang may đo tại xưởng (Preparing)
        $order1 = Order::updateOrCreate(
            ['order_code' => 'ORD-20260908-MAY01'],
            [
                'user_id' => $customer->id,
                'recipient_name' => 'Nguyễn Thu Trang',
                'recipient_phone' => '0987654321',
                'recipient_email' => 'thutrang.nguyen@gmail.com',
                'shipping_address' => 'Căn hộ 1205 Tòa S2.03 Vinhomes Smart City, P. Tây Mỗ, Q. Nam Từ Liêm, Hà Nội',
                'subtotal' => 2450000,
                'promotion_discount' => 0,
                'voucher_discount' => 245000,
                'voucher_id' => $voucher10?->id,
                'shipping_fee' => 45000,
                'total' => 2250000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'status' => 'preparing',
                'shipping_status' => 'ready_to_pick',
                'ghn_order_code' => 'GHN-REM-8891',
                'ghn_total_fee' => 45000,
                'to_province_id' => 201,
                'to_district_id' => 1450,
                'to_ward_code' => '1A0707',
                'note' => 'Gia chủ muốn may dập ly 2 lớp, chiều cao tính sát sàn trừ 2cm.',
            ]
        );

        if ($order1->wasRecentlyCreated || $order1->items()->count() === 0) {
            $p1 = $products[0] ?? null;
            $c1 = $colors[1] ?? null;
            if ($p1) {
                OrderItem::create([
                    'order_id' => $order1->id,
                    'product_id' => $p1->id,
                    'product_name' => $p1->name,
                    'product_sku' => $p1->sku,
                    'color_id' => $c1?->id,
                    'color_name' => $c1?->name ?? 'Kem Sữa Sang Trọng',
                    'width' => 2.8,
                    'height' => 2.7,
                    'area' => 7.56,
                    'unit_price' => 380000,
                    'sale_unit_price' => 320000,
                    'quantity' => 1,
                    'item_total' => 7.56 * 320000,
                ]);
            }
        }

        // Đơn 2: Chờ xác nhận mới đặt (Pending)
        $order2 = Order::updateOrCreate(
            ['order_code' => 'ORD-20260908-XAC02'],
            [
                'user_id' => $customer->id,
                'recipient_name' => 'Trần Văn Mạnh',
                'recipient_phone' => '0912345678',
                'recipient_email' => 'manh.tran@yahoo.com',
                'shipping_address' => 'Số 45 Ngõ 192 Lê Trọng Tấn, P. Khương Mai, Q. Thanh Xuân, Hà Nội',
                'subtotal' => 1560000,
                'promotion_discount' => 0,
                'voucher_discount' => 50000,
                'voucher_id' => $voucher50?->id,
                'shipping_fee' => 35000,
                'total' => 1545000,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'status' => 'pending',
                'shipping_status' => 'not_shipped',
                'ghn_order_code' => 'GHN-REM-9922',
                'ghn_total_fee' => 35000,
                'to_province_id' => 201,
                'to_district_id' => 1488,
                'to_ward_code' => '10001',
                'note' => 'Giao hàng sau giờ hành chính giúp mình nhé.',
            ]
        );

        if ($order2->wasRecentlyCreated || $order2->items()->count() === 0) {
            $p2 = $products[1] ?? null;
            $c2 = $colors[3] ?? null;
            if ($p2) {
                OrderItem::create([
                    'order_id' => $order2->id,
                    'product_id' => $p2->id,
                    'product_name' => $p2->name,
                    'product_sku' => $p2->sku,
                    'color_id' => $c2?->id,
                    'color_name' => $c2?->name ?? 'Xám Đậm Cản Sáng',
                    'width' => 2.0,
                    'height' => 2.0,
                    'area' => 4.0,
                    'unit_price' => 450000,
                    'sale_unit_price' => 390000,
                    'quantity' => 1,
                    'item_total' => 4.0 * 390000,
                ]);
            }
        }

        // Đơn 3: Đã giao thành công & Hoàn tất (Completed)
        $order3 = Order::updateOrCreate(
            ['order_code' => 'ORD-20260907-HT003'],
            [
                'user_id' => $customer->id,
                'recipient_name' => 'Lê Hoàng Yến',
                'recipient_phone' => '0934567890',
                'recipient_email' => 'hoangyen@outlook.com',
                'shipping_address' => 'Biệt thự BT4-08 Khu Đô Thị Ngoại Giao Đoàn, P. Xuân Đỉnh, Q. Bắc Từ Liêm, Hà Nội',
                'subtotal' => 3680000,
                'promotion_discount' => 0,
                'voucher_discount' => 300000,
                'voucher_id' => $voucher10?->id,
                'shipping_fee' => 50000,
                'total' => 3430000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'status' => 'completed',
                'shipping_status' => 'delivered',
                'ghn_order_code' => 'L8WQFC',
                'ghn_total_fee' => 50000,
                'to_province_id' => 201,
                'to_district_id' => 1450,
                'to_ward_code' => '1A0707',
                'note' => 'Đã lắp đặt hoàn thiện và nghiệm thu phòng khách + phòng ngủ master.',
            ]
        );

        if ($order3->wasRecentlyCreated || $order3->items()->count() === 0) {
            $p3 = $products[2] ?? null;
            $c3 = $colors[0] ?? null;
            if ($p3) {
                OrderItem::create([
                    'order_id' => $order3->id,
                    'product_id' => $p3->id,
                    'product_name' => $p3->name,
                    'product_sku' => $p3->sku,
                    'color_id' => $c3?->id,
                    'color_name' => $c3?->name ?? 'Trắng Tinh Khôi',
                    'width' => 2.5,
                    'height' => 3.2,
                    'area' => 8.0,
                    'unit_price' => 520000,
                    'sale_unit_price' => 460000,
                    'quantity' => 1,
                    'item_total' => 8.0 * 460000,
                ]);
            }
        }
    }
}
