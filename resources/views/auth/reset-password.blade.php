<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="mb-3 text-success fs-1">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Đặt Lại Mật Khẩu</h5>
        <p class="text-muted small">Vui lòng thiết lập mật khẩu mới cho tài khoản của bạn</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1 text-secondary"></i> Địa chỉ Email
            </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                   readonly>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-1 text-secondary"></i> Mật khẩu mới <span class="text-danger">*</span>
            </label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="new-password"
                   placeholder="Tối thiểu 8 ký tự">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="bi bi-shield-check me-1 text-secondary"></i> Xác nhận mật khẩu mới <span class="text-danger">*</span>
            </label>
            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   name="password_confirmation" required autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu mới">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-gold py-2">
                <i class="bi bi-check2-circle me-2"></i> Lưu Mật Khẩu Mới
            </button>
        </div>
    </form>
</x-guest-layout>
