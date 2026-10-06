<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNhanVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tai_khoan' => [
                'required',
                'integer',
                'exists:tai_khoans,id_tai_khoan',
                Rule::unique('nhan_viens', 'id_tai_khoan')->ignore($this->route('nhan_vien'), 'id_nhan_vien')
            ],
            'ma_nhan_vien' => [
                'required',
                'string',
                'max:50',
                Rule::unique('nhan_viens', 'ma_nhan_vien')->ignore($this->route('nhan_vien'), 'id_nhan_vien')
            ],
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('nhan_viens', 'so_dien_thoai')->ignore($this->route('nhan_vien'), 'id_nhan_vien')
            ],
            'ngay_sinh' => 'nullable|date',
            'ngay_vao_lam' => 'nullable|date',
            'phong_ban' => 'nullable|string|max:100',
            'chuc_vu' => 'nullable|string|max:100',
            'trang_thai_lam_viec' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'id_tai_khoan.required' => ':attribute không được để trống.',
            'id_tai_khoan.exists' => ':attribute không tồn tại.',
            'id_tai_khoan.unique' => ':attribute đã được liên kết với một nhân viên khác.',
            
            'ma_nhan_vien.required' => ':attribute không được để trống.',
            'ma_nhan_vien.string' => ':attribute phải là chuỗi ký tự.',
            'ma_nhan_vien.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_nhan_vien.unique' => ':attribute đã tồn tại.',
            
            'ho_ten.required' => ':attribute không được để trống.',
            'ho_ten.string' => ':attribute phải là chuỗi ký tự.',
            'ho_ten.max' => ':attribute không được vượt quá :max ký tự.',
            
            'so_dien_thoai.string' => ':attribute phải là chuỗi ký tự.',
            'so_dien_thoai.max' => ':attribute không được vượt quá :max ký tự.',
            'so_dien_thoai.unique' => ':attribute đã được sử dụng.',
            
            'ngay_sinh.date' => ':attribute không đúng định dạng ngày.',
            'ngay_vao_lam.date' => ':attribute không đúng định dạng ngày.',
            
            'phong_ban.string' => ':attribute phải là chuỗi ký tự.',
            'phong_ban.max' => ':attribute không được vượt quá :max ký tự.',
            
            'chuc_vu.string' => ':attribute phải là chuỗi ký tự.',
            'chuc_vu.max' => ':attribute không được vượt quá :max ký tự.',
            
            'trang_thai_lam_viec.string' => ':attribute phải là chuỗi ký tự.',
            'trang_thai_lam_viec.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_tai_khoan' => 'Tài khoản',
            'ma_nhan_vien' => 'Mã nhân viên',
            'ho_ten' => 'Họ tên',
            'so_dien_thoai' => 'Số điện thoại',
            'ngay_sinh' => 'Ngày sinh',
            'ngay_vao_lam' => 'Ngày vào làm',
            'phong_ban' => 'Phòng ban',
            'chuc_vu' => 'Chức vụ',
            'trang_thai_lam_viec' => 'Trạng thái làm việc',
        ];
    }
}
