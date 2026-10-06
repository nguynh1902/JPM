<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XoaSanPhamKhoiGioHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:chi_tiet_gio_hangs,id_chi_tiet_gio_hang',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.array' => 'Danh sách sản phẩm không hợp lệ.',
            'ids.*.integer' => 'Sản phẩm không hợp lệ.',
            'ids.*.exists' => 'Sản phẩm không tồn tại trong giỏ hàng.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ids' => 'Danh sách sản phẩm',
        ];
    }
}
