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
        Schema::table('chi_tiet_don_hangs', function (Blueprint $table) {
            $table->string('ten_phu_tung')->nullable()->after('thanh_tien');
            $table->string('ma_phu_tung')->nullable()->after('ten_phu_tung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_don_hangs', function (Blueprint $table) {
            $table->dropColumn(['ten_phu_tung', 'ma_phu_tung']);
        });
    }
};
