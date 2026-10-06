<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class TienTe extends Model
{
    protected $table = 'tien_tes';
    protected $fillable = ['ma_tien_te', 'ten_tien_te', 'ky_hieu', 'ty_gia', 'la_mac_dinh', 'trang_thai'];

    //
}

