<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class GHNOrderService
{
    public function __construct(private GHNService $ghn)
    {
    }

    /**
     * Tạo vận đơn GHN từ Order của hệ thống rèm
     */
    public function create(Order $order, bool $isPaid = false): array
    {
        $items = [];
        $totalWeight = 0;

        foreach ($order->items as $item) {
            // Quy đổi trọng lượng rèm: 1m² vải ~ 400g + phụ kiện
            $areaWeight = (int) round(($item->area ?? 2.0) * 400);
            $itemWeight = max(500, $areaWeight);
            $totalWeight += $itemWeight * (int) $item->quantity;

            $items[] = [
                'name' => $item->product_name ?? 'Bộ Rèm Cửa May Đo',
                'code' => $item->product_sku ?? 'REM-' . $item->id,
                'quantity' => (int) $item->quantity,
                'price' => (int) ($item->sale_unit_price ?? $item->unit_price),
                'weight' => $itemWeight,
                'category' => [
                    'level1' => 'Rèm Cửa & Nội Thất',
                ],
            ];
        }

        $weight = $totalWeight > 0 ? $totalWeight : 1500;

        $payload = [
            'payment_type_id' => 2, // Người mua/Người bán thanh toán
            'note' => 'Đơn hàng rèm may đo #' . $order->order_code . ' - ' . ($order->note ?? 'Hàng rèm dễ nhăn, nhẹ tay'),
            'required_note' => 'KHONGCHOXEMHANG',
            'return_phone' => '0901234567',
            'return_address' => config('services.ghn.from_address', '322/76 Ngách 76 Ngõ 322 Mỹ Đình, Phường Mỹ Đình 1, Quận Nam Từ Liêm, Hà Nội'),
            'return_district_id' => (int) config('services.ghn.from_district_id', 1450),
            'return_ward_code' => (string) config('services.ghn.from_ward_code', '1A0707'),
            'to_name' => $order->recipient_name,
            'to_phone' => $order->recipient_phone,
            'to_address' => $order->shipping_address,
            'to_ward_code' => (string) $order->to_ward_code,
            'to_district_id' => (int) $order->to_district_id,
            'cod_amount' => ($order->payment_method === 'cod' && $order->payment_status !== 'paid') ? (int) $order->total : 0,
            'weight' => $weight,
            'length' => 40,
            'width' => 25,
            'height' => 15,
            'service_type_id' => 2, // Chuẩn
            'items' => $items,
        ];

        $response = $this->ghn->createOrder($payload);

        // Nếu tạo thành công trên GHN -> cập nhật mã vận đơn vào đơn hàng
        if (isset($response['code']) && $response['code'] === 200 && isset($response['data']['order_code'])) {
            $order->update([
                'ghn_order_code' => $response['data']['order_code'],
                'ghn_total_fee' => (int) ($response['data']['total_fee'] ?? $order->shipping_fee),
                'shipping_status' => 'ready_to_pick',
            ]);
        } else {
            Log::warning('Tạo vận đơn GHN không thành công:', ['order_id' => $order->id, 'response' => $response]);
        }

        return $response;
    }
}
