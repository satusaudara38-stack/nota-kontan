@extends('layouts.app')

@section('title', 'Kontak Pimpinan')

@section('content')

{{-- Header Halaman --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-3 border-bottom">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Kelola Kontak Pimpinan</h1>
        <p class="text-secondary small mb-0">
            Daftar nomor WhatsApp pimpinan yang menerima laporan otomatis nota kontan.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-secondary border px-3 py-2 fw-medium shadow-sm">
            Total: {{ $kontakBos->count() }} Pimpinan
        </span>
        <button type="button" class="btn btn-brand-primary btn-sm px-3 py-2 shadow-sm d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahBos">
            <i class="bi bi-plus-lg me-2"></i>
            <span>Tambah Kontak Baru</span>
        </button>
    </div>
</div>

{{-- Main Table Card --}}
<div class="card card-custom overflow-hidden mb-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;" class="text-center">No.</th>
                    <th style="min-width: 240px;">Nama Pimpinan</th>
                    <th style="min-width: 180px;">Jabatan / Peran</th>
                    <th style="min-width: 200px;">Nomor WhatsApp</th>
                    <th style="min-width: 180px;" class="text-end pe-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($kontakBos as $b)
                    <tr>
                        <td class="text-center text-muted fw-semibold">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3 flex-shrink-0"
                                     style="width: 44px; height: 44px; background-color: #f0f7ff; border: 1px solid #cce3fd; font-size: 0.9rem; letter-spacing: 0.5px;">
                                    {{ strtoupper(substr($b->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.3;">
                                        {{ $b->nama }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        Ditambahkan {{ $b->created_at ? $b->created_at->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 fw-medium" style="font-size: 0.8rem; letter-spacing: 0.02em;">
                                {{ $b->jabatan ?? 'Pimpinan' }}
                            </span>
                        </td>

                        <td>
                            <a href="https://wa.me/{{ $b->wa_link }}" target="_blank" class="text-decoration-none text-success fw-semibold d-inline-flex align-items-center">
                                <i class="bi bi-whatsapp me-2 fs-5"></i>
                                <span style="font-size: 0.9rem;">{{ $b->nomor_wa }}</span>
                            </a>
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-2">
                                {{-- Tombol Edit --}}
                                <button type="button"
                                        class="btn btn-light btn-sm text-secondary border px-3 py-1.5 d-inline-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditBos{{ $b->id }}"
                                        title="Ubah Data">
                                    <i class="bi bi-pencil me-1.5"></i> Edit
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('kontak-bos.destroy', $b->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontak {{ addslashes($b->nama) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-light btn-sm text-danger border px-3 py-1.5 d-inline-flex align-items-center"
                                            title="Hapus Kontak">
                                        <i class="bi bi-trash3 me-1.5"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Edit Kontak --}}
                    <div class="modal fade" id="modalEditBos{{ $b->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $b->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content card-custom shadow border-0 overflow-hidden">
                                <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                                    <h5 class="modal-title fs-6 fw-bold" id="modalEditLabel{{ $b->id }}">
                                        Edit Kontak Pimpinan
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form action="{{ route('kontak-bos.update', $b->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Nama Pimpinan <span class="text-danger">*</span></label>
                                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $b->nama) }}" required placeholder="Contoh: Pak Ariq">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Nomor WhatsApp <span class="text-danger">*</span></label>
                                            <input type="tel" name="nomor_wa" class="form-control" value="{{ old('nomor_wa', $b->nomor_wa) }}" required placeholder="Contoh: 081234567890">
                                            <div class="form-text small">Nomor aktif WhatsApp. Boleh diawali 08 atau 62.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-dark">Jabatan / Peran</label>
                                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $b->jabatan) }}" placeholder="Contoh: Direktur Utama / Keuangan / Operasional">
                                        </div>
                                    </div>

                                    <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-brand-primary btn-sm px-3">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-people text-muted display-4 d-block mb-3"></i>
                                <h6 class="fw-semibold text-dark mb-1">Belum Ada Kontak Pimpinan</h6>
                                <p class="text-muted small mb-3">Tambahkan kontak pimpinan agar fitur pengiriman nota otomatis dapat digunakan.</p>
                                <button type="button" class="btn btn-brand-primary btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahBos">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Kontak Sekarang
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Kontak Baru --}}
<div class="modal fade" id="modalTambahBos" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom shadow border-0 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                <div>
                    <h5 class="modal-title fs-6 fw-bold mb-0" id="modalTambahLabel">
                        Tambah Kontak Pimpinan
                    </h5>
                    <div class="small text-white-50" style="font-size: 0.75rem;">
                        Penerima otomatis laporan dokumen nota kontan
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('kontak-bos.store') }}" method="POST">
                @csrf

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_bos" class="form-label small fw-semibold text-dark">
                            Nama Pimpinan <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama_bos" name="nama" class="form-control" required placeholder="Contoh: Pak Ariq">
                    </div>

                    <div class="mb-3">
                        <label for="wa_bos" class="form-label small fw-semibold text-dark">
                            Nomor WhatsApp <span class="text-danger">*</span>
                        </label>
                        <input type="tel" id="wa_bos" name="nomor_wa" class="form-control" required placeholder="Contoh: 081234567890">
                        <div class="form-text small">Masukkan nomor WhatsApp aktif. Boleh diawali 08 atau 62.</div>
                    </div>

                    <div class="mb-3">
                        <label for="jabatan_bos" class="form-label small fw-semibold text-dark">
                            Jabatan / Peran (Opsional)
                        </label>
                        <input type="text" id="jabatan_bos" name="jabatan" class="form-control" placeholder="Contoh: Direktur / Keuangan / Operasional">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand-primary btn-sm px-4">Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
