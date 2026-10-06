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
            $table->decimal('phi_van_chuyen', 15, 2)->default(0)->after('tong_tien');
        });

        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->decimal('phi_van_chuyen', 15, 2)->default(0)->after('tong_tien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn('phi_van_chuyen');
        });

        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->dropColumn('phi_van_chuyen');
        });
    }
};
