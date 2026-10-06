@extends('layouts.client')

@section('title', 'Thanh Toán & Tính Phí Giao Hàng GHN — Rèm Online')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #leafletMapContainer {
        height: 380px;
        width: 100%;
        border-radius: 0.75rem;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 0.5rem;
        font-family: inherit;
        font-size: 0.85rem;
    }
    .gps-pulse-btn {
        animation: pulse-border 2s infinite;
    }
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>
@endpush

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">Giỏ hàng</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Thanh toán & Giao hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-warning me-2"></i>Thanh Toán & Đặt May Rèm</h3>
        <p class="text-muted small mb-0">Địa chỉ kho gửi: <strong>322/76 Ngõ 322 Mỹ Đình, P. Mỹ Đình 1, Q. Nam Từ Liêm, Hà Nội</strong></p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3 mb-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Vui lòng kiểm tra lại các thông tin:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <!-- Input ẩn lưu giá trị phục vụ tính toán -->
        <input type="hidden" id="total_price_input" value="{{ (int) $subtotal }}">
        <input type="hidden" id="estimated_shipping_fee" name="estimated_shipping_fee" value="0">
        
        <input type="hidden" id="province_name_input" name="province_name" value="{{ old('province_name') }}">
        <input type="hidden" id="district_name_input" name="district_name" value="{{ old('district_name') }}">
        <input type="hidden" id="ward_name_input" name="ward_name" value="{{ old('ward_name') }}">

        <div class="row g-4">
            <!-- ========================================== -->
            <!-- 1. CỘT TRÁI: THÔNG TIN GIAO HÀNG & ĐỊA CHỈ -->
            <!-- ========================================== -->
            <div class="col-12 col-lg-7">
                <!-- Thông tin người nhận -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark pb-3 border-bottom mb-3">
                        <i class="bi bi-person-lines-fill text-warning me-2"></i>1. Thông Tin Người Nhận Hàng
                    </h5>

                    <div class="mb-3">
                        <label for="recipient_name" class="form-label small fw-bold">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name" 
                               value="{{ old('recipient_name', $user?->name) }}" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="recipient_phone" class="form-label small fw-bold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('recipient_phone') is-invalid @enderror" id="recipient_phone" name="recipient_phone" 
                                   value="{{ old('recipient_phone', $user?->phone) }}" required placeholder="Ví dụ: 0912345678">
                        </div>
                        <div class="col-md-6">
                            <label for="recipient_email" class="form-label small fw-bold">Địa chỉ Email (Nhận thông báo)</label>
                            <input type="email" class="form-control" id="recipient_email" name="recipient_email" 
                                   value="{{ old('recipient_email', $user?->email) }}" placeholder="name@example.com">
                        </div>
                    </div>
                </div>

                <!-- Địa chỉ giao hàng GHN 3 Cấp -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>2. Địa Chỉ Nhận Hàng (Tích hợp GHN)
                        </h5>
                        <span class="badge bg-danger-subtle text-danger small font-monospace">GHN API & GPS</span>
                    </div>

                    <!-- Thanh tiện ích Định vị GPS & Bản đồ tương tác như Shopee -->
                    <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 mb-3">
                        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-danger small d-block">
                                    <i class="bi bi-crosshair me-1"></i> Định Vị Tự Động (Như Shopee):
                                </span>
                                <span class="text-muted" style="font-size: 0.75rem;">Tự động điền 3 cấp Tỉnh/Quận/Xã GHN & tính cước ship tức thì</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" id="btnGpsLocate" class="btn btn-sm btn-danger fw-bold shadow-sm gps-pulse-btn">
                                    <i class="bi bi-geo-alt-fill me-1"></i> 📍 Vị Trí Hiện Tại
                                </button>
                                <button type="button" id="btnOpenMapModal" class="btn btn-sm btn-outline-dark fw-bold bg-white shadow-sm" data-bs-toggle="modal" data-bs-target="#mapPickerModal">
                                    <i class="bi bi-map-fill text-primary me-1"></i> 🗺️ Chọn Bản Đồ
                                </button>
                            </div>
                        </div>

                        <!-- Trạng thái định vị -->
                        <div id="gpsStatusAlert" class="mt-2 small d-none alert alert-light border py-2 px-3 mb-0 rounded-2">
                            <div class="d-flex align-items-center gap-2">
                                <span id="gpsSpinner" class="spinner-border spinner-border-sm text-danger d-none"></span>
                                <span id="gpsStatusIcon"><i class="bi bi-geo-fill text-danger"></i></span>
                                <span id="gpsStatusText" class="fw-semibold text-dark">Đang xác định vị trí...</span>
                            </div>
                        </div>
                    </div>

                    <!-- 1. Tỉnh / Thành phố -->
                    <div class="mb-3">
                        <label for="province_select" class="form-label small fw-bold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_province_id') is-invalid @enderror" id="province_select" name="to_province_id" required>
                            <option value="">-- Đang tải danh sách Tỉnh/Thành từ GHN API... --</option>
                        </select>
                    </div>

                    <!-- 2. Quận / Huyện -->
                    <div class="mb-3">
                        <label for="district_select" class="form-label small fw-bold">Quận / Huyện <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_district_id') is-invalid @enderror" id="district_select" name="to_district_id" disabled required>
                            <option value="">-- Vui lòng chọn Tỉnh/Thành trước --</option>
                        </select>
                    </div>

                    <!-- 3. Phường / Xã -->
                    <div class="mb-3">
                        <label for="ward_select" class="form-label small fw-bold">Phường / Xã <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_ward_code') is-invalid @enderror" id="ward_select" name="to_ward_code" disabled required>
                            <option value="">-- Vui lòng chọn Quận/Huyện trước --</option>
                        </select>
                    </div>

                    <!-- 4. Địa chỉ cụ thể -->
                    <div class="mb-3">
                        <label for="specific_address" class="form-label small fw-bold">Số nhà, ngõ/ngách, tên đường chi tiết <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('specific_address') is-invalid @enderror" id="specific_address" name="specific_address" 
                               value="{{ old('specific_address') }}" required placeholder="Ví dụ: Số 18 Ngõ 45 Đường Cầu Giấy">
                    </div>

                    <div class="mb-0">
                        <label for="note" class="form-label small fw-bold">Ghi chú thêm cho thợ may & nhân viên giao hàng</label>
                        <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút, rèm may lọt lòng cửa sổ...">{{ old('note') }}</textarea>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark pb-3 border-bottom mb-3">
                        <i class="bi bi-wallet2 text-warning me-2"></i>3. Phương Thức Thanh Toán
                    </h5>

                    <!-- 1. Cổng MoMo (Mới tích hợp) -->
                    <div class="form-check p-3 border rounded-3 mb-2 bg-light border-danger-subtle">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payment_momo" value="momo">
                        <label class="form-check-label fw-semibold text-dark cursor-pointer d-flex justify-content-between align-items-center w-100" for="payment_momo">
                            <span class="d-flex align-items-center gap-2">
                                <span class="badge" style="background-color: #a50064; color: #ffffff; font-weight: 700; padding: 4px 8px;">MoMo</span>
                                <span>Thanh toán trực tuyến qua <strong>Ví MoMo (ATM / QR)</strong></span>
                            </span>
                            <span class="badge text-white" style="background-color: #a50064;">Khuyên dùng</span>
                        </label>
                        <small class="text-muted d-block ps-4 mt-1">Hỗ trợ quét mã MoMo QR hoặc thanh toán thẻ ATM nội địa qua cổng thanh toán MoMo Sandbox.</small>

                        <!-- Hướng dẫn tài khoản thẻ ATM Test MoMo cho Giảng viên / Người dùng -->
                        <div class="ms-4 mt-2 p-2 bg-white rounded-2 border border-secondary-subtle small">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark" style="font-size: 0.78rem;"><i class="bi bi-credit-card-2-front-fill text-danger me-1"></i>Thẻ ATM Test Sandbox MoMo:</strong>
                                <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">Hạn 12/30 • OTP bất kỳ</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                                    <tbody>
                                        <tr>
                                            <td class="py-0 text-success fw-semibold">• Thành công:</td>
                                            <td class="py-0 font-monospace"><strong>9704 0000 0000 0018</strong></td>
                                            <td class="py-0 text-muted">NGUYEN VAN A</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 text-warning fw-semibold">• Thẻ bị khóa:</td>
                                            <td class="py-0 font-monospace">9704 0000 0000 0026</td>
                                            <td class="py-0 text-muted">NGUYEN VAN A</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 text-danger fw-semibold">• Không đủ tiền:</td>
                                            <td class="py-0 font-monospace">9704 0000 0000 0034</td>
                                            <td class="py-0 text-muted">NGUYEN VAN A</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Thanh toán khi nhận hàng (COD) -->
                    <div class="form-check p-3 border rounded-3 mb-2 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                        <label class="form-check-label fw-semibold text-dark cursor-pointer d-flex justify-content-between align-items-center w-100" for="payment_cod">
                            <span><i class="bi bi-cash-stack text-success me-2"></i> Thanh toán khi nhận hàng (COD)</span>
                            <span class="badge bg-success-subtle text-success">Phổ biến</span>
                        </label>
                        <small class="text-muted d-block ps-4 mt-1">Kiểm tra vải rèm đúng kích thước và thanh toán tiền mặt cho shipper GHN.</small>
                    </div>

                    <!-- 3. Chuyển khoản VietQR -->
                    <div class="form-check p-3 border rounded-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                        <label class="form-check-label fw-semibold text-dark cursor-pointer d-flex justify-content-between align-items-center w-100" for="payment_bank">
                            <span><i class="bi bi-qr-code text-primary me-2"></i> Chuyển khoản ngân hàng (Quét mã VietQR)</span>
                            <span class="badge bg-primary-subtle text-primary">Tự động 24/7</span>
                        </label>
                        <small class="text-muted d-block ps-4 mt-1">Quét mã QR qua ứng dụng ngân hàng MBBank/Vietcombank nhanh chóng.</small>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. CỘT PHẢI: TỔNG KẾT ĐƠN HÀNG & PHÍ GHN   -->
            <!-- ========================================== -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px; z-index: 10;">
                    <h5 class="fw-bold text-dark pb-3 border-bottom mb-3">Đơn Hàng Của Bạn ({{ $cartItems->sum('quantity') }} bộ)</h5>

                    <!-- Danh sách rèm đặt -->
                    <div class="overflow-auto pe-1 mb-3" style="max-height: 260px;">
                        @foreach ($cartItems as $item)
                            @php
                                $effectivePrice = $item->sale_unit_price ?? $item->unit_price;
                                $itemTotal = $item->area * $effectivePrice * $item->quantity;
                            @endphp
                            <div class="d-flex gap-3 align-items-center py-2 border-bottom">
                                <img src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" 
                                     class="rounded-3 border object-fit-cover shadow-sm" style="width: 55px; height: 55px;">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0 small">{{ $item->product->name }}</h6>
                                    <small class="text-muted d-block" style="font-size: 0.78rem;">
                                        Màu: <strong>{{ $item->color?->name ?? 'Mặc định' }}</strong> • Kích thước: <strong>{{ number_format($item->width, 2) }}m × {{ number_format($item->height, 2) }}m</strong> ({{ number_format($item->area, 2) }}m²)
                                    </small>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">SL: <strong>{{ $item->quantity }}</strong></small>
                                        <strong class="text-danger small">{{ number_format($itemTotal, 0, ',', '.') }} đ</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- KHU VỰC ÁP DỤNG MÃ GIẢM GIÁ (VOUCHER) -->
                    <div class="card border border-warning bg-warning bg-opacity-10 rounded-3 p-3 mb-3">
                        <label for="voucher_input" class="form-label fw-bold small text-dark d-flex align-items-center justify-content-between mb-2">
                            <span><i class="bi bi-ticket-perforated-fill text-warning me-1"></i> Mã Giảm Giá / Voucher</span>
                            <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.7rem;">TIẾT KIỆM HƠN</span>
                        </label>
                        
                        <div class="input-group mb-2">
                            <input type="text" id="voucher_input" class="form-control text-uppercase font-monospace form-control-sm" 
                                   placeholder="Nhập mã (VD: REMMOI50K)" value="{{ $appliedVoucher?->code ?? '' }}" {{ $appliedVoucher ? 'readonly' : '' }}>
                            <button class="btn btn-dark btn-sm fw-bold px-3" type="button" id="applyVoucherBtn" style="{{ $appliedVoucher ? 'display:none;' : '' }}">
                                Áp Dụng
                            </button>
                            <button class="btn btn-outline-danger btn-sm" type="button" id="removeVoucherBtn" style="{{ $appliedVoucher ? '' : 'display:none;' }}" title="Gỡ mã này">
                                <i class="bi bi-x-lg"></i> Gỡ
                            </button>
                        </div>

                        <!-- Feedback Message -->
                        <div id="voucherMessage" class="small {{ $appliedVoucher ? 'text-success fw-bold' : 'd-none' }}">
                            @if($appliedVoucher)
                                <i class="bi bi-check-circle-fill me-1"></i> Đã áp dụng mã {{ $appliedVoucher->code }} ({{ $appliedVoucher->discount_label }})
                            @endif
                        </div>

                        <!-- Gợi ý các mã có sẵn -->
                        <div class="mt-2 pt-2 border-top border-warning border-opacity-25">
                            <span class="text-muted small d-block mb-1" style="font-size: 0.75rem;">Mã ưu đãi có thể dùng:</span>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 font-monospace text-dark" style="font-size: 0.72rem;" onclick="quickApplyVoucher('REMMOI50K')">
                                    REMMOI50K (-50k)
                                </button>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 font-monospace text-dark" style="font-size: 0.72rem;" onclick="quickApplyVoucher('VIPREM10')">
                                    VIPREM10 (-10%)
                                </button>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 font-monospace text-dark" style="font-size: 0.72rem;" onclick="quickApplyVoucher('GIAM100K')">
                                    GIAM100K (-100k)
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="voucher_code" id="voucher_code_input" value="{{ $appliedVoucher?->code ?? '' }}">

                    <!-- Tóm tắt chi phí -->
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Tiền hàng rèm tạm tính:</span>
                        <strong class="text-dark">{{ number_format($subtotal, 0, ',', '.') }} VNĐ</strong>
                    </div>

                    <!-- Dòng Giảm Giá Voucher -->
                    <div id="voucherDiscountRow" class="d-flex justify-content-between small text-danger mb-2 {{ $voucherDiscount > 0 ? '' : 'd-none' }}">
                        <span><i class="bi bi-tag-fill me-1"></i>Giảm giá Voucher:</span>
                        <strong id="voucher_discount_text">-{{ number_format($voucherDiscount, 0, ',', '.') }} VNĐ</strong>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-2 align-items-center">
                        <span>Cước vận chuyển (GHN):</span>
                        <strong class="text-danger fs-6" id="shipping_fee_text">0 VNĐ (Chọn địa chỉ)</strong>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-3">
                        <span>Khảo sát & phụ kiện thanh ray:</span>
                        <span class="text-success fw-bold">MIỄN PHÍ</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fw-bold text-dark fs-6">TỔNG CỘNG THANH TOÁN:</span>
                        <h3 class="fw-extrabold text-danger mb-0" id="final_total_text">
                            {{ number_format(max(0, $subtotal - $voucherDiscount), 0, ',', '.') }} VNĐ
                        </h3>
                    </div>

                    <!-- Nút Đặt Hàng -->
                    <div class="d-grid gap-2">
                        <button type="submit" id="submitOrderBtn" class="btn btn-gold py-3 fw-bold shadow">
                            <i class="bi bi-bag-check-fill me-2"></i> XÁC NHẬN ĐẶT HÀNG NGAY
                        </button>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 small text-secondary">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-shield-lock-fill text-success fs-6"></i>
                            <span>Bảo mật đơn hàng, kiểm tra rèm may chuẩn trước khi thanh toán.</span>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-box-seam-fill text-warning fs-6"></i>
                            <span>Vận đơn được kết nối trực tiếp với <strong>Giao Hàng Nhanh (GHN)</strong>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ========================================================== -->
