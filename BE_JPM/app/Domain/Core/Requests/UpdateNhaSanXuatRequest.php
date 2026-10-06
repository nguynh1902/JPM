<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNhaSanXuatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_nha_san_xuat' => 'required|string|max:150',
            'quoc_gia' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_nha_san_xuat.required' => ':attribute không được để trống.',
            'ten_nha_san_xuat.string' => ':attribute phải là chuỗi ký tự.',
            'ten_nha_san_xuat.max' => ':attribute không được vượt quá :max ký tự.',
            'quoc_gia.string' => ':attribute phải là chuỗi ký tự.',
            'quoc_gia.max' => ':attribute không được vượt quá :max ký tự.',
            'website.string' => ':attribute phải là chuỗi ký tự.',
            'website.max' => ':attribute không được vượt quá :max ký tự.',
            'mo_ta.string' => ':attribute phải là chuỗi ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_nha_san_xuat' => 'Tên nhà sản xuất',
            'quoc_gia' => 'Quốc gia',
            'website' => 'Website',
            'mo_ta' => 'Mô tả',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
