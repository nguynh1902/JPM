<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrangThaiDonHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trang_thai_don_hang' => 'required|string|max:50',
            'ghi_chu_noi_bo' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'trang_thai_don_hang.required' => ':attribute không được để trống.',
            'trang_thai_don_hang.string' => ':attribute phải là chuỗi ký tự.',
            'trang_thai_don_hang.max' => ':attribute không được vượt quá :max ký tự.',
            
            'ghi_chu_noi_bo.string' => ':attribute phải là chuỗi ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'trang_thai_don_hang' => 'Trạng thái đơn hàng',
            'ghi_chu_noi_bo' => 'Ghi chú nội bộ',
        ];
    }
}
