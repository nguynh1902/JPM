<template>
  <section class="jpm-not-found d-flex align-items-center justify-content-center py-5">
    <div class="container text-center py-5">
      <div class="nf-code fw-bold mb-2">404</div>
      <h1 class="nf-title mb-3">Không tìm thấy trang bạn yêu cầu</h1>
      <p class="nf-desc mx-auto mb-4">
        Đường dẫn có thể đã thay đổi, bị xóa hoặc mã phụ tùng không còn tồn tại trong hệ thống JPM Marine.
      </p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <router-link to="/" class="btn btn-danger lux-btn">
          <i class="bi bi-house-door"></i>
          <span>Về trang chủ</span>
        </router-link>
        <router-link to="/tra-cuu" class="btn btn-outline-secondary lux-btn">
          <i class="bi bi-search"></i>
          <span>Tra cứu phụ tùng</span>
        </router-link>
      </div>
    </div>
  </section>
</template>

<style scoped>
.jpm-not-found {
  min-height: 62vh;
  background: #f8fafc;
  font-family: var(--default-font, var(--jpm-font-family));
}

.nf-code {
  font-family: 'Roboto Mono', monospace;
  font-size: clamp(4rem, 10vw, 6.25rem);
  line-height: 1;
  letter-spacing: -0.04em;
  color: var(--jpm-color-red-marine, #F82249);
}

.nf-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: -0.015em;
}

.nf-desc {
  max-width: 460px;
  font-size: 0.92rem;
  color: #64748b;
  line-height: 1.6;
}
</style>
