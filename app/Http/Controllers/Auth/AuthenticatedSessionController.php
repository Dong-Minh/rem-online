<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Gộp giỏ hàng session (nếu có) vào tài khoản khách hàng
        app(\App\Services\CartService::class)->mergeSessionCartToUser($request->user());

        // 1. Nếu là Admin hoặc Staff -> Chuyển vào trang Quản trị Admin
        if ($request->user()->isAdmin() || $request->user()->isStaff()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // 2. Nếu là Khách hàng -> Chuyển về Trang Chủ
        return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công! Chào mừng ' . $request->user()->name . ' quay trở lại.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
