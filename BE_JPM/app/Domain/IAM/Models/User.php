<?php

namespace App\Domain\IAM\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\DonHang;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'avatar',
        'last_login_at',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'id_tai_khoan');
    }

    public function nhanVien()
    {
        return $this->hasOne(NhanVien::class, 'id_tai_khoan');
    }

    public function diaChis()
    {
        return $this->hasMany(DiaChi::class, 'id_tai_khoan');
    }

    public function garages()
    {
        return $this->hasMany(GarageMayThuy::class, 'id_tai_khoan');
    }

    public function yeuCauBaoGias()
    {
        return $this->hasMany(YeuCauBaoGia::class, 'user_id');
    }

    public function donHangs()
    {
        return $this->hasManyThrough(DonHang::class, KhachHang::class, 'id_tai_khoan', 'id_khach_hang', 'id', 'id');
    }
}
