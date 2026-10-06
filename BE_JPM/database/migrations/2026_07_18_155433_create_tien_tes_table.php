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
        Schema::create('tien_tes', function (Blueprint $table) {
            $table->id();
            $table->string('ma_tien_te', 10)->unique();
            $table->string('ten_tien_te', 50);
            $table->string('ky_hieu', 10)->nullable();
            $table->double('ty_gia')->nullable();
            $table->boolean('la_mac_dinh')->nullable();
            $table->integer('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tien_tes');
    }
};


