@extends('layouts.client')

@section('title', ($currentCategory ? $currentCategory->name : 'Tất Cả Sản Phẩm Rèm') . ' — Cửa Hàng Rèm Online')

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Sản phẩm rèm</a></li>
                @if ($currentCategory)
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $currentCategory->name }}</li>
                @else
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Tất cả mẫu rèm</li>
                @endif
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <!-- Category Title / Banner -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">
            {{ $currentCategory ? $currentCategory->name : 'Bộ Sưu Tập Rèm Cửa Cao Cấp' }}
        </h3>
        <p class="text-muted small mb-0">
            {{ $currentCategory && $currentCategory->description ? $currentCategory->description : 'Khám phá các mẫu rèm chất lượng cao, tính giá theo mét vuông chuẩn xác, đa dạng mẫu mã và màu sắc.' }}
        </p>
    </div>

    <div class="row g-4">
        <!-- 1. CỘT TRÁI: BỘ LỌC TÌM KIẾM (SIDEBAR FILTER) -->
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 sticky-top" style="top: 90px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-funnel me-1 text-warning"></i> Bộ Lọc Sản Phẩm</h6>
                    <a href="{{ route('products.index') }}" class="text-muted small text-decoration-none">Xóa bộ lọc</a>
                </div>

                <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Lọc theo Danh Mục -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Danh Mục Rèm</label>
                        <div class="d-flex flex-column gap-1">
                            <a href="{{ route('products.index', array_merge(request()->except('category', 'page'))) }}" 
                               class="text-decoration-none small py-1 px-2 rounded {{ !request('category') ? 'bg-warning text-dark fw-bold' : 'text-muted' }}">
                                Tất cả danh mục
                            </a>
                            @foreach ($categories as $cat)
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                                   class="text-decoration-none small py-1 px-2 rounded d-flex justify-content-between align-items-center {{ request('category') == $cat->slug ? 'bg-warning text-dark fw-bold' : 'text-muted' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="badge bg-light text-secondary border">{{ $cat->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Lọc theo khoảng giá / m2 -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Khoảng Giá / m² (VNĐ)</label>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ..." value="{{ request('min_price') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến..." value="{{ request('max_price') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-dark w-100">Áp dụng giá</button>
                    </div>

                    <!-- Lọc theo Kiểu Rèm -->
                    @if ($curtainTypes->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-secondary">Kiểu Rèm</label>
                            <select name="curtain_type" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">-- Tất cả kiểu rèm --</option>
                                @foreach ($curtainTypes as $ct)
                                    <option value="{{ $ct }}" {{ request('curtain_type') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Lọc theo Chất liệu -->
                    @if ($materials->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-secondary">Chất Liệu</label>
                            <select name="material" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">-- Tất cả chất liệu --</option>
                                @foreach ($materials as $mat)
                                    <option value="{{ $mat }}" {{ request('material') == $mat ? 'selected' : '' }}>{{ $mat }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Lọc theo Phong cách -->
                    @if ($styles->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-secondary">Phong Cách</label>
                            <select name="style" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">-- Tất cả phong cách --</option>
                                @foreach ($styles as $st)
                                    <option value="{{ $st }}" {{ request('style') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- 2. CỘT PHẢI: DANH SÁCH SẢN PHẨM RÈM (PRODUCT GRID) -->
        <div class="col-12 col-lg-9">
            <!-- Top Bar: Số lượng & Sắp xếp -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm border">
                <span class="small text-muted mb-2 mb-sm-0">
                    Hiển thị <strong>{{ $products->count() }}</strong> trên tổng số <strong>{{ $products->total() }}</strong> mẫu rèm
                </span>

                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted text-nowrap">Sắp xếp theo:</label>
                    <select class="form-select form-select-sm" style="width: auto;" onchange="location = this.value;">
                        <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá / m²: Tăng dần</option>
                        <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá / m²: Giảm dần</option>
                        <option value="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'popular'])) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Bán chạy nhất</option>
                    </select>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="row g-4">
                @forelse ($products as $prod)
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="product-card">
                            <div class="product-thumb-wrap">
                                @if ($prod->sale_price)
                                    <div class="badge-sale">-{{ $prod->discount_percentage }}%</div>
                                @endif
                                
                                <div class="badge-tag">
                                    @if ($prod->is_hot)
                                        <span class="badge bg-danger">HOT</span>
                                    @elseif ($prod->is_trending)
                                        <span class="badge bg-info text-dark">Trending</span>
                                    @endif
                                </div>

                                <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="product-thumb">
                            </div>

                            <div class="product-body">
                                <span class="product-category">{{ $prod->categories->first()->name ?? 'RÈM CỬA' }}</span>
                                <a href="{{ route('products.show', $prod->slug) }}" class="product-title">{{ $prod->name }}</a>
                                
                                <!-- Color dots -->
                                <div class="d-flex gap-1 mb-2">
                                    @foreach ($prod->colors->take(4) as $clr)
                                        <span class="rounded-circle border" style="width: 14px; height: 14px; background-color: {{ $clr->hex_code ?? '#ccc' }};" title="{{ $clr->name }}"></span>
                                    @endforeach
                                </div>

                                <div class="product-price-box">
                                    <span class="product-price">{{ $prod->formatted_effective_price }}</span>
                                    @if ($prod->sale_price)
                                        <span class="product-old-price">{{ $prod->formatted_unit_price }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('products.show', $prod->slug) }}" class="btn btn-sm btn-outline-gold mt-3 w-100">
                                    <i class="bi bi-rulers me-1"></i> Xem & Tính Giá Theo m²
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="p-5 bg-white rounded-4 shadow-sm">
                            <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold text-dark">Không tìm thấy mẫu rèm phù hợp</h5>
                            <p class="text-muted small mb-3">Hãy thử thay đổi từ khóa tìm kiếm hoặc điều chỉnh lại bộ lọc giá.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-gold btn-sm">Xem tất cả rèm</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
