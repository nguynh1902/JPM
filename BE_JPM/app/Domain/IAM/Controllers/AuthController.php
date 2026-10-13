<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\IAM\Requests\LoginRequest;
use App\Domain\IAM\Requests\RegisterRequest;
use App\Domain\IAM\Resources\UserResource;
use App\Domain\IAM\Services\AuthService;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * API Login
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'token' => $result['token'],
                    'user' => new UserResource($result['user'])
                ]
            ], 200);

        } catch (Exception $e) {
            $code = (int) $e->getCode();
            $statusCode = ($code >= 100 && $code <= 599) ? $code : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * API Register
     */
    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công',
                'data' => [
                    'token' => $result['token'],
                    'user' => new UserResource($result['user'])
                ]
            ], 201);

        } catch (Exception $e) {
            $code = (int) $e->getCode();
            $statusCode = ($code >= 100 && $code <= 599) ? $code : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * API Get Me
     */
    
    /**
     * API Update Profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $data = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:100|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|regex:/^[0-9]{9,15}$/',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:800',
            ], [
                'name.max' => 'Họ và tên không được vượt quá 100 ký tự.',
                'phone.regex' => 'Số điện thoại không hợp lệ.',
                'avatar.max' => 'Ảnh đại diện không được vượt quá 800KB.',
            ]);

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars'), $filename);
                $user->avatar = '/uploads/avatars/' . $filename;
            }

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->phone = $data['phone'] ?? $user->phone;
            $user->save();

            if ($user->nhanVien) {
                $user->nhanVien->ho_ten = $user->name;
                $user->nhanVien->so_dien_thoai = $user->phone;
                $user->nhanVien->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hồ sơ thành công',
                'data' => [
                    'user' => new UserResource($user)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        $userData = new UserResource($request->user());
        \Illuminate\Support\Facades\Log::info('Get Me API response:', $userData->toArray($request));
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $userData
            ]
        ], 200);
    }

    /**
     * API Change Password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'min:25',
                    'confirmed',
                    'regex:/[a-z]/',
                    'regex:/[\d\W]/',
                ],
            ], [
                'new_password.regex' => 'Mật khẩu mới phải chứa ít nhất 1 chữ cái viết thường và 1 chữ số/ký tự đặc biệt.',
            ]);

            $user = $request->user();

            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác'
                ], 400);
            }

            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Logout
     */
    public function deactivateAccount(Request $request)
    {
        try {
            $user = $request->user();
            
            $request->validate([
                'password' => 'required|string'
            ], [
                'password.required' => 'Vui lòng nhập mật khẩu để xác nhận.'
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu không chính xác.'
                ], 422);
            }

            // Lock account
            $user->status = 0;
            $user->save();

            // Revoke tokens to force logout
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tài khoản của bạn đã bị vô hiệu hóa và sẽ bị xóa vĩnh viễn sau 15 ngày.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Lấy plain text token từ Authorization header để invalidate cache
            $bearerToken = $request->bearerToken();

            // Xóa token khỏi Redis cache ngay lập tức
            if ($bearerToken) {
                \App\Domain\IAM\Services\CachedSanctumPersonalAccessToken::invalidateCache($bearerToken);
            }

            // Xóa token khỏi database
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đăng xuất.',
            ], 500);
        }
    }
}







