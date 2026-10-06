<?php

namespace App\Domain\Core\Services;

use App\Domain\IAM\Models\User;
use App\Models\KhachHangProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class XacThucService
{
    /**
     * Đăng nhập tài khoản
     *
     * @throws ValidationException
     */
    public function dangNhap(string $email, string $matKhau): User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($matKhau, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        if ($user->status !== 1) {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản của bạn đã bị khóa hoặc chưa được kích hoạt.'],
            ]);
        }

        $user->last_login_at = now();
        $user->save();

        return $user;
    }

    /**
     * Đăng ký tài khoản khách hàng mới
     *
     * @throws \Throwable
     */
    public function dangKy(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['ho_ten'] ?? ($data['ho'] . ' ' . $data['ten']),
                'email'    => $data['email'],
                'password' => Hash::make($data['mat_khau']),
                'status'   => 1,
            ]);

            $user->assignRole('customer');

            KhachHangProfile::create([
                'user_id' => $user->id,
            ]);

            return $user;
        });
    }

    /**
     * Đổi mật khẩu tài khoản
     *
     * @throws ValidationException
     */
    public function doiMatKhau(User $user, string $matKhauCu, string $matKhauMoi): bool
    {
        if (!Hash::check($matKhauCu, $user->password)) {
            throw ValidationException::withMessages([
                'mat_khau_cu' => ['Mật khẩu cũ không chính xác.'],
            ]);
        }

        $user->password = Hash::make($matKhauMoi);
        return $user->save();
    }
}

