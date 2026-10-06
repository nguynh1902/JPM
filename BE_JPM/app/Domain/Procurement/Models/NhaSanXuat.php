<?php

namespace App\Domain\Procurement\Models;

use Illuminate\Database\Eloquent\Model;

class NhaSanXuat extends Model
{
    protected $table = 'nha_san_xuats';
    protected $fillable = ['ten_nha_san_xuat', 'quoc_gia', 'website', 'mo_ta', 'trang_thai'];

    //
}

