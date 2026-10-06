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
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->string('loai_yeu_cau', 20)->default('bao_gia')->after('trang_thai'); // 'bao_gia' or 'dat_hang'
            $table->text('dia_chi_giao_hang')->nullable()->after('khach_hang_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->dropColumn(['loai_yeu_cau', 'dia_chi_giao_hang']);
        });
    }
};
