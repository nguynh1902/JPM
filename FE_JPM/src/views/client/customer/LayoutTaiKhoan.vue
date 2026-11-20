<template>
  <div class="account-portal-layout flex-grow-1 inner-page-section" style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 60px;">
    <section class="py-0" style="background: transparent;">
      <div class="container">
        
        <!-- Header chuẩn 100% Cửa Hàng & Giỏ Hàng -->
        <div class="row mb-4">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">
              {{ titleParts.main }} <span class="text-danger">{{ titleParts.accent }}</span>
            </h2>
            <p class="text-muted jpm-page-subtitle mb-0">
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
            <router-view class="h-100 flex-grow-1 d-flex flex-column" />
          </div>
        </div>
      </div>
    </section>

    <!-- Global Preloader cho toàn khu vực -->
    <div id="preloader" v-if="isPageLoading"></div>

    <!-- Hidden file input for Avatar upload -->
    <input 
      type="file" 
      ref="avatarFileInput" 
      class="d-none" 
      accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" 
      @change="handleAvatarFileChange" 
    />
  </div>
</template>

<script setup>
import { ref, computed, provide } from 'vue';
import { useRoute } from 'vue-router';
import AccountSidebar from '../../../components/client/AccountSidebar.vue';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { compressAvatar, formatBytes } from '../../../utils/imageOptimizer';
import { useAuthStore } from '../../../stores/authStore';
import Swal from 'sweetalert2';

const route = useRoute();
const authStore = useAuthStore();
const queryClient = useQueryClient();
const avatarFileInput = ref(null);
const isUploadingAvatar = ref(false);

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

const triggerFileInput = () => {
  if (avatarFileInput.value) {
    avatarFileInput.value.click();
  }
};

const handleAvatarFileChange = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  try {
    isUploadingAvatar.value = true;
    
    // Tự động crop vuông 1:1, resize 300x300 và nén WebP ngay trên trình duyệt
    const { file: compressedFile, previewUrl, originalSize, compressedSize, savedPercent } = await compressAvatar(file);
    
    // Preview tức thì
    avatarPreview.value = previewUrl;

    const formData = new FormData();
    formData.append('avatar', compressedFile);

    const res = await axiosClient.post('/client/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (res.data?.avatar) {
      if (authStore.user) {
        authStore.user.avatar = res.data.avatar;
      }
      queryClient.invalidateQueries(['clientProfile']);
      
      Swal.fire({
        icon: 'success',
        title: 'Đổi ảnh đại diện thành công!',
        html: `Đã nén tối ưu: <strong>${formatBytes(originalSize)}</strong> &rarr; <strong>${formatBytes(compressedSize)}</strong> (tiết kiệm ${savedPercent}% dung lượng).`,
        timer: 3500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
  } catch (error) {
    console.error('Lỗi upload avatar:', error);
    Swal.fire({
      icon: 'error',
      title: 'Lỗi tải ảnh',
      text: error.message || 'Không thể tải ảnh đại diện lúc này.',
      confirmButtonColor: '#F82249'
    });
  } finally {
    isUploadingAvatar.value = false;
    if (event.target) event.target.value = '';
  }
};
</script>

<style scoped>
/* jpm-page-title & jpm-page-subtitle đồng bộ 100% Cửa Hàng, Tra Cứu, Tin Tức */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 28px !important;
  font-weight: 800;
  color: var(--heading-color, #0A1128) !important;
  letter-spacing: -0.5px;
  line-height: 1.25;
}
.jpm-page-subtitle {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.95rem;
  font-weight: 400;
  color: #64748b !important;
  line-height: 1.6;
}
</style>
