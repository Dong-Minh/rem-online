<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\GHNService;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    /**
     * Lấy danh sách Tỉnh/Thành từ GHN
     */
    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    /**
     * Lấy danh sách Quận/Huyện theo Tỉnh từ GHN
     */
    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    /**
     * Lấy danh sách Phường/Xã theo Quận/Huyện từ GHN
     */
    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    /**
     * Tính cước vận chuyển giao hàng GHN theo địa chỉ nhận
     */
    public function getShippingFee(Request $request, GHNService $ghn, CartService $cartService)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
        ]);

        // Tính trọng lượng tổng từ giỏ hàng hiện tại (1m² rèm ~ 400g, tối thiểu 1.5kg)
        $totalArea = $cartService->getTotalArea();
        $calcWeight = (int) round($totalArea * 400);
        $weight = max(1500, $calcWeight);

        $params = array_merge([
            'from_district_id' => (int) config('services.ghn.from_district_id', 1450),
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
        ], $ghn->packageParameters($weight));

        $feeResult = $ghn->calculateFee($params);

        return response()->json($feeResult);
    }
}
