@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Mã Giảm Giá ' . $voucher->code)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách mã giảm giá
    </a>
    <h4 class="fw-bold text-dark mt-2 mb-1">
        <i class="bi bi-pencil-square text-warning me-2"></i>Chỉnh Sửa Mã Giảm Giá: <span class="font-monospace text-primary">{{ $voucher->code }}</span>
    </h4>
    <p class="text-muted small">Cập nhật hạn mức, điều kiện áp dụng hoặc thời gian hiệu lực của voucher.</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger shadow-sm rounded-3 mb-4">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Vui lòng kiểm tra lại các lỗi:</h6>
        <ul class="mb-0 small ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Cột trái: Thông tin cơ bản & Mức giảm -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">1. Thông Tin Mã Khuyến Mãi</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label small fw-bold">Mã Voucher Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase font-monospace @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code', $voucher->code) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label small fw-bold">Mô tả / Tên chương trình</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" 
                               id="description" name="description" value="{{ old('description', $voucher->description) }}">
                    </div>
                </div>

                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3 mt-4">2. Thiết Lập Mức Giảm Giá</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="discount_type" class="form-label small fw-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                        <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" onchange="toggleDiscountType(this.value)" required>
                            <option value="fixed" {{ old('discount_type', $voucher->discount_type) == 'fixed' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                            <option value="percent" {{ old('discount_type', $voucher->discount_type) == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="discount_value" class="form-label small fw-bold">Mức Giảm <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control @error('discount_value') is-invalid @enderror" 
                                   id="discount_value" name="discount_value" value="{{ old('discount_value', (float) $voucher->discount_value) }}" required>
                            <span class="input-group-text font-monospace" id="discount_unit_label">VNĐ</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Giảm tối đa (Chỉ áp dụng cho %) -->
                    <div class="col-md-6" id="max_discount_group" style="display: none;">
                        <label for="max_discount_amount" class="form-label small fw-bold">Số tiền giảm tối đa (VNĐ)</label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control @error('max_discount_amount') is-invalid @enderror" 
                                   id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : '') }}">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        <div class="form-text small">Để trống nếu không giới hạn mức giảm tối đa.</div>
                    </div>

                    <!-- Đơn hàng tối thiểu -->
                    <div class="col-md-6">
                        <label for="min_order_amount" class="form-label small fw-bold">Giá trị đơn hàng tối thiểu (VNĐ)</label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                   id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', (float) $voucher->min_order_amount) }}">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Hạn mức & Thời gian -->
        <div class="col-12 col-lg-4">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">3. Hạn Mức & Thời Hạn</h5>

                <div class="mb-3">
                    <label for="usage_limit" class="form-label small fw-bold">Tổng số lượt dùng toàn hệ thống</label>
                    <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                           id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}">
                </div>

                <div class="mb-3">
                    <label for="usage_per_user" class="form-label small fw-bold">Số lượt dùng tối đa / 1 khách hàng <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('usage_per_user') is-invalid @enderror" 
                           id="usage_per_user" name="usage_per_user" value="{{ old('usage_per_user', $voucher->usage_per_user) }}" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="starts_at" class="form-label small fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                           id="starts_at" name="starts_at" value="{{ old('starts_at', $voucher->starts_at->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="ends_at" class="form-label small fw-bold">Thời gian kết thúc <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" 
                           id="ends_at" name="ends_at" value="{{ old('ends_at', $voucher->ends_at->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold small" for="is_active">Kích hoạt áp dụng</label>
                </div>

                <hr class="my-3">

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-gold py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Lưu Cập Nhật Voucher
                    </button>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary py-2">
                        Hủy Bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function toggleDiscountType(type) {
        const maxGroup = document.getElementById('max_discount_group');
        const unitLabel = document.getElementById('discount_unit_label');
        if (type === 'percent') {
            maxGroup.style.display = 'block';
            unitLabel.textContent = '%';
        } else {
            maxGroup.style.display = 'none';
            unitLabel.textContent = 'VNĐ';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleDiscountType(document.getElementById('discount_type').value);
    });
</script>
@endpush
@endsection
