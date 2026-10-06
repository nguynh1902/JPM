<?php

namespace App\Domain\MarineCatalog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'model_id' => $this->model_id,
            'thuong_hieu_id' => $this->thuong_hieu_id,
            'loai_phuong_tien_id' => $this->loai_phuong_tien_id,
            'ten_dong_xe' => $this->ten_dong_xe,
            'nam_bat_dau' => $this->nam_bat_dau ? (int) $this->nam_bat_dau : null,
            'nam_ket_thuc' => $this->nam_ket_thuc ? (int) $this->nam_ket_thuc : null,
            'trang_thai' => (int) $this->trang_thai,

            // Relationships
            'thuong_hieu' => new ThuongHieuResource($this->whenLoaded('thuongHieu')),
            'loai_phuong_tien' => new LoaiPhuongTienResource($this->whenLoaded('loaiPhuongTien')),
        ];
    }
}
