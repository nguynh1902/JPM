<template>
  <section class="jpm-not-found d-flex align-items-center justify-content-center py-5">
    <div class="container text-center py-5">
      <div class="nf-code fw-bold mb-2">404</div>
      <h1 class="h3 fw-bold mb-3" style="color: #0e1b4d;">Không tìm thấy trang bạn yêu cầu</h1>
      <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
        Đường dẫn có thể đã thay đổi, bị xóa hoặc mã phụ tùng không còn tồn tại trong hệ thống JPM Marine.
      </p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <router-link to="/" class="btn btn-danger px-4">
          <i class="bi bi-house-door me-1"></i> Về Trang Chủ
        </router-link>
        <router-link to="/tra-cuu" class="btn btn-outline-secondary px-4">
          <i class="bi bi-search me-1"></i> Tra Cứu Phụ Tùng
        </router-link>
      </div>
    </div>
  </section>
</template>

<style scoped>
.jpm-not-found {
  min-height: 62vh;
  background: #f8fafc;
}
.nf-code {
  font-size: clamp(4rem, 10vw, 6.5rem);
  line-height: 1;
  letter-spacing: -0.04em;
  color: #f82249;
}
</style>
