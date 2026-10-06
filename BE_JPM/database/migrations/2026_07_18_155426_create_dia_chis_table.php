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
        Schema::create('dia_chis', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tai_khoan');
            $table->string('ten_nguoi_nhan', 100);
            $table->string('so_dien_thoai', 20);
            $table->string('dia_chi_cu_the', 255);
            $table->string('id_phuong_xa', 20)->nullable();
            $table->string('id_quan_huyen', 20)->nullable();
            $table->string('id_tinh_thanh', 20)->nullable();
            $table->boolean('la_mac_dinh')->nullable();
            $table->string('loai_dia_chi', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dia_chis');
    }
};


