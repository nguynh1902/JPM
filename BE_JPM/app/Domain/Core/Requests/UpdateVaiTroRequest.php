<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVaiTroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_vai_tro' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vai_tros', 'ten_vai_tro')->ignore($this->route('vai_tro'), 'id_vai_tro')
            ],
            'mo_ta' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_vai_tro.required' => ':attribute không được để trống.',
            'ten_vai_tro.string' => ':attribute phải là chuỗi ký tự.',
            'ten_vai_tro.max' => ':attribute không được vượt quá :max ký tự.',
            'ten_vai_tro.unique' => ':attribute đã tồn tại.',
            'mo_ta.string' => ':attribute phải là chuỗi ký tự.',
            'mo_ta.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_vai_tro' => 'Tên vai trò',
            'mo_ta' => 'Mô tả',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
