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
        Schema::table('phu_tung_models', function (Blueprint $table) {
            $table->index(['phu_tung_id', 'dong_co_model_id'], 'idx_phu_tung_dong_co_model');
        });

        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->index(['thuong_hieu_id', 'nhom_phu_tung_id'], 'idx_thuong_hieu_nhom_phu_tung');
            $table->index('ten_phu_tung');
        });

        Schema::table('chi_tiet_so_dos', function (Blueprint $table) {
            $table->index(['so_do_phu_tung_id', 'phu_tung_id'], 'idx_so_do_phu_tung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_tung_models', function (Blueprint $table) {
            $table->dropIndex('idx_phu_tung_dong_co_model');
        });

        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->dropIndex('idx_thuong_hieu_nhom_phu_tung');
            $table->dropIndex(['ten_phu_tung']);
        });

        Schema::table('chi_tiet_so_dos', function (Blueprint $table) {
            $table->dropIndex('idx_so_do_phu_tung');
        });
    }
};
