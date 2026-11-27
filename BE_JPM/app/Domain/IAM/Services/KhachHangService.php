<?php

namespace App\Domain\IAM\Services;

use App\Domain\IAM\Models\DiaChi;
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
                        ->orWhere('phone', 'like', "%{$s}%")
                        ->orWhereHas('diaChis', fn ($d) => $d->where('dia_chi_cu_the', 'like', "%{$s}%"));
                });
            });
        }

        if (request()->filled('cursor')) {
            return $query->cursorPaginate(50);
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }

        if (request()->boolean('all', false)) {
            return $query->get();
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

            if (!empty($data['initial_address'])) {
                DiaChi::create([
                    'id_tai_khoan' => $user->id,
                    'ten_nguoi_nhan' => $data['name'],
                    'so_dien_thoai' => $data['phone'],
                    'dia_chi_cu_the' => trim($data['initial_address']),
                    'la_mac_dinh' => true,
                ]);
            }
            
            DB::commit();
            $result = clone $kh->load(['user.diaChis']);
            $result->temporary_password = empty($data['password']) ? $plainPassword : null;
            return $result;
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
                $oldStatus = (int) $kh->user->status;
                $newStatus = isset($data['status']) ? (int) $data['status'] : $oldStatus;
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

                if ($oldStatus !== $newStatus) {
                    \App\Domain\IAM\Models\SystemLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'UPDATE',
                        'description' => ($newStatus === 0 ? 'Khóa' : 'Mở khóa') . " tài khoản Khách Hàng ({$kh->ho_ten})",
                        'ip_address' => request()->ip(),
                    ]);
                }

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
                \App\Domain\IAM\Models\SystemLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'UPDATE',
                    'description' => "Đặt lại mật khẩu Khách Hàng ({$kh->ho_ten})",
                    'ip_address' => request()->ip(),
                ]);
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

    public function addAddress(int $khachHangId, array $data): KhachHang
    {
        return DB::transaction(function () use ($khachHangId, $data) {
            $kh = KhachHang::findOrFail($khachHangId);
            $userId = (int) $kh->id_tai_khoan;

            $isDefault = !empty($data['la_mac_dinh']);
            if (DiaChi::where('id_tai_khoan', $userId)->lockForUpdate()->count() === 0) {
                $isDefault = true;
            }
            if ($isDefault) {
                DiaChi::where('id_tai_khoan', $userId)->update(['la_mac_dinh' => false]);
            }

            DiaChi::create([
                'id_tai_khoan' => $userId,
                'ten_nguoi_nhan' => $data['ten_nguoi_nhan'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'dia_chi_cu_the' => $data['dia_chi_cu_the'],
                'la_mac_dinh' => $isDefault,
            ]);

            return $kh->load(['user.diaChis']);
        });
    }

    public function updateAddress(int $khachHangId, int $addressId, array $data): KhachHang
    {
        return DB::transaction(function () use ($khachHangId, $addressId, $data) {
            $kh = KhachHang::findOrFail($khachHangId);
            $userId = (int) $kh->id_tai_khoan;

            $address = DiaChi::where('id_tai_khoan', $userId)->lockForUpdate()->findOrFail($addressId);
            $isDefault = !empty($data['la_mac_dinh']);

            if ($isDefault && !$address->la_mac_dinh) {
                DiaChi::where('id_tai_khoan', $userId)->update(['la_mac_dinh' => false]);
            } elseif (!$isDefault && $address->la_mac_dinh) {
                if (DiaChi::where('id_tai_khoan', $userId)->count() <= 1) {
                    $isDefault = true;
                } else {
                    $another = DiaChi::where('id_tai_khoan', $userId)->where('id', '!=', $addressId)->first();
                    if ($another) {
                        $another->update(['la_mac_dinh' => true]);
                    }
                }
            }

            $address->update([
                'ten_nguoi_nhan' => $data['ten_nguoi_nhan'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'dia_chi_cu_the' => $data['dia_chi_cu_the'],
                'la_mac_dinh' => $isDefault,
            ]);

            return $kh->load(['user.diaChis']);
        });
    }

    public function setDefaultAddress(int $khachHangId, int $addressId): KhachHang
    {
        return DB::transaction(function () use ($khachHangId, $addressId) {
            $kh = KhachHang::findOrFail($khachHangId);
            $userId = (int) $kh->id_tai_khoan;

            $address = DiaChi::where('id_tai_khoan', $userId)->lockForUpdate()->findOrFail($addressId);
            DiaChi::where('id_tai_khoan', $userId)->update(['la_mac_dinh' => false]);
            $address->update(['la_mac_dinh' => true]);

            return $kh->load(['user.diaChis']);
        });
    }

    public function deleteAddress(int $khachHangId, int $addressId): KhachHang
    {
        return DB::transaction(function () use ($khachHangId, $addressId) {
            $kh = KhachHang::findOrFail($khachHangId);
            $userId = (int) $kh->id_tai_khoan;

            $address = DiaChi::where('id_tai_khoan', $userId)->lockForUpdate()->findOrFail($addressId);
            $wasDefault = (bool) $address->la_mac_dinh;
            $address->delete();

            if ($wasDefault) {
                $firstRemaining = DiaChi::where('id_tai_khoan', $userId)->first();
                if ($firstRemaining) {
                    $firstRemaining->update(['la_mac_dinh' => true]);
                }
            }

            return $kh->load(['user.diaChis']);
        });
    }
}
