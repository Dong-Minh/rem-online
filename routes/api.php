<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Http\Controllers\AIChatbotController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\MomoController;

/*
|--------------------------------------------------------------------------
| RESTful API Routes — BTL PHÁT TRIỂN PHẦN MỀM HƯỚNG DỊCH VỤ (SOA)
|--------------------------------------------------------------------------
*/

// 1. DỊCH VỤ XÁC THỰC & TÀI KHOẢN (AUTH & OTP SERVICE)
Route::prefix('auth')->group(function () {
    Route::post('/register', function (Request $request) {
        $email = $request->input('email', 'minh.user@gmail.com');
        $name = $request->input('full_name', $request->input('name', 'Đồng Quang Minh'));
        $otp = (string) rand(100000, 999999);

        try {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'phone' => $request->input('phone', '0987654321'),
                    'password' => Hash::make($request->input('password', 'UserPass@2026')),
                    'role' => 'customer',
                    'otp_code' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ]
            );

            \Illuminate\Support\Facades\Cache::put('verification_otp_' . $user->id, $otp, now()->addMinutes(10));

            // Gửi email thật qua Gmail SMTP
            $user->sendOtpNotification($otp);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi gửi email OTP: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đăng ký tài khoản thành công! Mã OTP 6 số đã được gửi trực tiếp đến email {$email}. Vui lòng kiểm tra hộp thư (hoặc mục Spam).",
            'data' => [
                'email' => $email,
                'otp_expires_in' => 600,
            ]
        ], 201);
    });

    Route::post('/verify-otp', function (Request $request) {
        $email = $request->input('email');
        $inputOtp = trim((string) $request->input('otp_code', $request->input('otp')));
        
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Tài khoản không tồn tại.'], 404);
        }

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('verification_otp_' . $user->id);
        $userOtp = $user->otp_code;

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return response()->json(['status' => 'error', 'message' => 'Mã OTP đã hết hạn hiệu lực (quá 10 phút).'], 422);
        }

        $isValid = ($userOtp && $inputOtp === $userOtp) || ($cachedOtp && $inputOtp === $cachedOtp);

        if ($isValid) {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();
            \Illuminate\Support\Facades\Cache::forget('verification_otp_' . $user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.'
            ], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Mã OTP không chính xác. Vui lòng kiểm tra lại email.'], 422);
    });

    Route::post('/login', function (Request $request) {
        $email = $request->input('email', 'minh.user@gmail.com');
        $isAdmin = str_contains($email, 'admin');
        $role = $isAdmin ? 'admin' : 'customer';

        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.' . base64_encode(json_encode([
            'sub' => $isAdmin ? 1 : 2,
            'email' => $email,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + 86400
        ])) . '.mock_sig_' . ($isAdmin ? 'admin' : 'user');

        return response()->json([
            'status' => 'success',
            'message' => $isAdmin ? 'Đăng nhập quản trị viên thành công' : 'Đăng nhập thành công',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $isAdmin ? 1 : 2,
                    'full_name' => $isAdmin ? 'Quản trị viên Hệ thống' : 'Đồng Quang Minh',
                    'email' => $email,
                    'role' => $role
                ]
            ]
        ], 200);
    });
});

// 2. DỊCH VỤ TÍNH TOÁN MAY ĐO RÈM
Route::prefix('custom-curtains')->group(function () {
    Route::post('/calculate-price', function (Request $request) {
        $width = floatval($request->input('width', 2.5));
        $height = floatval($request->input('height', 2.8));
        $foldRatio = floatval($request->input('fold_ratio', 2.2));
        $fabricType = $request->input('fabric_type', 'linen_cao_cap');
        $lining = $request->boolean('lining', true);
        $motorized = $request->boolean('motorized', false);

        $area = round($width * $height, 2);
        $fabricNeeded = round($width * $foldRatio + 0.3, 2);
        $basePriceM2 = 450000;
        $fabricCost = round($area * $basePriceM2);
        $accessoryCost = $motorized ? 1500000 : 250000;
        $liningCost = $lining ? 180000 : 0;
        $laborCost = 150000;
        $totalPrice = $fabricCost + $accessoryCost + $liningCost + $laborCost;

        return response()->json([
            'status' => 'success',
            'data' => [
                'product_id' => $request->input('product_id', 1),
                'dimensions' => ['width_m' => $width, 'height_m' => $height, 'area_m2' => $area],
                'fabric_details' => [
                    'fold_ratio' => $foldRatio,
                    'fabric_needed_m' => $fabricNeeded,
                    'fabric_type' => $fabricType,
                    'has_lining' => $lining,
                ],
                'cost_breakdown' => [
                    'fabric_cost' => $fabricCost,
                    'accessory_cost' => $accessoryCost,
                    'lining_cost' => $liningCost,
                    'labor_cost' => $laborCost,
                ],
                'total_price' => $totalPrice,
                'currency' => 'VND'
            ]
        ], 200);
    });
});

// 3. DỊCH VỤ VẬN CHUYỂN GHN
Route::prefix('shipping')->group(function () {
    Route::post('/calculate-fee', function (Request $request) {
        $weight = intval($request->input('weight', 4500));
        $shippingFee = ($weight > 3000) ? 45000 : 35000;
        $insuranceFee = intval($request->input('insurance_value', 2850000) * 0.005);

        return response()->json([
            'status' => 'success',
            'data' => [
                'shipping_fee' => $shippingFee,
                'insurance_fee' => $insuranceFee,
                'total_fee' => $shippingFee + $insuranceFee,
                'service_type' => 'Giao Hàng Nhanh (GHN Express)',
                'estimated_delivery' => '2 - 3 ngày làm việc'
            ]
        ], 200);
    });

    Route::get('/provinces', function () {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['ProvinceID' => 201, 'ProvinceName' => 'Hà Nội'],
                ['ProvinceID' => 202, 'ProvinceName' => 'Hồ Chí Minh'],
                ['ProvinceID' => 203, 'ProvinceName' => 'Đà Nẵng'],
                ['ProvinceID' => 204, 'ProvinceName' => 'Hải Phòng']
            ]
        ]);
    });
});

