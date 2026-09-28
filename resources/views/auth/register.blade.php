<x-guest-layout>
    <div class="mb-4 text-center">
        <h5 class="fw-bold text-dark mb-1">Tạo Tài Khoản Mới</h5>
        <p class="text-muted small">Đăng ký để nhận ưu đãi và quản lý đơn hàng dễ dàng</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="bi bi-person me-1 text-secondary"></i> Họ và tên <span class="text-danger">*</span>
            </label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Nguyễn Văn A">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1 text-secondary"></i> Địa chỉ Email <span class="text-danger">*</span>
            </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="nguyenvana@gmail.com">
            <div class="form-text small text-muted">Hệ thống sẽ gửi email xác thực tài khoản tới hòm thư này.</div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone Number -->
        <div class="mb-3">
            <label for="phone" class="form-label">
                <i class="bi bi-telephone me-1 text-secondary"></i> Số điện thoại
            </label>
            <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" 
                   name="phone" value="{{ old('phone') }}" autocomplete="tel"
                   placeholder="0912345678">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-shield-lock me-1 text-secondary"></i> Mật khẩu <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password"
                       placeholder="Tối thiểu 8 ký tự">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="bi bi-shield-check me-1 text-secondary"></i> Nhập lại mật khẩu <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                       name="password_confirmation" required autocomplete="new-password"
                       placeholder="Nhập lại đúng mật khẩu trên">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-gold py-2">
                <i class="bi bi-person-plus me-2"></i> Hoàn Tất Đăng Ký
            </button>
        </div>
    </form>

    @section('auth_footer')
        <span>Đã có tài khoản?</span>
        <a href="{{ route('login') }}" class="auth-link ms-1">Đăng nhập ngay</a>
    @endsection

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</x-guest-layout>
