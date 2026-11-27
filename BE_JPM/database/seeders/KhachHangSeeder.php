<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Domain\IAM\Models\User;
use App\Domain\IAM\Models\KhachHang;

class KhachHangSeeder extends Seeder
{
    public function run(): void
    {
        $customer1 = User::where('email', 'haiduong@abc-marine.com')->first();
        if ($customer1) {
            KhachHang::firstOrCreate(
                ['id_tai_khoan' => $customer1->id],
                [
                    'ho_ten' => 'Nguyễn Hải Dương',
                    'so_dien_thoai' => '0988123456',
                ]
            );
        }

        $customer2 = User::where('email', 'minh.tran@gmail.com')->first();
        if ($customer2) {
            KhachHang::firstOrCreate(
                ['id_tai_khoan' => $customer2->id],
                [
                    'ho_ten' => 'Trần Quang Minh',
                    'so_dien_thoai' => '0905987654',
                ]
            );
        }
    }
}
