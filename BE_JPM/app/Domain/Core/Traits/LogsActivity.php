<?php

namespace App\Domain\Core\Traits;

use App\Domain\IAM\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logAction('CREATE', 'Thêm mới ' . self::getModelName($model));
        });

        static::updated(function ($model) {
            // Prevent double logging when model is created and then immediately updated in the same instance
            if ($model->wasRecentlyCreated) {
                return;
            }
            self::logAction('UPDATE', 'Cập nhật ' . self::getModelName($model));
        });

        static::deleted(function ($model) {
            self::logAction('DELETE', 'Xóa ' . self::getModelName($model));
        });
    }

    protected static function getModelName($model)
    {
        $class = class_basename(static::class);
        $name = '';
        
        switch($class) {
            case 'KhachHang':
                $name = 'Khách Hàng (' . ($model->ho_ten ?? $model->id) . ')';
                break;
            case 'NhanVien':
                $name = 'Nhân Viên (' . ($model->ho_ten ?? $model->id) . ')';
                break;
            case 'Role':
                $name = 'Vai Trò (' . ($model->name ?? $model->id) . ')';
                break;
            case 'PhuTung':
                $name = 'Phụ Tùng (' . ($model->ten_phu_tung ?? $model->id) . ')';
                break;
            case 'SanPham':
                $name = 'Sản Phẩm (' . ($model->ten_san_pham ?? $model->id) . ')';
                break;
            case 'ThuongHieu':
                $name = 'Thương Hiệu (' . ($model->name ?? $model->ten_thuong_hieu ?? $model->id) . ')';
                break;
            case 'DongCoModel':
                $name = 'Model Động Cơ (' . ($model->ten_model ?? $model->id) . ')';
                break;
            case 'TonKho':
                $name = 'Tồn Kho Phụ Tùng ID: ' . $model->id_phu_tung;
                break;
            case 'Kho':
                $name = 'Kho (' . ($model->ten_kho ?? $model->id) . ')';
                break;
            case 'DonHang':
                $name = 'Đơn Hàng (' . ($model->ma_don_hang ?? $model->id) . ')';
                break;
            default:
                $name = $class . ' (' . $model->id . ')';
        }
        
        return $name;
    }

    protected static function logAction($action, $description)
    {
        if (Auth::guard('sanctum')->check()) {
            SystemLog::create([
                'user_id' => Auth::guard('sanctum')->id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip()
            ]);
        } elseif (Auth::check()) {
            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip()
            ]);
        }
    }
}
