<?php

namespace App\Domain\Communication\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuThongBao extends Model
{
    protected $table = 'lich_su_thong_baos';
    protected $guarded = [];

    protected $casts = [
        'phan_hoi_gateway' => 'array',
    ];
}
