<template>
  <footer id="footer" class="footer dark-background">
    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <!-- Cột 1: Branding & Thông tin chung -->
          <div class="col-lg-4 col-md-6 footer-about pe-lg-5">
            <router-link to="/" class="logo d-flex align-items-center mb-3 text-decoration-none">
              <span class="sitename fs-2 fw-bolder text-white" style="letter-spacing: -0.5px;">{{ settings.company_name || 'JPM Marine' }}</span>
            </router-link>
            <p class="text-white-50 mb-4" style="font-size: 0.95rem; line-height: 1.6;">
              {{ settings.footer_about_text || 'Nhà phân phối phụ tùng động cơ thủy gắn ngoài hàng đầu tại Việt Nam. Uy tín, chất lượng và đồng hành cùng bạn trên mọi hành trình vươn khơi.' }}
            </p>
            <div class="footer-contact">
              <p class="text-white-50 mb-2 d-flex align-items-start">
                <i class="bi bi-geo-alt text-danger me-3 fs-5 flex-shrink-0 mt-1"></i>
                <span>{{ settings.company_address || '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh' }}</span>
              </p>
              <p class="text-white-50 mb-2 d-flex align-items-center">
                <i class="bi bi-telephone text-danger me-3 fs-5 flex-shrink-0"></i>
                <span><strong>Hotline:</strong> <a :href="'tel:' + (settings.company_hotline || '').replace(/\s+/g, '')" class="text-white text-decoration-none ms-1 hover-danger">{{ settings.company_hotline || '0909 123 456' }}</a></span>
              </p>
              <p class="text-white-50 mb-2 d-flex align-items-center">
                <i class="bi bi-envelope text-danger me-3 fs-5 flex-shrink-0"></i>
                <span><strong>Email:</strong> <a :href="'mailto:' + (settings.company_email || 'contact@jpm-marine.vn')" class="text-white text-decoration-none ms-1 hover-danger">{{ settings.company_email || 'contact@jpm-marine.vn' }}</a></span>
              </p>
              <p v-if="settings.business_hours" class="text-white-50 mb-0 d-flex align-items-center">
                <i class="bi bi-clock text-danger me-3 fs-5 flex-shrink-0"></i>
                <span><strong>Mở cửa:</strong> <span class="ms-1">{{ settings.business_hours }}</span></span>
              </p>
            </div>
          </div>

          <!-- Cột 2: Liên kết -->
          <div class="col-lg-2 col-md-6 footer-links">
            <h5 class="text-white fw-bold mb-4 position-relative pb-2 footer-heading">Về chúng tôi</h5>
            <ul class="list-unstyled">
              <li class="mb-3"><router-link to="/gioi-thieu" class="footer-link">Giới thiệu JPM Marine</router-link></li>
              <li class="mb-3"><router-link to="/tin-tuc" class="footer-link">Tin tức & Sự kiện</router-link></li>
              <li class="mb-3"><router-link to="/he-thong-dai-ly" class="footer-link">Hệ thống đại lý</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro" class="footer-link">Trung tâm hỗ trợ</router-link></li>
            </ul>
          </div>

          <!-- Cột 3: Danh mục -->
          <div class="col-lg-3 col-md-6 footer-links">
            <h5 class="text-white fw-bold mb-4 position-relative pb-2 footer-heading">Sản phẩm chính</h5>
            <ul class="list-unstyled">
              <li class="mb-3"><router-link to="/danh-sach-phu-tung" class="footer-link">Phụ tùng động cơ ngoài</router-link></li>
              <li class="mb-3"><router-link to="/danh-sach-phu-tung" class="footer-link">Phụ tùng động cơ trong</router-link></li>
              <li class="mb-3"><router-link to="/danh-sach-phu-tung" class="footer-link">Hệ thống lái & Truyền động</router-link></li>
              <li class="mb-3"><router-link to="/danh-sach-phu-tung" class="footer-link">Dầu nhớt & Bảo dưỡng</router-link></li>
            </ul>
          </div>

          <!-- Cột 4: Hỗ trợ -->
          <div class="col-lg-3 col-md-6 footer-links">
            <h5 class="text-white fw-bold mb-4 position-relative pb-2 footer-heading">Hỗ trợ khách hàng</h5>
            <ul class="list-unstyled">
              <li class="mb-3"><router-link to="/ho-tro" class="footer-link">Hướng dẫn tra mã phụ tùng</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro" class="footer-link">Chính sách bảo hành</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro" class="footer-link">Chính sách đổi trả</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro" class="footer-link">Vận chuyển & Giao nhận</router-link></li>
            </ul>
          </div>

        </div>
      </div>
    </div>

    <!-- Đáy Footer -->
    <div class="copyright text-center">
      <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
        <div class="d-flex flex-column align-items-center align-items-lg-start mb-3 mb-lg-0">
          <div class="text-white-50" style="font-size: 0.9rem;">
            {{ settings.copyright_text || '© 2026 JPM Marine. Bảo lưu mọi quyền.' }}
          </div>
          <div class="text-white-50 mt-1" style="font-size: 0.85rem;">
            Vận hành và phát triển bởi <a href="#" class="text-danger text-decoration-none fw-semibold hover-white">JPM Marine Team</a>
          </div>
        </div>

        <div class="social-links order-first order-lg-last mb-3 mb-lg-0 d-flex gap-2">
          <a v-if="settings.social_facebook" :href="settings.social_facebook" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a v-if="settings.social_youtube" :href="settings.social_youtube" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="YouTube"><i class="bi bi-youtube"></i></a>
          <a v-if="settings.social_zalo" :href="settings.social_zalo" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="Zalo"><i class="bi bi-chat-dots-fill"></i></a>
          <a v-if="settings.social_instagram" :href="settings.social_instagram" target="_blank" rel="noopener noreferrer" class="social-icon-circle" title="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { useSiteSettings } from '../../composables/useSiteSettings';

export default {
  name: "FooterClient",
  setup() {
    const { settings } = useSiteSettings();
    return { settings };
  }
};
</script>

<style scoped>
.hover-danger:hover {
  color: var(--accent-color, #f82249) !important;
}
</style>