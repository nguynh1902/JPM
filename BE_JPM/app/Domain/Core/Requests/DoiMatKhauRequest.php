<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mat_khau_cu' => 'required|string',
            'mat_khau_moi' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'mat_khau_cu.required' => ':attribute không được để trống.',
            'mat_khau_cu.string' => ':attribute phải là chuỗi ký tự.',
            
            'mat_khau_moi.required' => ':attribute không được để trống.',
            'mat_khau_moi.string' => ':attribute phải là chuỗi ký tự.',
            'mat_khau_moi.min' => ':attribute phải có ít nhất :min ký tự.',
            'mat_khau_moi.confirmed' => 'Xác nhận :attribute không khớp.',
        ];
    }

    public function attributes(): array
    {
        return [
            'mat_khau_cu' => 'Mật khẩu cũ',
            'mat_khau_moi' => 'Mật khẩu mới',
        ];
    }
}
