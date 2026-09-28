@extends('layouts.admin')

@section('title', 'Thống Kê Tài Chính & Doanh Thu')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Quản trị</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Thống kê tài chính</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-graph-up-arrow text-warning me-2"></i>Thống Kê Tài Chính & Dòng Tiền
            </h4>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Đã có lỗi xảy ra:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 border-bottom pb-2 gap-2">
        <li class="nav-item">
            <a class="nav-link active fw-semibold shadow-sm" href="{{ route('admin.finance.index') }}">
                <i class="bi bi-pie-chart-fill me-1"></i> Thống kê chỉ số
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary fw-semibold bg-white border" href="{{ route('admin.finance.transactions') }}">
                <i class="bi bi-receipt-cutoff me-1"></i> Giao dịch thanh toán
            </a>
        </li>
    </ul>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.finance.index') }}" method="GET">
                <div class="row g-3">
                    <!-- Search query -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Mã đơn, Tên, SĐT..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Từ ngày</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To -->
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Đến ngày</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <!-- Min Amount -->
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Số tiền từ (đ)</label>
                        <input type="number" name="min_amount" class="form-control" placeholder="0" min="0" value="{{ request('min_amount') }}">
                    </div>

                    <!-- Max Amount -->
                    <div class="col-6 col-md-3 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Số tiền đến (đ)</label>
                        <input type="number" name="max_amount" class="form-control" placeholder="Tối đa" min="0" value="{{ request('max_amount') }}">
                    </div>

                    <!-- Gateway / Payment Method -->
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Phương thức thanh toán</label>
                        <select name="gateway" class="form-select">
                            <option value="">Tất cả phương thức</option>
                            @foreach ($methods as $k => $v)
                                <option value="{{ $k }}" {{ request('gateway') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            @foreach ($statuses as $k => $v)
                                <option value="{{ $k }}" {{ request('payment_status') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-12 col-md-4 col-lg-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4 shadow-sm flex-grow-1 flex-md-grow-0">
                            <i class="bi bi-funnel-fill me-1"></i> Áp dụng bộ lọc
                        </button>
                        <a href="{{ route('admin.finance.index') }}" class="btn btn-light border px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Info Notice -->
    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border mb-4">
        <div class="small text-muted">
            <i class="bi bi-info-circle-fill text-primary me-1"></i>
            Có <strong class="text-dark">{{ number_format($summary->order_count ?? 0) }}</strong> đơn phù hợp. 
            Số tiền bao gồm phí vận chuyển, thống kê theo ngày tạo đơn trên toàn bộ kết quả lọc.
        </div>
        <div>
            <span class="badge bg-dark text-warning px-3 py-2 fs-6 fw-bold">
                Tổng: {{ number_format($summary->total_amount ?? 0, 0, ',', '.') }} đ
            </span>
        </div>
    </div>

    <!-- 8 Metric Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- 1. Tổng giá trị đơn hàng -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Tổng giá trị đơn hàng</span>
                        <h4 class="fw-bold text-primary mb-1 mt-2">
                            {{ number_format($summary->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-box-seam me-1"></i>{{ number_format($summary->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Chờ thanh toán (pending) -->
        @php
            $pendingStat = $statusTotals['pending'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Chờ thanh toán</span>
                        <h4 class="fw-bold text-warning mb-1 mt-2">
                            {{ number_format($pendingStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-hourglass-split me-1"></i>{{ number_format($pendingStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Đang chờ MoMo (initiated) -->
        @php
            $initStat = $statusTotals['initiated'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Đang chờ MoMo</span>
                        <h4 class="fw-bold text-info mb-1 mt-2">
                            {{ number_format($initStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-qr-code me-1"></i>{{ number_format($initStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-phone-vibrate fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Đã thanh toán (paid) -->
        @php
            $paidStat = $statusTotals['paid'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Đã thanh toán</span>
                        <h4 class="fw-bold text-success mb-1 mt-2">
                            {{ number_format($paidStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-check2-circle me-1"></i>{{ number_format($paidStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-check-all fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Thanh toán thất bại (failed) -->
        @php
            $failedStat = $statusTotals['failed'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Thanh toán thất bại</span>
                        <h4 class="fw-bold text-danger mb-1 mt-2">
                            {{ number_format($failedStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-x-circle me-1"></i>{{ number_format($failedStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                        <i class="bi bi-exclamation-octagon fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Đã hủy (cancelled) -->
        @php
            $cancelStat = $statusTotals['cancelled'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-secondary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Đã hủy</span>
                        <h4 class="fw-bold text-secondary mb-1 mt-2">
                            {{ number_format($cancelStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-slash-circle me-1"></i>{{ number_format($cancelStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle">
                        <i class="bi bi-ban fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Chờ hoàn tiền (refund_pending) -->
        @php
            $refPendStat = $statusTotals['refund_pending'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Chờ hoàn tiền</span>
                        <h4 class="fw-bold text-warning mb-1 mt-2">
                            {{ number_format($refPendStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-arrow-return-left me-1"></i>{{ number_format($refPendStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-arrow-left-right fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Đã hoàn tiền (refunded) -->
        @php
            $refundedStat = $statusTotals['refunded'] ?? null;
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-dark">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold">Đã hoàn tiền</span>
                        <h4 class="fw-bold text-dark mb-1 mt-2">
                            {{ number_format($refundedStat->total_amount ?? 0, 0, ',', '.') }} đ
                        </h4>
                        <div class="small text-muted">
                            <i class="bi bi-shield-check me-1"></i>{{ number_format($refundedStat->order_count ?? 0) }} đơn hàng
                        </div>
                    </div>
                    <div class="bg-dark bg-opacity-10 text-dark p-3 rounded-circle">
                        <i class="bi bi-arrow-counterclockwise fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table: Thống Kê Theo Phương Thức -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-wallet2 text-warning me-2"></i>Thống Kê Theo Phương Thức Thanh Toán
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Phương thức</th>
                            <th class="text-center py-3">Số đơn</th>
                            <th class="text-end py-3">Tổng giá trị</th>
                            <th class="text-end pe-4 py-3">Đã thanh toán (Thực thu)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (['cod' => 'COD (Thanh toán khi nhận hàng)', 'momo' => 'Ví MoMo', 'bank_transfer' => 'Chuyển khoản VietQR', 'unknown' => 'Chưa xác định'] as $mKey => $mLabel)
                            @php
                                $mStat = $methodTotals[$mKey] ?? null;
                            @endphp
                            <tr>
                                <td class="ps-4 py-3 fw-semibold">
                                    @if ($mKey === 'cod')
                                        <span class="badge bg-secondary text-white px-2 py-1 me-2"><i class="bi bi-truck me-1"></i>COD</span>
                                    @elseif ($mKey === 'momo')
                                        <span class="badge bg-danger text-white px-2 py-1 me-2"><i class="bi bi-wallet-fill me-1"></i>MoMo</span>
                                    @elseif ($mKey === 'bank_transfer')
                                        <span class="badge bg-primary text-white px-2 py-1 me-2"><i class="bi bi-qr-code-scan me-1"></i>VietQR</span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2 py-1 me-2">Khác</span>
                                    @endif
                                    {{ $mLabel }}
                                </td>
                                <td class="text-center fw-bold">
                                    {{ number_format($mStat->order_count ?? 0) }}
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($mStat->total_amount ?? 0, 0, ',', '.') }} đ
                                </td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    {{ number_format($mStat->paid_amount ?? 0, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Không có dữ liệu giao dịch trong khoảng thời gian đã chọn.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
