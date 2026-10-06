<?php

namespace App\Domain\PIM\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietSoDo extends Model
{
    protected $table = 'chi_tiet_so_dos';
    protected $fillable = ['so_do_phu_tung_id', 'phu_tung_id', 'ref_number', 'qty', 'x', 'y', 'width', 'height'];

    public function phuTung()
    {
        return $this->belongsTo(PhuTung::class, 'phu_tung_id');
    }
}

