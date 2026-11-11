<template>
  <footer id="footer" class="footer dark-background">
    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <!-- Cột 1: Branding & Thông tin chung -->
          <div class="col-lg-4 col-md-6 footer-about pe-lg-5">
            <router-link to="/" class="logo d-inline-flex align-items-center text-decoration-none">
              <span class="sitename">{{ settings.company_name || 'JPM Marine' }}</span>
            </router-link>
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
              <li class="mb-3 p-0"><router-link to="/ho-tro" class="footer-link">Giới thiệu JPM Marine</router-link></li>
              <li class="mb-3 p-0"><router-link to="/tin-tuc" class="footer-link">Tin tức & Sự kiện</router-link></li>
              <li class="mb-3 p-0"><router-link to="/ho-tro" class="footer-link">Hệ thống đại lý</router-link></li>
              <li class="mb-0 p-0"><router-link to="/ho-tro" class="footer-link">Trung tâm hỗ trợ</router-link></li>
            </ul>
          </div>

          <!-- Cột 3: Danh mục -->
          <div class="col-lg-3 col-md-6 footer-links mb-0">
            <h5 class="footer-heading mb-4">Sản phẩm chính</h5>
            <ul class="list-unstyled">
              <li class="mb-3 p-0"><router-link to="/tra-cuu" class="footer-link">Phụ tùng động cơ ngoài</router-link></li>
              <li class="mb-3 p-0"><router-link to="/tra-cuu" class="footer-link">Phụ tùng động cơ trong</router-link></li>
              <li class="mb-3 p-0"><router-link to="/tra-cuu" class="footer-link">Hệ thống lái & Truyền động</router-link></li>
              <li class="mb-0 p-0"><router-link to="/san-pham" class="footer-link">Dầu nhớt & Bảo dưỡng</router-link></li>
            </ul>
          </div>

          <!-- Cột 4: Hỗ trợ -->
          <div class="col-lg-3 col-md-6 footer-links mb-0">
            <h5 class="footer-heading mb-4">Hỗ trợ khách hàng</h5>
            <ul class="list-unstyled">
              <li class="mb-3 p-0"><router-link to="/tra-cuu-don-hang" class="footer-link">Tra cứu đơn hàng & Báo giá</router-link></li>
              <li class="mb-3 p-0"><router-link to="/ho-tro" class="footer-link">Hướng dẫn tra mã phụ tùng</router-link></li>
              <li class="mb-3 p-0"><router-link to="/ho-tro" class="footer-link">Chính sách bảo hành & Đổi trả</router-link></li>
              <li class="mb-0 p-0"><router-link to="/ho-tro" class="footer-link">Vận chuyển & Giao nhận</router-link></li>
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
    const currentYear = new Date().getFullYear();
    return { settings, currentYear };
  }
};
</script>

<style scoped>
.hover-danger:hover {
  color: var(--accent-color, #f82249) !important;
}
</style>