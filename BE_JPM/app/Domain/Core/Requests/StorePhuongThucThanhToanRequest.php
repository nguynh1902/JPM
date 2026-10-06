<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhuongThucThanhToanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_phuong_thuc' => 'required|string|max:50',
            'mo_ta' => 'nullable|string',
            'phi_xu_ly' => 'nullable|numeric|min:0',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_phuong_thuc.required' => ':attribute không được để trống.',
            'ten_phuong_thuc.string' => ':attribute phải là chuỗi ký tự.',
            'ten_phuong_thuc.max' => ':attribute không được vượt quá :max ký tự.',
            'mo_ta.string' => ':attribute phải là chuỗi ký tự.',
            'phi_xu_ly.numeric' => ':attribute phải là số.',
            'phi_xu_ly.min' => ':attribute không được nhỏ hơn :min.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_phuong_thuc' => 'Tên phương thức thanh toán',
            'mo_ta' => 'Mô tả',
            'phi_xu_ly' => 'Phí xử lý',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
