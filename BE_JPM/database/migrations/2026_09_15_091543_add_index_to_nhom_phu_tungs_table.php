<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nhom_phu_tungs', function (Blueprint $table) {
            $table->index('ma_nhom');
        });
    }

    public function down(): void
    {
        Schema::table('nhom_phu_tungs', function (Blueprint $table) {
            $table->dropIndex(['ma_nhom']);
        });
    }
};
