<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('nota_kontans', 'nama_customer')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->string('nama_customer')->nullable();
            });
        }

        if (!Schema::hasColumn('nota_kontans', 'nama_barang')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->string('nama_barang')->nullable();
            });
        }

        if (!Schema::hasColumn('nota_kontans', 'nominal_pembayaran')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->unsignedBigInteger('nominal_pembayaran')->default(0);
            });
        }

        if (!Schema::hasColumn('nota_kontans', 'bukti_pembayaran')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->string('bukti_pembayaran')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Tidak menghapus kolom agar data yang ada tetap aman.
    }
};