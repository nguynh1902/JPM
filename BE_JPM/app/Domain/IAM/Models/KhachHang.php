<?php

namespace App\Domain\IAM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhachHang extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;
    use SoftDeletes;

    protected $table = 'khach_hangs';
    protected $fillable = ['id_tai_khoan', 'ho_ten', 'ten_cong_ty', 'so_dien_thoai', 'ngay_sinh', 'gioi_tinh', 'hang_thanh_vien', 'diem_thuong', 'ghi_chu'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_tai_khoan');
    }
}


