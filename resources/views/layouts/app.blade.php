<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nota Kontan') &mdash; Sistem Pencatatan Transaksi</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3 & Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #0f172a;
            --brand-accent: #2563eb;
            --brand-surface: #ffffff;
            --brand-bg: #f8fafc;
            --brand-border: #e2e8f0;
            --brand-text-main: #0f172a;
            --brand-text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--brand-bg);
            color: var(--brand-text-main);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styling */
        .app-navbar {
            background-color: #0f172a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .navbar-brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: -0.5px;
            margin-right: 10px;
        }

        .navbar-brand-title {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            line-height: 1.2;
        }

        .navbar-brand-subtitle {
            font-size: 0.72rem;
            font-weight: 500;
            color: #94a3b8;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .nav-link-custom {
            color: #94a3b8 !important;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
        }

        .nav-link-custom:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.06);
        }

        .nav-link-custom.active {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.12);
            font-weight: 600;
        }

        /* Card & Panel Styling */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
        }

        /* Action Buttons */
        .btn-brand-primary {
            background-color: #0f172a;
            color: #ffffff;
            border: 1px solid #0f172a;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.15s ease-in-out;
        }
        .btn-brand-primary:hover {
            background-color: #1e293b;
            color: #ffffff;
            border-color: #1e293b;
        }

        .btn-brand-accent {
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.15s ease-in-out;
        }
        .btn-brand-accent:hover {
            background-color: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
        }

        /* Table Styling */
        .table-custom {
            margin-bottom: 0;
        }
        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--brand-border);
        }
        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }
        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Footer */
        .app-footer {
            margin-top: auto;
            border-top: 1px solid var(--brand-border);
            background-color: #ffffff;
            padding: 1rem 0;
            font-size: 0.8125rem;
            color: var(--brand-text-muted);
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Header / Navbar (Full-Width Fluid: Pepet Kiri & Pepet Kanan) --}}
    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container-fluid px-3 px-md-4">
            {{-- Brand Logo di Sudut Kiri --}}
            <a class="navbar-brand d-flex align-items-center text-decoration-none me-auto" href="{{ route('dashboard') }}">
                <span class="navbar-brand-badge">NK</span>
                <div>
                    <div class="navbar-brand-title">NOTA KONTAN</div>
                    <div class="navbar-brand-subtitle">Financial Transaction Manager</div>
                </div>
            </a>

            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-4"></i>
            </button>

            {{-- Navigasi di Sudut Kanan --}}
            <div class="collapse navbar-collapse flex-grow-0" id="navbarMenu">
                <ul class="navbar-nav align-items-lg-center gap-1 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2 me-2"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('nota.index') }}" class="nav-link nav-link-custom {{ request()->routeIs('nota.index') || request()->routeIs('nota.show') || request()->routeIs('nota.edit') ? 'active' : '' }}">
                            <i class="bi bi-receipt me-2"></i> Data Transaksi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('kontak-bos.index') }}" class="nav-link nav-link-custom {{ request()->routeIs('kontak-bos.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i> Kontak Pimpinan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main Content (Full-Width Fluid agar seimbang dengan Navbar) --}}
    <main class="container-fluid px-3 px-md-4 py-4 my-2 flex-grow-1">
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="small fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div class="small fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer (Full-Width Fluid) --}}
    <footer class="app-footer">
        <div class="container-fluid px-3 px-md-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <div>&copy; {{ date('Y') }} <strong>Aplikasi Nota Kontan</strong> &bull; Sistem Pencatatan & Pelaporan Keuangan</div>
            <div class="text-muted small">Versi 2.0 &bull; Ready for Cloud Deployment</div>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
