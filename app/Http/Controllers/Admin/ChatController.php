<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Lấy danh sách những User đã từng nhắn tin với Admin
     */
    public function getUsers()
    {
        $adminId = Auth::id();

        // Tìm tất cả ID của người dùng có tương tác với admin
        $userIds = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($msg) use ($adminId) {
                return $msg->sender_id == $adminId ? $msg->receiver_id : $msg->sender_id;
            })
            ->unique()
            ->values()
            ->toArray();

        // Lấy thông tin chi tiết các User đó kèm tin nhắn cuối cùng và số lượng chưa đọc
        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', $adminId)
            ->select('id', 'name', 'email', 'role')
            ->get()
            ->map(function ($user) use ($adminId) {
                $lastMsg = Message::where(function ($q) use ($user, $adminId) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $adminId);
                })->orWhere(function ($q) use ($user, $adminId) {
                    $q->where('sender_id', $adminId)->where('receiver_id', $user->id);
                })->latest()->first();

                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $adminId)
                    ->where('is_read', false)
                    ->count();

                $user->last_message = $lastMsg ? $lastMsg->content : '';
                $user->last_message_time = $lastMsg ? $lastMsg->created_at->format('H:i d/m') : '';
                $user->unread_count = $unreadCount;
                return $user;
            });

        return response()->json($users);
    }

    /**
     * Lấy lịch sử tin nhắn của một User cụ thể
     */
    public function getMessages($userId)
    {
        $adminId = Auth::id();

        // Đánh dấu các tin nhắn gửi tới admin là đã đọc
        Message::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('sender')
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

    /**
     * Admin gửi tin nhắn phản hồi cho khách hàng
     */
    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->user_id,
            'content' => $request->message,
            'is_read' => false,
        ]);

        $message->load('sender');

        return response()->json($message);
    }
}
