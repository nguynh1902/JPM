<?php

namespace App\Domain\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucSanPham extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'ma_danh_muc',
        'ten_danh_muc',
        'slug',
        'trang_thai'
    ];

    public function parent()
    {
        return $this->belongsTo(DanhMucSanPham::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DanhMucSanPham::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'danh_muc_san_pham_id');
    }
}
