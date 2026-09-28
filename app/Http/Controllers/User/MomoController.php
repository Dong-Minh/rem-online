<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    /**
     * Khởi tạo và chuyển hướng đến trang thanh toán MoMo
     */
    public function start(Order $order, MomoService $momo)
    {
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Thanh toán lại cho đơn hàng chưa hoàn tất hoặc thất bại trước đó
     */
    public function payAgain(Order $order, MomoService $momo)
    {
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)->with('info', 'Đơn hàng này đã được thanh toán thành công.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Xử lý khi khách hàng thanh toán xong và được MoMo redirect về website
     */
    public function callback(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo Callback Received:', [
            'payload' => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        $orderId = $momo->orderId($request->all());

        if (!$momo->isValidSuccessfulResponse($request->all())) {
            Log::warning('MoMo Callback Rejected:', [
                'result_code' => $request->input('resultCode'),
                'order_id' => $request->input('orderId'),
                'signature_valid' => $momo->isValidResponse($request->all()),
            ]);

            if ($momo->isValidResponse($request->all())) {
                $this->markFailed($request->all(), $momo);
            }

            $redirectRoute = $orderId ? route('orders.show', $orderId) : route('orders.index');
            return redirect($redirectRoute)->with('error', 'Giao dịch MoMo không thành công hoặc đã bị hủy. Bạn có thể bấm "Thanh toán lại".');
        }

        $result = $this->completePayment($request->all(), $ghnOrders, $momo);
        $message = in_array($result, ['created', 'already_created'], true)
            ? 'Thanh toán qua Ví MoMo thành công! Vận đơn giao hàng GHN đã được khởi tạo.'
            : 'Thanh toán qua Ví MoMo thành công! Đơn hàng của bạn đang được xưởng may tiếp nhận.';

        $redirectRoute = $orderId ? route('orders.show', $orderId) : route('orders.index');
        return redirect($redirectRoute)->with('success', $message);
    }

    /**
     * Nhận thông báo tự động (IPN Webhook) trực tiếp từ máy chủ MoMo
     */
    public function ipn(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo IPN Received:', [
            'payload' => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        if ($momo->isValidSuccessfulResponse($request->all())) {
            $this->completePayment($request->all(), $ghnOrders, $momo);
        } elseif ($momo->isValidResponse($request->all())) {
            $this->markFailed($request->all(), $momo);
        }

        return response()->json(['message' => 'Received']);
    }

    /**
     * Tạo bản ghi giao dịch thanh toán mới
     */
    private function newTransaction(Order $order): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'momo',
            'amount' => $order->total ?? $order->total_price,
            'status' => 'pending',
        ]);
    }

    /**
     * Lấy đường dẫn thanh toán từ MomoService và chuyển hướng
     */
    private function redirectToMomo(Order $order, PaymentTransaction $transaction, MomoService $momo)
    {
        $result = $momo->createPayment($order, $transaction);

        if (isset($result['payUrl']) && !empty($result['payUrl'])) {
            return redirect($result['payUrl']);
        }

        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Không thể kết nối đến cổng thanh toán MoMo: ' . ($result['message'] ?? 'Vui lòng thử lại sau.'));
    }

    /**
     * Xác nhận thanh toán thành công và tự động tạo vận đơn GHN
     */
    private function completePayment(array $payload, GHNOrderService $ghnOrders, MomoService $momo): string
    {
        $result = DB::transaction(function () use ($payload, $momo) {
            $transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return 'invalid';
            }

            $order = Order::lockForUpdate()->find($transaction->order_id);
            if (!$order) {
                return 'invalid';
            }

            if ($order->payment_status === 'paid' && $order->ghn_order_code) {
                return 'already_created';
            }

            if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
                $momo->markFailed($transaction, $payload);
                return 'invalid';
            }

            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ]);

            $momo->markPaid($transaction, $payload);

            return ['create', $order->id];
        });

        if (!is_array($result)) {
            return (string) $result;
        }

        $order = Order::with('items.product')->find($result[1]);
        if (!$order->ghn_order_code) {
            try {
                $response = $ghnOrders->create($order, true);
                if (isset($response['code']) && $response['code'] === 200 && !empty($response['data']['order_code'])) {
                    $order->update([
                        'ghn_order_code' => $response['data']['order_code'],
                        'shipping_status' => 'ready_to_pick',
                    ]);
                    return 'created';
                }
            } catch (\Exception $e) {
                Log::error('GHN Order Creation Failed after MoMo Payment:', ['message' => $e->getMessage()]);
            }
        }

        return 'paid_without_ghn';
    }

    /**
     * Ghi nhận giao dịch thanh toán thất bại
     */
    private function markFailed(array $payload, MomoService $momo): void
    {
        $transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->first();

        if ($transaction && $transaction->status !== 'paid') {
            $momo->markFailed($transaction, $payload);
            
            $order = Order::find($transaction->order_id);
            if ($order && $order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'failed']);
            }
        }
    }
}
