<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiaChiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'dia_chi_id' => $this->dia_chi_id,
            'user_id' => $this->user_id,
            'ten_nguoi_nhan' => $this->ten_nguoi_nhan,
            'so_dien_thoai' => $this->so_dien_thoai,
            'dia_chi_chi_tiet' => $this->dia_chi_chi_tiet,
            'phuong_xa' => $this->phuong_xa,
            'quan_huyen' => $this->quan_huyen,
            'tinh_thanh_pho' => $this->tinh_thanh_pho,
            'quoc_gia' => $this->quoc_gia,
            'la_dia_chi_mac_dinh' => (bool) $this->la_dia_chi_mac_dinh,

            // Relationships
            'khach_hang' => new KhachHangResource($this->whenLoaded('khachHang')),
        ];
    }
}
