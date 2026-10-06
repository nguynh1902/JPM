<?php

namespace App\Domain\IAM\Models;

use Illuminate\Database\Eloquent\Model;

class DiaChi extends Model
{
    protected $table = 'dia_chis';
    protected $fillable = ['id_tai_khoan', 'ten_nguoi_nhan', 'so_dien_thoai', 'dia_chi_cu_the', 'id_phuong_xa', 'id_quan_huyen', 'id_tinh_thanh', 'la_mac_dinh', 'loai_dia_chi'];

    //
}

