@extends('layouts.app')

@section('title', 'Data Transaksi')

@section('content')

{{-- Header Halaman --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Daftar Nota Kontan</h1>
        <p class="text-secondary small mb-0">
            Arsip transaksi penjualan tunai dan riwayat pelaporan dokumen keuangan.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-muted border px-3 py-2 fw-medium shadow-sm">
            Total: {{ $notas->total() }} Transaksi
        </span>
        <a href="{{ route('nota.create') }}" class="btn btn-brand-accent btn-sm px-3 py-2 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Input Nota Baru
        </a>
    </div>
</div>

{{-- Panel Filter Tanggal & Pencarian --}}
<div class="card card-custom shadow-sm mb-3 border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('nota.index') }}" id="formFilter">
            <div class="row g-2 align-items-end">

                {{-- Filter Tanggal Dari --}}
                <div class="col-12 col-md-3">
                    <label for="tanggal_dari" class="form-label small fw-semibold text-dark mb-1">
                        <i class="bi bi-calendar-event me-1 text-muted"></i>Dari Tanggal
                    </label>
                    <input type="date"
                           id="tanggal_dari"
                           name="tanggal_dari"
                           class="form-control form-control-sm"
                           value="{{ request('tanggal_dari') }}"
                           onchange="document.getElementById('formFilter').submit()">
                </div>

                {{-- Filter Tanggal Sampai --}}
                <div class="col-12 col-md-3">
                    <label for="tanggal_sampai" class="form-label small fw-semibold text-dark mb-1">
                        <i class="bi bi-calendar-event me-1 text-muted"></i>Sampai Tanggal
                    </label>
                    <input type="date"
                           id="tanggal_sampai"
                           name="tanggal_sampai"
                           class="form-control form-control-sm"
                           value="{{ request('tanggal_sampai') }}"
                           onchange="document.getElementById('formFilter').submit()">
                </div>

                {{-- Pencarian Nama --}}
                <div class="col-12 col-md-4">
                    <label for="cari" class="form-label small fw-semibold text-dark mb-1">
                        <i class="bi bi-search me-1 text-muted"></i>Cari Customer / Barang
                    </label>
                    <input type="text"
                           id="cari"
                           name="cari"
                           class="form-control form-control-sm"
                           value="{{ request('cari') }}"
                           placeholder="Nama customer atau nama barang...">
                </div>

                {{-- Tombol Aksi --}}
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-brand-primary btn-sm w-100 py-2">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    @if($filterAktif)
                        <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm py-2" title="Hapus semua filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Indikator filter aktif --}}
            @if($filterAktif)
                <div class="mt-2 pt-2 border-top d-flex flex-wrap align-items-center gap-2">
                    <span class="small text-muted"><i class="bi bi-funnel-fill text-warning me-1"></i>Filter aktif:</span>
                    @if(request('tanggal_dari'))
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-normal">
                            Dari: {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') }}
                        </span>
                    @endif
                    @if(request('tanggal_sampai'))
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-normal">
                            Sampai: {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') }}
                        </span>
                    @endif
                    @if(request('cari'))
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle fw-normal">
                            Kata kunci: "{{ request('cari') }}"
                        </span>
                    @endif
                    <a href="{{ route('nota.index') }}" class="small text-danger text-decoration-none ms-1">
                        <i class="bi bi-x-circle me-1"></i>Hapus semua filter
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Main Table Card --}}
<div class="card card-custom overflow-hidden mb-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No.</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="min-width: 180px;">Customer</th>
                    <th style="min-width: 150px;">Barang / Jasa</th>
                    <th style="min-width: 160px;">Nominal</th>
                    <th style="min-width: 330px;" class="text-end pe-4">Aksi Dokumen</th>
                </tr>
            </thead>

            <tbody>
                @forelse($notas as $nota)
                    <tr>
                        {{-- No --}}
                        <td class="text-center text-muted fw-semibold">
                            {{ $loop->iteration + (($notas->currentPage() - 1) * $notas->perPage()) }}
                        </td>

                        {{-- Tanggal --}}
                        <td>
                            <div class="fw-medium text-dark">
                                {{ $nota->tanggal_upload ? \Carbon\Carbon::parse($nota->tanggal_upload)->format('d/m/Y') : '-' }}
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                {{ $nota->created_at ? $nota->created_at->format('H:i') . ' WIB' : '' }}
                            </small>
                        </td>

                        {{-- Customer --}}
                        <td>
                            <div class="fw-bold text-dark mb-0.5">
                                {{ $nota->nama_customer ?? '-' }}
                            </div>
                            @if($nota->nomor_wa)
                                <div class="small text-muted d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-whatsapp text-success"></i>
                                    <span>{{ $nota->nomor_wa }}</span>
                                </div>
                            @else
                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.7rem;">
                                    Tanpa No. WA
                                </span>
                            @endif
                        </td>

                        {{-- Nama Barang --}}
                        <td>
                            <div class="text-dark fw-medium">
                                {{ $nota->nama_barang ?? '-' }}
                            </div>
                        </td>

                        {{-- Nominal --}}
                        <td>
                            <div class="fw-bold text-success fs-6">
                                Rp {{ number_format($nota->nominal_pembayaran ?? 0, 0, ',', '.') }}
                            </div>
                        </td>

                        {{-- Aksi Dokumen (Rapi, Bernafas, Tidak Dempet) --}}
                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-2">

                                {{-- 1. Tombol Detail --}}
                                <a href="{{ route('nota.show', $nota->id) }}"
                                   class="btn btn-outline-dark btn-sm py-1 px-2.5"
                                   style="font-size: 0.78rem;"
                                   title="Tinjau Detail Faktur & Bukti Struk">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>

                                {{-- 2. Tombol Unduh PDF --}}
                                <a href="{{ route('nota.pdf', $nota->id) }}"
                                   target="_blank"
                                   class="btn btn-outline-danger btn-sm py-1 px-2.5"
                                   style="font-size: 0.78rem;"
                                   title="Unduh Berkas PDF Resmi">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                </a>

                                {{-- 3. Tombol Utama: Kirim ke Bos --}}
                                <button type="button"
                                        class="btn btn-brand-primary btn-sm py-1 px-3"
                                        style="font-size: 0.78rem;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalKirimBos{{ $nota->id }}"
                                        title="Kirim Laporan Nota ke WhatsApp Pimpinan">
                                    <i class="bi bi-send me-1"></i> Kirim ke Bos
                                </button>

                                {{-- 4. Menu Dropdown: Kelola & Customer --}}
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm border text-secondary py-1 px-2.5 dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            style="font-size: 0.78rem;">
                                        Kelola
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1" style="font-size: 0.8125rem;">
                                        {{-- WhatsApp Customer --}}
                                        @if($nota->nomor_wa && $nota->wa_customer_url)
                                            <li>
                                                <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2"
                                                   href="{{ $nota->wa_customer_url }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer">
                                                    <i class="bi bi-whatsapp text-success"></i>
                                                    <span>Kirim WA ke Customer</span>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <span class="dropdown-item py-1.5 px-3 text-muted disabled">
                                                    <i class="bi bi-whatsapp"></i> Tanpa No. WhatsApp
                                                </span>
                                            </li>
                                        @endif

                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2"
                                               href="{{ route('nota.edit', $nota->id) }}">
                                                <i class="bi bi-pencil text-secondary"></i>
                                                <span>Edit Data Nota</span>
                                            </a>
                                        </li>

                                        <li><hr class="dropdown-divider my-1"></li>

                                        {{-- Hapus --}}
                                        <li>
                                            <form action="{{ route('nota.destroy', $nota->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data nota milik customer {{ addslashes($nota->nama_customer ?? 'ini') }}? Tindakan ini tidak dapat dibatalkan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                                    <i class="bi bi-trash3"></i>
                                                    <span>Hapus Nota</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </td>
                    </tr>

                    {{-- Modal Kirim ke Bos per Nota --}}
                    @include('nota.partials.modal-kirim-bos', ['nota' => $nota, 'bosses' => $bosses])

                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-receipt-cutoff text-muted display-4 d-block mb-3"></i>
                                @if($filterAktif)
                                    <h6 class="fw-semibold text-dark mb-1">Tidak Ada Data yang Cocok</h6>
                                    <p class="text-muted small mb-3">Tidak ditemukan transaksi yang sesuai dengan filter yang diterapkan.</p>
                                    <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Hapus Filter
                                    </a>
                                @else
                                    <h6 class="fw-semibold text-dark mb-1">Belum Ada Transaksi Tercatat</h6>
                                    <p class="text-muted small mb-3">Mulai catat transaksi pertama Anda dengan menekan tombol di bawah.</p>
                                    <a href="{{ route('nota.create') }}" class="btn btn-brand-accent btn-sm px-3 py-2">
                                        <i class="bi bi-plus-lg me-1"></i> Input Nota Pertama
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    @if($notas->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Halaman {{ $notas->currentPage() }} dari {{ $notas->lastPage() }}
            </div>
            <div>
                {{ $notas->links() }}
            </div>
        </div>
    @endif
</div>

@endsection
