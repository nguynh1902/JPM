<?php

namespace App\Domain\CMS\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_value', 
        'min_order_value', 'start_date', 'end_date', 
        'status'
    ];
}

