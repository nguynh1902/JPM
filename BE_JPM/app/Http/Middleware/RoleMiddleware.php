<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập.',
                'errors' => []
            ], 401);
        }

        // Lấy mã vai trò từ quan hệ (giả sử bảng vai_tros có cột ma_vai_tro)
        $userRole = strtolower($request->user()->vaiTro->ma_vai_tro ?? '');
        
        // Chuyển danh sách roles cần kiểm tra về chữ thường
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.',
                'errors' => []
            ], 403);
        }

        return $next($request);
    }
}
