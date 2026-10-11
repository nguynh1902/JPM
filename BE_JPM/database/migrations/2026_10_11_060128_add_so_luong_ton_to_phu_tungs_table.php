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
            if (!Schema::hasColumn('phu_tungs', 'so_luong_ton')) {
                $table->integer('so_luong_ton')->default(0)->after('is_gia_lien_he');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            if (Schema::hasColumn('phu_tungs', 'so_luong_ton')) {
                $table->dropColumn('so_luong_ton');
            }
        });
    }
};
