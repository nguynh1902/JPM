<template>
  <!-- Footer Staff -->
  <footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
      <div class="mb-2 mb-md-0">
        © {{ currentYear }} 
        bản quyền thuộc về
        <a href="javascript:void(0)" class="footer-link fw-bolder">JPM Marine - Phụ Tùng Máy Thủy</a>
      </div>
      <div>
        <span class="badge bg-label-primary me-3">Khu vực làm việc Nhân viên</span>
        <a href="javascript:void(0)" class="footer-link me-3">Quy trình nội bộ</a>
        <a href="javascript:void(0)" class="footer-link">Hỗ trợ kỹ thuật</a>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "FooterStaff",
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
};
</script>
