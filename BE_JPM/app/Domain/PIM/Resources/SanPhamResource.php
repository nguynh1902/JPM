<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SanPhamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'danh_muc_san_pham_id' => $this->danh_muc_san_pham_id,
            'ma_san_pham' => $this->ma_san_pham,
            'ten_san_pham' => $this->ten_san_pham,
            'slug' => $this->slug,
            'mo_ta_chi_tiet' => $this->mo_ta_chi_tiet,
            'thong_so_ky_thuat' => $this->thong_so_ky_thuat,
            'video_url' => $this->video_url,
            'gia_ban' => $this->gia_ban,
            'is_gia_lien_he' => $this->is_gia_lien_he,
            'so_luong_ton' => $this->so_luong_ton,
            'hinh_anh_chinh' => $this->hinh_anh_chinh,
            'thu_vien_anh' => $this->thu_vien_anh,
            'trang_thai' => $this->trang_thai,
            'danh_muc' => $this->whenLoaded('danhMuc', function() {
                return [
                    'id' => $this->danhMuc->id,
                    'ten_danh_muc' => $this->danhMuc->ten_danh_muc,
                ];
            }),
        ];
    }
}
