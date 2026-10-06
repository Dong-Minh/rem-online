<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified via signed link.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Địa chỉ email của bạn đã được xác thực trước đó.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('home')->with('success', '🎉 Xác thực Email thành công! Bạn đã có thể đặt may rèm và thanh toán.');
    }

    /**
     * Kích hoạt xác thực ngay lập tức (Dành cho Giảng viên / Chấm thi / Demo)
     */
    public function verifyInstant(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }
            return redirect()->route('home')->with('success', '🎉 Kích hoạt tài khoản thành công! Bạn có thể thoải mái thêm giỏ hàng và đặt may rèm.');
        }

        return redirect()->route('login');
    }
}
