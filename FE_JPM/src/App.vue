<template>
  <component :is="layout">
    <router-view />
  </component>
  <Toaster
    position="top-right"
    :offset="toastOffset"
    :mobile-offset="{ top: '14px', left: '14px', right: '14px' }"
    :visible-toasts="4"
    :gap="10"
    rich-colors
  />
</template>

<script>
import { Toaster } from 'vue-sonner'
import { useAuthStore } from './stores/authStore'
import { useCartStore } from './stores/cartStore'
import { setupOfflineListener, syncOfflineData } from './utils/offlineSyncService'
import { notify } from './utils/notify'

export default {
  components: {
    Toaster
  },
  computed: {
    layout() {
      return (this.$route.meta.layout || "client") + "-layout";
    },
    toastOffset() {
      const currentLayout = this.$route.meta?.layout || 'client';
      return currentLayout === 'client'
        ? { top: '76px', right: '20px' }
        : { top: '16px', right: '20px' };
    },
    authStore() {
      return useAuthStore();
    }
  },
  created() {
    setupOfflineListener(notify);
    syncOfflineData(notify);

    // Luôn gọi ngầm API để lấy dữ liệu User mới nhất (Stale-While-Revalidate)
    if (this.authStore.token) {
      this.authStore.fetchProfile();
      useCartStore().fetchFromCloud();
    }
  },
  watch: {
    '$route.path': {
      immediate: true,
      handler(path) {
        if (path === '/') {
          document.documentElement.style.setProperty('--background-color', '#ffffff');
        } else {
          document.documentElement.style.setProperty('--background-color', '#f5f6fa');
        }
      }
    },
    '$route.meta.layout': {
      immediate: true,
      handler(layout) {
        const layoutName = layout || 'client';
        document.body.classList.remove('layout-client', 'layout-admin', 'layout-auth', 'layout-blank');
        document.body.classList.add(`layout-${layoutName}`);
      }
    }
  }
};
</script>

<style>
.global-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-color: #f8f9fa;
  font-family: inherit;
}

/* Tùy chỉnh thanh cuộn (Scrollbar) cho toàn bộ ứng dụng */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #c1c9d2;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #a8b1bb;
}

/* Ép kiểu chữ Sentence Case (không in hoa toàn bộ) cho các Label trong Modal Admin */
.modal .form-label {
  text-transform: none !important;
  font-size: 0.85rem !important; /* Điều chỉnh lại size cho dễ đọc khi không in hoa */
  letter-spacing: normal !important;
}

/* Ép hiển thị đường kẻ ngang ở Footer của Modal để phân tách rõ ràng */
.modal-footer {
  border-top: 1px solid #e9ecef !important;
}

/* Giới hạn chiều cao của menu Dropdown (tăng gấp đôi chiều dài hiển thị) */
.multiselect-dropdown {
  max-height: 400px !important;
}

:root {
  --ms-max-height: 400px !important;
}

/* Tắt hoàn toàn tính năng ẩn thanh cuộn và bù padding của Bootstrap Modal / SweetAlert */
body.modal-open,
body.swal2-shown {
  overflow: auto !important;
  padding-right: 0 !important;
}

/* Sửa lại lề khoảng trắng ở mép phải khi mở Modal (Đồng bộ màu nền Body) */
body {
  background-color: var(--background-color, #f5f6fa) !important;
}
</style>