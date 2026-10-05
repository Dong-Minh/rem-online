<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Rèm Online') — Rèm Cửa & Nội Thất Cao Cấp</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #b8860b;
            --primary-gold-dark: #8c6508;
            --primary-gold-light: #fdfaf2;
            --dark-navy: #1a2232;
            --text-main: #334155;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fbfbfc;
            color: var(--text-main);
        }

        /* Top Bar */
        .top-bar {
            background-color: var(--dark-navy);
            color: #94a3b8;
            font-size: 0.82rem;
            padding: 0.45rem 0;
        }

        /* Navbar */
        .main-navbar {
            background: #ffffff;
            box-shadow: 0 4px 20px -10px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.45rem;
            color: var(--dark-navy) !important;
            letter-spacing: -0.02em;
        }

        .navbar-brand i {
            color: var(--primary-gold);
        }

        .nav-link {
            font-weight: 600;
            font-size: 0.93rem;
            color: #475569 !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-gold) !important;
        }

        /* Search input */
        .header-search .form-control {
            border-radius: 2rem 0 0 2rem;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            padding-left: 1.25rem;
            font-size: 0.9rem;
        }

        .header-search .btn {
            border-radius: 0 2rem 2rem 0;
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
            color: #fff;
            padding-right: 1.25rem;
        }

        /* Buttons */
        .btn-gold {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--primary-gold-dark) 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 0.6rem;
            padding: 0.6rem 1.25rem;
            transition: all 0.25s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #a67909 0%, #765406 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.25);
        }

        .btn-outline-gold {
            border: 1.5px solid var(--primary-gold);
            color: var(--primary-gold-dark);
            font-weight: 600;
            border-radius: 0.6rem;
        }

        .btn-outline-gold:hover {
            background-color: var(--primary-gold);
            color: #ffffff;
        }

        /* Product Card */
        .product-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.1);
            border-color: #e2e8f0;
        }

        .product-thumb-wrap {
            position: relative;
            padding-top: 85%;
            overflow: hidden;
            background-color: #f8fafc;
        }

        .product-thumb {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-thumb {
            transform: scale(1.06);
        }

        .badge-sale {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            background: #ef4444;
            color: #fff;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            border-radius: 0.4rem;
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
            z-index: 2;
        }

        .badge-tag {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            z-index: 2;
        }

        .product-body {
            padding: 1.15rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-category {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .product-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--dark-navy);
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.7rem;
            margin-bottom: 0.5rem;
        }

        .product-title:hover {
            color: var(--primary-gold);
        }

        .product-price-box {
            margin-top: auto;
            padding-top: 0.5rem;
        }

        .product-price {
            font-size: 1.15rem;
            font-weight: 800;
            color: #dc2626;
        }

        .product-old-price {
            font-size: 0.85rem;
            color: #94a3b8;
            text-decoration: line-through;
            margin-left: 0.35rem;
        }

        /* Footer */
        .main-footer {
            background-color: var(--dark-navy);
            color: #94a3b8;
            padding-top: 4rem;
            padding-bottom: 2rem;
            margin-top: 5rem;
        }

        .footer-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 1.25rem;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            display: block;
            margin-bottom: 0.6rem;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: var(--primary-gold);
            padding-left: 4px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- 1. Top Bar -->
    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-geo-alt me-1 text-warning"></i> Hà Nội & TP. Hồ Chí Minh • Khảo sát & Đo đạc tận nhà Miễn Phí
            </div>
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-telephone-fill me-1 text-warning"></i> Hotline: <strong>0901.234.567</strong></span>
                <span>•</span>
                <a href="{{ route('consultations.create') }}" class="text-white text-decoration-none fw-semibold">
                    <i class="bi bi-calendar2-check me-1 text-warning"></i> Đăng Ký Đo Tận Nhà
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Main Navbar -->
    <nav class="navbar navbar-expand-lg main-navbar py-3">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <div class="bg-warning bg-opacity-25 rounded p-2 text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-shop fs-5"></i>
                </div>
                <span>RÈM ONLINE</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="bi bi-list fs-2"></i>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Search bar in Header -->
                <form class="d-flex header-search my-2 my-lg-0 mx-lg-4 flex-grow-1" action="{{ route('products.index') }}" method="GET" style="max-width: 420px;">
                    <input class="form-control" type="search" name="search" placeholder="Tìm loại rèm, chất liệu, phong cách..." value="{{ request('search') }}">
                    <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                </form>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Trang Chủ</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Danh Mục Rèm
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                            <li><a class="dropdown-item py-2" href="{{ route('products.index') }}"><strong>Tất Cả Sản Phẩm Rèm</strong></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index', ['category' => 'rem-vai-cao-cap']) }}">Rèm Vải Cao Cấp</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index', ['category' => 'rem-phong-khach']) }}">Rèm Phòng Khách</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index', ['category' => 'rem-phong-ngu']) }}">Rèm Phòng Ngủ</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index', ['category' => 'rem-cau-vong-han-quoc']) }}">Rèm Cầu Vồng Hàn Quốc</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index', ['category' => 'rem-cuon-van-phong']) }}">Rèm Cuốn Văn Phòng</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index', ['category' => 'rem-chong-nang-cach-nhiet']) }}">Rèm Chống Nắng Cách Nhiệt</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Cửa Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('consultations.*') ? 'active text-gold fw-bold' : '' }}" href="{{ route('consultations.create') }}">
                            <i class="bi bi-rulers me-1 text-warning"></i> Đo Tận Nhà
                        </a>
                    </li>

                    <!-- User Account / Auth -->
                    @auth
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ Str::limit(auth()->user()->name, 15) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                                    <li><a class="dropdown-item py-2 text-warning fw-bold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Quản trị Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2 text-muted"></i> Tổng quan tài khoản</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="bi bi-bag-check me-2 text-muted"></i> Đơn hàng của tôi</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('profile.addresses.index') }}"><i class="bi bi-geo-alt me-2 text-muted"></i> Sổ địa chỉ nhận hàng</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear me-2 text-muted"></i> Cài đặt tài khoản</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-gold me-2">Đăng Nhập</a>
                            <a href="{{ route('register') }}" class="btn btn-gold">Đăng Ký</a>
                        </li>
                    @endauth

                    <!-- Cart Icon Button -->
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('cart.index') }}" class="btn btn-light position-relative rounded-circle p-2 border" title="Xem giỏ hàng">
                            <i class="bi bi-bag fs-5 text-dark"></i>
                            @php
                                $cartItemCount = app(\App\Services\CartService::class)->getItemCount();
                            @endphp
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ $cartItemCount }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Slot -->
    <main>
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-shop text-warning"></i> RÈM ONLINE
                    </h5>
                    <p class="small text-muted">
                        Thương hiệu chuyên cung cấp, tư vấn thiết kế và may đo rèm cửa cao cấp theo kích thước thực tế cho mọi công trình chung cư, nhà phố, biệt thự, văn phòng.
                    </p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <h6 class="footer-title">DANH MỤC</h6>
                    <a href="{{ route('products.index', ['category' => 'rem-vai-cao-cap']) }}" class="footer-link">Rèm Vải Cao Cấp</a>
                    <a href="{{ route('products.index', ['category' => 'rem-phong-khach']) }}" class="footer-link">Rèm Phòng Khách</a>
                    <a href="{{ route('products.index', ['category' => 'rem-phong-ngu']) }}" class="footer-link">Rèm Phòng Ngủ</a>
                    <a href="{{ route('products.index', ['category' => 'rem-cau-vong-han-quoc']) }}" class="footer-link">Rèm Cầu Vồng</a>
                    <a href="{{ route('products.index', ['category' => 'rem-cuon-van-phong']) }}" class="footer-link">Rèm Cuốn Văn Phòng</a>
                </div>

                <div class="col-6 col-md-3">
                    <h6 class="footer-title">CHÍNH SÁCH & HỖ TRỢ</h6>
                    <a href="#" class="footer-link">Quy trình đo đạc tận nhà</a>
                    <a href="#" class="footer-link">Cách tính diện tích & giá rèm</a>
                    <a href="#" class="footer-link">Chính sách bảo hành 2 năm</a>
                    <a href="#" class="footer-link">Chính sách vận chuyển & lắp đặt</a>
                    <a href="#" class="footer-link">Bảo mật thông tin khách hàng</a>
                </div>

                <div class="col-12 col-md-3">
                    <h6 class="footer-title">LIÊN HỆ KHẢO SÁT</h6>
                    <p class="small mb-2"><i class="bi bi-geo-alt text-warning me-2"></i> Hà Nội: 123 Hoàng Quốc Việt, Cầu Giấy</p>
                    <p class="small mb-2"><i class="bi bi-geo-alt text-warning me-2"></i> TP.HCM: 456 Nguyễn Thị Thập, Quận 7</p>
                    <p class="small mb-2"><i class="bi bi-telephone text-warning me-2"></i> Hotline: 0901.234.567</p>
                    <p class="small mb-0"><i class="bi bi-envelope text-warning me-2"></i> Email: support@remonline.vn</p>
                </div>
            </div>

            <div class="border-top border-secondary border-opacity-25 mt-4 pt-4 text-center small text-muted">
                &copy; {{ date('Y') }} RÈM ONLINE — Đồ Án Website Bán & Quản Lý Cửa Hàng Rèm Cửa (Laravel). Tất cả quyền được bảo lưu.
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- LIVECHAT FLOATING WIDGET (TƯ VẤN TRỰC TUYẾN) -->
    <!-- ========================================== -->
    <div id="client-chat-widget" style="position: fixed; bottom: 25px; right: 25px; z-index: 1050;">
        <!-- Nút mở chat tròn nổi bật -->
        <button id="chat-toggle" class="btn shadow-lg d-flex align-items-center gap-2 px-3 py-2 rounded-pill text-white border-0" 
                style="background: linear-gradient(135deg, #1a2232 0%, #2a3b5c 100%); border: 2px solid #d4af37; box-shadow: 0 8px 25px rgba(26,34,50,0.35);">
            <div class="position-relative d-inline-block">
                <i class="bi bi-chat-dots-fill text-warning fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </div>
            <span class="fw-bold small">Tư Vấn Trực Tuyến</span>
        </button>

        <!-- Khung chat popup -->
        <div id="chat-popup" class="card shadow-2xl border-0 rounded-4 overflow-hidden" 
             style="display: none; width: 360px; max-width: calc(100vw - 30px); height: 500px; box-shadow: 0 15px 35px rgba(0,0,0,0.25);">
            <!-- Header -->
            <div class="card-header py-3 px-3 d-flex justify-content-between align-items-center text-white border-0" 
                 style="background: linear-gradient(135deg, #1a2232 0%, #111723 100%); border-bottom: 2px solid #b8860b;">
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-dark fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                            <i class="bi bi-headset"></i>
                        </div>
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold small text-white">Tư Vấn Rèm Online</h6>
                        <small class="text-warning" style="font-size: 0.72rem;"><i class="bi bi-dot"></i>Hỗ trợ may đo 24/7</small>
                    </div>
                </div>
                <button id="chat-close" type="button" class="btn btn-sm btn-outline-light rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Body Message List -->
            <div id="chat-messages" class="card-body p-3 overflow-auto bg-light d-flex flex-column gap-2" style="flex: 1; font-size: 0.85rem;">
                @auth
                    <div class="text-center text-muted my-auto" id="chat-loading-placeholder">
                        <div class="spinner-border spinner-border-sm text-warning mb-2" role="status"></div>
                        <p class="small mb-0">Đang tải lịch sử tư vấn...</p>
                    </div>
                @else
                    <div class="text-center my-auto p-3 bg-white rounded-3 shadow-sm border">
                        <i class="bi bi-person-lock text-warning fs-1 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark mb-1">Đăng nhập để chat</h6>
                        <p class="small text-muted mb-3">Vui lòng đăng nhập tài khoản để được nhân viên tư vấn may đo rèm theo kích thước và gửi mẫu vải trực tiếp.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-gold btn-sm fw-bold">Đăng Nhập Ngay</a>
                            <a href="{{ route('consultations.create') }}" class="btn btn-outline-dark btn-sm">Đặt Lịch Đo Tại Nhà</a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Footer Chat Input -->
            @auth
                <div class="card-footer bg-white p-2 border-top">
                    <form id="chat-form" onsubmit="event.preventDefault(); sendMessage();" class="m-0">
                        <div class="input-group">
                            <input type="text" id="chat-input" class="form-control form-control-sm rounded-start-pill border-end-0 ps-3" 
                                   placeholder="Nhập câu hỏi kích thước, loại vải..." autocomplete="off">
                            <button id="send-btn" class="btn btn-warning rounded-end-pill px-3 fw-bold text-dark" type="submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @auth
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("chat-toggle");
        const chatPopup = document.getElementById("chat-popup");
        const closeBtn = document.getElementById("chat-close");
        const sendBtn = document.getElementById("send-btn");
        const input = document.getElementById("chat-input");
        const chatBox = document.getElementById("chat-messages");
        const currentUserId = "{{ Auth::id() }}";

        if (!toggleBtn || !chatPopup) return;

        // Mở/Đóng popup
        toggleBtn.onclick = () => {
            chatPopup.style.display = "block";
            toggleBtn.style.display = "none";
            loadMessages();
            if (input) input.focus();
        };

        if (closeBtn) {
            closeBtn.onclick = () => {
                chatPopup.style.display = "none";
                toggleBtn.style.display = "flex";
            };
        }

        // Tải lịch sử tin nhắn
        window.loadMessages = function () {
            fetch("{{ route('user.chat.messages') }}")
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if (!messages || messages.length === 0) {
                        html = `
                            <div class="text-center text-muted my-auto p-3">
                                <div class="bg-white p-3 rounded-3 shadow-sm border mb-2">
                                    <i class="bi bi-chat-heart text-warning fs-3 mb-1 d-block"></i>
                                    <span class="fw-bold text-dark d-block">Xin chào {{ Auth::user()->name }}!</span>
                                    <small class="text-muted">Bạn cần tư vấn mẫu rèm vải, rèm cuốn, rèm cầu vồng hay cần thợ mang mẫu đến đo đạc tận nhà? Hãy nhắn tin ngay nhé!</small>
                                </div>
                            </div>
                        `;
                    } else {
                        messages.forEach(msg => {
                            const isMe = (msg.sender_id == currentUserId);
                            const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : '';
                            if (isMe) {
                                html += `
                                    <div class="d-flex flex-column align-items-end mb-2">
                                        <div class="p-2 px-3 rounded-4 shadow-sm text-white" 
                                             style="background: linear-gradient(135deg, #1a2232 0%, #2a3b5c 100%); border-bottom-right-radius: 4px !important; max-width: 82%; word-break: break-word;">
                                            ${escapeHtml(msg.content)}
                                        </div>
                                        <small class="text-muted mt-1" style="font-size: 0.68rem;">${timeStr} • Bạn</small>
                                    </div>
                                `;
                            } else {
                                html += `
                                    <div class="d-flex flex-column align-items-start mb-2">
                                        <div class="d-flex align-items-start gap-1">
                                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold mt-1" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                QTV
                                            </div>
                                            <div class="p-2 px-3 rounded-4 shadow-sm text-dark bg-white border" 
                                                 style="border-bottom-left-radius: 4px !important; max-width: 82%; word-break: break-word;">
                                                ${escapeHtml(msg.content)}
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1 ps-4" style="font-size: 0.68rem;">${timeStr} • Ban Tư Vấn Rèm</small>
                                    </div>
                                `;
                            }
                        });
                    }
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        };

        // Gửi tin nhắn
        window.sendMessage = function () {
            if (!input) return;
            let message = input.value.trim();
            if (message === "") return;

            input.disabled = true;
            if (sendBtn) sendBtn.disabled = true;

            fetch("{{ route('user.chat.send') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                input.value = "";
                input.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
                input.focus();
                loadMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin:", err);
                input.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
            });
        };

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Tự động polling cập nhật mỗi 3 giây khi mở chat
        setInterval(() => {
            if (chatPopup.style.display === "block") {
                loadMessages();
            }
        }, 3000);
    });
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
