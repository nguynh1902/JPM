<?php

namespace App\Domain\MarineCatalog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DongCoModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thuong_hieu_id' => $this->thuong_hieu_id,
            'loai_dong_co_id' => $this->loai_dong_co_id,
            'ma_model' => $this->ma_model,
            'ten_model' => $this->ten_model,
            'hinh_anh' => $this->hinh_anh,
            'hinh_anh_url' => $this->hinh_anh ? asset('storage/' . $this->hinh_anh) : null,
            'nam_san_xuat' => $this->nam_san_xuat,
            'ma_luc' => $this->ma_luc,
            'loai_nhip' => $this->loai_nhip,
            'trang_thai' => $this->trang_thai,
            'thuong_hieu' => [
                'id' => $this->whenLoaded('thuongHieu', function() { return $this->thuongHieu->id; }),
                'name' => $this->whenLoaded('thuongHieu', function() { return $this->thuongHieu->name; }),
            ],
            'loai_dong_co' => [
                'id' => $this->whenLoaded('loaiDongCo', function() { return $this->loaiDongCo->id; }),
                'ten_loai_dong_co' => $this->whenLoaded('loaiDongCo', function() { return $this->loaiDongCo->ten_loai_dong_co; }),
            ]
        ];
    }
}



