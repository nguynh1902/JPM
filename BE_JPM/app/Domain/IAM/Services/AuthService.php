<?php

namespace App\Domain\IAM\Services;

use App\Domain\IAM\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{

    /**
     * Handle user registration.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function register(array $data): array
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Create User Account
            $user = User::create([
                'name' => $data['ho'] . ' ' . $data['ten'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'status' => 1,
            ]);

            // 2. Assign 'Customer' Role
            $user->assignRole('customer');

            // 3. Create KhachHang Profile
            \App\Domain\IAM\Models\KhachHang::create([
                'id_tai_khoan' => $user->id,
                'ho_ten' => $user->name,
                'so_dien_thoai' => $user->phone,
            ]);

            // 4. Generate Token
            $token = $user->createToken('auth_token')->plainTextToken;

            // 5. Update last login
            $user->last_login_at = now();
            $user->save();

            \Illuminate\Support\Facades\DB::commit();

            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw new Exception('Lỗi khi đăng ký tài khoản: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle user login.
     *
     * @param array $credentials
     * @return array
     * @throws Exception
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['login_id'])->orWhere('phone', $credentials['login_id'])->first();

        // Validate user existence and password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Email hoặc mật khẩu không chính xác.', 401);
        }

        // Validate account status
        if ($user->status !== 1) {
            throw new Exception('Tài khoản của bạn đã bị khóa hoặc chưa kích hoạt.', 403);
        }

        // Create API Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}




