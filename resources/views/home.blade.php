@extends('layouts.client')

@section('title', 'Trang Chủ — Rèm Cửa & Nội Thất Cao Cấp')

@section('content')
<!-- 1. Hero Banner Carousel / Section -->
<section class="py-5" style="background: linear-gradient(135deg, #1a2232 0%, #2a374f 100%); color: #ffffff;">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-6">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="bi bi-stars"></i> Đẳng Cấp Không Gian Sống
                </span>
                <h1 class="display-4 fw-extrabold mb-3 text-white" style="line-height: 1.15;">
                    Rèm Cửa Cao Cấp <br><span style="color: #f59e0b;">May Đo Theo Kích Thước</span>
                </h1>
                <p class="lead text-light text-opacity-75 mb-4">
                    Chuyên rèm vải Bỉ, rèm cầu vồng Hàn Quốc, rèm cản sáng 100% cách nhiệt. Báo giá trực tiếp theo mét vuông (m²), minh bạch và tối ưu chi phí.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-gold btn-lg px-4 py-3">
                        <i class="bi bi-grid-fill me-2"></i> Khám Phá Bộ Sưu Tập
                    </a>
                    <a href="#consultation-form" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="bi bi-telephone-outbound me-2"></i> Đăng Ký Khảo Sát Tận Nhà
                    </a>
                </div>

                <div class="row g-3 mt-4 pt-4 border-top border-secondary border-opacity-50">
                    <div class="col-4">
                        <h4 class="fw-bold text-warning mb-0">100%</h4>
                        <small class="text-light text-opacity-75">Cản sáng chống UV</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-warning mb-0">3 Năm</h4>
                        <small class="text-light text-opacity-75">Bảo hành phụ kiện</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-warning mb-0">Miễn Phí</h4>
                        <small class="text-light text-opacity-75">Khảo sát & Đo đạc</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 text-center">
                <div class="position-relative">
                    <img src="{{ asset('images/products/c44d8461-6023-4705-88f1-ec80d9b56841.jpeg') }}" 
                         alt="Rèm cửa cao cấp biệt thự" class="img-fluid rounded-4 shadow-lg border border-warning border-opacity-25" style="max-height: 480px; width: 100%; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-dark bg-opacity-75 backdrop-blur rounded-3 text-start border border-secondary border-opacity-50 text-white shadow">
                        <span class="small text-warning fw-bold d-block">★ Bộ Sưu Tập Rèm Hoàng Gia</span>
                        <span class="fw-semibold">Rèm Vải 2 Lớp Biệt Thự & Chung Cư Cao Cấp</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Danh Mục Nổi Bật -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-warning text-uppercase fw-bold small">Bộ Sưu Tập Đa Dạng</span>
            <h2 class="fw-bold text-dark mt-1">Danh Mục Rèm Nổi Bật</h2>
            <p class="text-muted">Phân loại theo không gian sử dụng và công năng đặc thù</p>
        </div>

        <div class="row g-4">
            @foreach ($homeCategories as $cat)
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 text-center p-3 rounded-4 shadow-sm" style="background-color: #f8fafc; transition: all 0.3s ease;">
                            <div class="mx-auto mb-3 rounded-circle overflow-hidden shadow-sm" style="width: 80px; height: 80px;">
                                <img src="{{ Str::startsWith($cat->image, ['http://', 'https://']) ? $cat->image : asset('storage/' . $cat->image) }}" 
                                     alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $cat->name }}</h6>
                            <small class="text-muted">{{ $cat->products_count }} mẫu</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. Sản Phẩm HOT -->
