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
        Schema::table('don_nhap_hangs', function (Blueprint $table) {
            $table->string('ma_po')->unique()->after('id')->nullable();
            $table->integer('trang_thai')->default(0)->after('ma_po'); // 0: NHÁP, 1: ĐANG GIAO, 2: HOÀN THÀNH, 3: ĐÃ HỦY
            $table->date('ngay_du_kien')->nullable()->after('ngay_nhap');
            $table->double('tong_tien')->default(0)->after('ngay_du_kien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_nhap_hangs', function (Blueprint $table) {
            $table->dropColumn(['ma_po', 'trang_thai', 'ngay_du_kien', 'tong_tien']);
        });
    }
};


