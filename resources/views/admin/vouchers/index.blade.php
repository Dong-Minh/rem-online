@extends('layouts.admin')

@section('title', 'Quản Lý Mã Giảm Giá (Vouchers)')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-ticket-perforated text-warning me-2"></i>Quản Lý Mã Giảm Giá & Voucher
        </h4>
        <p class="text-muted small mb-0">Tạo mã khuyến mãi theo %, tiền cố định, giới hạn lượt dùng và đơn tối thiểu.</p>
    </div>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Tạo Mã Giảm Giá Mới
    </a>
</div>

<!-- Bộ lọc & Tìm kiếm -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.vouchers.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm theo mã code hoặc mô tả..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiệu lực</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Chưa bắt đầu</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đang tạm tắt</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Lọc</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary" title="Đặt lại"><i class="bi bi-arrow-clockwise"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Bảng danh sách Vouchers -->
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Mã Code</th>
                    <th class="py-3">Mức Giảm</th>
                    <th class="py-3">Đơn Tối Thiểu</th>
                    <th class="py-3">Lượt Sử Dụng</th>
                    <th class="py-3">Thời Gian Hiệu Lực</th>
                    <th class="py-3">Trạng Thái</th>
                    <th class="py-3 text-end pe-4">Thao Tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($vouchers as $voucher)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning bg-opacity-25 text-dark font-monospace fs-6 px-2 py-1 border border-warning">
                                    {{ $voucher->code }}
                                </span>
                            </div>
                            @if($voucher->description)
                                <small class="text-muted d-block mt-1" style="max-width: 250px;">{{ Str::limit($voucher->description, 50) }}</small>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="fw-bold text-danger">{{ $voucher->discount_label }}</span>
                        </td>
                        <td class="py-3 small">
                            @if($voucher->min_order_amount > 0)
                                <strong>{{ number_format($voucher->min_order_amount, 0, ',', '.') }} đ</strong>
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </td>
                        <td class="py-3 small">
                            <div><strong>{{ $voucher->usages_count }}</strong> / {{ $voucher->usage_limit ?? '∞' }} lượt</div>
                            <small class="text-muted">Tối đa {{ $voucher->usage_per_user }} lần/user</small>
                        </td>
                        <td class="py-3 small">
                            <div class="text-dark">{{ $voucher->starts_at->format('d/m/Y H:i') }}</div>
                            <div class="text-muted">đến {{ $voucher->ends_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="py-3">
                            {!! $voucher->status_badge !!}
                        </td>
                        <td class="text-end pe-4 py-3">
                            <div class="btn-group">
                                <a href="{{ route('admin.vouchers.show', $voucher->id) }}" class="btn btn-sm btn-outline-info" title="Xem lịch sử sử dụng">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-ticket-perforated fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Chưa có mã giảm giá nào phù hợp. Hãy bấm nút "Tạo Mã Giảm Giá Mới".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($vouchers->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $vouchers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
