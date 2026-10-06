<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuocGiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_quoc_gia' => 'required|string|max:10|unique:quoc_gias,ma_quoc_gia',
            'ten_quoc_gia' => 'required|string|max:100',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ma_quoc_gia.required' => ':attribute không được để trống.',
            'ma_quoc_gia.string' => ':attribute phải là chuỗi ký tự.',
            'ma_quoc_gia.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_quoc_gia.unique' => ':attribute đã tồn tại trong hệ thống.',
            'ten_quoc_gia.required' => ':attribute không được để trống.',
            'ten_quoc_gia.string' => ':attribute phải là chuỗi ký tự.',
            'ten_quoc_gia.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là một số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ma_quoc_gia' => 'Mã quốc gia',
            'ten_quoc_gia' => 'Tên quốc gia',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
