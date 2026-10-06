<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonNhapHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_nha_cung_cap' => 'required|integer|exists:nha_cung_caps,id_nha_cung_cap',
            'id_kho' => 'required|integer|exists:khos,id_kho',
            'id_tien_te' => 'nullable|integer|exists:tien_tes,id_tien_te',
            'ty_gia' => 'nullable|numeric|min:0',
            'ngay_nhap' => 'nullable|date',
            'ghi_chu' => 'nullable|string',
            
            'chi_tiets' => 'required|array|min:1',
            'chi_tiets.*.id_phu_tung' => 'required|integer|exists:phu_tungs,id_phu_tung',
            'chi_tiets.*.so_luong' => 'required|integer|min:1',
            'chi_tiets.*.don_gia' => 'required|numeric|min:0',
            'chi_tiets.*.thue_vat' => 'nullable|numeric|min:0',
            'chi_tiets.*.ghi_chu' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id_nha_cung_cap.required' => ':attribute không được để trống.',
            'id_nha_cung_cap.exists' => ':attribute không tồn tại.',
            
            'id_kho.required' => ':attribute không được để trống.',
            'id_kho.exists' => ':attribute không tồn tại.',
            
            'id_tien_te.exists' => ':attribute không tồn tại.',
            'ty_gia.numeric' => ':attribute phải là số.',
            'ty_gia.min' => ':attribute không hợp lệ.',
            
            'ngay_nhap.date' => ':attribute không hợp lệ.',
            'ghi_chu.string' => ':attribute phải là chuỗi ký tự.',
            
            'chi_tiets.required' => 'Danh sách sản phẩm nhập không được để trống.',
            'chi_tiets.array' => 'Danh sách sản phẩm nhập không hợp lệ.',
            'chi_tiets.min' => 'Vui lòng chọn ít nhất :min sản phẩm.',
            
            'chi_tiets.*.id_phu_tung.required' => 'Phụ tùng không được để trống.',
            'chi_tiets.*.id_phu_tung.exists' => 'Phụ tùng không tồn tại.',
            
            'chi_tiets.*.so_luong.required' => 'Số lượng không được để trống.',
            'chi_tiets.*.so_luong.integer' => 'Số lượng phải là số nguyên.',
            'chi_tiets.*.so_luong.min' => 'Số lượng phải lớn hơn hoặc bằng :min.',
            
            'chi_tiets.*.don_gia.required' => 'Đơn giá không được để trống.',
            'chi_tiets.*.don_gia.numeric' => 'Đơn giá phải là số.',
            'chi_tiets.*.don_gia.min' => 'Đơn giá không được nhỏ hơn :min.',
            
            'chi_tiets.*.thue_vat.numeric' => 'Thuế VAT phải là số.',
            'chi_tiets.*.thue_vat.min' => 'Thuế VAT không được nhỏ hơn :min.',
            
            'chi_tiets.*.ghi_chu.string' => 'Ghi chú phải là chuỗi.',
            'chi_tiets.*.ghi_chu.max' => 'Ghi chú không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_nha_cung_cap' => 'Nhà cung cấp',
            'id_kho' => 'Kho',
            'id_tien_te' => 'Tiền tệ',
            'ty_gia' => 'Tỷ giá',
            'ngay_nhap' => 'Ngày nhập',
            'ghi_chu' => 'Ghi chú',
            'chi_tiets' => 'Chi tiết nhập hàng',
        ];
    }
}
