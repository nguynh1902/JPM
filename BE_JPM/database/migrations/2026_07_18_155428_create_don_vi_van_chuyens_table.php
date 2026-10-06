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
        Schema::create('don_vi_van_chuyens', function (Blueprint $table) {
            $table->id();
            $table->string('ten_don_vi', 100);
            $table->string('ma_don_vi', 50)->unique();
            $table->string('api_url', 255)->nullable();
            $table->string('api_key', 255)->nullable();
            $table->integer('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_vi_van_chuyens');
    }
};


