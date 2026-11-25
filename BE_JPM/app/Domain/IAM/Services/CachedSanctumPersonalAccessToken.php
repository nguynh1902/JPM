<?php

namespace App\Domain\IAM\Services;

use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * CachedSanctumPersonalAccessToken
 *
 * Cache token Sanctum với cơ chế thu hồi tức thì theo từng user (user_token_revoked_at)
 * và kiểm tra trạng thái tài khoản hoạt động (status === 1).
 */
class CachedSanctumPersonalAccessToken extends PersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    const CACHE_TTL_SECONDS = 1800;

    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return parent::findToken($token);
        }

        [$id, $tokenHash] = explode('|', $token, 2);
        $cacheKey = 'sanctum_token:' . $id . ':' . hash('sha256', $tokenHash);

        $accessToken = null;
        $fromCache = false;

        try {
            $cachedToken = Cache::get($cacheKey);
            if ($cachedToken !== null) {
                $accessToken = $cachedToken;
                $fromCache = true;
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        if (!$accessToken) {
            $accessToken = parent::findToken($token);
            if (!$accessToken) {
                return null;
            }
        }

        // Kiểm tra thu hồi tức thì theo user (khi reset mật khẩu / khóa / xóa tài khoản)
        try {
            $revokedAt = Cache::get('user_token_revoked_at:' . $accessToken->tokenable_id);
            $tokenCreatedTs = $accessToken->created_at ? $accessToken->created_at->timestamp : 0;
            if ($revokedAt !== null && $tokenCreatedTs <= (int) $revokedAt) {
                Cache::forget($cacheKey);
                return null;
            }
        } catch (\Throwable $e) {
            // Ignore cache read error
        }

        // Từ chối ngay nếu tài khoản đã bị khóa (status = 0)
        $tokenable = $accessToken->tokenable;
        if ($tokenable && isset($tokenable->status) && (int) $tokenable->status === 0) {
            Cache::forget($cacheKey);
            return null;
        }

        if (!$fromCache) {
            try {
                Cache::put($cacheKey, $accessToken, self::CACHE_TTL_SECONDS);
            } catch (\Throwable $e) {
                // Ignore cache write error
            }
        }

        return $accessToken;
    }

    public static function invalidateCache(string $plainTextToken): void
    {
        if (strpos($plainTextToken, '|') === false) {
            return;
        }

        [$id, $tokenHash] = explode('|', $plainTextToken, 2);
        $cacheKey = 'sanctum_token:' . $id . ':' . hash('sha256', $tokenHash);

        try {
            Cache::forget($cacheKey);
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Thu hồi tức thì toàn bộ token đang cache của 1 user (khóa tài khoản, xóa user, reset password).
     */
    public static function invalidateUserTokens(int $userId): void
    {
        try {
            Cache::put('user_token_revoked_at:' . $userId, time(), self::CACHE_TTL_SECONDS + 60);
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }
}
