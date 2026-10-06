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
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->decimal('tien_coc_yeu_cau', 15, 2)->default(0)->after('tong_tien');
            $table->decimal('da_thu', 15, 2)->default(0)->after('tien_coc_yeu_cau');
            $table->boolean('khach_tu_tra_cuoc')->default(false)->after('phi_van_chuyen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn(['tien_coc_yeu_cau', 'da_thu', 'khach_tu_tra_cuoc']);
        });
    }
};
