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
        Schema::table('khach_hangs', function (Blueprint $table) {
            $table->string('ten_cong_ty')->nullable()->after('ho_ten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khach_hangs', function (Blueprint $table) {
            $table->dropColumn('ten_cong_ty');
        });
    }
};
