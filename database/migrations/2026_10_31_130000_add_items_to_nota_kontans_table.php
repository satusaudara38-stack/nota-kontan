<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nota_kontans') && !Schema::hasColumn('nota_kontans', 'items')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->text('items')->nullable()->after('nama_barang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('nota_kontans') && Schema::hasColumn('nota_kontans', 'items')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->dropColumn('items');
            });
        }
    }
};
