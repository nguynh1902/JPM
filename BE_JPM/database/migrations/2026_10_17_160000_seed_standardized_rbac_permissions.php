<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // PIM & Động cơ
            'Xem Phụ tùng',
            'Thêm Phụ tùng',
            'Sửa Phụ tùng',
            'Xóa Phụ tùng',
            'Quản lý Model & Động cơ',

            // Bán hàng & Khách hàng
            'Xem Đơn hàng',
            'Duyệt Đơn hàng',
            'Sửa Đơn hàng',
            'Xóa Đơn hàng',
            'Xem Khách hàng',
            'Quản lý Khách hàng',

            // Kho & Vận hành
            'Xem Tồn kho',
            'Quản lý Thẻ kho',
            'Quản lý Đơn nhập PO',
            'Quản lý Danh sách kho',

            // Sản phẩm & Nội dung CMS
            'Quản lý Sản phẩm',
            'Quản lý Tin tức & CMS',

            // Hệ thống
            'Quản lý Nhân sự',
            'Quản lý Vai trò',
            'Cấu hình chung',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Cấp toàn bộ quyền cho role 'admin'
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Cập nhật role 'staff' có các quyền cơ bản (Xem)
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $defaultStaffPermissions = Permission::whereIn('name', [
            'Xem Phụ tùng',
            'Xem Đơn hàng',
            'Xem Khách hàng',
            'Xem Tồn kho',
            'Quản lý Model & Động cơ',
            'Quản lý Sản phẩm',
        ])->get();
        $staffRole->syncPermissions($defaultStaffPermissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep permissions intact
    }
};
