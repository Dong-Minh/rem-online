@extends('layouts.app')

@section('title', 'Danh Sách Rèm Yêu Thích — ' . config('app.name', 'Rèm Online'))

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang Chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Tài Khoản</a></li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Rèm Yêu Thích</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 text-center bg-light border-bottom">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-4" style="width: 70px; height: 70px; border: 3px solid #b8860b;">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>
                </div>
                <div class="list-group list-group-flush py-2">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-speedometer2 text-muted fs-5"></i>
                        <span>Bảng Tổng Quan</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-bag-check text-muted fs-5"></i>
                        <span>Đơn Hàng Của Tôi</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3 active bg-warning bg-opacity-10 text-dark fw-bold border-start border-4 border-warning">
                        <i class="bi bi-heart-fill text-danger fs-5"></i>
                        <span>Rèm Yêu Thích</span>
                    </a>
                    <a href="{{ route('profile.addresses.index') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-geo-alt text-muted fs-5"></i>
                        <span>Sổ Địa Chỉ Nhận Hàng</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action px-4 py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-person-gear text-muted fs-5"></i>
                        <span>Cài Đặt Tài Khoản</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Danh Sách Mẫu Rèm Yêu Thích</h4>
                    <p class="text-muted small mb-0">Các mẫu rèm cao cấp bạn đã lưu để xem lại hoặc đặt may đo khi cần.</p>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-heart-fill me-1"></i>{{ $wishlists->total() }} Mẫu đã lưu
                </span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($wishlists->count() > 0)
                <div class="row g-4">
                    @foreach($wishlists as $wish)
                        @php $product = $wish->product; @endphp
                        @if($product)
                            <div class="col-12 col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative product-card">
                                    <!-- Remove Wishlist Button -->
                                    <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="position-absolute top-0 end-0 m-3 z-3">
                                        @csrf
                                        <button type="submit" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center text-danger" style="width: 36px; height: 36px;" title="Bỏ khỏi yêu thích">
                                            <i class="bi bi-heart-fill"></i>
                                        </button>
                                    </form>

                                    <!-- Product Image -->
                                    <div class="position-relative overflow-hidden" style="height: 220px; background-color: #f1f5f9;">
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover transition-transform">
                                    </div>

                                    <!-- Product Info -->
                                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="text-muted small mb-1">
                                                {{ $product->categories->first()->name ?? 'Rèm cao cấp' }}
                                            </div>
                                            <h6 class="fw-bold mb-2">
                                                <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none text-truncate d-block">
                                                    {{ $product->name }}
                                                </a>
                                            </h6>

                                            <!-- Rating Stars -->
                                            <div class="d-flex align-items-center gap-1 mb-2 small text-warning">
                                                {!! $product->star_rating_html !!}
                                                <span class="text-muted ms-1 small">({{ $product->reviews_count }})</span>
                                            </div>

                                            <div class="fw-bold text-danger fs-5 mb-3">
                                                {{ number_format($product->price_per_m2) }} <small class="fs-6 text-muted">₫/m²</small>
                                            </div>
                                        </div>

                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-dark btn-gold w-100 rounded-pill py-2 text-decoration-none">
                                            <i class="bi bi-rulers me-1"></i>Xem Chi Tiết & May Đo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($wishlists->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $wishlists->links() }}
                    </div>
                @endif
            @else
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-heart fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Danh sách yêu thích của bạn đang trống</h5>
                        <p class="text-muted small mb-4">Hãy khám phá các bộ sưu tập rèm vải, rèm roman, rèm cuốn và nhấn biểu tượng trái tim để lưu lại nhé.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-dark btn-gold px-4 py-2 rounded-pill">
                            <i class="bi bi-grid me-2"></i>Khám Phá Các Mẫu Rèm
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
