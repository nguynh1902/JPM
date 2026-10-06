<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoDoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Thông tin cơ bản
            'ten_so_do' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'required|string|max:255',
            'id_dong_xe' => 'nullable|integer|exists:dong_xes,id_dong_xe',
            'id_phien_ban' => 'nullable|integer|exists:phien_bans,id_phien_ban',
            'nam_ap_dung_tu' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'nam_ap_dung_den' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'trang_thai' => 'nullable|integer|in:0,1',

            // Chi tiết sơ đồ
            'chi_tiets' => 'nullable|array',
            'chi_tiets.*.id_phu_tung' => 'required|integer|exists:phu_tungs,id_phu_tung',
            'chi_tiets.*.so_thu_tu_tren_so_do' => 'required|integer|min:1',
            'chi_tiets.*.toa_do_x' => 'nullable|numeric',
            'chi_tiets.*.toa_do_y' => 'nullable|numeric',
            'chi_tiets.*.so_luong_yeu_cau' => 'nullable|integer|min:1',
            'chi_tiets.*.ghi_chu' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute phải là chuỗi ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'integer' => ':attribute phải là số nguyên.',
            'exists' => ':attribute không tồn tại.',
            'numeric' => ':attribute phải là số.',
            'min' => ':attribute không được nhỏ hơn :min.',
            'array' => ':attribute phải là mảng.',
            'in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_so_do' => 'Tên sơ đồ',
            'mo_ta' => 'Mô tả',
            'hinh_anh' => 'Hình ảnh',
            'id_dong_xe' => 'Dòng xe',
            'id_phien_ban' => 'Phiên bản',
            'nam_ap_dung_tu' => 'Năm áp dụng từ',
            'nam_ap_dung_den' => 'Năm áp dụng đến',
            'trang_thai' => 'Trạng thái',
            
            'chi_tiets' => 'Danh sách chi tiết sơ đồ',
            'chi_tiets.*.id_phu_tung' => 'Phụ tùng',
            'chi_tiets.*.so_thu_tu_tren_so_do' => 'Số thứ tự trên sơ đồ',
        ];
    }
}
