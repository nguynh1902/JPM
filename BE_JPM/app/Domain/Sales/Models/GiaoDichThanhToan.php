<?php

namespace App\Domain\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class GiaoDichThanhToan extends Model
{
    protected $table = 'giao_dich_thanh_toans';
    protected $guarded = [];

    protected $casts = [
        'so_tien' => 'decimal:2',
        'du_lieu_raw' => 'array',
        'ngay_giao_dich' => 'datetime',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'id_doi_tuong');
    }

    public function yeuCauBaoGia()
    {
        return $this->belongsTo(YeuCauBaoGia::class, 'id_doi_tuong');
    }
}
