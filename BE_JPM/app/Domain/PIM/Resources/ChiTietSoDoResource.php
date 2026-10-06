<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiTietSoDoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_chi_tiet_so_do' => $this->id_chi_tiet_so_do,
            'so_do_id' => $this->so_do_id,
            'phu_tung_id' => $this->phu_tung_id,
            'vi_tri_x' => (float) $this->vi_tri_x,
            'vi_tri_y' => (float) $this->vi_tri_y,
            'ghi_chu' => $this->ghi_chu,

            // Relationships
            'phu_tung' => new PhuTungResource($this->whenLoaded('phuTung')),
        ];
    }
}
