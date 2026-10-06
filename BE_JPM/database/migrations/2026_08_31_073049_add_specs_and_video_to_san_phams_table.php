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
        Schema::table('san_phams', function (Blueprint $table) {
            $table->longText('thong_so_ky_thuat')->nullable()->after('mo_ta_chi_tiet');
            $table->string('video_url', 500)->nullable()->after('thu_vien_anh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('san_phams', function (Blueprint $table) {
            $table->dropColumn(['thong_so_ky_thuat', 'video_url']);
        });
    }
};
