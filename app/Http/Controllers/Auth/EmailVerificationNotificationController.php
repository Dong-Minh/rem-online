<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false));
        }

        $otp = str_pad((string) mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        session(['verification_otp' => $otp]);
        \Illuminate\Support\Facades\Cache::put('verification_otp_' . $user->id, $otp, now()->addMinutes(30));

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'otp_code')) {
                $user->otp_code = $otp;
                $user->otp_expires_at = now()->addMinutes(30);
                $user->save();
            }
        } catch (\Throwable $e) {}

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Không thể gửi lại mail xác thực:', ['error' => $e->getMessage()]);
        }

        return back()->with('status', 'verification-link-sent')->with('success', 'Mã xác thực OTP mới đã được tạo và gửi thành công!');
    }
}
