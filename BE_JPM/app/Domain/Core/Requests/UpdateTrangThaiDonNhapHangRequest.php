<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrangThaiDonNhapHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trang_thai' => 'required|string|max:50',
            'ghi_chu' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'trang_thai.required' => ':attribute không được để trống.',
            'trang_thai.string' => ':attribute phải là chuỗi ký tự.',
            'trang_thai.max' => ':attribute không được vượt quá :max ký tự.',
            
            'ghi_chu.string' => ':attribute phải là chuỗi ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'trang_thai' => 'Trạng thái đơn nhập hàng',
            'ghi_chu' => 'Ghi chú',
        ];
    }
}
