<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaKontan extends Model
{
    protected $fillable = [
        'nama_customer',
        'nama_barang',
        'nominal_pembayaran',
        'tanggal_upload',
        'bukti_pembayaran',
        'nama_file',
        'path_file',
        'nomor_wa',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'date',
            'nominal_pembayaran' => 'integer',
        ];
    }

    /**
     * Link WhatsApp langsung untuk Customer (Universal Link untuk iOS & Android)
     */
    public function getWaCustomerUrlAttribute(): ?string
    {
        $nomor = preg_replace('/\D/', '', $this->nomor_wa ?? '');
        if (empty($nomor)) {
            return null;
        }
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        $tanggal = $this->tanggal_upload ? \Carbon\Carbon::parse($this->tanggal_upload)->format('d-m-Y') : date('d-m-Y');
        $pdfUrl = route('nota.pdf', $this->id);

        $pesan = "Halo {$this->nama_customer},\n"
            . "Terima kasih atas transaksi Anda. Berikut rincian tanda bukti pembayaran nota kontan:\n\n"
            . "🧾 No. Nota: #" . str_pad($this->id, 5, '0', STR_PAD_LEFT) . "\n"
            . "📦 Barang/Jasa: {$this->nama_barang}\n"
            . "💰 Nominal: Rp " . number_format($this->nominal_pembayaran, 0, ',', '.') . "\n"
            . "📅 Tanggal Pembayaran: {$tanggal}\n\n"
            . "📄 Buka & Tinjau Berkas Nota PDF Resmi Anda di sini:\n"
            . "{$pdfUrl}\n\n"
            . "Status: LUNAS. Simpan pesan ini sebagai bukti pembayaran yang sah.";

        return 'https://api.whatsapp.com/send?phone=' . $nomor . '&text=' . rawurlencode($pesan);
    }

    /**
     * Link WhatsApp langsung untuk Bos/Pimpinan (Universal Link untuk iOS & Android)
     */
    public function getWaBossUrl(KontakBos $boss): string
    {
        $nomor = $boss->wa_link;
        $jabatan = $boss->jabatan ? " ({$boss->jabatan})" : '';
        $tanggal = $this->tanggal_upload ? \Carbon\Carbon::parse($this->tanggal_upload)->format('d-m-Y') : date('d-m-Y');
        $pdfUrl = route('nota.pdf', $this->id);

        $pesan = "Halo {$boss->nama}{$jabatan},\n"
            . "Berikut laporan nota pembayaran transaksi baru:\n\n"
            . "🧾 No. Nota: #" . str_pad($this->id, 5, '0', STR_PAD_LEFT) . "\n"
            . "👤 Customer: {$this->nama_customer}\n"
            . "📦 Barang: {$this->nama_barang}\n"
            . "💰 Nominal: Rp " . number_format($this->nominal_pembayaran, 0, ',', '.') . "\n"
            . "📅 Tanggal: {$tanggal}\n\n"
            . "📄 Dokumen Nota PDF resmi dapat langsung dilihat di tautan berikut:\n"
            . "{$pdfUrl}\n\n"
            . "Laporan ini dikirim otomatis melalui Aplikasi Nota Kontan.";

        return 'https://api.whatsapp.com/send?phone=' . $nomor . '&text=' . rawurlencode($pesan);
    }
}
