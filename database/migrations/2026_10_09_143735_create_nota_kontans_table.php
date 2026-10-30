<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_kontans', function (Blueprint $table) {
            $table->string('nama_customer')->nullable();
            $table->string('nama_barang')->nullable();
            $table->unsignedBigInteger('nominal_pembayaran')->default(0);
            $table->string('bukti_pembayaran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nota_kontans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_customer',
                'nama_barang',
                'nominal_pembayaran',
                'bukti_pembayaran',
            ]);
        });
    }
};