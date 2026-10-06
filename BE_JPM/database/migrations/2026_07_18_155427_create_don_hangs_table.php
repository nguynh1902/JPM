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
        Schema::create('don_hangs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_khach_hang')->nullable();
            $table->integer('id_dia_chi_giao_hang');
            $table->integer('id_phuong_thuc_thanh_toan');
            $table->integer('id_don_vi_van_chuyen')->nullable();
            $table->string('ghi_chu_khach_hang')->nullable();
            // Bỏ cột JSON san_phams vì chúng ta sẽ dùng bảng chi_tiet_don_hangs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_hangs');
    }
};


