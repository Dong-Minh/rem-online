@extends('layouts.client')

@section('title', 'Cài Đặt Tài Khoản — ' . config('app.name', 'Rèm Online'))

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang Chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Tài Khoản</a></li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Cài Đặt Tài Khoản</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 text-center bg-light border-bottom">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-4" style="width: 70px; height: 70px; border: 3px solid #b8860b;">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>
                </div>
                <div class="list-group list-group-flush py-2">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-speedometer2 text-muted fs-5"></i>
                        <span>Bảng Tổng Quan</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-bag-check text-muted fs-5"></i>
                        <span>Đơn Hàng Của Tôi</span>
                    </a>
                    <a href="{{ route('profile.addresses.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-geo-alt text-muted fs-5"></i>
                        <span>Sổ Địa Chỉ Nhận Hàng</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3 active bg-warning bg-opacity-10 text-dark fw-bold border-start border-4 border-warning">
                        <i class="bi bi-person-gear text-warning fs-5"></i>
                        <span>Cài Đặt Tài Khoản</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div>Thông tin tài khoản của bạn đã được cập nhật thành công!</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-4 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-shield-check fs-5 text-success"></i>
                    <div>Mật khẩu tài khoản đã được đổi thành công!</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form 1: Cập Nhật Thông Tin Cá Nhân -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-person-lines-fill text-gold"></i> Thông Tin Cá Nhân
                    </h5>
                    <p class="text-muted small mb-0">Cập nhật họ tên, địa chỉ email và số điện thoại liên hệ của bạn.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small">Địa Chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                <div class="alert alert-warning d-flex align-items-center justify-content-between rounded-3 mt-3 py-2 px-3 small">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                        <span>Email của bạn chưa được xác thực.</span>
                                    </div>
                                    <button type="submit" form="send-verification-form" class="btn btn-sm btn-outline-dark fw-semibold">
                                        Gửi lại email xác thực
                                    </button>
                                </div>
                            @else
                                <div class="text-success small mt-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-patch-check-fill"></i> Email đã được xác thực chính chủ.
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold small">Số Điện Thoại Liên Hệ</label>
                            <input type="text" class="form-control rounded-3 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0912345678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-gold px-4 py-2 fw-semibold rounded-3">
                            <i class="bi bi-save me-1"></i> Lưu Thay Đổi
                        </button>
                    </form>

                    <form id="send-verification-form" method="POST" action="{{ route('verification.send') }}" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- Form 2: Đổi Mật Khẩu -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-gold"></i> Đổi Mật Khẩu
                    </h5>
                    <p class="text-muted small mb-0">Đảm bảo tài khoản của bạn luôn được bảo vệ bằng mật khẩu mạnh và an toàn.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="update_password_current_password" class="form-label fw-semibold small">Mật Khẩu Hiện Tại <span class="text-danger">*</span></label>
                            <input type="password" class="form-control rounded-3 @error('current_password', 'updatePassword') is-invalid @enderror" id="update_password_current_password" name="current_password" autocomplete="current-password" required>
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password" class="form-label fw-semibold small">Mật Khẩu Mới <span class="text-danger">*</span></label>
                            <input type="password" class="form-control rounded-3 @error('password', 'updatePassword') is-invalid @enderror" id="update_password_password" name="password" autocomplete="new-password" required>
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="update_password_password_confirmation" class="form-label fw-semibold small">Xác Nhận Mật Khẩu Mới <span class="text-danger">*</span></label>
                            <input type="password" class="form-control rounded-3 @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-dark px-4 py-2 fw-semibold rounded-3">
                            <i class="bi bi-key me-1"></i> Cập Nhật Mật Khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
