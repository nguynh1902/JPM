<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPhuTungResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'thuong_hieu_id'    => $this->thuong_hieu_id,
            'nhom_phu_tung_id'  => $this->nhom_phu_tung_id,
            'ma_phu_tung'       => $this->ma_phu_tung,
            'ten_phu_tung'      => $this->ten_phu_tung,
            'gia_ban'           => $this->gia_ban !== null ? (float) $this->gia_ban : null,
            'is_gia_lien_he'    => (bool) $this->is_gia_lien_he,
            'trang_thai'        => (int) $this->trang_thai,
            'don_vi_tinh'       => $this->don_vi_tinh,
            'mo_ta'             => $this->mo_ta,
            'thong_so_ky_thuat' => $this->thong_so_ky_thuat,
            'video_url'         => $this->video_url,
            'hinh_anh'          => $this->hinh_anh,
            'thu_vien_anh'      => $this->thu_vien_anh ?? [],
            // Relations
            'thuong_hieu'       => $this->whenLoaded('thuongHieu', fn() => [
                'id'   => $this->thuongHieu->id,
                'name' => $this->thuongHieu->name,
                'code' => $this->thuongHieu->code,
            ]),
            'nhom_phu_tung'     => $this->whenLoaded('nhomPhuTung', fn() => [
                'id'       => $this->nhomPhuTung->id,
                'ten_nhom' => $this->nhomPhuTung->ten_nhom,
            ]),
            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
