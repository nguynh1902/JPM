<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * CachePublicApiResponse Middleware
 *
 * Tự động cache response của các GET request công khai vào Redis.
 * Áp dụng cho các endpoint ít thay đổi như:
 * - Danh sách phụ tùng, thương hiệu, loại động cơ (cache 10 phút)
 * - Tin tức, FAQ (cache 5 phút)
 * - Sơ đồ phụ tùng (cache 30 phút)
 *
 * Giúp giảm ~70% query MySQL cho các endpoint đọc phổ biến.
 */
class CachePublicApiResponse
{
    /**
     * Thời gian cache mặc định theo endpoint pattern (giây).
     */
    protected array $cacheDurations = [
        'thuong-hieu'        => 600,  // 10 phút
        'loai-dong-co'       => 600,
        'dong-co-model'      => 300,  // 5 phút
        'danh-muc-san-pham'  => 600,  // 10 phút
        'cau-hinh'           => 1800, // 30 phút
        'san-pham-ban-chay'  => 300,  // 5 phút
        'tin-tuc-moi-nhat'   => 300,  // 5 phút
        'tin-tuc-categories' => 600,
        'so-do'              => 1800, // 30 phút - ít thay đổi nhất
        'nhom-phu-tung'      => 600,
        'faqs'               => 600,
    ];

    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Chỉ cache GET requests (không cache POST, PUT, DELETE)
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Xác định TTL phù hợp cho từng loại endpoint
        foreach ($this->cacheDurations as $pattern => $duration) {
            if (str_contains($request->path(), $pattern)) {
                $ttl = $duration;
                break;
            }
        }

        // Tạo cache key sạch: Loại bỏ query param _t (cache-buster) và chuẩn hóa query string
        $url = $request->url();
        $query = $request->query();
        unset($query['_t']);
        ksort($query);
        $fullCleanUrl = count($query) > 0 ? $url . '?' . http_build_query($query) : $url;
        $cacheKey = 'api_response:' . sha1($fullCleanUrl);

        // Trả về từ cache nếu tồn tại
        try {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json(
                    json_decode($cached, true),
                    200,
                    [
                        'X-Cache' => 'HIT',
                        'X-Cache-Key' => substr($cacheKey, 0, 16),
                        'Cache-Control' => 'public, max-age=' . $ttl,
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Graceful fallback nếu cache không khả dụng
        }

        // Xử lý request bình thường
        $response = $next($request);

        // Chỉ cache response 200 OK
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
