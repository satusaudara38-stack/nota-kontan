<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nota_kontans', function (Blueprint $table) {
            if (!Schema::hasColumn('nota_kontans', 'nama_stnk_bpkb')) {
                $table->string('nama_stnk_bpkb')->nullable()->after('nama_customer');
            }
            if (!Schema::hasColumn('nota_kontans', 'type_unit')) {
                $table->string('type_unit')->nullable()->after('nama_stnk_bpkb');
            }
            if (!Schema::hasColumn('nota_kontans', 'warna')) {
                $table->string('warna')->nullable()->after('type_unit');
            }
            if (!Schema::hasColumn('nota_kontans', 'total_diskon')) {
                $table->decimal('total_diskon', 15, 2)->default(0)->nullable()->after('nominal_pembayaran');
            }
            if (!Schema::hasColumn('nota_kontans', 'cara_pembelian')) {
                $table->string('cara_pembelian', 20)->default('Credit')->nullable()->after('total_diskon');
            }
            if (!Schema::hasColumn('nota_kontans', 'nama_leasing')) {
                $table->string('nama_leasing', 100)->nullable()->after('cara_pembelian');
            }
            if (!Schema::hasColumn('nota_kontans', 'tenor')) {
                $table->string('tenor', 50)->nullable()->after('nama_leasing');
            }
            if (!Schema::hasColumn('nota_kontans', 'tahun_mobil')) {
                $table->string('tahun_mobil', 10)->default('2026')->nullable()->after('tenor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_kontans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_stnk_bpkb',
                'type_unit',
                'warna',
                'total_diskon',
                'cara_pembelian',
                'nama_leasing',
                'tenor',
                'tahun_mobil',
            ]);
        });
    }
};
