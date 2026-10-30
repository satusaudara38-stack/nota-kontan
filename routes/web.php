<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaKontanController;
use App\Http\Controllers\KontakBosController;

Route::get('/', [NotaKontanController::class, 'dashboard'])
    ->name('dashboard');

Route::get('/nota-kontan', [NotaKontanController::class, 'index'])
    ->name('nota.index');

Route::get('/nota-kontan/upload', [NotaKontanController::class, 'create'])
    ->name('nota.create');

Route::post('/nota-kontan', [NotaKontanController::class, 'store'])
    ->name('nota.store');

Route::get('/nota-kontan/{nota}', [NotaKontanController::class, 'show'])
    ->name('nota.show');

Route::get('/nota-kontan/{nota}/whatsapp', [
    NotaKontanController::class,
    'share'
])->name('nota.share');

Route::get('/nota-kontan/{nota}/share-boss', [
    NotaKontanController::class,
    'shareBoss'
])->name('nota.share-boss');

Route::get('/nota-kontan/{nota}/pdf', [NotaKontanController::class, 'downloadPdf'])
    ->name('nota.pdf');

Route::delete('/nota-kontan/{nota}', [NotaKontanController::class, 'destroy'])
    ->name('nota.destroy');

// Form edit nota kontan
Route::get('/nota-kontan/{nota}/edit', [
    NotaKontanController::class,
    'edit'
])->name('nota.edit');

// Simpan perubahan nota kontan
Route::put('/nota-kontan/{nota}', [
    NotaKontanController::class,
    'update'
])->name('nota.update');

// Manajemen Kontak WhatsApp Pimpinan (Bos)
Route::resource('kontak-bos', KontakBosController::class)
    ->parameters(['kontak-bos' => 'kontakBos'])
    ->except(['create', 'show', 'edit']);
    