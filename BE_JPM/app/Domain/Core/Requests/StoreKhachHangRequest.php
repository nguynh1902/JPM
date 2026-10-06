<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tai_khoan' => 'required|integer|exists:tai_khoans,id_tai_khoan|unique:khach_hangs,id_tai_khoan',
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'nullable|string|max:20|unique:khach_hangs,so_dien_thoai',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|integer|in:0,1,2',
            'hang_thanh_vien' => 'nullable|string|max:50',
            'diem_thuong' => 'nullable|integer|min:0',
            'ghi_chu' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_tai_khoan.required' => ':attribute không được để trống.',
            'id_tai_khoan.exists' => ':attribute không tồn tại.',
            'id_tai_khoan.unique' => ':attribute đã được liên kết với một khách hàng khác.',
            
            'ho_ten.required' => ':attribute không được để trống.',
            'ho_ten.string' => ':attribute phải là chuỗi ký tự.',
            'ho_ten.max' => ':attribute không được vượt quá :max ký tự.',
            
            'so_dien_thoai.string' => ':attribute phải là chuỗi ký tự.',
            'so_dien_thoai.max' => ':attribute không được vượt quá :max ký tự.',
            'so_dien_thoai.unique' => ':attribute đã được sử dụng.',
            
            'ngay_sinh.date' => ':attribute không đúng định dạng ngày.',
            
            'gioi_tinh.integer' => ':attribute phải là số nguyên.',
            'gioi_tinh.in' => ':attribute không hợp lệ.',
            
            'hang_thanh_vien.string' => ':attribute phải là chuỗi ký tự.',
            'hang_thanh_vien.max' => ':attribute không được vượt quá :max ký tự.',
            
            'diem_thuong.integer' => ':attribute phải là số nguyên.',
            'diem_thuong.min' => ':attribute không được nhỏ hơn :min.',
            
            'ghi_chu.string' => ':attribute phải là chuỗi ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_tai_khoan' => 'Tài khoản',
            'ho_ten' => 'Họ tên',
            'so_dien_thoai' => 'Số điện thoại',
            'ngay_sinh' => 'Ngày sinh',
            'gioi_tinh' => 'Giới tính',
            'hang_thanh_vien' => 'Hạng thành viên',
            'diem_thuong' => 'Điểm thưởng',
            'ghi_chu' => 'Ghi chú',
        ];
    }
}
