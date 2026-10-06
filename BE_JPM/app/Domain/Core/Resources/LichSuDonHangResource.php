<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LichSuDonHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_lich_su' => $this->id_lich_su,
            'id_don_hang' => $this->id_don_hang,
            'trang_thai_don_hang' => $this->trang_thai_don_hang,
            'ghi_chu' => $this->ghi_chu,
            'thoi_gian_tao' => $this->thoi_gian_tao ? $this->thoi_gian_tao->toISOString() : null,
            'nguoi_tao' => $this->nguoi_tao,
        ];
    }
}
