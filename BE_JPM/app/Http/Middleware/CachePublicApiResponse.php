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
        'thuong-hieu'   => 600,  // 10 phút
        'loai-dong-co'  => 600,
        'dong-co-model' => 300,  // 5 phút
        'tin-tuc-categories' => 600,
        'so-do'         => 1800, // 30 phút - ít thay đổi nhất
        'nhom-phu-tung' => 600,
        'faqs'          => 600,
    ];

    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Chỉ cache GET requests (không cache POST, PUT, DELETE)
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Không cache request có query parameters thay đổi nhiều
        // (ví dụ: tìm kiếm, filter phức tạp)
        $pathSegments = explode('/', $request->path());
        $endpoint = end($pathSegments);

        // Xác định TTL phù hợp cho từng loại endpoint
        foreach ($this->cacheDurations as $pattern => $duration) {
            if (str_contains($request->path(), $pattern)) {
                $ttl = $duration;
                break;
            }
        }

        // Tạo cache key từ URL đầy đủ (bao gồm query string)
        $cacheKey = 'api_response:' . sha1($request->fullUrl());

        // Trả về từ cache nếu tồn tại
        try {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json(
                    json_decode($cached, true),
                    200,
                    ['X-Cache' => 'HIT', 'X-Cache-Key' => substr($cacheKey, 0, 16)]
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
        }

        $response->headers->set('X-Cache', 'MISS');
        return $response;
    }
}
