/**
 * Singleton Client CSS Loader
 * Tải đồng bộ & bất đồng bộ an toàn các tài nguyên stylesheet của Client
 * Tránh trùng lặp link tags, chống FOUC (Flash of Unstyled Content), hỗ trợ Vite HMR tức thì cho main.css và tối ưu F5.
 */
import bootstrapCss from '../assets/client/vendor/bootstrap/css/bootstrap.min.css?url';
import bootstrapIconsCss from '../assets/client/vendor/bootstrap-icons/bootstrap-icons.css?url';
import glightboxCss from '../assets/client/vendor/glightbox/css/glightbox.min.css?url';
import swiperCss from '../assets/client/vendor/swiper/swiper-bundle.min.css?url';
import aosCss from 'aos/dist/aos.css?url';
import mainCssContent from '../assets/client/css/main.css?inline';

const clientVendorCssFiles = [
  { href: bootstrapCss, class: "client-bootstrap-css" },
  { href: bootstrapIconsCss, class: "client-icons-css" },
  { href: glightboxCss, class: "" },
  { href: swiperCss, class: "" },
  { href: aosCss, class: "" },
];

let currentMainCss = mainCssContent;

const syncMainCssStyle = (cssText) => {
  if (typeof document === 'undefined') return;
  // Gỡ bỏ thẻ link main.css cũ (nếu có từ phiên HMR trước)
  document.querySelectorAll('link.client-main-css').forEach((el) => el.remove());

  let styleEl = document.getElementById('client-main-css');
  if (!styleEl) {
    styleEl = document.createElement('style');
    styleEl.id = 'client-main-css';
    styleEl.classList.add('client-main-css');
    styleEl.setAttribute('data-layout', 'client');
  }
  styleEl.textContent = cssText;
  // Luôn đặt main.css ở cuối <head> để đảm bảo thứ tự ưu tiên đè lên Bootstrap vendor CSS
  document.head.appendChild(styleEl);
};

export const loadClientCSS = () => {
  if (typeof document === 'undefined') return Promise.resolve();

  // 1. Nạp các vendor CSS (Bootstrap, Icons, GLightbox, Swiper) nếu chưa có
  const missingFiles = clientVendorCssFiles.filter(
    (file) => !document.querySelector(`link[href="${file.href}"]`)
  );

  const vendorPromise = missingFiles.length === 0
    ? Promise.resolve()
    : Promise.all(
        missingFiles.map((file) => {
          return new Promise((resolve) => {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = file.href;
            if (file.class) link.classList.add(file.class);
            link.setAttribute("data-layout", "client");

            let settled = false;
            const onFinish = () => {
              if (!settled) {
                settled = true;
                resolve();
              }
            };

            link.onload = onFinish;
            link.onerror = onFinish;
            // Fallback an toàn 150ms: Tránh treo Promise nếu trình duyệt không bắn event onload
            setTimeout(onFinish, 150);

            document.head.appendChild(link);
          });
        })
      );

  // 2. Luôn đảm bảo main.css nằm sau cùng trong <head> (không phụ thuộc cache mạng, 0 FOUC)
  syncMainCssStyle(currentMainCss);

  return vendorPromise;
};

export const unloadClientCSS = () => {
  if (typeof document === 'undefined') return;
  document.querySelectorAll('link[data-layout="client"], style[data-layout="client"]').forEach((el) => el.remove());
};

// Hỗ trợ Vite HMR cập nhật main.css tức thì trong trình duyệt không cần F5
if (import.meta.hot) {
  if (typeof document !== 'undefined' && (document.querySelector('link.client-main-css') || document.getElementById('client-main-css'))) {
    syncMainCssStyle(currentMainCss);
  }
  import.meta.hot.accept('../assets/client/css/main.css?inline', (mod) => {
    if (mod && typeof mod.default === 'string') {
      currentMainCss = mod.default;
      if (typeof document !== 'undefined' && document.getElementById('client-main-css')) {
        syncMainCssStyle(currentMainCss);
      }
    }
  });
}
