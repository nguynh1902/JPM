<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\IAM\Requests\LoginRequest;
use App\Domain\IAM\Requests\RegisterRequest;
use App\Domain\IAM\Resources\UserResource;
use App\Domain\IAM\Services\AuthService;
use App\Domain\IAM\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
     * API Quên mật khẩu (Forgot Password)
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'Vui lòng nhập địa chỉ email.',
                'email.email' => 'Địa chỉ email không hợp lệ.',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài khoản với địa chỉ email này.'
                ], 404);
            }

            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo liên kết khôi phục mật khẩu thành công.',
                'reset_token' => $token,
                'email' => $user->email,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý yêu cầu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Đặt lại mật khẩu (Reset Password)
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'token' => 'nullable|string',
                'password' => 'required|string|min:8|max:25|confirmed',
            ], [
                'email.required' => 'Vui lòng nhập email.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu phải từ 8 đến 25 ký tự.',
                'password.max' => 'Mật khẩu phải từ 8 đến 25 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài khoản với email này.'
                ], 404);
            }

            if ($request->filled('token')) {
                $record = DB::table('password_reset_tokens')->where('email', $user->email)->first();
                if (!$record || !Hash::check($request->token, $record->token)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mã khôi phục không hợp lệ hoặc đã hết hạn.'
                    ], 422);
                }
            }

            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đặt lại mật khẩu: ' . $e->getMessage(),
            ], 500);
        }
    }

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
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg,bmp|max:5120',
            ], [
                'name.max' => 'Họ và tên không được vượt quá 100 ký tự.',
                'phone.regex' => 'Số điện thoại không hợp lệ.',
                'avatar.max' => 'Ảnh đại diện không được vượt quá 5MB.',
            ]);

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars'), $filename);
                $user->avatar = '/uploads/avatars/' . $filename;
            } elseif ($request->boolean('remove_avatar') || ($request->has('avatar') && ($request->input('avatar') === '' || $request->input('avatar') === null))) {
                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    @unlink(public_path($user->avatar));
                }
                $user->avatar = null;
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

            if ($user->khachHang) {
                $user->khachHang->ho_ten = $user->name;
                $user->khachHang->so_dien_thoai = $user->phone;
                $user->khachHang->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hồ sơ thành công',
                'data' => [
                    'user' => new UserResource($user)
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
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
                    'min:8',
                    'max:25',
                    'confirmed',
                    'regex:/[a-z]/',
                    'regex:/[\d\W]/',
                ],
            ], [
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                'new_password.max' => 'Mật khẩu mới không được vượt quá 25 ký tự.',
                'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Deactivate Account
     */
    public function deactivateAccount(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->hasRole('admin') || $user->hasRole('Super Admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể vô hiệu hóa tài khoản Quản trị viên tối cao!'
                ], 403);
            }
            
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
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
