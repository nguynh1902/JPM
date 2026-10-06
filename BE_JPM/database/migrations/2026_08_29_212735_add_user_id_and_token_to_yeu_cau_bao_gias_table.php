<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('token', 64)->nullable()->unique()->after('ma_yeu_cau');
        });
    }

    public function down()
    {
        Schema::table('yeu_cau_bao_gias', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'token']);
        });
    }
};
