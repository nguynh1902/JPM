<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhuTungRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('phu_tung') ?? $this->route('id');
        return [
            'thuong_hieu_id'         => 'required|integer|exists:thuong_hieus,id',
            'nhom_phu_tung_id'       => 'required|integer|exists:nhom_phu_tungs,id',
            'ma_phu_tung'            => 'required|string|max:100|unique:phu_tungs,ma_phu_tung,' . $id,
            'ten_phu_tung'           => 'required|string|max:255',
            'gia_ban'                => 'nullable|numeric|min:0',
            'is_gia_lien_he'         => 'nullable|boolean',
            'trang_thai'             => 'nullable|integer',
            'don_vi_tinh'            => 'nullable|string|max:50',
            'mo_ta'                  => 'nullable|string',
            'thong_so_ky_thuat'      => 'nullable|string',
            'video_url'              => 'nullable|string|max:500',
            'hinh_anh'               => 'nullable|string',
            'thu_vien_anh_kept'      => 'nullable|string',
            'hinh_anh_file'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp|max:5120',
            'thu_vien_anh_files.*'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'thuong_hieu_id.required'   => 'Vui lòng chọn Thương hiệu',
            'nhom_phu_tung_id.required' => 'Vui lòng chọn Nhóm phụ tùng',
            'ma_phu_tung.required'      => 'Mã phụ tùng là bắt buộc',
            'ma_phu_tung.unique'        => 'Mã phụ tùng đã tồn tại trong hệ thống',
            'ten_phu_tung.required'     => 'Tên phụ tùng là bắt buộc',
        ];
    }
}
