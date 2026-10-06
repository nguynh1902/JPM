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
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->string('khach_hang_ten')->nullable()->after('id_khach_hang');
            $table->string('khach_hang_sdt')->nullable()->after('khach_hang_ten');
            $table->text('dia_chi_text')->nullable()->after('id_dia_chi_giao_hang');
            $table->integer('id_dia_chi_giao_hang')->nullable()->change();
            $table->integer('id_phuong_thuc_thanh_toan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn(['khach_hang_ten', 'khach_hang_sdt', 'dia_chi_text']);
            // Reverting nullable changes is complex in SQLite, skipping for brevity
        });
    }
};
