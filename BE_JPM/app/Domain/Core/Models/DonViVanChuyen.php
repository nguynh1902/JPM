<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class DonViVanChuyen extends Model
{
    protected $table = 'don_vi_van_chuyens';
    protected $fillable = ['ten_don_vi', 'ma_don_vi', 'api_url', 'api_key', 'trang_thai'];

    //
}

