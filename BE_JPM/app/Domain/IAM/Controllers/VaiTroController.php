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
    public function show($id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            $isSystem = in_array(strtolower($role->name), ['admin', 'super admin', 'quản trị viên'], true);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'is_system' => $isSystem,
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'status' => $role->status ?? 1,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $userCounts = DB::table(config('permission.table_names.model_has_roles'))
                ->select('role_id', DB::raw('COUNT(*) as total'))
                ->groupBy('role_id')
                ->pluck('total', 'role_id');

            $roles = Role::with('permissions')
                ->where('name', '!=', 'customer')
                ->orderBy('id', 'asc')
                ->get()
                ->map(function($role) use ($userCounts) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'description' => $role->description,
                        'is_system' => in_array(strtolower($role->name), ['admin', 'super admin', 'quản trị viên'], true),
                        'user_count' => (int) ($userCounts[$role->id] ?? 0),
                        'permissions' => $role->permissions->pluck('id')->toArray(),
                        'status' => $role->status ?? 1,
                    ];
                });
            
            $allPermissions = Permission::orderBy('id', 'asc')->get()->map(function($p) {
                return ['id' => $p->id, 'name' => $p->name];
            })->keyBy('name');

            // Group permissions into 6 standardized modules matching Backoffice layout
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
                    'id' => 'product',
                    'name' => 'Cửa Hàng & Sản Phẩm',
                    'permissions' => [
                        $allPermissions->get('Xem Sản phẩm'),
                        $allPermissions->get('Thêm Sản phẩm'),
                        $allPermissions->get('Sửa Sản phẩm'),
                        $allPermissions->get('Xóa Sản phẩm'),
                    ]
                ],
                [
                    'id' => 'sales',
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
                    'name' => 'Kho Hàng & Vận Hành',
                    'permissions' => [
                        $allPermissions->get('Xem Tồn kho'),
                        $allPermissions->get('Cập nhật Tồn kho'),
                        $allPermissions->get('Xem Thẻ kho'),
                        $allPermissions->get('Quản lý Danh sách kho'),
                        $allPermissions->get('Quản lý Đơn nhập PO'),
                    ]
                ],
                [
                    'id' => 'cms',
                    'name' => 'Nội Dung & CMS',
                    'permissions' => [
                        $allPermissions->get('Xem Tin tức & CMS'),
                        $allPermissions->get('Soạn thảo Tin tức & CMS'),
                        $allPermissions->get('Xóa Tin tức & CMS'),
                    ]
                ],
                [
                    'id' => 'system',
                    'name' => 'Hệ Thống & Quản Trị',
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
        $actor = auth()->user();
        $isSuperAdmin = $actor && ($actor->hasRole('admin') || $actor->hasRole('Super Admin'));
        $isReservedName = in_array(strtolower(trim((string) $request->name)), ['admin', 'super admin', 'quản trị viên', 'customer'], true);

        if ($isReservedName) {
            return response()->json(['success' => false, 'message' => 'Tên vai trò này được dành riêng cho hệ thống.'], 422);
        }

        if ($request->permissions && is_array($request->permissions) && !$isSuperAdmin) {
            $requestedPerms = Permission::whereIn('id', $request->permissions)->pluck('name');
            foreach ($requestedPerms as $permName) {
                if (!$actor || !$actor->hasPermissionTo($permName)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bạn không thể cấp quyền \"{$permName}\" mà tài khoản của bạn không sở hữu."
                    ], 403);
                }
            }
        }

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

            SystemLog::create([
                'user_id' => $actor?->id,
                'action' => 'CREATE',
                'description' => 'Thêm mới Vai Trò (' . $role->name . ')',
                'ip_address' => request()->ip(),
            ]);
            
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
                    'status' => (int) ($role->status ?? 1)
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
            $isSystemRole = in_array(strtolower($role->name), ['admin', 'super admin', 'quản trị viên'], true);
            $actor = auth()->user();
            $isSuperAdmin = $actor && ($actor->hasRole('admin') || $actor->hasRole('Super Admin'));

            // Chỉ Super Admin mới được phép sửa quyền trên vai trò hệ thống (Admin / Super Admin)
            if ($isSystemRole && !$isSuperAdmin) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Chỉ Quản trị viên tối cao mới được chỉnh sửa vai trò hệ thống.'], 403);
            }

            // Chống tự nâng quyền cho vai trò của chính mình nếu không phải Super Admin
            if (!$isSuperAdmin && $actor && $actor->hasRole($role->name)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Bạn không thể tự chỉnh sửa quyền hạn của vai trò mình đang nắm giữ.'], 403);
            }

            // Chống leo thang quyền: không thể cấp quyền mình không sở hữu
            if (isset($request->permissions) && is_array($request->permissions) && !$isSuperAdmin) {
                $requestedPerms = Permission::whereIn('id', $request->permissions)->pluck('name');
                foreach ($requestedPerms as $permName) {
                    if (!$actor || !$actor->hasPermissionTo($permName)) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Bạn không thể cấp quyền \"{$permName}\" mà tài khoản của bạn không sở hữu."
                        ], 403);
                    }
                }
            }

            // Bảo vệ vai trò Admin / Super Admin: không cho đổi tên hoặc khóa trạng thái
            if ($isSystemRole) {
                if ($request->has('status') && (int)$request->status === 0) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Không thể khóa vai trò Quản trị viên hệ thống'], 400);
                }
            }

            $role->update([
                'name' => $isSystemRole ? $role->name : $request->name,
                'description' => $request->description,
                'status' => $request->status ?? 1
            ]);
            
            if (isset($request->permissions) && is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            // Nếu vai trò bị khóa (status = 0), thu hồi token các user đang giữ vai trò đó (nếu không phải Admin)
            if ((int) ($role->status ?? 1) === 0) {
                $userIds = DB::table(config('permission.table_names.model_has_roles'))
                    ->where('role_id', $role->id)
                    ->pluck('model_id');
                foreach ($userIds as $uid) {
                    \App\Domain\IAM\Services\CachedSanctumPersonalAccessToken::invalidateUserTokens((int) $uid);
                }
            }

            SystemLog::create([
                'user_id' => $actor?->id,
                'action' => 'UPDATE',
                'description' => 'Cập nhật Vai Trò (' . $role->name . ')',
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật vai trò thành công',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'is_system' => $isSystemRole,
                    'user_count' => DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count(),
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'status' => (int) ($role->status ?? 1)
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi cập nhật vai trò.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            if (in_array(strtolower($role->name), ['admin', 'super admin', 'quản trị viên'], true)) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa vai trò Quản trị viên hệ thống'], 400);
            }

            if (DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $role->id)->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa vai trò đang có người dùng gán'], 400);
            }

            $roleName = $role->name;
            $role->syncPermissions([]);
            $role->delete();
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            SystemLog::create([
                'user_id' => auth()->id(),
                'action' => 'DELETE',
                'description' => 'Xóa Vai Trò (' . $roleName . ')',
                'ip_address' => request()->ip(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa vai trò thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
