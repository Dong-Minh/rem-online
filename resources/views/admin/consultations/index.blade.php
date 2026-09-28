@extends('layouts.admin')

@section('title', 'Quản Lý Lịch Hẹn Khảo Sát & Đo Đạc Rèm')
@section('page_title', 'Lịch Hẹn Khảo Sát Tận Nhà')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Title & Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Quản Lý Lịch Hẹn Khảo Sát Đo Đạc</h4>
            <p class="text-muted mb-0 small">Theo dõi yêu cầu xem mẫu vải, phân công thợ kỹ thuật và quản lý báo giá tại nhà khách hàng.</p>
        </div>
        <a href="{{ route('consultations.create') }}" target="_blank" class="btn btn-dark btn-gold shadow-sm px-3">
            <i class="bi bi-plus-circle me-1"></i> Đăng Ký Hộ Khách Hàng
        </a>
    </div>

    <!-- Quick Stats KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Tổng Lịch Hẹn</div>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-calendar-range fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Chờ Tiếp Nhận</div>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">{{ number_format($stats['pending']) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Đang Khảo Sát / Đã Phân Công</div>
                        <h3 class="fw-bold mb-0 mt-1 text-info">{{ number_format($stats['measuring']) }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                        <i class="bi bi-rulers fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Đã Chốt May / Báo Giá</div>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($stats['completed']) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.consultations.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Tìm mã CS, Tên khách, SĐT, Địa chỉ..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Chờ tiếp nhận</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>👤 Đã phân công thợ</option>
                        <option value="measuring" {{ request('status') === 'measuring' ? 'selected' : '' }}>📏 Đang đo đạc tại nhà</option>
                        <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>📝 Đã báo giá</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✅ Đã chốt may đo</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy lịch</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="time_slot" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Khung giờ --</option>
                        <option value="morning" {{ request('time_slot') === 'morning' ? 'selected' : '' }}>Sáng (08h - 12h)</option>
                        <option value="afternoon" {{ request('time_slot') === 'afternoon' ? 'selected' : '' }}>Chiều (13h30 - 17h30)</option>
                        <option value="evening" {{ request('time_slot') === 'evening' ? 'selected' : '' }}>Tối (18h - 20h30)</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <input type="date" name="date" class="form-control bg-light" value="{{ request('date') }}" onchange="this.form.submit()" title="Lọc theo ngày hẹn">
                </div>

                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Lọc</button>
                    @if(request()->hasAny(['search', 'status', 'time_slot', 'date']))
                        <a href="{{ route('admin.consultations.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Consultations Data Table -->
    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 130px;">Mã Lịch Hẹn</th>
                        <th scope="col" style="width: 180px;">Khách Hàng</th>
                        <th scope="col" style="width: 220px;">Địa Chỉ Khảo Sát</th>
                        <th scope="col" style="width: 170px;">Thời Gian Hẹn</th>
                        <th scope="col">Dòng Rèm & Ô Cửa</th>
                        <th scope="col" style="width: 150px;">Thợ Phụ Trách</th>
                        <th scope="col" style="width: 140px;" class="text-center">Trạng Thái</th>
                        <th scope="col" style="width: 100px;" class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultations as $item)
                        <tr>
                            <td>
                                <a href="{{ route('admin.consultations.show', $item) }}" class="fw-bold text-decoration-none text-dark font-monospace hover-gold d-block">
                                    {{ $item->code }}
                                </a>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $item->created_at->format('d/m H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->customer_name }}</div>
                                <div class="text-muted small">
                                    <a href="tel:{{ $item->phone }}" class="text-decoration-none text-muted">
                                        <i class="bi bi-telephone me-1 text-gold"></i>{{ $item->phone }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark fw-semibold text-truncate" style="max-width: 210px;" title="{{ $item->address }}">
                                    {{ $item->address }}
                                </div>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    {{ $item->district }}, {{ $item->province }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">
                                    <i class="bi bi-calendar-event me-1 text-warning"></i>{{ $item->preferred_date->format('d/m/Y') }}
                                </div>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    {{ $item->time_slot_text }}
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark fw-semibold">
                                    {{ $item->estimated_windows ?? 'Chưa xác định ô cửa' }}
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $item->curtain_types_text }}">
                                    {{ $item->curtain_types_text }}
                                </div>
                            </td>
                            <td>
                                @if($item->assignedStaff)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold small" style="width: 28px; height: 28px; min-width: 28px; font-size: 0.75rem;">
                                            {{ mb_substr($item->assignedStaff->name, 0, 1) }}
                                        </div>
                                        <span class="small fw-semibold text-dark text-truncate">{{ $item->assignedStaff->name }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border">Chưa phân công</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {!! $item->status_badge !!}
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.consultations.show', $item) }}" class="btn btn-outline-dark" title="Chi tiết & Xử lý">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.consultations.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa lịch hẹn này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa lịch hẹn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Chưa có lịch hẹn khảo sát nào phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($consultations->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-center">
                    {{ $consultations->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
