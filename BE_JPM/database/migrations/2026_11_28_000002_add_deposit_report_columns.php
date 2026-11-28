<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('yeu_cau_bao_gias')) {
            Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
                if (!Schema::hasColumn('yeu_cau_bao_gias', 'da_bao_chuyen_khoan')) {
                    $table->boolean('da_bao_chuyen_khoan')->default(false)->after('anh_chuyen_khoan_2');
                }
                if (!Schema::hasColumn('yeu_cau_bao_gias', 'thoi_gian_bao_coc')) {
                    $table->timestamp('thoi_gian_bao_coc')->nullable()->after('da_bao_chuyen_khoan');
                }
            });
        }

        if (Schema::hasTable('don_hangs')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                if (!Schema::hasColumn('don_hangs', 'da_bao_chuyen_khoan')) {
                    $table->boolean('da_bao_chuyen_khoan')->default(false)->after('anh_chuyen_khoan_2');
                }
                if (!Schema::hasColumn('don_hangs', 'thoi_gian_bao_coc')) {
                    $table->timestamp('thoi_gian_bao_coc')->nullable()->after('da_bao_chuyen_khoan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('yeu_cau_bao_gias')) {
            Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
                if (Schema::hasColumn('yeu_cau_bao_gias', 'thoi_gian_bao_coc')) {
                    $table->dropColumn('thoi_gian_bao_coc');
                }
                if (Schema::hasColumn('yeu_cau_bao_gias', 'da_bao_chuyen_khoan')) {
                    $table->dropColumn('da_bao_chuyen_khoan');
                }
            });
        }

        if (Schema::hasTable('don_hangs')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                if (Schema::hasColumn('don_hangs', 'thoi_gian_bao_coc')) {
                    $table->dropColumn('thoi_gian_bao_coc');
                }
                if (Schema::hasColumn('don_hangs', 'da_bao_chuyen_khoan')) {
                    $table->dropColumn('da_bao_chuyen_khoan');
                }
            });
        }
    }
};
