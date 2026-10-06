<?php
namespace App\Domain\Sales\Models;
use Illuminate\Database\Eloquent\Model;

class ChiTietBaoGia extends Model
{
    protected $guarded = [];
    
    public function yeuCauBaoGia()
    {
        return $this->belongsTo(YeuCauBaoGia::class, 'yeu_cau_bao_gia_id');
    }
}

