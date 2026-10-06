<?php
namespace App\Domain\Procurement\Models;

use Illuminate\Database\Eloquent\Model;

class DonNhapHang extends Model
{
    protected $guarded = [];

    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'id_nha_cung_cap');
    }

    public function kho()
    {
        return $this->belongsTo(Kho::class, 'id_kho');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietDonNhapHang::class, 'id_don_nhap_hang');
    }
}
