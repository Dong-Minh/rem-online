@extends('layouts.admin')

@section('title', 'Giao Dịch Thanh Toán & Cập Nhật COD')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Quản trị</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Giao dịch thanh toán</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-credit-card-2-front-fill text-warning me-2"></i>Danh Sách Giao Dịch & Xử Lý COD
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
            <a class="nav-link text-secondary fw-semibold bg-white border" href="{{ route('admin.finance.index') }}">
                <i class="bi bi-pie-chart-fill me-1"></i> Thống kê chỉ số
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-semibold shadow-sm" href="{{ route('admin.finance.transactions') }}">
                <i class="bi bi-receipt-cutoff me-1"></i> Giao dịch thanh toán
            </a>
        </li>
    </ul>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.finance.transactions') }}" method="GET">
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
                        <label class="form-label small fw-semibold text-muted mb-1">Phương thức</label>
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

                    <!-- Sort -->
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="amount_asc" {{ request('sort') === 'amount_asc' ? 'selected' : '' }}>Số tiền tăng dần</option>
                            <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>Số tiền giảm dần</option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-12 col-md-12 col-lg-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-3 shadow-sm flex-grow-1">
                            <i class="bi bi-funnel-fill me-1"></i> Áp dụng
                        </button>
                        <a href="{{ route('admin.finance.transactions') }}" class="btn btn-light border px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Transactions -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-list-check text-warning me-2"></i>Danh Sách Giao Dịch Đơn Hàng
            </h5>
            <span class="badge bg-light text-secondary border px-3 py-2">
                Tổng cộng: <strong>{{ number_format($orders->total()) }}</strong> bản ghi
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Đơn hàng</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3">Phương thức</th>
                            <th class="text-end py-3">Số tiền</th>
                            <th class="py-3">Trạng thái thanh toán</th>
                            <th class="pe-4 py-3" style="min-width: 220px;">Cập nhật COD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $statusBadgeClass = match($order->payment_status) {
                                    'paid' => 'bg-success text-white',
                                    'pending' => 'bg-warning text-dark',
                                    'initiated' => 'bg-info text-dark',
                                    'failed' => 'bg-danger text-white',
                                    'cancelled' => 'bg-secondary text-white',
                                    'refund_pending' => 'bg-warning text-dark border border-warning',
                                    'refunded' => 'bg-dark text-white',
                                    default => 'bg-light text-dark border'
                                };
                            @endphp
                            <tr>
                                <!-- Đơn hàng -->
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-primary">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none fw-bold">
                                            #{{ $order->order_code ?? $order->id }}
                                        </a>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i d/m/Y') }}
                                    </div>
                                </td>

                                <!-- Khách hàng -->
                                <td class="py-3">
                                    <div class="fw-semibold text-dark">{{ $order->name ?: 'Khách vãng lai' }}</div>
                                    <div class="small text-muted">
                                        <i class="bi bi-telephone me-1"></i>{{ $order->phone ?: 'Chưa có SĐT' }}
                                    </div>
                                </td>

                                <!-- Phương thức -->
                                <td class="py-3">
                                    @if ($order->gateway === 'cod')
                                        <span class="badge bg-secondary text-white px-2 py-1"><i class="bi bi-truck me-1"></i>COD</span>
                                    @elseif ($order->gateway === 'momo')
                                        <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-wallet-fill me-1"></i>MoMo</span>
                                    @elseif ($order->gateway === 'bank_transfer')
                                        <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-qr-code-scan me-1"></i>VietQR</span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2 py-1">Chưa rõ</span>
                                    @endif
                                </td>

                                <!-- Số tiền -->
                                <td class="text-end py-3 fw-bold text-dark fs-6">
                                    {{ number_format($order->total_price, 0, ',', '.') }} đ
                                </td>

                                <!-- Trạng thái thanh toán -->
                                <td class="py-3">
                                    <span class="badge {{ $statusBadgeClass }} px-2 py-1">
                                        {{ $statuses[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                    @if ($order->paid_at)
                                        <div class="small text-success mt-1">
                                            <i class="bi bi-check-circle me-1"></i>{{ \Carbon\Carbon::parse($order->paid_at)->format('H:i d/m/Y') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Cập nhật COD -->
                                <td class="pe-4 py-3">
                                    @if ($order->gateway === 'cod')
                                        @php
                                            $allowedTransitions = $codTransitions[$order->payment_status] ?? [];
                                        @endphp
                                        @if (count($allowedTransitions) > 0)
                                            <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST" class="d-flex align-items-center gap-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="current_payment_status" value="{{ $order->payment_status }}">
                                                <input type="hidden" name="current_order_status" value="{{ $order->status }}">
                                                <input type="hidden" name="current_payment_id" value="{{ (int) ($order->payment_id ?? 0) }}">
                                                
                                                <select name="payment_status" class="form-select form-select-sm" style="min-width: 140px; font-size: 0.825rem;">
                                                    @foreach ($allowedTransitions as $statusKey)
                                                        <option value="{{ $statusKey }}" {{ $statusKey === $order->payment_status ? 'selected' : '' }}>
                                                            {{ $statuses[$statusKey] ?? $statusKey }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold text-nowrap px-2" title="Cập nhật trạng thái">
                                                    <i class="bi bi-check2"></i> Lưu
                                                </button>
                                            </form>
                                        @else
                                            <span class="small text-muted italic">Không thể thay đổi</span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">
                                            <i class="bi bi-cpu me-1"></i>Tự động cổng
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Không tìm thấy giao dịch nào phù hợp với bộ lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
