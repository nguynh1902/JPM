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
        Schema::create('ma_thay_thes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phu_tung_cu_id')->constrained('phu_tungs')->onDelete('cascade');
            $table->foreignId('phu_tung_moi_id')->constrained('phu_tungs')->onDelete('cascade');
            $table->string('loai_thay_the')->default('Thay thế trực tiếp');
            $table->text('ghi_chu')->nullable();
            $table->boolean('trang_thai')->default(1);
            $table->timestamps();

            // Ràng buộc duy nhất: Một phụ tùng cũ chỉ map với một phụ tùng mới trong cùng một loại thay thế
            $table->unique(['phu_tung_cu_id', 'phu_tung_moi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ma_thay_thes');
    }
};
