<template>
  <main class="account-portal-layout flex-grow-1" style="background-color: #f8fafc; min-height: 100vh; padding-top: 110px; padding-bottom: 60px;">
    <section class="py-0" style="background: transparent;">
      <div class="container">
        
        <!-- Header chuẩn 100% Cửa Hàng & Giỏ Hàng -->
        <div class="row mb-4">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">
              {{ titleParts.main }} <span class="text-danger">{{ titleParts.accent }}</span>
            </h2>
            <p class="text-muted mb-0">
              {{ titleParts.desc }}
            </p>
          </div>
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
  if (route.path === '/tai-khoan') return { 
    main: 'Hồ Sơ', 
    accent: 'Cá Nhân', 
    desc: 'Quản lý thông tin tài khoản cá nhân, bảo mật và sổ địa chỉ giao nhận hàng hải.' 
  };
  if (route.path === '/garage' || route.path.startsWith('/garage/')) return { 
    main: 'Garage', 
    accent: 'Của Tôi', 
    desc: 'Quản lý danh sách động cơ máy thủy, sơ đồ kỹ thuật và phụ tùng đã lưu trong garage.' 
  };
  if (route.path === '/vat-tu-thuong-mua') return { 
    main: 'Vật Tư', 
    accent: 'Thường Mua', 
    desc: 'Theo dõi danh mục vật tư tiêu hao, phụ tùng định kỳ và đặt hàng nhanh chóng.' 
  };
  if (route.path === '/lich-su-don-hang') return { 
    main: 'Đơn Hàng &', 
    accent: 'Báo Giá', 
    desc: 'Theo dõi tiến trình báo giá, thanh toán đặt cọc và lộ trình giao nhận phụ tùng.' 
  };
  return { 
    main: 'Quản Lý', 
    accent: 'Tài Khoản', 
    desc: 'Trung tâm quản lý tài khoản và giao dịch khách hàng JPM Marine.' 
  };
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
/* jpm-page-title kế thừa từ main.css */
</style>
