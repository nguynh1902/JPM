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
        Schema::create('chi_tiet_so_dos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("so_do_phu_tung_id")->constrained("so_do_phu_tungs");
            $table->foreignId("phu_tung_id")->constrained("phu_tungs");
            $table->string("ref_number")->nullable();
            $table->integer("qty")->default(1);
            $table->decimal("x", 5, 2)->nullable();
            $table->decimal("y", 5, 2)->nullable();
            $table->decimal("width", 5, 2)->nullable();
            $table->decimal("height", 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_so_dos');
    }
};


