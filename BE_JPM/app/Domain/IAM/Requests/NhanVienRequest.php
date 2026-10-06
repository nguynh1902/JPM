<?php

namespace App\Domain\IAM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NhanVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

        public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $id = $this->route('nhan_vien');
        $userId = null;
        
        if ($id) {
            $nv = \App\Domain\IAM\Models\NhanVien::find($id);
            if ($nv) $userId = $nv->id_tai_khoan;
        }

        $emailRule = 'required|email|max:100';
        $phoneRule = 'required|string|regex:/^[0-9]{9,15}$/';
        
        if ($isUpdate) {
            $emailRule .= '|unique:users,email,' . $userId;
            $phoneRule .= '|unique:nhan_viens,so_dien_thoai,' . $id;
        } else {
            $emailRule .= '|unique:users,email';
            $phoneRule .= '|unique:nhan_viens,so_dien_thoai';
        }

        return [
            'name' => 'required|string|max:100',
            'role' => 'required|string',
            'email' => $emailRule,
            'phone' => $phoneRule,
            'password' => ['nullable', 'string', 'min:8', 'max:25', 'regex:/^[\x20-\x7E]+$/']
        ];
    }

        public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên nhân viên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải từ 9 đến 15 chữ số.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'password.min' => 'Mật khẩu phải dài ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu tối đa là 25 ký tự.',
            'password.regex' => 'Mật khẩu không được chứa dấu Tiếng Việt.'
        ];
    }
}