@if ($hotProducts->isNotEmpty())
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-danger px-2 py-1 mb-1">🔥 BÁN CHẠY NHẤT</span>
                <h3 class="fw-bold text-dark mb-0">Sản Phẩm Rèm Đang HOT</h3>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach ($hotProducts as $prod)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="product-card">
                        <div class="product-thumb-wrap">
                            @if ($prod->sale_price)
                                <div class="badge-sale">-{{ $prod->discount_percentage }}%</div>
                            @endif
                            <span class="badge bg-danger badge-tag">HOT</span>
                            <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="product-thumb">
                        </div>
                        <div class="product-body">
                            <span class="product-category">{{ $prod->categories->first()->name ?? 'RÈM CỬA' }}</span>
                            <a href="{{ route('products.show', $prod->slug) }}" class="product-title">{{ $prod->name }}</a>
                            
                            <!-- Bảng màu chấm tròn -->
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
                                <i class="bi bi-rulers me-1"></i> Xem Chi Tiết & Tính Giá
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 4. Sản Phẩm Khuyến Mãi (Sale) -->
@if ($saleProducts->isNotEmpty())
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-warning text-dark px-2 py-1 mb-1">🏷️ GIÁ TỐT NHẤT</span>
                <h3 class="fw-bold text-dark mb-0">Đang Khuyến Mãi Giảm Giá</h3>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach ($saleProducts as $prod)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="product-card">
                        <div class="product-thumb-wrap">
                            <div class="badge-sale">-{{ $prod->discount_percentage }}%</div>
                            <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="product-thumb">
                        </div>
                        <div class="product-body">
                            <span class="product-category">{{ $prod->categories->first()->name ?? 'RÈM CỬA' }}</span>
                            <a href="{{ route('products.show', $prod->slug) }}" class="product-title">{{ $prod->name }}</a>
                            
                            <div class="product-price-box">
                                <span class="product-price">{{ $prod->formatted_sale_price }}</span>
                                <span class="product-old-price">{{ $prod->formatted_unit_price }}</span>
                            </div>

                            <a href="{{ route('products.show', $prod->slug) }}" class="btn btn-sm btn-gold mt-3 w-100">
                                <i class="bi bi-cart-plus me-1"></i> Mua Ngay Giá Khuyến Mãi
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 5. Form Đăng Ký Tư Vấn & Khảo Sát Đo Đạc -->
<section id="consultation-form" class="py-5" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-6">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="bi bi-headset"></i> Dịch Vụ Khách Hàng Chu Đáo
                </span>
                <h2 class="display-6 fw-bold text-white mb-3">Đăng Ký Tư Vấn & Khảo Sát Rèm Tận Nhà</h2>
                <p class="text-light text-opacity-75 mb-4">
                    Vì rèm cửa cần độ chính xác về kích thước và màu sắc hài hòa với ánh sáng phòng, đội ngũ chuyên viên của chúng tôi sẽ mang mẫu vải tận nơi để bạn trải nghiệm và tư vấn giải pháp cản sáng tối ưu nhất.
                </p>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle bg-warning text-dark fs-5"><i class="bi bi-check-lg"></i></div>
                        <span>Mang theo hơn 500+ mẫu vải catalogue thực tế tận nhà.</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle bg-warning text-dark fs-5"><i class="bi bi-check-lg"></i></div>
                        <span>Đo đạc bằng máy laser chính xác từng milimet.</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle bg-warning text-dark fs-5"><i class="bi bi-check-lg"></i></div>
                        <span>Báo giá minh bạch, cam kết không phát sinh phụ phí.</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card p-4 rounded-4 shadow-lg border-0" style="background: rgba(255, 255, 255, 0.95); color: #334155;">
                    <h5 class="fw-bold text-dark mb-1">Gửi Yêu Cầu Tư Vấn Nhanh</h5>
                    <p class="text-muted small mb-3">Chúng tôi sẽ liên hệ lại với bạn trong vòng 15 phút.</p>

                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" placeholder="0912345678" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Loại rèm quan tâm</label>
                                <select class="form-select">
                                    <option value="">-- Chọn loại rèm --</option>
                                    <option value="Rèm Vải 2 Lớp">Rèm Vải 2 Lớp</option>
                                    <option value="Rèm Cầu Vồng">Rèm Cầu Vồng Hàn Quốc</option>
                                    <option value="Rèm Cuốn Văn Phòng">Rèm Cuốn</option>
                                    <option value="Khác">Cần tư vấn loại phù hợp</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nội dung ghi chú (Kích thước dự kiến, địa chỉ...)</label>
                            <textarea class="form-control" rows="2" placeholder="Ví dụ: Cần may rèm phòng khách căn hộ 3 phòng ngủ ở Cầu Giấy..."></textarea>
                        </div>
                        <button type="button" class="btn btn-gold w-100 py-2 fw-bold" onclick="alert('Cảm ơn bạn! Yêu cầu tư vấn rèm đã được ghi nhận. Chuyên viên sẽ gọi điện cho bạn sớm nhất.')">
                            <i class="bi bi-send me-1"></i> Gửi Yêu Cầu Khảo Sát Miễn Phí
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
