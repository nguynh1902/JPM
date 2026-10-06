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
        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->string('hinh_anh')->nullable()->after('ten_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
};
