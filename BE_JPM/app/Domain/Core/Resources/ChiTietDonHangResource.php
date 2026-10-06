<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiTietDonHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_chi_tiet_don' => $this->id_chi_tiet_don,
            'id_don_hang' => $this->id_don_hang,
            'phu_tung_id' => $this->phu_tung_id,
            'so_luong' => (int) $this->so_luong,
            'don_gia' => (float) $this->don_gia,
            'thue_vat' => (float) $this->thue_vat,
            'thanh_tien' => (float) $this->thanh_tien,
            'ghi_chu' => $this->ghi_chu,

            // Relationships
            'phu_tung' => new PhuTungResource($this->whenLoaded('phuTung')),
        ];
    }
}
