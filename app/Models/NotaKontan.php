<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaKontan extends Model
{
    protected $fillable = [
        'user_id',
        'kategori_transaksi',
        'nama_customer',
        'nama_stnk_bpkb',
        'type_unit',
        'warna',
        'nama_barang',
        'items',
        'nominal_pembayaran',
        'total_diskon',
        'cara_pembelian',
        'nama_leasing',
        'tenor',
        'tahun_mobil',
        'tanggal_upload',
        'bukti_pembayaran',
        'nama_file',
        'path_file',
        'nomor_wa',
        'wa_bos_sent_at',
        'wa_bos_sent_count',
        'wa_customer_sent_at',
        'wa_customer_sent_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Cek apakah transaksi merupakan penjualan suku cadang / sparepart
     */
    public function isSparepart(): bool
    {
        return ($this->kategori_transaksi === 'sparepart');
    }

    /**
     * Cek apakah transaksi merupakan delivery order mobil
     */
    public function isMobil(): bool
    {
        return !$this->isSparepart();
    }

    /**
     * Label Kategori Transaksi
     */
    public function getKategoriLabelAttribute(): string
    {
        return $this->isSparepart() ? 'Sparepart & Servis' : 'Unit Mobil (DO)';
    }

    /**
     * Badge CSS class untuk Kategori Transaksi
     */
    public function getKategoriBadgeClassAttribute(): string
    {
        return $this->isSparepart() 
            ? 'bg-success-subtle text-success border border-success-subtle' 
            : 'bg-primary-subtle text-primary border border-primary-subtle';
    }

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'date',
            'nominal_pembayaran' => 'integer',
            'total_diskon' => 'integer',
            'items' => 'array',
            'wa_bos_sent_at' => 'datetime',
            'wa_customer_sent_at' => 'datetime',
            'wa_bos_sent_count' => 'integer',
            'wa_customer_sent_count' => 'integer',
        ];
    }

    /**
     * Status Pengiriman WA ke Bos
     */
    public function isTerkirimBos(): bool
    {
        return !is_null($this->wa_bos_sent_at);
    }

    /**
     * Status Pengiriman WA ke Customer
     */
    public function isTerkirimCustomer(): bool
    {
        return !is_null($this->wa_customer_sent_at);
    }

    /**
     * Tandai pengiriman ke Bos berhasil dibuka / dikirim
     */
    public function markSentToBoss(): void
    {
        $this->update([
            'wa_bos_sent_at' => now(),
            'wa_bos_sent_count' => ($this->wa_bos_sent_count ?? 0) + 1,
        ]);
    }

    /**
     * Tandai pengiriman ke Customer berhasil dibuka / dikirim
     */
    public function markSentToCustomer(): void
    {
        $this->update([
            'wa_customer_sent_at' => now(),
            'wa_customer_sent_count' => ($this->wa_customer_sent_count ?? 0) + 1,
        ]);
    }

    /**
     * Reset status pengiriman ke Bos
     */
    public function resetStatusBos(): void
    {
        $this->update([
            'wa_bos_sent_at' => null,
            'wa_bos_sent_count' => 0,
        ]);
    }

    /**
     * Reset status pengiriman ke Customer
     */
    public function resetStatusCustomer(): void
    {
        $this->update([
            'wa_customer_sent_at' => null,
            'wa_customer_sent_count' => 0,
        ]);
    }

    /**
     * Rincian nama STNK/BPKB
     */
    public function getStnkBpkbNameAttribute(): string
    {
        return $this->nama_stnk_bpkb ?: ($this->nama_customer ?: '-');
    }

    /**
     * Tipe unit kendaraan
     */
    public function getUnitTypeAttribute(): string
    {
        return $this->type_unit ?: ($this->nama_barang ?: '-');
    }

    /**
     * Format angka total bayar
     */
    public function getFormattedTotalBayarAttribute(): string
    {
        return number_format($this->nominal_pembayaran ?: 0, 0, ',', '.');
    }

    /**
     * Format angka total diskon
     */
    public function getFormattedTotalDiskonAttribute(): string
    {
        return number_format($this->total_diskon ?: 0, 0, ',', '.');
    }

    /**
     * Rincian barang / item yang dibeli (selalu berupa array terstruktur)
     */
    public function getItemsListAttribute(): array
    {
        if (!empty($this->items) && is_array($this->items)) {
            return $this->items;
        }

        $nama = $this->unit_type;
        if ($this->warna) {
            $nama .= ' (' . $this->warna . ')';
        }

        return [
            [
                'nama' => $nama,
                'qty' => 1,
                'harga' => (int) ($this->nominal_pembayaran ?: 0),
                'subtotal' => (int) ($this->nominal_pembayaran ?: 0),
            ]
        ];
    }

    /**
     * Teks pesan WhatsApp untuk Konfirmasi DO ke Customer
     */
    public function getCustomerWaMessage(): string
    {
        $timestamp = $this->created_at 
            ? $this->created_at->format('d/m/Y H:i:s') 
            : ($this->tanggal_upload ? $this->tanggal_upload->format('d/m/Y 12:00:00') : date('d/m/Y H:i:s'));

        $totalBayar = $this->formatted_total_bayar;
        $totalDiskon = $this->formatted_total_diskon;
        $pdfUrl = route('nota.pdf', $this->id);

        // Jika Transaksi Sparepart / Servis
        if ($this->isSparepart()) {
            $itemsText = "";
            $items = $this->items_list;
            foreach ($items as $idx => $it) {
                $nomor = $idx + 1;
                $nama = $it['nama'] ?? '-';
                $qty = $it['qty'] ?? 1;
                $harga = number_format((int)($it['harga'] ?? 0), 0, ',', '.');
                $subtotal = number_format((int)($it['subtotal'] ?? 0), 0, ',', '.');
                $itemsText .= "{$nomor}. {$nama} ({$qty}x @Rp {$harga}) = Rp {$subtotal}\n";
            }

            return "Data Konfirmasi Pembelian Sparepart / Servis\n"
                . "ASTRA DAIHATSU Palembang Veteran\n\n"
                . "No HP Customer : {$this->nomor_wa}\n"
                . "Nama Customer : {$this->nama_customer}\n"
                . "Kategori : Penjualan Sparepart & Servis\n\n"
                . "Rincian Pembelian:\n"
                . "{$itemsText}\n"
                . "Total Diskon : Rp {$totalDiskon}\n"
                . "Total Bayar : Rp {$totalBayar}\n"
                . "Status Pembayaran : Lunas (Cash / Transfer)\n"
                . "Timestamp : {$timestamp}\n\n"
                . "📄 Buka & Unduh Berkas Dokumen Nota PDF Resmi Anda:\n"
                . "{$pdfUrl}\n\n"
                . "Terima kasih telah mempercayakan perawatan kendaraan Anda di Astra Daihatsu.";
        }
        
        $stnk = $this->stnk_bpkb_name;
        $unit = $this->unit_type;
        $warna = $this->warna ?: '-';
        $cara = $this->cara_pembelian ?: 'Credit';
        $leasing = ($cara === 'Cash') ? '-' : ($this->nama_leasing ?: '-');
        $tenor = ($cara === 'Cash') ? '-' : ($this->tenor ?: '-');
        $tahun = $this->tahun_mobil ?: '2026';

        return "Data Konfirmasi DO ke Customer\n\n"
            . "No HP Customer : {$this->nomor_wa}\n"
            . "Nama Customer : {$this->nama_customer}\n"
            . "Nama STNK/BPKB : {$stnk}\n"
            . "Type Unit : {$unit}\n"
            . "Warna : {$warna}\n"
            . "Total Bayar : {$totalBayar}\n"
            . "Total Diskon : {$totalDiskon}\n"
            . "Cara Pembelian : {$cara}\n"
            . "Nama Leasing : {$leasing}\n"
            . "Tenor : {$tenor}\n"
            . "Tahun Mobil : {$tahun}\n"
            . "Timestamp : {$timestamp}\n\n"
            . "📄 Buka & Unduh Berkas Dokumen Konfirmasi DO / Nota PDF Resmi Anda:\n"
            . "{$pdfUrl}";
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

        $pesan = $this->getCustomerWaMessage();

        return 'https://api.whatsapp.com/send?phone=' . $nomor . '&text=' . rawurlencode($pesan);
    }

    /**
     * Teks pesan WhatsApp konfirmasi untuk Bos / Pimpinan
     */
    public function getBossWaMessage(KontakBos $boss = null): string
    {
        $customer = $this->nama_customer ?: '-';
        $totalBayar = $this->formatted_total_bayar;
        $totalDiskon = $this->formatted_total_diskon;
        $namaPimpinan = $boss ? $boss->nama : 'Pimpinan';
        $pdfUrl = route('nota.pdf', $this->id);

        // Jika Transaksi Sparepart / Servis
        if ($this->isSparepart()) {
            $itemsText = "";
            $items = $this->items_list;
            foreach ($items as $idx => $it) {
                $nomor = $idx + 1;
                $nama = $it['nama'] ?? '-';
                $qty = $it['qty'] ?? 1;
                $subtotal = number_format((int)($it['subtotal'] ?? 0), 0, ',', '.');
                $itemsText .= "{$nomor}. {$nama} ({$qty}x) = Rp {$subtotal}\n";
            }
            $petugas = $this->user ? $this->user->name : 'Admin Kantor';

            return "Yth Bapak/Ibu. {$namaPimpinan}\n\n"
                . "Kami dari ASTRA DAIHATSU Palembang Veteran\n"
                . "Laporan Kas Masuk - Penjualan Sparepart / Servis Kantor:\n\n"
                . "Nama Customer : {$customer}\n"
                . "No HP Customer : {$this->nomor_wa}\n"
                . "Kategori : Sparepart & Servis\n\n"
                . "Rincian Barang:\n"
                . "{$itemsText}\n"
                . "Total Diskon : Rp {$totalDiskon}\n"
                . "Total Kas Masuk : Rp {$totalBayar}\n"
                . "Petugas Input : {$petugas}\n\n"
                . "📄 Berkas Dokumen Nota PDF Digital:\n"
                . "{$pdfUrl}\n\n"
                . "Terimakasih atas perhatiannya.";
        }

        $stnk = $this->stnk_bpkb_name;
        $unit = $this->unit_type;
        $warna = $this->warna ?: '-';
        $cara = $this->cara_pembelian ?: 'Credit';
        $leasing = ($cara === 'Cash') ? '-' : ($this->nama_leasing ?: '-');
        $tenor = ($cara === 'Cash') ? '-' : ($this->tenor ?: '-');
        $tahun = $this->tahun_mobil ?: '2026';
        $sapaan = $boss ? $boss->nama : $customer;

        return "Yth Bapak/Ibu. {$sapaan}\n\n"
            . "Kami dari ASTRA DAIHATSU Palembang Veteran\n"
            . "Bersama ini kami ingin melakukan konfirmasi data pembelian mobil Bapak/Ibu di Astra Daihatsu\n"
            . "Mohon Konfirmasinya.\n"
            . "OK, jika data sesuai.\n"
            . "NO, Jika data tidak sesuai.\n\n"
            . "Nama Customer : {$customer}\n"
            . "Nama STNK/BPKB : {$stnk}\n"
            . "Type Unit : {$unit}\n"
            . "Warna : {$warna}\n"
            . "Total Bayar : {$totalBayar}\n"
            . "Total Diskon : {$totalDiskon}\n"
            . "Cara Pembelian : {$cara}\n"
            . "Nama Leasing : {$leasing}\n"
            . "Tenor : {$tenor}\n"
            . "Tahun Mobil : {$tahun}\n\n"
            . "📄 Berkas Dokumen Konfirmasi DO / Nota PDF Digital:\n"
            . "{$pdfUrl}\n\n"
            . "Terimakasih atas perhatiannya.";
    }

    /**
     * Link WhatsApp langsung untuk Bos/Pimpinan (Universal Link untuk iOS & Android)
     */
    public function getWaBossUrl(KontakBos $boss): string
    {
        $nomor = $boss->wa_link;
        $pesan = $this->getBossWaMessage($boss);

        return 'https://api.whatsapp.com/send?phone=' . $nomor . '&text=' . rawurlencode($pesan);
    }
}
