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
        if (Schema::hasTable('yeu_cau_bao_gias') && !Schema::hasColumn('yeu_cau_bao_gias', 'anh_chuyen_khoan')) {
            Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
                $table->text('anh_chuyen_khoan')->nullable()->after('da_thu');
            });
        }

        if (Schema::hasTable('don_hangs') && !Schema::hasColumn('don_hangs', 'anh_chuyen_khoan')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                $table->text('anh_chuyen_khoan')->nullable()->after('da_thu');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('yeu_cau_bao_gias') && Schema::hasColumn('yeu_cau_bao_gias', 'anh_chuyen_khoan')) {
            Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
                $table->dropColumn('anh_chuyen_khoan');
            });
        }

        if (Schema::hasTable('don_hangs') && Schema::hasColumn('don_hangs', 'anh_chuyen_khoan')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                $table->dropColumn('anh_chuyen_khoan');
            });
        }
    }
};
