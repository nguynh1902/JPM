<template>
  <!-- Mobile / Tablet Sneat Header (hiển thị gọn trên màn hình < 1200px để mở Sidebar & điều hướng nhanh) -->
  <nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme d-xl-none"
    id="layout-navbar"
  >
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" @click.prevent="toggleSidebar" aria-label="Mở menu quản trị">
        <i class="bx bx-menu bx-sm"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center w-100 justify-content-between">
      <router-link to="/portal/dashboard" class="fw-bold text-dark text-decoration-none d-flex align-items-center gap-2">
        <span class="badge bg-label-primary rounded-2 px-2 py-1">JPM</span>
        <span>Portal Quản Trị</span>
      </router-link>

      <div class="d-flex align-items-center gap-2">
        <router-link to="/" class="btn btn-sm btn-outline-secondary" title="Về trang Client">
          <i class="bx bx-store-alt me-1"></i> Client
        </router-link>
        <router-link to="/portal/ho-so" class="btn btn-sm btn-outline-primary" title="Hồ sơ cá nhân">
          <i class="bx bx-user"></i>
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
const toggleSidebar = () => {
  if (window.Helpers && typeof window.Helpers.toggleCollapsed === "function") {
    window.Helpers.toggleCollapsed();
  } else {
    document.documentElement.classList.toggle("layout-menu-expanded");
  }
};
</script>
