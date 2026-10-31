@extends('layouts.app')

@section('title', 'Detail Nota #' . str_pad($nota->id, 5, '0', STR_PAD_LEFT))

@section('content')

{{-- Breadcrumb & Back --}}
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5" title="Kembali ke Daftar">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h5 fw-bold text-dark mb-0">
                Detail Transaksi #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
            <span class="text-muted small">Tinjauan dokumen nota dan lampiran bukti fisik</span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('nota.pdf', $nota->id) }}" target="_blank" class="btn btn-outline-danger btn-sm py-1.5 px-3">
            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak / PDF
        </a>
        <button type="button" class="btn btn-brand-primary btn-sm py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#modalKirimBos{{ $nota->id }}">
            <i class="bi bi-send me-1"></i> Kirim ke Bos
        </button>
    </div>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Rincian Faktur --}}
    <div class="col-lg-7">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.05em;">Informasi Pembayaran</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-semibold">
                    Lunas / Terverifikasi
                </span>
            </div>

            <table class="table table-borderless align-middle mb-0">
                <tbody>
                    <tr>
                        <td class="text-muted small py-2.5" style="width: 35%;">No. Registrasi Nota</td>
                        <td class="fw-bold text-dark py-2.5">
                            #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted small py-2.5">Nama Customer</td>
                        <td class="fw-bold text-dark py-2.5 fs-6">
                            {{ $nota->nama_customer ?? '-' }}
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted small py-2.5">Nomor WhatsApp</td>
                        <td class="py-2.5">
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
                        <td class="text-muted small py-2.5 align-top">Rincian Barang / Jasa</td>
                        <td class="py-2.5">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30px;" class="text-center">No</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center" style="width: 70px;">Qty</th>
                                            <th class="text-end" style="width: 110px;">Harga</th>
                                            <th class="text-end" style="width: 120px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nota->items_list as $idx => $it)
                                            <tr>
                                                <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                                <td class="fw-medium text-dark">{{ $it['nama'] ?? '-' }}</td>
                                                <td class="text-center">{{ $it['qty'] ?? 1 }}</td>
                                                <td class="text-end">Rp {{ number_format($it['harga'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end fw-semibold text-dark">
                                                    Rp {{ number_format($it['subtotal'] ?? (($it['qty'] ?? 1) * ($it['harga'] ?? 0)), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted small py-2.5">Tanggal Transaksi</td>
                        <td class="text-dark py-2.5">
                            {{ $nota->tanggal_upload ? \Carbon\Carbon::parse($nota->tanggal_upload)->format('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr class="border-top" style="background-color: #f8fafc;">
                        <td class="text-muted fw-semibold py-3 ps-2">Total Pembayaran</td>
                        <td class="fw-bold text-success fs-5 py-3 pe-2">
                            Rp {{ number_format($nota->nominal_pembayaran ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Action buttons bar --}}
            <div class="d-flex flex-wrap gap-2 pt-4 mt-3 border-top">
                @if($nota->nomor_wa && $nota->wa_customer_url)
                    <a href="{{ $nota->wa_customer_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm py-1.5 px-3">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp Customer
                    </a>
                @endif

                <a href="{{ route('nota.edit', $nota->id) }}" class="btn btn-light btn-sm text-secondary border py-1.5 px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Data
                </a>

                <form action="{{ route('nota.destroy', $nota->id) }}" method="POST" class="d-inline ms-auto" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data nota ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm py-1.5 px-3">
                        <i class="bi bi-trash3 me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Pratinjau Foto Bukti Pembayaran --}}
    <div class="col-lg-5">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.05em;">Lampiran Struk / Bukti</span>
                @if($nota->bukti_pembayaran)
                    <a href="{{ asset('storage/' . $nota->bukti_pembayaran) }}" target="_blank" class="text-decoration-none small text-primary fw-medium" title="Buka dalam ukuran penuh">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Ukuran Penuh
                    </a>
                @endif
            </div>

            @if ($nota->bukti_pembayaran)
                <div class="text-center p-2 rounded-3 border bg-light">
                    <img src="{{ asset('storage/' . $nota->bukti_pembayaran) }}"
                         alt="Foto Bukti Pembayaran"
                         class="img-fluid rounded"
                         style="max-height: 420px; width: auto; object-fit: contain;">
                </div>
                <div class="small text-muted mt-2 text-center">
                    {{ $nota->nama_file ?? 'Bukti transaksi terlampir' }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-image fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="small mb-0">Foto bukti pembayaran tidak tersedia.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Kirim ke Bos --}}
@include('nota.partials.modal-kirim-bos', ['nota' => $nota, 'bosses' => $bosses])

@endsection
