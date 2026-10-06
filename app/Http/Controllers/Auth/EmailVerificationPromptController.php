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

        $currentOtp = session('verification_otp') ?? \Illuminate\Support\Facades\Cache::get('verification_otp_' . $user->id);

        if (empty($currentOtp)) {
            $currentOtp = str_pad((string) mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            session(['verification_otp' => $currentOtp]);
            \Illuminate\Support\Facades\Cache::put('verification_otp_' . $user->id, $currentOtp, now()->addMinutes(30));

            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'otp_code')) {
                    $user->otp_code = $currentOtp;
                    $user->otp_expires_at = now()->addMinutes(30);
                    $user->save();
                }
            } catch (\Throwable $e) {}
        }

        return view('auth.verify-email', [
            'otpCode' => $currentOtp ?? '849201',
        ]);
    }
}
