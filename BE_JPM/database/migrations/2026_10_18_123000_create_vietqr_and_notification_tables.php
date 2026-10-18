<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng lịch sử giao dịch thanh toán & đối soát VietQR
        if (!Schema::hasTable('giao_dich_thanh_toans')) {
            Schema::create('giao_dich_thanh_toans', function (Blueprint $table) {
                $table->id();
                $table->string('gateway', 50)->default('VietQR'); // VietQR, SePay, Casso, Vietcombank, MB
                $table->string('ma_giao_dich', 100)->nullable()->index(); // Mã GD ngân hàng / reference number
                $table->string('so_tai_khoan', 50)->nullable();
                $table->decimal('so_tien', 15, 2);
                $table->text('noi_dung')->nullable();
                $table->string('loai_doi_tuong', 50)->default('don_hang'); // don_hang, yeu_cau_bao_gia, khac
                $table->unsignedBigInteger('id_doi_tuong')->nullable()->index();
                $table->string('ma_tham_chieu', 100)->nullable()->index(); // DH20261018001, YCBG...
                $table->string('trang_thai', 50)->default('Thành công'); // Thành công, Không khớp, Đã đối soát
                $table->json('du_lieu_raw')->nullable();
                $table->timestamp('ngay_giao_dich')->nullable();
                $table->timestamps();
            });
        }

        // 2. Bảng lịch sử gửi thông báo Zalo ZNS & SMS Brandname
        if (!Schema::hasTable('lich_su_thong_baos')) {
            Schema::create('lich_su_thong_baos', function (Blueprint $table) {
                $table->id();
                $table->string('loai_tin', 50)->default('zalo_zns'); // zalo_zns, sms_brandname, email
                $table->string('kenh_gui', 50)->default('Mock/Log'); // Zalo, SpeedSMS, eSMS, Mock/Log
                $table->string('nguoi_nhan_loai', 50)->default('khach_hang'); // khach_hang, chu_tau, tai_xe_chanh
                $table->string('so_dien_thoai', 20)->nullable()->index();
                $table->string('ten_nguoi_nhan', 255)->nullable();
                $table->string('mau_thong_bao', 100)->nullable(); // bao_gia_moi, xac_nhan_coc, giao_chanh_xe, thong_tin_nha_xe
                $table->string('tieu_de', 255)->nullable();
                $table->text('noi_dung');
                $table->string('ma_tham_chieu', 100)->nullable()->index();
                $table->string('trang_thai', 50)->default('thanh_cong'); // thanh_cong, that_bai, mo_phong
                $table->json('phan_hoi_gateway')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_su_thong_baos');
        Schema::dropIfExists('giao_dich_thanh_toans');
    }
};
