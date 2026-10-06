<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoaiPhuongTienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_loai' => [
                'required',
                'string',
                'max:50',
                Rule::unique('loai_phuong_tiens', 'ten_loai')->ignore($this->route('loai_phuong_tien'), 'id_loai_phuong_tien')
            ],
            'mo_ta' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_loai.required' => ':attribute không được để trống.',
            'ten_loai.string' => ':attribute phải là chuỗi ký tự.',
            'ten_loai.max' => ':attribute không được vượt quá :max ký tự.',
            'ten_loai.unique' => ':attribute đã tồn tại.',
            'mo_ta.string' => ':attribute phải là chuỗi ký tự.',
            'mo_ta.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_loai' => 'Tên loại phương tiện',
            'mo_ta' => 'Mô tả',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
