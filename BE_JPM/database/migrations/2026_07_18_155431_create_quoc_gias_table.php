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
        Schema::create('quoc_gias', function (Blueprint $table) {
            $table->id();
            $table->string('ma_quoc_gia', 10)->unique();
            $table->string('ten_quoc_gia', 100);
            $table->integer('trang_thai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quoc_gias');
    }
};


