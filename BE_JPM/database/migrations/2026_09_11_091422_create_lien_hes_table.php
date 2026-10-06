<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lien_hes', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email');
            $table->string('so_dien_thoai');
            $table->text('noi_dung');
            $table->string('trang_thai')->default('Mới'); // Mới, Đang xử lý, Đã phản hồi
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lien_hes');
    }
};
