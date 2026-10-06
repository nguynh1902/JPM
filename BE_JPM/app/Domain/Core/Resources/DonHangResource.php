<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_don_hang' => $this->id_don_hang,
            'ma_don_hang' => $this->ma_don_hang,
            'user_id' => $this->user_id,
            'id_nhan_vien' => $this->id_nhan_vien,
            'dia_chi_id_giao_hang' => $this->dia_chi_id_giao_hang,
            'id_phuong_thuc_thanh_toan' => $this->id_phuong_thuc_thanh_toan,
            'id_don_vi_van_chuyen' => $this->id_don_vi_van_chuyen,
            
            'ngay_dat_hang' => $this->ngay_dat_hang ? $this->ngay_dat_hang->toISOString() : null,
            'tong_tien_hang' => (float) $this->tong_tien_hang,
            'phi_van_chuyen' => (float) $this->phi_van_chuyen,
            'thue_vat' => (float) $this->thue_vat,
            'giam_gia' => (float) $this->giam_gia,
            'tong_thanh_toan' => (float) $this->tong_thanh_toan,
            
            'ghi_chu_khach_hang' => $this->ghi_chu_khach_hang,
            'ghi_chu_noi_bo' => $this->when($request->user() && $request->user()->isAdmin(), $this->ghi_chu_noi_bo),
            'trang_thai_don_hang' => $this->trang_thai_don_hang,
            'trang_thai_thanh_toan' => $this->trang_thai_thanh_toan,

            // Relationships
            'khach_hang' => new KhachHangResource($this->whenLoaded('khachHang')),
            'nhan_vien' => new NhanVienResource($this->whenLoaded('nhanVien')),
            'dia_chi_giao_hang' => new DiaChiResource($this->whenLoaded('diaChiGiaoHang')),
            'phuong_thuc_thanh_toan' => $this->whenLoaded('phuongThucThanhToan'),
            'don_vi_van_chuyen' => $this->whenLoaded('donViVanChuyen'),
            
            // Collections
            'chi_tiets' => ChiTietDonHangResource::collection($this->whenLoaded('chiTiets')),
            'lich_sus' => LichSuDonHangResource::collection($this->whenLoaded('lichSus')),
        ];
    }
}


