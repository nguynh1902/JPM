<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNhaCungCapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_nha_cung_cap' => 'required|string|max:150',
            'ma_nha_cung_cap' => 'required|string|max:50|unique:nha_cung_caps,ma_nha_cung_cap',
            'dia_chi' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_nha_cung_cap.required' => ':attribute không được để trống.',
            'ten_nha_cung_cap.string' => ':attribute phải là chuỗi ký tự.',
            'ten_nha_cung_cap.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_nha_cung_cap.required' => ':attribute không được để trống.',
            'ma_nha_cung_cap.string' => ':attribute phải là chuỗi ký tự.',
            'ma_nha_cung_cap.max' => ':attribute không được vượt quá :max ký tự.',
            'ma_nha_cung_cap.unique' => ':attribute đã tồn tại.',
            'dia_chi.string' => ':attribute phải là chuỗi ký tự.',
            'dia_chi.max' => ':attribute không được vượt quá :max ký tự.',
            'so_dien_thoai.string' => ':attribute phải là chuỗi ký tự.',
            'so_dien_thoai.max' => ':attribute không được vượt quá :max ký tự.',
            'email.email' => ':attribute không đúng định dạng.',
            'email.max' => ':attribute không được vượt quá :max ký tự.',
            'website.string' => ':attribute phải là chuỗi ký tự.',
            'website.max' => ':attribute không được vượt quá :max ký tự.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_nha_cung_cap' => 'Tên nhà cung cấp',
            'ma_nha_cung_cap' => 'Mã nhà cung cấp',
            'dia_chi' => 'Địa chỉ',
            'so_dien_thoai' => 'Số điện thoại',
            'email' => 'Email',
            'website' => 'Website',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
