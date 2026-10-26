<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            if (!Schema::hasColumn('yeu_cau_bao_gias', 'da_thu')) {
                $table->decimal('da_thu', 15, 2)->default(0)->after('tien_coc_yeu_cau');
            }
        });

        Schema::table('don_hangs', function (Blueprint $table) {
            if (!Schema::hasColumn('don_hangs', 'ma_van_don')) {
                $table->string('ma_van_don')->nullable()->after('id_don_vi_van_chuyen');
            }
            if (!Schema::hasColumn('don_hangs', 'ten_don_vi_van_chuyen')) {
                $table->string('ten_don_vi_van_chuyen')->default('ViettelPost')->after('ma_van_don');
            }
        });
    }

    public function down(): void
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            if (Schema::hasColumn('yeu_cau_bao_gias', 'da_thu')) {
                $table->dropColumn('da_thu');
            }
        });

        Schema::table('don_hangs', function (Blueprint $table) {
            if (Schema::hasColumn('don_hangs', 'ma_van_don')) {
                $table->dropColumn('ma_van_don');
            }
            if (Schema::hasColumn('don_hangs', 'ten_don_vi_van_chuyen')) {
                $table->dropColumn('ten_don_vi_van_chuyen');
            }
        });
    }
};
