@extends('layouts.admin')

@section('title', 'Lịch Sử Sử Dụng Voucher ' . $voucher->code)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách mã giảm giá
    </a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-ticket-perforated text-warning me-2"></i>Chi Tiết & Lịch Sử Sử Dụng: <span class="font-monospace text-primary">{{ $voucher->code }}</span>
            </h4>
            <span class="text-muted small">{{ $voucher->description ?? 'Chưa có mô tả' }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil-square me-1"></i> Chỉnh Sửa
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Thống kê nhanh -->
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <span class="text-muted small">Mức Giảm Giá</span>
            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $voucher->discount_label }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <span class="text-muted small">Đơn Tối Thiểu</span>
            <h4 class="fw-bold text-dark mb-0 mt-1">{{ number_format($voucher->min_order_amount, 0, ',', '.') }} đ</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <span class="text-muted small">Tổng Lượt Sử Dụng</span>
            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $voucher->usages()->count() }} / {{ $voucher->usage_limit ?? '∞' }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <span class="text-muted small">Trạng Thái</span>
            <div class="mt-1">{!! $voucher->status_badge !!}</div>
        </div>
    </div>
</div>

<!-- Bảng lịch sử sử dụng -->
<div class="card card-custom overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold text-dark mb-0">Danh Sách Khách Hàng Đã Dùng Mã ({{ $usages->total() }} lượt)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Khách Hàng</th>
                    <th class="py-3">Đơn Hàng</th>
                    <th class="py-3">Số Tiền Đã Giảm</th>
                    <th class="py-3">Thời Gian Sử Dụng</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($usages as $usage)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $usage->user?->name ?? 'Khách vãng lai' }}</div>
                            <small class="text-muted">{{ $usage->user?->email }} • {{ $usage->user?->phone }}</small>
                        </td>
                        <td class="py-3">
                            @if($usage->order)
                                <a href="{{ route('orders.show', $usage->order->id) }}" class="font-monospace fw-bold text-decoration-none">
                                    {{ $usage->order->order_code }}
                                </a>
                            @else
                                <span class="text-muted">#{{ $usage->order_id }}</span>
                            @endif
                        </td>
                        <td class="py-3 fw-bold text-danger">
                            -{{ number_format($usage->discount_amount, 0, ',', '.') }} đ
                        </td>
                        <td class="py-3 small text-muted">
                            {{ $usage->used_at ? $usage->used_at->format('d/m/Y H:i:s') : 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Chưa có khách hàng nào sử dụng mã giảm giá này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($usages->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $usages->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
