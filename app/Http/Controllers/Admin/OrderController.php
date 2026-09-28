<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected GHNService $ghn;

    public function __construct(GHNService $ghn)
    {
        $this->ghn = $ghn;
    }

    /**
     * Danh sách đơn hàng toàn hệ thống kèm thống kê & bộ lọc
     */
    public function index(Request $request)
    {
        // 1. Thống kê nhanh (Metrics KPIs)
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $preparingOrders = Order::where('status', 'preparing')->count();
        $totalRevenue = Order::where(function ($q) {
            $q->where('status', 'completed')
              ->orWhere('payment_status', 'paid');
        })->sum('total');

        // 2. Query lọc đơn hàng
        $query = Order::with(['items.product', 'user', 'voucher'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'LIKE', "%{$search}%")
                  ->orWhere('ghn_order_code', 'LIKE', "%{$search}%")
                  ->orWhere('recipient_name', 'LIKE', "%{$search}%")
                  ->orWhere('recipient_phone', 'LIKE', "%{$search}%")
                  ->orWhere('recipient_email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $request->shipping_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.index', compact(
            'orders',
            'totalOrders',
            'pendingOrders',
            'preparingOrders',
            'totalRevenue'
        ));
    }

    /**
     * Chi tiết đơn hàng rèm may đo
     */
    public function show(Order $order)
    {
        $order->load(['items.product.categories', 'items.color', 'user', 'voucher']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng, thanh toán & xưởng may
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,shipping,delivered,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'shipping_status' => 'nullable|in:not_shipped,pending,ready_to_pick,delivering,delivered,cancelled',
            'note' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'payment_status.required' => 'Vui lòng chọn trạng thái thanh toán.',
        ]);

        // 1. NGHIỆP VỤ: Nếu đơn hàng ở trạng thái ĐANG GIAO (shipping / delivering) hoặc ĐÃ GIAO -> KHÔNG CHO PHÉP HỦY
        if ($validated['status'] === 'cancelled') {
            if (in_array($order->status, ['shipping', 'delivered', 'completed'], true) || 
                in_array($order->shipping_status, ['delivering', 'delivered'], true)) {
                return back()->with('error', 'Đơn hàng đang trong quá trình giao hàng hoặc đã hoàn tất. KHÔNG ĐƯỢC PHÉP HỦY!');
            }

            // Nếu trạng thái đổi sang cancelled và có mã GHN -> Hủy mã trên GHN
            if ($order->ghn_order_code) {
                try {
                    $this->ghn->cancelOrder([$order->ghn_order_code]);
                    $validated['shipping_status'] = 'cancelled';
                } catch (\Exception $e) {
                    Log::warning('Lỗi khi hủy đơn GHN từ admin:', ['message' => $e->getMessage()]);
                }
            }
        }

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Cập nhật trạng thái đơn hàng ' . $order->order_code . ' thành công!');
    }

    /**
     * Đồng bộ trạng thái vận chuyển từ API GHN
     */
    public function syncGHN(Order $order)
    {
        if (!$order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng này chưa được tạo mã vận đơn GHN.');
        }

        try {
            $result = $this->ghn->getOrderDetail($order->ghn_order_code);
            if (isset($result['code']) && $result['code'] === 200 && isset($result['data']['status'])) {
                $ghnStatus = $result['data']['status'];
                $order->update(['shipping_status' => $ghnStatus]);
                return back()->with('success', 'Đã đồng bộ trạng thái từ GHN: ' . $ghnStatus);
            }
        } catch (\Exception $e) {
            Log::warning('Lỗi đồng bộ GHN:', ['message' => $e->getMessage()]);
        }

        return back()->with('info', 'Đã kiểm tra kết nối với hệ thống Giao Hàng Nhanh.');
    }

    /**
     * In phiếu cắt may & phiếu giao hàng rèm chuẩn A4 cho xưởng
     */
    public function print(Order $order)
    {
        $order->load(['items.product', 'items.color', 'user', 'voucher']);

        return view('admin.orders.print', compact('order'));
    }
}
