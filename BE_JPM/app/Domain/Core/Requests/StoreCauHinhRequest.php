<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCauHinhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'khoa_cau_hinh' => 'required|string|max:100|unique:cau_hinhs,khoa_cau_hinh',
            'gia_tri' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'loai_gia_tri' => 'nullable|string|max:50',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'khoa_cau_hinh.required' => ':attribute không được để trống.',
            'khoa_cau_hinh.string' => ':attribute phải là chuỗi ký tự.',
            'khoa_cau_hinh.max' => ':attribute không được vượt quá :max ký tự.',
            'khoa_cau_hinh.unique' => ':attribute đã tồn tại.',
            'gia_tri.string' => ':attribute phải là chuỗi ký tự.',
            'mo_ta.string' => ':attribute phải là chuỗi ký tự.',
            'loai_gia_tri.string' => ':attribute phải là chuỗi ký tự.',
            'loai_gia_tri.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'khoa_cau_hinh' => 'Khóa cấu hình',
            'gia_tri' => 'Giá trị',
            'mo_ta' => 'Mô tả',
            'loai_gia_tri' => 'Loại giá trị',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
