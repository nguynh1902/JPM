<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatLaiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email|exists:tai_khoans,email',
            'mat_khau' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Mã xác thực không được để trống.',
            'token.string' => 'Mã xác thực không hợp lệ.',
            
            'email.required' => ':attribute không được để trống.',
            'email.email' => ':attribute không đúng định dạng.',
            'email.exists' => ':attribute không tồn tại trong hệ thống.',
            
            'mat_khau.required' => ':attribute không được để trống.',
            'mat_khau.string' => ':attribute phải là chuỗi ký tự.',
            'mat_khau.min' => ':attribute phải có ít nhất :min ký tự.',
            'mat_khau.confirmed' => 'Xác nhận :attribute không khớp.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'mat_khau' => 'Mật khẩu mới',
        ];
    }
}
