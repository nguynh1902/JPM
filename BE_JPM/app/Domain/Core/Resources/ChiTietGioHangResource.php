<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiTietGioHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_chi_tiet_gio_hang' => $this->id_chi_tiet_gio_hang,
            'gio_hang_id' => $this->gio_hang_id,
            'phu_tung_id' => $this->phu_tung_id,
            'so_luong' => (int) $this->so_luong,
            'thoi_gian_them' => $this->thoi_gian_them ? $this->thoi_gian_them->toISOString() : null,

            // Relationships
            'phu_tung' => new PhuTungResource($this->whenLoaded('phuTung')),
        ];
    }
}
