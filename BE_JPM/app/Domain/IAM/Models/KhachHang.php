<?php

namespace App\Domain\IAM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhachHang extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;
    use SoftDeletes;

    protected $table = 'khach_hangs';
    protected $fillable = ['id_tai_khoan', 'ho_ten', 'so_dien_thoai'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_tai_khoan');
    }
}


