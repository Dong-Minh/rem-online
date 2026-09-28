<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Gửi tin nhắn từ Khách hàng tới Ban Quản Trị / Nhân viên tư vấn
     */
    public function send(Request $request)
    {
        $messageText = $request->input('message');

        if (empty(trim((string) $messageText))) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        // Tìm Admin hoặc Super Admin hoặc mặc định ID 1
        $admin = User::whereIn('role', ['admin', 'super_admin'])->first();
        $receiverId = $admin ? $admin->id : 1;

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'content' => trim((string) $messageText),
                'is_read' => false,
            ]);

            $message->load(['sender', 'receiver']);

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể gửi tin nhắn: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lấy lịch sử chat giữa User hiện tại và Admin
     */
    public function getMessages()
    {
        $userId = Auth::id();

        // Tìm Admin / Super Admin
        $admin = User::whereIn('role', ['admin', 'super_admin'])->first();
        $adminId = $admin ? $admin->id : 1;

        // Đánh dấu các tin nhắn admin gửi cho user này là đã đọc
        Message::where('sender_id', $adminId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Lấy toàn bộ hội thoại giữa 2 bên
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
