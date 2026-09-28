<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="mb-3 text-warning fs-1">
            <i class="bi bi-envelope-check-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Xác Thực Địa Chỉ Email</h5>
        <p class="text-muted small">
            Cảm ơn bạn đã đăng ký tài khoản tại <strong>Rèm Online</strong>! Trước khi bắt đầu mua sắm hoặc sử dụng các tính năng nâng cao, vui lòng kiểm tra hòm thư và nhấn vào liên kết xác nhận chúng tôi vừa gửi cho bạn.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-send-check me-2 fs-5"></i>
            <div>
                Một liên kết xác minh mới vừa được gửi tới địa chỉ email bạn đã cung cấp khi đăng ký.
            </div>
        </div>
    @endif

    <div class="d-flex flex-column gap-2 mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-gold py-2">
                    <i class="bi bi-arrow-repeat me-2"></i> Gửi Lại Email Xác Minh
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center mt-2">
            @csrf
            <button type="submit" class="btn btn-link text-muted small text-decoration-none">
                <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất tài khoản
            </button>
        </form>
    </div>

    @section('auth_footer')
        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Không nhận được email? Hãy kiểm tra cả thư mục <strong>Spam / Rác</strong> hoặc nhấn nút gửi lại phía trên.
        </div>
    @endsection
</x-guest-layout>
