<?php
namespace App\Domain\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\CMS\Models\SanPham;

class ChiTietDonNhapHang extends Model
{
    protected $guarded = [];

    public function donNhapHang()
    {
        return $this->belongsTo(DonNhapHang::class, 'id_don_nhap_hang');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'id_san_pham');
    }
}
