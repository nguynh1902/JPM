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
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tai_khoan')->unique();
            $table->string('ho_ten', 100);
            $table->string('so_dien_thoai', 20)->nullable()->unique();
            $table->date('ngay_sinh')->nullable();
            $table->integer('gioi_tinh')->nullable();
            $table->string('hang_thanh_vien', 50)->nullable();
            $table->integer('diem_thuong')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khach_hangs');
    }
};


