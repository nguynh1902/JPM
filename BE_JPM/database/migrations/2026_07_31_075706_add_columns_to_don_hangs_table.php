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
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->string('ma_don_hang')->unique()->after('id')->nullable();
            $table->double('tong_tien')->default(0)->after('ma_don_hang');
            $table->integer('trang_thai')->default(0)->after('tong_tien'); // 0: Chờ duyệt, 1: Đang giao, 2: Hoàn thành, 3: Đã hủy
            $table->integer('trang_thai_thanh_toan')->default(0)->after('trang_thai'); // 0: Chưa thanh toán, 1: Đã thanh toán một phần, 2: Đã thanh toán đủ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn(['ma_don_hang', 'tong_tien', 'trang_thai', 'trang_thai_thanh_toan']);
        });
    }
};
