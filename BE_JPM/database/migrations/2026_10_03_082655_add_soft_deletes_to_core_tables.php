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
            $table->softDeletes();
        });
        Schema::table('khos', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('khach_hangs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('khos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('khach_hangs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
