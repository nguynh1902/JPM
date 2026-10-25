<?php

namespace App\Domain\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\CMS\Models\SanPham;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\IAM\Models\User;

class TheKho extends Model
{
    protected $guarded = [];

    public function kho()
    {
        return $this->belongsTo(Kho::class, 'id_kho');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'id_san_pham');
    }

    public function phuTung()
    {
        return $this->belongsTo(PhuTung::class, 'id_san_pham')->withTrashed();
    }

    public function nguoiThucHien()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
