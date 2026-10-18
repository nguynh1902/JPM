<?php

namespace App\Domain\MarineCatalog\Services;

use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\MarineCatalog\Models\LoaiDongCo;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\PIM\Models\NhomPhuTung;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CatalogCacheService
{
    /**
     * Danh sách tiền tố cache keys của Catalog
     */
    protected const CACHE_PREFIXES = [
        'public_thuong_hieu',
        'public_loai_dong_co_',
        'public_dong_co_model_',
        'public_nam_san_xuat_',
        'public_nhom_phu_tung_',
        'public_sodo_',
    ];

    /**
     * Xóa sạch toàn bộ cache của Catalog và PIM
     */
    public function clearAllCatalogCache(): bool
    {
        try {
            // 1. Thử xóa qua Redis nếu Redis driver đang hoạt động
            if (config('cache.default') === 'redis' || config('cache.default') === 'failover') {
                try {
                    $redis = Redis::connection('cache');
                    $prefix = config('cache.prefix', '');
                    foreach (self::CACHE_PREFIXES as $p) {
                        $keys = $redis->keys($prefix . $p . '*');
                        if (!empty($keys)) {
                            $redis->del($keys);
                        }
                    }
                } catch (\Exception $e) {
                    // Fallback an toàn nếu Redis không chạy
                }
            }

            // 2. Xóa các key cố định trực tiếp qua Cache facade
            Cache::forget('public_thuong_hieu');
            Cache::forget('public_loai_dong_co_all');
            Cache::forget('public_dong_co_model_all_all_all');
            Cache::forget('public_nam_san_xuat_all_all');
            Cache::forget('public_nhom_phu_tung_all_all_all_all');

            // 3. Xóa application cache
            Cache::flush();

            Log::info("[CATALOG CACHE] Successfully cleared all catalog cache.");
            return true;
        } catch (\Exception $e) {
            Log::error("[CATALOG CACHE] Error clearing cache: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Làm ấm (Warm-up) lại Cache sau khi xóa để tối ưu tốc độ người dùng
     */
    public function warmUpCatalogCache(): array
    {
        $this->clearAllCatalogCache();

        $stats = [];

        // 1. Cache Thương hiệu
        $brands = Cache::remember('public_thuong_hieu', 86400, function () {
            return ThuongHieu::where('status', 1)->orderBy('created_at', 'asc')->get();
        });
        $stats['thuong_hieu'] = $brands->count();

        // 2. Cache Loại động cơ
        $types = Cache::remember('public_loai_dong_co_all', 86400, function () {
            return LoaiDongCo::where('trang_thai', 1)->get();
        });
        $stats['loai_dong_co'] = $types->count();

        // 3. Cache Model động cơ
        $models = Cache::remember('public_dong_co_model_all_all_all', 86400, function () {
            return DongCoModel::where('trang_thai', 1)->get();
        });
        $stats['dong_co_model'] = $models->count();

        // 4. Cache Nhóm phụ tùng
        $groups = Cache::remember('public_nhom_phu_tung_all_all_all_all', 86400, function () {
            return NhomPhuTung::where('trang_thai', 1)->get();
        });
        $stats['nhom_phu_tung'] = $groups->count();

        Log::info("[CATALOG CACHE] Warm-up completed.", $stats);

        return [
            'success' => true,
            'message' => 'Làm ấm Cache Catalog thành công',
            'stats' => $stats,
        ];
    }
}
