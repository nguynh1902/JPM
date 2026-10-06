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
        Schema::create('san_phams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('danh_muc_san_pham_id')->constrained('danh_muc_san_phams');
            $table->string('ma_san_pham')->unique();
            $table->string('ten_san_pham');
            $table->string('slug')->unique();
            $table->text('mo_ta_ngan')->nullable();
            $table->longText('mo_ta_chi_tiet')->nullable();
            $table->decimal('gia_ban', 15, 2)->default(0);
            $table->decimal('gia_khuyen_mai', 15, 2)->nullable();
            $table->integer('so_luong_ton')->default(0);
            $table->text('hinh_anh_chinh')->nullable();
            $table->json('thu_vien_anh')->nullable();
            $table->string('loai_ung_dung', 20)->default('MARINE');
            $table->integer('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_phams');
    }
};


