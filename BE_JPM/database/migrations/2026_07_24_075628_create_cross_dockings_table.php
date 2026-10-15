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
        Schema::create('cross_dockings', function (Blueprint $table) {
            $table->id();
            $table->string("ma_van_don")->nullable(); $table->string("ten_san_pham")->nullable(); $table->integer("so_luong")->default(0); $table->string("khach_hang")->nullable(); $table->string("trang_thai")->default("WAITING");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cross_dockings');
    }
};


