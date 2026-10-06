<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->json('thu_vien_anh')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('phu_tungs', function (Blueprint $table) {
            $table->dropColumn('thu_vien_anh');
        });
    }
};
