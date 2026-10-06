<template>
  <div class="col-lg-3">
    <div class="card border-0 tk-card overflow-hidden" style="box-shadow: var(--shadow-sm); border-radius: 12px;">
      <div class="card-body text-center p-4 border-bottom">
        <div class="position-relative d-inline-block mb-3" style="width: 140px; height: 140px;">
          <img :src="userAvatar" alt="Avatar" class="rounded-circle w-100 h-100" style="object-fit: cover; border: 4px solid var(--surface-color); box-shadow: var(--shadow-sm);">
          <button v-if="showCamera" @click="$emit('change-avatar')" class="btn btn-light position-absolute rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; bottom: 5px; right: 5px; box-shadow: var(--shadow-md); border: 1px solid #eaeaea;" title="Đổi ảnh">
            <i class="bi bi-camera-fill text-secondary" style="font-size: 18px;"></i>
          </button>
        </div>
        <h4 style="color: var(--heading-color); font-weight: 700; margin-bottom: 5px;">{{ userName }}</h4>
        <span class="text-muted" style="font-size: 14px; font-weight: 500;"><i class="bi bi-person-badge me-2"></i>Thành viên</span>
      </div>
      <div class="list-group list-group-flush" style="padding: 10px 0;">
        <router-link to="/tai-khoan" class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 text-secondary sidebar-menu-item" active-class="active-sidebar-link">
          <i class="bi bi-person-circle fs-5"></i> Hồ sơ cá nhân
        </router-link>
        <router-link to="/garage" class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 text-secondary sidebar-menu-item" active-class="active-sidebar-link">
          <i class="bi bi-tools fs-5"></i> Garage
        </router-link>
        <router-link to="/vat-tu-thuong-mua" class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 text-secondary sidebar-menu-item" active-class="active-sidebar-link">
          <i class="bi bi-bookmark-heart fs-5"></i> Vật tư thường mua
        </router-link>
        <router-link to="/lich-su-don-hang" class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 text-secondary sidebar-menu-item" active-class="active-sidebar-link">
          <i class="bi bi-receipt fs-5"></i> Lịch sử báo giá
        </router-link>
        <hr class="my-2 mx-3 text-muted">
        <a href="#" @click.prevent="logout" class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-3 text-danger sidebar-menu-item">
          <i class="bi bi-box-arrow-right fs-5"></i> Đăng xuất
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

const logout = async () => {
  const result = await Swal.fire({
    title: "Đăng xuất?",
    text: "Bạn có chắc chắn muốn đăng xuất?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#f82249",
    cancelButtonColor: "#6c757d",
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
.sidebar-menu-item {
  padding: 12px 24px;
  font-weight: 500;
  transition: all 0.2s ease;
}
.sidebar-menu-item:hover {
  background-color: #f8f9fa;
  color: var(--heading-color) !important;
}
.active-sidebar-link {
  color: var(--heading-color) !important;
  font-weight: 600;
  background-color: rgba(248, 34, 73, 0.05);
  border-left: 3px solid #f82249 !important;
}
.active-sidebar-link i {
  color: #f82249;
}
</style>

