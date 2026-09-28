<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivechatTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;

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

    public function test_customer_can_send_message_to_admin(): void
    {
        $this->actingAs($this->customer);

        $response = $this->postJson(route('user.chat.send'), [
            'message' => 'Xin chào shop, tôi muốn hỏi giá may rèm cầu vồng cửa sổ 2mx2m.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('content', 'Xin chào shop, tôi muốn hỏi giá may rèm cầu vồng cửa sổ 2mx2m.');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Xin chào shop, tôi muốn hỏi giá may rèm cầu vồng cửa sổ 2mx2m.',
            'is_read' => false,
        ]);
    }

    public function test_customer_can_retrieve_chat_history(): void
    {
        Message::create([
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Câu hỏi từ khách',
        ]);

        Message::create([
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->customer->id,
            'content' => 'Câu trả lời từ admin',
        ]);

        $this->actingAs($this->customer);

        $response = $this->getJson(route('user.chat.messages'));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.content', 'Câu hỏi từ khách');
        $response->assertJsonPath('1.content', 'Câu trả lời từ admin');
    }

    public function test_admin_can_get_user_list_and_reply_message(): void
    {
        Message::create([
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Rèm gấm hoàng gia có sẵn không admin?',
            'is_read' => false,
        ]);

        $this->actingAs($this->admin);

        // 1. Admin lấy danh sách user đã nhắn tin
        $usersResponse = $this->getJson(route('admin.chat.users'));
        $usersResponse->assertStatus(200);
        $usersResponse->assertJsonCount(1);
        $usersResponse->assertJsonPath('0.name', 'Khách Hàng A');

        // 2. Admin lấy chi tiết tin nhắn của khách
        $msgResponse = $this->getJson(route('admin.chat.messages', $this->customer->id));
        $msgResponse->assertStatus(200);
        $msgResponse->assertJsonCount(1);
        $msgResponse->assertJsonPath('0.content', 'Rèm gấm hoàng gia có sẵn không admin?');

        // 3. Admin gửi tin nhắn trả lời
        $sendResponse = $this->postJson(route('admin.chat.send'), [
            'user_id' => $this->customer->id,
            'message' => 'Dạ bên em còn sẵn mẫu vải gấm hoàng gia ạ, bên em có hỗ trợ thợ mang mẫu đến đo đạc tận nơi.',
        ]);

        $sendResponse->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->customer->id,
            'content' => 'Dạ bên em còn sẵn mẫu vải gấm hoàng gia ạ, bên em có hỗ trợ thợ mang mẫu đến đo đạc tận nơi.',
        ]);
    }
}
