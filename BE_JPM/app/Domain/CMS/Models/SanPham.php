<?php

namespace App\Domain\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;

    use HasFactory;

    protected $fillable = [
        'danh_muc_san_pham_id',
        'ma_san_pham',
        'ten_san_pham',
        'slug',
        'mo_ta_chi_tiet',
        'thong_so_ky_thuat',
        'gia_ban',
        'is_gia_lien_he',
        'so_luong_ton',
        'hinh_anh_chinh',
        'thu_vien_anh',
        'video_url',
        'trang_thai'
    ];

    protected $casts = [
        'thu_vien_anh' => 'array',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMucSanPham::class, 'danh_muc_san_pham_id');
    }
}

