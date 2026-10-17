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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $granularPermissions = [
            // 1. PIM & Catalog (Phụ tùng, Sơ đồ)
            'Xem Phụ tùng',
            'Thêm Phụ tùng',
            'Sửa Phụ tùng',
            'Xóa Phụ tùng',

            // 2. Động cơ & Thương hiệu (Thương hiệu, Loại, Model)
            'Xem Động cơ & Thương hiệu',
            'Thêm Động cơ & Thương hiệu',
            'Sửa Động cơ & Thương hiệu',
            'Xóa Động cơ & Thương hiệu',

            // 3. Bán hàng & Đơn hàng
            'Xem Đơn hàng',
            'Duyệt Đơn hàng',
            'Sửa Đơn hàng',
            'Xóa Đơn hàng',

            // 4. Khách hàng
            'Xem Khách hàng',
            'Thêm Khách hàng',
            'Sửa Khách hàng',
            'Khóa Khách hàng',

            // 5. Kho & Chuỗi cung ứng
            'Xem Tồn kho',
            'Cập nhật Tồn kho',
            'Xem Thẻ kho',
            'Quản lý Đơn nhập PO',
            'Quản lý Danh sách kho',

            // 6. Sản phẩm phổ thông
            'Xem Sản phẩm',
            'Thêm Sản phẩm',
            'Sửa Sản phẩm',
            'Xóa Sản phẩm',

            // 7. Tin tức & CMS
            'Xem Tin tức & CMS',
            'Soạn thảo Tin tức & CMS',
            'Xóa Tin tức & CMS',

            // 8. Hệ thống & Quản trị
            'Quản lý Nhân sự',
            'Quản lý Vai trò',
            'Cấu hình chung',
        ];

        foreach ($granularPermissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Cấp toàn bộ quyền cho role 'admin'
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Cập nhật role 'staff' có các quyền Xem và tác nghiệp cơ bản
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $defaultStaffPermissions = Permission::whereIn('name', [
            'Xem Phụ tùng',
            'Xem Động cơ & Thương hiệu',
            'Xem Đơn hàng',
            'Xem Khách hàng',
            'Xem Tồn kho',
            'Xem Thẻ kho',
            'Xem Sản phẩm',
            'Xem Tin tức & CMS',
        ])->get();
        $staffRole->syncPermissions($defaultStaffPermissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
