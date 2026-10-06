<?php

namespace App\Domain\IAM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NhanVien extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;

    protected $table = 'nhan_viens';
    protected $fillable = ['id_tai_khoan', 'ma_nhan_vien', 'ho_ten', 'so_dien_thoai', 'ngay_sinh', 'ngay_vao_lam', 'phong_ban', 'chuc_vu', 'trang_thai_lam_viec'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_tai_khoan');
    }
}


