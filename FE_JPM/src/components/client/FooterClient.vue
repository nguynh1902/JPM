<template>
  <div class="footer-wrapper">
    <!-- Banner Tàu Thuyền Full Màn Hình Dính Liền Footer (Dùng chung toàn Client) -->
    <section class="home-full-banner prefooter-marine-banner">
      <div class="container prefooter-marine-container">
        <div class="prefooter-marine-content">
          <span class="prefooter-eyebrow">Tư vấn kỹ thuật OEM</span>
          <h2 class="home-banner-title mb-2">
            Chưa tìm đúng <span class="text-danger">mã phụ tùng</span><br class="d-none d-sm-inline" /> cho đời máy của bạn?
          </h2>
          <p class="home-banner-desc mb-3">
            Gửi số máy (Serial), model động cơ hoặc ảnh linh kiện — kỹ thuật viên JPM đối chiếu sơ đồ rã máy và phản hồi báo giá minh bạch trong vòng 24 giờ.
          </p>

          <div class="prefooter-actions d-flex flex-wrap align-items-center gap-2">
            <a
              :href="'tel:' + (settings.company_hotline || '0909123456').replace(/\s+/g, '')"
              class="btn prefooter-btn prefooter-btn-navy-outline d-inline-flex align-items-center justify-content-center gap-2 text-nowrap"
            >
              <i class="bi bi-telephone"></i>
              <span>{{ settings.company_hotline || '0909 123 456' }}</span>
            </a>

            <a
              v-if="settings.social_zalo"
              :href="settings.social_zalo"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-danger prefooter-btn d-inline-flex align-items-center justify-content-center gap-2 text-nowrap"
            >
              <span>Tư vấn qua Zalo</span>
              <i class="bi bi-arrow-right"></i>
            </a>
            <router-link
              v-else
              to="/ho-tro"
              class="btn btn-danger prefooter-btn d-inline-flex align-items-center justify-content-center gap-2 text-nowrap"
            >
              <span>Liên hệ kỹ thuật</span>
              <i class="bi bi-arrow-right"></i>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <footer id="footer" class="footer dark-background">
    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <!-- Cột 1: Branding & Thông tin chung -->
          <div class="col-lg-4 col-md-6 footer-about pe-lg-5">
            <div class="footer-brand mb-3">
              <router-link to="/" class="footer-brand-title text-decoration-none">
                {{ settings.company_name || 'JPM Marine' }}
              </router-link>
            </div>
            <p class="footer-about-desc mb-4">
              {{ settings.footer_about_text || 'Nhà phân phối phụ tùng động cơ thủy gắn ngoài hàng đầu tại Việt Nam. Uy tín, chất lượng và đồng hành cùng bạn trên mọi hành trình vươn khơi.' }}
            </p>
            <div class="footer-contact">
              <div class="footer-contact-item">
                <i class="bi bi-geo-alt footer-contact-icon"></i>
                <span class="footer-contact-value">{{ settings.company_address || '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh' }}</span>
              </div>
              <div class="footer-contact-item">
                <i class="bi bi-telephone footer-contact-icon"></i>
                <div>
                  <span class="footer-contact-label">Hotline:</span>
                  <a :href="'tel:' + (settings.company_hotline || '').replace(/\s+/g, '')" class="footer-contact-value">{{ settings.company_hotline || '0909 123 456' }}</a>
                </div>
              </div>
              <div class="footer-contact-item">
                <i class="bi bi-envelope footer-contact-icon"></i>
                <div>
                  <span class="footer-contact-label">Email:</span>
                  <a :href="'mailto:' + (settings.company_email || 'contact@jpm-marine.vn')" class="footer-contact-value">{{ settings.company_email || 'contact@jpm-marine.vn' }}</a>
                </div>
              </div>
              <div v-if="settings.business_hours" class="footer-contact-item mb-0">
                <i class="bi bi-clock footer-contact-icon"></i>
                <div>
                  <span class="footer-contact-label">Mở cửa:</span>
                  <span class="footer-contact-value">{{ settings.business_hours }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Cột 2: Liên kết -->
          <div class="col-lg-2 col-md-6 footer-links mb-0">
            <h5 class="footer-heading mb-4">Về chúng tôi</h5>
            <ul class="list-unstyled">
              <li class="mb-3 p-0">
                <router-link :to="{ path: '/', hash: '#about' }" @click="scrollToHash('#about')" class="footer-link">Giới thiệu JPM Marine</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link to="/tin-tuc" class="footer-link">Tin tức & Sự kiện</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link :to="{ path: '/ho-tro', hash: '#map-section' }" @click="scrollToHash('#map-section')" class="footer-link">Hệ thống đại lý</router-link>
              </li>
              <li class="mb-0 p-0">
                <router-link :to="{ path: '/ho-tro', hash: '#contact' }" @click="scrollToHash('#contact')" class="footer-link">Trung tâm hỗ trợ</router-link>
              </li>
            </ul>
          </div>

          <!-- Cột 3: Danh mục -->
          <div class="col-lg-3 col-md-6 footer-links mb-0">
            <h5 class="footer-heading mb-4">Sản phẩm chính</h5>
            <ul class="list-unstyled">
              <li class="mb-3 p-0">
                <router-link to="/thuong-hieu/yam" class="footer-link">Phụ tùng chính hãng Yamaha</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link to="/thuong-hieu/hon" class="footer-link">Phụ tùng chính hãng Honda</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link to="/thuong-hieu/suz" class="footer-link">Phụ tùng chính hãng Suzuki</router-link>
              </li>
              <li class="mb-0 p-0">
                <router-link to="/san-pham" class="footer-link">Dầu nhớt & Vật tư bảo dưỡng</router-link>
              </li>
            </ul>
          </div>

          <!-- Cột 4: Hỗ trợ -->
          <div class="col-lg-3 col-md-6 footer-links mb-0">
            <h5 class="footer-heading mb-4">Hỗ trợ khách hàng</h5>
            <ul class="list-unstyled">
              <li class="mb-3 p-0">
                <router-link :to="{ path: '/ho-tro', query: { faq: 'mua-hang' }, hash: '#faq-section' }" @click="scrollToHash('#faq-section')" class="footer-link">Hướng dẫn mua hàng & Thanh toán</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link to="/tra-cuu" class="footer-link">Hướng dẫn tra mã phụ tùng</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link :to="{ path: '/ho-tro', query: { faq: 'bao-hanh' }, hash: '#faq-section' }" @click="scrollToHash('#faq-section')" class="footer-link">Chính sách bảo hành & Đổi trả</router-link>
              </li>
              <li class="mb-3 p-0">
                <router-link :to="{ path: '/ho-tro', query: { faq: 'van-chuyen' }, hash: '#faq-section' }" @click="scrollToHash('#faq-section')" class="footer-link">Vận chuyển & Giao nhận</router-link>
              </li>
              <li class="mb-0 p-0">
                <router-link to="/dieu-khoan-chinh-sach" class="footer-link">Điều khoản & Chính sách</router-link>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </div>

    <!-- Đáy Footer -->
    <div class="copyright text-center">
      <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
        <div class="d-flex flex-column align-items-center align-items-lg-start mb-3 mb-lg-0">
          <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.55);">
            © {{ currentYear }} {{ settings.company_name || 'JPM Marine' }}. Bảo lưu mọi quyền.
          </div>
          <div class="mt-1" style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.45);">
            Vận hành và phát triển bởi <span class="text-danger fw-semibold">JPM Marine Team</span>
          </div>
        </div>

        <div class="social-links order-first order-lg-last mb-3 mb-lg-0 d-flex align-items-center gap-2">
          <a v-if="settings.social_facebook" :href="settings.social_facebook" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="13" height="15" fill="currentColor">
              <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 155.9v45.6H32v97.8H80z"/>
            </svg>
          </a>
          <a v-if="settings.social_youtube" :href="settings.social_youtube" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="YouTube"><i class="bi bi-youtube"></i></a>
          <a v-if="settings.social_zalo" :href="settings.social_zalo" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="Zalo">
            <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor">
              <path d="M12.49 10.2722v-.4496h1.3467v6.3218h-.7704a.576.576 0 01-.5763-.5729l-.0006.0005a3.273 3.273 0 01-1.9372.6321c-1.8138 0-3.2844-1.4697-3.2844-3.2823 0-1.8125 1.4706-3.2822 3.2844-3.2822a3.273 3.273 0 011.9372.6321l.0006.0005zM6.9188 7.7896v.205c0 .3823-.051.6944-.2995 1.0605l-.03.0343c-.0542.0615-.1815.206-.2421.2843L2.024 14.8h4.8948v.7682a.5764.5764 0 01-.5767.5761H0v-.3622c0-.4436.1102-.6414.2495-.8476L4.8582 9.23H.1922V7.7896h6.7266zm8.5513 8.3548a.4805.4805 0 01-.4803-.4798v-7.875h1.4416v8.3548H15.47zM20.6934 9.6C22.52 9.6 24 11.0807 24 12.9044c0 1.8252-1.4801 3.306-3.3066 3.306-1.8264 0-3.3066-1.4808-3.3066-3.306 0-1.8237 1.4802-3.3044 3.3066-3.3044zm-10.1412 5.253c1.0675 0 1.9324-.8645 1.9324-1.9312 0-1.065-.865-1.9295-1.9324-1.9295s-1.9324.8644-1.9324 1.9295c0 1.0667.865 1.9312 1.9324 1.9312zm10.1412-.0033c1.0737 0 1.945-.8707 1.945-1.9453 0-1.073-.8713-1.9436-1.945-1.9436-1.0753 0-1.945.8706-1.945 1.9453 0 1.0746.8697 1.9453 1.945 1.9453z"/>
            </svg>
          </a>
          <a v-if="settings.social_instagram" :href="settings.social_instagram" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="Instagram"><i class="bi bi-instagram"></i></a>
          <a v-if="settings.social_tiktok" :href="settings.social_tiktok" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="TikTok"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>
    </div>
  </footer>
  </div>
