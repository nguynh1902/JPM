<?php

namespace App\Domain\IAM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

        public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $id = $this->route('id') ?? $this->route('khach_hang');
        $userId = null;
        
        if ($id) {
            $kh = \App\Domain\IAM\Models\KhachHang::find($id);
            if ($kh) $userId = $kh->id_tai_khoan ?? $kh->user_id;
        }

        $emailRule = 'required|email|max:100';
        $phoneRule = 'required|string|regex:/^[0-9]{9,15}$/';
        
        if ($isUpdate) {
            $emailRule .= '|unique:users,email,' . $userId;
            $phoneRule .= '|unique:khach_hangs,so_dien_thoai,' . $id;
        } else {
            $emailRule .= '|unique:users,email';
            $phoneRule .= '|unique:khach_hangs,so_dien_thoai';
        }

        return [
            'name' => 'required|string|max:100',
            'phone' => $phoneRule,
            'email' => $emailRule,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg,bmp|max:5120',
            'status' => 'nullable|integer|in:0,1',
            'password' => ['nullable', 'string', 'min:8', 'max:25', 'regex:/^[\x20-\x7E]+$/']
        ];
    }

    public function messages(): array
    {
                return [
            'name.required' => 'Vui lòng nhập tên khách hàng.',
            'name.max' => 'Tên khách hàng không được vượt quá 100 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ (9-15 chữ số).',
            'phone.unique' => 'Số điện thoại đã được đăng ký bởi khách hàng khác.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được đăng ký.',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện chỉ chấp nhận định dạng: jpeg, png, jpg, gif, webp, svg, bmp.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 5MB.',
            'password.min' => 'Mật khẩu phải dài ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu tối đa là 25 ký tự.',
            'password.regex' => 'Mật khẩu không được chứa dấu Tiếng Việt.'
        ];
    }
}






