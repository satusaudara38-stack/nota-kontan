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
}