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
        Schema::create('so_do_phu_tungs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("may_thuy_model_id")->constrained("may_thuy_models");
            $table->foreignId("nhom_phu_tung_id")->constrained("nhom_phu_tungs");
            $table->string("ten_so_do");
            $table->text('hinh_anh')->nullable();
            $table->integer("thu_tu")->default(0);
            $table->integer("trang_thai")->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_do_phu_tungs');
    }
};


