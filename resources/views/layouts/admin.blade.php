<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản Trị Hệ Thống') — {{ config('app.name', 'Rèm Online') }}</title>

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
            --sidebar-bg: #1a2232;
            --sidebar-hover: #26334a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            min-height: 100vh;
        }

        #admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        #admin-sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
        }

        .sidebar-brand i {
            color: var(--primary-gold);
            font-size: 1.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0.75rem;
            margin: 0;
            flex-grow: 1;
        }

        .menu-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 0.75rem 0.75rem 0.25rem;
            font-weight: 700;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.85rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .menu-item a:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .menu-item.active a {
            color: #ffffff;
            background-color: var(--primary-gold);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.3);
        }

        /* Content Area */
        #admin-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .admin-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-main {
            padding: 1.75rem;
            flex-grow: 1;
        }

        .card-custom {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .btn-gold {
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
            color: #ffffff;
            font-weight: 600;
        }

        .btn-gold:hover {
            background-color: var(--primary-gold-dark);
            border-color: var(--primary-gold-dark);
            color: #ffffff;
        }

        /* Pagination Styling & SVG Protection */
        .pagination {
            margin-bottom: 0;
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .pagination svg, nav svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            display: inline-block;
        }
        .page-item .page-link {
            border-radius: 6px;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-item.active .page-link {
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
            color: #ffffff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div id="admin-wrapper">
        <!-- Sidebar -->
        <aside id="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-shop"></i>
                <span>RÈM ONLINE <small class="text-warning d-block fs-6 font-monospace">Admin Portal</small></span>
            </a>

            <ul class="sidebar-menu">
                <li class="menu-header">Tổng Quan</li>
                <li class="menu-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Bảng Điều Khiển</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.analytics.index') }}">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Báo Cáo & Thống Kê</span>
                    </a>
                </li>

                <li class="menu-header">Quản Lý Sản Phẩm</li>
                <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-folder2-open"></i>
                        <span>Danh Mục Rèm</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Sản Phẩm Rèm</span>
                    </a>
                </li>

                <li class="menu-header">Tài Chính & Thanh Toán</li>
                <li class="menu-item {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.index') }}">
                        <i class="bi bi-wallet2"></i>
                        <span>Thống Kê Tài Chính</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.transactions') }}">
                        <i class="bi bi-credit-card-2-front"></i>
                        <span>Giao Dịch Thanh Toán</span>
                    </a>
                </li>

                <li class="menu-header">Khuyến Mãi & Bán Hàng</li>
                <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}" class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-cart-check"></i>
                            <span>Quản Lý Đơn Hàng</span>
                        </div>
                        @php
                            $sidebarPendingOrders = \App\Models\Order::where('status', 'pending')->count();
                        @endphp
                        @if($sidebarPendingOrders > 0)
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">{{ $sidebarPendingOrders }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.vouchers.index') }}">
                        <i class="bi bi-ticket-perforated"></i>
                        <span>Mã Giảm Giá (Voucher)</span>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.consultations.index') }}" class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-check"></i>
                            <span>Lịch Hẹn Khảo Sát</span>
                        </div>
                        @php
                            $pendingConsultationsCount = \App\Models\Consultation::where('status', 'pending')->count();
                        @endphp
                        @if($pendingConsultationsCount > 0)
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">{{ $pendingConsultationsCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="menu-header">Hệ Thống & Khách Hàng</li>
                <li class="menu-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews.index') }}" class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-star-half"></i>
                            <span>Đánh Giá Sản Phẩm</span>
                        </div>
                        @php
                            $pendingReviewsCount = \App\Models\Review::where('is_approved', false)->count();
                        @endphp
                        @if($pendingReviewsCount > 0)
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">{{ $pendingReviewsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ url('/') }}" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Xem Website Ngoài</span>
                    </a>
                </li>
            </ul>

            <div class="p-3 border-top border-secondary border-opacity-25 text-center small">
                <span class="badge bg-warning text-dark px-2 py-1">{{ auth()->user()->role ?? 'Admin' }}</span>
                <div class="text-white mt-1 fw-semibold">{{ auth()->user()->name ?? 'Administrator' }}</div>
            </div>
        </aside>

        <!-- Main Content -->
        <div id="admin-content">
            <!-- Top Navbar -->
            <header class="admin-navbar">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Bảng Điều Khiển')</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 text-secondary"></i>
                            <span class="fw-semibold">{{ auth()->user()->name ?? 'Quản Trị Viên' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="admin-main">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LIVECHAT ADMIN FLOATING WIDGET             -->
    <!-- ========================================== -->
    <div id="admin-chat-widget" style="position: fixed; bottom: 25px; right: 25px; z-index: 1060;">
        <!-- Nút toggle mở chat admin -->
        <button id="admin-chat-toggle" class="btn shadow-lg d-flex align-items-center gap-2 px-3 py-2 rounded-pill text-white border-0" 
                style="background: linear-gradient(135deg, #1a2232 0%, #2a3b5c 100%); border: 2px solid #b8860b; box-shadow: 0 8px 25px rgba(26,34,50,0.35);">
            <i class="bi bi-chat-left-text-fill text-warning fs-5"></i>
            <span class="fw-bold small">Chat Khách Hàng</span>
            <span id="admin-total-unread" class="badge bg-danger rounded-pill" style="display: none;">0</span>
        </button>

        <!-- Khung chat popup chia 2 cột -->
        <div id="admin-chat-popup" class="card shadow-2xl border-0 rounded-4 overflow-hidden" 
             style="display: none; width: 680px; max-width: calc(100vw - 30px); height: 530px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
            
            <!-- Header popup -->
            <div class="card-header py-3 px-3 d-flex justify-content-between align-items-center text-white border-0" 
                 style="background: linear-gradient(135deg, #1a2232 0%, #111723 100%); border-bottom: 2px solid #b8860b;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill text-warning fs-5"></i>
                    <h6 class="mb-0 fw-bold small text-white">Trung Tâm Hỗ Trợ & Livechat Khách Hàng</h6>
                </div>
                <button id="admin-chat-close" type="button" class="btn btn-sm btn-outline-light rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Body: 2 cột (Danh sách User bên trái & Cửa sổ Chat bên phải) -->
            <div class="d-flex" style="height: calc(100% - 56px);">
                <!-- Cột 1: Danh sách User -->
                <div class="border-end bg-light d-flex flex-column" style="width: 240px; min-width: 220px;">
                    <div class="p-2 border-bottom bg-white">
                        <small class="fw-bold text-muted text-uppercase" style="font-size: 0.72rem;">
                            <i class="bi bi-people-fill text-warning me-1"></i>Hội Thoại Khách Hàng
                        </small>
                    </div>
                    <div id="admin-user-list" class="overflow-auto flex-grow-1 p-1 divide-y" style="font-size: 0.82rem;">
                        <div class="p-3 text-center text-muted">
                            <div class="spinner-border spinner-border-sm text-warning mb-1" role="status"></div>
                            <small class="d-block">Đang tải danh sách...</small>
                        </div>
                    </div>
                </div>

                <!-- Cột 2: Khung Chat -->
                <div class="flex-grow-1 d-flex flex-column bg-white">
                    <!-- Selected user header -->
                    <div id="admin-chat-header" class="p-2 px-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <span id="admin-current-user-name" class="fw-bold text-dark small d-block">Chưa chọn khách hàng</span>
                                <small id="admin-current-user-sub" class="text-muted" style="font-size: 0.7rem;">Chọn từ danh sách bên trái để phản hồi</small>
                            </div>
                        </div>
                    </div>

                    <!-- Messages stream -->
                    <div id="admin-chat-messages" class="p-3 overflow-auto flex-grow-1 d-flex flex-column gap-2" style="font-size: 0.85rem; background-color: #f8fafc;">
                        <div class="text-center text-muted my-auto">
                            <i class="bi bi-chat-square-quote fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                            <p class="small mb-0">Vui lòng chọn một khách hàng bên trái để xem tin nhắn và gửi phản hồi.</p>
                        </div>
                    </div>

                    <!-- Footer input -->
                    <div class="p-2 border-top bg-white">
                        <form id="admin-chat-form" onsubmit="event.preventDefault(); adminSendMessage();" class="m-0">
                            <div class="input-group">
                                <input type="text" id="admin-chat-input" class="form-control form-control-sm rounded-start-pill ps-3" 
                                       placeholder="Nhập câu trả lời tư vấn..." autocomplete="off" disabled>
                                <button id="admin-send-btn" class="btn btn-warning rounded-end-pill px-3 fw-bold text-dark" type="submit" disabled>
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentChatUserId = null;
        let currentChatUserName = "";

        const toggleBtn = document.getElementById("admin-chat-toggle");
        const popup = document.getElementById("admin-chat-popup");
        const closeBtn = document.getElementById("admin-chat-close");
        const userListContainer = document.getElementById("admin-user-list");
        const messagesContainer = document.getElementById("admin-chat-messages");
        const chatInput = document.getElementById("admin-chat-input");
        const sendBtn = document.getElementById("admin-send-btn");
        const currentUserNameEl = document.getElementById("admin-current-user-name");
        const currentUserSubEl = document.getElementById("admin-current-user-sub");
        const totalUnreadBadge = document.getElementById("admin-total-unread");

        const adminId = "{{ Auth::id() }}";

        if (!toggleBtn || !popup) return;

        toggleBtn.onclick = () => {
            popup.style.display = "block";
            toggleBtn.style.display = "none";
            loadAdminUsers();
        };

        closeBtn.onclick = () => {
            popup.style.display = "none";
            toggleBtn.style.display = "flex";
        };

        // 1. Tải danh sách User đã từng nhắn tin
        window.loadAdminUsers = function () {
            fetch("{{ route('admin.chat.users') }}")
                .then(res => res.json())
                .then(users => {
                    let totalUnread = 0;
                    if (!users || users.length === 0) {
                        userListContainer.innerHTML = '<div class="p-3 text-center text-muted small">Chưa có cuộc trò chuyện nào.</div>';
                        return;
                    }

                    let html = "";
                    users.forEach(user => {
                        totalUnread += (user.unread_count || 0);
                        const activeClass = (currentChatUserId == user.id) ? 'bg-warning bg-opacity-25 border-start border-3 border-warning' : 'bg-white';
                        const unreadBadge = (user.unread_count > 0) ? `<span class="badge bg-danger rounded-pill">${user.unread_count}</span>` : '';
                        const snippet = user.last_message ? escapeHtml(user.last_message.substring(0, 22) + (user.last_message.length > 22 ? '...' : '')) : 'Đoạn hội thoại';

                        html += `
                            <div class="p-2 mb-1 rounded-2 cursor-pointer transition-all ${activeClass}" 
                                 style="cursor: pointer;"
                                 onclick="selectChatUser(${user.id}, '${escapeHtml(user.name)}')">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark small text-truncate" style="max-width: 130px;">${escapeHtml(user.name)}</strong>
                                    ${unreadBadge}
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.72rem;">
                                    <span class="text-truncate" style="max-width: 140px;">${snippet}</span>
                                    <span>${user.last_message_time || ''}</span>
                                </div>
                            </div>
                        `;
                    });

                    userListContainer.innerHTML = html;

                    if (totalUnread > 0) {
                        totalUnreadBadge.innerText = totalUnread;
                        totalUnreadBadge.style.display = "inline-block";
                    } else {
                        totalUnreadBadge.style.display = "none";
                    }
                })
                .catch(err => console.error("Lỗi tải danh sách người dùng chat:", err));
        };

        // 2. Chọn một User để chat
        window.selectChatUser = function (userId, userName) {
            currentChatUserId = userId;
            currentChatUserName = userName;

            currentUserNameEl.innerText = userName;
            currentUserSubEl.innerText = 'Đang trao đổi trực tuyến • ID #' + userId;

            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();

            loadAdminMessages();
            loadAdminUsers();
        };

        // 3. Tải tin nhắn của User đang chọn
        window.loadAdminMessages = function () {
            if (!currentChatUserId) return;

            fetch(`/admin/chat/messages/${currentChatUserId}`)
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if (!messages || messages.length === 0) {
                        html = `
                            <div class="text-center text-muted my-auto p-3">
                                <small>Chưa có tin nhắn nào trong hội thoại này.</small>
                            </div>
                        `;
                    } else {
                        messages.forEach(msg => {
                            const isMe = (msg.sender_id == adminId);
                            const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : '';
                            if (isMe) {
                                html += `
                                    <div class="d-flex flex-column align-items-end mb-2">
                                        <div class="p-2 px-3 rounded-4 shadow-sm text-white" 
                                             style="background: linear-gradient(135deg, #1a2232 0%, #2a3b5c 100%); border-bottom-right-radius: 4px !important; max-width: 80%; word-break: break-word;">
                                            ${escapeHtml(msg.content)}
                                        </div>
                                        <small class="text-muted mt-1" style="font-size: 0.68rem;">${timeStr} • Bạn (QTV)</small>
                                    </div>
                                `;
                            } else {
                                html += `
                                    <div class="d-flex flex-column align-items-start mb-2">
                                        <div class="d-flex align-items-start gap-1">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold mt-1" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                KH
                                            </div>
                                            <div class="p-2 px-3 rounded-4 shadow-sm text-dark bg-white border" 
                                                 style="border-bottom-left-radius: 4px !important; max-width: 80%; word-break: break-word;">
                                                ${escapeHtml(msg.content)}
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1 ps-4" style="font-size: 0.68rem;">${timeStr} • ${escapeHtml(currentChatUserName)}</small>
                                    </div>
                                `;
                            }
                        });
                    }

                    messagesContainer.innerHTML = html;
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                })
                .catch(err => console.error("Lỗi tải tin nhắn Admin:", err));
        };

        // 4. Admin gửi tin nhắn
        window.adminSendMessage = function () {
            if (!currentChatUserId) return;
            const msg = chatInput.value.trim();
            if (!msg) return;

            chatInput.disabled = true;
            sendBtn.disabled = true;

            fetch("{{ route('admin.chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    user_id: currentChatUserId,
                    message: msg
                })
            })
            .then(res => res.json())
            .then(data => {
                chatInput.value = "";
                chatInput.disabled = false;
                sendBtn.disabled = false;
                chatInput.focus();
                loadAdminMessages();
                loadAdminUsers();
            })
            .catch(err => {
                console.error("Lỗi gửi tin Admin:", err);
                chatInput.disabled = false;
                sendBtn.disabled = false;
            });
        };

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // 5. Polling tự động mỗi 3 giây khi popup đang mở
        setInterval(() => {
            if (popup.style.display === "block") {
                loadAdminMessages();
                loadAdminUsers();
            }
        }, 3000);
    });
    </script>

    @stack('scripts')
</body>
</html>
