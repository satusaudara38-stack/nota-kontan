<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaKontan extends Model
{
    protected $fillable = [
        'user_id',
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'date',
            'nominal_pembayaran' => 'integer',
            'total_diskon' => 'integer',
            'items' => 'array',
        ];
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
        
        $stnk = $this->stnk_bpkb_name;
        $unit = $this->unit_type;
        $warna = $this->warna ?: '-';
        $totalBayar = $this->formatted_total_bayar;
        $totalDiskon = $this->formatted_total_diskon;
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
            . "Timestamp : {$timestamp}";
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
        $stnk = $this->stnk_bpkb_name;
        $unit = $this->unit_type;
        $warna = $this->warna ?: '-';
        $totalBayar = $this->formatted_total_bayar;
        $totalDiskon = $this->formatted_total_diskon;
        $cara = $this->cara_pembelian ?: 'Credit';
        $leasing = ($cara === 'Cash') ? '-' : ($this->nama_leasing ?: '-');
        $tenor = ($cara === 'Cash') ? '-' : ($this->tenor ?: '-');
        $tahun = $this->tahun_mobil ?: '2026';

        return "Yth Bapak/Ibu. {$customer}\n\n"
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
