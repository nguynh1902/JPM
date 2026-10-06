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
            $table->string('loai_ung_dung', 20)->default('MARINE')->after('hinh_anh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->dropColumn('loai_ung_dung');
        });
    }
};
