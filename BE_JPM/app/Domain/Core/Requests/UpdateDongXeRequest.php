<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDongXeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_thuong_hieu' => 'required|integer|exists:thuong_hieus,id_thuong_hieu',
            'id_loai_phuong_tien' => 'required|integer|exists:loai_phuong_tiens,id_loai_phuong_tien',
            'ten_dong_xe' => 'required|string|max:100',
            'nam_bat_dau' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'nam_ket_thuc' => 'nullable|integer|min:1900|max:' . (date('Y') + 5) . '|gte:nam_bat_dau',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_thuong_hieu.required' => ':attribute không được để trống.',
            'id_thuong_hieu.exists' => ':attribute không tồn tại.',
            'id_loai_phuong_tien.required' => ':attribute không được để trống.',
            'id_loai_phuong_tien.exists' => ':attribute không tồn tại.',
            'ten_dong_xe.required' => ':attribute không được để trống.',
            'ten_dong_xe.string' => ':attribute phải là chuỗi ký tự.',
            'ten_dong_xe.max' => ':attribute không được vượt quá :max ký tự.',
            'nam_bat_dau.integer' => ':attribute phải là số nguyên.',
            'nam_bat_dau.min' => ':attribute không hợp lệ.',
            'nam_bat_dau.max' => ':attribute không hợp lệ.',
            'nam_ket_thuc.integer' => ':attribute phải là số nguyên.',
            'nam_ket_thuc.min' => ':attribute không hợp lệ.',
            'nam_ket_thuc.max' => ':attribute không hợp lệ.',
            'nam_ket_thuc.gte' => ':attribute phải lớn hơn hoặc bằng năm bắt đầu.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_thuong_hieu' => 'Thương hiệu',
            'id_loai_phuong_tien' => 'Loại phương tiện',
            'ten_dong_xe' => 'Tên dòng xe',
            'nam_bat_dau' => 'Năm bắt đầu',
            'nam_ket_thuc' => 'Năm kết thúc',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
