<x-guest-layout>
    <div class="mb-4 text-center">
        <h5 class="fw-bold text-dark mb-1">Đăng Nhập Tài Khoản</h5>
        <p class="text-muted small">Chào mừng bạn quay trở lại với Rèm Online</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1 text-secondary"></i> Địa chỉ Email
            </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                   placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">
                    <i class="bi bi-shield-lock me-1 text-secondary"></i> Mật khẩu
                </label>
                @if (Route::has('password.request'))
                    <a class="small auth-link" href="{{ route('password.request') }}">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="current-password"
                       placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remember Me -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label small text-muted">
                Ghi nhớ đăng nhập trên thiết bị này
            </label>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-gold py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i> Đăng Nhập
            </button>
        </div>
    </form>

    <!-- Demo Account Quick Fill Helper (Phục vụ chấm bài/LAB) -->
    <div class="mt-4 pt-3 border-top">
        <p class="text-muted text-center small fw-semibold mb-2">Tài khoản demo sẵn có để test:</p>
        <div class="d-flex flex-wrap gap-1 justify-content-center">
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" 
                    onclick="fillAccount('admin@remonline.vn', 'password')">
                <i class="bi bi-person-badge"></i> Admin
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" 
                    onclick="fillAccount('staff@remonline.vn', 'password')">
                <i class="bi bi-person-workspace"></i> Staff
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" 
                    onclick="fillAccount('customer@remonline.vn', 'password')">
                <i class="bi bi-person"></i> Khách hàng
            </button>
        </div>
    </div>

    @section('auth_footer')
        <span>Chưa có tài khoản?</span>
        <a href="{{ route('register') }}" class="auth-link ms-1">Đăng ký tài khoản mới</a>
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

        function fillAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
