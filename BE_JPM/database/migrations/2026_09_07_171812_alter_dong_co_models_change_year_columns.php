<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột mới
        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->integer('nam_san_xuat')->nullable()->after('hinh_anh');
        });

        // Copy data từ nam_bat_dau sang nam_san_xuat
        DB::statement('UPDATE dong_co_models SET nam_san_xuat = nam_bat_dau');

        // Xóa 2 cột cũ
        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->dropColumn(['nam_bat_dau', 'nam_ket_thuc']);
        });
    }

    public function down(): void
    {
        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->integer('nam_bat_dau')->nullable();
            $table->integer('nam_ket_thuc')->nullable();
        });

        DB::statement('UPDATE dong_co_models SET nam_bat_dau = nam_san_xuat');

        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->dropColumn('nam_san_xuat');
        });
    }
};
