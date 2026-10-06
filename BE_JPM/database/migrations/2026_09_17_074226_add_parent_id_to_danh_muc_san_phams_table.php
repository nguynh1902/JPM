<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('danh_muc_san_phams', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            // Dùng Restrict để không cho xóa danh mục cha nếu còn danh mục con
            $table->foreign('parent_id')->references('id')->on('danh_muc_san_phams')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('danh_muc_san_phams', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};