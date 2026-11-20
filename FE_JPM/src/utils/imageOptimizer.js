/**
 * JPM Marine - Image Optimizer Utility
 * Zero-cost, 100% Client-side, Canvas-based compression & WebP converter.
 * Reduces 5MB-15MB raw photos down to 25KB-150KB before sending to Backend.
 */

/**
 * Format bytes to human readable format (KB, MB)
 */
export function formatBytes(bytes, decimals = 1) {
  if (!bytes || bytes === 0) return '0 B';
  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

/**
 * Compress a single image file
 * 
 * @param {File} file - Original file from <input type="file">
 * @param {Object} options
 * @param {number} options.maxWidth - Max width (default: 1200)
 * @param {number} options.maxHeight - Max height (default: 1200)
 * @param {number} options.quality - Quality from 0.1 to 1.0 (default: 0.82)
 * @param {boolean} options.cropSquare - Center-crop 1:1 square for avatars (default: false)
 * @param {string} options.format - Target mime type 'image/webp' or 'image/jpeg' (default: 'image/webp')
 * @returns {Promise<{ file: File, previewUrl: string, originalSize: number, compressedSize: number, savedPercent: number, width: number, height: number }>}
 */
export function compressImage(file, options = {}) {
  return new Promise((resolve, reject) => {
    if (!file || !file.type || !file.type.startsWith('image/')) {
      return reject(new Error('File được chọn không phải là hình ảnh hợp lệ.'));
    }

    // Default configuration
    const maxWidth = options.maxWidth || 1200;
    const maxHeight = options.maxHeight || 1200;
    const quality = options.quality !== undefined ? options.quality : 0.82;
    const cropSquare = Boolean(options.cropSquare);
    const targetFormat = options.format || 'image/webp';

    const reader = new FileReader();

    reader.onerror = () => reject(new Error('Không thể đọc file ảnh.'));

    reader.onload = (e) => {
      const img = new Image();

      img.onerror = () => reject(new Error('Trình duyệt không thể xử lý ảnh này.'));

      img.onload = () => {
        try {
          const originalWidth = img.width;
          const originalHeight = img.height;

          let targetWidth = originalWidth;
          let targetHeight = originalHeight;
          let sourceX = 0;
          let sourceY = 0;
          let sourceWidth = originalWidth;
          let sourceHeight = originalHeight;

          if (cropSquare) {
            // Center crop to 1:1 square
            const squareSize = Math.min(originalWidth, originalHeight);
            sourceX = (originalWidth - squareSize) / 2;
            sourceY = (originalHeight - squareSize) / 2;
            sourceWidth = squareSize;
            sourceHeight = squareSize;

            // Scale down to maxWidth/maxHeight if needed
            const finalSize = Math.min(squareSize, maxWidth, maxHeight);
            targetWidth = finalSize;
            targetHeight = finalSize;
          } else {
            // Proportional resize preserving aspect ratio
            if (targetWidth > maxWidth || targetHeight > maxHeight) {
              const widthRatio = maxWidth / targetWidth;
              const heightRatio = maxHeight / targetHeight;
              const bestRatio = Math.min(widthRatio, heightRatio);

              targetWidth = Math.round(targetWidth * bestRatio);
              targetHeight = Math.round(targetHeight * bestRatio);
            }
          }

          // Create canvas
          const canvas = document.createElement('canvas');
          canvas.width = targetWidth;
          canvas.height = targetHeight;
          const ctx = canvas.getContext('2d');

          // High quality image smoothing
          ctx.imageSmoothingEnabled = true;
          ctx.imageSmoothingQuality = 'high';

          // Draw image
          ctx.drawImage(
            img,
            sourceX,
            sourceY,
            sourceWidth,
            sourceHeight,
            0,
            0,
            targetWidth,
            targetHeight
          );

          // Test WebP export support
          canvas.toBlob(
            (blob) => {
              if (!blob) {
                // Fallback to JPEG if WebP fails
                canvas.toBlob(
                  (fallbackBlob) => {
                    if (!fallbackBlob) {
                      return reject(new Error('Lỗi chuyển đổi dữ liệu ảnh.'));
                    }
                    finishExport(fallbackBlob, 'jpeg');
                  },
                  'image/jpeg',
                  quality
                );
                return;
              }
              finishExport(blob, targetFormat === 'image/webp' ? 'webp' : 'jpeg');
            },
            targetFormat,
            quality
          );

          function finishExport(blob, extension) {
            // Build optimized file name
            const originalName = file.name || 'image';
            const baseName = originalName.substring(0, originalName.lastIndexOf('.')) || originalName;
            const newFileName = `${baseName}_opt.${extension}`;

            const optimizedFile = new File([blob], newFileName, {
              type: blob.type,
              lastModified: Date.now(),
            });

            const originalSize = file.size;
            const compressedSize = optimizedFile.size;
            const savedPercent = originalSize > 0 
              ? Math.max(0, Math.round(((originalSize - compressedSize) / originalSize) * 100))
              : 0;

            const previewUrl = URL.createObjectURL(optimizedFile);

            resolve({
              file: optimizedFile,
              previewUrl,
              originalSize,
              compressedSize,
              savedPercent,
              width: targetWidth,
              height: targetHeight,
            });
          }
        } catch (err) {
          reject(err);
        }
      };

      img.src = e.target.result;
    };

    reader.readAsDataURL(file);
  });
}

/**
 * Avatar Preset: Center-crop 1:1, max 300x300, WebP, quality 0.85 (~20KB - 40KB)
 */
export function compressAvatar(file) {
  return compressImage(file, {
    maxWidth: 300,
    maxHeight: 300,
    quality: 0.85,
    cropSquare: true,
    format: 'image/webp',
  });
}

/**
 * Product & Spare Part Preset: Proportional, max 1200x1200px, WebP, quality 0.82 (~80KB - 160KB)
 */
export function compressProductImage(file) {
  return compressImage(file, {
    maxWidth: 1200,
    maxHeight: 1200,
    quality: 0.82,
    cropSquare: false,
    format: 'image/webp',
  });
}

/**
 * Receipt / Bank Transfer Proof Preset: Max 1200x1600px, WebP, quality 0.80 (~60KB - 120KB)
 */
export function compressReceipt(file) {
  return compressImage(file, {
    maxWidth: 1200,
    maxHeight: 1600,
    quality: 0.80,
    cropSquare: false,
    format: 'image/webp',
  });
}

/**
 * Batch compress an array or FileList of images
 */
export async function compressImages(files, options = {}) {
  const fileArray = Array.from(files || []);
  const results = [];
  for (const f of fileArray) {
    if (f.type && f.type.startsWith('image/')) {
      const res = await compressImage(f, options);
      results.push(res);
    } else {
      results.push({ file: f, previewUrl: '', originalSize: f.size, compressedSize: f.size, savedPercent: 0 });
    }
  }
  return results;
}