<!-- MODAL CHỌN VỊ TRÍ TRÊN BẢN ĐỒ (LEAFLET / OPENSTREETMAP)    -->
<!-- ========================================================== -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-labelledby="mapPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold" id="mapPickerModalLabel">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>Chọn Vị Trí Giao Hàng Trên Bản Đồ (Như Shopee)
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex gap-2 mb-2">
                    <input type="text" id="mapSearchInput" class="form-control form-control-sm" placeholder="Tìm kiếm địa điểm (ví dụ: Cầu Giấy, Nam Từ Liêm, Quận 1...)">
                    <button type="button" id="btnMapSearch" class="btn btn-sm btn-dark px-3 fw-semibold">
                        <i class="bi bi-search me-1"></i> Tìm
                    </button>
                    <button type="button" id="btnMapCurrentGps" class="btn btn-sm btn-danger px-3 fw-semibold text-nowrap">
                        <i class="bi bi-crosshair me-1"></i> GPS
                    </button>
                </div>

                <!-- Leaflet Container -->
                <div id="leafletMapContainer" class="border shadow-sm mb-3"></div>

                <!-- Preview Địa Chỉ Chọn Được -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="small fw-bold text-muted mb-1">
                        <i class="bi bi-pin-map-fill text-danger me-1"></i> Vị trí bạn đã ghim:
                    </div>
                    <div id="mapSelectedAddressPreview" class="fw-bold text-dark small">
                        Nhấn vào bất kỳ điểm nào trên bản đồ hoặc kéo ghim để chọn vị trí giao hàng.
                    </div>
                    <div id="mapGeocodeStatus" class="small text-muted mt-1" style="font-size: 0.75rem;">
                        Tọa độ: <span id="mapCoordsText">21.0285, 105.8542</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="btnConfirmMapLocation" class="btn btn-gold btn-sm px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                    <i class="bi bi-check2-circle me-1"></i> Dùng Địa Chỉ Này
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- SCRIPT CHUẨN HÓA GHN & VOUCHERS & LEAFLET GPS              -->
<!-- ========================================================== -->
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let currentShippingFee = 0;
let currentVoucherDiscount = {{ (int) $voucherDiscount }};
const subtotal = parseInt(document.getElementById('total_price_input')?.value || 0) || 0;

