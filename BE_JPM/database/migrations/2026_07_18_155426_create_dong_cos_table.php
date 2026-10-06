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
        Schema::create('dong_cos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_phien_ban');
            $table->string('ten_dong_co', 150);
            $table->string('loai_nhien_lieu', 50)->nullable();
            $table->double('dung_tich')->nullable();
            $table->integer('ma_luc')->nullable();
            $table->integer('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dong_cos');
    }
};


