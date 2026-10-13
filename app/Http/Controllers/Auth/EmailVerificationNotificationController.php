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
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        \Illuminate\Support\Facades\Cache::put('verification_otp_' . $user->id, $otp, now()->addMinutes(10));
        session(['verification_otp' => $otp]);

        try {
            $user->sendOtpNotification($otp);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Không thể gửi lại mail xác thực: ' . $e->getMessage());
        }

        return back()->with('success', 'Mã xác thực OTP mới đã được gửi tới email ' . $user->email . '!');
    }
}
