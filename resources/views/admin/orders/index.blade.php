@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng Rèm & Xưởng May')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-cart-check-fill text-warning me-2"></i>Quản Lý Đơn Hàng & Xưởng May
        </h4>
        <p class="text-muted small mb-0">Theo dõi quy trình duyệt đơn, tiến độ cắt may rèm và bàn giao vận chuyển GHN.</p>
    </div>
</div>

<!-- ========================================== -->
<!-- 1. CÁC THẺ THỐNG KÊ KPI ĐƠN HÀNG          -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <!-- Tổng đơn -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Tổng Đơn Hàng</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Đơn chờ duyệt -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Chờ Xác Nhận</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($pendingOrders) }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Đơn đang may đo -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Đang Cắt May</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($preparingOrders) }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                    <i class="bi bi-scissors fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu hoàn tất -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Doanh Thu Đã Thu</span>
                    <h4 class="fw-extrabold text-success mb-0 mt-1">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h4>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="bi bi-cash-coin fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. BỘ LỌC ĐA NĂNG & TÌM KIẾM ĐƠN HÀNG      -->
<!-- ========================================== -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
        <!-- Tìm kiếm -->
        <div class="col-12 col-lg-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" 
                       placeholder="Mã đơn, mã GHN, Tên, SĐT..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Trạng thái đơn -->
        <div class="col-6 col-md-3 col-lg-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Trạng thái đơn --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Đang may đo</option>
                <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>

        <!-- Trạng thái thanh toán -->
        <div class="col-6 col-md-3 col-lg-2">
            <select name="payment_status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Thanh toán --</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
            </select>
        </div>

        <!-- Trạng thái GHN -->
        <div class="col-6 col-md-3 col-lg-2">
            <select name="shipping_status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Vận chuyển GHN --</option>
                <option value="not_shipped" {{ request('shipping_status') == 'not_shipped' ? 'selected' : '' }}>Chưa gửi GHN</option>
                <option value="ready_to_pick" {{ request('shipping_status') == 'ready_to_pick' ? 'selected' : '' }}>Chờ GHN lấy</option>
                <option value="delivering" {{ request('shipping_status') == 'delivering' ? 'selected' : '' }}>Đang giao</option>
                <option value="delivered" {{ request('shipping_status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                <option value="cancelled" {{ request('shipping_status') == 'cancelled' ? 'selected' : '' }}>Đã hủy vận đơn</option>
            </select>
        </div>

        <!-- Nút Lọc / Đặt lại -->
        <div class="col-6 col-md-3 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100">
                <i class="bi bi-funnel me-1"></i> Lọc Đơn
            </button>
            @if(request()->hasAny(['search', 'status', 'payment_status', 'shipping_status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- 3. BẢNG DANH SÁCH ĐƠN HÀNG                -->
<!-- ========================================== -->
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Mã Đơn / Ngày Đặt</th>
                    <th class="py-3">Khách Hàng</th>
                    <th class="py-3">Mẫu Rèm & Số Lượng</th>
                    <th class="py-3">Tổng Tiền</th>
                    <th class="py-3">Vận Đơn GHN</th>
                    <th class="py-3">Trạng Thái</th>
                    <th class="py-3 text-end pe-4">Thao Tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($orders as $order)
                    <tr>
                        <!-- Mã Đơn & Ngày đặt -->
                        <td class="ps-4 py-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold font-monospace text-primary text-decoration-none d-block">
                                {{ $order->order_code }}
                            </a>
                            <small class="text-muted d-block" style="font-size: 0.78rem;">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </small>
                        </td>

                        <!-- Thông tin người nhận -->
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ $order->recipient_name }}</div>
                            <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $order->recipient_phone }}</small>
                        </td>

                        <!-- Sản phẩm rèm -->
                        <td class="py-3 small">
                            <div><strong>{{ $order->items->sum('quantity') }}</strong> bộ rèm</div>
                            <small class="text-muted">{{ number_format($order->items->sum('area'), 2) }} m² vải may</small>
                        </td>

                        <!-- Tổng tiền & Voucher -->
                        <td class="py-3">
                            <div class="fw-extrabold text-danger fs-6">
                                {{ number_format($order->total, 0, ',', '.') }} đ
                            </div>
                            @if ($order->voucher_discount > 0)
                                <span class="badge bg-warning bg-opacity-25 text-dark" style="font-size: 0.7rem;">
                                    <i class="bi bi-tag-fill me-1"></i> -{{ number_format($order->voucher_discount, 0, ',', '.') }} đ
                                </span>
                            @endif
                        </td>

                        <!-- Mã vận đơn GHN -->
                        <td class="py-3">
                            @if ($order->ghn_order_code)
                                <span class="badge bg-warning text-dark font-monospace px-2 py-1">
                                    <i class="bi bi-truck me-1"></i> {{ $order->ghn_order_code }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Chưa tạo mã</span>
                            @endif
                            <div class="mt-1">{!! $order->shipping_status_badge !!}</div>
                        </td>

                        <!-- Trạng thái đơn & thanh toán -->
                        <td class="py-3">
                            <div class="mb-1">{!! $order->status_badge !!}</div>
                            <div>{!! $order->payment_status_badge !!}</div>
                        </td>

                        <!-- Thao tác -->
                        <td class="text-end pe-4 py-3">
                            <div class="btn-group">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-dark" title="Xem chi tiết & Xử lý">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="In phiếu may đo A4">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Không tìm thấy đơn hàng nào phù hợp với điều kiện lọc.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
