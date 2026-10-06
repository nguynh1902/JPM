<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GioHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gio_hang_id' => $this->gio_hang_id,
            'user_id' => $this->user_id,
            'thoi_gian_tao' => $this->thoi_gian_tao ? $this->thoi_gian_tao->toISOString() : null,
            'thoi_gian_cap_nhat' => $this->thoi_gian_cap_nhat ? $this->thoi_gian_cap_nhat->toISOString() : null,

            // Relationships
            'khach_hang' => new KhachHangResource($this->whenLoaded('khachHang')),
            'chi_tiets' => ChiTietGioHangResource::collection($this->whenLoaded('chiTiets')),
        ];
    }
}
