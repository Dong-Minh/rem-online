<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="mb-3 text-warning fs-1">
            <i class="bi bi-key-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Quên Mật Khẩu?</h5>
        <p class="text-muted small">
            Đừng lo lắng! Hãy nhập email đã đăng ký của bạn. Chúng tôi sẽ gửi đường dẫn đặt lại mật khẩu an toàn tới email của bạn.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1 text-secondary"></i> Địa chỉ Email của bạn
            </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autofocus
                   placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-gold py-2">
                <i class="bi bi-send me-2"></i> Gửi Link Đặt Lại Mật Khẩu
            </button>
        </div>
    </form>

    @section('auth_footer')
        <a href="{{ route('login') }}" class="auth-link">
            <i class="bi bi-arrow-left me-1"></i> Quay lại Đăng nhập
        </a>
    @endsection
</x-guest-layout>
