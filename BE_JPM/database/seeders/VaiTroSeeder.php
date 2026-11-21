<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Domain\IAM\Models\User;
use Illuminate\Support\Facades\Hash;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa cache của spatie/laravel-permission trước khi tạo mới
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Tạo 31 Permissions chuẩn hóa theo 6 module Backoffice
        $permissions = [
            // Module 1: PIM & Động Cơ
            'Xem Phụ tùng', 'Thêm Phụ tùng', 'Sửa Phụ tùng', 'Xóa Phụ tùng',
            'Xem Động cơ & Thương hiệu', 'Thêm Động cơ & Thương hiệu', 'Sửa Động cơ & Thương hiệu', 'Xóa Động cơ & Thương hiệu',

            // Module 2: Cửa Hàng & Sản Phẩm
            'Xem Sản phẩm', 'Thêm Sản phẩm', 'Sửa Sản phẩm', 'Xóa Sản phẩm',

            // Module 3: Bán Hàng & Khách Hàng
            'Xem Đơn hàng', 'Duyệt Đơn hàng', 'Sửa Đơn hàng', 'Xóa Đơn hàng',
            'Xem Khách hàng', 'Thêm Khách hàng', 'Sửa Khách hàng', 'Khóa Khách hàng',

            // Module 4: Kho Hàng & Vận Hành
            'Xem Tồn kho', 'Cập nhật Tồn kho', 'Xem Thẻ kho', 'Quản lý Danh sách kho', 'Quản lý Đơn nhập PO',

            // Module 5: Nội Dung & CMS
            'Xem Tin tức & CMS', 'Soạn thảo Tin tức & CMS', 'Xóa Tin tức & CMS',

            // Module 6: Hệ Thống & Quản Trị
            'Quản lý Nhân sự', 'Quản lý Vai trò', 'Cấu hình chung'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Tạo Roles (Vai trò) và gán quyền
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleSuperAdmin->syncPermissions(Permission::all());

        $roleNhanVien = Role::firstOrCreate(['name' => 'Nhân viên PIM', 'guard_name' => 'web']);
        $roleNhanVien->syncPermissions([
            'Xem Phụ tùng', 'Thêm Phụ tùng', 'Sửa Phụ tùng', 'Xóa Phụ tùng',
            'Xem Động cơ & Thương hiệu', 'Thêm Động cơ & Thương hiệu', 'Sửa Động cơ & Thương hiệu'
        ]);

        $roleStaff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $roleStaff->syncPermissions([
            'Xem Phụ tùng',
            'Xem Động cơ & Thương hiệu',
            'Xem Đơn hàng',
            'Xem Khách hàng',
            'Xem Tồn kho',
            'Xem Thẻ kho',
            'Xem Sản phẩm',
            'Xem Tin tức & CMS'
        ]);

        $roleKhachHang = Role::firstOrCreate(['name' => 'customer']);
        // Khách hàng mặc định chưa có quyền quản trị

        // 3. Tạo User mẫu để test đăng nhập
        $avatarUrl = 'https://imgs.search.brave.com/Tuuaec6b4bnBCERP1mrixq3iANwRwsLWmVGN-fgKsxU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTYy/MzI0OTQxL3ZpL2Fu/aC9tJUUxJUJCJTk5/dC1jaGklRTElQkEl/QkZjLWR1LXRodXkl/RTElQkIlODFuLW0l/QzMlQTB1LXRyJUUx/JUJBJUFGbmctcyVF/MSVCQiU4RGMteGFu/aC1jaCVFMSVCQSVB/MXktcXUlQzMlQTEt/dCVFMSVCQiU5MWMt/JUM0JTkxJUUxJUJC/JTk5LXRyJUMzJUFB/bi1iaSVFMSVCQiU4/M24uanBnP3M9NjEy/eDYxMiZ3PTAmaz0y/MCZjPWo0QVc2aWxC/OThPc1Qwc2k2WEJa/RWdZeG1lV042cTE1/YjItdFd1SDlQUnM9';
        $admin = User::firstOrCreate(
            ['email' => 'admin@jpm.com'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('12345678'), // Password mặc định
                'phone' => '0901234567',
                'avatar' => $avatarUrl,
                'status' => 1
            ]
        );
        $admin->assignRole('admin');

        $staff = User::firstOrCreate(
            ['email' => 'staff@jpm.com'],
            [
                'name' => 'Nhân Viên Sale',
                'password' => Hash::make('12345678'),
                'phone' => '0909876543',
                'avatar' => $avatarUrl,
                'status' => 1
            ]
        );
        $staff->assignRole('Nhân viên PIM');
        $staff->assignRole('staff');

        $customer1 = User::firstOrCreate(
            ['email' => 'haiduong@abc-marine.com'],
            [
                'name' => 'Nguyễn Hải Dương',
                'password' => Hash::make('12345678'),
                'phone' => '0988123456',
                'avatar' => $avatarUrl,
                'status' => 1
            ]
        );
        $customer1->assignRole('customer');

        $customer2 = User::firstOrCreate(
            ['email' => 'minh.tran@gmail.com'],
            [
                'name' => 'Trần Quang Minh',
                'password' => Hash::make('12345678'),
                'phone' => '0905987654',
                'avatar' => $avatarUrl,
                'status' => 1
            ]
        );
        $customer2->assignRole('customer');
    }
}


