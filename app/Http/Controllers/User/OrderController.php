<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Services\CartService;
use App\Services\GHNOrderService;
use App\Services\GHNService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected CartService $cartService;
    protected GHNService $ghn;
    protected GHNOrderService $ghnOrder;
    protected VoucherService $voucherService;

    public function __construct(
        CartService $cartService,
        GHNService $ghn,
        GHNOrderService $ghnOrder,
        VoucherService $voucherService
    ) {
        $this->cartService = $cartService;
        $this->ghn = $ghn;
        $this->ghnOrder = $ghnOrder;
        $this->voucherService = $voucherService;
    }

    /**
     * Hiển thị trang Thanh Toán / Đặt Hàng (Checkout)
     */
    public function index()
    {
        $cart = $this->cartService->getCart();
        $cartItems = $cart->items()->with(['product', 'color'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống. Vui lòng chọn sản phẩm rèm trước khi thanh toán.');
        }

        $subtotal = $this->cartService->getSubtotal();
        $totalArea = $this->cartService->getTotalArea();
        $user = Auth::user();

        // Kiểm tra mã voucher đang áp dụng trong session
        $appliedVoucherData = $this->voucherService->getAppliedVoucher($subtotal, $user);
        $voucherDiscount = $appliedVoucherData ? $appliedVoucherData['discount_amount'] : 0;
        $appliedVoucher = $appliedVoucherData ? $appliedVoucherData['voucher'] : null;

        return view('checkout.index', compact('cart', 'cartItems', 'subtotal', 'totalArea', 'user', 'appliedVoucher', 'voucherDiscount'));
    }

    /**
     * Xử lý tạo đơn hàng & đẩy vận đơn sang GHN
     */
    public function processPayment(Request $request)
    {
        $cart = $this->cartService->getCart();
        $cartItems = $cart->items()->with(['product', 'color'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'recipient_email' => 'nullable|email|max:255',
            'to_province_id' => 'required|integer',
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'specific_address' => 'required|string|max:255',
            'province_name' => 'required|string|max:100',
            'district_name' => 'required|string|max:100',
            'ward_name' => 'required|string|max:100',
            'payment_method' => 'required|in:cod,momo,bank_transfer',
            'note' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string|max:50',
        ], [
            'recipient_name.required' => 'Vui lòng nhập họ tên người nhận.',
            'recipient_phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'recipient_phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',
            'to_province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố nhận hàng.',
            'to_district_id.required' => 'Vui lòng chọn Quận/Huyện nhận hàng.',
            'to_ward_code.required' => 'Vui lòng chọn Phường/Xã nhận hàng.',
            'specific_address.required' => 'Vui lòng nhập số nhà, tên đường chi tiết.',
        ]);

        // 1. Tính toán lại chi phí trên Backend để chống gian lận
        $subtotal = $this->cartService->getSubtotal();
        $totalArea = $this->cartService->getTotalArea();
        $fullShippingAddress = "{$validated['specific_address']}, {$validated['ward_name']}, {$validated['district_name']}, {$validated['province_name']}";

        // 2. Tính phí vận chuyển thực tế từ GHN API
        $calcWeight = max(1500, (int) round($totalArea * 400));
        $ghnFeeResult = $this->ghn->calculateFee(array_merge([
            'from_district_id' => (int) config('services.ghn.from_district_id', 1450),
            'to_district_id' => (int) $validated['to_district_id'],
            'to_ward_code' => (string) $validated['to_ward_code'],
        ], $this->ghn->packageParameters($calcWeight)));

        $shippingFee = (isset($ghnFeeResult['code']) && $ghnFeeResult['code'] === 200 && isset($ghnFeeResult['data']['total']))
            ? (float) $ghnFeeResult['data']['total']
            : (float) $request->input('estimated_shipping_fee', 35000);

        // 3. Xử lý Voucher Giảm Giá
        $user = Auth::user();
        $voucherCode = $request->input('voucher_code') ?: session('applied_voucher_code');
        $voucher = null;
        $voucherDiscount = 0;

        if ($voucherCode) {
            $voucherResult = $this->voucherService->validateVoucher($voucherCode, $subtotal, $user);
            if ($voucherResult['valid']) {
                $voucher = $voucherResult['voucher'];
                $voucherDiscount = $voucherResult['discount_amount'];
            }
        }

        $total = max(0, $subtotal - $voucherDiscount) + $shippingFee;

        // 4. Tạo đơn hàng trong Database
        $order = DB::transaction(function () use ($validated, $subtotal, $voucherDiscount, $voucher, $shippingFee, $total, $fullShippingAddress, $cartItems, $user) {
            $order = Order::create([
                'order_code' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => Auth::id(),
                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'recipient_email' => $validated['recipient_email'] ?? (Auth::user()?->email),
                'shipping_address' => $fullShippingAddress,
                'subtotal' => $subtotal,
                'promotion_discount' => 0,
                'voucher_discount' => $voucherDiscount,
                'voucher_id' => $voucher?->id,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'shipping_status' => 'not_shipped',
                'ghn_total_fee' => (int) $shippingFee,
                'to_province_id' => (int) $validated['to_province_id'],
                'to_district_id' => (int) $validated['to_district_id'],
                'to_ward_code' => (string) $validated['to_ward_code'],
                'note' => $validated['note'] ?? null,
            ]);

            // Snapshot từng bộ rèm may đo vào order_items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'color_id' => $item->color_id,
                    'color_name' => $item->color?->name ?? 'Mặc định',
                    'width' => $item->width,
                    'height' => $item->height,
                    'area' => $item->area,
                    'unit_price' => $item->unit_price,
                    'sale_unit_price' => $item->sale_unit_price,
                    'quantity' => $item->quantity,
                    'item_total' => $item->area * ($item->sale_unit_price ?? $item->unit_price) * $item->quantity,
                ]);
            }

            // Ghi nhận lịch sử sử dụng voucher nếu có
            if ($voucher && $user) {
                $this->voucherService->recordUsage($voucher, $user, $order, $voucherDiscount);
            }

            return $order;
        });

        // 5. Xóa voucher khỏi session & làm trống giỏ hàng
        $this->voucherService->removeVoucher();
        session([
            'last_order_id' => $order->id,
            'last_order_code' => $order->order_code,
        ]);
        $this->cartService->clearCart();

        // 6. Phân luồng theo phương thức thanh toán
        if ($validated['payment_method'] === 'momo') {
            // Nhánh MoMo: Khởi tạo giao dịch MoMo và redirect sang MoMo Gateway
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'momo',
                'amount' => $order->total,
                'status' => 'pending',
                'message' => 'Khởi tạo thanh toán qua Ví MoMo',
            ]);

            return redirect()->route('orders.momo.start', $order->id);
        }

        // Nhánh COD / Bank Transfer: Ghi nhật ký giao dịch và tạo đơn GHN
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => $validated['payment_method'],
            'amount' => $order->total,
            'status' => 'pending',
            'message' => $validated['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản VietQR',
        ]);

        try {
            $this->ghnOrder->create($order);
        } catch (\Exception $e) {
            Log::error('Lỗi khi đẩy đơn sang GHN:', ['message' => $e->getMessage()]);
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Chúc mừng bạn đã đặt hàng thành công! Đơn hàng rèm đã được ghi nhận.');
    }

    /**
     * Lịch sử đơn hàng của khách hàng
     */
    public function orderHistory()
    {
        if (!Auth::check()) {
            $lastOrderId = session('last_order_id');
            if ($lastOrderId) {
                $orders = Order::where('id', $lastOrderId)
                    ->with(['items.product', 'items.color'])
                    ->paginate(10);
                return view('orders.index', compact('orders'));
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem đầy đủ lịch sử đơn hàng của bạn.');
        }

        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'items.color'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng (kèm mã vận đơn GHN)
     */
    public function show(Order $order)
    {
        if (Auth::check()) {
            if ($order->user_id && $order->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isStaff()) {
                abort(403, 'Bạn không có quyền xem đơn hàng này.');
            }
        } else {
            if ($order->user_id !== null && session('last_order_id') !== $order->id && session('last_order_code') !== $order->order_code) {
                return redirect()->route('login')->with('error', 'Đơn hàng này thuộc tài khoản thành viên. Vui lòng đăng nhập để xem chi tiết.');
            }
        }

        $order->load(['items.product', 'items.color']);

        return view('orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng và hủy vận đơn GHN
     */
    public function cancel(Order $order)
    {
        $isAuthorized = false;
        if (Auth::check()) {
            $isAuthorized = ($order->user_id === Auth::id()) || Auth::user()->isAdmin() || Auth::user()->isStaff();
        } else {
            $isAuthorized = ($order->user_id === null) || (session('last_order_id') === $order->id) || (session('last_order_code') === $order->order_code);
        }

        if (!$isAuthorized) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã được hủy trước đó.');
        }

        $allowedStatuses = ['pending', 'ready_to_pick', 'not_shipped'];
        if (!in_array($order->shipping_status, $allowedStatuses, true) && $order->status !== 'pending') {
            return back()->with('error', 'Đơn hàng rèm đang may hoặc đã giao cho shipper, không thể tự hủy.');
        }

        // Hủy mã vận đơn trên GHN nếu đã có mã
        if ($order->ghn_order_code) {
            try {
                $response = $this->ghn->cancelOrder([$order->ghn_order_code]);
                if (($response['code'] ?? null) !== 200) {
                    Log::warning('GHN từ chối hủy mã:', ['response' => $response]);
                }
            } catch (\Exception $e) {
                Log::warning('Lỗi khi hủy đơn GHN: ' . $e->getMessage());
            }
        }

        $order->update([
            'status' => 'cancelled',
            'shipping_status' => 'cancelled',
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Đã hủy đơn hàng và hủy mã vận đơn GHN thành công.');
    }
}
