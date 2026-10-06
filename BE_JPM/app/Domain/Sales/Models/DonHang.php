<?php 
namespace App\Domain\Sales\Models; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class DonHang extends Model { 
    use SoftDeletes;
    
    protected $guarded = []; 
    
    public function khachHang() { 
        return $this->belongsTo(\App\Domain\IAM\Models\KhachHang::class, 'id_khach_hang'); 
    } 
    
    public function chiTiet() { 
        return $this->hasMany(ChiTietDonHang::class, 'id_don_hang'); 
    } 
    
    public function diaChi() { 
        return $this->belongsTo(\App\Domain\IAM\Models\DiaChi::class, 'id_dia_chi_giao_hang'); 
    } 
}
