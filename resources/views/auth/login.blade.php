<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk &mdash; ASTRA DAIHATSU Palembang Veteran</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            margin: 0;
            color: #0f172a;
        }

        .login-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
            max-width: 400px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .brand-badge {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-weight: 800;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }

        .form-control {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .input-group-text {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .btn-brand-accent {
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            font-weight: 600;
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 0.55rem 1rem;
            transition: all 0.15s ease;
        }
        .btn-brand-accent:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .quick-btn {
            font-size: 0.72rem;
            padding: 0.45rem 0.4rem;
            border-radius: 8px;
            transition: all 0.15s ease;
            line-height: 1.2;
        }
        .quick-btn:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        {{-- Header Card --}}
        <div class="text-center pt-4 px-4 pb-2">
            <div class="brand-badge mb-2">AD</div>
            <h1 class="h6 fw-bold text-dark mb-0.5" style="letter-spacing: -0.01em;">ASTRA DAIHATSU</h1>
            <p class="text-muted small mb-0" style="font-size: 0.75rem;">Palembang Veteran &bull; Sistem Delivery Order</p>
        </div>

        {{-- Form Body --}}
        <div class="px-4 pb-4 pt-2">
            {{-- Flash Messages (Compact) --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 px-3 small d-flex align-items-center gap-2 mb-2.5" role="alert" style="font-size: 0.75rem;">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small d-flex align-items-center gap-2 mb-2.5" role="alert" style="font-size: 0.75rem;">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Email Input --}}
                <div class="mb-2.5">
                    <label for="email" class="form-label small fw-semibold text-dark mb-1" style="font-size: 0.78rem;">
                        Alamat Email <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email"
                               class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@perusahaan.com"
                               required
                               autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div class="mb-2.5">
                    <label for="password" class="form-label small fw-semibold text-dark mb-1" style="font-size: 0.78rem;">
                        Kata Sandi <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password"
                               class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Masukkan kata sandi"
                               required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked style="width: 14px; height: 14px;">
                        <label class="form-check-label text-muted" for="remember" style="font-size: 0.75rem;">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-brand-accent d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk ke Akun</span>
                    </button>
                </div>
            </form>

            {{-- Akses Cepat Demo (Horizontal 3 Kolom Kompak) --}}
            <div class="pt-2.5 border-top">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Pilih Akun Cepat:
                    </span>
                    <span class="text-muted" style="font-size: 0.68rem;">1-klik auto-fill</span>
                </div>
                <div class="row g-1.5 text-center">
                    {{-- 1. Super Admin --}}
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-danger w-100 quick-btn fw-semibold d-flex flex-column align-items-center justify-content-center" onclick="fillLogin('admin@notakontan.com', 'admin123')">
                            <i class="bi bi-shield-lock fs-6 mb-0.5"></i>
                            <span>Super Admin</span>
                        </button>
                    </div>

                    {{-- 2. Admin Kantor --}}
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-primary w-100 quick-btn fw-semibold d-flex flex-column align-items-center justify-content-center" onclick="fillLogin('kantor@notakontan.com', 'admin123')">
                            <i class="bi bi-building fs-6 mb-0.5"></i>
                            <span>Admin Kantor</span>
                        </button>
                    </div>

                    {{-- 3. Sales Lapangan --}}
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-success w-100 quick-btn fw-semibold d-flex flex-column align-items-center justify-content-center" onclick="fillLogin('sales@notakontan.com', 'sales123')">
                            <i class="bi bi-phone fs-6 mb-0.5"></i>
                            <span>Sales</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer Minimalis --}}
        <div class="bg-light border-top py-2 px-3 text-center text-muted" style="font-size: 0.7rem;">
            &copy; {{ date('Y') }} ASTRA DAIHATSU Palembang Veteran
        </div>
    </div>

    {{-- Script Quick Fill --}}
    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>

</body>
</html>
