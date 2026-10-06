<?php

namespace App\Http\Controllers;

use App\Services\AIChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIChatbotController extends Controller
{
    protected AIChatbotService $aiService;

    public function __construct(AIChatbotService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Nhận tin nhắn chat từ người dùng và trả về phản hồi thông minh từ AI.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array'],
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);

        $result = $this->aiService->respond($message, $history);

        return response()->json([
            'success' => true,
            'reply' => $result['reply'] ?? '',
            'products' => $result['products'] ?? [],
            'quick_replies' => $result['quick_replies'] ?? [],
        ]);
    }
}
