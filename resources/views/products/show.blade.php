@extends('layouts.client')

@section('title', $product->name . ' — Rèm Cửa Cao Cấp')

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Cửa hàng rèm</a></li>
                @if ($product->categories->first())
                    <li class="breadcrumb-item">
                        <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}" class="text-decoration-none text-muted">
                            {{ $product->categories->first()->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ Str::limit($product->name, 35) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4 g-xl-5">
        <!-- ========================================== -->
        <!-- 1. CỘT TRÁI: GALLERY HÌNH ẢNH SẢN PHẨM     -->
        <!-- ========================================== -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white sticky-top" style="top: 90px; z-index: 10;">
                <!-- Main Image Preview -->
                <div class="position-relative rounded-4 overflow-hidden mb-3 bg-light" style="padding-top: 80%;">
                    @if ($product->sale_price)
                        <div class="badge-sale">-{{ $product->discount_percentage }}%</div>
                    @endif

                    <div class="badge-tag">
                        @if ($product->is_hot)
                            <span class="badge bg-danger">🔥 HOT</span>
                        @elseif ($product->is_trending)
                            <span class="badge bg-info text-dark">⚡ Trending</span>
                        @endif
                    </div>

                    <img id="mainImageDisplay" src="{{ $product->main_image_url }}" alt="{{ $product->name }}" 
                         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover transition-all" style="transition: all 0.3s ease;">
                </div>

                <!-- Thumbnails Carousel / Row -->
                <div class="d-flex gap-2 overflow-auto pb-2" id="galleryThumbnails">
                    <!-- Thumbnail ảnh chính -->
                    <div class="border rounded-3 overflow-hidden p-1 cursor-pointer gallery-thumb active-thumb border-warning" 
                         style="width: 75px; height: 75px; flex-shrink: 0;" onclick="switchMainImage('{{ $product->main_image_url }}', this)">
                        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover rounded-2">
                    </div>

                    <!-- Thumbnails ảnh phụ -->
                    @foreach ($product->images as $img)
                        <div class="border rounded-3 overflow-hidden p-1 cursor-pointer gallery-thumb" 
                             style="width: 75px; height: 75px; flex-shrink: 0;" onclick="switchMainImage('{{ Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . $img->image_path) }}', this)">
                            <img src="{{ Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . $img->image_path) }}" 
                                 alt="Ảnh chi tiết {{ $product->name }}" class="w-100 h-100 object-fit-cover rounded-2">
                        </div>
                    @endforeach
                </div>

                <!-- Cam kết chất lượng -->
                <div class="row g-2 mt-3 pt-3 border-top text-center small text-muted">
                    <div class="col-4 border-end">
                        <i class="bi bi-shield-check text-warning fs-5 d-block mb-1"></i>
                        <span class="fw-semibold text-dark">Bảo hành 3 năm</span>
                        <small class="d-block" style="font-size: 0.75rem;">Phụ kiện chính hãng</small>
                    </div>
                    <div class="col-4 border-end">
                        <i class="bi bi-rulers text-warning fs-5 d-block mb-1"></i>
                        <span class="fw-semibold text-dark">Đo đạc tận nhà</span>
                        <small class="d-block" style="font-size: 0.75rem;">Miễn phí khảo sát</small>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-truck text-warning fs-5 d-block mb-1"></i>
                        <span class="fw-semibold text-dark">Lắp đặt trọn gói</span>
                        <small class="d-block" style="font-size: 0.75rem;">Nhanh chóng trong 48h</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. CỘT PHẢI: TÙY CHỈNH & TÍNH GIÁ REALTIME -->
        <!-- ========================================== -->
        <div class="col-12 col-lg-6">
            <div class="ps-lg-2">
                <!-- Categories & SKU -->
                <!-- Categories & SKU & Wishlist -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex gap-1 flex-wrap">
                        @foreach ($product->categories as $c)
                            <span class="badge bg-light text-secondary border">{{ $c->name }}</span>
                        @endforeach
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Mã SKU: <strong class="text-dark font-monospace">{{ $product->sku }}</strong></span>
                        @auth
                            <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex align-items-center justify-content-center shadow-none" style="width: 32px; height: 32px;" title="{{ $product->isWishlistedBy(Auth::user()) ? 'Bỏ thích' : 'Lưu yêu thích' }}">
                                    <i class="bi bi-heart{{ $product->isWishlistedBy(Auth::user()) ? '-fill text-danger' : '' }}"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center shadow-none" style="width: 32px; height: 32px;" title="Đăng nhập để lưu yêu thích">
                                <i class="bi bi-heart"></i>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Product Name -->
                <h2 class="fw-bold text-dark mb-2" style="line-height: 1.25;">{{ $product->name }}</h2>

                <!-- Rating & Sold count -->
                <div class="d-flex align-items-center gap-3 mb-3 small flex-wrap">
                    <div class="d-flex align-items-center gap-1 text-warning">
                        {!! $product->star_rating_html !!}
                        <span class="text-dark fw-bold ms-1">{{ number_format($product->average_rating, 1) }}</span>
                        <a href="#reviews-tab" onclick="document.getElementById('reviews-tab').click();" class="text-muted text-decoration-none ms-1">
                            ({{ $product->reviews_count }} đánh giá)
                        </a>
                    </div>
                    <span class="text-muted">|</span>
                    <span class="text-muted">Đã bán: <strong class="text-dark">{{ $product->sold_count ?? 120 }}</strong> bộ rèm</span>
                    <span class="text-muted">|</span>
                    <span class="badge bg-success-subtle text-success">Còn nhận may đo</span>
                </div>

                <!-- Unit Price Box -->
                <div class="p-3 bg-light rounded-4 mb-4 d-flex align-items-baseline gap-3">
                    <div>
                        <small class="text-muted d-block fw-semibold mb-1">ĐƠN GIÁ THEO MÉT VUÔNG (m²):</small>
                        <span class="display-6 fw-extrabold text-danger" id="displayUnitPrice">
                            {{ $product->formatted_effective_price }}
                        </span>
                    </div>
                    @if ($product->sale_price)
                        <div>
                            <span class="text-muted text-decoration-line-through fs-5">
                                {{ $product->formatted_unit_price }}
                            </span>
                            <span class="badge bg-danger ms-2">Tiết kiệm {{ $product->discount_percentage }}%</span>
                        </div>
                    @endif
                </div>

                <!-- Short Description -->
                @if ($product->short_description)
                    <p class="text-secondary small mb-4 pb-2 border-bottom">
                        {{ $product->short_description }}
                    </p>
                @endif

                <!-- ========================================== -->
                <!-- FORM TÍNH GIÁ & ĐẶT MAY RÈM THEO KÍCH THƯỚC -->
                <!-- ========================================== -->
                <form id="curtainOrderForm" action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="action_type" id="formActionType" value="add_to_cart">
                    <input type="hidden" id="rawUnitPrice" value="{{ $product->effective_price }}">

                    <!-- BƯỚC 1: CHỌN MÀU SẮC RÈM -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>1. Chọn Màu Sắc Rèm <span class="text-danger">*</span>:</span>
                            <span class="small text-warning fw-bold" id="selectedColorName">Vui lòng chọn màu</span>
                        </label>
                        <div class="d-flex flex-wrap gap-2" id="colorPickerContainer">
                            @foreach ($product->colors as $index => $clr)
                                <label class="color-option-card border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer position-relative {{ $index === 0 ? 'border-warning bg-warning bg-opacity-10' : '' }}" 
                                       style="cursor: pointer; transition: all 0.2s ease;">
                                    <input type="radio" name="color_id" value="{{ $clr->id }}" class="d-none color-radio" 
                                           data-color-name="{{ $clr->name }}" {{ $index === 0 ? 'checked' : '' }} onchange="onColorSelected(this)">
                                    <span class="rounded-circle border shadow-sm" style="width: 22px; height: 22px; background-color: {{ $clr->hex_code ?? '#ccc' }};"></span>
                                    <span class="small fw-semibold text-dark">{{ $clr->name }}</span>
                                    @if ($clr->pivot->stock_quantity <= 10)
                                        <small class="text-danger font-monospace" style="font-size: 0.7rem;">(Sắp hết)</small>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- BƯỚC 2: NHẬP KÍCH THƯỚC MAY ĐO (MÉT) -->
                    <div class="mb-4 p-3 bg-white border rounded-4 shadow-sm">
                        <label class="form-label fw-bold text-dark mb-1">
                            <i class="bi bi-arrows-fullscreen text-warning me-1"></i> 2. Nhập Kích Thước May Đo Cửa Sổ (Mét):
                        </label>
                        <small class="text-muted d-block mb-3">
                            Giới hạn xưởng nhận may: Rộng ({{ $product->min_width ?? 1.0 }}m - {{ $product->max_width ?? 6.0 }}m) × Cao ({{ $product->min_height ?? 1.5 }}m - {{ $product->max_height ?? 4.5 }}m)
                        </small>

                        <div class="row g-3">
                            <!-- Chiều rộng -->
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Chiều Rộng (Mét) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.05" class="form-control fw-bold fs-5 text-dark" id="inputWidth" name="width" 
                                           value="{{ old('width', 2.50) }}" 
                                           min="{{ $product->min_width ?? 0.5 }}" 
                                           max="{{ $product->max_width ?? 10.0 }}" 
                                           oninput="calculateRealtimePrice()" required>
                                    <span class="input-group-text">m</span>
                                </div>
                            </div>

                            <!-- Chiều cao -->
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Chiều Cao (Mét) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.05" class="form-control fw-bold fs-5 text-dark" id="inputHeight" name="height" 
                                           value="{{ old('height', 2.80) }}" 
                                           min="{{ $product->min_height ?? 0.5 }}" 
                                           max="{{ $product->max_height ?? 10.0 }}" 
                                           oninput="calculateRealtimePrice()" required>
                                    <span class="input-group-text">m</span>
                                </div>
                            </div>
                        </div>

                        <!-- Kích thước phổ biến mẫu -->
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted me-2">Gợi ý kích thước chuẩn:</small>
                            <div class="d-inline-flex flex-wrap gap-1 mt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="setDimensions(1.8, 2.2)">Cửa sổ nhỏ (1.8m × 2.2m)</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="setDimensions(2.5, 2.8)">Cửa ban công (2.5m × 2.8m)</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="setDimensions(3.2, 2.8)">Phòng khách lớn (3.2m × 2.8m)</button>
                            </div>
                        </div>

                        <!-- Alert lỗi kích thước -->
                        <div id="dimensionAlert" class="alert alert-danger py-2 px-3 small mt-3 d-none" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="dimensionAlertText"></span>
                        </div>
                    </div>

                    <!-- BƯỚC 3: HỘP TÍNH TOÁN REALTIME & SỐ LƯỢNG -->
                    <div class="card border-warning border-opacity-50 rounded-4 p-3 mb-4" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-sm-6">
                                <div class="small text-muted mb-1">Diện tích rèm: <strong class="text-dark fs-6" id="computedArea">7.00 m²</strong></div>
                                <div class="small text-muted">Đơn giá: <strong class="text-dark">{{ $product->formatted_effective_price }}</strong></div>
                            </div>
                            <div class="col-12 col-sm-6 text-sm-end">
                                <span class="small text-muted d-block">TỔNG THÀNH TIỀN (TẠM TÍNH):</span>
                                <h3 class="fw-extrabold text-danger mb-0" id="computedTotalPrice">2.240.000 đ</h3>
                            </div>
                        </div>
                    </div>

                    <!-- SỐ LƯỢNG & NÚT ĐẶT HÀNG -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <label class="form-label fw-bold text-dark mb-0 text-nowrap">Số Lượng Bộ:</label>
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)"><i class="bi bi-dash"></i></button>
                            <input type="number" id="inputQuantity" name="quantity" class="form-control text-center fw-bold" value="1" min="1" max="99" oninput="calculateRealtimePrice()">
                            <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    <!-- 2 Nút Thao Tác Chính -->
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-6">
                            <button type="button" class="btn btn-outline-gold w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" onclick="onAddToCartClick()">
                                <i class="bi bi-cart-plus fs-5"></i> THÊM VÀO GIỎ HÀNG
                            </button>
                        </div>
                        <div class="col-12 col-sm-6">
                            <button type="button" class="btn btn-gold w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow" onclick="onBuyNowClick()">
                                <i class="bi bi-lightning-charge-fill fs-5"></i> MUA NGAY GIÁ TỐT
                            </button>
                        </div>
                    </div>

                    <!-- Nút Hẹn Khảo Sát Tận Nhà -->
                    <a href="{{ route('consultations.create') }}" class="btn btn-light w-100 border text-muted small py-2 d-flex align-items-center justify-content-center gap-1 hover-shadow transition">
                        <i class="bi bi-rulers text-warning"></i> Bạn chưa chắc chắn kích thước? <strong class="text-dark">Đăng ký thợ đến đo tận nhà miễn phí &rarr;</strong>
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. TABS CHI TIẾT SẢN PHẨM & HƯỚNG DẪN ĐO   -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-sm rounded-4 mt-5 overflow-hidden" id="consultation-tabs">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0" id="productDetailTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold py-3 px-4 border-0" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-tab-pane" type="button">
                        <i class="bi bi-file-text me-1"></i> Mô Tả & Thông Số Chi Tiết
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold py-3 px-4 border-0" id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide-tab-pane" type="button">
                        <i class="bi bi-rulers me-1 text-warning"></i> Hướng Dẫn Tự Đo Rèm Tại Nhà
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold py-3 px-4 border-0" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy-tab-pane" type="button">
                        <i class="bi bi-shield-check me-1 text-success"></i> Chính Sách May Đo & Bảo Hành
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold py-3 px-4 border-0" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" type="button">
                        <i class="bi bi-star-fill me-1 text-warning"></i> Đánh Giá & Nhận Xét ({{ $product->reviews_count }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4 p-lg-5">
            <div class="tab-content" id="productDetailTabContent">
                <!-- Tab 1: Mô tả chi tiết -->
                <div class="tab-pane fade show active" id="desc-tab-pane">
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <h5 class="fw-bold text-dark mb-3">Giới thiệu sản phẩm {{ $product->name }}</h5>
                            <div class="text-secondary" style="line-height: 1.8;">
                                {!! nl2br(e($product->description ?? $product->short_description)) !!}
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Bảng Thông Số Kỹ Thuật</h6>
                                <table class="table table-sm table-borderless small mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Chất liệu:</td>
                                            <td class="fw-semibold text-dark">{{ $product->material ?? 'Vải dệt cao cấp' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Kiểu rèm:</td>
                                            <td class="fw-semibold text-dark">{{ $product->curtain_type ?? 'Rèm vải 2 lớp' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Phong cách:</td>
                                            <td class="fw-semibold text-dark">{{ $product->style ?? 'Hiện đại' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Khả năng cản sáng:</td>
                                            <td class="fw-semibold text-success">100% Cản sáng tuyệt đối</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Phụ kiện đi kèm:</td>
                                            <td class="fw-semibold text-dark">Thanh ray hợp kim nhôm định hình</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Hướng dẫn đo rèm -->
                <div class="tab-pane fade" id="guide-tab-pane">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-rulers text-warning me-2"></i>Hướng dẫn tự đo kích thước cửa sổ chuẩn từng milimet</h5>
                    <p class="text-muted">Có 2 phương pháp đo rèm thông dụng nhất hiện nay tùy theo cấu trúc ô cửa phòng của bạn:</p>

                    <div class="row g-4 mt-2">
                        <div class="col-12 col-md-6">
                            <div class="p-4 border rounded-4 h-100 bg-light">
                                <h6 class="fw-bold text-dark mb-2">1. Đo Lọt Lòng (Lắp trong khung cửa sổ)</h6>
                                <p class="small text-muted mb-3">Áp dụng cho cửa sổ có chiều sâu khung cửa từ 6cm trở lên (thường dùng cho rèm cuốn, rèm cầu vồng, rèm Roman).</p>
                                <ul class="small text-secondary ps-3 mb-0">
                                    <li class="mb-2"><strong>Chiều rộng rèm:</strong> Đo đúng chiều rộng mép trong khung cửa và trừ đi 1cm để rèm không bị cọ xát mép tường.</li>
                                    <li><strong>Chiều cao rèm:</strong> Đo chiều cao mép trong từ trên xuống dưới bậu cửa.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-4 border rounded-4 h-100 bg-light">
                                <h6 class="fw-bold text-dark mb-2">2. Đo Phủ Bì (Lắp phủ ngoài mép tường)</h6>
                                <p class="small text-muted mb-3">Áp dụng cho rèm vải 2 lớp phòng khách, phòng ngủ để cản sáng triệt để không bị lọt khe sáng.</p>
                                <ul class="small text-secondary ps-3 mb-0">
                                    <li class="mb-2"><strong>Chiều rộng rèm:</strong> Đo chiều rộng khung cửa cộng thêm từ 20cm - 30cm (mỗi bên phủ ra 10cm - 15cm).</li>
                                    <li><strong>Chiều cao rèm:</strong> Lắp cao hơn mép trên cửa 10cm - 15cm và thả cách mặt sàn 2cm - 3cm.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Chính sách may đo -->
                <div class="tab-pane fade" id="policy-tab-pane">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i>Chính Sách Đảm Bảo May Đo & Bảo Hành</h5>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold text-dark"><i class="bi bi-check-circle-fill text-success me-1"></i> May Đúng Kích Thước 100%</h6>
                                <p class="small text-muted mb-0">Chúng tôi cam kết sản phẩm hoàn thiện đúng kích thước mét vuông đã đặt, đường may thẳng đều đẹp mắt.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold text-dark"><i class="bi bi-tools text-warning me-1"></i> Bảo Hành Phụ Kiện 3 Năm</h6>
                                <p class="small text-muted mb-0">Đổi mới 1-1 đối với thanh ray, con lăn, dây kéo rèm nếu xảy ra lỗi do nhà sản xuất.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold text-dark"><i class="bi bi-arrow-repeat text-primary me-1"></i> Hỗ Trợ Chỉnh Sửa Chiều Dài</h6>
                                <p class="small text-muted mb-0">Hỗ trợ cắt ngắn chiều cao rèm hoàn toàn miễn phí nếu khách hàng thay đổi độ cao trần thạch cao.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Đánh Giá & Nhận Xét Của Khách Hàng -->
                <div class="tab-pane fade" id="reviews-tab-pane">
                    <div class="row g-4">
                        <!-- Rating Summary & Breakdown -->
                        <div class="col-12 col-lg-5">
                            <div class="p-4 bg-light rounded-4 border h-100">
                                <h5 class="fw-bold text-dark mb-3">Đánh Giá Tổng Quan</h5>
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="display-4 fw-extrabold text-dark mb-0">
                                        {{ number_format($product->average_rating, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-warning fs-5">
                                            {!! $product->star_rating_html !!}
                                        </div>
                                        <div class="text-muted small">
                                            Dựa trên <strong>{{ $product->reviews_count }}</strong> nhận xét thực tế
                                        </div>
                                    </div>
                                </div>

                                <!-- Rating Breakdown Bars -->
                                @php
                                    $approvedReviews = $product->approvedReviews;
                                    $totalRev = $approvedReviews->count();
                                @endphp
                                <div class="d-flex flex-column gap-2 mb-4">
                                    @for($star = 5; $star >= 1; $star--)
                                        @php
                                            $count = $approvedReviews->where('rating', $star)->count();
                                            $percent = $totalRev > 0 ? round(($count / $totalRev) * 100) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center gap-2 small">
                                            <span style="width: 50px;" class="text-muted fw-semibold">{{ $star }} sao</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span style="width: 35px;" class="text-end text-muted">{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>

                                <!-- Review Form -->
                                <div class="pt-3 border-top">
                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pencil-square text-warning me-1"></i> Viết Đánh Giá Của Bạn</h6>
                                    @auth
                                        <form method="POST" action="{{ route('reviews.store') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                                            <!-- Star Rating Picker -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-secondary mb-1">Đánh giá sản phẩm:</label>
                                                <div class="star-rating-picker d-flex gap-1 fs-4 text-secondary cursor-pointer" id="interactiveStarPicker">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi bi-star cursor-pointer star-pick" data-rating="{{ $i }}" onclick="selectRating({{ $i }})" onmouseover="hoverRating({{ $i }})" onmouseout="resetRating()"></i>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="selectedRatingInput" value="5" required>
                                                <small class="text-warning fw-semibold d-block mt-1" id="ratingLabelText">5 Sao — Cực kỳ hài lòng</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-secondary mb-1">Nhận xét chi tiết:</label>
                                                <textarea name="comment" rows="3" class="form-control rounded-3" placeholder="Chia sẻ cảm nhận của bạn về chất vải rèm, đường may, độ cản sáng và dịch vụ..." required></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-dark btn-gold w-100 rounded-pill py-2 fw-semibold">
                                                <i class="bi bi-send me-1"></i> Gửi Đánh Giá Ngay
                                            </button>
                                        </form>
                                    @else
                                        <div class="p-3 bg-white border rounded-3 text-center">
                                            <p class="small text-muted mb-2">Vui lòng đăng nhập tài khoản để gửi nhận xét và đánh giá cho mẫu rèm này.</p>
                                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Để Đánh Giá
                                            </a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Customer Reviews List -->
                        <div class="col-12 col-lg-7">
                            <h5 class="fw-bold text-dark mb-3">Nhận Xét Của Khách Hàng ({{ $product->reviews_count }})</h5>
                            
                            @forelse($product->approvedReviews as $rev)
                                <div class="p-3 border rounded-4 bg-white mb-3 shadow-none hover-shadow transition">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.85rem; border: 2px solid #b8860b;">
                                                {{ mb_substr($rev->user->name ?? 'K', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small mb-0">{{ $rev->user->name ?? 'Khách hàng' }}</div>
                                                @if($rev->order_id)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.65rem;">
                                                        <i class="bi bi-patch-check-fill me-1"></i>Đã mua hàng & may đo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock me-1"></i>{{ $rev->created_at->diffForHumans() }}
                                        </small>
                                    </div>

                                    <div class="text-warning mb-2" style="font-size: 0.85rem;">
                                        {!! $rev->star_html !!}
                                    </div>

                                    <div class="text-secondary small lh-base">
                                        "{{ $rev->comment }}"
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center bg-light rounded-4 text-muted">
                                    <i class="bi bi-chat-heart fs-1 d-block mb-2 text-warning opacity-50"></i>
                                    <h6 class="fw-bold text-dark">Chưa có đánh giá nào cho mẫu rèm này</h6>
                                    <p class="small text-muted mb-0">Hãy là người đầu tiên đặt may và chia sẻ trải nghiệm đánh giá sản phẩm nhé!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. SẢN PHẨM LIÊN QUAN CÙNG PHÂN KHÚC       -->
    <!-- ========================================== -->
    @if ($relatedProducts->isNotEmpty())
        <div class="mt-5 pt-4">
            <h4 class="fw-bold text-dark mb-4">Mẫu Rèm Cùng Phân Khúc Bạn Có Thể Thích</h4>
            <div class="row g-4">
                @foreach ($relatedProducts as $rel)
                    <div class="col-6 col-md-3">
                        <div class="product-card">
                            <div class="product-thumb-wrap">
                                @if ($rel->sale_price)
                                    <div class="badge-sale">-{{ $rel->discount_percentage }}%</div>
                                @endif
                                <img src="{{ $rel->main_image_url }}" alt="{{ $rel->name }}" class="product-thumb">
                            </div>
                            <div class="product-body">
                                <span class="product-category">{{ $rel->categories->first()->name ?? 'RÈM CỬA' }}</span>
                                <a href="{{ route('products.show', $rel->slug) }}" class="product-title">{{ $rel->name }}</a>
                                <div class="product-price-box">
                                    <span class="product-price">{{ $rel->formatted_effective_price }}</span>
                                </div>
                                <a href="{{ route('products.show', $rel->slug) }}" class="btn btn-sm btn-outline-gold mt-2 w-100">
                                    Xem mẫu
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- 5. JAVASCRIPT TÍNH TOÁN REALTIME & GALLERY -->
<!-- ========================================== -->
@push('scripts')
<script>
    // 1. Chuyển đổi ảnh Gallery
    function switchMainImage(imageUrl, thumbElement) {
        const mainDisplay = document.getElementById('mainImageDisplay');
        mainDisplay.style.opacity = '0.4';
        setTimeout(() => {
            mainDisplay.src = imageUrl;
            mainDisplay.style.opacity = '1';
        }, 150);

        document.querySelectorAll('.gallery-thumb').forEach(el => {
            el.classList.remove('border-warning', 'active-thumb');
        });
        thumbElement.classList.add('border-warning', 'active-thumb');
    }

    // 2. Chọn màu sắc rèm
    function onColorSelected(radioInput) {
        document.querySelectorAll('.color-option-card').forEach(card => {
            card.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10');
        });
        const card = radioInput.closest('.color-option-card');
        card.classList.add('border-warning', 'bg-warning', 'bg-opacity-10');

        const colorName = radioInput.getAttribute('data-color-name');
        document.getElementById('selectedColorName').innerText = 'Đã chọn: ' + colorName;
    }

    // 3. Đặt kích thước theo gợi ý nhanh
    function setDimensions(w, h) {
        document.getElementById('inputWidth').value = w;
        document.getElementById('inputHeight').value = h;
        calculateRealtimePrice();
    }

    // 4. Tăng/Giảm số lượng
    function changeQuantity(delta) {
        const qtyInput = document.getElementById('inputQuantity');
        let val = parseInt(qtyInput.value) || 1;
        val = Math.max(1, Math.min(99, val + delta));
        qtyInput.value = val;
        calculateRealtimePrice();
    }

    // 5. CÔNG THỨC TÍNH TIỀN REALTIME THEO MÉT VUÔNG (m2)
    function calculateRealtimePrice() {
        const unitPrice = parseFloat(document.getElementById('rawUnitPrice').value) || 0;
        const width = parseFloat(document.getElementById('inputWidth').value) || 0;
        const height = parseFloat(document.getElementById('inputHeight').value) || 0;
        const quantity = parseInt(document.getElementById('inputQuantity').value) || 1;

        const alertBox = document.getElementById('dimensionAlert');
        const alertText = document.getElementById('dimensionAlertText');

        // Validation kiểm tra kích thước
        if (width <= 0 || height <= 0) {
            alertBox.classList.remove('d-none');
            alertText.innerText = 'Chiều rộng và chiều cao phải lớn hơn 0 mét.';
            document.getElementById('computedTotalPrice').innerText = '0 đ';
            return;
        }

        const minW = parseFloat({{ $product->min_width ?? 0.5 }});
        const maxW = parseFloat({{ $product->max_width ?? 10.0 }});
        const minH = parseFloat({{ $product->min_height ?? 0.5 }});
        const maxH = parseFloat({{ $product->max_height ?? 10.0 }});

        if (width < minW || width > maxW || height < minH || height > maxH) {
            alertBox.classList.remove('d-none');
            alertText.innerText = `Kích thước vượt giới hạn xưởng may (Rộng: ${minW}m - ${maxW}m, Cao: ${minH}m - ${maxH}m).`;
        } else {
            alertBox.classList.add('d-none');
        }

        // Tính Diện tích (m²) = Width × Height
        const area = Math.round((width * height) * 100) / 100;
        document.getElementById('computedArea').innerText = area.toFixed(2) + ' m²';

        // Tính Tổng tiền = Diện tích × Đơn giá/m² × Số lượng
        const total = Math.round(area * unitPrice * quantity);
        document.getElementById('computedTotalPrice').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    // 6. Xử lý nút Thêm vào giỏ & Mua ngay
    function onAddToCartClick() {
        const selectedRadio = document.querySelector('.color-radio:checked');
        if (!selectedRadio) {
            alert('Vui lòng chọn màu sắc rèm trước khi thêm vào giỏ hàng!');
            return;
        }

        const width = parseFloat(document.getElementById('inputWidth').value) || 0;
        const height = parseFloat(document.getElementById('inputHeight').value) || 0;

        if (width <= 0 || height <= 0) {
            alert('Vui lòng nhập chiều rộng và chiều cao hợp lệ.');
            return;
        }

        document.getElementById('formActionType').value = 'add_to_cart';
        document.getElementById('curtainOrderForm').submit();
    }

    function onBuyNowClick() {
        const selectedRadio = document.querySelector('.color-radio:checked');
        if (!selectedRadio) {
            alert('Vui lòng chọn màu sắc rèm trước khi mua!');
            return;
        }

        const width = parseFloat(document.getElementById('inputWidth').value) || 0;
        const height = parseFloat(document.getElementById('inputHeight').value) || 0;

        if (width <= 0 || height <= 0) {
            alert('Vui lòng nhập chiều rộng và chiều cao hợp lệ.');
            return;
        }

        document.getElementById('formActionType').value = 'buy_now';
        document.getElementById('curtainOrderForm').submit();
    }

    // 7. Xử lý chọn số sao đánh giá sản phẩm (Star Picker)
    const ratingLabels = {
        1: '1 Sao — Rất không hài lòng',
        2: '2 Sao — Chưa hài lòng',
        3: '3 Sao — Bình thường',
        4: '4 Sao — Hài lòng',
        5: '5 Sao — Cực kỳ hài lòng'
    };
    let currentSelectedRating = 5;

    function renderStarPicker(val) {
        document.querySelectorAll('#interactiveStarPicker .star-pick').forEach((star, idx) => {
            const starValue = idx + 1;
            if (starValue <= val) {
                star.classList.remove('bi-star', 'text-secondary');
                star.classList.add('bi-star-fill', 'text-warning');
            } else {
                star.classList.remove('bi-star-fill', 'text-warning');
                star.classList.add('bi-star', 'text-secondary');
            }
        });
        const labelEl = document.getElementById('ratingLabelText');
        if (labelEl) {
            labelEl.innerText = ratingLabels[val] || '';
        }
    }

    function selectRating(val) {
        currentSelectedRating = val;
        const inputEl = document.getElementById('selectedRatingInput');
        if (inputEl) inputEl.value = val;
        renderStarPicker(val);
    }

    function hoverRating(val) {
        renderStarPicker(val);
    }

    function resetRating() {
        renderStarPicker(currentSelectedRating);
    }

    // Khởi tạo tính toán ban đầu khi load trang
    document.addEventListener('DOMContentLoaded', function () {
        const firstRadio = document.querySelector('.color-radio:checked');
        if (firstRadio) {
            onColorSelected(firstRadio);
        }
        calculateRealtimePrice();
        renderStarPicker(5);
    });
</script>
@endpush
@endsection
