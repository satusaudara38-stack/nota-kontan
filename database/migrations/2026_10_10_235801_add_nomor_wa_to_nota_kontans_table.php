<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('nota_kontans', 'nomor_wa')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->string('nomor_wa', 20)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('nota_kontans', 'nomor_wa')) {
            Schema::table('nota_kontans', function (Blueprint $table) {
                $table->dropColumn('nomor_wa');
            });
        }
    }
};