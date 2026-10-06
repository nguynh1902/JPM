<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:100|unique:users,email',
            'mat_khau' => 'required|string|min:8|confirmed',
            'ho' => 'nullable|string|max:50',
            'ten' => 'nullable|string|max:50',
            'ho_ten' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => ':attribute không được để trống.',
            'email.email' => ':attribute không đúng định dạng.',
            'email.max' => ':attribute không được vượt quá :max ký tự.',
            'email.unique' => ':attribute đã tồn tại.',
            
            'mat_khau.required' => ':attribute không được để trống.',
            'mat_khau.string' => ':attribute phải là chuỗi ký tự.',
            'mat_khau.min' => ':attribute phải có ít nhất :min ký tự.',
            'mat_khau.confirmed' => 'Xác nhận :attribute không khớp.',
            
            'ho_ten.string' => ':attribute phải là chuỗi ký tự.',
            'ho_ten.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'mat_khau' => 'Mật khẩu',
            'ho' => 'Họ',
            'ten' => 'Tên',
            'ho_ten' => 'Họ tên',
        ];
    }
}
