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
        Schema::create('phu_tungs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("thuong_hieu_id")->constrained("thuong_hieus");
            $table->foreignId("nhom_phu_tung_id")->constrained("nhom_phu_tungs");
            $table->string("ma_phu_tung")->unique();
            $table->string("ten_phu_tung");
            $table->decimal("gia_ban", 15, 2);
            $table->boolean("is_gia_lien_he")->default(false);
            $table->integer("trang_thai")->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phu_tungs');
    }
};



