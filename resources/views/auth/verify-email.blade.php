<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="mb-3 text-warning fs-1">
            <i class="bi bi-envelope-check-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Xác Thực Địa Chỉ Email</h5>
        <p class="text-muted small">
            Cảm ơn bạn đã đăng ký tài khoản tại <strong>Rèm Online</strong>! Hệ thống đã gửi một liên kết xác minh đến email:
        </p>
        <div class="badge bg-light text-primary border px-3 py-2 fs-6 font-monospace mb-2">
            <i class="bi bi-envelope-at me-1"></i> {{ auth()->user()->email ?? 'Email của bạn' }}
        </div>
        <p class="text-muted small">
            Vui lòng kiểm tra hòm thư (kể cả mục Spam/Thư rác) và nhấn vào liên kết xác nhận để kích hoạt đầy đủ quyền mua hàng.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-send-check me-2 fs-5"></i>
            <div>
                Một liên kết xác minh mới vừa được gửi tới hòm thư của bạn!
            </div>
        </div>
    @endif

    <div class="d-flex flex-column gap-3 mt-4">
        <!-- 1. Nút kích hoạt nhanh dành cho Chấm thi & Demo -->
        <div class="p-3 bg-success-subtle border border-success-subtle rounded-3 text-center">
            <div class="small fw-bold text-success mb-2">
                <i class="bi bi-patch-check-fill me-1"></i> Dành cho Giảng viên / Chấm thi / Demo trực tiếp:
            </div>
            <form method="POST" action="{{ route('verification.instant') }}">
                @csrf
                <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Kích Hoạt Nhanh Tài Khoản Này
                </button>
            </form>
            <div class="form-text text-muted" style="font-size: 0.75rem;">
                Nhấn để bỏ qua chờ email và xác thực tài khoản tức thì.
            </div>
        </div>

        <!-- 2. Gửi lại email -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-outline-secondary py-2">
                    <i class="bi bi-arrow-repeat me-2"></i> Gửi Lại Email Xác Minh
                </button>
            </div>
        </form>

        <!-- 3. Đăng xuất -->
        <form method="POST" action="{{ route('logout') }}" class="text-center mt-1">
            @csrf
            <button type="submit" class="btn btn-link text-muted small text-decoration-none">
                <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất tài khoản
            </button>
        </form>
    </div>

    @section('auth_footer')
        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Không nhận được email? Hãy kiểm tra cả thư mục <strong>Spam / Rác</strong> hoặc dùng nút <strong>Kích Hoạt Nhanh</strong> phía trên.
        </div>
    @endsection
</x-guest-layout>
