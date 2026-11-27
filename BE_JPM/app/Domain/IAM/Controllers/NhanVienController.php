<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\IAM\Models\NhanVien;
use App\Domain\IAM\Models\User;
use App\Domain\IAM\Requests\NhanVienRequest;
use App\Domain\IAM\Services\CachedSanctumPersonalAccessToken;
use App\Domain\Core\Services\ImageUploadService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class NhanVienController extends Controller
{
    public function show($id): JsonResponse
    {
        try {
            $nv = NhanVien::with('user.roles')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $nv->id,
                    'id_tai_khoan' => $nv->id_tai_khoan,
                    'user_code' => $nv->ma_nhan_vien ?? ('NV' . str_pad($nv->id, 2, '0', STR_PAD_LEFT)),
                    'name' => $nv->ho_ten,
                    'phone' => $nv->so_dien_thoai,
                    'email' => $nv->user->email ?? '',
                    'avatar' => !empty($nv->user->avatar) ? (str_starts_with($nv->user->avatar, 'http') ? $nv->user->avatar : asset($nv->user->avatar)) : '',
                    'role' => $nv->user?->roles->first()?->name ?? '',
                    'status' => $nv->user->status ?? 1,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $nhanViens = NhanVien::with('user.roles')->orderBy('id', 'desc')->get()->map(function($nv) {
                return [
                    'id' => $nv->id,
                    'id_tai_khoan' => $nv->id_tai_khoan,
                    'user_code' => $nv->ma_nhan_vien ?? ('NV' . str_pad($nv->id, 2, '0', STR_PAD_LEFT)),
                    'name' => $nv->ho_ten,
                    'phone' => $nv->so_dien_thoai,
                    'email' => $nv->user->email ?? '',
                    'avatar' => !empty($nv->user->avatar) ? (str_starts_with($nv->user->avatar, 'http') ? $nv->user->avatar : asset($nv->user->avatar)) : '',
                    'role' => $nv->user?->roles->first()?->name ?? '',
                    'status' => $nv->user->status ?? 1,
                ];
            });
            
            $roles = Role::where('name', '!=', 'customer')
                ->where(function ($q) {
                    $q->where('status', 1)->orWhereNull('status');
                })
                ->pluck('name')
                ->map(function($role) {
                    return [
                        'value' => $role,
                        'label' => $role
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $nhanViens,
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isSuperAdminUser(?User $user): bool
    {
        return (bool) ($user && ($user->hasRole('admin') || $user->hasRole('Super Admin')));
    }

    private function isPrivilegedRole(?string $roleName): bool
    {
        return in_array(strtolower(trim((string) $roleName)), ['admin', 'super admin', 'quản trị viên'], true);
    }

    public function store(NhanVienRequest $request): JsonResponse
    {
        $actor = auth()->user();
        if ($this->isPrivilegedRole($request->role) && !$this->isSuperAdminUser($actor)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền gán vai trò Quản trị viên (Admin / Super Admin).'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = ImageUploadService::storeUploadedFile(
                    $request->file('avatar'),
                    'uploads/avatars',
                    'avatar',
                    400
                );
            }

            $plainPassword = !empty($request->password) ? $request->password : (Str::random(10) . '!9');

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($plainPassword),
                'phone' => $request->phone,
                'status' => $request->status ?? 1,
                'avatar' => $avatarPath
            ]);
            
            if ($request->role) {
                $user->assignRole($request->role);
            }

            $nv = NhanVien::create([
                'ma_nhan_vien' => 'TMP_' . time(),
                'id_tai_khoan' => $user->id,
                'ho_ten' => $request->name,
                'so_dien_thoai' => $request->phone,
            ]);
            
            $nv->update([
                'ma_nhan_vien' => 'NV' . str_pad($nv->id, 2, '0', STR_PAD_LEFT)
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Thêm nhân viên thành công',
                'data' => $nv,
                'temporary_password' => empty($request->password) ? $plainPassword : null,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi thêm nhân viên.'], 500);
        }
    }

    public function update(NhanVienRequest $request, $id): JsonResponse
    {
        $actor = auth()->user();
        $nv = NhanVien::with('user')->findOrFail($id);

        if ($nv->user && $this->isSuperAdminUser($nv->user) && !$this->isSuperAdminUser($actor)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chỉnh sửa tài khoản Quản trị viên (Admin / Super Admin).'
            ], 403);
        }

        if ($this->isPrivilegedRole($request->role) && !$this->isSuperAdminUser($actor)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền gán vai trò Quản trị viên (Admin / Super Admin).'
            ], 403);
        }

        // Chống tự nâng vai trò hoặc tự khóa chính mình
        if ($actor && (int) $nv->id_tai_khoan === (int) $actor->id) {
            $currentPrimaryRole = $nv->user?->roles->first()?->name;
            if ($request->filled('role') && $currentPrimaryRole !== $request->role && !$this->isSuperAdminUser($actor)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể tự thay đổi vai trò của chính mình.'
                ], 403);
            }
            if ($request->has('status') && (int) $request->status === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể tự khóa tài khoản của chính mình.'
                ], 400);
            }
        }

        // Bảo vệ Quản trị viên tối cao cuối cùng khỏi bị khóa hoặc hạ quyền
        if ($nv->user && $this->isSuperAdminUser($nv->user)) {
            $willLock = $request->has('status') && (int) $request->status === 0;
            $willDemote = $request->filled('role') && !$this->isPrivilegedRole($request->role);
            if ($willLock || $willDemote) {
                $otherActiveAdmins = User::role(['admin', 'Super Admin'])
                    ->where('id', '!=', $nv->user->id)
                    ->where('status', 1)
                    ->count();
                if ($otherActiveAdmins === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể khóa hoặc hạ quyền Quản trị viên tối cao cuối cùng đang hoạt động.'
                    ], 400);
                }
            }
        }

        DB::beginTransaction();
        try {
            $nv->update([
                'ho_ten' => $request->name,
                'so_dien_thoai' => $request->phone,
            ]);
            
            if ($nv->user) {
                $oldStatus = (int) $nv->user->status;
                $newStatus = $request->has('status') ? (int) $request->status : $oldStatus;
                $oldPrimaryRole = $nv->user->roles->first()?->name;

                $updateData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'status' => $newStatus,
                ];

                if ($request->hasFile('avatar')) {
                    $oldAvatar = $nv->user->avatar;
                    $updateData['avatar'] = ImageUploadService::storeUploadedFile(
                        $request->file('avatar'),
                        'uploads/avatars',
                        'avatar',
                        400
                    );
                    ImageUploadService::delete($oldAvatar);
                } elseif ($request->boolean('remove_avatar') || ($request->has('avatar') && ($request->input('avatar') === null || $request->input('avatar') === ''))) {
                    ImageUploadService::delete($nv->user->avatar);
                    $updateData['avatar'] = null;
                }

                $nv->user->update($updateData);
                
                // Chỉ sync role khi thực sự đổi vai trò (giữ nguyên multi-role như 'staff' nếu không đổi)
                if ($request->filled('role') && $request->role !== 'N/A' && $request->role !== $oldPrimaryRole) {
                    $nv->user->syncRoles([$request->role]);
                    \App\Domain\IAM\Models\SystemLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'UPDATE',
                        'description' => "Đổi vai trò Nhân Viên ({$nv->ho_ten}) từ {$oldPrimaryRole} sang {$request->role}",
                        'ip_address' => request()->ip(),
                    ]);
                }

                if ($oldStatus !== $newStatus) {
                    \App\Domain\IAM\Models\SystemLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'UPDATE',
                        'description' => ($newStatus === 0 ? 'Khóa' : 'Mở khóa') . " tài khoản Nhân Viên ({$nv->ho_ten})",
                        'ip_address' => request()->ip(),
                    ]);
                }

                if ($newStatus === 0) {
                    $nv->user->tokens()->delete();
                    CachedSanctumPersonalAccessToken::invalidateUserTokens($nv->user->id);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhân viên thành công',
                'data' => $nv
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi cập nhật nhân viên.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $nv = NhanVien::with('user')->findOrFail($id);
            $user = $nv->user;

            if ($user && $user->id === auth()->id()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tự xóa tài khoản của chính mình!'
                ], 403);
            }

            if ($user && $this->isSuperAdminUser($user)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa tài khoản Quản trị viên tối cao (Super Admin)!'
                ], 403);
            }
            
            // Check dependency via FK constraint by attempting to delete
            $nv->delete();
            
            if ($user) {
                $oldAvatar = $user->avatar;
                $user->tokens()->delete();
                CachedSanctumPersonalAccessToken::invalidateUserTokens($user->id);
                $user->delete();
                ImageUploadService::delete($oldAvatar);
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa tài khoản nhân viên thành công'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == '23000') {
                return response()->json(['success' => false, 'message' => 'Không thể xóa nhân viên này vì đã phát sinh dữ liệu (đơn hàng, báo giá, thẻ kho...). Vui lòng Khóa tài khoản thay vì xóa.'], 409);
            }
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống khi xóa nhân viên.'], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi xóa nhân viên.'], 500);
        }
    }
    
    public function resetPassword($id): JsonResponse
    {
        try {
            $actor = auth()->user();
            $nv = NhanVien::with('user')->findOrFail($id);

            if ($nv->user && $this->isSuperAdminUser($nv->user) && !$this->isSuperAdminUser($actor)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền đặt lại mật khẩu tài khoản Quản trị viên (Admin / Super Admin)!'
                ], 403);
            }

            $tempPassword = Str::random(10) . '!9';
            if ($nv->user) {
                $nv->user->update([
                    'password' => Hash::make($tempPassword)
                ]);
                // Revoke tokens + invalidate Redis token cache to force immediate logout
                $nv->user->tokens()->delete();
                CachedSanctumPersonalAccessToken::invalidateUserTokens($nv->user->id);
                \App\Domain\IAM\Models\SystemLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'UPDATE',
                    'description' => "Đặt lại mật khẩu Nhân Viên ({$nv->ho_ten})",
                    'ip_address' => request()->ip(),
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => "Đã đặt lại mật khẩu thành công! Mật khẩu tạm thời: {$tempPassword}",
                'temporary_password' => $tempPassword,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi đặt lại mật khẩu.'], 500);
        }
    }
}
