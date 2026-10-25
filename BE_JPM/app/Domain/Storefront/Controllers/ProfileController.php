<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\IAM\Models\KhachHang;
use App\Domain\IAM\Models\DiaChi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user()->load('khachHang');
        return response()->json($user);
    }

    public function changeEmail(Request $request)
    {
        return $this->updateProfile($request);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $emailChanged = $request->email && $request->email !== $user->email;

        // Validation rules
        $rules = [
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'ten_cong_ty'  => 'nullable|string|max:255',
        ];

        // Chỉ bắt nhập mật khẩu khi email thay đổi
        if ($emailChanged) {
            $rules['current_password'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Nếu email bị đổi → kiểm tra mật khẩu
        if ($emailChanged) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'errors' => ['current_password' => ['Mật khẩu không chính xác. Không thể đổi Email.']]
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $user->name  = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->save();

            if ($user->khachHang) {
                $user->khachHang->ho_ten      = $request->name;
                $user->khachHang->so_dien_thoai = $request->phone;
                $user->khachHang->ten_cong_ty = $request->ten_cong_ty;
                $user->khachHang->save();
            }

            DB::commit();
            return response()->json(['message' => 'Cập nhật thông tin thành công.', 'user' => $user->load('khachHang')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Profile Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật thông tin.'], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Mật khẩu hiện tại không đúng.']]], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công.']);
    }

    // --- Addresses ---
    public function getAddresses(Request $request)
    {
        $addresses = DiaChi::where('id_tai_khoan', $request->user()->id)->orderBy('la_mac_dinh', 'desc')->get();
        return response()->json($addresses);
    }

    public function addAddress(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'ten_nguoi_nhan' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi_cu_the' => 'required|string',
            'la_mac_dinh' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $laMacDinh = $request->input('la_mac_dinh', false);

            // If this is the first address, make it default
            if (DiaChi::where('id_tai_khoan', $user->id)->lockForUpdate()->count() === 0) {
                $laMacDinh = true;
            }

            if ($laMacDinh) {
                DiaChi::where('id_tai_khoan', $user->id)->update(['la_mac_dinh' => false]);
            }

            $address = DiaChi::create([
                'id_tai_khoan' => $user->id,
                'ten_nguoi_nhan' => $request->ten_nguoi_nhan,
                'so_dien_thoai' => $request->so_dien_thoai,
                'dia_chi_cu_the' => $request->dia_chi_cu_the,
                'la_mac_dinh' => $laMacDinh,
            ]);

            DB::commit();
            return response()->json(['message' => 'Thêm địa chỉ thành công.', 'address' => $address]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add Address Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi thêm địa chỉ.'], 500);
        }
    }

    public function updateAddress(Request $request, $id)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'ten_nguoi_nhan' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi_cu_the' => 'required|string',
            'la_mac_dinh' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $address = DiaChi::where('id_tai_khoan', $user->id)->lockForUpdate()->findOrFail($id);
            $laMacDinh = $request->input('la_mac_dinh', false);

            if ($laMacDinh && !$address->la_mac_dinh) {
                DiaChi::where('id_tai_khoan', $user->id)->update(['la_mac_dinh' => false]);
            } else if (!$laMacDinh && $address->la_mac_dinh) {
                // Cannot unset default address if it's the only one
                if (DiaChi::where('id_tai_khoan', $user->id)->count() > 1) {
                    // Not allowed to just unset, must set another one as default. We'll ignore the unset.
                    $laMacDinh = true; 
                }
            }

            $address->update([
                'ten_nguoi_nhan' => $request->ten_nguoi_nhan,
                'so_dien_thoai' => $request->so_dien_thoai,
                'dia_chi_cu_the' => $request->dia_chi_cu_the,
                'la_mac_dinh' => $laMacDinh,
            ]);

            DB::commit();
            return response()->json(['message' => 'Cập nhật địa chỉ thành công.', 'address' => $address]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Address Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật địa chỉ.'], 500);
        }
    }

    public function deleteAddress(Request $request, $id)
    {
        $user = $request->user();
        
        try {
            DB::beginTransaction();
            $address = DiaChi::where('id_tai_khoan', $user->id)->lockForUpdate()->findOrFail($id);
            
            $wasDefault = $address->la_mac_dinh;
            $address->delete();

            // If we deleted the default address, make the first remaining one default
            if ($wasDefault) {
                $firstRemaining = DiaChi::where('id_tai_khoan', $user->id)->first();
                if ($firstRemaining) {
                    $firstRemaining->update(['la_mac_dinh' => true]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Xóa địa chỉ thành công.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Address Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa địa chỉ.'], 500);
        }
    }

    public function setDefaultAddress(Request $request, $id)
    {
        $user = $request->user();
        
        try {
            DB::beginTransaction();
            $address = DiaChi::where('id_tai_khoan', $user->id)->lockForUpdate()->findOrFail($id);

            DiaChi::where('id_tai_khoan', $user->id)->update(['la_mac_dinh' => false]);
            $address->update(['la_mac_dinh' => true]);

            DB::commit();
            return response()->json(['message' => 'Đã thiết lập địa chỉ mặc định.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Set Default Address Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi thiết lập địa chỉ.'], 500);
        }
    }

    public function getCart(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'cart_data' => $user->cart_data ? json_decode($user->cart_data) : []
        ]);
    }

    public function updateCart(Request $request)
    {
        $user = $request->user();
        $user->cart_data = json_encode($request->input('cart_data', []));
        $user->save();
        return response()->json(['message' => 'Cart updated successfully.']);
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->limit(20)->get();

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications()->count(),
            'data' => $notifications
        ]);
    }

    public function markNotificationsAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu đã đọc tất cả thông báo.'
        ]);
    }
}
