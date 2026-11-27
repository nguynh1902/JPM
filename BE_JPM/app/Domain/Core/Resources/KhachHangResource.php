<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KhachHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $addressText = 'Chưa cập nhật';
        $addressesList = collect();

        if ($this->user) {
            $diaChis = $this->user->relationLoaded('diaChis')
                ? $this->user->diaChis
                : \App\Domain\IAM\Models\DiaChi::where('id_tai_khoan', $this->id_tai_khoan)->get();

            if ($diaChis && $diaChis->isNotEmpty()) {
                $sorted = $diaChis->sortByDesc(fn ($a) => (int) ($a->la_mac_dinh ?? 0))->values();
                $defaultAddr = $sorted->first();
                if ($defaultAddr && !empty($defaultAddr->dia_chi_cu_the)) {
                    $addressText = $defaultAddr->dia_chi_cu_the;
                }
                $addressesList = $sorted->map(fn ($addr) => [
                    'id' => $addr->id,
                    'ten_nguoi_nhan' => $addr->ten_nguoi_nhan,
                    'so_dien_thoai' => $addr->so_dien_thoai,
                    'dia_chi_cu_the' => $addr->dia_chi_cu_the,
                    'la_mac_dinh' => (bool) $addr->la_mac_dinh,
                ])->values();
            }
        }

        return [
            'id' => $this->id,
            'id_tai_khoan' => $this->id_tai_khoan,
            'user_code' => 'KH' . str_pad($this->id, 2, '0', STR_PAD_LEFT),
            'name' => $this->ho_ten,
            'phone' => $this->so_dien_thoai,
            'address' => $addressText,
            'addresses_count' => $addressesList->count(),
            'addresses' => $addressesList,
            'email' => $this->user ? $this->user->email : '',
            'avatar' => ($this->user && !empty($this->user->avatar)) ? (str_starts_with($this->user->avatar, 'http') ? $this->user->avatar : asset($this->user->avatar)) : '',
            'status' => $this->user ? $this->user->status : 1,
            'created_at' => $this->created_at,
        ];
    }
}
