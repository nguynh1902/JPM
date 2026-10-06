<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\IAM\Models\NhanVien;
use App\Domain\IAM\Models\User;
use App\Domain\IAM\Requests\NhanVienRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;

class NhanVienController extends Controller
{
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
                    'role' => $nv->user->roles->first()?->name ?? 'N/A',
                    'status' => $nv->user->status ?? 1,
                ];
            });
            
            $roles = Role::pluck('name')->map(function($role) {
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

    public function store(NhanVienRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars'), $filename);
                $avatarPath = '/uploads/avatars/' . $filename;
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(!empty($request->password) ? $request->password : 'Jpm@2026'),
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
                'data' => $nv
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(NhanVienRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $nv = NhanVien::findOrFail($id);
            
            $nv->update([
                'ho_ten' => $request->name,
                'so_dien_thoai' => $request->phone,
            ]);
            
            if ($nv->user) {
                $updateData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'status' => $request->status,
                ];

                if ($request->hasFile('avatar')) {
                    $file = $request->file('avatar');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/avatars'), $filename);
                    $updateData['avatar'] = '/uploads/avatars/' . $filename;
                    
                    // Xóa ảnh cũ
                    if ($nv->user->avatar && file_exists(public_path($nv->user->avatar))) {
                        unlink(public_path($nv->user->avatar));
                    }
                } elseif ($request->has('avatar') && ($request->input('avatar') === null || $request->input('avatar') === '')) {
                    if ($nv->user->avatar && file_exists(public_path($nv->user->avatar))) {
                        unlink(public_path($nv->user->avatar));
                    }
                    $updateData['avatar'] = null;
                }

                $nv->user->update($updateData);
                
                if ($request->role) {
                    $nv->user->syncRoles([$request->role]);
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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $nv = NhanVien::findOrFail($id);
            $user = $nv->user;
            
            // Check dependency via FK constraint by attempting to delete
            $nv->delete();
            
            if ($user) {
                $user->tokens()->delete();
                $user->delete();
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
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function resetPassword($id): JsonResponse
    {
        try {
            $nv = NhanVien::findOrFail($id);
            if ($nv->user) {
                $nv->user->update([
                    'password' => Hash::make('Jpm@2026')
                ]);
                // Revoke tokens to force logout
                $nv->user->tokens()->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Đã reset mật khẩu thành công!'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}




