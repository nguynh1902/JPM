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
        Schema::create('so_dos', function (Blueprint $table) {
            $table->id();
            $table->string('ten_so_do', 255);
            $table->string('mo_ta')->nullable();
            $table->string('hinh_anh', 255);
            $table->integer('id_dong_xe')->nullable();
            $table->integer('id_phien_ban')->nullable();
            $table->integer('nam_ap_dung_tu')->nullable();
            $table->integer('nam_ap_dung_den')->nullable();
            $table->integer('trang_thai')->nullable();
            $table->json('chi_tiets')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_dos');
    }
};


