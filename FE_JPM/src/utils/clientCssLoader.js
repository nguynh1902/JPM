/**
 * Singleton Client CSS Loader
 * Tải đồng bộ & bất đồng bộ an toàn các tài nguyên stylesheet của Client
 * Tránh trùng lặp link tags, chống FOUC (Flash of Unstyled Content) và tối ưu F5.
 */
import bootstrapCss from '../assets/client/vendor/bootstrap/css/bootstrap.min.css?url';
import bootstrapIconsCss from '../assets/client/vendor/bootstrap-icons/bootstrap-icons.css?url';
import glightboxCss from '../assets/client/vendor/glightbox/css/glightbox.min.css?url';
import swiperCss from '../assets/client/vendor/swiper/swiper-bundle.min.css?url';
import mainCss from '../assets/client/css/main.css?url';

const clientCssFiles = [
  { href: bootstrapCss, class: "client-bootstrap-css" },
  { href: bootstrapIconsCss, class: "client-icons-css" },
  { href: glightboxCss, class: "" },
  { href: swiperCss, class: "" },
  { href: mainCss, class: "client-main-css" },
];

export const loadClientCSS = () => {
  if (typeof document === 'undefined') return Promise.resolve();

  // Tìm các stylesheet chưa được gắn vào DOM
  const missingFiles = clientCssFiles.filter(
    (file) => !document.querySelector(`link[href="${file.href}"]`)
  );

  if (missingFiles.length === 0) {
    return Promise.resolve();
  }

  return Promise.all(
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
};

export const unloadClientCSS = () => {
  if (typeof document === 'undefined') return;
  document.querySelectorAll('link[data-layout="client"]').forEach(el => el.remove());
};
