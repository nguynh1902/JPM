<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domain\IAM\Requests\VaiTroRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Domain\IAM\Models\SystemLog;

class VaiTroController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $roles = Role::with('permissions')
                ->where('name', '!=', 'customer')
                ->orderBy('id', 'asc')
                ->get()
                ->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'description' => $role->description,
                        'is_system' => strtolower($role->name) === 'admin',
                        'user_count' => DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count(),
                        'permissions' => $role->permissions->pluck('id')->toArray(),
                        'status' => $role->status ?? 1,
                    ];
                });
            
            $allPermissions = Permission::orderBy('id', 'asc')->get()->map(function($p) {
                return ['id' => $p->id, 'name' => $p->name];
            })->keyBy('name');

            // Group permissions into 5 standardized modules
            $permissionModules = [
                [
                    'id' => 'pim',
                    'name' => 'PIM & Động Cơ',
                    'permissions' => [
                        $allPermissions->get('Xem Phụ tùng'),
                        $allPermissions->get('Thêm Phụ tùng'),
                        $allPermissions->get('Sửa Phụ tùng'),
                        $allPermissions->get('Xóa Phụ tùng'),
                        $allPermissions->get('Xem Động cơ & Thương hiệu'),
                        $allPermissions->get('Thêm Động cơ & Thương hiệu'),
                        $allPermissions->get('Sửa Động cơ & Thương hiệu'),
                        $allPermissions->get('Xóa Động cơ & Thương hiệu'),
                    ]
                ],
                [
                    'id' => 'order',
                    'name' => 'Bán Hàng & Khách Hàng',
                    'permissions' => [
                        $allPermissions->get('Xem Đơn hàng'),
                        $allPermissions->get('Duyệt Đơn hàng'),
                        $allPermissions->get('Sửa Đơn hàng'),
                        $allPermissions->get('Xóa Đơn hàng'),
                        $allPermissions->get('Xem Khách hàng'),
                        $allPermissions->get('Thêm Khách hàng'),
                        $allPermissions->get('Sửa Khách hàng'),
                        $allPermissions->get('Khóa Khách hàng'),
                    ]
                ],
                [
                    'id' => 'warehouse',
                    'name' => 'Kho & Vận Hành',
                    'permissions' => [
                        $allPermissions->get('Xem Tồn kho'),
                        $allPermissions->get('Cập nhật Tồn kho'),
                        $allPermissions->get('Xem Thẻ kho'),
                        $allPermissions->get('Quản lý Đơn nhập PO'),
                        $allPermissions->get('Quản lý Danh sách kho'),
                    ]
                ],
                [
                    'id' => 'cms',
                    'name' => 'Sản Phẩm & CMS',
                    'permissions' => [
                        $allPermissions->get('Xem Sản phẩm'),
                        $allPermissions->get('Thêm Sản phẩm'),
                        $allPermissions->get('Sửa Sản phẩm'),
                        $allPermissions->get('Xóa Sản phẩm'),
                        $allPermissions->get('Xem Tin tức & CMS'),
                        $allPermissions->get('Soạn thảo Tin tức & CMS'),
                        $allPermissions->get('Xóa Tin tức & CMS'),
                    ]
                ],
                [
                    'id' => 'system',
                    'name' => 'Hệ Thống & Cài Đặt',
                    'permissions' => [
                        $allPermissions->get('Quản lý Nhân sự'),
                        $allPermissions->get('Quản lý Vai trò'),
                        $allPermissions->get('Cấu hình chung'),
                    ]
                ]
            ];

            // Remove null permissions in case any were missing
            foreach ($permissionModules as &$module) {
                $module['permissions'] = array_values(array_filter($module['permissions']));
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'roles' => $roles,
                    'modules' => $permissionModules
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(VaiTroRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status ?? 1,
                'guard_name' => 'web'
            ]);
            
            if ($request->permissions && is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Thêm vai trò thành công',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'is_system' => false,
                    'user_count' => 0,
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'status' => 1
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(VaiTroRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            
            // Bảo vệ vai trò Admin: không cho đổi tên hoặc khóa trạng thái
            if (strtolower($role->name) === 'admin') {
                if ($request->has('status') && (int)$request->status === 0) {
                    return response()->json(['success' => false, 'message' => 'Không thể khóa vai trò Quản trị viên hệ thống (Admin)'], 400);
                }
            }

            $role->update([
                'name' => (strtolower($role->name) === 'admin') ? 'admin' : $request->name,
                'description' => $request->description,
                'status' => $request->status ?? 1
            ]);
            
            if (isset($request->permissions) && is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật vai trò thành công',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'is_system' => strtolower($role->name) === 'admin',
                    'user_count' => DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count(),
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'status' => $role->status ?? 1
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            if (strtolower($role->name) === 'admin') {
                return response()->json(['success' => false, 'message' => 'Không thể xóa vai trò Quản trị viên hệ thống (Admin)'], 400);
            }

            if (DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa vai trò đang có người dùng gán'], 400);
            }
            
            DB::table(config('permission.table_names.role_has_permissions'))->where('role_id', $role->id)->delete();
            DB::table(config('permission.table_names.roles'))->where('id', $role->id)->delete();
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa vai trò thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
