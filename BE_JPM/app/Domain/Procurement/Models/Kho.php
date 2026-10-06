<?php namespace App\Domain\Procurement\Models; use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; class Kho extends Model {
    use \App\Domain\Core\Traits\LogsActivity;
 protected $guarded = []; }

