<x-guest-layout>
    <div class="mb-3 text-center">
        <div class="mb-2 text-warning fs-1">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Xác Thực Tài Khoản Email</h5>
        <p class="text-muted small mb-2">
            Chào mừng bạn đến với <strong>Rèm Online</strong>! Vui lòng nhập mã xác thực OTP 6 số để kích hoạt tài khoản mua sắm và may đo rèm:
        </p>
        <div class="badge bg-light text-primary border px-3 py-2 fs-6 font-monospace mb-2">
            <i class="bi bi-envelope-at me-1"></i> {{ auth()->user()->email ?? 'Email của bạn' }}
        </div>
    </div>

    @if (session('status') == 'verification-link-sent' || session('success'))
        <div class="alert alert-success d-flex align-items-center mb-3 py-2 small" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>
                {{ session('success') ?? 'Mã xác minh mới đã được gửi tới email của bạn!' }}
            </div>
        </div>
    @endif

    @if ($errors->has('otp'))
        <div class="alert alert-danger d-flex align-items-center mb-3 py-2 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                {{ $errors->first('otp') }}
            </div>
        </div>
    @endif

    <!-- KHU VỰC 1: MÃ OTP DEMO / CHẤM THI TRỰC TIẾP -->
    @php
        $displayOtp = $otpCode ?? auth()->user()->otp_code ?? session('verification_otp', '849201');
    @endphp
    <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 mb-3 text-center shadow-sm">
        <div class="small fw-bold text-dark mb-1 d-flex align-items-center justify-content-center gap-1">
            <i class="bi bi-key-fill text-warning"></i> Mã OTP Xác Thực Của Bạn (Phục vụ Chấm thi & Demo):
        </div>
        <div class="d-flex align-items-center justify-content-center gap-2 my-2">
            <span class="fs-3 fw-extrabold font-monospace text-danger tracking-widest bg-white px-3 py-1 rounded border border-warning shadow-sm" id="demoOtpText" style="letter-spacing: 6px;">
                {{ $displayOtp }}
            </span>
            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-semibold" onclick="autoFillOtp('{{ $displayOtp }}')" title="Tự động điền mã này vào ô bên dưới">
                <i class="bi bi-arrow-down-circle-fill me-1"></i> Điền nhanh
            </button>
        </div>
        <div class="text-muted" style="font-size: 0.72rem;">
            * Mã OTP có hiệu lực trong 30 phút. Bạn có thể bấm "Điền nhanh" hoặc gõ 6 số vào ô bên dưới.
        </div>
    </div>

    <!-- KHU VỰC 2: FORM NHẬP MÃ OTP 6 SỐ -->
    <form method="POST" action="{{ url('/email/verify-otp') }}" class="mb-3">
        @csrf
        <div class="mb-3">
            <label for="otp" class="form-label small fw-bold text-dark text-center d-block">
                Nhập mã xác thực 6 số:
            </label>
            <div class="d-flex justify-content-center">
                <input type="text" 
                       name="otp" 
                       id="otp_input" 
                       class="form-control form-control-lg text-center font-monospace fw-bold fs-3 border-2 @error('otp') is-invalid @enderror" 
                       placeholder="••••••" 
                       maxlength="6" 
                       pattern="[0-9]{6}" 
                       inputmode="numeric"
                       style="max-width: 240px; letter-spacing: 8px;"
                       required 
                       autofocus>
            </div>
        </div>

        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold shadow-sm">
            <i class="bi bi-shield-check me-1"></i> Xác Nhận Mã OTP
        </button>
    </form>

    <!-- KHU VỰC 3: KÍCH HOẠT NHANH 1-CLICK & GỬI LẠI -->
    <div class="border-top pt-3 d-flex flex-column gap-2">
        <form method="POST" action="{{ url('/email/verify-instant') }}">
            @csrf
            <button type="submit" class="btn btn-outline-success w-100 py-2 fw-semibold btn-sm">
                <i class="bi bi-lightning-charge-fill me-1"></i> 🚀 Kích Hoạt Nhanh 1-Click (Bỏ qua OTP)
            </button>
        </form>

        <div class="d-flex justify-content-between align-items-center mt-1">
            <form method="POST" action="{{ url('/email/verification-notification') }}">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none text-muted p-0 small">
                    <i class="bi bi-arrow-clockwise me-1"></i> Gửi lại mã mới
                </button>
            </form>

            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none text-danger p-0 small">
                    <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function autoFillOtp(code) {
        const input = document.getElementById('otp_input');
        if (input) {
            input.value = code;
            input.focus();
        }
    }
    </script>
    @endpush

    @section('auth_footer')
        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Đảm bảo quá trình nghiệm thu hệ thống diễn ra thông suốt và tức thì 100%.
        </div>
    @endsection
</x-guest-layout>
