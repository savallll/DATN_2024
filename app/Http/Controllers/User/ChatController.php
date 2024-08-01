<?php

namespace App\Http\Controllers\User;

use App\Models\Message;
use App\Events\TestEvent;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $receiverId = $request->receiverId;

        // Tìm hoặc tạo cuộc trò chuyện
        $conversation = Conversation::where(function($query) use ($receiverId) {
            $query->where('user1_id', Auth::id())
                  ->orWhere('user2_id', Auth::id());
        })->where(function($query) use ($receiverId) {
            $query->where('user1_id', $receiverId)
                  ->orWhere('user2_id', $receiverId);
        })->first();

        if (!$conversation) {
            // Nếu không có cuộc trò chuyện, tạo một cái mới
            $conversation = Conversation::create([
                'user1_id' => Auth::id(),
                'user2_id' => $receiverId
            ]);
        }

        // Lưu tin nhắn
        $message = Message::create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'conversation_id' => $conversation->id,
            // 'is_read' => false
        ]);

        // Phát sự kiện MessageSent
        broadcast(new MessageSent($request->message, Auth::id(), $receiverId));

        return response()->json([
            'status' => 'Message Sent!',
            'message' => $message->message,
            'sender' => $message->sender_id,
            'receiver' => $receiverId
        ]);
    }

    public function getMessages($receiverId)
    {
        // Tìm cuộc trò chuyện giữa người dùng hiện tại và người nhận
        $conversation = Conversation::where(function($query) use ($receiverId) {
            $query->where('user1_id', Auth::id())
                  ->orWhere('user2_id', Auth::id());
        })->where(function($query) use ($receiverId) {
            $query->where('user1_id', $receiverId)
                  ->orWhere('user2_id', $receiverId);
        })->first();

        if (!$conversation) {
            return response()->json([]);
        }

        // Lấy tất cả tin nhắn trong cuộc trò chuyện
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
