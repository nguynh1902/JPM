<template>
  <div class="col-lg-3">
    <div class="card border-0 account-sidebar-card overflow-hidden">
      <!-- Profile Header Area -->
      <div class="card-body text-center p-4 border-bottom">
        <div class="position-relative d-inline-block mb-3" style="width: 108px; height: 108px;">
          <img :src="userAvatar" alt="Avatar" class="rounded-circle w-100 h-100 account-avatar-img">
          <button v-if="showCamera" @click="$emit('change-avatar')" class="btn btn-light position-absolute rounded-circle d-flex align-items-center justify-content-center account-camera-btn" title="Đổi ảnh đại diện">
            <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
          </button>
        </div>
        <h5 class="account-user-name mb-1">{{ userName }}</h5>
        <p class="account-user-role mb-0">{{ userRoleLabel }}</p>
      </div>

      <!-- Modern Floating Pill Navigation -->
      <div class="account-nav-list p-3">
        <router-link to="/tai-khoan" class="account-pill-link" active-class="active-pill-link">
          <i class="bi bi-person-circle fs-5"></i>
          <span>Hồ sơ cá nhân</span>
        </router-link>
        <router-link to="/garage" class="account-pill-link" active-class="active-pill-link">
          <i class="bi bi-tools fs-5"></i>
          <span>Garage của tôi</span>
        </router-link>
        <router-link to="/vat-tu-thuong-mua" class="account-pill-link" active-class="active-pill-link">
          <i class="bi bi-bookmark-heart fs-5"></i>
          <span>Vật tư thường mua</span>
        </router-link>
        <router-link to="/lich-su-don-hang" class="account-pill-link" active-class="active-pill-link">
          <i class="bi bi-receipt fs-5"></i>
          <span>Đơn hàng & Báo giá</span>
        </router-link>

        <div class="my-2 border-top mx-2" style="border-color: #f1f5f9 !important;"></div>

        <a href="#" @click.prevent="logout" class="account-pill-link account-logout-link">
          <i class="bi bi-box-arrow-right fs-5"></i>
          <span>Đăng xuất</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/authStore";
import Swal from "sweetalert2";

const props = defineProps({
  showCamera: {
    type: Boolean,
    default: false
  },
  customName: {
    type: String,
    default: ""
  },
  previewAvatar: {
    type: String,
    default: null
  }
});

defineEmits(['change-avatar']);

const router = useRouter();
const authStore = useAuthStore();

const userAvatar = computed(() => {
  if (props.previewAvatar) return props.previewAvatar;
  return authStore.user?.avatar || "https://ui-avatars.com/api/?name=" + (authStore.user?.name || "U") + "&background=random";
});

const userName = computed(() => {
  return props.customName || authStore.user?.name || "Tài khoản";
});

const userRoleLabel = computed(() => {
  const user = authStore.user;
  if (!user) return 'Khách hàng';
  
  if (authStore.isAdmin || user.is_admin) return 'Admin';
  
  const roles = user.roles || [];
  const roleNames = roles.map(r => String(typeof r === 'string' ? r : (r?.name || '')).toLowerCase().trim());
  if (authStore.isStaffOrAdmin || roleNames.some(r => r === 'staff' || r.includes('nhân viên')) || user.nhan_vien) {
    return 'Nhân viên';
  }
  
  return 'Khách hàng';
});

const logout = async () => {
  const result = await Swal.fire({
    title: "Đăng xuất?",
    text: "Bạn có chắc chắn muốn đăng xuất khỏi hệ thống JPM?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#f82249",
    cancelButtonColor: "#64748b",
    confirmButtonText: "Đăng xuất",
    cancelButtonText: "Hủy",
  });

  if (result.isConfirmed) {
    authStore.logout();
    router.push("/");
  }
};
</script>

<style scoped>
.account-sidebar-card {
  border: 1px solid #e2e8f0 !important;
  border-radius: var(--jpm-radius-card, 12px) !important;
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04) !important;
  background: #ffffff;
}

.account-avatar-img {
  object-fit: cover;
  border: 3px solid #ffffff;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.08);
}

.account-camera-btn {
  width: 32px;
  height: 32px;
  bottom: 2px;
  right: 2px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  transition: all 0.2s ease;
  color: #475569;
}
.account-camera-btn:hover {
  background: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  border-color: var(--jpm-color-red-marine, #F82249);
}

.account-user-name {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.account-user-role {
  font-size: 13.5px;
  color: #64748b;
  font-weight: 500;
}

.account-nav-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.account-pill-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  border-radius: var(--jpm-radius-control, 8px);
  font-size: 0.9rem;
  font-weight: 500;
  color: #475569;
  text-decoration: none;
  transition: all 0.2s ease;
}

.account-pill-link i {
  color: #94a3b8;
  transition: color 0.2s ease;
}

.account-pill-link:hover {
  background-color: #f1f5f9;
  color: var(--jpm-color-navy-deep, #0A1128);
  transform: translateX(2px);
}

.account-pill-link:hover i {
  color: var(--jpm-color-navy-deep, #0A1128);
}

.active-pill-link {
  background-color: rgba(248, 34, 73, 0.08) !important;
  color: var(--jpm-color-red-marine, #F82249) !important;
  font-weight: 600 !important;
}

.active-pill-link i {
  color: var(--jpm-color-red-marine, #F82249) !important;
}

.account-logout-link {
  color: #64748b;
}

.account-logout-link:hover {
  background-color: rgba(248, 34, 73, 0.06);
  color: var(--jpm-color-red-marine, #F82249) !important;
}

.account-logout-link:hover i {
  color: var(--jpm-color-red-marine, #F82249) !important;
}
</style>
