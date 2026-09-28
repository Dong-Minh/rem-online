@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển')
@section('page_title', 'Tổng Quan Hệ Thống')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1: Sản phẩm -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Sản Phẩm Rèm</span>
                    <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['total_products'] }}</h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 fs-3">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small text-decoration-none mt-3 d-inline-block text-primary">
                Xem kho sản phẩm <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Stat 2: Đơn hàng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Đơn Hàng (Chờ Duyệt: {{ $stats['pending_orders'] }})</span>
                    <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['total_orders'] }}</h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-3">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small text-decoration-none mt-3 d-inline-block text-warning fw-bold">
                Xử lý đơn hàng <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Stat 3: Khách hàng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Khách Hàng</span>
                    <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['total_customers'] }}</h3>
                </div>
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-3">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <span class="small text-muted mt-3 d-inline-block">Tài khoản thành viên</span>
        </div>
    </div>

    <!-- Stat 4: Doanh thu -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 bg-white h-100 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Doanh Thu Thu Được</span>
                    <h4 class="fw-extrabold mb-0 mt-1 text-success">{{ number_format($stats['total_revenue'], 0, ',', '.') }} đ</h4>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-3">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
            <span class="small text-muted mt-3 d-inline-block">Đơn hoàn tất / Đã thanh toán</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Bảng Đơn Hàng Mới Nhất -->
    <div class="col-12 col-xl-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-warning me-1"></i> Đơn Hàng Rèm Mới Nhất
                </h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark">
                    Xem tất cả <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th class="text-end pe-3">Xem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $ord)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                        {{ $ord->order_code }}
                                    </a>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $ord->recipient_name }}</div>
                                    <small class="text-muted">{{ $ord->recipient_phone }}</small>
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ number_format($ord->total, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    {!! $ord->status_badge !!}
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-light border">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bảng Sản Phẩm Mới Nhất -->
    <div class="col-12 col-xl-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-box-seam text-warning me-1"></i> Sản Phẩm Rèm Mới
                </h6>
                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-gold">
                    <i class="bi bi-plus-lg"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">Sản Phẩm</th>
                            <th>Đơn Giá / m²</th>
                            <th class="text-end pe-3">Sửa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentProducts as $prod)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ Str::limit($prod->name, 25) }}</div>
                                            <code>{{ $prod->sku }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark small">{{ $prod->formatted_sale_price ?? $prod->formatted_unit_price }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.products.edit', $prod) }}" class="btn btn-sm btn-light border">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
