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
        Schema::table('cau_hinhs', function (Blueprint $table) {
            $table->text('gia_tri')->nullable()->change();
            $table->text('mo_ta')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cau_hinhs', function (Blueprint $table) {
            $table->string('gia_tri', 255)->nullable()->change();
            $table->string('mo_ta', 255)->nullable()->change();
        });
    }
};
