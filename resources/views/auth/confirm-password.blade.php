<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="mb-3 text-danger fs-1">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Xác Nhận Mật Khẩu</h5>
        <p class="text-muted small">
            Đây là khu vực bảo mật an toàn. Vui lòng xác nhận lại mật khẩu của bạn trước khi tiếp tục.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="bi bi-key me-1 text-secondary"></i> Mật khẩu của bạn
            </label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-gold py-2">
                <i class="bi bi-check-lg me-2"></i> Xác Nhận
            </button>
        </div>
    </form>
</x-guest-layout>
