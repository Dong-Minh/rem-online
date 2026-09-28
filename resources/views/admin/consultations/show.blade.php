@extends('layouts.admin')

@section('title', 'Chi Tiết Lịch Hẹn Khảo Sát — ' . $consultation->code)
@section('page_title', 'Chi Tiết Lịch Hẹn #' . $consultation->code)

@section('content')
<div class="container-fluid px-0">
    <!-- Header Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2" title="Quay lại danh sách">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Lịch Hẹn: <span class="font-monospace text-gold">{{ $consultation->code }}</span></h4>
                <small class="text-muted">Đăng ký lúc: {{ $consultation->created_at->format('d/m/Y H:i:s') }} ({{ $consultation->created_at->diffForHumans() }})</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="tel:{{ $consultation->phone }}" class="btn btn-success btn-sm px-3 shadow-sm">
                <i class="bi bi-telephone-fill me-1"></i> Gọi Cho Khách ({{ $consultation->phone }})
            </a>
            <form method="POST" action="{{ route('admin.consultations.destroy', $consultation) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa lịch hẹn này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                    <i class="bi bi-trash me-1"></i> Xóa
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Cột Trái: Thông Tin Khách Hàng & Nhu Cầu Rèm -->
        <div class="col-12 col-lg-7">
            <!-- Card 1: Khách hàng & Địa chỉ -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-person-badge text-warning me-2"></i>Thông Tin Khách Hàng & Địa Điểm Khảo Sát</h6>
                    {!! $consultation->status_badge !!}
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small d-block">Họ và Tên Khách Hàng:</label>
                            <span class="fw-bold text-dark fs-5">{{ $consultation->customer_name }}</span>
                            @if($consultation->user_id)
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size: 0.7rem;">Thành viên</span>
                            @endif
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small d-block">Số Điện Thoại:</label>
                            <a href="tel:{{ $consultation->phone }}" class="fw-bold text-danger text-decoration-none fs-5">
                                {{ $consultation->phone }}
                            </a>
                        </div>
                        @if($consultation->email)
                            <div class="col-12">
                                <label class="text-muted small d-block">Email:</label>
                                <span class="fw-semibold text-dark">{{ $consultation->email }}</span>
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="text-muted small d-block">Địa Chỉ Khảo Sát & Đo Đạc:</label>
                            <div class="p-3 bg-light rounded-3 text-dark fw-semibold">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $consultation->full_address }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Chi tiết lịch hẹn & Mẫu rèm quan tâm -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-check text-warning me-2"></i>Thời Gian Hẹn & Mẫu Rèm Cần Mang Đến</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small d-block mb-1">Ngày Hẹn Khảo Sát:</label>
                                <span class="fw-bold text-dark fs-5"><i class="bi bi-calendar-event me-2 text-warning"></i>{{ $consultation->preferred_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small d-block mb-1">Khung Giờ Đã Chọn:</label>
                                <span class="fw-bold text-dark fs-5"><i class="bi bi-clock me-2 text-warning"></i>{{ $consultation->time_slot_text }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-2">Số Lượng Ô Cửa Dự Kiến:</label>
                        <span class="badge bg-dark px-3 py-2 rounded-pill fs-6 fw-semibold">
                            <i class="bi bi-window me-1 text-warning"></i> {{ $consultation->estimated_windows ?? 'Chưa xác định' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-2">Các Dòng Mẫu Vải Rèm Cần Mang Theo:</label>
                        @if(is_array($consultation->curtain_types) && count($consultation->curtain_types) > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($consultation->curtain_types as $type)
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-3">
                                        <i class="bi bi-check2 text-warning me-1"></i>{{ $type }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted fst-italic">Mang theo toàn bộ catalogue rèm cao cấp mới nhất</span>
                        @endif
                    </div>

                    @if($consultation->notes)
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 mt-3">
                            <label class="text-dark small fw-bold d-block mb-1"><i class="bi bi-chat-left-quote me-1"></i> Ghi Chú Yêu Cầu Của Khách:</label>
                            <p class="mb-0 text-dark small">{{ $consultation->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cột Phải: Form Cập Nhật Tiến Độ & Phân Công Thợ -->
        <div class="col-12 col-lg-5">
            <div class="card card-custom sticky-top" style="top: 85px;">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="fw-bold mb-0 text-white"><i class="bi bi-gear-wide-connected text-warning me-2"></i>Xử Lý & Điều Phối Lịch Hẹn</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.consultations.update', $consultation) }}">
                        @csrf
                        @method('PUT')

                        <!-- Cập nhật trạng thái -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Trạng Thái Lịch Hẹn <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-lg fs-6 rounded-3">
                                <option value="pending" {{ $consultation->status === 'pending' ? 'selected' : '' }}>⏳ Chờ tiếp nhận</option>
                                <option value="assigned" {{ $consultation->status === 'assigned' ? 'selected' : '' }}>👤 Đã phân công thợ</option>
                                <option value="measuring" {{ $consultation->status === 'measuring' ? 'selected' : '' }}>📏 Đang đo đạc tại nhà</option>
                                <option value="quoted" {{ $consultation->status === 'quoted' ? 'selected' : '' }}>📝 Đã báo giá sau khi đo</option>
                                <option value="completed" {{ $consultation->status === 'completed' ? 'selected' : '' }}>✅ Đã chốt may đo đơn hàng</option>
                                <option value="cancelled" {{ $consultation->status === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy lịch</option>
                            </select>
                        </div>

                        <!-- Phân công thợ kỹ thuật -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Thợ Kỹ Thuật / NV Phụ Trách Đo:</label>
                            <select name="assigned_staff_id" class="form-select rounded-3">
                                <option value="">-- Chưa phân công thợ --</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ $consultation->assigned_staff_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->phone ?? $staff->email }}) — {{ ucfirst($staff->role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Báo giá dự toán sau khảo sát -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Tổng Tiền Báo Giá Dự Toán (₫):</label>
                            <div class="input-group">
                                <input type="number" step="1000" name="quoted_amount" class="form-control rounded-3" 
                                       placeholder="Ví dụ: 8500000" 
                                       value="{{ old('quoted_amount', $consultation->quoted_amount) }}">
                                <span class="input-group-text">₫</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Điền sau khi thợ đo đạc xong kích thước thực tế và tính $m^2$.</small>
                        </div>

                        <!-- Ghi chú nội bộ admin -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Ghi Chú Kết Quả Khảo Sát (Nội bộ):</label>
                            <textarea name="admin_notes" rows="4" class="form-control rounded-3" 
                                      placeholder="Ví dụ: Khách chọn vải gấm Melbourne màu xám ghi cho 3 cửa phòng ngủ (15.5m2), hẹn lắp đặt thứ 6...">{{ old('admin_notes', $consultation->admin_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-dark btn-gold w-100 py-3 rounded-pill fw-bold shadow">
                            <i class="bi bi-save me-1"></i> Lưu Cập Nhật Lịch Hẹn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
