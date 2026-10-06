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
        Schema::create('don_nhap_hangs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nha_cung_cap');
            $table->integer('id_kho');
            $table->integer('id_tien_te')->nullable();
            $table->double('ty_gia')->nullable();
            $table->date('ngay_nhap')->nullable();
            $table->string('ghi_chu')->nullable();
            // Bỏ cột JSON chi_tiets vì chúng ta sẽ dùng bảng chi_tiet_nhap_khos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_nhap_hangs');
    }
};


