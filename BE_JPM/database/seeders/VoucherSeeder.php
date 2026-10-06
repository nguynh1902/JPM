<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vouchers')->insert([
            [
                'code' => 'MARINE2026',
                'discount_type' => 'PERCENT',
                'discount_value' => 10,
                'min_order_value' => 1000000,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(20),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AUTO500K',
                'discount_type' => 'AMOUNT',
                'discount_value' => 500000,
                'min_order_value' => 5000000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(30),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
