<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTienTeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_tien_te' => 'required|string|max:10|unique:tien_tes,ma_tien_te',
            'ten_tien_te' => 'required|string|max:50',
            'ky_hieu' => 'nullable|string|max:10',
            'ty_gia' => 'nullable|numeric|min:0',
            'la_mac_dinh' => 'nullable|boolean',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ma_tien_te.required' => ':attribute không được để trống.',
            'ma_tien_te.string' => ':attribute phải là chuỗi ký tự.',
            'ma_tien_te.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_tien_te.unique' => ':attribute đã tồn tại.',
            'ten_tien_te.required' => ':attribute không được để trống.',
            'ten_tien_te.string' => ':attribute phải là chuỗi ký tự.',
            'ten_tien_te.max' => ':attribute không được vượt quá :max ký tự.',
            'ky_hieu.string' => ':attribute phải là chuỗi ký tự.',
            'ky_hieu.max' => ':attribute không được vượt quá :max ký tự.',
            'ty_gia.numeric' => ':attribute phải là số.',
            'ty_gia.min' => ':attribute không được nhỏ hơn :min.',
            'la_mac_dinh.boolean' => ':attribute phải là kiểu logic (true/false).',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ma_tien_te' => 'Mã tiền tệ',
            'ten_tien_te' => 'Tên tiền tệ',
            'ky_hieu' => 'Ký hiệu',
            'ty_gia' => 'Tỷ giá',
            'la_mac_dinh' => 'Là mặc định',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
