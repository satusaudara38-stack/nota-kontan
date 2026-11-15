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
            if (!Schema::hasColumn('nota_kontans', 'kategori_transaksi')) {
                $table->string('kategori_transaksi', 30)->default('mobil')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_kontans', function (Blueprint $table) {
            if (Schema::hasColumn('nota_kontans', 'kategori_transaksi')) {
                $table->dropColumn('kategori_transaksi');
            }
        });
    }
};
