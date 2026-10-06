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
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->string('don_vi_tinh', 50)->default('Cái')->after('is_gia_lien_he');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->dropColumn('don_vi_tinh');
        });
    }
};


