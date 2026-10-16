<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Tài khoản /</span> Hồ sơ cá nhân</h4>
      <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger" @click="confirmLogout">
        <i class="icon-base bx bx-log-out-circle icon-sm me-1"></i>Logout
      </a>
    </div>

    <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-md-row mb-4">
          <li class="nav-item">
            <router-link class="nav-link active" :to="`${basePath}/ho-so`"><i class="bx bx-user me-1"></i> Hồ sơ</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" :to="`${basePath}/doi-mat-khau`"><i class="bx bx-lock-alt me-1"></i> Đổi mật khẩu</router-link>
          </li>
        </ul>
        
        <div class="card mb-4">
          <h5 class="card-header border-bottom py-3 fw-bold">Thông tin chi tiết</h5>
          
          <form id="formAccountSettings" @submit.prevent="saveChanges">
            <div class="row g-0">
              
              <!-- CỘT TRÁI (AVATAR) -->
              <div class="col-md-4 border-end">
                <div class="card-body d-flex flex-column align-items-center text-center justify-content-center h-100 py-5">
                  <div class="position-relative d-inline-block mb-3">
                    <img
                      :src="previewAvatar || getAvatarUrl(profile.avatar)"
                      alt="user-avatar"
                      class="d-block rounded-circle object-fit-cover shadow-sm"
                      height="160"
                      width="160"
                      id="uploadedAvatar"
                      style="border: 3px solid #fff; outline: 1px solid #e7e7ff;"
                    />
                    <!-- Nút Camera đổi ảnh (Chỉ render khi đang Edit) -->
                    <label v-if="isEditing" for="upload" class="btn btn-primary rounded-circle shadow position-absolute d-flex align-items-center justify-content-center" style="bottom: 5px; right: 5px; width: 42px; height: 42px; cursor: pointer; padding: 0;">
                      <i class="bx bx-camera fs-4"></i>
                      <input
                        type="file"
                        id="upload"
                        class="account-file-input"
                        hidden
                        accept="image/png, image/jpeg, image/gif"
                        @change="handleAvatarUpload"
                      />
                    </label>
                  </div>
                  
                  <h6 class="fw-bold mb-1 text-dark">{{ profile.name || 'Admin Manager' }}</h6>
                                    <span class="badge bg-label-primary mt-1">Quản trị viên</span>

                  <p v-if="isEditing" class="text-muted mt-3 mb-0" style="font-size: 0.8rem;">Cho phép JPG, GIF, PNG.<br>Tối đa 800K</p>
                </div>
              </div>
              
              <!-- CỘT PHẢI (THÔNG TIN) -->
              <div class="col-md-8">
                <div class="card-body py-5 px-md-5">
                  <div class="row">
                    
                    <!-- Form dùng 1 cấu trúc duy nhất, chỉ toggle viền -->
                    <div class="mb-4 col-12">
                      <label for="fullName" class="form-label fw-bold text-dark">Họ và Tên <span v-if="isEditing" class="text-danger">*</span></label>
                      <input :class="isEditing ? 'form-control ps-3' : 'form-control-plaintext text-dark px-0'" type="text" id="fullName" v-model="profile.name" :readonly="!isEditing" required maxlength="100" />
                    </div>
                    
                    <div class="mb-4 col-12">
                      <label for="email" class="form-label fw-bold text-dark">E-mail <span v-if="isEditing" class="text-danger">*</span></label>
  <input :class="isEditing ? 'form-control' : 'form-control-plaintext text-dark px-0'" type="email" id="email" v-model="profile.email" :readonly="!isEditing" required />
                    </div>
                    
                    <div class="mb-5 col-12">
                      <label class="form-label fw-bold text-dark" for="phoneNumber">Số điện thoại</label>
                      <div v-if="!isEditing" class="d-flex align-items-center">
  <span class="text-dark me-2 text-nowrap">VN (+84)</span>
  <input type="text" class="form-control-plaintext text-dark px-0" :value="profile.phone" readonly />
