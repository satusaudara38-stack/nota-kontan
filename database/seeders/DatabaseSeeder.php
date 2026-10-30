<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed kontak pimpinan default jika masih kosong
        if (\App\Models\KontakBos::count() === 0) {
            \App\Models\KontakBos::create([
                'nama' => 'Pak Ariq',
                'nomor_wa' => '087887144617',
                'jabatan' => 'Pimpinan',
                'is_active' => true,
            ]);
            \App\Models\KontakBos::create([
                'nama' => 'Pak Danu',
                'nomor_wa' => '089628218727',
                'jabatan' => 'Keuangan',
                'is_active' => true,
            ]);
            \App\Models\KontakBos::create([
                'nama' => 'Bos A.A',
                'nomor_wa' => '085942354726',
                'jabatan' => 'Operasional',
                'is_active' => true,
            ]);
        }
    }
}
