<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThanhToanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_thanh_toan' => $this->id_thanh_toan,
            'ma_thanh_toan' => $this->ma_thanh_toan,
            'id_don_hang' => $this->id_don_hang,
            'id_phuong_thuc_thanh_toan' => $this->id_phuong_thuc_thanh_toan,
            'tong_tien' => (float) $this->tong_tien,
            'trang_thai_thanh_toan' => $this->trang_thai_thanh_toan,
            'ngay_thanh_toan' => $this->ngay_thanh_toan ? $this->ngay_thanh_toan->toISOString() : null,
            'la_dat_coc' => (bool) $this->la_dat_coc,
            'ty_le_dat_coc' => $this->ty_le_dat_coc !== null ? (float) $this->ty_le_dat_coc : null,
            'so_tien_dat_coc' => $this->so_tien_dat_coc !== null ? (float) $this->so_tien_dat_coc : null,
            'so_tien_con_lai' => $this->so_tien_con_lai !== null ? (float) $this->so_tien_con_lai : null,
            'han_thanh_toan_con_lai' => $this->han_thanh_toan_con_lai ? $this->han_thanh_toan_con_lai->toISOString() : null,
            'ghi_chu' => $this->ghi_chu,

            // Relationships
            'don_hang' => new DonHangResource($this->whenLoaded('donHang')),
            'phuong_thuc_thanh_toan' => $this->whenLoaded('phuongThucThanhToan'),
            
            // Collections
            'giao_dich_thanh_toans' => GiaoDichThanhToanResource::collection($this->whenLoaded('giaoDichThanhToans')),
        ];
    }
}
