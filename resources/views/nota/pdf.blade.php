<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Kontan #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}</title>

    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-badge {
            text-align: right;
        }

        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        .doc-date {
            font-size: 11px;
            color: #64748b;
        }

        .status-pill {
            display: inline-block;
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .meta-table td {
            vertical-align: top;
            padding: 4px 0;
        }

        .meta-label {
            width: 130px;
            color: #64748b;
            font-weight: normal;
        }

        .meta-value {
            font-weight: bold;
            color: #0f172a;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            font-weight: bold;
            text-align: left;
            padding: 10px 12px;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #cbd5e1;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .total-box {
            width: 100%;
            margin-bottom: 30px;
        }

        .total-box td {
            padding: 10px 12px;
        }

        .total-label {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            background-color: #f8fafc;
        }

        .total-value {
            text-align: right;
            font-size: 15px;
            font-weight: bold;
            color: #059669;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            background-color: #f8fafc;
            width: 180px;
        }

        .attachment-section {
            page-break-inside: avoid;
            margin-top: 25px;
            padding: 15px;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            background-color: #fdfdfd;
            text-align: center;
        }

        .attachment-title {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .attachment-img {
            max-width: 280px;
            max-height: 320px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .footer-note {
            margin-top: 35px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-title">NOTA KONTAN</div>
                <div class="brand-subtitle">Tanda Bukti Penerimaan Pembayaran Tunai</div>
            </td>
            <td class="doc-badge" style="vertical-align: middle;">
                <div class="doc-number">#NK-{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="doc-date">Tanggal: {{ $nota->tanggal_upload ? \Carbon\Carbon::parse($nota->tanggal_upload)->format('d/m/Y') : '-' }}</div>
                <div><span class="status-pill">LUNAS</span></div>
            </td>
        </tr>
    </table>

    {{-- Customer & Transaction Meta --}}
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="meta-label">Diterima Dari</td>
            <td class="meta-value">: {{ $nota->nama_customer ?? '-' }}</td>
        </tr>
        @if($nota->nomor_wa)
        <tr>
            <td class="meta-label">Kontak WhatsApp</td>
            <td>: {{ $nota->nomor_wa }}</td>
        </tr>
        @endif
        <tr>
            <td class="meta-label">Tanggal Pelaporan</td>
            <td>: {{ $nota->created_at ? $nota->created_at->format('d F Y, H:i') . ' WIB' : '-' }}</td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">No.</th>
                <th>Rincian Barang / Jasa</th>
                <th style="width: 60px; text-align: center;">Qty</th>
                <th style="width: 120px; text-align: right;">Harga Satuan</th>
                <th style="width: 130px; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nota->items_list as $index => $item)
                <tr>
                    <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $item['nama'] ?? '-' }}</strong>
                    </td>
                    <td style="text-align: center; color: #64748b;">
                        {{ $item['qty'] ?? 1 }}
                    </td>
                    <td style="text-align: right; color: #64748b;">
                        Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #0f172a;">
                        Rp {{ number_format($item['subtotal'] ?? (($item['qty'] ?? 1) * ($item['harga'] ?? 0)), 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total Box --}}
    <table class="total-box" cellpadding="0" cellspacing="0">
        <tr>
            <td class="total-label">TOTAL PEMBAYARAN</td>
            <td class="total-value">
                Rp {{ number_format($nota->nominal_pembayaran ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- Lampiran Bukti Fisik / Struk --}}
    @if($nota->bukti_pembayaran)
        @php
            $path = storage_path('app/public/' . $nota->bukti_pembayaran);
        @endphp

        @if(file_exists($path))
            <div class="attachment-section">
                <div class="attachment-title">Lampiran Bukti Transaksi Fisik / Transfer</div>
                <img src="{{ $path }}" alt="Bukti Pembayaran" class="attachment-img">
            </div>
        @endif
    @endif

    {{-- Footer --}}
    <div class="footer-note">
        Dokumen ini dibuat dan diterbitkan secara digital oleh Sistem Aplikasi Nota Kontan &bull; Sah tanpa tanda tangan basah.
    </div>

</body>
</html>
