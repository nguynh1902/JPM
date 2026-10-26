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
        Schema::dropIfExists('banners');
        Schema::dropIfExists('vouchers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('image_url');
                $table->string('link_url')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name')->nullable();
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->integer('discount_percent')->default(0);
                $table->decimal('min_order_amount', 15, 2)->default(0);
                $table->decimal('max_discount_amount', 15, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }
};
