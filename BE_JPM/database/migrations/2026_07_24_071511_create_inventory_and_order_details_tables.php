<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng Chi tiết đơn hàng (Bán ra)
        Schema::create('chi_tiet_don_hangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_don_hang');
            $table->unsignedBigInteger('id_san_pham'); // id từ bảng san_phams (General or Spare parts)
            $table->string('loai_san_pham')->nullable(); // Có thể là 'phu_tung' hoặc 'san_pham'
            $table->integer('so_luong');
            $table->double('don_gia')->default(0);
            $table->double('thanh_tien')->default(0);
            $table->string('ghi_chu')->nullable();
            $table->timestamps();
        });

        // Bảng Chi tiết nhập kho (Nhập vào / PO)
        Schema::create('chi_tiet_don_nhap_hangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_don_nhap_hang');
            $table->unsignedBigInteger('id_san_pham');
            $table->string('loai_san_pham')->nullable();
            $table->integer('so_luong_nhap');
            $table->double('don_gia_nhap')->default(0);
            $table->double('thanh_tien')->default(0);
            $table->timestamps();
        });

        // Bảng Tồn Kho (Số dư hiện tại ở từng Kho)
        Schema::create('ton_khos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kho');
            $table->unsignedBigInteger('id_san_pham');
            $table->string('loai_san_pham')->nullable();
            $table->integer('so_luong_ton')->default(0); // Tồn thực tế
            $table->integer('so_luong_dang_giu')->default(0); // Reserve cho các đơn hàng chưa giao
            $table->timestamps();
        });

        // Bảng Thẻ Kho (Lịch sử giao dịch nhập/xuất)
        Schema::create('the_khos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kho');
            $table->unsignedBigInteger('id_san_pham');
            $table->string('loai_san_pham')->nullable();
            $table->string('loai_giao_dich'); // NHAP_KHO, XUAT_BAN, KIEM_KE, CHUYEN_KHO
            $table->integer('so_luong_thay_doi'); // Dấu + hoặc -
            $table->integer('so_luong_ton_cuoi')->default(0); // Tồn kho sau giao dịch
            $table->unsignedBigInteger('id_tham_chieu')->nullable(); // ID đơn hàng hoặc ID PO
            $table->string('ma_tham_chieu')->nullable(); // Mã đơn hàng (DH001, PO001)
            $table->string('ghi_chu')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // Người thực hiện
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('the_khos');
        Schema::dropIfExists('ton_khos');
        Schema::dropIfExists('chi_tiet_don_nhap_hangs');
        Schema::dropIfExists('chi_tiet_don_hangs');
    }
};


