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
        $user = User::where('email', $credentials['login_id'])
            ->orWhere('phone', $credentials['login_id'])
            ->first();

        // Validate user existence and password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Email hoặc mật khẩu không chính xác.', 401);
        }

        // Validate account status
        if ((int) $user->status !== 1) {
            throw new Exception('Tài khoản của bạn đã bị khóa hoặc chưa kích hoạt.', 403);
        }

        // Chặn đăng nhập nếu toàn bộ vai trò được gán đều đang bị khóa (status = 0)
        $roles = $user->roles()->get();
        if ($roles->isNotEmpty() && !$roles->contains(fn($r) => (int) ($r->status ?? 1) === 1)) {
            throw new Exception('Vai trò của bạn hiện đang bị tạm khóa. Vui lòng liên hệ Quản trị viên.', 403);
        }

        // Xóa token cũ (không giữ nhiều token, tiết kiệm bộ nhớ DB)
        // Mỗi người chỉ giữ tối đa 3 token gần nhất (multi-device hợp lý)
        $currentTokenCount = $user->tokens()->count();
        if ($currentTokenCount >= 3) {
            $user->tokens()->oldest()->limit($currentTokenCount - 2)->delete();
        }

        // Create API Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Cache user roles để không phải JOIN bảng permissions mỗi request
        try {
            $cacheKey = 'user_roles:' . $user->id;
            \Illuminate\Support\Facades\Cache::put(
                $cacheKey,
                $user->getRoleNames()->toArray(),
                now()->addHours(2)
            );
        } catch (\Throwable $e) {
            // Silently ignore cache failure
        }

        // Update last login timestamp (async: không block response)
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $user->id)
            ->update(['last_login_at' => now()]);

        try {
            \App\Domain\IAM\Models\SystemLog::create([
                'user_id' => $user->id,
                'action' => 'LOGIN',
                'description' => 'Đăng nhập hệ thống (' . $user->email . ')',
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking audit log
        }

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}




