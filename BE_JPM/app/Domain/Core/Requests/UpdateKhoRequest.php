<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_kho' => 'required|string|max:100',
            'ma_kho' => [
                'required',
                'string',
                'max:50',
                Rule::unique('khos', 'ma_kho')->ignore($this->route('kho'), 'id_kho')
            ],
            'dia_chi' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_kho.required' => ':attribute không được để trống.',
            'ten_kho.string' => ':attribute phải là chuỗi ký tự.',
            'ten_kho.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_kho.required' => ':attribute không được để trống.',
            'ma_kho.string' => ':attribute phải là chuỗi ký tự.',
            'ma_kho.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_kho.unique' => ':attribute đã tồn tại.',
            'dia_chi.string' => ':attribute phải là chuỗi ký tự.',
            'dia_chi.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_kho' => 'Tên kho',
            'ma_kho' => 'Mã kho',
            'dia_chi' => 'Địa chỉ',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