function recalculateGrandTotal() {
    const shippingFeeText = document.getElementById('shipping_fee_text');
    const finalTotalText = document.getElementById('final_total_text');
    const estimatedShippingFeeInput = document.getElementById('estimated_shipping_fee');
    const voucherDiscountRow = document.getElementById('voucherDiscountRow');
    const voucherDiscountText = document.getElementById('voucher_discount_text');

    if (currentVoucherDiscount > 0) {
        voucherDiscountRow.classList.remove('d-none');
        voucherDiscountText.innerText = '-' + new Intl.NumberFormat('vi-VN').format(currentVoucherDiscount) + ' VNĐ';
    } else {
        voucherDiscountRow.classList.add('d-none');
    }

    if (estimatedShippingFeeInput) {
        estimatedShippingFeeInput.value = currentShippingFee;
    }

    const finalAmount = Math.max(0, subtotal - currentVoucherDiscount) + currentShippingFee;
    finalTotalText.innerText = new Intl.NumberFormat('vi-VN').format(finalAmount) + ' VNĐ';
}

function quickApplyVoucher(code) {
    const input = document.getElementById('voucher_input');
    if (input) {
        input.value = code;
        document.getElementById('applyVoucherBtn')?.click();
    }
}

// Chuẩn hóa chuỗi tiếng Việt để so khớp Fuzzy Match Tỉnh/Quận/Xã GHN
function normalizeVietnamese(str) {
    if (!str) return '';
    return str.toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        .replace(/^(tinh|thanh pho|tp\.|tp|quan|huyen|thi xa|tx\.|phuong|xa|thi tran|tt\.)\s+/g, '')
        .replace(/[\s\-_,.]+/g, ' ')
        .trim();
}

