@extends('layouts.client')

@section('title', 'Sổ Địa Chỉ Nhận Hàng — ' . config('app.name', 'Rèm Online'))

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang Chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Tài Khoản</a></li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Sổ Địa Chỉ</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
                    <a href="{{ route('profile.addresses.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3 active bg-warning bg-opacity-10 text-dark fw-bold border-start border-4 border-warning">
                        <i class="bi bi-geo-alt-fill text-warning fs-5"></i>
                        <span>Sổ Địa Chỉ Nhận Hàng</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-person-gear text-muted fs-5"></i>
                        <span>Cài Đặt Tài Khoản</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Sổ Địa Chỉ Giao Hàng</h4>
                    <p class="text-muted small mb-0">Quản lý các địa chỉ nhận hàng để thanh toán nhanh hơn khi đặt mua rèm.</p>
                </div>
                <button type="button" class="btn btn-dark btn-gold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bi bi-plus-circle me-2"></i>Thêm Địa Chỉ Mới
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                @forelse($addresses as $addr)
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border rounded-4 shadow-sm position-relative {{ $addr->is_default ? 'border-warning border-2' : '' }}">
                            @if($addr->is_default)
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                                        <i class="bi bi-star-fill me-1"></i>Mặc định
                                    </span>
                                </div>
                            @endif

                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-person-circle fs-5 text-secondary"></i>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $addr->recipient_name }}</h5>
                                </div>

                                <div class="text-muted small mb-3">
                                    <i class="bi bi-telephone me-2 text-gold"></i>{{ $addr->phone }}
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-3 text-secondary small lh-base">
                                    <div class="fw-semibold text-dark mb-1">{{ $addr->address_detail }}</div>
                                    <div>{{ $addr->ward }}, {{ $addr->district }}, {{ $addr->province }}</div>
                                </div>

                                <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 border-top gap-2">
                                    <div>
                                        @if(!$addr->is_default)
                                            <form method="POST" action="{{ route('profile.addresses.default', $addr) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                    Đặt làm mặc định
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $addr->id }}">
                                            <i class="bi bi-pencil me-1"></i>Sửa
                                        </button>

                                        @if(!$addr->is_default || $addresses->count() === 1)
                                            <form method="POST" action="{{ route('profile.addresses.destroy', $addr) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit Address -->
                        <div class="modal fade" id="editAddressModal{{ $addr->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form method="POST" action="{{ route('profile.addresses.update', $addr) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom px-4 py-3">
                                            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Cập Nhật Địa Chỉ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Họ và Tên Người Nhận <span class="text-danger">*</span></label>
                                                <input type="text" name="recipient_name" class="form-control rounded-3" value="{{ old('recipient_name', $addr->recipient_name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Số Điện Thoại <span class="text-danger">*</span></label>
                                                <input type="tel" name="phone" class="form-control rounded-3" value="{{ old('phone', $addr->phone) }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold small">Tỉnh / TP <span class="text-danger">*</span></label>
                                                    <input type="text" name="province" class="form-control rounded-3" value="{{ old('province', $addr->province) }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold small">Quận / Huyện <span class="text-danger">*</span></label>
                                                    <input type="text" name="district" class="form-control rounded-3" value="{{ old('district', $addr->district) }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold small">Phường / Xã <span class="text-danger">*</span></label>
                                                    <input type="text" name="ward" class="form-control rounded-3" value="{{ old('ward', $addr->ward) }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Địa Chỉ Chi Tiết (Số nhà, ngõ, tên đường) <span class="text-danger">*</span></label>
                                                <textarea name="address_detail" rows="2" class="form-control rounded-3" required>{{ old('address_detail', $addr->address_detail) }}</textarea>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="editDefaultCheck{{ $addr->id }}" {{ $addr->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="editDefaultCheck{{ $addr->id }}">
                                                    Đặt làm địa chỉ nhận hàng mặc định
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top px-4 py-3">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-dark btn-gold rounded-pill px-4">Lưu Thay Đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                            <div class="card-body">
                                <i class="bi bi-geo-alt fs-1 text-secondary opacity-50 d-block mb-3"></i>
                                <h5 class="fw-bold text-dark">Bạn chưa có địa chỉ nhận hàng nào</h5>
                                <p class="text-muted small mb-4">Hãy thêm địa chỉ giao hàng để thuận tiện khi đặt may rèm cửa nhé.</p>
                                <button type="button" class="btn btn-dark btn-gold px-4 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="bi bi-plus-circle me-2"></i>Thêm Địa Chỉ Ngay
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Address -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('profile.addresses.store') }}">
                @csrf
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle me-2 text-warning"></i>Thêm Địa Chỉ Nhận Hàng Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ và Tên Người Nhận <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control rounded-3" placeholder="Ví dụ: Nguyễn Văn An" value="{{ old('recipient_name', Auth::user()->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Số Điện Thoại Nhận Hàng <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control rounded-3" placeholder="Ví dụ: 0912345678" value="{{ old('phone') }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tỉnh / TP <span class="text-danger">*</span></label>
                            <input type="text" name="province" class="form-control rounded-3" placeholder="Hà Nội" value="{{ old('province') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Quận / Huyện <span class="text-danger">*</span></label>
                            <input type="text" name="district" class="form-control rounded-3" placeholder="Nam Từ Liêm" value="{{ old('district') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Phường / Xã <span class="text-danger">*</span></label>
                            <input type="text" name="ward" class="form-control rounded-3" placeholder="Mỹ Đình 1" value="{{ old('ward') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Địa Chỉ Chi Tiết (Số nhà, ngõ, tên đường) <span class="text-danger">*</span></label>
                        <textarea name="address_detail" rows="2" class="form-control rounded-3" placeholder="Số 322/76 ngách 76 ngõ 322 Mỹ Đình" required>{{ old('address_detail') }}</textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="newDefaultCheck" {{ $addresses->count() === 0 ? 'checked' : '' }}>
                        <label class="form-check-label small" for="newDefaultCheck">
                            Đặt làm địa chỉ nhận hàng mặc định
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-dark btn-gold rounded-pill px-4">Lưu Địa Chỉ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
