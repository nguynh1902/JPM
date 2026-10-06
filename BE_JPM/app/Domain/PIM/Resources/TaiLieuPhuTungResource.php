<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaiLieuPhuTungResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_tai_lieu' => $this->id_tai_lieu,
            'phu_tung_id' => $this->phu_tung_id,
            'ten_tai_lieu' => $this->ten_tai_lieu,
            'duong_dan' => $this->duong_dan,
            'loai_tai_lieu' => $this->loai_tai_lieu,
            'thoi_gian_tao' => $this->thoi_gian_tao ? $this->thoi_gian_tao->toISOString() : null,
        ];
    }
}
