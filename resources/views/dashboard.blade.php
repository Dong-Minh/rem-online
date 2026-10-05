@extends('layouts.client')

@section('title', 'Tài Khoản Khách Hàng — ' . config('app.name', 'Rèm Online'))

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang Chủ</a></li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Bảng Điều Khiển Tài Khoản</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Menu Khách hàng -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 text-center bg-light border-bottom">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-4" style="width: 70px; height: 70px; border: 3px solid #b8860b;">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-1">{{ Auth::user()->email }}</p>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold">Khách hàng thành viên</span>
                </div>
                <div class="list-group list-group-flush py-2">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3 active bg-warning bg-opacity-10 text-dark fw-bold border-start border-4 border-warning">
                        <i class="bi bi-speedometer2 text-warning fs-5"></i>
                        <span>Bảng Tổng Quan</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-bag-check text-muted fs-5"></i>
                        <span>Đơn Hàng Của Tôi</span>
                    </a>
                    <a href="{{ route('profile.addresses.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-geo-alt text-muted fs-5"></i>
                        <span>Sổ Địa Chỉ Nhận Hàng</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-person-gear text-muted fs-5"></i>
                        <span>Cài Đặt Tài Khoản</span>
                    </a>
                    @if (Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3 text-warning fw-bold">
                            <i class="bi bi-shield-lock text-warning fs-5"></i>
                            <span>Trang Quản Trị (Admin)</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Nội dung chính -->
        <div class="col-lg-9">
            <!-- 4 Thẻ KPI Khách Hàng -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Tổng đơn hàng</small>
                                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total_orders'] ?? 0 }}</h3>
                            </div>
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 fs-4">
                                <i class="bi bi-bag-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Đang xử lý</small>
                                <h3 class="fw-bold text-warning mb-0 mt-1">{{ $stats['pending_orders'] ?? 0 }}</h3>
                            </div>
                            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3 fs-4">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Rèm yêu thích</small>
                                <h3 class="fw-bold text-danger mb-0 mt-1">{{ $stats['wishlist_count'] ?? 0 }}</h3>
                            </div>
                            <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 fs-4">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Đã chi tiêu</small>
                                <h4 class="fw-bold text-success mb-0 mt-1 fs-5">{{ number_format($stats['total_spent'] ?? 0) }} <small class="fs-6">₫</small></h4>
                            </div>
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded-3 fs-4">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Ưu đãi May Rèm Theo Yêu Cầu -->
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white position-relative overflow-hidden mb-4" style="background: linear-gradient(135deg, #1a2232 0%, #2a374f 100%);">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8">
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2 fw-bold">Dịch vụ may đo cao cấp</span>
                        <h4 class="fw-bold text-white mb-2">Bạn cần tư vấn hoặc may rèm kích thước riêng?</h4>
                        <p class="text-light text-opacity-75 small mb-3">
                            Rèm Online cung cấp giải pháp trọn gói từ khảo sát, báo giá theo mét vuông ($m^2$), may đo tỉ mỉ đến lắp đặt tại nhà.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-dark btn-gold btn-sm px-3 py-2 rounded-pill">
                                <i class="bi bi-shop me-1"></i> Khám Phá Mẫu Rèm
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill">
                                <i class="bi bi-heart me-1"></i> Xem Rèm Đã Lưu
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn Hàng Gần Đây -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-warning"></i>Đơn Hàng Gần Đây</h5>
                    <a href="{{ route('orders.index') }}" class="text-decoration-none small text-gold fw-semibold">Xem tất cả đơn hàng &rarr;</a>
                </div>

                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Sản Phẩm May Đo</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th class="text-end">Chi Tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="fw-bold text-dark">
                                            <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-dark hover-gold">
                                                #{{ $order->code ?? $order->id }}
                                            </a>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $order->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($order->items->first() && $order->items->first()->product)
                                                    <img src="{{ $order->items->first()->product->primary_image_url }}" alt="" class="rounded border object-fit-cover" style="width: 40px; height: 40px;">
                                                    <div class="overflow-hidden">
                                                        <div class="text-truncate fw-semibold small text-dark" style="max-width: 220px;">
                                                            {{ $order->items->first()->product->name }}
                                                        </div>
                                                        @if($order->items->count() > 1)
                                                            <span class="text-muted small" style="font-size: 0.72rem;">+{{ $order->items->count() - 1 }} sản phẩm khác</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">{{ $order->items->count() }} bộ rèm</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="fw-bold text-danger">
                                            {{ number_format($order->total) }} ₫
                                        </td>
                                        <td>
                                            @php
                                                $statusBadges = [
                                                    'pending' => ['bg-warning text-dark', 'Chờ xử lý'],
                                                    'confirmed' => ['bg-primary text-white', 'Đã xác nhận'],
                                                    'processing' => ['bg-info text-dark', 'Đang may xưởng'],
                                                    'shipping' => ['bg-primary bg-opacity-75 text-white', 'Đang giao hàng'],
                                                    'delivered' => ['bg-success text-white', 'Đã giao thành công'],
                                                    'cancelled' => ['bg-danger text-white', 'Đã hủy'],
                                                ];
                                                $badge = $statusBadges[$order->status] ?? ['bg-secondary text-white', $order->status];
                                            @endphp
                                            <span class="badge {{ $badge[0] }} px-2 py-1 rounded-pill small">{{ $badge[1] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-5 text-center bg-light rounded-4 text-muted">
                        <i class="bi bi-bag-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="mb-2 fw-semibold text-dark">Bạn chưa có đơn hàng nào</p>
                        <small class="d-block mb-3">Hãy chọn mẫu rèm ưng ý và may đo ngay nhé!</small>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-dark btn-gold rounded-pill px-4 py-2">Khám Phá Sản Phẩm Rèm</a>
                    </div>
                @endif
            </div>

            <!-- Rèm Yêu Thích Mới Lưu (Wishlist Preview) -->
            @if(isset($wishlistedProducts) && $wishlistedProducts->count() > 0)
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-heart-fill me-2 text-danger"></i>Mẫu Rèm Đã Lưu Gần Đây</h5>
                        <a href="{{ route('wishlist.index') }}" class="text-decoration-none small text-gold fw-semibold">Xem tất cả &rarr;</a>
                    </div>
                    <div class="row g-3">
                        @foreach($wishlistedProducts as $item)
                            @if($item->product)
                                <div class="col-6 col-md-3">
                                    <div class="card h-100 border rounded-3 overflow-hidden shadow-none hover-shadow transition">
                                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-100 object-fit-cover" style="height: 120px;">
                                        <div class="p-2">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-dark fw-semibold small text-decoration-none text-truncate d-block">
                                                {{ $item->product->name }}
                                            </a>
                                            <div class="text-danger small fw-bold mt-1">
                                                {{ number_format($item->product->price_per_m2) }} ₫/m²
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
