<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Khách Hàng A',
            'email' => 'customer@remonline.vn',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Quản Trị Viên',
            'email' => 'admin@remonline.vn',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_finance_index()
    {
        Order::create([
            'order_code' => 'ORD-10001',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Nguyễn Văn Test',
            'recipient_phone' => '0987654321',
            'shipping_address' => '123 Đường Cầu Giấy, Hà Nội',
            'subtotal' => 1500000,
            'shipping_fee' => 30000,
            'total' => 1530000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.index'));

        $response->assertStatus(200);
        $response->assertSee('Thống Kê Tài Chính');
        $response->assertSee('Tổng giá trị đơn hàng');
        $response->assertSee('1.530.000');
    }

    public function test_admin_can_view_finance_transactions()
    {
        $order = Order::create([
            'order_code' => 'ORD-10002',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Hoàng Minh Châu',
            'recipient_phone' => '0912345678',
            'shipping_address' => '456 Lê Duẩn, Đà Nẵng',
            'subtotal' => 2500000,
            'shipping_fee' => 40000,
            'total' => 2540000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.transactions'));

        $response->assertStatus(200);
        $response->assertSee('Hoàng Minh Châu');
        $response->assertSee('2.540.000');
        $response->assertSee('ORD-10002');
    }

    public function test_admin_can_update_cod_status_to_paid()
    {
        $order = Order::create([
            'order_code' => 'ORD-10003',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Trần Thị Thu',
            'recipient_phone' => '0905123456',
            'shipping_address' => '789 Nguyễn Huệ, TP.HCM',
            'subtotal' => 3000000,
            'shipping_fee' => 50000,
            'total' => 3050000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.finance.update-status', $order->id), [
            'payment_status' => 'paid',
            'current_payment_status' => 'pending',
            'current_order_status' => 'pending',
            'current_payment_id' => 0,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'status' => 'paid',
            'gateway' => 'cod',
        ]);
    }

    public function test_filter_finance_by_search_and_payment_status()
    {
        Order::create([
            'order_code' => 'ORD-MATCH',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Hàng Đặc Biệt',
            'recipient_phone' => '0999888777',
            'shipping_address' => 'Hà Nội',
            'subtotal' => 5000000,
            'shipping_fee' => 0,
            'total' => 5000000,
            'payment_method' => 'momo',
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        Order::create([
            'order_code' => 'ORD-OTHER',
            'user_id' => $this->customer->id,
            'recipient_name' => 'Khách Khác',
            'recipient_phone' => '0111222333',
            'shipping_address' => 'Hải Phòng',
            'subtotal' => 1000000,
            'shipping_fee' => 0,
            'total' => 1000000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.transactions', [
            'search' => 'Đặc Biệt',
            'payment_status' => 'paid',
        ]));

        $response->assertStatus(200);
        $response->assertSee('ORD-MATCH');
        $response->assertDontSee('ORD-OTHER');
    }
}
