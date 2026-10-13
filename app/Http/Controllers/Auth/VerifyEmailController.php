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
     * Xác thực tài khoản bằng mã OTP 6 số.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP gồm 6 chữ số.',
            'otp.size' => 'Mã OTP phải gồm đúng 6 chữ số.',
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Tài khoản của bạn đã được xác thực trước đó.');
        }

        $inputOtp = trim($request->input('otp'));
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('verification_otp_' . $user->id);
        $userOtp = $user->otp_code;
        $sessionOtp = session('verification_otp');

        // Kiểm tra thời hạn hiệu lực của mã OTP
        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn hiệu lực (quá 10 phút). Vui lòng nhấn "Gửi lại mã mới" để nhận mã khác.']);
        }

        // So khớp mã OTP thực tế
        $isValid = ($userOtp && $inputOtp === $userOtp)
                || ($cachedOtp && $inputOtp === $cachedOtp)
                || ($sessionOtp && $inputOtp === $sessionOtp);

        if ($isValid) {
            $user->markEmailAsVerified();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            session()->forget('verification_otp');
            \Illuminate\Support\Facades\Cache::forget('verification_otp_' . $user->id);

            event(new Verified($user));

            return redirect()->route('home')->with('success', '🎉 Xác thực mã OTP thành công! Chào mừng bạn đến với Rèm Online.');
        }

        return back()->withErrors(['otp' => 'Mã OTP không chính xác. Vui lòng kiểm tra lại hòm thư email của bạn (bao gồm mục Thư rác/Spam).']);
    }

    /**
     * Chuyển hướng nếu truy cập instant verification
     */
    public function verifyInstant(Request $request): RedirectResponse
    {
        return redirect()->route('verification.notice');
    }
}
