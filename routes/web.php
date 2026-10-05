<?php

use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — RÈM ONLINE
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (Khách hàng & Khách vãng lai)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{slug}', function ($slug) {
    return redirect()->route('products.index', ['category' => $slug]);
})->name('categories.show');

// Giỏ hàng (Cart)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

// Áp dụng / Hủy Voucher mã giảm giá
Route::post('/vouchers/apply', [VoucherController::class, 'apply'])->name('vouchers.apply');
Route::post('/vouchers/remove', [VoucherController::class, 'remove'])->name('vouchers.remove');

// Đăng ký lịch hẹn khảo sát & đo đạc tận nhà
Route::get('/dat-lich-khao-sat', [ConsultationController::class, 'create'])->name('consultations.create');
Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');

// ==========================================
// 2. GIAO HÀNG NHANH (GHN) API ROUTES
// ==========================================
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
});

// ==========================================
// 2.1 CỔNG THANH TOÁN MOMO & WEBHOOKS
// ==========================================
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('payment.momo.callback');
Route::get('/user/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

// ==========================================
// 3. THANH TOÁN (CHECKOUT) & ĐƠN HÀNG (YÊU CẦU ĐĂNG NHẬP & XÁC THỰC EMAIL)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [OrderController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [OrderController::class, 'processPayment'])->name('checkout.process');
    Route::get('/orders', [OrderController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Thanh toán qua Cổng MoMo
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('orders.momo.pay');
    Route::get('/user/orders/{order}/start-momo', [MomoController::class, 'start'])->name('user.orders.momo.start');
    Route::get('/user/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('user.orders.momo.pay');

    // Đánh giá sản phẩm
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Sổ địa chỉ khách hàng (Address Book)
    Route::get('/profile/addresses', [AddressController::class, 'index'])->name('profile.addresses.index');
    Route::post('/profile/addresses', [AddressController::class, 'store'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [AddressController::class, 'update'])->name('profile.addresses.update');
    Route::patch('/profile/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('profile.addresses.default');
    Route::delete('/profile/addresses/{address}', [AddressController::class, 'destroy'])->name('profile.addresses.destroy');

    // Livechat — Khách hàng nhắn tin với Admin
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/user/chat/send', [UserChatController::class, 'send'])->name('user.chat.send');
    Route::get('/user/chat/messages', [UserChatController::class, 'getMessages'])->name('user.chat.messages');
});

// Chuyển hướng route wishlist cũ về trang chủ
Route::get('/wishlist', function () {
    return redirect()->route('home');
})->name('wishlist.index');

// ==========================================
// 4. AUTHENTICATED CUSTOMER ROUTES
// ==========================================
Route::get('/dashboard', function () {
    $user = auth()->user();
    $stats = [
        'total_orders' => $user->orders()->count(),
        'pending_orders' => $user->orders()->where('status', 'pending')->count(),
        'total_spent' => $user->orders()->whereIn('status', ['delivered', 'completed'])->sum('total'),
        'wishlist_count' => $user->wishlists()->count(),
    ];
    $recentOrders = $user->orders()->with('items.product')->latest()->take(5)->get();
    $wishlistedProducts = $user->wishlists()->with('product')->latest('created_at')->take(4)->get();

    return view('dashboard', compact('user', 'stats', 'recentOrders', 'wishlistedProducts'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 5. ADMIN PORTAL ROUTES (Role: admin, super_admin)
// ==========================================
Route::middleware(['auth', 'verified', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');
        
        // CRUD Danh mục rèm
        Route::resource('categories', AdminCategoryController::class);

        // CRUD Sản phẩm rèm
        Route::resource('products', AdminProductController::class);

        // CRUD Mã giảm giá & Vouchers
        Route::resource('vouchers', AdminVoucherController::class);

        // Quản lý Đơn hàng & Xưởng may
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/{order}/sync-ghn', [AdminOrderController::class, 'syncGHN'])->name('orders.syncGHN');
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');

        // Quản lý Đánh giá sản phẩm
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/toggle', [AdminReviewController::class, 'toggleApproval'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Quản lý Lịch hẹn khảo sát & Đo đạc tận nhà
        Route::resource('consultations', AdminConsultationController::class);

        // Xử lý & Báo cáo Giao dịch Tài chính (Finance)
        Route::get('/finance', [AdminFinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/transactions', [AdminFinanceController::class, 'transactions'])->name('finance.transactions');
        Route::patch('/finance/{order}/status', [AdminFinanceController::class, 'updateStatus'])->name('finance.update-status');

        // Livechat — Admin quản lý và phản hồi tin nhắn
        Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('chat.users');
        Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/send', [AdminChatController::class, 'send'])->name('chat.send');

        // Báo cáo thống kê & Phân tích doanh thu nâng cao
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export', [AdminAnalyticsController::class, 'export'])->name('analytics.export');
    });

// ==========================================
// 6. AUTH ROUTES (Laravel Breeze)
// ==========================================
require __DIR__.'/auth.php';
