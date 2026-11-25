<?php

namespace App\Domain\IAM\Services;

use App\Domain\IAM\Models\KhachHang;
use App\Domain\IAM\Models\User;
use App\Domain\Core\Services\ImageUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class KhachHangService
{
    public function getById($id)
    {
        return KhachHang::with(['user.diaChis'])->findOrFail($id);
    }

    public function getAll(array $filters = [], ?int $perPage = null)
    {
        $query = KhachHang::with(['user.diaChis'])->orderBy('id', 'desc');

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($sub) use ($s) {
                    $sub->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%");
                })->orWhere('ten_cong_ty', 'like', "%{$s}%");
            });
        }

        if (request()->filled('cursor')) {
            return $query->cursorPaginate(50);
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->limit(500)->get();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            if (User::where('email', $data['email'])->exists()) {
                throw new Exception('Email này đã được sử dụng.', 422);
            }

            $plainPassword = !empty($data['password']) ? $data['password'] : (Str::random(10) . '!9');
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($plainPassword),
                'phone' => $data['phone'],
                'status' => $data['status'] ?? 1,
            ]);
            $user->assignRole('customer');

            if (request()->hasFile('avatar')) {
                $user->avatar = ImageUploadService::storeUploadedFile(
                    request()->file('avatar'),
                    'uploads/avatars',
                    'avatar',
                    400
                );
                $user->save();
            }

            $kh = KhachHang::create([
                'id_tai_khoan' => $user->id,
                'ho_ten' => $data['name'],
                'so_dien_thoai' => $data['phone'],
            ]);
            
            DB::commit();
            return clone $kh->load(['user.diaChis']);
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
            
            // Update User properties (Name, Email, Phone, Status, Avatar)
            if ($kh->user) {
                $newStatus = isset($data['status']) ? (int) $data['status'] : (int) $kh->user->status;
                $kh->user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? $kh->user->phone,
                    'status' => $newStatus,
                ]);

                if (request()->hasFile('avatar')) {
                    $oldAvatar = $kh->user->avatar;
                    $kh->user->avatar = ImageUploadService::storeUploadedFile(
                        request()->file('avatar'),
                        'uploads/avatars',
                        'avatar',
                        400
                    );
                    ImageUploadService::delete($oldAvatar);
                } elseif (!empty($data['remove_avatar']) || request()->boolean('remove_avatar') || (array_key_exists('avatar', $data) && ($data['avatar'] === '' || $data['avatar'] === null))) {
                    ImageUploadService::delete($kh->user->avatar);
                    $kh->user->avatar = null;
                }
                $kh->user->save();

                if ($newStatus === 0) {
                    $kh->user->tokens()->delete();
                    CachedSanctumPersonalAccessToken::invalidateUserTokens($kh->user->id);
                }
            }

            // Update KhachHang properties
            $kh->update([
                'ho_ten' => $data['name'],
                'so_dien_thoai' => $data['phone'],
            ]);
            
            DB::commit();
            return clone $kh->load(['user.diaChis']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resetPassword($id): string
    {
        DB::beginTransaction();
        try {
            $kh = KhachHang::findOrFail($id);
            $tempPassword = Str::random(10) . '!9';
            if ($kh->user) {
                $kh->user->password = Hash::make($tempPassword);
                // Revoke tokens + invalidate Redis cache so they are forced to re-login immediately
                $kh->user->tokens()->delete();
                CachedSanctumPersonalAccessToken::invalidateUserTokens($kh->user->id);
                $kh->user->save();
            } else {
                throw new Exception("Không tìm thấy tài khoản người dùng.");
            }
            DB::commit();
            return $tempPassword;
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
            
            $kh->delete();
            
            if ($user) {
                $oldAvatar = $user->avatar;
                $user->tokens()->delete();
                CachedSanctumPersonalAccessToken::invalidateUserTokens($user->id);
                $user->delete();
                ImageUploadService::delete($oldAvatar);
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
