<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemLogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_logs')->insert([
            [
                'user_id' => 1,
                'action' => 'LOGIN',
                'description' => 'Đăng nhập vào hệ thống',
                'ip_address' => '192.168.1.15',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => 1,
                'action' => 'UPDATE',
                'description' => 'Cập nhật giá sản phẩm Yamaha F150',
                'ip_address' => '192.168.1.15',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30),
            ]
        ]);
    }
}
