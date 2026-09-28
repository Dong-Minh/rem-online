<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    protected string $baseUrl;
    protected string $token;
    protected int $shopId;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.base_url', 'https://dev-online-gateway.ghn.vn/shiip/public-api');
        $this->token = config('services.ghn.token') ?? '';
        $this->shopId = (int) config('services.ghn.shop_id', 0);
        $this->verifySsl = filter_var(config('services.ghn.verify_ssl', false), FILTER_VALIDATE_BOOLEAN);
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => $this->verifySsl,
            ])
            ->acceptJson()
            ->timeout(15)
            ->withHeaders([
                'Token' => $this->token,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ]);
    }

    /**
     * Lấy danh sách Tỉnh/Thành phố từ GHN
     */
    public function getProvinces(): array
    {
        return $this->get('/master-data/province');
    }

    /**
     * Lấy danh sách Quận/Huyện theo Tỉnh từ GHN
     */
    public function getDistricts(int $provinceId): array
    {
        return $this->get('/master-data/district', [
            'province_id' => $provinceId,
        ]);
    }

    /**
     * Lấy danh sách Phường/Xã theo Quận/Huyện từ GHN
     */
    public function getWards(int $districtId): array
    {
        return $this->get('/master-data/ward', [
            'district_id' => $districtId,
        ]);
    }

    /**
     * Tham số gói hàng chuẩn (mặc định rèm tính khoảng 1.5kg - 3kg tùy đơn)
     */
    public function packageParameters(int $weight = 1500): array
    {
        return [
            'service_type_id' => 2, // Giao hàng chuẩn
            'insurance_value' => 0,
            'coupon' => null,
            'weight' => $weight > 0 ? $weight : 1500,
            'length' => 30, // cm
            'width' => 20,  // cm
            'height' => 15, // cm
        ];
    }

    /**
     * Tính phí giao hàng GHN
     */
    public function calculateFee(array $params): array
    {
        return $this->post('/v2/shipping-order/fee', array_merge([
            'shop_id' => $this->shopId,
        ], $params));
    }

    /**
     * Tạo đơn vận chuyển giao hàng trên GHN
     */
    public function createOrder(array $orderData): array
    {
        return $this->post('/v2/shipping-order/create', array_merge([
            'shop_id' => $this->shopId,
        ], $orderData));
    }

    /**
     * Hủy đơn vận chuyển trên GHN
     */
    public function cancelOrder(array $orderCodes): array
    {
        return $this->post('/v2/switch-status/cancel', [
            'order_codes' => $orderCodes,
            'shop_id' => $this->shopId,
        ]);
    }

    protected function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client()->get($uri, $query);

            if (!$response->successful()) {
                Log::warning('GHN GET request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return ['code' => $response->status(), 'message' => 'GHN API request failed.'];
            }

            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.'];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN.'];
        }
    }

    protected function post(string $uri, array $payload): array
    {
        try {
            $response = $this->client()->post($uri, $payload);

            if (!$response->successful()) {
                Log::warning('GHN POST request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return ['code' => $response->status(), 'message' => 'GHN API request failed.'];
            }

            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.'];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN.'];
        }
    }
}
