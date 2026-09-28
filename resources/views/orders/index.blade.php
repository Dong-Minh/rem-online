@extends('layouts.client')

@section('title', 'Lịch Sử Đơn Hàng — Rèm Online')

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Lịch sử đơn hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history text-warning me-2"></i>Lịch Sử Đơn Hàng Rèm Của Bạn</h3>
            <p class="text-muted small mb-0">Theo dõi tiến độ may đo, mã vận đơn GHN và tình trạng giao hàng.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Đặt thêm mẫu rèm
        </a>
    </div>

    @if ($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
            <div class="bg-light rounded-circle mx-auto p-4 d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px;">
                <i class="bi bi-box-seam fs-1 text-muted"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Bạn chưa có đơn hàng rèm nào</h5>
            <p class="text-muted small mb-4">Hãy chọn mẫu rèm ưng ý và gửi đơn đặt may đo ngay nhé!</p>
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-gold px-4 py-2">
                    <i class="bi bi-shop me-2"></i> Khám Phá Bộ Sưu Tập Rèm
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Mã Đơn Hàng</th>
                            <th class="py-3">Ngày Đặt</th>
                            <th class="py-3">Mã Vận Đơn GHN</th>
                            <th class="py-3">Số Lượng Rèm</th>
                            <th class="py-3">Tổng Tiền</th>
                            <th class="py-3">Trạng Thái</th>
                            <th class="py-3 text-end pe-4">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($orders as $ord)
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <a href="{{ route('orders.show', $ord->id) }}" class="fw-bold text-dark font-monospace text-decoration-none">
                                        {{ $ord->order_code }}
                                    </a>
                                </td>
                                <td class="py-3 small text-muted">
                                    {{ $ord->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3">
                                    @if ($ord->ghn_order_code)
                                        <span class="badge bg-warning text-dark font-monospace px-2 py-1">
                                            <i class="bi bi-truck me-1"></i>{{ $ord->ghn_order_code }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Chưa tạo mã</span>
                                    @endif
                                </td>
                                <td class="py-3 small">
                                    <strong>{{ $ord->items->sum('quantity') }}</strong> bộ rèm
                                </td>
                                <td class="py-3 fw-extrabold text-danger">
                                    {{ number_format($ord->total, 0, ',', '.') }} đ
                                </td>
                                <td class="py-3">
                                    <div class="d-flex flex-column gap-1">
                                        <div>{!! $ord->status_badge !!}</div>
                                        <div>{!! $ord->payment_status_badge !!}</div>
                                    </div>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                        @if ($ord->payment_method === 'momo' && $ord->payment_status !== 'paid' && $ord->status !== 'cancelled')
                                            <a href="{{ route('orders.momo.pay', $ord->id) }}" class="btn btn-sm btn-danger text-white py-1 px-2 fw-semibold" style="background-color: #a50064; border-color: #a50064; font-size: 0.78rem;">
                                                <i class="bi bi-wallet2 me-1"></i> Trả MoMo
                                            </a>
                                        @endif
                                        <a href="{{ route('orders.show', $ord->id) }}" class="btn btn-sm btn-outline-dark py-1 px-2" style="font-size: 0.78rem;">
                                            Chi tiết <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
