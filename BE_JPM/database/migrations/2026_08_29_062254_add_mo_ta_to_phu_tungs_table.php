<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->text('mo_ta')->nullable();
            $table->text('thong_so_ky_thuat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->dropColumn(['mo_ta', 'thong_so_ky_thuat']);
        });
    }
};
