const RAW_API_BASE = import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000/api";

export const BACKEND_BASE = RAW_API_BASE.replace(/\/api\/?$/, "");

/**
 * Chuẩn hóa đường dẫn ảnh từ Backend (uploads/, storage/, URL tuyệt đối, blob:, data:)
 */
export function getImageUrl(path, fallback = "") {
  if (!path) return fallback;
  const str = String(path).trim();
  if (!str) return fallback;
  if (
    str.startsWith("http://") ||
    str.startsWith("https://") ||
    str.startsWith("blob:") ||
    str.startsWith("data:") ||
    str.startsWith("/client/")
  ) {
    return str;
  }
  const clean = str.replace(/^\/+/, "");
  if (clean.startsWith("storage/") || clean.startsWith("uploads/")) {
    return `${BACKEND_BASE}/${clean}`;
  }
  return `${BACKEND_BASE}/storage/${clean}`;
}
