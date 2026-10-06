<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garage_phu_tungs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garage_id');
            $table->unsignedBigInteger('phu_tung_id');
            $table->integer('so_luong')->default(1);
            $table->text('ghi_chu')->nullable();

            $table->foreign('garage_id')->references('id')->on('garage_may_thuy')->onDelete('cascade');
            $table->foreign('phu_tung_id')->references('id')->on('phu_tungs')->onDelete('cascade');

            $table->unique(['garage_id', 'phu_tung_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garage_phu_tungs');
    }
};
