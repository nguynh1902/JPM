<template>
  <main class="py-5 flex-grow-1" style="background-color: var(--surface-color);">
    <section>
      <div class="container">
        
        <!-- PAGE TITLE -->
        <div style="margin-bottom: 40px; text-align: left;">
          <h1 style="color: var(--heading-color); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: clamp(1.8rem, 3vw, 2.2rem); margin-bottom: 8px;">{{ pageTitle }}</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="margin-bottom: 0; font-size: 0.85rem;">
              <li class="breadcrumb-item"><router-link to="/" class="text-secondary text-decoration-none">Trang chủ</router-link></li>
              <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ pageTitle }}</li>
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

const pageTitle = computed(() => {
  if (route.path === '/tai-khoan') return 'QUẢN LÝ TÀI KHOẢN';
  if (route.path === '/garage' || route.path.startsWith('/garage/')) return 'GARAGE';
  if (route.path === '/vat-tu-thuong-mua') return 'VẬT TƯ THƯỜNG MUA';
  if (route.path === '/lich-su-don-hang') return 'LỊCH SỬ BÁO GIÁ';
  return 'TÀI KHOẢN';
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


