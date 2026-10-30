@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Dashboard Ikhtisar</h1>
        <p class="text-secondary small mb-0">
            Monitoring performa arus kas masuk, arsip faktur, dan aktivitas pelaporan terkini.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-secondary border px-3 py-2 fw-medium shadow-sm">
            <i class="bi bi-calendar3 me-1 text-primary"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
        <a href="{{ route('nota.create') }}" class="btn btn-brand-accent btn-sm px-3 py-2 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Input Nota Baru
        </a>
    </div>
</div>

{{-- 4 Kartu Metrik Keuangan (KPI Cards) --}}
<div class="row g-3 mb-4">

    {{-- Total Kas Masuk --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Kas Masuk</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-wallet2 fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-success mb-1 text-truncate" title="Rp {{ number_format($totalNominal, 0, ',', '.') }}">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                Akumulasi seluruh transaksi
            </div>
        </div>
    </div>

    {{-- Kas Bulan Ini --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Bulan {{ now()->translatedFormat('F') }}</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-graph-up-arrow fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-primary mb-1 text-truncate" title="Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}">
                Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                {{ $transaksiBulanIni }} transaksi bulan berjalan
            </div>
        </div>
    </div>

    {{-- Total Dokumen Transaksi --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Transaksi</span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-receipt fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-dark mb-1">
                {{ number_format($totalNota, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">Nota</span>
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                Rata-rata: Rp {{ number_format($rataRata, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Saluran Pelaporan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">Jalur Distribusi</span>
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-send-check fs-6"></i>
                </span>
            </div>
            <div class="fs-5 fw-bold text-dark mb-1">
                WhatsApp Ready
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                3 Kontak Pimpinan Terdaftar
            </div>
        </div>
    </div>

</div>

{{-- Aktivitas Transaksi Terbaru (Recent Transactions) --}}
<div class="card card-custom mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <div>
            <h2 class="h6 fw-bold text-dark mb-0">Aktivitas Transaksi Terbaru</h2>
            <span class="text-muted small" style="font-size: 0.75rem;">5 data nota yang baru saja dimasukkan ke dalam sistem</span>
        </div>
        <a href="{{ route('nota.index') }}" class="btn btn-outline-dark btn-sm py-1 px-3 d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
            <span>Lihat Semua Data</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 130px;">Tanggal</th>
                    <th>Customer</th>
                    <th>Barang / Jasa</th>
                    <th style="width: 170px;">Nominal</th>
                    <th style="width: 200px;" class="text-end pe-4">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentNotas as $nota)
                    <tr>
                        <td>
                            <div class="fw-medium text-dark">
                                {{ $nota->tanggal_upload ? \Carbon\Carbon::parse($nota->tanggal_upload)->format('d/m/Y') : '-' }}
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                {{ $nota->created_at ? $nota->created_at->format('H:i') . ' WIB' : '' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $nota->nama_customer }}</div>
                            @if($nota->nomor_wa)
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-whatsapp text-success me-1"></i>{{ $nota->nomor_wa }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark">{{ $nota->nama_barang }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success fs-6">
                                Rp {{ number_format($nota->nominal_pembayaran, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-2">
                                <a href="{{ route('nota.show', $nota->id) }}" class="btn btn-outline-dark btn-sm py-1 px-2.5" style="font-size: 0.78rem;">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                <button type="button" class="btn btn-brand-primary btn-sm py-1 px-2.5" data-bs-toggle="modal" data-bs-target="#modalKirimBos{{ $nota->id }}" style="font-size: 0.78rem;">
                                    <i class="bi bi-send me-1"></i> Kirim ke Bos
                                </button>
                            </div>
                        </td>
                    </tr>
                    @include('nota.partials.modal-kirim-bos', ['nota' => $nota, 'bosses' => $bosses])
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted small">
                            Belum ada riwayat transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Alur Standar Pengelolaan Nota (SOP) --}}
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
        <div>
            <h2 class="h6 fw-bold text-dark mb-0">Standar Operasional Prosedur (SOP)</h2>
            <span class="text-muted small" style="font-size: 0.75rem;">Tahapan pencatatan dan distribusi dokumen keuangan</span>
        </div>
        <span class="badge bg-light text-muted border small">Pedoman Kerja</span>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-4">
            <div class="p-3 rounded-3 border h-100 bg-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white rounded-circle p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">1</span>
                    <h3 class="h6 fw-bold text-dark mb-0">Input & Arsip Struk</h3>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.8125rem;">
                    Catat data customer, deskripsi barang/jasa, nominal pembayaran, serta unggah file foto struk atau bukti transfer fisik.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-3 border h-100 bg-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white rounded-circle p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">2</span>
                    <h3 class="h6 fw-bold text-dark mb-0">Verifikasi & Tinjauan</h3>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.8125rem;">
                    Periksa kembali data pada tabel data transaksi, verifikasi kesesuaian nilai nominal dengan struk, atau edit jika ada kekeliruan.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-3 border h-100 bg-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white rounded-circle p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">3</span>
                    <h3 class="h6 fw-bold text-dark mb-0">Distribusi Digital</h3>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.8125rem;">
                    Kirimkan laporan berkas PDF langsung ke WhatsApp jajaran pimpinan (Bos) serta tanda terima pembelian ke pihak customer.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
