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
        Schema::create('may_thuy_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId("thuong_hieu_id")->constrained("thuong_hieus");
            $table->foreignId("loai_dong_co_id")->constrained("loai_dong_cos");
            $table->string("ma_model")->unique();
            $table->string("ten_model");
            $table->integer("nam_bat_dau")->nullable();
            $table->integer("nam_ket_thuc")->nullable();
            $table->integer("ma_luc")->nullable();
            $table->integer("trang_thai")->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('may_thuy_models');
    }
};


