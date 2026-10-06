<?php

namespace App\Domain\MarineCatalog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoaiDongCoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_loai_dong_co' => $this->ma_loai_dong_co,
            'ten_loai_dong_co' => $this->ten_loai_dong_co,
            'trang_thai' => $this->trang_thai,
        ];
    }
}

