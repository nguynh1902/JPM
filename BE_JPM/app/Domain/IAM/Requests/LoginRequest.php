<?php

namespace App\Domain\IAM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Bất kỳ ai cũng có thể gọi API login
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'login_id' => 'required|string',
            'password' => 'required'
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'login_id.required' => 'Vui lòng nhập số điện thoại hoặc email.',
            
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ];
    }
}




