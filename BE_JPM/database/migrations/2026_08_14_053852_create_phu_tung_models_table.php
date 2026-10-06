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
        Schema::create('phu_tung_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phu_tung_id')->constrained('phu_tungs')->onDelete('cascade');
            $table->foreignId('dong_co_model_id')->constrained('dong_co_models')->onDelete('cascade');
            $table->integer('nam_ap_dung')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phu_tung_models');
    }
};
