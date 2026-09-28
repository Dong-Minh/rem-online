@extends('layouts.admin')

@section('title', 'Quản Lý Đánh Giá Sản Phẩm')
@section('page_title', 'Quản Lý Đánh Giá & Nhận Xét Rèm')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Đánh Giá Từ Khách Hàng</h4>
            <p class="text-muted mb-0 small">Kiểm duyệt, theo dõi độ hài lòng và quản lý bình luận đánh giá các mẫu rèm.</p>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    @php
        $totalReviews = \App\Models\Review::count();
        $approvedReviews = \App\Models\Review::where('is_approved', true)->count();
        $pendingReviews = \App\Models\Review::where('is_approved', false)->count();
        $avgSystemRating = \App\Models\Review::avg('rating') ?: 5.0;
        $fiveStarReviews = \App\Models\Review::where('rating', 5)->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Tổng Đánh Giá</div>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($totalReviews) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-chat-heart fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Điểm Trung Bình</div>
                        <h3 class="fw-bold mb-0 mt-1 text-primary">{{ number_format($avgSystemRating, 1) }} <i class="bi bi-star-fill text-warning fs-5"></i></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-award fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Đánh Giá 5 Sao</div>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($fiveStarReviews) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-stars fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Đang Ẩn / Chờ Duyệt</div>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">{{ number_format($pendingReviews) }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="bi bi-eye-slash fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Tìm theo khách hàng, sản phẩm hoặc nội dung..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="rating" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Tất cả số sao --</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Sao</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Sao</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 Sao</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ 2 Sao</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ 1 Sao</option>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đang hiển thị (Đã duyệt)</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Đang ẩn</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Lọc</button>
                    @if(request()->hasAny(['search', 'rating', 'status']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table List -->
    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 50px;" class="text-center">#</th>
                        <th scope="col" style="width: 200px;">Khách Hàng</th>
                        <th scope="col" style="width: 220px;">Sản Phẩm Rèm</th>
                        <th scope="col" style="width: 140px;">Đánh Giá</th>
                        <th scope="col">Nội Dung Nhận Xét</th>
                        <th scope="col" style="width: 130px;" class="text-center">Trạng Thái</th>
                        <th scope="col" style="width: 120px;" class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="text-center text-muted small fw-semibold">{{ $review->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; min-width: 36px; font-size: 0.85rem;">
                                        {{ mb_substr($review->user->name ?? 'K', 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-semibold text-dark text-truncate">{{ $review->user->name ?? 'Khách ẩn danh' }}</div>
                                        <div class="text-muted small text-truncate">{{ $review->user->email ?? '' }}</div>
                                        @if($review->order_id)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.65rem;">
                                                <i class="bi bi-patch-check-fill me-1"></i>Đã mua hàng
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($review->product)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $review->product->primary_image_url }}" alt="{{ $review->product->name }}" class="rounded border object-fit-cover" style="width: 44px; height: 44px; min-width: 44px;">
                                        <div class="overflow-hidden">
                                            <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="fw-semibold text-dark text-decoration-none text-truncate d-block small hover-gold">
                                                {{ $review->product->name }}
                                            </a>
                                            <span class="text-muted" style="font-size: 0.75rem;">Mã: #{{ $review->product->id }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">Sản phẩm đã bị xóa</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-warning mb-1" style="font-size: 0.85rem;">
                                    {!! $review->star_html !!}
                                </div>
                                <span class="badge bg-light text-dark border fw-bold">{{ $review->rating }}/5 Sao</span>
                            </td>
                            <td>
                                <div class="text-dark small lh-sm mb-1 fw-normal">
                                    "{{ $review->comment }}"
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $review->created_at->format('d/m/Y H:i') }} ({{ $review->created_at->diffForHumans() }})
                                </small>
                            </td>
                            <td class="text-center">
                                @if($review->is_approved)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i>Hiển thị
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1 rounded-pill">
                                        <i class="bi bi-eye-slash-fill me-1"></i>Đang ẩn
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <!-- Toggle Approval Button -->
                                    <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $review->is_approved ? 'warning' : 'success' }}" title="{{ $review->is_approved ? 'Ẩn đánh giá' : 'Duyệt hiển thị' }}">
                                            <i class="bi bi-{{ $review->is_approved ? 'eye-slash' : 'check2' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa đánh giá">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Chưa có đánh giá nào phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-center">
                    {{ $reviews->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
