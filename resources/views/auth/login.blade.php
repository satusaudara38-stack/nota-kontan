<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login &mdash; Aplikasi Nota Kontan</title>

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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #0f172a;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-weight: 800;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.15);
        }

        .btn-brand-accent {
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .btn-brand-accent:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .account-preset-pill {
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 8px;
        }
        .account-preset-pill:hover {
            background-color: #e2e8f0 !important;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        {{-- Header Card --}}
        <div class="text-center p-4 p-sm-5 pb-3">
            <div class="brand-badge mb-3">NK</div>
            <h1 class="h5 fw-bold text-dark mb-1">Masuk ke Sistem</h1>
            <p class="text-secondary small mb-0">Aplikasi Pengelolaan Nota Kontan & Arsip Transaksi</p>
        </div>

        {{-- Form Body --}}
        <div class="p-4 p-sm-5 pt-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Email Input --}}
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-dark">
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
                        <div class="text-danger small mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label small fw-semibold text-dark mb-1">
                            Kata Sandi <span class="text-danger">*</span>
                        </label>
                    </div>
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
                        <div class="text-danger small mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label small text-muted" for="remember">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-brand-accent py-2.5 d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        <span>Masuk Sekarang</span>
                    </button>
                </div>
            </form>

            {{-- Akun Bawaan untuk Uji Coba Cepat (Preset Quick Fill) --}}
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex align-items-center gap-1.5 mb-2 text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">
                    <i class="bi bi-shield-check text-primary"></i>
                    <span>Akun Pengujian Demo (Klik untuk Isi):</span>
                </div>
                <div class="d-flex flex-column gap-1.5">
                    {{-- 1. Super Admin --}}
                    <div class="account-preset-pill bg-white border d-flex justify-content-between align-items-center" onclick="fillLogin('admin@notakontan.com', 'admin123')">
                        <span class="fw-semibold text-danger"><i class="bi bi-person-fill-gear me-1"></i>Administrator Utama</span>
                        <span class="text-muted small">admin123</span>
                    </div>

                    {{-- 2. Admin Kantor --}}
                    <div class="account-preset-pill bg-white border d-flex justify-content-between align-items-center" onclick="fillLogin('kantor@notakontan.com', 'admin123')">
                        <span class="fw-semibold text-primary"><i class="bi bi-building me-1"></i>Admin Kantor</span>
                        <span class="text-muted small">admin123</span>
                    </div>

                    {{-- 3. Sales Lapangan --}}
                    <div class="account-preset-pill bg-white border d-flex justify-content-between align-items-center" onclick="fillLogin('sales@notakontan.com', 'sales123')">
                        <span class="fw-semibold text-success"><i class="bi bi-phone me-1"></i>Sales Lapangan</span>
                        <span class="text-muted small">sales123</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-light border-top py-3 text-center small text-muted" style="font-size: 0.75rem;">
            &copy; {{ date('Y') }} Sistem Aplikasi Nota Kontan &bull; Multi-Role Ready
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
