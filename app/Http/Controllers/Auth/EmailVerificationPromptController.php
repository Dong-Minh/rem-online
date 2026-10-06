<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false));
        }

        // Tự động tạo OTP nếu chưa có hoặc đã hết hạn
        if (empty($user->otp_code) || ($user->otp_expires_at && $user->otp_expires_at->isPast())) {
            $otp = str_pad((string) mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            try {
                $user->otp_code = $otp;
                $user->otp_expires_at = now()->addMinutes(30);
                $user->save();
            } catch (\Throwable $e) {
                // Fallback nếu chưa kịp migrate
            }
            session(['verification_otp' => $otp]);
        } else {
            session(['verification_otp' => $user->otp_code]);
        }

        return view('auth.verify-email', [
            'otpCode' => $user->otp_code ?? session('verification_otp', '849201'),
        ]);
    }
}
