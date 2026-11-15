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
            if (!Schema::hasColumn('nota_kontans', 'wa_bos_sent_at')) {
                $table->timestamp('wa_bos_sent_at')->nullable()->after('tanggal_upload');
            }
            if (!Schema::hasColumn('nota_kontans', 'wa_bos_sent_count')) {
                $table->unsignedInteger('wa_bos_sent_count')->default(0)->after('wa_bos_sent_at');
            }
            if (!Schema::hasColumn('nota_kontans', 'wa_customer_sent_at')) {
                $table->timestamp('wa_customer_sent_at')->nullable()->after('wa_bos_sent_count');
            }
            if (!Schema::hasColumn('nota_kontans', 'wa_customer_sent_count')) {
                $table->unsignedInteger('wa_customer_sent_count')->default(0)->after('wa_customer_sent_at');
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
                'wa_bos_sent_at',
                'wa_bos_sent_count',
                'wa_customer_sent_at',
                'wa_customer_sent_count',
            ]);
        });
    }
};
