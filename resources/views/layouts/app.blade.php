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

        /* User Profile Button in Navbar */
        .user-profile-btn {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            transition: all 0.15s ease-in-out;
        }
        .user-profile-btn:hover,
        .user-profile-btn:focus,
        .user-profile-btn:active,
        .user-profile-btn.show {
            background-color: rgba(255, 255, 255, 0.18) !important;
            border-color: rgba(255, 255, 255, 0.35) !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }
        .user-profile-btn .profile-name {
            color: #ffffff !important;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.2;
        }
        .user-profile-btn .profile-role {
            color: #cbd5e1 !important;
            font-size: 0.6875rem;
            line-height: 1;
        }
        .user-profile-btn::after {
            color: #cbd5e1;
            margin-left: 0.5rem;
            vertical-align: 0.15em;
        }
        .user-profile-btn:hover::after,
        .user-profile-btn.show::after {
            color: #ffffff;
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

        /* Utility Spacing & Icons Helper */
        .gap-1\.5 { gap: 0.375rem !important; }
        .gap-2 { gap: 0.5rem !important; }
        .gap-2\.5 { gap: 0.625rem !important; }
        .gap-3 { gap: 0.75rem !important; }
        .me-1\.5 { margin-right: 0.375rem !important; }
        .ms-1\.5 { margin-left: 0.375rem !important; }
        .px-3\.5 { padding-left: 0.875rem !important; padding-right: 0.875rem !important; }
        .p-3\.5 { padding: 0.875rem !important; }
        .btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            white-space: nowrap;
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

    {{-- Header / Navbar (Full-Width Fluid: Lebar Penuh) --}}
    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container-fluid px-3 px-md-4">
            {{-- Brand Logo di Sudut Kiri --}}
            <a class="navbar-brand d-flex align-items-center text-decoration-none me-auto" href="{{ route('dashboard') }}">
                <span class="navbar-brand-badge">AD</span>
                <div>
                    <div class="navbar-brand-title">ASTRA DAIHATSU</div>
                    <div class="navbar-brand-subtitle">Palembang Veteran &bull; Delivery Order</div>
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
                            <i class="bi bi-grid-1x2 me-1.5"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('nota.index') }}" class="nav-link nav-link-custom {{ request()->routeIs('nota.index') || request()->routeIs('nota.show') || request()->routeIs('nota.edit') ? 'active' : '' }}">
                            <i class="bi bi-receipt me-1.5"></i>
                            @if(auth()->check() && auth()->user()->isSales())
                                Riwayat Transaksi Saya
                            @else
                                Data Transaksi
                            @endif
                        </a>
                    </li>

                    @if(auth()->check() && !auth()->user()->isSales())
                        <li class="nav-item">
                            <a href="{{ route('kontak-bos.index') }}" class="nav-link nav-link-custom {{ request()->routeIs('kontak-bos.*') ? 'active' : '' }}">
                                <i class="bi bi-telephone-outbound me-1.5"></i> Kontak Pimpinan
                            </a>
                        </li>
                    @endif

                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="bi bi-people-fill me-1.5"></i> Kelola Pengguna
                            </a>
                        </li>
                    @endif

                    {{-- User Profile & Logout --}}
                    @auth
                        <li class="nav-item dropdown ms-lg-2 mt-2 mt-lg-0 border-top border-lg-0 pt-2 pt-lg-0">
                            <button class="btn user-profile-btn d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 28px; height: 28px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; font-size: 0.75rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </span>
                                <div class="text-start d-none d-sm-block pe-1">
                                    <div class="profile-name">{{ auth()->user()->name }}</div>
                                    <div class="profile-role">{{ auth()->user()->role_label }}</div>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border p-2 mt-2" style="font-size: 0.8125rem; min-width: 220px; border-radius: 10px;">
                                <li class="px-2 py-2 border-bottom mb-1 bg-light rounded-top">
                                    <div class="fw-bold text-dark" style="font-size: 0.875rem;">{{ auth()->user()->name }}</div>
                                    <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                                    <span class="badge {{ auth()->user()->role_badge_class }} mt-1.5 fw-semibold" style="font-size: 0.7rem;">
                                        {{ auth()->user()->role_label }}
                                    </span>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-2 px-2 text-dark d-flex align-items-center gap-2 rounded" data-bs-toggle="modal" data-bs-target="#modalUbahProfil">
                                        <i class="bi bi-shield-lock text-primary"></i>
                                        <span class="fw-medium">Ubah Email & Kata Sandi</span>
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 px-2 text-danger d-flex align-items-center gap-2 rounded mt-1">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span class="fw-medium">Keluar (Logout)</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
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
            <div>&copy; {{ date('Y') }} <strong>ASTRA DAIHATSU Palembang Veteran</strong> &bull; Sistem Delivery Order</div>
            <div class="text-muted small">Versi 2.0 &bull; Multi-Role Ready</div>
        </div>
    </footer>

    {{-- Modal Ubah Email & Kata Sandi Akun Login --}}
    @auth
        <div class="modal fade" id="modalUbahProfil" tabindex="-1" aria-labelledby="modalUbahProfilLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card-custom shadow-lg border-0">
                    <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                        <div>
                            <h5 class="modal-title fs-6 fw-bold mb-0" id="modalUbahProfilLabel">
                                <i class="bi bi-shield-lock me-1 text-primary"></i> Ubah Email & Kata Sandi
                            </h5>
                            <div class="small text-white-50" style="font-size: 0.75rem;">
                                Perbarui kredensial akun login Anda ({{ auth()->user()->role_label }})
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            {{-- Nama Lengkap --}}
                            <div class="mb-3">
                                <label for="profile_name" class="form-label small fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="profile_name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Alamat Email --}}
                            <div class="mb-3">
                                <label for="profile_email" class="form-label small fw-semibold text-dark">Alamat Email Login <span class="text-danger">*</span></label>
                                <input type="email" id="profile_email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                                <small class="text-muted" style="font-size: 0.72rem;">Email ini digunakan saat masuk (login) ke aplikasi.</small>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-3 text-muted">

                            {{-- Kata Sandi Baru --}}
                            <div class="mb-3">
                                <label for="profile_password" class="form-label small fw-semibold text-dark">Kata Sandi Baru (Opsional)</label>
                                <input type="password" id="profile_password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin merubah sandi" minlength="6">
                                <small class="text-muted" style="font-size: 0.72rem;">Isi minimal 6 karakter hanya jika ingin mengganti kata sandi login.</small>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Konfirmasi Kata Sandi Baru --}}
                            <div class="mb-3">
                                <label for="profile_password_confirmation" class="form-label small fw-semibold text-dark">Ketik Ulang Kata Sandi Baru</label>
                                <input type="password" id="profile_password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" minlength="6">
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-brand-primary btn-sm px-3.5 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($errors->has('name') || $errors->has('email') || $errors->has('password'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var el = document.getElementById('modalUbahProfil');
                    if (el) {
                        var myModal = new bootstrap.Modal(el);
                        myModal.show();
                    }
                });
            </script>
        @endif
    @endauth

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
