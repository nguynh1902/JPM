<?php

namespace App\Domain\IAM\Services;

use App\Domain\IAM\Models\KhachHang;
use App\Domain\IAM\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Storage;

class KhachHangService
{
    public function getById($id)
    {
        return KhachHang::with('user')->findOrFail($id);
    }

    public function getAll()
    {
        return KhachHang::with('user')->orderBy('id', 'desc')->cursorPaginate(50);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            if (User::where('email', $data['email'])->exists()) {
                throw new Exception('Email này đã được sử dụng.', 422);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(!empty($data['password']) ? $data['password'] : 'Jpm@2026'),
                'phone' => $data['phone'],
                'status' => $data['status'] ?? 1,
            ]);
            $user->assignRole('customer');

            if (isset($data['avatar']) && request()->hasFile('avatar')) {
                $file = request()->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars'), $filename);
                $user->avatar = '/uploads/avatars/' . $filename;
                $user->save();
            }

            $kh = KhachHang::create([
                'id_tai_khoan' => $user->id,
                'ho_ten' => $data['name'],
                'so_dien_thoai' => $data['phone'],
            ]);
            
            DB::commit();
            return clone $kh->load('user');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $kh = KhachHang::findOrFail($id);
            
                        // Update User properties (Name, Email, Status, Avatar)
            if ($kh->user) {
                $kh->user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'status' => $data['status'] ?? 1,
                ]);

                if (isset($data['avatar']) && request()->hasFile('avatar')) {
                    if ($kh->user->avatar && file_exists(public_path($kh->user->avatar))) {
                        @unlink(public_path($kh->user->avatar));
                    }
                    $file = request()->file('avatar');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/avatars'), $filename);
                    $kh->user->avatar = '/uploads/avatars/' . $filename;
                }
                $kh->user->save();
            }

            // Update KhachHang properties
            $kh->update([
                'ho_ten' => $data['name'],
                'so_dien_thoai' => $data['phone'],
            ]);
            
            DB::commit();
            return clone $kh->load('user');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resetPassword($id)
    {
        DB::beginTransaction();
        try {
            $kh = KhachHang::findOrFail($id);
            if ($kh->user) {
                $kh->user->password = Hash::make('Jpm@2026');
                // Revoke tokens so they are forced to re-login with the new password
                $kh->user->tokens()->delete();
                $kh->user->save();
            } else {
                throw new Exception("Không tìm thấy tài khoản người dùng.");
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $kh = KhachHang::findOrFail($id);
            
            // Check dependency
            $hasOrders = \App\Domain\Sales\Models\DonHang::where('id_khach_hang', $kh->id)->exists();
            if ($hasOrders) {
                throw new Exception("Không thể xóa khách hàng này vì đã phát sinh đơn hàng. Vui lòng khóa tài khoản thay vì xóa.");
            }
            
            $user = $kh->user;
            
            $kh->delete(); // Hard delete customer profile
            
            if ($user) {
                // Hard delete user account
                $user->tokens()->delete();
                $user->delete();
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}





