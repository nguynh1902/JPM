<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class DonViTinh extends Model
{
    protected $table = 'don_vi_tinhs';
    protected $fillable = ['ten_don_vi', 'mo_ta', 'trang_thai'];

    //
}