</div>
<div v-else class="input-group">
  <span class="input-group-text rounded-end-0" style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important;">VN (+84)</span>
  <input type="text" id="phoneNumber" name="phoneNumber" class="form-control rounded-start-0" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important;" v-model="profile.phone" placeholder="90 123 4567" maxlength="15" pattern="[0-9]{9,15}" title="Số điện thoại phải từ 9-15 chữ số" />
</div>
                    </div>
                  </div>
                  
                  <div class="mt-2 pt-3 border-top d-flex gap-2">
                    <button v-if="!isEditing" type="button" class="btn btn-primary px-4 shadow-sm" @click="isEditing = true">
                      <i class="bx bx-edit-alt me-2"></i> Cập nhật
                    </button>

                    <template v-else>
                      <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Lưu thay đổi
                      </button>
                      <button type="button" class="btn btn-outline-secondary px-4" @click="revertChanges">Hủy</button>
                    </template>
                  </div>
                </div>
              </div>
              
            </div>
          </form>
        </div>

        <!-- Khôi phục lại y chang thiết kế Vô hiệu hóa tài khoản gốc của Sneat -->
        <div class="card">
          <h5 class="card-header border-bottom">Vô hiệu hóa tài khoản</h5>
          <div class="card-body pt-4">
            <div class="mb-3 col-12 mb-0">
              <div class="alert alert-warning">
                <h6 class="alert-heading fw-bold mb-1">Bạn có chắc chắn muốn vô hiệu hóa tài khoản?</h6>
                <p class="mb-0">Dữ liệu sau khi xóa sẽ không thể khôi phục.</p>
              </div>
            </div>
            <form id="formAccountDeactivation" @submit.prevent="deactivateAccount">
              <div class="form-check mb-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="accountActivation"
                  id="accountActivation"
                  v-model="isDeactivateConfirmed"
                />
                <label class="form-check-label" for="accountActivation">Tôi xác nhận vô hiệu hóa tài khoản của mình</label>
              </div>
              <button type="submit" class="btn btn-danger deactivate-account shadow-sm" :disabled="!isDeactivateConfirmed">Vô hiệu hóa tài khoản</button>
            </form>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>


<script>
export default { name: 'HoSo' }
</script>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axiosClient from '../../../api/axiosClient';
import { useAuthStore } from '../../../stores/authStore';
import { createToaster } from "@meforma/vue-toaster";
import Swal from 'sweetalert2';
import { useMutation } from '@tanstack/vue-query';

const route = useRoute();
const basePath = computed(() => (route.path.startsWith('/staff') ? '/staff' : '/admin'));

const toaster = createToaster({ position: "top-right", duration: 3000 });
const authStore = useAuthStore();

const confirmLogout = async () => {
  const result = await Swal.fire({
    title: 'Xác nhận đăng xuất?',
    text: 'Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Đăng xuất',
    cancelButtonText: 'Hủy bỏ',
    customClass: {
      confirmButton: 'btn btn-danger px-4',
      cancelButton: 'btn btn-outline-secondary px-4 me-3'
    },
    reverseButtons: true,
    buttonsStyling: false
  });

  if (result.isConfirmed) {
    await authStore.logout();
    window.location.href = '/dang-nhap';
  }
};

const isEditing = ref(false);
const isDeactivateConfirmed = ref(false);
const avatarFile = ref(null);
const previewAvatar = ref(null);

const defaultProfile = {
  name: "",
  email: "",
  phone: "",
  avatar: ""
};

const profile = ref({ ...defaultProfile });

// Load Data
onMounted(() => {
  if (authStore.user) {
    profile.value = {
      name: authStore.user.name || "",
      email: authStore.user.email || "",
      phone: authStore.user.phone || "",
      avatar: authStore.user.avatar || ""
    };
  }
});

const getAvatarUrl = (path) => {
  if (!path) return `https://ui-avatars.com/api/?name=${encodeURIComponent(authStore.user?.name || 'Admin')}&background=e7e7ff&color=696cff`;
  if (path.startsWith('http')) return path;
  return `http://localhost:8000${path}`;
};

const handleAvatarUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 800 * 1024) {
      toaster.error("Kích thước file vượt quá 800KB");
      event.target.value = '';
      return;
    }
    avatarFile.value = file;
    previewAvatar.value = URL.createObjectURL(file);
  }
};

const resetAvatar = () => {
  avatarFile.value = null;
  previewAvatar.value = null;
  const fileInput = document.getElementById('upload');
  if (fileInput) fileInput.value = '';
};

const revertChanges = () => {
  if (authStore.user) {
    profile.value = {
      name: authStore.user.name || "",
      email: authStore.user.email || "",
      phone: authStore.user.phone || "",
      avatar: authStore.user.avatar || ""
    };
  }
  resetAvatar();
  isEditing.value = false;
};

const updateProfileMutation = useMutation({
  mutationFn: (payload) => axiosClient.post('/me', payload, { headers: { 'Content-Type': 'multipart/form-data' } }),
  onSuccess: async (response) => {
    if (response.success) {
      toaster.success("Cập nhật hồ sơ thành công!");
      await authStore.fetchProfile();
      resetAvatar();
      profile.value.avatar = authStore.user.avatar;
      isEditing.value = false;
    }
  },
  onError: (error) => {
    toaster.error(error.response?.data?.message || "Cập nhật thất bại!");
  }
});

const { isPending: isSubmitting } = updateProfileMutation;

const saveChanges = () => {
  if (!profile.value.name || profile.value.name.length > 100) {
    toaster.error("Vui lòng nhập Họ và Tên (Tối đa 100 ký tự)!");
    return;
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!profile.value.email || !emailRegex.test(profile.value.email) || profile.value.email.length > 100) {
    toaster.error("E-mail không hợp lệ hoặc quá 100 ký tự!");
    return;
  }
  
  if (profile.value.phone && !/^[0-9]{9,15}$/.test(profile.value.phone)) {
    toaster.error("Số điện thoại không hợp lệ (9-15 chữ số)!");
    return;
  }

  const payload = new FormData();
  payload.append('name', profile.value.name);
  payload.append('email', profile.value.email);
  if (profile.value.phone) payload.append('phone', profile.value.phone);
  if (avatarFile.value) payload.append('avatar', avatarFile.value);

  updateProfileMutation.mutate(payload);
};

const deactivateAccountMutation = useMutation({
  mutationFn: (password) => axiosClient.post('/me/deactivate', { password }),
  onSuccess: (response) => {
    if (response.success) {
      Swal.fire({
        icon: 'success',
        title: 'Đã vô hiệu hóa!',
        text: response.message,
        confirmButtonColor: '#696cff'
      }).then(() => {
        authStore.logout();
        window.location.href = '/dang-nhap';
      });
    }
  },
  onError: (error) => {
    if (error.response && error.response.status === 422) {
      toaster.error(error.response.data.message || 'Mật khẩu không chính xác');
    } else {
      toaster.error('Có lỗi xảy ra, vui lòng thử lại!');
    }
  }
});

const deactivateAccount = async () => {
  if (!isDeactivateConfirmed.value) return;

  const { value: password } = await Swal.fire({
    title: 'Xác nhận vô hiệu hóa',
    text: 'Vui lòng nhập mật khẩu của bạn để xác nhận hành động này. Tài khoản sẽ bị xóa vĩnh viễn sau 15 ngày.',
    input: 'password',
    inputPlaceholder: 'Nhập mật khẩu của bạn',
    icon: 'warning',
    iconColor: '#ff3e1d',
    showCancelButton: true,
    confirmButtonText: 'Vô hiệu hóa',
    cancelButtonText: 'Hủy',
    inputValidator: (value) => {
      if (!value) {
        return 'Vui lòng nhập mật khẩu!';
      }
    }
  });

  if (password) {
    deactivateAccountMutation.mutate(password);
  }

  isDeactivateConfirmed.value = false;
};
</script>

<style scoped>
.form-control-plaintext {
  outline: none;
  border-color: transparent !important;
  background-color: transparent !important;
}
</style>



















