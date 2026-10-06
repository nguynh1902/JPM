<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonViTinhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_don_vi' => 'required|string|max:50|unique:don_vi_tinhs,ten_don_vi',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_don_vi.required' => ':attribute không được để trống.',
            'ten_don_vi.string' => ':attribute phải là chuỗi ký tự.',
            'ten_don_vi.max' => ':attribute không được vượt quá :max ký tự.',
            'ten_don_vi.unique' => ':attribute đã tồn tại.',
            'mo_ta.string' => ':attribute phải là chuỗi ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_don_vi' => 'Tên đơn vị tính',
            'mo_ta' => 'Mô tả',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
