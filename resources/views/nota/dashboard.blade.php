@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">
            @if(auth()->check() && auth()->user()->isSales())
                Dashboard Penjualan Saya
            @else
                Dashboard Ikhtisar
            @endif
        </h1>
        <p class="text-secondary small mb-0">
            @if(auth()->check() && auth()->user()->isSales())
                Monitoring performa pencatatan kas masuk dan penjualan lapangan Anda.
            @else
                Monitoring performa arus kas masuk, arsip faktur, dan aktivitas pelaporan seluruh tim.
            @endif
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-secondary border px-3 py-2 fw-medium shadow-sm">
            <i class="bi bi-calendar3 me-1 text-primary"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
        <a href="{{ route('nota.create') }}" class="btn btn-brand-accent btn-sm px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i>
            <span>Input Transaksi Baru</span>
        </a>
    </div>
</div>

{{-- Panel Filter Tanggal Dashboard --}}
<div class="card card-custom shadow-sm mb-4 border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('dashboard') }}" id="formFilterDashboard">
            <div class="row g-2 align-items-end">

                {{-- Label & Deskripsi Ringkas --}}
                <div class="col-12 col-md-4">
                    <div class="fw-semibold text-dark small mb-1">
                        <i class="bi bi-funnel text-primary me-1"></i> Filter Periode Transaksi
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Pilih rentang tanggal untuk melihat statistik & ringkasan kas
                    </div>
                </div>

                {{-- Filter Dari Tanggal --}}
                <div class="col-6 col-md-3">
                    <label for="tanggal_dari" class="form-label small fw-semibold text-dark mb-1">
                        Dari Tanggal
                    </label>
                    <input type="date"
                           id="tanggal_dari"
                           name="tanggal_dari"
                           class="form-control form-control-sm"
                           value="{{ request('tanggal_dari') }}"
                           onchange="document.getElementById('formFilterDashboard').submit()">
                </div>

                {{-- Filter Sampai Tanggal --}}
                <div class="col-6 col-md-3">
                    <label for="tanggal_sampai" class="form-label small fw-semibold text-dark mb-1">
                        Sampai Tanggal
                    </label>
                    <input type="date"
                           id="tanggal_sampai"
                           name="tanggal_sampai"
                           class="form-control form-control-sm"
                           value="{{ request('tanggal_sampai') }}"
                           onchange="document.getElementById('formFilterDashboard').submit()">
                </div>

                {{-- Tombol Terapkan & Reset --}}
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-brand-primary btn-sm w-100 py-2">
                        <i class="bi bi-check2 me-1"></i>Terapkan
                    </button>
                    @if($filterAktif)
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm py-2" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Indikator Filter Aktif --}}
            @if($filterAktif)
                <div class="mt-2 pt-2 border-top d-flex flex-wrap align-items-center gap-2">
                    <span class="small text-muted"><i class="bi bi-funnel-fill text-warning me-1"></i>Menampilkan data periode:</span>
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
                    <a href="{{ route('dashboard') }}" class="small text-danger text-decoration-none ms-1">
                        <i class="bi bi-x-circle me-1"></i>Tampilkan Seluruh Data
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- 4 Kartu Metrik Keuangan (KPI Cards) --}}
<div class="row g-3 mb-4">

    {{-- Total Kas Masuk --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    {{ auth()->check() && auth()->user()->isSales() ? 'Kas Masuk Saya' : 'Total Kas Masuk' }}
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-wallet2 fs-6"></i>
                </span>
            </div>
            <div class="fs-4 fw-bold text-success mb-1 text-truncate" title="Rp {{ number_format($totalNominal, 0, ',', '.') }}">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                {{ $filterAktif ? 'Sesuai rentang tanggal terpilih' : (auth()->check() && auth()->user()->isSales() ? 'Akumulasi transaksi Anda' : 'Akumulasi seluruh transaksi') }}
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

    {{-- Saluran Pelaporan / Status Petugas --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3.5 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    {{ auth()->check() && auth()->user()->isSales() ? 'Petugas Lapangan' : 'Jalur Distribusi' }}
                </span>
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi {{ auth()->check() && auth()->user()->isSales() ? 'bi-person-badge' : 'bi-send-check' }} fs-6"></i>
                </span>
            </div>
            <div class="fs-5 fw-bold text-dark mb-1">
                {{ auth()->check() && auth()->user()->isSales() ? auth()->user()->name : 'WhatsApp Ready' }}
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                {{ auth()->check() && auth()->user()->isSales() ? 'Status: Akun Sales Aktif' : $totalBosses . ' Kontak Pimpinan Terdaftar' }}
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
                    <th style="width: 120px;">Tanggal</th>
                    <th>Customer</th>
                    <th>Type Unit & Warna</th>
                    <th style="width: 160px;">Total Bayar</th>
                    <th style="width: {{ auth()->check() && auth()->user()->isSales() ? '110px' : '220px' }};" class="text-end pe-4">Aksi Cepat</th>
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
                            <div class="fw-bold text-dark mb-0.5">{{ $nota->nama_customer }}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">
                                {{ $nota->isSparepart() ? 'Pelanggan Sparepart' : 'STNK: ' . $nota->stnk_bpkb_name }}
                            </div>
                        </td>
                        <td>
                            @if($nota->isSparepart())
                                <span class="badge bg-success-subtle text-success border border-success-subtle me-1" style="font-size: 0.68rem;">
                                    <i class="bi bi-tools"></i> Sparepart
                                </span>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="{{ $nota->nama_barang }}">{{ $nota->nama_barang }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ count($nota->items_list) }} macam part &bull; Tunai</small>
                            @else
                                <div class="fw-bold text-dark">{{ $nota->unit_type }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $nota->warna ?: '-' }} &bull; {{ $nota->cara_pembelian ?: 'Credit' }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-success fs-6">
                                Rp {{ $nota->formatted_total_bayar }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-2">
                                <a href="{{ route('nota.show', $nota->id) }}" class="btn btn-outline-dark btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                    <i class="bi bi-eye"></i>
                                    <span>Detail</span>
                                </a>
                                @if(auth()->check() && !auth()->user()->isSales())
                                    <button type="button" class="btn btn-brand-primary btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalKirimBos{{ $nota->id }}" style="font-size: 0.8rem;">
                                        <i class="bi bi-send"></i>
                                        <span>Kirim ke Bos</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @include('nota.partials.modal-kirim-bos', ['nota' => $nota, 'bosses' => $bosses])
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                            <div class="fw-medium text-dark">Belum ada riwayat transaksi</div>
                            <small class="text-muted">Transaksi baru yang dicatat akan muncul di sini.</small>
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
