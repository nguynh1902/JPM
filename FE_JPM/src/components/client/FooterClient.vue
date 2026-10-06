<template >
  <footer id="footer" class="footer dark-background">
    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <!-- Cột 1: Branding -->
          <div class="col-lg-4 col-md-6 footer-about pe-lg-5">
            <router-link to="/" class="logo d-flex align-items-center mb-3 text-decoration-none">
              <!-- Chờ sếp gửi ảnh logo sau -->
              <span class="sitename fs-2 fw-bolder text-white" style="letter-spacing: -0.5px;">JPM Marine</span>
            </router-link>
            <p class="text-white-50 mb-4" style="font-size: 0.95rem; line-height: 1.6;">
              Nhà phân phối phụ tùng động cơ thủy gắn ngoài hàng đầu tại Việt Nam. Uy tín, chất lượng và đồng hành cùng bạn trên mọi hành trình vươn khơi.
            </p>
            <div class="footer-contact">
              <p class="text-white-50 mb-2 d-flex align-items-center"><i class="bi bi-geo-alt text-danger me-3 fs-5"></i><span>123 Đường Bạch Đằng, Q. Bình Thạnh<br>TP. Hồ Chí Minh, Việt Nam</span></p>
              <p class="text-white-50 mb-2 d-flex align-items-center"><i class="bi bi-telephone text-danger me-3 fs-5"></i><span><strong>Hotline:</strong> 0909 123 456</span></p>
              <p class="text-white-50 mb-0 d-flex align-items-center"><i class="bi bi-envelope text-danger me-3 fs-5"></i><span><strong>Email:</strong> contact@jpm-marine.vn</span></p>
            </div>
          </div>

          <!-- Cột 2: Liên kết -->
          <div class="col-lg-2 col-md-6 footer-links">
            <h5 class="text-white fw-bold mb-4 position-relative pb-2 footer-heading">Về chúng tôi</h5>
            <ul class="list-unstyled">
              <li class="mb-3"><router-link to="/gioi-thieu" class="footer-link">Giới thiệu JPM Marine</router-link></li>
              <li class="mb-3"><router-link to="/tin-tuc" class="footer-link">Tin tức & Sự kiện</router-link></li>
              <li class="mb-3"><router-link to="/he-thong-dai-ly" class="footer-link">Hệ thống đại lý</router-link></li>
              <li class="mb-3"><router-link to="/lien-he" class="footer-link">Liên hệ trực tuyến</router-link></li>
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
              <li class="mb-3"><router-link to="/ho-tro/huong-dan" class="footer-link">Hướng dẫn tra mã phụ tùng</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro/bao-hanh" class="footer-link">Chính sách bảo hành</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro/doi-tra" class="footer-link">Chính sách đổi trả</router-link></li>
              <li class="mb-3"><router-link to="/ho-tro/van-chuyen" class="footer-link">Vận chuyển & Giao nhận</router-link></li>
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
            © <span class="text-white fw-bold">2026 JPM Marine</span>. Bảo lưu mọi quyền.
          </div>
          <div class="text-white-50 mt-1" style="font-size: 0.85rem;">
            Vận hành và phát triển bởi <a href="#" class="text-danger text-decoration-none fw-semibold hover-white">JPM Marine Team</a>
          </div>
        </div>

        <div class="social-links order-first order-lg-last mb-3 mb-lg-0 d-flex gap-2">
          <a href="#" class="social-icon-circle"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-icon-circle"><i class="bi bi-youtube"></i></a>
          <a href="#" class="social-icon-circle"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-icon-circle"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
    name: "FooterClient"
}
</script>

<style scoped>
/* Scoped overrides if needed, but primary styles go to main.css */
</style>