<?php

namespace App\Domain\Procurement\Models;

use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    protected $table = 'nha_cung_caps';
    protected $fillable = ['ten_nha_cung_cap', 'ma_nha_cung_cap', 'dia_chi', 'so_dien_thoai', 'email', 'website', 'trang_thai'];

    //
}

