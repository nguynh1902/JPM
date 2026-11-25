<?php

namespace App\Domain\MarineCatalog\Services;

use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\MarineCatalog\Models\LoaiDongCo;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\PIM\Models\NhomPhuTung;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CatalogCacheService
{
    public const VERSION_KEY = 'catalog_cache_version';

    /**
     * Lấy phiên bản cache hiện tại cho public response & catalog keys (O(1), không bao giờ dùng KEYS * hay Cache::flush)
     */
    public static function getVersion(): int
    {
        try {
            $ver = Cache::get(self::VERSION_KEY);
            if ($ver === null) {
                Cache::forever(self::VERSION_KEY, 1);
                return 1;
            }
            return (int) $ver;
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /**
     * Tăng phiên bản cache để vô hiệu hóa toàn bộ Public API Cache tức thì mà không phá session/rate-limiter/Sanctum tokens
     */
    public static function bumpVersion(): int
    {
        try {
            $next = self::getVersion() + 1;
            Cache::forever(self::VERSION_KEY, $next);
            return $next;
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /**
     * Xóa sạch toàn bộ cache của Catalog và PIM một cách an toàn (Zero Cache::flush / Zero Redis KEYS *)
     */
    public function clearAllCatalogCache(): bool
    {
        try {
            $newVersion = self::bumpVersion();

            // Xóa trực tiếp các fixed catalog keys
            Cache::forget('public_thuong_hieu');
            Cache::forget('public_loai_dong_co_all');
            Cache::forget('public_dong_co_model_all_all_all');
            Cache::forget('public_nam_san_xuat_all_all');
            Cache::forget('public_nhom_phu_tung_all_all_all_all');
            Cache::forget('public_site_settings');

            Log::info("[CATALOG CACHE] Bumped catalog cache version to v{$newVersion} (safe invalidation).");
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

        $brands = Cache::remember('public_thuong_hieu', 86400, function () {
            return ThuongHieu::where('status', 1)->orderBy('created_at', 'asc')->get();
        });
        $stats['thuong_hieu'] = $brands->count();

        $types = Cache::remember('public_loai_dong_co_all', 86400, function () {
            return LoaiDongCo::where('trang_thai', 1)->get();
        });
        $stats['loai_dong_co'] = $types->count();

        $models = Cache::remember('public_dong_co_model_all_all_all', 86400, function () {
            return DongCoModel::where('trang_thai', 1)->get();
        });
        $stats['dong_co_model'] = $models->count();

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
