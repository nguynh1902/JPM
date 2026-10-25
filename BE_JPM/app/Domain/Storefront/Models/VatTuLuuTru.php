<?php

namespace App\Domain\Storefront\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\IAM\Models\User;
use App\Domain\CMS\Models\SanPham;

class VatTuLuuTru extends Model
{
    protected $table = 'vat_tu_luu_trus';

    protected $fillable = [
        'user_id',
        'san_pham_id',
        'ghi_chu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}
