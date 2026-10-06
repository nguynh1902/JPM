<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script>
import { useAuthStore } from './stores/authStore'
import { setupOfflineListener, syncOfflineData } from './utils/offlineSyncService'
import { createToaster } from '@meforma/vue-toaster'

export default {
  computed: {
    layout() {
      return (this.$route.meta.layout || "client") + "-layout";
    },
    authStore() {
      return useAuthStore();
    }
  },
  created() {
    const toaster = createToaster({ position: "top-right" });
    setupOfflineListener(toaster);
    syncOfflineData(toaster);

    // Luôn gọi ngầm API để lấy dữ liệu User mới nhất (Stale-While-Revalidate)
    if (this.authStore.token) {
      this.authStore.fetchProfile();
      import('./stores/cartStore').then(({ useCartStore }) => {
        useCartStore().fetchFromCloud();
      });
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

/* Tắt hoàn toàn tính năng ẩn thanh cuộn và bù padding của Bootstrap Modal */
body.modal-open {
  overflow: auto !important;
  padding-right: 0 !important;
}

/* Sửa lại lề khoảng trắng ở mép phải khi mở Modal (Đồng bộ màu nền Body) */
body {
  background-color: var(--background-color, #f5f6fa) !important;
}
</style>