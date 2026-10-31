<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Kontan #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }} - {{ $nota->nama_customer ?? 'Customer' }}</title>

    {{-- Bootstrap 5.3 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .paper-invoice {
            max-width: 800px;
            margin: 24px auto 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            padding: 40px;
        }

        .brand-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .brand-logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .table-custom-invoice th {
            background-color: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            padding: 12px 14px;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #cbd5e1;
        }

        .table-custom-invoice td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .total-banner {
            background: #f8fafc;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            padding: 16px 20px;
        }

        @media (max-width: 576px) {
            .paper-invoice {
                margin: 12px 8px 30px 8px;
                padding: 20px 16px;
                border-radius: 8px;
            }
            .brand-logo-text {
                font-size: 1.2rem;
            }
        }

        /* Print styling: Hanya cetak kertas faktur, sembunyikan bar navigasi */
        @media print {
            body {
                background: #ffffff !important;
            }
            .no-print {
                display: none !important;
            }
            .paper-invoice {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar (Hanya tampil di layar web, otomatis hilang saat diprint) --}}
    <nav class="navbar navbar-dark no-print sticky-top py-2 px-3 shadow-sm" style="background-color: #0f172a;">
        <div class="container-fluid max-w-custom d-flex justify-content-between align-items-center" style="max-width: 840px;">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/') }}" class="text-white text-decoration-none fw-bold small d-flex align-items-center gap-1.5" title="Kembali ke Aplikasi">
                    <i class="bi bi-arrow-left"></i>
                    <span>Nota Kontan</span>
                </a>
                <span class="badge bg-white text-dark fw-bold border px-2.5 py-1 shadow-sm" style="font-size: 0.8rem;">
                    #NK-{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Tombol Cetak --}}
                <button type="button" onclick="window.print()" class="btn btn-outline-light btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                    <i class="bi bi-printer"></i>
                    <span class="d-none d-sm-inline">Cetak</span>
                </button>

                {{-- Tombol Unduh PDF Resmi --}}
                <a href="{{ route('nota.pdf', ['nota' => $nota->id, 'download' => 1]) }}" class="btn btn-success btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5 fw-semibold shadow-sm" style="font-size: 0.8rem;">
                    <i class="bi bi-download"></i>
                    <span>Unduh PDF</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- Kertas Faktur Resmi (Paper Invoice) --}}
    <div class="container px-2">
        <div class="paper-invoice">

            {{-- Header Faktur --}}
            <div class="brand-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <div>
                    <div class="brand-logo-text">NOTA KONTAN</div>
                    <div class="text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Tanda Bukti Penerimaan Pembayaran Tunai
                    </div>
                </div>

                <div class="text-start text-sm-end">
                    <div class="fw-bold fs-6 text-dark">
                        #NK-{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="text-muted small" style="font-size: 0.78rem;">
                        Tanggal: {{ $nota->tanggal_upload ? \Carbon\Carbon::parse($nota->tanggal_upload)->format('d/m/Y') : '-' }}
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold mt-1" style="font-size: 0.72rem;">
                        LUNAS
                    </span>
                </div>
            </div>

            {{-- Metadata Transaksi & Customer --}}
            <div class="row g-2 mb-4 small">
                <div class="col-sm-6">
                    <div class="text-muted" style="font-size: 0.75rem;">Diterima Dari (Customer):</div>
                    <div class="fw-bold text-dark fs-6">{{ $nota->nama_customer ?? '-' }}</div>
                    @if($nota->nomor_wa)
                        <div class="text-muted d-flex align-items-center gap-1 mt-0.5">
                            <i class="bi bi-whatsapp text-success"></i>
                            <span>{{ $nota->nomor_wa }}</span>
                        </div>
                    @endif
                </div>

                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <div class="text-muted" style="font-size: 0.75rem;">Tanggal Pencatatan:</div>
                    <div class="fw-semibold text-dark">
                        {{ $nota->created_at ? $nota->created_at->format('d F Y, H:i') . ' WIB' : '-' }}
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Status Verifikasi: <span class="text-success fw-semibold">Terverifikasi Sah</span>
                    </div>
                </div>
            </div>

            {{-- Tabel Barang / Jasa --}}
            <div class="table-responsive mb-3">
                <table class="table table-custom-invoice align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">No.</th>
                            <th>Rincian Barang / Jasa</th>
                            <th style="width: 80px;" class="text-center">Qty</th>
                            <th style="width: 140px;" class="text-end">Harga Satuan</th>
                            <th style="width: 160px;" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nota->items_list as $index => $item)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item['nama'] ?? '-' }}</div>
                                </td>
                                <td class="text-center text-muted">
                                    {{ $item['qty'] ?? 1 }}
                                </td>
                                <td class="text-end text-muted">
                                    Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($item['subtotal'] ?? (($item['qty'] ?? 1) * ($item['harga'] ?? 0)), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Total Box --}}
            <div class="total-banner d-flex justify-content-between align-items-center rounded mb-4">
                <span class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.05em;">TOTAL PEMBAYARAN</span>
                <span class="fs-4 fw-bold text-success">
                    Rp {{ number_format($nota->nominal_pembayaran ?? 0, 0, ',', '.') }}
                </span>
            </div>

            {{-- Lampiran Bukti Fisik / Struk --}}
            @if($nota->bukti_pembayaran)
                <div class="p-3 border rounded-3 bg-light text-center mb-4">
                    <div class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <i class="bi bi-paperclip me-1"></i>Lampiran Bukti Transaksi Fisik / Transfer
                    </div>
                    <a href="{{ asset('storage/' . $nota->bukti_pembayaran) }}" target="_blank" title="Buka gambar ukuran penuh">
                        <img src="{{ asset('storage/' . $nota->bukti_pembayaran) }}" 
                             alt="Bukti Pembayaran" 
                             class="img-fluid rounded border shadow-sm" 
                             style="max-height: 400px; width: auto; object-fit: contain; background: #fff;">
                    </a>
                    <div class="small text-muted mt-2 no-print" style="font-size: 0.72rem;">
                        <i class="bi bi-zoom-in me-1"></i>Sentuh atau klik foto untuk melihat dalam resolusi asli
                    </div>
                </div>
            @endif

            {{-- Footer Catatan Dokumen --}}
            <div class="pt-3 mt-4 border-top text-center text-muted" style="font-size: 0.72rem;">
                Dokumen ini dibuat dan diterbitkan secara digital oleh Sistem Aplikasi Nota Kontan &bull; Sah sebagai tanda bukti transaksi penerimaan kas tanpa tanda tangan basah.
            </div>

        </div>
    </div>

</body>
</html>

