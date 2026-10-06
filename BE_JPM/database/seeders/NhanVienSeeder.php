<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Domain\IAM\Models\User;
use App\Domain\IAM\Models\NhanVien;

class NhanVienSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@jpm.com')->first();
        if ($admin) {
            NhanVien::firstOrCreate(
                ['id_tai_khoan' => $admin->id],
                [
                    'ma_nhan_vien' => 'NV_ADMIN',
                    'ho_ten' => 'Admin Manager',
                    'so_dien_thoai' => '0901234567',
                    'chuc_vu' => 'Giám đốc',
                    'phong_ban' => 'Ban Giám Đốc',
                    'trang_thai_lam_viec' => 'Đang làm việc'
                ]
            );
        }

        $staff = User::where('email', 'staff@jpm.com')->first();
        if ($staff) {
            NhanVien::firstOrCreate(
                ['id_tai_khoan' => $staff->id],
                [
                    'ma_nhan_vien' => 'NV001',
                    'ho_ten' => 'Nhân Viên Sale',
                    'so_dien_thoai' => '0909876543',
                    'chuc_vu' => 'Nhân viên',
                    'phong_ban' => 'Phòng Kinh Doanh',
                    'trang_thai_lam_viec' => 'Đang làm việc'
                ]
            );
        }
    }
}
