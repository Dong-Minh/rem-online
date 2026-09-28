<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\MomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MomoPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Color $color;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::create([
            'name' => 'Rèm Vải Luxury',
            'slug' => 'rem-vai-luxury',
        ]);

        $this->product = Product::create([
            'name' => 'Rèm Gấm Hoàng Gia',
            'slug' => 'rem-gam-hoang-gia',
            'sku' => 'REM-GAM-001',
            'unit_price' => 450000,
            'pricing_type' => 'area',
            'status' => 'active',
            'is_featured' => true,
        ]);

        $this->color = Color::create([
            'name' => 'Vàng Đồng',
            'hex_code' => '#D4AF37',
        ]);
    }

    public function test_can_create_order_with_momo_and_create_payment_transaction(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->user);

        // Tạo giỏ hàng gắn với user
        $cart = \App\Models\Cart::create(['user_id' => $this->user->id]);
        \App\Models\CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
            'width' => 2.0,
            'height' => 2.5,
            'area' => 5.0,
            'unit_price' => 450000,
            'quantity' => 1,
        ]);

        // Tạo đơn hàng qua checkout
        $orderData = [
            'recipient_name' => 'Nguyen Van Test',
            'recipient_phone' => '0987654321',
            'recipient_email' => 'test@example.com',
            'to_province_id' => 201,
            'to_district_id' => 1450,
            'to_ward_code' => '1A0707',
            'specific_address' => '123 Đường Cầu Giấy',
            'province_name' => 'Hà Nội',
            'district_name' => 'Nam Từ Liêm',
            'ward_name' => 'Mỹ Đình 1',
            'payment_method' => 'momo',
            'estimated_shipping_fee' => 35000,
        ];

        // Mock MoMo & GHN HTTP calls
        Http::fake([
            '*ghn.vn*' => Http::response([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'total' => 35000,
                    'order_code' => 'GHN-TEST-123456',
                ],
            ], 200),
            '*momo.vn*' => Http::response([
                'resultCode' => 0,
                'message' => 'Success',
                'payUrl' => 'https://test-payment.momo.vn/v2/gateway/pay?token=mocktoken123',
                'orderId' => 'mock_order_123',
            ], 200),
        ]);

        $response = $this->post(route('checkout.process'), $orderData);

        // Đơn hàng được tạo thành công
        $this->assertDatabaseHas('orders', [
            'recipient_name' => 'Nguyen Van Test',
            'payment_method' => 'momo',
            'payment_status' => 'pending',
        ]);

        $order = Order::latest()->first();

        // Giao dịch PaymentTransaction được khởi tạo
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'gateway' => 'momo',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('orders.momo.start', $order->id));
    }

    public function test_momo_callback_marks_transaction_paid_and_updates_order(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-MOMO-TEST-01',
            'user_id' => $this->user->id,
            'recipient_name' => 'Nguyen Van Test',
            'recipient_phone' => '0987654321',
            'shipping_address' => '123 Đường Cầu Giấy, Hà Nội',
            'subtotal' => 500000,
            'shipping_fee' => 35000,
            'total' => 535000,
            'payment_method' => 'momo',
            'payment_status' => 'pending',
            'status' => 'pending',
            'shipping_status' => 'not_shipped',
        ]);

        $gatewayOrderId = $order->id . '_1_' . time();
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'momo',
            'gateway_order_id' => $gatewayOrderId,
            'amount' => 535000,
            'status' => 'initiated',
        ]);

        $accessKey = config('services.momo.access_key', 'klm05TvNBzhg7h7j');
        $secretKey = config('services.momo.secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $amount = '535000';
        $extraData = (string) $order->id;
        $message = 'Successful.';
        $orderInfo = 'Thanh toan don hang #' . $order->order_code;
        $orderType = 'momo_wallet';
        $partnerCode = 'MOMOBKUN20180529';
        $payType = 'qr';
        $requestId = (string) time();
        $responseTime = (string) time();
        $resultCode = '0';
        $transId = '999888777';

        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&message={$message}&orderId={$gatewayOrderId}&orderInfo={$orderInfo}&orderType={$orderType}&partnerCode={$partnerCode}&payType={$payType}&requestId={$requestId}&responseTime={$responseTime}&resultCode={$resultCode}&transId={$transId}";
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $callbackData = [
            'partnerCode' => $partnerCode,
            'orderId' => $gatewayOrderId,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderInfo' => $orderInfo,
            'orderType' => $orderType,
            'transId' => $transId,
            'resultCode' => $resultCode,
            'message' => $message,
            'payType' => $payType,
            'responseTime' => $responseTime,
            'extraData' => $extraData,
            'signature' => $signature,
        ];

        $response = $this->get(route('payment.momo.callback', $callbackData));

        // Kiểm tra redirect về trang chi tiết đơn hàng
        $response->assertRedirect(route('orders.show', $order->id));

        // Kiểm tra database: transaction đã paid, order payment_status = paid
        $this->assertDatabaseHas('payment_transactions', [
            'id' => $transaction->id,
            'status' => 'paid',
            'transaction_id' => $transId,
            'result_code' => 0,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);
    }
}
