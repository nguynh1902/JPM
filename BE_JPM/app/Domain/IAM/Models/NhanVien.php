<?php

namespace App\Domain\IAM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NhanVien extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;
    use SoftDeletes;

    protected $table = 'nhan_viens';
    protected $fillable = ['id_tai_khoan', 'ma_nhan_vien', 'ho_ten', 'so_dien_thoai'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_tai_khoan');
    }
}


