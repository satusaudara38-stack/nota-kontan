<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi bersih gabungan: membuat tabel nota_kontans dengan semua kolom sekaligus
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_kontans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_customer')->nullable();
            $table->string('nomor_wa', 20)->nullable();
            $table->string('nama_barang')->nullable();
            $table->unsignedBigInteger('nominal_pembayaran')->default(0);
            $table->string('bukti_pembayaran')->nullable();
            $table->string('nama_file')->nullable();
            $table->string('path_file')->nullable();
            $table->date('tanggal_upload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_kontans');
    }
};
