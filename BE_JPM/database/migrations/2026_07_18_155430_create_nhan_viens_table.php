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
        Schema::create('nhan_viens', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tai_khoan')->unique();
            $table->string('ma_nhan_vien', 50)->unique();
            $table->string('ho_ten', 100);
            $table->string('so_dien_thoai', 20)->nullable()->unique();
            $table->date('ngay_sinh')->nullable();
            $table->date('ngay_vao_lam')->nullable();
            $table->string('phong_ban', 100)->nullable();
            $table->string('chuc_vu', 100)->nullable();
            $table->string('trang_thai_lam_viec', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_viens');
    }
};


