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
        Schema::create('cau_hinhs', function (Blueprint $table) {
            $table->id();
            $table->string('khoa_cau_hinh', 100)->unique();
            $table->string('gia_tri')->nullable();
            $table->string('mo_ta')->nullable();
            $table->string('loai_gia_tri', 50)->nullable();
            $table->integer('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hinhs');
    }
};


