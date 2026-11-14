@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')

{{-- Header Halaman --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Manajemen Pengguna & Hak Akses</h1>
        <p class="text-secondary small mb-0">
            Kelola akun staf operasional kantor, sales lapangan, dan penetapan wewenang sistem.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-brand-accent btn-sm px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="bi bi-person-plus-fill"></i>
            <span>Tambah Akun Baru</span>
        </button>
    </div>
</div>

{{-- 4 Kartu Metrik Pengguna --}}
<div class="row g-3 mb-4">
    {{-- Total Pengguna --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Pengguna</span>
                <span class="badge bg-secondary-subtle text-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-people-fill fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-dark mb-1">{{ $totalUsers }} <span class="fs-6 fw-normal text-muted">Akun</span></div>
            <div class="text-muted small" style="font-size: 0.75rem;">Terdaftar di sistem</div>
        </div>
    </div>

    {{-- Administrator Utama --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Super Admin</span>
                <span class="badge bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-shield-lock-fill fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-danger mb-1">{{ $totalSuperAdmin }} <span class="fs-6 fw-normal text-muted">Akun</span></div>
            <div class="text-muted small" style="font-size: 0.75rem;">Akses penuh sistem</div>
        </div>
    </div>

    {{-- Admin Kantor --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Admin Kantor</span>
                <span class="badge bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-building fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-primary mb-1">{{ $totalAdminKantor }} <span class="fs-6 fw-normal text-muted">Akun</span></div>
            <div class="text-muted small" style="font-size: 0.75rem;">Verifikasi & Arsip Kantor</div>
        </div>
    </div>

    {{-- Sales Lapangan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Sales Lapangan</span>
                <span class="badge bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-phone-fill fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-success mb-1">{{ $totalSales }} <span class="fs-6 fw-normal text-muted">Akun</span></div>
            <div class="text-muted small" style="font-size: 0.75rem;">Input transaksi lapangan</div>
        </div>
    </div>
</div>

{{-- Filter & Tabel Daftar Pengguna --}}
<div class="card card-custom shadow-sm overflow-hidden mb-4">
    {{-- Filter Bar --}}
    <div class="p-3 bg-light border-bottom">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama atau email pengguna..." value="{{ request('cari') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Peran / Role</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Administrator Utama</option>
                        <option value="admin_kantor" {{ request('role') === 'admin_kantor' ? 'selected' : '' }}>Admin Kantor</option>
                        <option value="sales" {{ request('role') === 'sales' ? 'selected' : '' }}>Sales Lapangan</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-brand-primary btn-sm w-100 py-1.5">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['cari', 'role', 'status']))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm py-1.5" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No.</th>
                    <th style="min-width: 200px;">Nama Pengguna</th>
                    <th style="min-width: 180px;">Email Login</th>
                    <th style="width: 170px;">Peran (Hak Akses)</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 150px;">Tanggal Daftar</th>
                    <th style="width: 180px;" class="text-end pe-4">Aksi Akun</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="text-center text-muted fw-semibold">
                            {{ $loop->iteration + (($users->currentPage() - 1) * $users->perPage()) }}
                        </td>

                        {{-- Nama & Avatar --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-2.5 flex-shrink-0"
                                     style="width: 36px; height: 36px; background-color: #f1f5f9; color: #0f172a; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-0.5">
                                        {{ $u->name }}
                                        @if($u->id === auth()->id())
                                            <span class="badge bg-light text-primary border ms-1 fw-normal" style="font-size: 0.68rem;">Anda</span>
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">ID Pengguna: #{{ $u->id }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td>
                            <span class="text-dark fw-medium">{{ $u->email }}</span>
                        </td>

                        {{-- Role Badge --}}
                        <td>
                            <span class="badge {{ $u->role_badge_class }} px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                {{ $u->role_label }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($u->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium" style="font-size: 0.72rem;">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-medium" style="font-size: 0.72rem;">
                                    <i class="bi bi-slash-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td>
                            <div class="small text-dark">{{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $u->created_at ? $u->created_at->diffForHumans() : '' }}</small>
                        </td>

                        {{-- Aksi --}}
                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                {{-- Tombol Edit --}}
                                <button type="button" class="btn btn-outline-dark btn-sm py-1 px-2.5" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $u->id }}" title="Edit Data Pengguna">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>

                                {{-- Dropdown Aksi Lanjutan --}}
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm border text-secondary py-1 px-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-size: 0.78rem;">
                                        Opsi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1" style="font-size: 0.8125rem;">
                                        {{-- Toggle Status --}}
                                        @if($u->id !== auth()->id())
                                            <li>
                                                <form action="{{ route('users.toggle', $u->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 {{ $u->is_active ? 'text-warning' : 'text-success' }}">
                                                        <i class="bi {{ $u->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                                        <span>{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}</span>
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            {{-- Hapus Akun --}}
                                            <li>
                                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ addslashes($u->name) }} secara permanen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                                        <i class="bi bi-trash3"></i>
                                                        <span>Hapus Akun</span>
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <span class="dropdown-item py-1.5 px-3 text-muted disabled">
                                                    <i class="bi bi-lock me-1"></i>Akun Anda Sedang Digunakan
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Edit Pengguna --}}
                    <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content card-custom shadow-lg border-0">
                                <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                                    <div>
                                        <h5 class="modal-title fs-6 fw-bold mb-0">Edit Akun Pengguna</h5>
                                        <div class="small text-white-50" style="font-size: 0.75rem;">Perbarui data staf atau reset kata sandi</div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('users.update', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Alamat Email Login <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Peran Akses (Role) <span class="text-danger">*</span></label>
                                            <select name="role" class="form-select" {{ $u->id === auth()->id() ? 'disabled' : '' }} required>
                                                <option value="sales" {{ $u->role === 'sales' ? 'selected' : '' }}>Sales Lapangan (Hanya Input & Riwayat Sendiri)</option>
                                                <option value="admin_kantor" {{ $u->role === 'admin_kantor' ? 'selected' : '' }}>Admin Kantor (Kelola Nota & Cetak)</option>
                                                <option value="super_admin" {{ $u->role === 'super_admin' ? 'selected' : '' }}>Administrator Utama (Akses Penuh)</option>
                                            </select>
                                            @if($u->id === auth()->id())
                                                <input type="hidden" name="role" value="super_admin">
                                                <small class="text-muted" style="font-size: 0.72rem;">Anda tidak dapat mengubah peran akun Anda sendiri.</small>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Reset Kata Sandi (Opsional)</label>
                                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" minlength="6">
                                            <small class="text-muted" style="font-size: 0.72rem;">Isi minimal 6 karakter hanya jika ingin mengganti sandi akun staf ini.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-brand-primary btn-sm px-3">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-people text-muted display-4 d-block mb-3"></i>
                            <h6 class="fw-semibold text-dark mb-1">Tidak Ada Pengguna Ditemukan</h6>
                            <p class="text-muted small mb-0">Gunakan tombol "Tambah Akun Baru" untuk mendaftarkan staf operasional.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    @if($users->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }} (Total {{ $users->total() }} akun)
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>

{{-- Modal Tambah User Baru --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom shadow-lg border-0">
            <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                <div>
                    <h5 class="modal-title fs-6 fw-bold mb-0">Tambah Akun Pengguna Baru</h5>
                    <div class="small text-white-50" style="font-size: 0.75rem;">Buatkan akses login untuk Admin Kantor atau Sales Lapangan</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="new_name" class="form-label small fw-semibold text-dark">Nama Lengkap Staf <span class="text-danger">*</span></label>
                        <input type="text" id="new_name" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_email" class="form-label small fw-semibold text-dark">Alamat Email Login <span class="text-danger">*</span></label>
                        <input type="email" id="new_email" name="email" class="form-control" placeholder="Contoh: budi@notakontan.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label small fw-semibold text-dark">Kata Sandi Awal <span class="text-danger">*</span></label>
                        <input type="password" id="new_password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_role" class="form-label small fw-semibold text-dark">Tingkatan Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select id="new_role" name="role" class="form-select" required>
                            <option value="sales" selected>Sales Lapangan (Hanya Input & Lihat Riwayat Sendiri)</option>
                            <option value="admin_kantor">Admin Kantor (Input, Verifikasi Semua Nota, Kirim WA Bos)</option>
                            <option value="super_admin">Administrator Utama (Akses Penuh Seluruh Sistem)</option>
                        </select>
                        <div class="p-2.5 rounded-3 bg-light border mt-2 small text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            <strong>Sales Lapangan:</strong> Cocok untuk tim luar kantor yang mencatat penjualan di tempat customer.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand-primary btn-sm px-3">
                        <i class="bi bi-person-check-fill me-1"></i>Simpan & Buat Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
