@extends('layouts.client')

@section('title', 'Đặt Lịch Khảo Sát & Đo Đạc Rèm Tận Nhà — ' . config('app.name', 'Rèm Online'))

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Đặt lịch khảo sát & Đo đạc rèm</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <!-- Hero Banner -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 text-white mb-5 position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, #1a2232 0%, #2a374f 100%);">
        <div class="row align-items-center position-relative z-2">
            <div class="col-12 col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-3">
                    <i class="bi bi-patch-check-fill me-1"></i> Dịch vụ cao cấp hoàn toàn Miễn Phí
                </span>
                <h2 class="display-6 fw-bold mb-3 text-white">Khảo Sát & Mang Mẫu Vải Rèm Tận Nhà</h2>
                <p class="text-light text-opacity-75 lead mb-4" style="font-size: 1.05rem;">
                    Bạn băn khoăn về chất liệu vải, khả năng cản sáng hay chưa chắc chắn cách đo ô cửa sổ? Hãy để chuyên viên của Rèm Online mang hơn <strong>100+ mẫu vải thực tế</strong> đến tận nhà tư vấn, đo đạc chuẩn milimet và lên phương án tối ưu nhất.
                </p>

                <div class="d-flex flex-wrap gap-4 pt-2 border-top border-secondary border-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-warning fs-5"></i>
                        <span class="small fw-semibold">Miễn phí 100% khảo sát</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-collection-fill text-warning fs-5"></i>
                        <span class="small fw-semibold">Trực tiếp sờ & cảm nhận mẫu vải</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-warning fs-5"></i>
                        <span class="small fw-semibold">Thợ có mặt đúng giờ hẹn</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 text-center mt-4 mt-lg-0 d-none d-lg-block">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 border border-white border-opacity-10 backdrop-blur">
                    <i class="bi bi-rulers text-warning display-3 mb-2 d-block"></i>
                    <h5 class="fw-bold text-white mb-1">Đo Đạc Chuẩn Xác</h5>
                    <p class="small text-light text-opacity-75 mb-0">Hỗ trợ lắp đặt trọn gói trong 48 giờ sau khi chốt mẫu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Success Alert Modal / Card -->
    @if(session('booking_success'))
        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 mb-5 border-start border-5 border-success bg-white">
            <div class="row align-items-center">
                <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center p-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-check2-circle display-4"></i>
                    </div>
                </div>
                <div class="col-12 col-md-10">
                    <span class="badge bg-success px-3 py-1 rounded-pill mb-2 fw-bold">Tiếp Nhận Lịch Hẹn Thành Công</span>
                    <h4 class="fw-bold text-dark mb-2">Cảm ơn quý khách {{ session('customer_name') }}!</h4>
                    <p class="text-secondary mb-3">
                        Yêu cầu khảo sát đo đạc rèm tận nhà của bạn đã được ghi nhận vào hệ thống với mã tiếp nhận:
                        <strong class="text-danger font-monospace fs-5">#{{ session('consultation_code') }}</strong>
                    </p>
                    <div class="p-3 bg-light rounded-3 mb-3 small text-secondary">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <i class="bi bi-calendar-event me-2 text-warning"></i><strong>Ngày hẹn:</strong> {{ session('preferred_date') }} ({{ session('time_slot') }})
                            </div>
                            <div class="col-12 col-md-6">
                                <i class="bi bi-geo-alt me-2 text-warning"></i><strong>Địa chỉ:</strong> {{ session('full_address') }}
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Chuyên viên kỹ thuật của Rèm Online sẽ gọi điện xác nhận lại lịch hẹn với bạn trong vòng <strong>30 phút</strong>. Hotline hỗ trợ khẩn cấp: <strong>0901.234.567</strong>.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4 g-xl-5">
        <!-- Form Đặt Lịch Hẹn (Cột Trái) -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar-plus fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Phiếu Đăng Ký Khảo Sát & Đo Rèm Tận Nhà</h4>
                        <small class="text-muted">Vui lòng cung cấp thông tin để chúng tôi chuẩn bị bảng mẫu vải phù hợp nhất.</small>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Vui lòng kiểm tra lại thông tin:</h6>
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('consultations.store') }}">
                    @csrf

                    <!-- 1. THÔNG TIN KHÁCH HÀNG -->
                    <h6 class="fw-bold text-dark text-uppercase small mb-3 text-gold">
                        <i class="bi bi-person-lines-fill me-1"></i> 1. Thông Tin Khách Hàng
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control form-control-lg rounded-3 fs-6" 
                                   placeholder="Ví dụ: Nguyễn Văn An" 
                                   value="{{ old('customer_name', $user->name ?? '') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Số Điện Thoại Liên Hệ <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control form-control-lg rounded-3 fs-6" 
                                   placeholder="Ví dụ: 0912345678" 
                                   value="{{ old('phone', $user->phone ?? ($defaultAddress->phone ?? '')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Email nhận xác nhận (không bắt buộc)</label>
                            <input type="email" name="email" class="form-control rounded-3" 
                                   placeholder="email@example.com" 
                                   value="{{ old('email', $user->email ?? '') }}">
                        </div>
                    </div>

                    <!-- 2. ĐỊA CHỈ KHẢO SÁT -->
                    <h6 class="fw-bold text-dark text-uppercase small mb-3 text-gold">
                        <i class="bi bi-geo-alt-fill me-1"></i> 2. Địa Chỉ Nhà Khảo Sát & Đo Đạc
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Tỉnh / Thành Phố <span class="text-danger">*</span></label>
                            <input type="text" name="province" class="form-control rounded-3" 
                                   placeholder="Hà Nội" 
                                   value="{{ old('province', $defaultAddress->province ?? 'Hà Nội') }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Quận / Huyện <span class="text-danger">*</span></label>
                            <input type="text" name="district" class="form-control rounded-3" 
                                   placeholder="Nam Từ Liêm" 
                                   value="{{ old('district', $defaultAddress->district ?? '') }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Phường / Xã</label>
                            <input type="text" name="ward" class="form-control rounded-3" 
                                   placeholder="Mỹ Đình 1" 
                                   value="{{ old('ward', $defaultAddress->ward ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Địa chỉ chi tiết (Số nhà, tên ngõ, số phòng chung cư, tên tòa nhà) <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control rounded-3" 
                                   placeholder="Ví dụ: P1204 Tòa R1 Vinhomes Smart City, Tây Mỗ" 
                                   value="{{ old('address', $defaultAddress->address_detail ?? '') }}" required>
                        </div>
                    </div>

                    <!-- 3. THỜI GIAN HẸN KHẢO SÁT -->
                    <h6 class="fw-bold text-dark text-uppercase small mb-3 text-gold">
                        <i class="bi bi-clock-fill me-1"></i> 3. Thời Gian Khảo Sát Mong Muốn
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-5">
                            <label class="form-label small fw-semibold">Ngày Hẹn <span class="text-danger">*</span></label>
                            <input type="date" name="preferred_date" class="form-control form-control-lg rounded-3 fs-6" 
                                   min="{{ date('Y-m-d') }}" 
                                   value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}" required>
                        </div>
                        <div class="col-12 col-md-7">
                            <label class="form-label small fw-semibold d-block">Khung Giờ Thuận Tiện <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="border rounded-3 p-2 text-center w-100 cursor-pointer time-slot-card h-100 d-flex flex-column justify-content-center">
                                        <input type="radio" name="preferred_time_slot" value="morning" class="d-none" {{ old('preferred_time_slot', 'morning') === 'morning' ? 'checked' : '' }} onchange="updateTimeSlotStyles()">
                                        <div class="fw-bold small text-dark">Buổi Sáng</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">08h - 12h</small>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="border rounded-3 p-2 text-center w-100 cursor-pointer time-slot-card h-100 d-flex flex-column justify-content-center">
                                        <input type="radio" name="preferred_time_slot" value="afternoon" class="d-none" {{ old('preferred_time_slot') === 'afternoon' ? 'checked' : '' }} onchange="updateTimeSlotStyles()">
                                        <div class="fw-bold small text-dark">Buổi Chiều</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">13h30 - 17h30</small>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="border rounded-3 p-2 text-center w-100 cursor-pointer time-slot-card h-100 d-flex flex-column justify-content-center">
                                        <input type="radio" name="preferred_time_slot" value="evening" class="d-none" {{ old('preferred_time_slot') === 'evening' ? 'checked' : '' }} onchange="updateTimeSlotStyles()">
                                        <div class="fw-bold small text-dark">Buổi Tối</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">18h - 20h30</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. NHU CẦU TƯ VẤN & LOẠI RÈM -->
                    <h6 class="fw-bold text-dark text-uppercase small mb-3 text-gold">
                        <i class="bi bi-palette-fill me-1"></i> 4. Loại Rèm & Số Lượng Cửa Bạn Quan Tâm
                    </h6>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Chọn các dòng rèm bạn muốn xem mẫu vải thực tế:</label>
                        <div class="row g-2">
                            @php
                                $sampleCurtains = [
                                    'Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)',
                                    'Rèm Cầu Vồng Hàn Quốc Hiện Đại',
                                    'Rèm Cuốn Văn Phòng & Cản Nhiệt',
                                    'Rèm Roman Xếp Lớp Tinh Tế',
                                    'Rèm Voan Thêu Nghệ Thuật',
                                    'Rèm Tự Động Điều Khiển Thông Minh',
                                ];
                            @endphp
                            @foreach($sampleCurtains as $item)
                                <div class="col-12 col-md-6">
                                    <div class="form-check p-2 border rounded-3 bg-light bg-opacity-50">
                                        <input class="form-check-input ms-1" type="checkbox" name="curtain_types[]" value="{{ $item }}" id="chk_{{ md5($item) }}">
                                        <label class="form-check-label small ms-2 fw-semibold text-dark cursor-pointer" for="chk_{{ md5($item) }}">
                                            {{ $item }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Số lượng ô cửa dự kiến làm rèm</label>
                            <select name="estimated_windows" class="form-select rounded-3">
                                <option value="1 - 2 ô cửa">1 - 2 ô cửa sổ / cửa chính</option>
                                <option value="3 - 5 ô cửa (Căn hộ 2-3 phòng ngủ)" selected>3 - 5 ô cửa (Căn hộ 2-3 phòng ngủ)</option>
                                <option value="Toàn bộ căn hộ / Nhà phố (> 5 cửa)">Toàn bộ căn hộ / Nhà phố (> 5 cửa)</option>
                                <option value="Biệt thự / Villa cao cấp">Biệt thự / Villa cao cấp</option>
                                <option value="Văn phòng / Tòa nhà doanh nghiệp">Văn phòng / Tòa nhà doanh nghiệp</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Ghi chú yêu cầu đặc biệt</label>
                            <input type="text" name="notes" class="form-control rounded-3" 
                                   placeholder="Ví dụ: Nhà hướng Tây cần loại cản sáng 100%, trần cao 3.2m...">
                        </div>
                    </div>

                    <div class="pt-3 border-top text-center">
                        <button type="submit" class="btn btn-dark btn-gold btn-lg px-5 py-3 rounded-pill shadow fw-bold w-100 w-md-auto">
                            <i class="bi bi-send-check-fill me-2"></i> Xác Nhận Đặt Lịch Hẹn Khảo Sát Tận Nhà
                        </button>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="bi bi-shield-check text-success me-1"></i> Cam kết 100% không mất phí, thợ mang mẫu vải tận tay, tư vấn tận tâm.
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quy Trình & Thông Tin Hỗ Trợ (Cột Phải) -->
        <div class="col-12 col-lg-4">
            <!-- Card Quy trình -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-diagram-3-fill text-warning me-2"></i>Quy Trình 4 Bước Tại Nhà
                </h5>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px;">
                            1
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Tiếp nhận & Xác nhận</h6>
                            <p class="small text-muted mb-0">Chuyên viên gọi điện xác nhận địa chỉ và thời gian hẹn thuận tiện nhất.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px;">
                            2
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Mang mẫu vải & Đo đạc</h6>
                            <p class="small text-muted mb-0">Thợ kỹ thuật đến tận nhà với catalogue vải mẫu và đo đạc ô cửa bằng thước laser.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px;">
                            3
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Báo giá theo $m^2$ trọn gói</h6>
                            <p class="small text-muted mb-0">Tính diện tích chuẩn xác, áp dụng voucher khuyến mãi và báo giá công khai không phát sinh.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px;">
                            4
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">May xưởng & Lắp đặt 48h</h6>
                            <p class="small text-muted mb-0">Hoàn thiện may đo tỉ mỉ và lắp đặt hoàn thiện tại nhà, bảo hành 3 năm.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Hotline -->
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white text-center" style="background: linear-gradient(135deg, #b8860b 0%, #8c6508 100%);">
                <i class="bi bi-headset display-4 mb-2"></i>
                <h5 class="fw-bold text-white mb-1">Cần Tư Vấn Gấp?</h5>
                <p class="small text-light text-opacity-90 mb-3">Gọi ngay tổng đài chăm sóc khách hàng trực 24/7 của chúng tôi</p>
                <a href="tel:0901234567" class="btn btn-dark btn-lg rounded-pill fw-bold text-white px-4 shadow-sm">
                    <i class="bi bi-telephone-fill me-2 text-warning"></i> 0901.234.567
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateTimeSlotStyles() {
        document.querySelectorAll('.time-slot-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.classList.add('border-warning', 'bg-warning', 'bg-opacity-10');
            } else {
                card.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateTimeSlotStyles();
    });
</script>
@endpush
@endsection
