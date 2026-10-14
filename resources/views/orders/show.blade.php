@extends('layouts.client')

@section('title', 'Chi Tiết Đơn Hàng ' . $order->order_code . ' — Rèm Online')

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Lịch sử đơn hàng</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $order->order_code }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-seam text-warning me-2"></i>Đơn Hàng: <span class="font-monospace">{{ $order->order_code }}</span>
            </h3>
            <span class="text-muted small">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>

        <div class="d-flex gap-2 align-items-center">
            {!! $order->status_badge !!}
            {!! $order->shipping_status_badge !!}
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
            <strong>{{ session('info') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- ========================================== -->
        <!-- 1. CỘT TRÁI: CHI TIẾT CÁC BỘ RÈM MAY ĐO    -->
        <!-- ========================================== -->
        <div class="col-12 col-lg-8">
            <!-- Vận đơn GHN Banner -->
            @if ($order->ghn_order_code)
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white mb-4" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge bg-white text-dark small fw-bold mb-2">ĐÃ TẠO VẬN ĐƠN GIAO HÀNG NHANH (GHN)</span>
                            <h4 class="fw-bold mb-1 font-monospace">{{ $order->ghn_order_code }}</h4>
                            <p class="small text-white text-opacity-75 mb-0">
                                Đơn vị giao nhận: <strong>Giao Hàng Nhanh Express</strong> • Phí vận chuyển: <strong>{{ number_format($order->ghn_total_fee, 0, ',', '.') }} đ</strong>
                            </p>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-truck me-1"></i> {{ $order->shipping_status === 'ready_to_pick' ? 'Chờ GHN nhận hàng' : 'Đang vận chuyển' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ========================================== -->
            <!-- KHỐI THANH TOÁN VIETQR (KHI CHỌN BANK_TRANSFER) -->
            <!-- ========================================== -->
            @if ($order->payment_method === 'bank_transfer')
                @if ($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border border-primary border-opacity-25" style="border: 2px solid #2563eb !important;">
                        <div class="p-3 text-white d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                            <div>
                                <span class="badge bg-white text-primary fw-bold small mb-1">
                                    <i class="bi bi-qr-code-scan me-1"></i> VIETQR • NAPAS 24/7
                                </span>
                                <h5 class="fw-bold mb-0">Quét Mã VietQR Để Thanh Toán</h5>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                                    <i class="bi bi-clock-history me-1"></i> Chờ Quét Mã Chuyển Khoản
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 bg-white">
                            <div class="row g-4 align-items-center">
                                <!-- Cột 1: Mã QR Động -->
                                <div class="col-12 col-md-5 text-center border-end">
                                    @php
                                        $bankId = config('services.vietqr.bank_id', 'MB');
                                        $accountNo = config('services.vietqr.account_no', '0988889999');
                                        $accountName = config('services.vietqr.account_name', 'DONG QUANG MINH');
                                        $template = config('services.vietqr.template', 'compact2');
                                        $qrAmount = (int) $order->total;
                                        $qrInfo = urlencode($order->order_code);
                                        $qrName = urlencode($accountName);
                                        $vietQrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$qrAmount}&addInfo={$qrInfo}&accountName={$qrName}";
                                    @endphp

                                    <div class="d-inline-block p-2 bg-light rounded-3 border shadow-sm mb-2">
                                        <img src="{{ $vietQrUrl }}" 
                                             alt="Mã QR Chuyển Khoản VietQR" 
                                             class="img-fluid rounded-2" 
                                             style="max-width: 230px; width: 100%; display: block;"
                                             loading="lazy">
                                    </div>
                                    <p class="small text-muted mb-0" style="font-size: 0.78rem;">
                                        <i class="bi bi-phone me-1"></i> Mở App ngân hàng bất kỳ để quét mã
                                    </p>
                                </div>

                                <!-- Cột 2: Thông tin chi tiết & Sao chép -->
                                <div class="col-12 col-md-7">
                                    <p class="small text-muted mb-3">
                                        Quý khách có thể quét mã QR bên cạnh hoặc chuyển khoản thủ công theo thông tin chính xác dưới đây:
                                    </p>

                                    <div class="bg-light p-3 rounded-3 border mb-3">
                                        <!-- Ngân hàng -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span class="text-muted small">Ngân hàng:</span>
                                            <strong class="text-dark small">{{ config('services.vietqr.bank_name', 'MBBank (Ngân Hàng Quân Đội)') }}</strong>
                                        </div>

                                        <!-- Số tài khoản -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span class="text-muted small">Số tài khoản:</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <strong class="font-monospace text-primary fs-6" id="qr-acc-no">{{ $accountNo }}</strong>
                                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.72rem;" onclick="copyText('qr-acc-no', 'Đã chép số tài khoản!')" title="Sao chép số tài khoản">
                                                    <i class="bi bi-copy"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Chủ tài khoản -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span class="text-muted small">Chủ tài khoản:</span>
                                            <strong class="text-dark small text-uppercase">{{ $accountName }}</strong>
                                        </div>

                                        <!-- Số tiền -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span class="text-muted small">Số tiền:</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <strong class="text-danger fw-extrabold fs-6" id="qr-amount">{{ number_format($order->total, 0, ',', '.') }} đ</strong>
                                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.72rem;" onclick="copyRawText('{{ (int)$order->total }}', 'Đã chép số tiền!')" title="Sao chép số tiền">
                                                    <i class="bi bi-copy"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Nội dung chuyển khoản -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Nội dung CK:</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="badge bg-primary text-white font-monospace fs-6 px-2 py-1" id="qr-memo">{{ $order->order_code }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.72rem;" onclick="copyRawText('{{ $order->order_code }}', 'Đã chép nội dung chuyển khoản!')" title="Sao chép nội dung">
                                                    <i class="bi bi-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning py-2 px-3 small border-0 mb-3" style="font-size: 0.78rem;">
                                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                                        Vui lòng giữ nguyên <strong>Nội dung chuyển khoản là {{ $order->order_code }}</strong> để đơn hàng được duyệt tự động.
                                    </div>

                                    <!-- Nút bấm xác nhận đã chuyển khoản -->
                                    <form action="{{ route('orders.bank_transfer.confirm', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-check-circle-fill me-1"></i> Tôi Đã Chuyển Khoản Xong
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($order->payment_status === 'paid')
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-white mb-4" style="background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-2">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">ĐÃ THANH TOÁN THÀNH CÔNG QUA VIETQR</h6>
                                <p class="small text-white text-opacity-80 mb-0">Hệ thống đã xác nhận khoản thanh toán chuyển khoản của đơn hàng này. Xưởng may đang tiến hành đo cắt vải.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Danh sách sản phẩm rèm trong đơn -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Danh Sách Rèm May Đo Đã Đặt ({{ $order->items->sum('quantity') }} bộ)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light border-bottom text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="min-width: 250px;">Sản Phẩm Rèm</th>
                                <th class="py-3 text-center">Đơn Giá / m²</th>
                                <th class="py-3 text-center">Số Lượng</th>
                                <th class="py-3 text-end pe-4">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($order->items as $item)
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark mb-1">{{ $item->product_name }}</div>
                                        <div class="small text-muted mb-1">
                                            Màu sắc: <strong>{{ $item->color_name ?? 'Mặc định' }}</strong> • SKU: <code>{{ $item->product_sku }}</code>
                                        </div>
                                        <div class="small bg-light px-2 py-1 rounded d-inline-block border">
                                            <i class="bi bi-rulers text-warning me-1"></i>
                                            Kích thước: <strong>{{ number_format($item->width, 2) }}m</strong> (R) × <strong>{{ number_format($item->height, 2) }}m</strong> (C) = <strong class="text-danger">{{ number_format($item->area, 2) }} m²</strong>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="fw-bold text-dark">{{ number_format($item->sale_unit_price ?? $item->unit_price, 0, ',', '.') }} đ</span>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border fw-bold px-3 py-2">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <span class="fw-extrabold text-danger fs-6">
                                            {{ number_format($item->item_total, 0, ',', '.') }} đ
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Trạng thái Hủy Đơn Hàng hoặc Nút Hủy Đơn -->
            @if ($order->status === 'cancelled')
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-danger bg-opacity-10 border-danger border-opacity-25">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-danger fs-3">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Đơn hàng này đã được hủy thành công</h6>
                            <p class="small text-muted mb-0">Mã vận đơn GHN tương ứng (nếu có) đã được tự động hủy trên hệ thống Giao Hàng Nhanh.</p>
                        </div>
                    </div>
                </div>
            @elseif (in_array($order->status, ['pending', 'confirmed']) && in_array($order->shipping_status, ['not_shipped', 'ready_to_pick', 'pending']))
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                    <div>
                        <span class="small fw-bold text-dark d-block">Bạn muốn hủy đơn hoặc đổi kích thước?</span>
                        <small class="text-muted">Bạn có thể tự hủy đơn khi xưởng chưa tiến hành cắt may vải rèm.</small>
                    </div>
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng rèm này? Mã vận đơn GHN cũng sẽ được hủy.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Hủy Đơn Hàng Này
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- 2. CỘT PHẢI: THÔNG TIN GIAO NHẬN & CHI PHÍ -->
        <!-- ========================================== -->
        <div class="col-12 col-lg-4">
            <!-- Thông tin người nhận -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h6 class="fw-bold text-dark pb-2 border-bottom mb-3">
                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> Địa Chỉ Nhận Hàng
                </h6>
                <div class="mb-2">
                    <strong class="text-dark">{{ $order->recipient_name }}</strong>
                </div>
                <div class="small text-muted mb-2">
                    <i class="bi bi-telephone me-1"></i> {{ $order->recipient_phone }}
                </div>
                <div class="small text-secondary mb-3">
                    <i class="bi bi-house-door me-1"></i> {{ $order->shipping_address }}
                </div>
                @if ($order->note)
                    <div class="p-2 bg-light rounded small text-muted border">
                        <strong>Ghi chú:</strong> {{ $order->note }}
                    </div>
                @endif
            </div>

            <!-- Tóm tắt thanh toán & Trạng thái thanh toán -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h6 class="fw-bold text-dark pb-2 border-bottom mb-3">
                    <i class="bi bi-receipt text-warning me-1"></i> Chi Tiết Thanh Toán
                </h6>

                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Tiền hàng rèm:</span>
                    <strong class="text-dark">{{ number_format($order->subtotal, 0, ',', '.') }} VNĐ</strong>
                </div>

                @if ($order->voucher_discount > 0)
                    <div class="d-flex justify-content-between small text-danger mb-2">
                        <span><i class="bi bi-tag-fill me-1"></i>Giảm giá voucher:</span>
                        <strong>-{{ number_format($order->voucher_discount, 0, ',', '.') }} VNĐ</strong>
                    </div>
                @endif

                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Cước vận chuyển (GHN):</span>
                    <strong class="text-dark">{{ number_format($order->shipping_fee, 0, ',', '.') }} VNĐ</strong>
                </div>

                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Phương thức:</span>
                    <span class="badge bg-light text-dark border">{{ $order->payment_method_name }}</span>
                </div>

                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span>Trạng thái thanh toán:</span>
                    <div>{!! $order->payment_status_badge !!}</div>
                </div>

                <!-- Lịch sử giao dịch tài chính (Payment Transactions) -->
                @if ($order->paymentTransactions && $order->paymentTransactions->isNotEmpty())
                    <div class="p-2 bg-light rounded-3 mb-3 border small">
                        <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.78rem;">
                            <i class="bi bi-clock-history me-1"></i>Nhật ký giao dịch cổng thanh toán:
                        </span>
                        @foreach ($order->paymentTransactions as $txn)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary-subtle" style="font-size: 0.73rem;">
                                <div>
                                    <span class="badge {{ $txn->gateway === 'momo' ? 'text-white' : 'bg-secondary' }}" style="{{ $txn->gateway === 'momo' ? 'background-color: #a50064;' : '' }}">
                                        {{ strtoupper($txn->gateway) }}
                                    </span>
                                    <span class="text-muted ms-1">{{ $txn->created_at->format('H:i d/m') }}</span>
                                </div>
                                <div>
                                    @if ($txn->status === 'paid' || $txn->status === 'completed')
                                        <span class="text-success fw-bold">Thành công</span>
                                    @elseif ($txn->status === 'failed')
                                        <span class="text-danger fw-semibold">Thất bại</span>
                                    @else
                                        <span class="text-warning fw-semibold">Đang chờ</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-baseline mb-3">
                    <span class="fw-bold text-dark">TỔNG THANH TOÁN:</span>
                    <h4 class="fw-extrabold text-danger mb-0">{{ number_format($order->total, 0, ',', '.') }} VNĐ</h4>
                </div>

                <!-- Nút Thanh Toán Lại MoMo nếu đơn chưa thanh toán hoặc thất bại -->
                @if ($order->payment_method === 'momo' && $order->payment_status !== 'paid' && $order->status !== 'cancelled')
                    <div class="d-grid gap-2 mb-2">
                        <a href="{{ route('orders.momo.pay', $order->id) }}" class="btn py-2 fw-bold text-white shadow-sm" style="background-color: #a50064; border-color: #a50064;">
                            <i class="bi bi-wallet2 me-1"></i> Thanh Toán Lại Qua Ví MoMo
                        </a>
                    </div>
                @endif
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-gold py-2">
                    <i class="bi bi-shop me-1"></i> Tiếp Tục Mua Sắm Rèm
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(elementId, message) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const text = el.innerText.trim();
    copyRawText(text, message);
}

function copyRawText(text, message) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showToast(message || 'Đã sao chép vào bộ nhớ tạm!');
        }).catch(function() {
            fallbackCopy(text, message);
        });
    } else {
        fallbackCopy(text, message);
    }
}

function fallbackCopy(text, message) {
    const tempInput = document.createElement("input");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    showToast(message || 'Đã sao chép vào bộ nhớ tạm!');
}

function showToast(msg) {
    alert(msg);
}
</script>
@endsection
