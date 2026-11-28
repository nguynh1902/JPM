<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            if (!Schema::hasColumn('yeu_cau_bao_gias', 'khach_da_chot')) {
                $table->boolean('khach_da_chot')->default(false)->after('trang_thai');
            }
            if (!Schema::hasColumn('yeu_cau_bao_gias', 'thoi_gian_khach_chot')) {
                $table->timestamp('thoi_gian_khach_chot')->nullable()->after('khach_da_chot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            foreach (['khach_da_chot', 'thoi_gian_khach_chot'] as $col) {
                if (Schema::hasColumn('yeu_cau_bao_gias', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
