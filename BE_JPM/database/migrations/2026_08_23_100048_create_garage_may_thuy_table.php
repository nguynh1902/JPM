<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garage_may_thuy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tai_khoan');
            $table->unsignedBigInteger('dong_co_model_id');
            $table->string('ten_may')->nullable();           // Biệt danh: "Máy tàu cá số 1"
            $table->string('so_serial')->nullable();         // Số thân máy (S/N)
            $table->integer('nam_mua')->nullable();          // Năm mua / sản xuất
            $table->text('ghi_chu')->nullable();             // Ghi chú thêm

            $table->foreign('id_tai_khoan')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dong_co_model_id')->references('id')->on('dong_co_models')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garage_may_thuy');
    }
};

