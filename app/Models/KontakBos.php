<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontakBos extends Model
{
    protected $table = 'kontak_bos';

    protected $fillable = [
        'nama',
        'nomor_wa',
        'jabatan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Format nomor WhatsApp untuk URL wa.me (awalan 62)
     */
    public function getWaLinkAttribute(): string
    {
        $nomor = preg_replace('/\D/', '', $this->nomor_wa ?? '');
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }
        return $nomor;
    }
}
