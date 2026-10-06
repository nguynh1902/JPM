<?php

namespace App\Domain\CMS\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'image_url', 'link_url', 'status'];
}

