<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        "danh_muc_san_phams",
        "san_phams",
        "thuong_hieus",
        "loai_dong_cos",
        "dong_co_models",
        "banners",
        "vouchers",
        "nhom_phu_tungs",
        "phu_tungs",
        "so_do_phu_tungs"
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, "loai_ung_dung")) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn("loai_ung_dung");
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, "loai_ung_dung")) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string("loai_ung_dung", 20)->default("MARINE");
                });
            }
        }
    }
};
