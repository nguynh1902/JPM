<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class QuocGia extends Model
{
    protected $table = 'quoc_gias';
    protected $fillable = ['ma_quoc_gia', 'ten_quoc_gia', 'trang_thai'];

    //
}

