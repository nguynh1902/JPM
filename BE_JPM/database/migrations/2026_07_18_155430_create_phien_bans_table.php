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
        Schema::create('phien_bans', function (Blueprint $table) {
            $table->id();
            $table->integer('id_dong_xe');
            $table->string('ten_phien_ban', 150);
            $table->integer('nam_san_xuat')->nullable();
            $table->string('kieu_dang', 50)->nullable();
            $table->integer('so_cho_ngoi')->nullable();
            $table->integer('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phien_bans');
    }
};


