<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thuong_hieus', function (Blueprint $table) {
            $table->string('loai_ung_dung', 20)->default('MARINE')->after('name');
        });

        Schema::table('loai_dong_cos', function (Blueprint $table) {
            $table->string('loai_ung_dung', 20)->default('MARINE')->after('ten_loai_dong_co');
        });

        Schema::rename('may_thuy_models', 'dong_co_models');

        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->string('loai_ung_dung', 20)->default('MARINE')->after('ten_model');
        });
    }

    public function down(): void
    {
        Schema::table('dong_co_models', function (Blueprint $table) {
            $table->dropColumn('loai_ung_dung');
        });

        Schema::rename('dong_co_models', 'may_thuy_models');

        Schema::table('loai_dong_cos', function (Blueprint $table) {
            $table->dropColumn('loai_ung_dung');
        });

        Schema::table('thuong_hieus', function (Blueprint $table) {
            $table->dropColumn('loai_ung_dung');
        });
    }
};