</template>

<script>
import { useRoute } from 'vue-router';
import { useSiteSettings } from '../../composables/useSiteSettings';

export default {
  name: "FooterClient",
  setup() {
    const route = useRoute();
    const { settings } = useSiteSettings();
    const currentYear = new Date().getFullYear();

    const scrollToHash = (hash) => {
      if (hash) {
        setTimeout(() => {
          const el = document.querySelector(hash);
          if (el) {
            const offset = 85;
            const y = el.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top: y, behavior: 'smooth' });
          }
        }, 150);
      }
    };

    return { route, settings, currentYear, scrollToHash };
  }
};
</script>

<style scoped>
.hover-danger:hover {
  color: var(--accent-color, #f82249) !important;
}

/* Banner Tàu Thuyền Full-Width Dính Liền Footer (Dùng chung toàn Client) */
.prefooter-marine-banner {
  background-color: #ffffff;
  background-position: center bottom;
  margin-bottom: 0;
  border-bottom: none;
  position: relative;
}

.prefooter-marine-banner::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 28px;
  background: linear-gradient(to bottom, rgba(0, 8, 32, 0) 0%, #000820 100%);
  pointer-events: none;
  z-index: 2;
}

.prefooter-marine-container {
  padding-top: clamp(22px, 3vw, 44px);
  padding-bottom: 0;
  position: relative;
  z-index: 3;
}

.prefooter-marine-content {
  max-width: 540px;
}

.prefooter-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 1.8px;
  text-transform: uppercase;
  color: var(--jpm-color-red-marine, #F82249);
  margin-bottom: 8px;
}

.prefooter-eyebrow::before {
  content: "";
  display: inline-block;
  width: 16px;
  height: 1.5px;
  background-color: var(--jpm-color-red-marine, #F82249);
}

.prefooter-btn {
  height: var(--jpm-control-height, 37px);
  padding: 0 18px !important;
  font-size: 0.92rem;
  font-weight: 600;
  border-radius: 50px !important;
  box-shadow: none !important;
}

.prefooter-btn-navy-outline {
  background-color: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  color: var(--jpm-color-navy-dark, #0A1128) !important;
}

.prefooter-btn-navy-outline:hover {
  background-color: var(--jpm-color-navy-dark, #0A1128) !important;
  border-color: var(--jpm-color-navy-dark, #0A1128) !important;
  color: #ffffff !important;
}

@media (max-width: 991.98px) {
  .prefooter-marine-container {
    padding-top: 0;
  }
  .prefooter-marine-content {
    max-width: 100%;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(10, 17, 40, 0.08);
  }
}

@media (max-width: 767.98px) {
  .prefooter-actions {
    width: 100%;
  }
  .prefooter-btn {
    flex: 1 1 auto;
  }
}

/* Footer Brand Typography - Editorial Luxury */
.footer-brand {
  margin-bottom: 1.25rem;
}

.footer-brand-title {
  font-family: var(--heading-font, var(--jpm-font-heading, 'Raleway', 'Montserrat', 'Roboto', sans-serif));
  font-size: 1.625rem; /* ~26px - Rõ ràng, uy lực, chuẩn nhận diện thương hiệu cao cấp */
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.5px;
  line-height: 1.25;
  display: inline-block;
  text-decoration: none;
  transition: color 0.25s ease;
}

.footer-brand-title:hover {
  color: var(--accent-color, #f82249);
}

@media (max-width: 767.98px) {
  .footer-brand-title {
    font-size: 1.35rem;
  }
}
</style>