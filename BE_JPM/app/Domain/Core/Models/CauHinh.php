<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class CauHinh extends Model
{
    protected $table = 'cau_hinhs';
    protected $fillable = ['khoa_cau_hinh', 'gia_tri', 'mo_ta', 'loai_gia_tri', 'trang_thai'];

    //
}

