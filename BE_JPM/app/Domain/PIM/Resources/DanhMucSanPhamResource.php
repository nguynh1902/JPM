<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DanhMucSanPhamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
                return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'ma_danh_muc' => $this->ma_danh_muc,
            'ten_danh_muc' => $this->ten_danh_muc,
            'slug' => $this->slug,
            'trang_thai' => $this->trang_thai,
            'children' => DanhMucSanPhamResource::collection($this->whenLoaded('children'))
        ];
    }
}
