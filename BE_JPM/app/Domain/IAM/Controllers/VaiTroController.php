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
            $roles = Role::with('permissions')->orderBy('id', 'desc')->get()->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'user_count' => DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count(),
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'status' => $role->status,
                ];
            });
            
            $allPermissions = Permission::orderBy('id', 'desc')->get()->map(function($p) {
                return ['id' => $p->id, 'name' => $p->name];
            })->keyBy('name');

            // For UI, group permissions into modules. Natively Spatie doesn't do this without a custom table,
            // so we send flat permissions or a mock grouped array.
            $permissionModules = [
                [
                    'id' => 'pim',
                    'name' => 'PIM (Danh mục & Phụ tùng)',
                    'permissions' => [
                        $allPermissions->get('Xem Phụ tùng'),
                        $allPermissions->get('Thêm Phụ tùng'),
                        $allPermissions->get('Sửa Phụ tùng'),
                        $allPermissions->get('Xóa Phụ tùng'),
                    ]
                ],
                [
                    'id' => 'order',
                    'name' => 'Quản lý Đơn hàng',
                    'permissions' => [
                        $allPermissions->get('Xem Đơn hàng'),
                        $allPermissions->get('Duyệt Đơn hàng'),
                        $allPermissions->get('Sửa Đơn hàng'),
                        $allPermissions->get('Xóa Đơn hàng'),
                    ]
                ],
                [
                    'id' => 'system',
                    'name' => 'Hệ thống & Cài đặt',
                    'permissions' => [
                        $allPermissions->get('Quản lý Nhân sự'),
                        $allPermissions->get('Quản lý Vai trò'),
                        $allPermissions->get('Cấu hình chung'),
                    ]
                ]
            ];

            // Remove null permissions in case some were not seeded
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
            $role = Role::create(['name' => $request->name, 'description' => $request->description, 'status' => $request->status ?? 1, 'guard_name' => 'web']);
            
            if ($request->permissions && is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Thêm vai trò thành công',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
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
            $role->update(['name' => $request->name, 'description' => $request->description, 'status' => $request->status]);
            
            if (isset($request->permissions) && is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật vai trò thành công',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'user_count' => DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count(),
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'status' => 1
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
            if (DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa vai trò đang có người dùng'], 400);
            }
            
            // Bypass Spatie's deleting event which calls users()->detach() and crashes due to getModelForGuard bug
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






