@extends('layouts.app')

@section('title', 'Detail Konfirmasi DO #' . str_pad($nota->id, 5, '0', STR_PAD_LEFT))

@section('content')

{{-- Breadcrumb & Back --}}
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5" title="Kembali ke Daftar">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h5 fw-bold text-dark mb-0">
                Konfirmasi DO #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
            <span class="text-muted small">ASTRA DAIHATSU Palembang Veteran &bull; Tinjauan Pesanan & Bukti Bayar</span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('nota.pdf', $nota->id) }}" target="_blank" class="btn btn-outline-danger btn-sm py-1.5 px-3">
            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak / PDF
        </a>
        @if(auth()->check() && !auth()->user()->isSales())
            <button type="button" class="btn btn-brand-primary btn-sm py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#modalKirimBos{{ $nota->id }}">
                <i class="bi bi-send me-1"></i> Kirim ke Bos
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Rincian Data Transaksi --}}
    <div class="col-lg-7">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <div>
                    @if($nota->isSparepart())
                        <span class="text-uppercase small fw-bold text-success" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                            <i class="bi bi-tools me-1"></i> Penjualan Sparepart & Servis
                        </span>
                        <div class="fw-bold text-dark fs-6">{{ $nota->nama_barang }}</div>
                    @else
                        <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.05em; font-size: 0.72rem;">Data Pembelian Mobil</span>
                        <div class="fw-bold text-dark fs-6">{{ $nota->unit_type }}</div>
                    @endif
                </div>
                @if($nota->isSparepart())
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-semibold">
                        Lunas / Toko
                    </span>
                @else
                    <span class="badge {{ $nota->cara_pembelian === 'Cash' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2.5 py-1 fw-semibold">
                        {{ $nota->cara_pembelian ?: 'Credit' }}
                    </span>
                @endif
            </div>

            <table class="table table-borderless align-middle mb-0">
                <tbody>
                    <tr>
                        <td class="text-muted small py-2" style="width: 35%;">No. Registrasi Nota</td>
                        <td class="fw-bold text-dark py-2">
                            #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted small py-2">Nama Customer</td>
                        <td class="fw-bold text-dark py-2 fs-6">
                            {{ $nota->nama_customer ?? '-' }}
                        </td>
                    </tr>
                    @if(!$nota->isSparepart())
                        <tr class="border-top">
                            <td class="text-muted small py-2">Nama STNK / BPKB</td>
                            <td class="fw-semibold text-dark py-2">
                                {{ $nota->stnk_bpkb_name }}
                            </td>
                        </tr>
                    @endif
                    <tr class="border-top">
                        <td class="text-muted small py-2">No. HP / WhatsApp</td>
                        <td class="py-2">
                            @if($nota->nomor_wa)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $nota->nomor_wa) }}" target="_blank" class="text-decoration-none text-success fw-semibold small d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-whatsapp"></i> {{ $nota->nomor_wa }}
                                </a>
                            @else
                                <span class="text-muted small">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @if(!$nota->isSparepart())
                        <tr class="border-top">
                            <td class="text-muted small py-2">Type Unit Mobil</td>
                            <td class="fw-bold text-dark py-2">
                                {{ $nota->unit_type }}
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted small py-2">Warna Unit</td>
                            <td class="py-2">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $nota->warna ?: '-' }}</span>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted small py-2">Tahun Mobil</td>
                            <td class="py-2 text-dark fw-medium">
                                {{ $nota->tahun_mobil ?: '2026' }}
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted small py-2">Cara Pembelian</td>
                            <td class="py-2 fw-semibold text-dark">
                                {{ $nota->cara_pembelian ?: 'Credit' }}
                            </td>
                        </tr>
                        @if($nota->cara_pembelian !== 'Cash')
                            <tr class="border-top">
                                <td class="text-muted small py-2">Nama Leasing</td>
                                <td class="py-2 fw-semibold text-primary">
                                    {{ $nota->nama_leasing ?: '-' }}
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted small py-2">Tenor Pembiayaan</td>
                                <td class="py-2 text-dark">
                                    {{ $nota->tenor ?: '-' }}
                                </td>
                            </tr>
                        @endif
                    @endif
                    <tr class="border-top">
                        <td class="text-muted small py-2">Total Diskon</td>
                        <td class="py-2 text-danger fw-semibold">
                            Rp {{ $nota->formatted_total_diskon }}
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted small py-2">Tanggal Transaksi</td>
                        <td class="text-dark py-2">
                            {{ $nota->tanggal_upload ? \Carbon\Carbon::parse($nota->tanggal_upload)->format('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted small py-2">Petugas Pembuat</td>
                        <td class="py-2">
                            <span class="fw-semibold text-dark">{{ $nota->user ? $nota->user->name : 'Sistem / Kantor' }}</span>
                            @if($nota->user)
                                <span class="badge {{ $nota->user->role_badge_class }} ms-1" style="font-size: 0.68rem;">{{ $nota->user->role_label }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-top" style="background-color: #f8fafc;">
                        <td class="text-muted fw-semibold py-3 ps-2">Total Bayar</td>
                        <td class="fw-bold text-success fs-5 py-3 pe-2">
                            Rp {{ $nota->formatted_total_bayar }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Tabel Rincian Barang jika Sparepart --}}
            @if($nota->isSparepart())
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark small text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        <i class="bi bi-box-seam me-1"></i> Rincian Barang / Sparepart Dibeli:
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35px;" class="text-center">#</th>
                                    <th>Nama Part / Barang</th>
                                    <th style="width: 70px;" class="text-center">Qty</th>
                                    <th style="width: 120px;" class="text-end">Harga Satuan</th>
                                    <th style="width: 130px;" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nota->items_list as $idx => $it)
                                    <tr>
                                        <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $it['nama'] ?? '-' }}</td>
                                        <td class="text-center">{{ $it['qty'] ?? 1 }}</td>
                                        <td class="text-end">Rp {{ number_format((int)($it['harga'] ?? 0), 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success">Rp {{ number_format((int)($it['subtotal'] ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Action buttons bar --}}
            <div class="d-flex flex-wrap gap-2 pt-4 mt-3 border-top">
                @if($nota->nomor_wa)
                    <a href="{{ route('nota.share', $nota->id) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm py-1.5 px-3">
                        <i class="bi bi-whatsapp me-1"></i> {{ $nota->isTerkirimCustomer() ? 'Kirim Ulang ke Customer' : 'Kirim Format Konfirmasi ke Customer' }}
                    </a>
                @endif

                <a href="{{ route('nota.edit', $nota->id) }}" class="btn btn-light btn-sm text-secondary border py-1.5 px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Data
                </a>

                @if(auth()->check() && auth()->user()->isSuperAdmin())
                    <form action="{{ route('nota.destroy', $nota->id) }}" method="POST" class="d-inline ms-auto" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data transaksi ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm py-1.5 px-3">
                            <i class="bi bi-trash3"></i> Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Pratinjau Dokumen & Foto Bukti Fisik --}}
    <div class="col-lg-5">
        {{-- Card Status Distribusi / Pengiriman WhatsApp --}}
        <div class="card card-custom p-3.5 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <i class="bi bi-broadcast text-primary me-1"></i> Status Pengiriman WhatsApp
                </span>
                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">Live Tracking</span>
            </div>

            <div class="d-flex flex-column gap-2.5">
                {{-- Status Customer --}}
                <div class="p-2.5 rounded-3 border d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <div class="fw-semibold small text-dark d-flex align-items-center gap-1.5">
                            <i class="bi bi-person-check text-muted"></i>
                            <span>Pesan ke Customer</span>
                        </div>
                        <div class="small mt-0.5" style="font-size: 0.72rem;">
                            @if($nota->isTerkirimCustomer())
                                <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-1.5">
                                    <i class="bi bi-check2-all"></i> Terkirim: {{ $nota->wa_customer_sent_at->format('d/m/Y H:i') }} WIB ({{ $nota->wa_customer_sent_count }}x)
                                </span>
                            @else
                                <span class="badge bg-light text-muted border py-0.5 px-1.5">
                                    <i class="bi bi-dash-circle"></i> Belum pernah dikirim
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        @if($nota->nomor_wa)
                            <a href="{{ route('nota.share', $nota->id) }}" target="_blank" class="btn btn-outline-success btn-sm py-1 px-2.5" style="font-size: 0.75rem;">
                                <i class="bi bi-whatsapp me-1"></i> {{ $nota->isTerkirimCustomer() ? 'Kirim Ulang' : 'Kirim' }}
                            </a>
                        @endif
                        <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="target" value="customer">
                            <input type="hidden" name="action" value="{{ $nota->isTerkirimCustomer() ? 'reset' : 'sent' }}">
                            <button type="submit" class="btn btn-light border btn-sm py-1 px-2 text-muted" style="font-size: 0.72rem;" title="{{ $nota->isTerkirimCustomer() ? 'Reset ke Belum Terkirim' : 'Tandai Sudah Terkirim' }}">
                                <i class="bi {{ $nota->isTerkirimCustomer() ? 'bi-arrow-counterclockwise' : 'bi-check2' }}"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Status Bos (khusus non-sales) --}}
                @if(auth()->check() && !auth()->user()->isSales())
                    <div class="p-2.5 rounded-3 border d-flex justify-content-between align-items-center bg-white">
                        <div>
                            <div class="fw-semibold small text-dark d-flex align-items-center gap-1.5">
                                <i class="bi bi-briefcase text-muted"></i>
                                <span>Laporan ke Pimpinan / Bos</span>
                            </div>
                            <div class="small mt-0.5" style="font-size: 0.72rem;">
                                @if($nota->isTerkirimBos())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-1.5">
                                        <i class="bi bi-check2-all"></i> Terkirim: {{ $nota->wa_bos_sent_at->format('d/m/Y H:i') }} WIB ({{ $nota->wa_bos_sent_count }}x)
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border py-0.5 px-1.5">
                                        <i class="bi bi-clock"></i> Belum pernah dikirim
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-brand-primary btn-sm py-1 px-2.5" data-bs-toggle="modal" data-bs-target="#modalKirimBos{{ $nota->id }}" style="font-size: 0.75rem;">
                                <i class="bi bi-send me-1"></i> {{ $nota->isTerkirimBos() ? 'Kirim Ulang' : 'Kirim' }}
                            </button>
                            <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="target" value="boss">
                                <input type="hidden" name="action" value="{{ $nota->isTerkirimBos() ? 'reset' : 'sent' }}">
                                <button type="submit" class="btn btn-light border btn-sm py-1 px-2 text-muted" style="font-size: 0.72rem;" title="{{ $nota->isTerkirimBos() ? 'Reset ke Belum Terkirim' : 'Tandai Sudah Terkirim' }}">
                                    <i class="bi {{ $nota->isTerkirimBos() ? 'bi-arrow-counterclockwise' : 'bi-check2' }}"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Pratinjau Pesan WA Konfirmasi --}}
        <div class="card card-custom p-3.5 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <i class="bi bi-chat-text text-success me-1"></i> Teks Konfirmasi {{ $nota->isSparepart() ? 'Sparepart & Servis' : 'DO Mobil' }} (Format WA)
                </span>
                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">SOP Astra Daihatsu</span>
            </div>
            <div class="p-2.5 rounded-3 bg-light border" style="font-family: monospace; font-size: 0.75rem; white-space: pre-wrap; line-height: 1.5; color: #334155;">{{ $nota->getCustomerWaMessage() }}</div>
        </div>

        {{-- Card Bukti Pembayaran / Struk Fisik --}}
        <div class="card card-custom p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.05em; font-size: 0.72rem;">Bukti Transaksi Fisik / Transfer</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    Terlampir
                </span>
            </div>

            @if($nota->bukti_pembayaran)
                <div class="text-center">
                    <a href="{{ asset('storage/' . $nota->bukti_pembayaran) }}" target="_blank" title="Klik untuk memperbesar foto">
                        <img src="{{ asset('storage/' . $nota->bukti_pembayaran) }}"
                             alt="Bukti Pembayaran"
                             class="img-fluid rounded border shadow-sm mb-3"
                             style="max-height: 380px; width: 100%; object-fit: contain; background-color: #f8fafc;">
                    </a>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ asset('storage/' . $nota->bukti_pembayaran) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-1.5 px-3">
                            <i class="bi bi-arrows-fullscreen me-1"></i> Buka Foto Penuh
                        </a>
                        <a href="{{ asset('storage/' . $nota->bukti_pembayaran) }}" download class="btn btn-outline-primary btn-sm py-1.5 px-3">
                            <i class="bi bi-download me-1"></i> Unduh Foto
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-image fs-1 d-block mb-2 text-secondary"></i>
                    <p class="small mb-0">Tidak ada bukti fisik yang dilampirkan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Include Modal Kirim ke WhatsApp Bos --}}
@include('nota.partials.modal-kirim-bos', ['nota' => $nota, 'bosses' => $bosses])

@endsection
