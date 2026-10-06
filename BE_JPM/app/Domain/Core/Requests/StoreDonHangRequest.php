<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_khach_hang' => 'nullable|integer|exists:khach_hangs,id_khach_hang',
            'id_dia_chi_giao_hang' => 'required|integer|exists:dia_chis,id_dia_chi',
            'id_phuong_thuc_thanh_toan' => 'required|integer|exists:phuong_thuc_thanh_toans,id_phuong_thuc',
            'id_don_vi_van_chuyen' => 'nullable|integer|exists:don_vi_van_chuyens,id_don_vi',
            'ghi_chu_khach_hang' => 'nullable|string',
            
            // Nếu mua trực tiếp không qua giỏ hàng
            'san_phams' => 'nullable|array',
            'san_phams.*.id_phu_tung' => 'required_with:san_phams|integer|exists:phu_tungs,id_phu_tung',
            'san_phams.*.so_luong' => 'required_with:san_phams|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_khach_hang.integer' => ':attribute phải là số nguyên.',
            'id_khach_hang.exists' => ':attribute không tồn tại.',
            
            'id_dia_chi_giao_hang.required' => ':attribute không được để trống.',
            'id_dia_chi_giao_hang.exists' => ':attribute không tồn tại.',
            
            'id_phuong_thuc_thanh_toan.required' => ':attribute không được để trống.',
            'id_phuong_thuc_thanh_toan.exists' => ':attribute không tồn tại.',
            
            'id_don_vi_van_chuyen.integer' => ':attribute phải là số nguyên.',
            'id_don_vi_van_chuyen.exists' => ':attribute không tồn tại.',
            
            'ghi_chu_khach_hang.string' => ':attribute phải là chuỗi ký tự.',
            
            'san_phams.array' => 'Danh sách sản phẩm không hợp lệ.',
            'san_phams.*.id_phu_tung.required_with' => 'Phụ tùng không được để trống.',
            'san_phams.*.id_phu_tung.exists' => 'Phụ tùng không tồn tại.',
            'san_phams.*.so_luong.required_with' => 'Số lượng không được để trống.',
            'san_phams.*.so_luong.min' => 'Số lượng phải lớn hơn hoặc bằng :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_khach_hang' => 'Khách hàng',
            'id_dia_chi_giao_hang' => 'Địa chỉ giao hàng',
            'id_phuong_thuc_thanh_toan' => 'Phương thức thanh toán',
            'id_don_vi_van_chuyen' => 'Đơn vị vận chuyển',
            'ghi_chu_khach_hang' => 'Ghi chú',
        ];
    }
}
