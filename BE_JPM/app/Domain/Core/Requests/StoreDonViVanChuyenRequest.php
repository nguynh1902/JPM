<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonViVanChuyenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_don_vi' => 'required|string|max:100',
            'ma_don_vi' => 'required|string|max:50|unique:don_vi_van_chuyens,ma_don_vi',
            'api_url' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_don_vi.required' => ':attribute không được để trống.',
            'ten_don_vi.string' => ':attribute phải là chuỗi ký tự.',
            'ten_don_vi.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_don_vi.required' => ':attribute không được để trống.',
            'ma_don_vi.string' => ':attribute phải là chuỗi ký tự.',
            'ma_don_vi.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_don_vi.unique' => ':attribute đã tồn tại.',
            'api_url.string' => ':attribute phải là chuỗi ký tự.',
            'api_url.max' => ':attribute không được vượt quá :max ký tự.',
            'api_key.string' => ':attribute phải là chuỗi ký tự.',
            'api_key.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_don_vi' => 'Tên đơn vị vận chuyển',
            'ma_don_vi' => 'Mã đơn vị vận chuyển',
            'api_url' => 'API URL',
            'api_key' => 'API Key',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
