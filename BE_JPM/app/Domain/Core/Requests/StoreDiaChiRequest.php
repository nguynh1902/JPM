<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiaChiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tai_khoan' => 'required|integer|exists:tai_khoans,id_tai_khoan',
            'ten_nguoi_nhan' => 'required|string|max:100',
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi_cu_the' => 'required|string|max:255',
            'id_phuong_xa' => 'nullable|string|max:20',
            'id_quan_huyen' => 'nullable|string|max:20',
            'id_tinh_thanh' => 'nullable|string|max:20',
            'la_mac_dinh' => 'nullable|boolean',
            'loai_dia_chi' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'id_tai_khoan.required' => ':attribute không được để trống.',
            'id_tai_khoan.exists' => ':attribute không tồn tại.',
            
            'ten_nguoi_nhan.required' => ':attribute không được để trống.',
            'ten_nguoi_nhan.string' => ':attribute phải là chuỗi ký tự.',
            'ten_nguoi_nhan.max' => ':attribute không được vượt quá :max ký tự.',
            
            'so_dien_thoai.required' => ':attribute không được để trống.',
            'so_dien_thoai.string' => ':attribute phải là chuỗi ký tự.',
            'so_dien_thoai.max' => ':attribute không được vượt quá :max ký tự.',
            
            'dia_chi_cu_the.required' => ':attribute không được để trống.',
            'dia_chi_cu_the.string' => ':attribute phải là chuỗi ký tự.',
            'dia_chi_cu_the.max' => ':attribute không được vượt quá :max ký tự.',
            
            'id_phuong_xa.string' => ':attribute phải là chuỗi ký tự.',
            'id_quan_huyen.string' => ':attribute phải là chuỗi ký tự.',
            'id_tinh_thanh.string' => ':attribute phải là chuỗi ký tự.',
            
            'la_mac_dinh.boolean' => ':attribute phải là kiểu logic.',
            
            'loai_dia_chi.string' => ':attribute phải là chuỗi ký tự.',
            'loai_dia_chi.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_tai_khoan' => 'Tài khoản',
            'ten_nguoi_nhan' => 'Tên người nhận',
            'so_dien_thoai' => 'Số điện thoại',
            'dia_chi_cu_the' => 'Địa chỉ cụ thể',
            'id_phuong_xa' => 'Phường xã',
            'id_quan_huyen' => 'Quận huyện',
            'id_tinh_thanh' => 'Tỉnh thành',
            'la_mac_dinh' => 'Là mặc định',
            'loai_dia_chi' => 'Loại địa chỉ',
        ];
    }
}
