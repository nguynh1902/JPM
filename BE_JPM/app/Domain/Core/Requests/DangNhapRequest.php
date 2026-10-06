<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangNhapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'mat_khau' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => ':attribute không được để trống.',
            'email.email' => ':attribute không đúng định dạng.',
            'mat_khau.required' => ':attribute không được để trống.',
            'mat_khau.string' => ':attribute phải là chuỗi ký tự.',
            'mat_khau.min' => ':attribute phải có ít nhất :min ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'mat_khau' => 'Mật khẩu',
        ];
    }
}
