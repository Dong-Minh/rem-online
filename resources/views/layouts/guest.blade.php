<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ $title ?? 'Tài khoản' }} — {{ config('app.name', 'Rèm Online') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #b8860b;
            --primary-gold-dark: #8c6508;
            --primary-gold-light: #fef8ee;
            --dark-navy: #1a2232;
            --bg-soft: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 50%, #e2e8f0 100%);
            min-height: 100vh;
            color: #334155;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 1px 1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
            transition: all 0.3s ease;
        }

        .auth-header {
            text-align: center;
            padding: 2.25rem 2rem 1.25rem;
            background: linear-gradient(180deg, #ffffff 0%, var(--primary-gold-light) 100%);
            border-bottom: 1px solid #f1f5f9;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--primary-gold-dark) 100%);
            color: #ffffff;
            font-size: 1.6rem;
            box-shadow: 0 8px 16px -4px rgba(184, 134, 11, 0.35);
            margin-bottom: 1rem;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1.5px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.12);
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--primary-gold-dark) 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 0.65rem;
            border: none;
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.25);
            transition: all 0.25s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #a67909 0%, #765406 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(184, 134, 11, 0.35);
        }

        .auth-footer {
            padding: 1.25rem 2rem;
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.9rem;
        }

        .auth-link {
            color: var(--primary-gold-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link:hover {
            color: var(--primary-gold);
            text-decoration: underline;
        }

        .alert {
            border-radius: 0.65rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Brand Header -->
            <div class="auth-header">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <div class="brand-badge">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--dark-navy);">RÈM ONLINE</h4>
                    <p class="text-muted small mb-0">Thương Hiệu Rèm & Nội Thất Cao Cấp</p>
                </a>
            </div>

            <!-- Main Auth Content -->
            <div class="auth-body">
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>{{ session('info') }}</div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ session('warning') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                {{ $slot }}
            </div>

            <!-- Footer helper -->
            @hasSection('auth_footer')
                <div class="auth-footer">
                    @yield('auth_footer')
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
