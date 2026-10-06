<?php
namespace App\Domain\Sales\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class YeuCauBaoGia extends Model
{
    protected $guarded = [];
    
    public function chiTiet()
    {
        return $this->hasMany(ChiTietBaoGia::class, 'yeu_cau_bao_gia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
