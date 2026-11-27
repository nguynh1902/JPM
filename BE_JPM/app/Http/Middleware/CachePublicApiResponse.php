<?php

namespace App\Http\Middleware;

use App\Domain\MarineCatalog\Services\CatalogCacheService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * CachePublicApiResponse Middleware
 *
 * Tự động cache response của các GET request công khai theo phiên bản cache (O(1) invalidation)
 * và lọc query param hợp lệ để chống cache-key explosion.
 */
class CachePublicApiResponse
{
    protected array $cacheDurations = [
        'thuong-hieu'        => 600,
        'loai-dong-co'       => 600,
        'dong-co-model'      => 300,
        'danh-muc-san-pham'  => 600,
        'cau-hinh'           => 1800,
        'san-pham-ban-chay'  => 300,
        'tin-tuc-moi-nhat'   => 300,
        'tin-tuc-categories' => 600,
        'so-do'              => 1800,
        'nhom-phu-tung'      => 600,
        'faqs'               => 600,
    ];

    protected array $allowedQueryParams = [
        'page', 'per_page', 'limit', 'sort', 'sort_by', 'keyword', 'q', 'search',
        'category', 'danh_muc', 'danh_muc_id', 'thuong_hieu_id', 'loai_dong_co_id',
        'dong_co_model_id', 'nam_sx', 'loai_nhip', 'nhom_phu_tung_id', 'min_price',
        'max_price', 'brand_id', 'type', 'all', 'code',
    ];

    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Không bao giờ cache endpoint động/PII (phòng thủ lớp 2)
        if (str_contains($request->path(), 'yeu-cau-bao-gia') || str_contains($request->path(), 'vietqr')) {
            return $next($request);
        }

        foreach ($this->cacheDurations as $pattern => $duration) {
            if (str_contains($request->path(), $pattern)) {
                $ttl = $duration;
                break;
            }
        }

        // Chỉ giữ lại các query params được phép để chống tấn công phình cache bằng random query strings
        $rawQuery = $request->query();
        $cleanQuery = array_intersect_key($rawQuery, array_flip($this->allowedQueryParams));
        ksort($cleanQuery);

        $version = CatalogCacheService::getVersion();
        $url = $request->url();
        $fullCleanUrl = count($cleanQuery) > 0 ? $url . '?' . http_build_query($cleanQuery) : $url;
        $cacheKey = 'api_response:v' . $version . ':' . sha1($fullCleanUrl);

        try {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json(
                    json_decode($cached, true),
                    200,
                    [
                        'X-Cache' => 'HIT',
                        'X-Cache-Key' => substr($cacheKey, 0, 20),
                        'Cache-Control' => 'public, max-age=' . $ttl,
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        $response = $next($request);

        if ($response->getStatusCode() === 200) {
            try {
                Cache::put($cacheKey, $response->getContent(), $ttl);
            } catch (\Throwable $e) {
                // Bỏ qua lỗi ghi cache
            }
            $response->headers->set('Cache-Control', 'public, max-age=' . $ttl);
        }

        $response->headers->set('X-Cache', 'MISS');
        return $response;
    }
}
