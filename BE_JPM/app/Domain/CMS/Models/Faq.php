<?php

namespace App\Domain\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\FaqFactory::new();
    }

    protected $guarded = [];
}
