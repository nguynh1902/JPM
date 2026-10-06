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
            $table->decimal('tien_coc_yeu_cau', 15, 2)->default(0)->after('phi_van_chuyen');
            $table->boolean('khach_tu_tra_cuoc')->default(false)->after('tien_coc_yeu_cau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->dropColumn(['tien_coc_yeu_cau', 'khach_tu_tra_cuoc']);
        });
    }
};
