<template>
  <!-- Footer -->
  <footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
      <div class="mb-2 mb-md-0">
        © {{ new Date().getFullYear() }}
        bản quyền thuộc về
        <router-link to="/portal/dashboard" class="footer-link fw-bolder">JPM Marine</router-link>
      </div>
      <div>
        <router-link to="/" class="footer-link me-4">Trang chủ Client</router-link>
        <router-link to="/portal/dashboard" class="footer-link me-4">Bảng điều khiển</router-link>
        <router-link to="/ho-tro" class="footer-link">Hỗ trợ kỹ thuật</router-link>
      </div>
    </div>
  </footer>
  <!-- / Footer -->
</template>
