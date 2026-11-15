@extends('layouts.app')

@section('title', 'Data Transaksi')

@section('content')

{{-- Header Halaman --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">
            @if(auth()->check() && auth()->user()->isSales())
                Riwayat Transaksi Saya
            @else
                Daftar Transaksi & Konfirmasi DO
            @endif
        </h1>
        <p class="text-secondary small mb-0">
            @if(auth()->check() && auth()->user()->isSales())
                Arsip pesanan dan konfirmasi delivery order mobil yang Anda tangani.
            @else
                Arsip pesanan, konfirmasi delivery order mobil, dan riwayat dokumen keuangan tim Astra Daihatsu.
            @endif
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-muted border px-3 py-2 fw-medium shadow-sm">
            Total: {{ $notas->total() }} Transaksi
        </span>
        <a href="{{ route('nota.create') }}" class="btn btn-brand-accent btn-sm px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i>
            <span>Input Transaksi Baru</span>
        </a>
    </div>
</div>

{{-- Panel Filter Tanggal & Pencarian --}}
<div class="card card-custom shadow-sm mb-3 border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('nota.index') }}" id="formFilter">
            <div class="row g-2 align-items-end">

                {{-- Filter Tanggal Dari --}}
                <div class="col-6 col-sm-4 col-lg-2">
                    <label for="tanggal_dari" class="form-label small fw-semibold text-dark mb-1 text-nowrap">
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
                <div class="col-6 col-sm-4 col-lg-2">
                    <label for="tanggal_sampai" class="form-label small fw-semibold text-dark mb-1 text-nowrap">
                        <i class="bi bi-calendar-event me-1 text-muted"></i>Sampai Tanggal
                    </label>
                    <input type="date"
                           id="tanggal_sampai"
                           name="tanggal_sampai"
                           class="form-control form-control-sm"
                           value="{{ request('tanggal_sampai') }}"
                           onchange="document.getElementById('formFilter').submit()">
                </div>

                {{-- Filter Kategori Transaksi (Mobil vs Sparepart) --}}
                <div class="col-6 col-sm-4 col-lg-2">
                    <label for="kategori" class="form-label small fw-semibold text-dark mb-1 text-nowrap">
                        <i class="bi bi-tags me-1 text-muted"></i>Kategori
                    </label>
                    <select name="kategori" id="kategori" class="form-select form-select-sm" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Kategori</option>
                        <option value="mobil" {{ request('kategori') === 'mobil' ? 'selected' : '' }}>🚗 Unit Mobil (DO)</option>
                        <option value="sparepart" {{ request('kategori') === 'sparepart' ? 'selected' : '' }}>🔧 Sparepart</option>
                    </select>
                </div>

                {{-- Filter Petugas / Sales (Khusus Super Admin & Admin Kantor) --}}
                @if(auth()->check() && !auth()->user()->isSales() && isset($salesList) && $salesList->isNotEmpty())
                    <div class="col-6 col-sm-4 col-lg-2">
                        <label for="sales_id" class="form-label small fw-semibold text-dark mb-1 text-nowrap">
                            <i class="bi bi-person-badge me-1 text-muted"></i>Petugas / Sales
                        </label>
                        <select name="sales_id" id="sales_id" class="form-select form-select-sm" onchange="document.getElementById('formFilter').submit()">
                            <option value="">Semua Petugas / Sales</option>
                            @foreach($salesList as $s)
                                <option value="{{ $s->id }}" {{ request('sales_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} (Sales)
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Pencarian Nama --}}
                <div class="col-12 col-sm-8 col-lg">
                    <label for="cari" class="form-label small fw-semibold text-dark mb-1 text-nowrap">
                        <i class="bi bi-search me-1 text-muted"></i>Cari Customer / Barang
                    </label>
                    <input type="text"
                           id="cari"
                           name="cari"
                           class="form-control form-control-sm"
                           value="{{ request('cari') }}"
                           placeholder="Nama customer atau barang...">
                </div>

                {{-- Tombol Aksi --}}
                <div class="col-12 col-sm-auto col-lg-auto">
                    <div class="d-flex gap-1.5 align-items-center">
                        <button type="submit" class="btn btn-brand-primary btn-sm px-3 d-inline-flex align-items-center gap-1.5 text-nowrap" style="height: 31px;">
                            <i class="bi bi-funnel"></i>
                            <span>Filter</span>
                        </button>
                        @if($filterAktif)
                            <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 d-inline-flex align-items-center" title="Hapus semua filter" style="height: 31px;">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
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
                    @if(request('sales_id') && isset($salesList))
                        @php $currentSales = $salesList->firstWhere('id', request('sales_id')); @endphp
                        @if($currentSales)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-normal">
                                Sales: {{ $currentSales->name }}
                            </span>
                        @endif
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
                    <th style="width: 45px;" class="text-center text-nowrap">No.</th>
                    <th style="width: 105px;" class="text-nowrap">Tanggal</th>
                    <th style="min-width: 170px;" class="text-nowrap">Customer</th>
                    <th style="min-width: 180px;" class="text-nowrap">Type Unit & Warna</th>
                    <th style="width: 110px;" class="text-center text-nowrap">Skema</th>
                    <th style="width: 140px;" class="text-nowrap">Total Bayar</th>
                    @if(auth()->check() && !auth()->user()->isSales())
                        <th style="width: 140px;" class="text-nowrap">Petugas / Sales</th>
                    @endif
                    <th style="min-width: {{ auth()->check() && auth()->user()->isSales() ? '220px' : '310px' }};" class="text-end pe-4 text-nowrap">Aksi Dokumen</th>
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
                            @if($nota->isSparepart())
                                <div class="small text-muted" style="font-size: 0.72rem;">
                                    <i class="bi bi-shop text-muted me-1"></i>Pelanggan Part Kantor
                                </div>
                            @else
                                <div class="small text-muted" style="font-size: 0.72rem;">
                                    STNK: {{ $nota->stnk_bpkb_name }}
                                </div>
                            @endif
                            @if($nota->nomor_wa)
                                <div class="small text-muted d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-whatsapp text-success"></i>
                                    <span>{{ $nota->nomor_wa }}</span>
                                </div>
                            @endif

                            {{-- Status Kirim WA ke Customer --}}
                            <div class="mt-1" id="badgeCustomerContainer{{ $nota->id }}">
                                @if($nota->isTerkirimCustomer())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-1.5 d-inline-flex align-items-center gap-1"
                                          title="Terkirim ke Customer: {{ $nota->wa_customer_sent_at->format('d/m/Y H:i') }} WIB ({{ $nota->wa_customer_sent_count }}x)"
                                          style="font-size: 0.68rem; font-weight: 500;">
                                        <i class="bi bi-check2-all text-success"></i> Terkirim ke Customer
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border py-0.5 px-1.5 d-inline-flex align-items-center gap-1"
                                          title="Belum dikirim via WhatsApp ke Customer"
                                          style="font-size: 0.68rem; font-weight: 500;">
                                        <i class="bi bi-dash-circle text-muted"></i> Belum ke Customer
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Type Unit & Warna --}}
                        <td>
                            @if($nota->isSparepart())
                                <span class="badge bg-success-subtle text-success border border-success-subtle mb-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-tools"></i> Sparepart
                                </span>
                                <div class="text-dark fw-bold mb-0.5 text-truncate" style="max-width: 220px;" title="{{ $nota->nama_barang }}">
                                    {{ $nota->nama_barang }}
                                </div>
                                <div class="small text-muted" style="font-size: 0.72rem;">
                                    {{ count($nota->items_list) }} macam part/jasa
                                </div>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-car-front"></i> Unit Mobil
                                </span>
                                <div class="text-dark fw-bold mb-0.5">
                                    {{ $nota->unit_type }}
                                </div>
                                <div class="small text-muted d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem;">
                                    <span class="badge bg-light text-dark border">{{ $nota->warna ?: '-' }}</span>
                                    <span>Thn {{ $nota->tahun_mobil ?: '2026' }}</span>
                                </div>
                            @endif
                        </td>

                        {{-- Skema Pembelian --}}
                        <td>
                            @if($nota->isSparepart())
                                <span class="badge bg-secondary-subtle text-secondary border fw-semibold mb-0.5" style="font-size: 0.72rem;">
                                    Tunai / Kasir
                                </span>
                                <div class="small text-muted" style="font-size: 0.72rem;">Toko / Servis</div>
                            @else
                                <span class="badge {{ $nota->cara_pembelian === 'Cash' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} fw-semibold mb-0.5" style="font-size: 0.72rem;">
                                    {{ $nota->cara_pembelian ?: 'Credit' }}
                                </span>
                                @if($nota->cara_pembelian !== 'Cash' && $nota->nama_leasing && $nota->nama_leasing !== '-')
                                    <div class="small text-muted" style="font-size: 0.72rem;">
                                        {{ $nota->nama_leasing }} {{ $nota->tenor && $nota->tenor !== '-' ? '• ' . $nota->tenor : '' }}
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- Total Bayar --}}
                        <td>
                            <div class="fw-bold text-success fs-6">
                                Rp {{ $nota->formatted_total_bayar }}
                            </div>
                            @if($nota->total_diskon > 0)
                                <div class="small text-danger" style="font-size: 0.7rem;">
                                    Diskon: Rp {{ $nota->formatted_total_diskon }}
                                </div>
                            @endif
                        </td>

                        {{-- Petugas Pembuat (Tampil jika Admin Kantor / Super Admin) --}}
                        @if(auth()->check() && !auth()->user()->isSales())
                            <td>
                                <div class="fw-semibold text-dark small">{{ $nota->user ? $nota->user->name : 'Sistem / Kantor' }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $nota->user ? $nota->user->role_label : 'Kantor Pusat' }}</small>
                            </td>
                        @endif

                        {{-- Aksi Dokumen --}}
                        <td class="text-end pe-4">
                            <div class="d-inline-flex flex-column align-items-end gap-1">
                                <div class="d-inline-flex align-items-center gap-2">

                                    {{-- 1. Tombol Detail --}}
                                    <a href="{{ route('nota.show', $nota->id) }}"
                                       class="btn btn-outline-dark btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5"
                                       style="font-size: 0.8rem;"
                                       title="Tinjau Detail Faktur & Bukti Struk">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>

                                    {{-- 2. Tombol Unduh / Lihat PDF --}}
                                    <a href="{{ route('nota.pdf', $nota->id) }}"
                                       target="_blank"
                                       class="btn btn-outline-danger btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5"
                                       style="font-size: 0.8rem;"
                                       title="Buka Berkas PDF Resmi">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>PDF</span>
                                    </a>

                                    {{-- 3. Tombol Kirim ke Bos (Khusus Super Admin & Admin Kantor) --}}
                                    @if(auth()->check() && !auth()->user()->isSales())
                                        <button type="button"
                                                class="btn btn-brand-primary btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5"
                                                style="font-size: 0.8rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKirimBos{{ $nota->id }}"
                                                title="Kirim Laporan Nota ke WhatsApp Pimpinan">
                                            <i class="bi bi-send"></i>
                                            <span>Kirim ke Bos</span>
                                        </button>
                                    @endif

                                    {{-- 4. Menu Dropdown: Kelola & Customer --}}
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-light btn-sm border text-secondary py-1.5 px-3 d-inline-flex align-items-center gap-1 dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                style="font-size: 0.8rem;">
                                            <span>Kelola</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1" style="font-size: 0.8125rem;">
                                            {{-- WhatsApp Customer --}}
                                            @if($nota->nomor_wa)
                                                <li>
                                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between gap-2"
                                                       href="{{ route('nota.share', $nota->id) }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       onclick="if(typeof updateBadgeCustomerTerkirim==='function'){ updateBadgeCustomerTerkirim({{ $nota->id }}); }">
                                                        <span class="d-flex align-items-center gap-2">
                                                            <i class="bi bi-whatsapp text-success"></i>
                                                            <span>{{ $nota->isTerkirimCustomer() ? 'Kirim Ulang ke Customer' : 'Kirim WA ke Customer' }}</span>
                                                        </span>
                                                        @if($nota->isTerkirimCustomer())
                                                            <i class="bi bi-check2 text-success fw-bold"></i>
                                                        @endif
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <span class="dropdown-item py-1.5 px-3 text-muted disabled">
                                                        <i class="bi bi-whatsapp"></i> Tanpa No. WhatsApp
                                                    </span>
                                                </li>
                                            @endif

                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li class="dropdown-header py-1 px-3 text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748b;">
                                                Ubah Status Manual
                                            </li>
                                            <li>
                                                <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="target" value="customer">
                                                    <input type="hidden" name="action" value="{{ $nota->isTerkirimCustomer() ? 'reset' : 'sent' }}">
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2">
                                                        <i class="bi {{ $nota->isTerkirimCustomer() ? 'bi-arrow-counterclockwise text-warning' : 'bi-check-circle text-success' }}"></i>
                                                        <span>{{ $nota->isTerkirimCustomer() ? 'Tandai Belum ke Customer' : 'Tandai Sudah ke Customer' }}</span>
                                                    </button>
                                                </form>
                                            </li>
                                            @if(auth()->check() && !auth()->user()->isSales())
                                                <li>
                                                    <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="target" value="boss">
                                                        <input type="hidden" name="action" value="{{ $nota->isTerkirimBos() ? 'reset' : 'sent' }}">
                                                        <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2">
                                                            <i class="bi {{ $nota->isTerkirimBos() ? 'bi-arrow-counterclockwise text-warning' : 'bi-check-circle text-success' }}"></i>
                                                            <span>{{ $nota->isTerkirimBos() ? 'Tandai Belum ke Bos' : 'Tandai Sudah ke Bos' }}</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            <li><hr class="dropdown-divider my-1"></li>

                                            {{-- Edit --}}
                                            <li>
                                                <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2"
                                                   href="{{ route('nota.edit', $nota->id) }}">
                                                    <i class="bi bi-pencil text-secondary"></i>
                                                    <span>Edit Data Nota</span>
                                                </a>
                                            </li>

                                            {{-- Hapus (Khusus Super Admin) --}}
                                            @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                <li><hr class="dropdown-divider my-1"></li>
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
                                            @endif
                                        </ul>
                                    </div>

                                </div>

                                {{-- Status Pengiriman ke Bos --}}
                                @if(auth()->check() && !auth()->user()->isSales())
                                    <div class="d-flex align-items-center gap-1" id="badgeBosContainer{{ $nota->id }}">
                                        @if($nota->isTerkirimBos())
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-2 d-inline-flex align-items-center gap-1"
                                                  title="Terkirim ke Bos: {{ $nota->wa_bos_sent_at->format('d/m/Y H:i') }} WIB ({{ $nota->wa_bos_sent_count }}x)"
                                                  style="font-size: 0.68rem; font-weight: 500;">
                                                <i class="bi bi-check2-all text-success"></i> Terkirim ke Bos ({{ $nota->wa_bos_sent_at->format('d/m H:i') }})
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border py-0.5 px-2 d-inline-flex align-items-center gap-1"
                                                  title="Belum dikirim via WhatsApp ke pimpinan"
                                                  style="font-size: 0.68rem; font-weight: 500;">
                                                <i class="bi bi-clock text-muted"></i> Belum ke Bos
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Kirim ke Bos per Nota --}}
                    @if(auth()->check() && !auth()->user()->isSales())
                        @include('nota.partials.modal-kirim-bos', ['nota' => $nota, 'bosses' => $bosses])
                    @endif

                @empty
                    <tr>
                        <td colspan="{{ auth()->check() && !auth()->user()->isSales() ? '8' : '7' }}" class="text-center py-5">
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
                                    <a href="{{ route('nota.create') }}" class="btn btn-brand-accent btn-sm px-3.5 py-2 d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Input Transaksi Pertama</span>
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

<script>
function updateBadgeCustomerTerkirim(notaId) {
    const el = document.getElementById('badgeCustomerContainer' + notaId);
    if (el) {
        el.innerHTML = `
            <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-1.5 d-inline-flex align-items-center gap-1"
                  style="font-size: 0.68rem; font-weight: 500;">
                <i class="bi bi-check2-all text-success"></i> Terkirim ke Customer
            </span>
        `;
    }
}

function updateBadgeBosTerkirim(notaId) {
    const el = document.getElementById('badgeBosContainer' + notaId);
    if (el) {
        el.innerHTML = `
            <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-2 d-inline-flex align-items-center gap-1"
                  style="font-size: 0.68rem; font-weight: 500;">
                <i class="bi bi-check2-all text-success"></i> Terkirim ke Bos (Baru saja)
            </span>
        `;
    }
}
</script>

@endsection
