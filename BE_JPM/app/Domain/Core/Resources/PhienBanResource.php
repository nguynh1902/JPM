<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhienBanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_phien_ban' => $this->id_phien_ban,
            'model_id' => $this->model_id,
            'ten_phien_ban' => $this->ten_phien_ban,
            'nam_san_xuat' => $this->nam_san_xuat ? (int) $this->nam_san_xuat : null,
            'kieu_dang' => $this->kieu_dang,
            'so_cho_ngoi' => $this->so_cho_ngoi ? (int) $this->so_cho_ngoi : null,
            'trang_thai' => (int) $this->trang_thai,

            // Relationships
            'dong_xe' => new VehicleModelResource($this->whenLoaded('dongXe')),
        ];
    }
}