document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById('province_select');
    const districtSelect = document.getElementById('district_select');
    const wardSelect = document.getElementById('ward_select');
    const specificAddressInput = document.getElementById('specific_address');
    const shippingFeeText = document.getElementById('shipping_fee_text');
    const provinceNameInput = document.getElementById('province_name_input');
    const districtNameInput = document.getElementById('district_name_input');
    const wardNameInput = document.getElementById('ward_name_input');

    const voucherInput = document.getElementById('voucher_input');
    const applyVoucherBtn = document.getElementById('applyVoucherBtn');
    const removeVoucherBtn = document.getElementById('removeVoucherBtn');
    const voucherMessage = document.getElementById('voucherMessage');
    const voucherCodeInput = document.getElementById('voucher_code_input');

    const btnGpsLocate = document.getElementById('btnGpsLocate');
    const gpsStatusAlert = document.getElementById('gpsStatusAlert');
    const gpsSpinner = document.getElementById('gpsSpinner');
    const gpsStatusIcon = document.getElementById('gpsStatusIcon');
    const gpsStatusText = document.getElementById('gpsStatusText');

    const districtsUrl = "{{ route('locations.districts', ['provinceId' => '__PROVINCE__']) }}";
    const wardsUrl = "{{ route('locations.wards', ['districtId' => '__DISTRICT__']) }}";

    let ghnProvincesCache = [];

    // ==========================================
    // 1. XỬ LÝ ÁP DỤNG & GỠ VOUCHER
    // ==========================================
    if (applyVoucherBtn) {
        applyVoucherBtn.addEventListener('click', function () {
            const code = voucherInput.value.trim();
            if (!code) {
                voucherMessage.className = 'small text-danger';
                voucherMessage.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Vui lòng nhập mã giảm giá.';
                voucherMessage.classList.remove('d-none');
                return;
            }

            applyVoucherBtn.disabled = true;
            applyVoucherBtn.innerText = 'Đang kiểm tra...';

            fetch("{{ route('vouchers.apply') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                applyVoucherBtn.disabled = false;
                applyVoucherBtn.innerText = 'Áp Dụng';

                if (body.success) {
                    currentVoucherDiscount = parseInt(body.discount_amount) || 0;
                    voucherMessage.className = 'small text-success fw-bold';
                    voucherMessage.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${body.message}`;
                    voucherMessage.classList.remove('d-none');

                    voucherInput.readOnly = true;
                    applyVoucherBtn.style.display = 'none';
                    removeVoucherBtn.style.display = 'inline-block';
                    voucherCodeInput.value = body.voucher_code;

                    recalculateGrandTotal();
                } else {
                    voucherMessage.className = 'small text-danger';
                    voucherMessage.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${body.message}`;
                    voucherMessage.classList.remove('d-none');
                }
            })
            .catch(err => {
                applyVoucherBtn.disabled = false;
                applyVoucherBtn.innerText = 'Áp Dụng';
                console.error("Lỗi áp dụng voucher:", err);
            });
        });
    }

    if (removeVoucherBtn) {
        removeVoucherBtn.addEventListener('click', function () {
            fetch("{{ route('vouchers.remove') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(body => {
                currentVoucherDiscount = 0;
                voucherInput.value = '';
                voucherInput.readOnly = false;
                applyVoucherBtn.style.display = 'inline-block';
                removeVoucherBtn.style.display = 'none';
                voucherCodeInput.value = '';

                voucherMessage.className = 'small text-muted';
                voucherMessage.innerHTML = '<i class="bi bi-info-circle me-1"></i> Đã gỡ bỏ mã giảm giá.';
                recalculateGrandTotal();
            })
            .catch(err => console.error("Lỗi gỡ voucher:", err));
        });
    }

    // ==========================================
    // 2. TẢI DANH SÁCH 63 TỈNH/THÀNH TỪ GHN API
    // ==========================================
    function loadProvinces(callback) {
        provinceSelect.innerHTML = '<option value="">-- Đang tải danh sách Tỉnh/Thành từ GHN API... --</option>';
        fetch("{{ route('locations.provinces') }}")
            .then(res => res.json())
            .then(res => {
                if (res.data && Array.isArray(res.data) && res.data.length > 0) {
                    ghnProvincesCache = res.data;
                    let sortedProvinces = res.data.slice().sort((a, b) => (a.ProvinceName || '').localeCompare(b.ProvinceName || '', 'vi'));
                    let options = '<option value="">-- Chọn Tỉnh / Thành phố (GHN) --</option>';
                    sortedProvinces.forEach(p => {
                        options += `<option value="${p.ProvinceID}" data-name="${p.ProvinceName}">${p.ProvinceName}</option>`;
                    });
                    provinceSelect.innerHTML = options;
                    if (typeof callback === 'function') callback();
                } else {
                    provinceSelect.innerHTML = '<option value="">-- Lỗi tải danh sách Tỉnh/Thành từ GHN --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi tải Tỉnh/Thành GHN:", err);
                provinceSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN API --</option>';
            });
    }
    loadProvinces();

    // 3. Khi chọn Tỉnh/Thành -> Tải Quận/Huyện
    provinceSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        provinceNameInput.value = selectedOption.getAttribute('data-name') || '';

        districtSelect.innerHTML = '<option value="">-- Đang tải Quận/Huyện... --</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;
        currentShippingFee = 0;
        shippingFeeText.innerText = '0 VNĐ (Chọn địa chỉ)';
        recalculateGrandTotal();

        if (!this.value) return;

        fetch(districtsUrl.replace('__PROVINCE__', this.value))
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Quận / Huyện --</option>';
                    res.data.forEach(d => {
                        options += `<option value="${d.DistrictID}" data-name="${d.DistrictName}">${d.DistrictName}</option>`;
                    });
                    districtSelect.innerHTML = options;
                    districtSelect.disabled = false;
                } else {
                    districtSelect.innerHTML = '<option value="">-- Không tải được quận/huyện --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi load quận huyện:", err);
                districtSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN --</option>';
            });
    });

    // 4. Khi chọn Quận/Huyện -> Tải Phường/Xã
    districtSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        districtNameInput.value = selectedOption.getAttribute('data-name') || '';

        wardSelect.innerHTML = '<option value="">-- Đang tải Phường/Xã... --</option>';
        wardSelect.disabled = true;
        currentShippingFee = 0;
        shippingFeeText.innerText = '0 VNĐ (Chọn địa chỉ)';
        recalculateGrandTotal();

        if (!this.value) return;

        fetch(wardsUrl.replace('__DISTRICT__', this.value))
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Phường / Xã --</option>';
                    res.data.forEach(w => {
                        options += `<option value="${w.WardCode}" data-name="${w.WardName}">${w.WardName}</option>`;
                    });
                    wardSelect.innerHTML = options;
                    wardSelect.disabled = false;
                } else {
                    wardSelect.innerHTML = '<option value="">-- Không tải được phường/xã --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi load phường xã:", err);
                wardSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN --</option>';
            });
    });

    // 5. Khi chọn Phường/Xã -> Tính cước vận chuyển GHN tự động
    wardSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        wardNameInput.value = selectedOption.getAttribute('data-name') || '';

        if (!this.value || !districtSelect.value) return;

        shippingFeeText.innerText = 'Đang tính cước GHN...';

        fetch("{{ route('locations.fee') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                to_district_id: districtSelect.value,
                to_ward_code: this.value
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.code === 200 && res.data) {
                currentShippingFee = parseInt(res.data.total) || 0;
            } else {
                console.warn('GHN tính cước trả về:', res);
                currentShippingFee = 35000;
            }
            shippingFeeText.innerText = new Intl.NumberFormat('vi-VN').format(currentShippingFee) + ' VNĐ';
            recalculateGrandTotal();
        })
        .catch(err => {
            console.error("Lỗi tính phí GHN:", err);
            currentShippingFee = 35000;
            shippingFeeText.innerText = new Intl.NumberFormat('vi-VN').format(currentShippingFee) + ' VNĐ';
            recalculateGrandTotal();
        });
    });

    // ==========================================
    // 6. THUẬT TOÁN ĐỊNH VỊ GPS & ĐỒNG BỘ GHN (SHOPEE STYLE)
    // ==========================================
    async function reverseGeocodeAndSyncGHN(lat, lng) {
        gpsStatusAlert.classList.remove('d-none');
        gpsSpinner.classList.remove('d-none');
        gpsStatusIcon.classList.add('d-none');
        gpsStatusText.innerText = `Đang định vị tọa độ (${lat.toFixed(4)}, ${lng.toFixed(4)})...`;

        try {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
            const response = await fetch(url, {
                headers: { 'Accept-Language': 'vi' }
            });
            const geoData = await response.json();

            if (!geoData || !geoData.address) {
                throw new Error("Không tìm thấy thông tin địa chỉ từ tọa độ này.");
            }

            const addr = geoData.address;
            const provName = addr.city || addr.state || addr.province || '';
            const distName = addr.city_district || addr.district || addr.county || addr.suburb || '';
            const wardName = addr.quarter || addr.suburb || addr.ward || addr.village || addr.neighbourhood || '';
            const roadName = [addr.house_number, addr.road].filter(Boolean).join(' ') || geoData.display_name.split(',')[0];

            gpsStatusText.innerText = `Đã tìm thấy: ${roadName}, ${wardName}, ${distName}, ${provName}. Đang đồng bộ GHN...`;

            // Tự động điền số nhà, đường
            if (roadName && specificAddressInput) {
                specificAddressInput.value = roadName;
            }

            // Đảm bảo cache tỉnh thành đã có
            if (ghnProvincesCache.length === 0) {
                await new Promise(resolve => loadProvinces(resolve));
            }

            // 1. Khớp Tỉnh/Thành phố
            const normProv = normalizeVietnamese(provName);
            let matchedProv = ghnProvincesCache.find(p => {
                const normP = normalizeVietnamese(p.ProvinceName);
                return normProv.includes(normP) || normP.includes(normProv);
            });

            // Nếu không tìm thấy, thử fallback Hà Nội / TP.HCM
            if (!matchedProv && ghnProvincesCache.length > 0) {
                matchedProv = ghnProvincesCache.find(p => normalizeVietnamese(p.ProvinceName).includes('ha noi')) || ghnProvincesCache[0];
            }

            if (!matchedProv) throw new Error("Không tìm thấy Tỉnh/Thành GHN phù hợp.");

            provinceSelect.value = matchedProv.ProvinceID;
            provinceNameInput.value = matchedProv.ProvinceName;

            // 2. Tải danh sách Quận/Huyện của Tỉnh này
            districtSelect.innerHTML = '<option value="">-- Đang đồng bộ Quận/Huyện GHN... --</option>';
            districtSelect.disabled = true;

            const distRes = await fetch(districtsUrl.replace('__PROVINCE__', matchedProv.ProvinceID)).then(r => r.json());
            if (!distRes.data || distRes.data.length === 0) throw new Error("Lỗi tải quận huyện GHN.");

            let distOptions = '<option value="">-- Chọn Quận / Huyện --</option>';
            distRes.data.forEach(d => {
                distOptions += `<option value="${d.DistrictID}" data-name="${d.DistrictName}">${d.DistrictName}</option>`;
            });
            districtSelect.innerHTML = distOptions;
            districtSelect.disabled = false;

            // Khớp Quận/Huyện
            const normDist = normalizeVietnamese(distName);
            let matchedDist = distRes.data.find(d => {
                const normD = normalizeVietnamese(d.DistrictName);
                return normDist.includes(normD) || normD.includes(normDist);
            });

            if (!matchedDist && distRes.data.length > 0) {
                matchedDist = distRes.data[0];
            }

            districtSelect.value = matchedDist.DistrictID;
            districtNameInput.value = matchedDist.DistrictName;

            // 3. Tải danh sách Phường/Xã của Quận này
            wardSelect.innerHTML = '<option value="">-- Đang đồng bộ Phường/Xã GHN... --</option>';
            wardSelect.disabled = true;

            const wardRes = await fetch(wardsUrl.replace('__DISTRICT__', matchedDist.DistrictID)).then(r => r.json());
            if (!wardRes.data || wardRes.data.length === 0) throw new Error("Lỗi tải phường xã GHN.");

            let wardOptions = '<option value="">-- Chọn Phường / Xã --</option>';
            wardRes.data.forEach(w => {
                wardOptions += `<option value="${w.WardCode}" data-name="${w.WardName}">${w.WardName}</option>`;
            });
            wardSelect.innerHTML = wardOptions;
            wardSelect.disabled = false;

            // Khớp Phường/Xã
            const normWard = normalizeVietnamese(wardName);
            let matchedWard = wardRes.data.find(w => {
                const normW = normalizeVietnamese(w.WardName);
                return normWard.includes(normW) || normW.includes(normWard);
            });

            if (!matchedWard && wardRes.data.length > 0) {
                matchedWard = wardRes.data[0];
            }

            wardSelect.value = matchedWard.WardCode;
            wardNameInput.value = matchedWard.WardName;

            // 4. Kích hoạt tính cước GHN tự động
            wardSelect.dispatchEvent(new Event('change'));

            gpsSpinner.classList.add('d-none');
            gpsStatusIcon.classList.remove('d-none');
            gpsStatusIcon.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-5"></i>';
            gpsStatusText.innerHTML = `<span class="text-success fw-bold">Đã định vị & khớp GHN thành công:</span> ${matchedWard.WardName}, ${matchedDist.DistrictName}, ${matchedProv.ProvinceName}`;

        } catch (err) {
            console.error("Lỗi đồng bộ GPS GHN:", err);
            gpsSpinner.classList.add('d-none');
            gpsStatusIcon.classList.remove('d-none');
            gpsStatusIcon.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>';
            gpsStatusText.innerHTML = `<span class="text-danger">Không thể tự động khớp đầy đủ:</span> ${err.message}. Vui lòng chọn tay dropdown bên dưới.`;
        }
    }

    // Sự kiện nút GPS Lấy vị trí hiện tại
    if (btnGpsLocate) {
        btnGpsLocate.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert("Trình duyệt của bạn không hỗ trợ định vị Geolocation.");
                return;
            }

            gpsStatusAlert.classList.remove('d-none');
            gpsSpinner.classList.remove('d-none');
            gpsStatusIcon.classList.add('d-none');
            gpsStatusText.innerText = "Đang xin quyền truy cập vị trí GPS từ trình duyệt...";

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    reverseGeocodeAndSyncGHN(lat, lng);
                },
                (error) => {
                    console.warn("Lỗi GPS:", error);
                    // Fallback mặc định Hà Nội khi bị chặn quyền GPS (Demo / localhost)
                    gpsStatusText.innerText = "Không lấy được GPS trực tiếp (do chặn quyền), sử dụng vị trí Demo tại Hà Nội...";
                    reverseGeocodeAndSyncGHN(21.0185, 105.7765);
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        });
    }

    // ==========================================
    // 7. KHỞI TẠO LEAFLET MAP MODAL (OPENSTREETMAP)
    // ==========================================
    let leafletMap = null;
    let mapMarker = null;
    let currentMapLat = 21.0285;
    let currentMapLng = 105.8542;
    const mapPickerModalEl = document.getElementById('mapPickerModal');
    const mapCoordsText = document.getElementById('mapCoordsText');
    const mapSelectedAddressPreview = document.getElementById('mapSelectedAddressPreview');
    const btnConfirmMapLocation = document.getElementById('btnConfirmMapLocation');
    const btnMapCurrentGps = document.getElementById('btnMapCurrentGps');
    const btnMapSearch = document.getElementById('btnMapSearch');
    const mapSearchInput = document.getElementById('mapSearchInput');

    function updateMapAddressPreview(lat, lng) {
        currentMapLat = lat;
        currentMapLng = lng;
        if (mapCoordsText) mapCoordsText.innerText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        if (mapSelectedAddressPreview) mapSelectedAddressPreview.innerText = "Đang tra cứu địa chỉ từ bản đồ...";

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept-Language': 'vi' }
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.display_name) {
                mapSelectedAddressPreview.innerHTML = `<strong>${data.display_name}</strong>`;
            } else {
                mapSelectedAddressPreview.innerText = `Tọa độ: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            }
        })
        .catch(() => {
            mapSelectedAddressPreview.innerText = `Tọa độ: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        });
    }

    if (mapPickerModalEl) {
        mapPickerModalEl.addEventListener('shown.bs.modal', function () {
            if (!leafletMap) {
                leafletMap = L.map('leafletMapContainer').setView([currentMapLat, currentMapLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(leafletMap);

                mapMarker = L.marker([currentMapLat, currentMapLng], { draggable: true }).addTo(leafletMap);

                mapMarker.on('dragend', function (e) {
                    const pos = e.target.getLatLng();
                    updateMapAddressPreview(pos.lat, pos.lng);
                });

                leafletMap.on('click', function (e) {
                    mapMarker.setLatLng(e.latlng);
                    updateMapAddressPreview(e.latlng.lat, e.latlng.lng);
                });
            } else {
                leafletMap.invalidateSize();
            }

            updateMapAddressPreview(currentMapLat, currentMapLng);
        });
    }

    // Nút GPS trong modal bản đồ
    if (btnMapCurrentGps) {
        btnMapCurrentGps.addEventListener('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    if (leafletMap && mapMarker) {
                        leafletMap.setView([lat, lng], 16);
                        mapMarker.setLatLng([lat, lng]);
                        updateMapAddressPreview(lat, lng);
                    }
                }, () => {
                    alert("Không thể lấy GPS. Bạn có thể bấm trực tiếp vào bản đồ để chọn.");
                });
            }
        });
    }

    // Nút tìm kiếm địa điểm trên bản đồ
    if (btnMapSearch && mapSearchInput) {
        const doMapSearch = () => {
            const query = mapSearchInput.value.trim();
            if (!query) return;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Vietnam')}&limit=1`, {
                headers: { 'Accept-Language': 'vi' }
            })
            .then(r => r.json())
            .then(res => {
                if (res && res.length > 0) {
                    const lat = parseFloat(res[0].lat);
                    const lng = parseFloat(res[0].lon);
                    if (leafletMap && mapMarker) {
                        leafletMap.setView([lat, lng], 15);
                        mapMarker.setLatLng([lat, lng]);
                        updateMapAddressPreview(lat, lng);
                    }
                } else {
                    alert("Không tìm thấy địa điểm này. Vui lòng thử từ khóa khác.");
                }
            });
        };
        btnMapSearch.addEventListener('click', doMapSearch);
        mapSearchInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); doMapSearch(); } });
    }

    // Nút Xác nhận vị trí từ bản đồ -> Đồng bộ sang đơn hàng
    if (btnConfirmMapLocation) {
        btnConfirmMapLocation.addEventListener('click', function () {
            reverseGeocodeAndSyncGHN(currentMapLat, currentMapLng);
        });
    }
});
</script>
@endpush
@endsection
