<?php

namespace App\Domain\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Enterprise-grade Image Upload, Validation, Server-Side Compression & Orphan Cleanup Service.
 * - Strict raster MIME allowlist (JPEG, PNG, WebP) — blocks PHP/PHTML/SVG/polyglot uploads.
 * - Automatic server-side resize & WebP compression when PHP GD is available.
 * - Random UUID filenames (never trusts client original filenames/extensions).
 * - Safe orphan file cleanup on update/delete.
 */
class ImageUploadService
{
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Normalize options from either array style or (prefix, maxWidth, maxBytes) convenience style.
     */
    private static function normalizeOptions(string $directory, array|string $optionsOrPrefix, int $maxWidth = 1600, int $maxBytes = 5242880): array
    {
        if (is_array($optionsOrPrefix)) {
            return $optionsOrPrefix;
        }

        $cleanDir = trim($directory, '/');
        return [
            'prefix'         => $optionsOrPrefix,
            'max_width'      => $maxWidth,
            'max_kb'         => (int) ceil($maxBytes / 1024),
            'quality'        => 82,
            'public_uploads' => str_starts_with($cleanDir, 'uploads/'),
        ];
    }

    /**
     * Store a multipart UploadedFile securely with optional server-side resize/compression.
     */
    public static function storeUploadedFile(
        UploadedFile $file,
        string $directory,
        array|string $optionsOrPrefix = [],
        int $maxWidth = 1600
    ): string {
        $options = self::normalizeOptions($directory, $optionsOrPrefix, $maxWidth);
        $maxKb = $options['max_kb'] ?? 5120;

        if ($file->getSize() > $maxKb * 1024) {
            throw ValidationException::withMessages([
                'image' => ['Dung lượng ảnh không được vượt quá ' . round($maxKb / 1024, 1) . 'MB.'],
            ]);
        }

        $rawBytes = @file_get_contents($file->getRealPath());
        if ($rawBytes === false) {
            throw ValidationException::withMessages([
                'image' => ['Không thể đọc dữ liệu file ảnh tải lên.'],
            ]);
        }

        return self::validateAndStoreBytes($rawBytes, $directory, $options);
    }

    /**
     * Store a base64 data URI image securely (replaces unsafe regex + raw file_put_contents).
     */
    public static function storeBase64(
        string $base64,
        string $directory,
        array|string $optionsOrPrefix = [],
        int $maxWidth = 1600,
        int $maxBytes = 5242880
    ): string {
        $options = self::normalizeOptions($directory, $optionsOrPrefix, $maxWidth, $maxBytes);

        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/si', trim($base64), $matches)) {
            throw ValidationException::withMessages([
                'image_base64' => ['Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, WebP.'],
            ]);
        }

        $maxKb = $options['max_kb'] ?? 5120;
        $encoded = $matches[2];
        if ((int) (strlen($encoded) * 0.75) > $maxKb * 1024) {
            throw ValidationException::withMessages([
                'image_base64' => ['Dung lượng ảnh vượt quá giới hạn ' . round($maxKb / 1024, 1) . 'MB.'],
            ]);
        }

        $rawBytes = base64_decode($encoded, true);
        if ($rawBytes === false) {
            throw ValidationException::withMessages([
                'image_base64' => ['Dữ liệu ảnh base64 bị hỏng.'],
            ]);
        }

        return self::validateAndStoreBytes($rawBytes, $directory, $options);
    }

    /**
     * Safely delete an old/orphaned image file from public storage or public/uploads.
     */
    public static function delete(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return;
        }

        if (str_contains($path, '..')) {
            return;
        }

        $clean = ltrim($path, '/');

        if (str_starts_with($clean, 'uploads/')) {
            $fullPath = public_path($clean);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
            return;
        }

        if (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($clean)) {
            Storage::disk('public')->delete($clean);
        }
    }

    /**
     * Validate actual binary image headers, optionally resize/compress via GD, and write using UUID filename.
     */
    private static function validateAndStoreBytes(string $rawBytes, string $directory, array $options): string
    {
        $info = @getimagesizefromstring($rawBytes);
        if ($info === false || empty($info['mime']) || !isset(self::ALLOWED_MIMES[$info['mime']])) {
            throw ValidationException::withMessages([
                'image' => ['Tệp tải lên không phải ảnh hợp lệ (chỉ chấp nhận JPG, PNG, WebP).'],
            ]);
        }

        $mime = $info['mime'];
        $ext = self::ALLOWED_MIMES[$mime];
        $maxWidth = $options['max_width'] ?? 1600;
        $quality = $options['quality'] ?? 82;
        $outputBytes = $rawBytes;

        // Server-side resize & WebP compression when PHP GD is enabled
        if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            $srcImage = @imagecreatefromstring($rawBytes);
            if ($srcImage !== false) {
                $origW = imagesx($srcImage);
                $origH = imagesy($srcImage);
                $targetImage = $srcImage;

                if ($origW > $maxWidth && $maxWidth > 0) {
                    $newW = $maxWidth;
                    $newH = (int) round(($origH / $origW) * $newW);
                    $resized = imagecreatetruecolor($newW, $newH);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                    imagedestroy($srcImage);
                    $targetImage = $resized;
                }

                ob_start();
                if (function_exists('imagewebp')) {
                    imagewebp($targetImage, null, $quality);
                    $compressed = ob_get_clean();
                    if ($compressed !== false && strlen($compressed) > 0) {
                        $outputBytes = $compressed;
                        $ext = 'webp';
                    }
                } elseif ($ext === 'png') {
                    imagepng($targetImage, null, 7);
                    $compressed = ob_get_clean();
                    if ($compressed !== false && strlen($compressed) > 0) {
                        $outputBytes = $compressed;
                    }
                } else {
                    imagejpeg($targetImage, null, $quality);
                    $compressed = ob_get_clean();
                    if ($compressed !== false && strlen($compressed) > 0) {
                        $outputBytes = $compressed;
                    }
                }
                imagedestroy($targetImage);
            }
        }

        $cleanDir = trim($directory, '/');
        $prefix = !empty($options['prefix']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $options['prefix']) . '_' : '';
        $filename = $prefix . Str::uuid()->toString() . '.' . $ext;

        if (!empty($options['public_uploads'])) {
            $subDir = str_starts_with($cleanDir, 'uploads/') ? substr($cleanDir, strlen('uploads/')) : $cleanDir;
            $targetDir = public_path('uploads/' . $subDir);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            file_put_contents($targetDir . '/' . $filename, $outputBytes);
            return '/uploads/' . $subDir . '/' . $filename;
        }

        $relativePath = $cleanDir . '/' . $filename;
        Storage::disk('public')->put($relativePath, $outputBytes);
        return $relativePath;
    }
}
