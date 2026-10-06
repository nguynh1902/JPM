<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->id();
            $table->string('ma_yeu_cau')->unique(); // e.g. YCBG-20231001
            $table->string('khach_hang_ten');
            $table->string('khach_hang_sdt')->nullable();
            $table->string('khach_hang_email')->nullable();
            $table->text('ghi_chu_khach_hang')->nullable();
            $table->text('ghi_chu_zalo')->nullable(); // Ghi chú thương lượng
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->tinyInteger('trang_thai')->default(0); // 0: Chờ báo giá, 1: Đã báo giá, 2: Đã chốt, 3: Đã từ chối
            $table->timestamps();
        });

        Schema::create('chi_tiet_bao_gias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yeu_cau_bao_gia_id')->constrained('yeu_cau_bao_gias')->onDelete('cascade');
            $table->string('ten_phu_tung');
            $table->string('ma_phu_tung')->nullable();
            $table->integer('so_luong')->default(1);
            $table->decimal('don_gia', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chi_tiet_bao_gias');
        Schema::dropIfExists('yeu_cau_bao_gias');
    }
};


