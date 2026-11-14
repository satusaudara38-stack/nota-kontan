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
        // Seed Pengguna Default untuk 3 Role
        if (User::where('email', 'admin@notakontan.com')->doesntExist()) {
            User::create([
                'name' => 'Administrator Utama',
                'email' => 'admin@notakontan.com',
                'password' => bcrypt('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
            ]);
        }

        if (User::where('email', 'kantor@notakontan.com')->doesntExist()) {
            User::create([
                'name' => 'Admin Kantor',
                'email' => 'kantor@notakontan.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin_kantor',
                'is_active' => true,
            ]);
        }

        if (User::where('email', 'sales@notakontan.com')->doesntExist()) {
            User::create([
                'name' => 'Sales Lapangan',
                'email' => 'sales@notakontan.com',
                'password' => bcrypt('sales123'),
                'role' => 'sales',
                'is_active' => true,
            ]);
        }

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
