<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotaKontanController;
use App\Http\Controllers\KontakBosController;
use App\Http\Controllers\UserController;

// --- Rute Autentikasi Pengguna ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Dokumen E-Invoice Publik (Bisa diakses langsung oleh Customer & Pimpinan via tautan WA) ---
Route::get('/nota-kontan/{nota}/pdf', [NotaKontanController::class, 'downloadPdf'])->name('nota.pdf');

// --- Rute Terproteksi (Wajib Login) ---
Route::middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/', [NotaKontanController::class, 'dashboard'])->name('dashboard');

    // Modul Data Transaksi (Bisa diakses oleh Super Admin, Admin Kantor, dan Sales Lapangan)
    Route::get('/nota-kontan', [NotaKontanController::class, 'index'])->name('nota.index');
    Route::get('/nota-kontan/upload', [NotaKontanController::class, 'create'])->name('nota.create');
    Route::post('/nota-kontan', [NotaKontanController::class, 'store'])->name('nota.store');
    Route::get('/nota-kontan/{nota}', [NotaKontanController::class, 'show'])->name('nota.show');
    Route::get('/nota-kontan/{nota}/edit', [NotaKontanController::class, 'edit'])->name('nota.edit');
    Route::put('/nota-kontan/{nota}', [NotaKontanController::class, 'update'])->name('nota.update');

    // Berbagi Nota via WhatsApp & Pembaruan Status Terkirim
    Route::get('/nota-kontan/{nota}/whatsapp', [NotaKontanController::class, 'share'])->name('nota.share');
    Route::get('/nota-kontan/{nota}/share-boss', [NotaKontanController::class, 'shareBoss'])->name('nota.share-boss');
    Route::post('/nota-kontan/{nota}/mark-sent', [NotaKontanController::class, 'markSent'])->name('nota.mark-sent');

    // Hapus Nota: Khusus Super Admin
    Route::delete('/nota-kontan/{nota}', [NotaKontanController::class, 'destroy'])
        ->name('nota.destroy')
        ->middleware('role:super_admin');

    // Kontak Pimpinan: Super Admin & Admin Kantor (Sales tidak dapat melihat)
    Route::middleware('role:super_admin,admin_kantor')->group(function () {
        Route::get('/kontak-bos', [KontakBosController::class, 'index'])->name('kontak-bos.index');
    });

    // Mutasi Kontak Pimpinan (Tambah/Edit/Hapus): Khusus Super Admin
    Route::middleware('role:super_admin')->group(function () {
        Route::post('/kontak-bos', [KontakBosController::class, 'store'])->name('kontak-bos.store');
        Route::put('/kontak-bos/{kontakBos}', [KontakBosController::class, 'update'])->name('kontak-bos.update');
        Route::delete('/kontak-bos/{kontakBos}', [KontakBosController::class, 'destroy'])->name('kontak-bos.destroy');
    });

    // Manajemen Pengguna (Kelola Akun Staf): Khusus Administrator Utama (Super Admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

});
