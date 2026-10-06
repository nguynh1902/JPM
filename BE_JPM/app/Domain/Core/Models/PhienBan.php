<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PhienBan extends Model
{
    protected $table = 'phien_bans';
    protected $fillable = ['id_dong_xe', 'ten_phien_ban', 'nam_san_xuat', 'kieu_dang', 'so_cho_ngoi', 'trang_thai'];

    //
}