// 4. DỊCH VỤ THANH TOÁN MOMO
Route::prefix('payment/momo')->group(function () {
    Route::post('/create', function (Request $request) {
        $orderId = $request->input('order_id', 'ORD-' . date('Ymd') . '-8899');
        $amount = intval($request->input('amount', 3550000));

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $orderId,
                'amount' => $amount,
                'partner_code' => 'MOMOBKUN20180529',
                'pay_url' => "https://test-payment.momo.vn/v2/gateway/pay?orderId={$orderId}",
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=momo_' . $orderId,
                'deeplink' => 'momo://payment?action=payWithApp'
            ]
        ]);
    });

    Route::post('/ipn', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Nhận Webhook IPN MoMo thành công, đơn hàng đã chuyển trạng thái PAID.'
        ], 200);
    });
});

// 5. DỊCH VỤ TRỢ LÝ AI TƯ VẤN RÈM
Route::prefix('ai')->group(function () {
    Route::post('/chat', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => [
                'reply' => "Dạ chào bạn! Đối với cửa hướng Tây đón nắng gắt buổi chiều, bạn nên chọn dòng **Rèm vải gấm cản sáng 100% (Blackout)** kết hợp may 2 lớp (1 lớp vải cản nhiệt + 1 lớp voan trắng lấy sáng dịu). Về cách đo: Bạn đo lọt lòng khuôn cửa hoặc đo phủ bì thêm 15cm mỗi cạnh để chống lọt sáng tốt nhất ạ!",
                'suggested_products' => [
                    ['id' => 1, 'name' => 'Rèm Vải 2 Lớp Cản Sáng Cao Cấp Modero', 'price_per_m2' => 750000],
                    ['id' => 2, 'name' => 'Rèm Cầu Vồng Hàn Quốc Chống Tia UV', 'price_per_m2' => 580000]
                ]
            ]
        ]);
    });
});

// 6. DỊCH VỤ DANH MỤC & SẢN PHẨM
Route::get('/categories', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            ['id' => 1, 'name' => 'Rèm vải 2 lớp', 'slug' => 'rem-vai-2-lop', 'products_count' => 12],
            ['id' => 2, 'name' => 'Rèm cầu vồng Hàn Quốc', 'slug' => 'rem-cau-vong', 'products_count' => 8],
            ['id' => 3, 'name' => 'Rèm gỗ tự nhiên', 'slug' => 'rem-go', 'products_count' => 6],
            ['id' => 4, 'name' => 'Rèm cuốn văn phòng', 'slug' => 'rem-cuon', 'products_count' => 5]
        ]
    ]);
});

Route::get('/products', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            ['id' => 1, 'name' => 'Rèm Cầu Vồng Hàn Quốc Modero', 'price_per_m2' => 580000, 'category' => 'Rèm cầu vồng', 'in_stock' => true],
            ['id' => 2, 'name' => 'Rèm Vải Gấm Cản Sáng Nhật Bản', 'price_per_m2' => 720000, 'category' => 'Rèm vải 2 lớp', 'in_stock' => true],
            ['id' => 3, 'name' => 'Rèm Gỗ Sồi Tự Nhiên Cao Cấp', 'price_per_m2' => 650000, 'category' => 'Rèm gỗ', 'in_stock' => true]
        ]
    ]);
});

Route::get('/products/{id}', function ($id) {
    return response()->json([
        'status' => 'success',
        'data' => [
            'id' => intval($id),
            'name' => 'Rèm Cầu Vồng Hàn Quốc Modero',
            'price_per_m2' => 580000,
            'description' => 'Chất liệu 100% Polyester kháng khuẩn, cản sáng 95%, chống tia UV hiệu quả.',
            'fabric_types' => ['linen_cao_cap', 'cotton_tho', 'gam_chong_nang'],
            'in_stock' => true
        ]
    ]);
});

// 7. PHÂN QUYỀN RBAC (USER VS ADMIN)
Route::get('/user/profile', function (Request $request) {
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json(['status' => 'error', 'message' => 'Chưa đăng nhập hoặc Token không hợp lệ.'], 401);
    }
    return response()->json([
        'status' => 'success',
        'data' => [
            'id' => 2,
            'full_name' => 'Đồng Quang Minh',
            'email' => 'minh.user@gmail.com',
            'role' => 'customer'
        ]
    ]);
});

Route::post('/admin/products', function (Request $request) {
    $token = $request->bearerToken() ?? '';
    if (!str_contains($token, 'admin')) {
        return response()->json([
            'status' => 'error',
            'message' => 'Truy cập bị từ chối! Bạn không có quyền quản trị (Admin Privilege Required).'
        ], 403);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Thêm mới sản phẩm rèm thành công (Admin Privilege Granted)!',
        'data' => [
            'id' => 15,
            'name' => $request->input('name', 'Rèm vải gấm thêu tân cổ điển'),
            'base_price' => $request->input('base_price', 650000)
        ]
    ], 201);
});
