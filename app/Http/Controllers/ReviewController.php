<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Khách hàng gửi đánh giá và nhận xét sản phẩm
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá (1 - 5 sao).',
            'comment.required' => 'Vui lòng viết đôi lời nhận xét về sản phẩm rèm.',
            'comment.min' => 'Nội dung nhận xét tối thiểu 5 ký tự.',
            'comment.max' => 'Nội dung nhận xét không được vượt quá 1000 ký tự.',
        ]);

        $userId = Auth::id();
        $productId = (int) $validated['product_id'];
        $orderId = $validated['order_id'] ?? null;

        // Lưu hoặc cập nhật đánh giá của khách
        Review::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
            ],
            [
                'rating' => (int) $validated['rating'],
                'comment' => $validated['comment'],
                'is_approved' => true, // Tự động duyệt hiển thị ngay (Admin có thể ẩn/xóa sau)
            ]
        );

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá & nhận xét cho mẫu rèm này!');
    }
}
