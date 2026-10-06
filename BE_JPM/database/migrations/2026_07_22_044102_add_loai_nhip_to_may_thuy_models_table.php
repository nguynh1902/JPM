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
        Schema::table('may_thuy_models', function (Blueprint $table) {
            $table->string('loai_nhip')->nullable()->after('ma_luc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('may_thuy_models', function (Blueprint $table) {
            $table->dropColumn('loai_nhip');
        });
    }
};


