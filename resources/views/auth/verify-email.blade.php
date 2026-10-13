<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="mb-3 text-warning" style="font-size: 2.8rem;">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">Xác Thực Tài Khoản Email</h4>
        <p class="text-muted small mb-3">
            Mã xác thực OTP gồm <strong>6 chữ số</strong> đã được gửi tới hộp thư điện tử của bạn:
        </p>
        <div class="d-inline-flex align-items-center bg-light border border-primary border-opacity-25 rounded-pill px-3 py-2 text-primary fw-semibold small mb-2 shadow-sm">
            <i class="bi bi-envelope-check-fill me-2 fs-6"></i> {{ auth()->user()->email ?? 'Email của bạn' }}
        </div>
        <p class="text-secondary mb-0" style="font-size: 0.8rem;">
            Vui lòng kiểm tra hộp thư đến (hoặc thư mục <strong>Thư rác / Spam</strong>) và nhập mã OTP bên dưới để kích hoạt tài khoản.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent' || session('success'))
        <div class="alert alert-success d-flex align-items-center mb-3 py-2 small rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div>
                {{ session('success') ?? 'Mã xác thực OTP mới đã được gửi tới email của bạn!' }}
            </div>
        </div>
    @endif

    @if ($errors->has('otp'))
        <div class="alert alert-danger d-flex align-items-center mb-3 py-2 small rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
            <div>
                {{ $errors->first('otp') }}
            </div>
        </div>
    @endif

    <!-- FORM NHẬP MÃ OTP 6 SỐ -->
    <form method="POST" action="{{ url('/email/verify-otp') }}" class="mb-4">
        @csrf
        <div class="mb-3 text-center">
            <label for="otp_input" class="form-label small fw-bold text-dark mb-2">
                Nhập mã xác thực 6 số:
            </label>
            <div class="d-flex justify-content-center">
                <input type="text" 
                       name="otp" 
                       id="otp_input" 
                       class="form-control form-control-lg text-center font-monospace fw-bold fs-2 border-2 shadow-sm @error('otp') is-invalid @enderror" 
                       placeholder="••••••" 
                       maxlength="6" 
                       pattern="[0-9]{6}" 
                       inputmode="numeric"
                       style="max-width: 250px; letter-spacing: 10px; border-color: #d97706; background-color: #fffbeb;"
                       required 
                       autofocus>
            </div>
            <div class="form-text text-muted small mt-2" style="font-size: 0.75rem;">
                <i class="bi bi-clock-history me-1"></i> Mã OTP có hiệu lực trong vòng 10 phút.
            </div>
        </div>

        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold shadow-sm fs-6">
            <i class="bi bi-shield-check me-1"></i> Xác Nhận Mã OTP
        </button>
    </form>

    <!-- KHU VỰC GỬI LẠI MÃ & ĐĂNG XUẤT -->
    <div class="border-top pt-3">
        <div class="d-flex justify-content-between align-items-center">
            <form method="POST" action="{{ url('/email/verification-notification') }}">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none text-primary p-0 small fw-semibold">
                    <i class="bi bi-arrow-clockwise me-1"></i> Chưa nhận được? Gửi lại mã OTP
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

    @section('auth_footer')
        <div class="text-muted small">
            <i class="bi bi-shield-fill-check text-success me-1"></i> Hệ thống xác thực danh tính bảo mật 2 lớp Rèm Online.
        </div>
    @endsection
</x-guest-layout>
