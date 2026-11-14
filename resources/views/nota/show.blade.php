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
    {{-- Kolom Kiri: Rincian Data Konfirmasi DO --}}
    <div class="col-lg-7">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <div>
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.05em; font-size: 0.72rem;">Data Pembelian Mobil</span>
                    <div class="fw-bold text-dark fs-6">{{ $nota->unit_type }}</div>
                </div>
                <span class="badge {{ $nota->cara_pembelian === 'Cash' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2.5 py-1 fw-semibold">
                    {{ $nota->cara_pembelian ?: 'Credit' }}
                </span>
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
                    <tr class="border-top">
                        <td class="text-muted small py-2">Nama STNK / BPKB</td>
                        <td class="fw-semibold text-dark py-2">
                            {{ $nota->stnk_bpkb_name }}
                        </td>
                    </tr>
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
                        <td class="text-muted fw-semibold py-3 ps-2">Total Bayar (DP / Pelunasan)</td>
                        <td class="fw-bold text-success fs-5 py-3 pe-2">
                            Rp {{ $nota->formatted_total_bayar }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Action buttons bar --}}
            <div class="d-flex flex-wrap gap-2 pt-4 mt-3 border-top">
                @if($nota->nomor_wa && $nota->wa_customer_url)
                    <a href="{{ $nota->wa_customer_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm py-1.5 px-3">
                        <i class="bi bi-whatsapp me-1"></i> Kirim Format Konfirmasi ke Customer
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
        {{-- Card Pratinjau Pesan WA Konfirmasi --}}
        <div class="card card-custom p-3.5 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <i class="bi bi-chat-text text-success me-1"></i> Teks Konfirmasi DO (Format WA)
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
