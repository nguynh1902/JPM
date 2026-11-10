<template>
  <main class="account-portal-layout flex-grow-1" style="background-color: #f8fafc; min-height: 100vh; padding-top: 110px; padding-bottom: 60px;">
    <section>
      <div class="container">
        
        <!-- PAGE TITLE (Title Case chuẩn Raleway cao cấp) -->
        <div class="account-header-wrap mb-4 pb-2" data-aos="fade-down">
          <h1 class="account-page-title mb-2">
            {{ titleParts.main }} <span style="color: var(--jpm-color-red-marine, #F82249);">{{ titleParts.accent }}</span>
          </h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
              <li class="breadcrumb-item"><router-link to="/" class="text-secondary text-decoration-none">Trang chủ</router-link></li>
              <li class="breadcrumb-item"><router-link to="/tai-khoan" class="text-secondary text-decoration-none">Tài khoản</router-link></li>
              <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ titleParts.full }}</li>
            </ol>
          </nav>
        </div>

        <div class="row g-4">
          <!-- SIDEBAR -->
          <AccountSidebar :show-camera="isProfilePage" :custom-name="profileData?.name" :preview-avatar="avatarPreview" @change-avatar="triggerFileInput" />

          <!-- MAIN CONTENT -->
          <div class="col-lg-9 d-flex flex-column">
            <!-- Tự động inject component con (TaiKhoan, Garage...) vào đây, không tải lại trang! -->
            <router-view />
          </div>
        </div>
      </div>
    </section>

    <!-- Global Preloader cho toàn khu vực -->
    <div id="preloader" v-if="isPageLoading"></div>
  </main>
</template>

<script setup>
import { ref, computed, provide } from 'vue';
import { useRoute } from 'vue-router';
import AccountSidebar from '../../../components/client/AccountSidebar.vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';

const route = useRoute();

const titleParts = computed(() => {
  if (route.path === '/tai-khoan') return { main: 'Hồ Sơ', accent: 'Cá Nhân', full: 'Hồ sơ cá nhân' };
  if (route.path === '/garage' || route.path.startsWith('/garage/')) return { main: 'Hạm Đội', accent: 'Garage', full: 'Garage máy thủy' };
  if (route.path === '/vat-tu-thuong-mua') return { main: 'Vật Tư', accent: 'Thường Mua', full: 'Vật tư thường mua' };
  if (route.path === '/lich-su-don-hang') return { main: 'Đơn Hàng &', accent: 'Báo Giá', full: 'Đơn hàng & Báo giá' };
  return { main: 'Quản Lý', accent: 'Tài Khoản', full: 'Tài khoản' };
});

const isProfilePage = computed(() => route.path === '/tai-khoan');

// Cung cấp state loading để các component con (như TaiKhoan) có thể kích hoạt Preloader ở layout chung
const isPageLoading = ref(false);
provide('setPageLoading', (val) => isPageLoading.value = val);

// Fetch profile data cho avatar sidebar
const { data: profileData } = useQuery({
  queryKey: ['clientProfile'],
  queryFn: async () => await axiosClient.get('/client/profile'),
  staleTime: 5 * 60 * 1000,
});
const avatarPreview = ref(null);
const triggerFileInput = () => {};
</script>

<style scoped>
.account-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 800;
  color: var(--jpm-color-navy-deep, #0A1128);
  font-size: clamp(1.75rem, 2.5vw, 2.2rem);
  letter-spacing: -0.5px;
}
</style>
