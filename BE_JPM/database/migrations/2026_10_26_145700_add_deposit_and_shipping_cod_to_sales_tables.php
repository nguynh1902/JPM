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
        if (Schema::hasTable('yeu_cau_bao_gias') && !Schema::hasColumn('yeu_cau_bao_gias', 'da_thu')) {
            Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
                $table->decimal('da_thu', 15, 2)->default(0)->after('tien_coc_yeu_cau');
            });
        }

        if (Schema::hasTable('don_hangs')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                if (!Schema::hasColumn('don_hangs', 'ma_van_don')) {
                    $table->string('ma_van_don', 100)->nullable()->after('id_don_vi_van_chuyen');
                }
                if (!Schema::hasColumn('don_hangs', 'don_vi_van_chuyen')) {
                    $table->string('don_vi_van_chuyen', 100)->nullable()->after('ma_van_don');
                }
                if (!Schema::hasColumn('don_hangs', 'tien_thu_ho_cod')) {
                    $table->decimal('tien_thu_ho_cod', 15, 2)->default(0)->after('don_vi_van_chuyen');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('yeu_cau_bao_gias') && Schema::hasColumn('yeu_cau_bao_gias', 'da_thu')) {
            Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
                $table->dropColumn('da_thu');
            });
        }

        if (Schema::hasTable('don_hangs')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                $table->dropColumn(['ma_van_don', 'don_vi_van_chuyen', 'tien_thu_ho_cod']);
            });
        }
    }
};
