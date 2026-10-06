<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatSoLuongGioHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'so_luong' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'so_luong.required' => ':attribute không được để trống.',
            'so_luong.integer' => ':attribute phải là số nguyên.',
            'so_luong.min' => ':attribute phải lớn hơn hoặc bằng :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'so_luong' => 'Số lượng',
        ];
    }
}
