<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Rate Limiters chuẩn doanh nghiệp (dùng Redis làm backend)
        $this->configureRateLimiting();

        // 2. Query time logging (chỉ bật trong môi trường local để debug)
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 300) { // Log các query chậm hơn 300ms
                    Log::warning('[SLOW QUERY] ' . $query->time . 'ms | ' . $query->sql, [
                        'bindings' => $query->bindings,
                    ]);
                }
            });
        }

        // 3. Register Custom Cached Sanctum Token Model
        // Thay thế Sanctum token lookup từ MySQL -> Redis Cache
        \Laravel\Sanctum\Sanctum::usePersonalAccessTokenModel(
            \App\Domain\IAM\Services\CachedSanctumPersonalAccessToken::class
        );

        // 4. Automatic Catalog Cache Invalidation Observers
        \App\Domain\MarineCatalog\Models\ThuongHieu::observe(\App\Domain\MarineCatalog\Observers\CatalogModelObserver::class);
        \App\Domain\MarineCatalog\Models\DongCoModel::observe(\App\Domain\MarineCatalog\Observers\CatalogModelObserver::class);
        \App\Domain\MarineCatalog\Models\LoaiDongCo::observe(\App\Domain\MarineCatalog\Observers\CatalogModelObserver::class);
        \App\Domain\PIM\Models\NhomPhuTung::observe(\App\Domain\MarineCatalog\Observers\CatalogModelObserver::class);
        \App\Domain\PIM\Models\SoDoPhuTung::observe(\App\Domain\MarineCatalog\Observers\CatalogModelObserver::class);
        \App\Domain\PIM\Models\PhuTung::observe(\App\Domain\MarineCatalog\Observers\CatalogModelObserver::class);
    }

    /**
     * Configure the rate limiters for the application.
     * Uses Redis as backend for shared state across multiple app nodes.
     */
    protected function configureRateLimiting(): void
    {
        // Login Rate Limiter: 5 lần thử/phút theo IP + email
        // Chống Brute Force & Peak Login Spikes
        RateLimiter::for('login', function (Request $request) {
            $key = 'login:' . $request->input('login_id', 'guest') . '|' . $request->ip();
            return Limit::perMinute(5)->by($key)->response(function (Request $req, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quá nhiều lần thử đăng nhập. Vui lòng thử lại sau 60 giây.',
                    'retry_after' => $headers['Retry-After'] ?? 60,
                ], 429, $headers);
            });
        });

        // Register Rate Limiter: 3 tài khoản mới / 10 phút / IP
        // Chống tạo tài khoản hàng loạt (spam registration)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 3)->by($request->ip())->response(function (Request $req, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đăng ký quá nhiều tài khoản. Vui lòng thử lại sau.',
                    'retry_after' => $headers['Retry-After'] ?? 600,
                ], 429, $headers);
            });
        });

        // Global Public API Rate Limiter: 120 req/phút/IP
        // Bảo vệ các endpoint công khai khỏi bot scraping
        RateLimiter::for('public-api', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Authenticated API Rate Limiter: 600 req/phút/user
        // Đủ thoải mái cho người dùng thực, chặn được bot/script
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(600)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });
    }
}
