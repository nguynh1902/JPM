<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $legacyTables = [
        'chi_tiet_so_do',       // phòng trường hợp tồn tại bảng số ít cũ
        'so_dos',
        'dong_cos',
        'phien_bans',
        'nha_san_xuats',
        'don_vi_tinhs',
        'quoc_gias',
        'tien_tes',
        'cross_dockings',
        'don_vi_van_chuyens',
        'phuong_thuc_thanh_toans',
        'vai_tros',
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->legacyTables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Legacy unused tables; restore via original create migrations if ever needed.
    }
};
