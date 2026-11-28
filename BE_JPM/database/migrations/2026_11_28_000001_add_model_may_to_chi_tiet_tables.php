<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chi_tiet_bao_gias') && !Schema::hasColumn('chi_tiet_bao_gias', 'model_may')) {
            Schema::table('chi_tiet_bao_gias', function (Blueprint $table) {
                $table->string('model_may')->nullable()->after('ma_phu_tung');
            });
        }

        if (Schema::hasTable('chi_tiet_don_hangs') && !Schema::hasColumn('chi_tiet_don_hangs', 'model_may')) {
            Schema::table('chi_tiet_don_hangs', function (Blueprint $table) {
                $table->string('model_may')->nullable()->after('ma_phu_tung');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chi_tiet_bao_gias') && Schema::hasColumn('chi_tiet_bao_gias', 'model_may')) {
            Schema::table('chi_tiet_bao_gias', function (Blueprint $table) {
                $table->dropColumn('model_may');
            });
        }

        if (Schema::hasTable('chi_tiet_don_hangs') && Schema::hasColumn('chi_tiet_don_hangs', 'model_may')) {
            Schema::table('chi_tiet_don_hangs', function (Blueprint $table) {
                $table->dropColumn('model_may');
            });
        }
    }
};
