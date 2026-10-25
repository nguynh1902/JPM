<template>
  <div>
            
            <!-- THONG TIN CA NHAN -->
            <div class="card border-0 tk-card mb-4">
              <div class="card-header bg-transparent border-bottom tk-card-header">
                <h5 class="mb-0 tk-heading">THÔNG TIN CHI TIẾT</h5>
              </div>
              <div class="card-body p-4">
                <form @submit.prevent="saveProfile">
                  <div class="row g-4">
                    <div class="col-md-6">
                      <label class="form-label fw-bold tk-form-label">Họ và tên</label>
                      <input type="text" v-model="profileForm.name" :class="['form-control-lg fw-bold', isEditingProfile ? 'form-control' : 'form-control-plaintext']" :readonly="!isEditingProfile" required class="tk-form-input">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold tk-form-label">Tên công ty <span class="fw-normal text-muted text-capitalize">(Nếu B2B)</span></label>
                      <input type="text" v-model="profileForm.ten_cong_ty" :class="['form-control-lg fw-bold', isEditingProfile ? 'form-control' : 'form-control-plaintext']" :placeholder="isEditingProfile ? 'Nhập tên công ty...' : 'Chưa cập nhật'" :readonly="!isEditingProfile" class="tk-form-input">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold tk-form-label">Số điện thoại</label>
                      <input type="tel" v-model="profileForm.phone" :class="['form-control-lg fw-bold', isEditingProfile ? 'form-control' : 'form-control-plaintext']" :readonly="!isEditingProfile" class="tk-form-input">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold tk-form-label">Email</label>
                      <input type="email" v-model="profileForm.email" :class="['form-control-lg fw-bold', isEditingProfile ? 'form-control' : 'form-control-plaintext']" :readonly="!isEditingProfile" required class="tk-form-input">
                    </div>
                    
                    <div class="col-12 mt-4 pt-4 border-top d-flex justify-content-end gap-3">
                      <template v-if="!isEditingProfile">
                        <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="bi bi-arrow-counterclockwise"></i>Đổi mật khẩu</button>
                        <button type="button" @click="startEditProfile" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2"><i class="bi bi-pencil"></i>Chỉnh sửa</button>
                      </template>
                      <template v-else>
                        <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2" @click="cancelEditProfile"><i class="bi bi-x-circle"></i>Hủy bỏ</button>
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" :disabled="isUpdatingProfile">
                          <span v-if="isUpdatingProfile" class="spinner-border spinner-border-sm me-2"></span>
                          <i class="bi bi-check-circle" v-else></i> Lưu thay đổi
                        </button>
                      </template>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- ĐỊA CHỈ GIAO HÀNG -->
            <div class="card border-0 tk-card">
              <div class="card-header bg-transparent border-bottom tk-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 tk-heading">ĐỊA CHỈ GIAO HÀNG</h5>
                <button class="btn btn-primary rounded-1 text-uppercase border-0" style="height: 38px; padding: 0 16px; font-size: 11px; font-weight: 600; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px;" @click="openAddAddressModal">
                  <i class="bi bi-plus-lg"></i>Thêm địa chỉ
                </button>
              </div>
              <div class="card-body p-4">

                <!-- Empty State -->
                <div class="text-center py-5" v-if="addresses.length === 0">
                  <i class="bi bi-geo-alt text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                  <h6 class="mt-3 mb-1 tk-heading">Chưa có địa chỉ nào</h6>
                  <p class="text-muted small mb-0">Vui lòng thêm địa chỉ để nhận báo giá hoặc giao hàng.</p>
                </div>

                <!-- List -->
                <div class="d-flex flex-column gap-3" v-else>
                  <div v-for="address in addresses" :key="address.id" class="p-4 border rounded-2" :style="address.la_mac_dinh ? 'border-color: var(--accent-color) !important; background-color: rgba(239, 35, 60, 0.02);' : 'background-color: #fff;'">
                    <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row gap-3">
                      <div>
                        <!-- Tên & SDT (Cùng 1 hàng, cách nhau khoảng rộng, icon) -->
                        <div class="d-flex align-items-center gap-3 mb-2">
                          <h6 class="mb-0" style="color: #031B4E; font-size: 16px; font-weight: 700;">{{ address.ten_nguoi_nhan }}</h6>
                          <span style="font-size: 15px; color: #8A94A6; font-weight: 500;">
                            <i class="bi bi-telephone me-1" style="opacity: 0.6;"></i>{{ address.so_dien_thoai }}
                          </span>
                          <span v-if="address.la_mac_dinh" class="badge rounded-pill ms-2" style="background-color: #FFE2E5; color: #F64E60; font-weight: 500; font-size: 12.5px; padding: 5px 12px;">Mặc định</span>
                        </div>
                        <p class="mb-0 mt-2" style="font-size: 15px; color: #505F79; font-weight: 400;">{{ address.dia_chi_cu_the }}</p>
                      </div>
                      
                      <!-- Actions -->
                      <div class="d-flex align-items-center gap-4 pe-2">
                        <button v-if="!address.la_mac_dinh" @click="setDefault(address.id)" class="btn p-0 text-secondary hover-red" style="font-size: 13px; text-decoration: underline; border: none; background: transparent; transition: color 0.2s;">Đặt mặc định</button>
                        
                        <!-- Nút Sửa -->
                        <button class="btn p-0 action-btn" title="Sửa địa chỉ" @click.prevent="openEditAddressModal(address)" style="border: none; background: transparent;">
                          <i class="bi bi-pencil-square" style="font-size: 1.25rem; color: #16A34A;"></i>
                        </button>
                        <!-- Nút Xóa -->
                        <button class="btn p-0 action-btn" title="Xóa địa chỉ" @click.prevent="deleteAddress(address.id)" style="border: none; background: transparent;">
                          <i class="bi bi-trash3" style="font-size: 1.25rem; color: #f82249;"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>
        

    <!-- MODAL: Doi mat khau -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; border: none;">
          <div class="modal-header border-bottom-0 pt-4 pb-2 px-4">
            <h5 class="modal-title" style="color: var(--heading-color); font-weight: 800; letter-spacing: 0.5px;">ĐỔI MẬT KHẨU</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="changePassword">
              <div class="mb-3">
                <label class="form-label fw-bold tk-form-label">Mật khẩu hiện tại</label>
                <input type="password" v-model="passwordForm.current_password" class="form-control form-control-lg" required>
                <div class="text-danger small mt-1" v-if="passwordErrors.current_password">{{ passwordErrors.current_password[0] }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold tk-form-label">Mật khẩu mới</label>
                <input type="password" v-model="passwordForm.new_password" class="form-control form-control-lg" required>
                <div class="text-danger small mt-1" v-if="passwordErrors.new_password">{{ passwordErrors.new_password[0] }}</div>
              </div>
              <div class="mb-4">
                <label class="form-label fw-bold tk-form-label">Xác nhận mật khẩu</label>
                <input type="password" v-model="passwordForm.new_password_confirmation" class="form-control form-control-lg" required>
              </div>
              <button type="submit" class="btn btn-danger w-100 justify-content-center py-2 fw-semibold mt-3" :disabled="isChangingPassword">
                <span v-if="isChangingPassword" class="spinner-border spinner-border-sm"></span>
                <i class="bi bi-shield-check" v-else></i> Cập nhật mật khẩu
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: Dia chi -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; border: none;">
          <div class="modal-header border-bottom-0 pt-4 pb-2 px-4">
            <h5 class="modal-title" style="color: var(--heading-color); font-weight: 800; letter-spacing: 0.5px;">{{ isEditingAddress ? 'SỬA ĐỊA CHỈ' : 'THÊM ĐỊA CHỈ MỚI' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveAddress">
              <div class="mb-3">
                <label class="form-label fw-bold tk-form-label">Họ và tên người nhận</label>
                <input type="text" v-model="addressForm.ten_nguoi_nhan" class="form-control form-control-lg fw-bold" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold tk-form-label">Số điện thoại</label>
                <input type="text" v-model="addressForm.so_dien_thoai" class="form-control form-control-lg fw-bold" required>
              </div>
              <div class="mb-4">
                <label class="form-label fw-bold tk-form-label">Địa chỉ cụ thể</label>
                <textarea v-model="addressForm.dia_chi_cu_the" class="form-control form-control-lg fw-bold" rows="3" required></textarea>
              </div>
              <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="defaultAddress" v-model="addressForm.la_mac_dinh">
                <label class="form-check-label fw-bold text-secondary" style="font-size: 13px;" for="defaultAddress">Đặt làm địa chỉ mặc định</label>
              </div>
              <button type="submit" class="btn btn-danger w-100 justify-content-center py-2 fw-semibold mt-3" :disabled="isSavingAddress">
                <span v-if="isSavingAddress" class="spinner-border spinner-border-sm me-2"></span>
                <i class="bi bi-save" v-else></i> Lưu địa chỉ
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useAuthStore } from '../../../stores/authStore';

import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { createToaster } from "@meforma/vue-toaster";
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';

const authStore = useAuthStore();
const toaster = createToaster({ position: "top-right" });
const queryClient = useQueryClient();

// 1. Fetch Profile Query
const { data: profileData, isLoading: isLoadingProfile } = useQuery({
  queryKey: ['clientProfile'],
  queryFn: async () => await axiosClient.get('/client/profile'),
  staleTime: 5 * 60 * 1000,
});

// 2. Fetch Addresses Query
const { data: addressesData, isLoading: isLoadingAddresses } = useQuery({
  queryKey: ['clientAddresses'],
  queryFn: async () => await axiosClient.get('/client/profile/addresses'),
  staleTime: 5 * 60 * 1000,
});
const addresses = computed(() => addressesData.value || []);

// Global page loading: shows full-screen preloader until all data is ready (same as TrangChu.vue)


// Local States
const isEditingProfile = ref(false);
const profileForm = ref({ name: '', phone: '', email: '', ten_cong_ty: '' });

watch(profileData, (newData) => {
  if (newData) {
    profileForm.value.name = newData.name || '';
    profileForm.value.phone = newData.khach_hang?.so_dien_thoai || newData.phone || '';
    profileForm.value.email = newData.email || '';
    profileForm.value.ten_cong_ty = newData.khach_hang?.ten_cong_ty || '';
  }
}, { immediate: true });

const userAvatar = computed(() => {
  if (profileData.value && profileData.value.avatar) return profileData.value.avatar;
  return 'https://ui-avatars.com/api/?name=' + (profileData.value?.name || 'User') + '&size=140&background=0A1128&color=fff';
});

// Sync form when entering edit mode
const startEditProfile = () => {
  profileForm.value.name = profileData.value?.name || '';
  profileForm.value.phone = profileData.value?.khach_hang?.so_dien_thoai || profileData.value?.phone || '';
  profileForm.value.email = profileData.value?.email || '';
  profileForm.value.ten_cong_ty = profileData.value?.khach_hang?.ten_cong_ty || '';
  isEditingProfile.value = true;
};

const cancelEditProfile = () => {
  if (profileData.value) {
    profileForm.value.name = profileData.value.name || '';
    profileForm.value.phone = profileData.value.khach_hang?.so_dien_thoai || profileData.value.phone || '';
    profileForm.value.email = profileData.value.email || '';
    profileForm.value.ten_cong_ty = profileData.value.khach_hang?.ten_cong_ty || '';
  }
  isEditingProfile.value = false;
};

// Update Profile Mutation
const updateProfileMutation = useMutation({
  mutationFn: (data) => axiosClient.put('/client/profile', data),
  onSuccess: (response) => {
    const updatedUser = response.user || response.data?.user || response;
    queryClient.setQueryData(['clientProfile'], updatedUser);
    authStore.user = updatedUser;
    localStorage.setItem('auth_user', JSON.stringify(authStore.user));
    isEditingProfile.value = false;
    toaster.success("Cập nhật thông tin thành công!");
  },
  onError: (error) => {
    if (error.response?.status === 422) {
      Object.values(error.response.data.errors).forEach(err => toaster.error(err[0]));
    } else {
      toaster.error("Có lỗi xảy ra khi cập nhật!");
    }
  }
});
const isUpdatingProfile = computed(() => updateProfileMutation.isPending.value);

const saveProfile = async () => {
  const emailChanged = profileForm.value.email !== profileData.value?.email;

  if (emailChanged) {
    // Email bị đổi → bắt xác nhận mật khẩu trước khi lưu
    const result = await Swal.fire({
      title: 'Xác nhận đổi Email',
      html: `<p class="text-muted" style="font-size:14px;">Bạn đang thay đổi Email đăng nhập.<br>Nhập mật khẩu hiện tại để xác nhận.</p>`,
      input: 'password',
      inputLabel: 'Mật khẩu hiện tại',
      inputPlaceholder: 'Nhập mật khẩu...',
      inputAttributes: { autocomplete: 'current-password' },
      showCancelButton: true,
      confirmButtonColor: '#f82249',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Xác nhận',
      cancelButtonText: 'Hủy',
      inputValidator: (value) => {
        if (!value) return 'Vui lòng nhập mật khẩu!';
      }
    });

    if (!result.isConfirmed) return; // Người dùng bấm Hủy
    updateProfileMutation.mutate({ ...profileForm.value, current_password: result.value });
  } else {
    // Không đổi Email → lưu bình thường
    updateProfileMutation.mutate(profileForm.value);
  }
};

// Password Mutation
const passwordForm = ref({ current_password: '', new_password: '', new_password_confirmation: '' });
const passwordErrors = ref({});
const changePasswordMutation = useMutation({
  mutationFn: (data) => axiosClient.put('/client/profile/password', data),
  onSuccess: () => {
    toaster.success("Đổi mật khẩu thành công!");
    const modal = window.bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
    if (modal) modal.hide();
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' };
    passwordErrors.value = {};
  },
  onError: (error) => {
    if (error.response?.status === 422) {
      passwordErrors.value = error.response.data.errors;
    } else {
      toaster.error(error.response?.data?.message || "Có lỗi xảy ra!");
    }
  }
});
const isChangingPassword = computed(() => changePasswordMutation.isPending.value);

const changePassword = () => {
  changePasswordMutation.mutate(passwordForm.value);
};

// Address Management
const addressForm = ref({ id: null, ten_nguoi_nhan: '', so_dien_thoai: '', dia_chi_cu_the: '', la_mac_dinh: false });
const isEditingAddress = ref(false);
let addressModalInstance = null;

onMounted(() => {
  if (typeof window !== 'undefined' && window.bootstrap) {
    addressModalInstance = new window.bootstrap.Modal(document.getElementById('addressModal'));
  }
});

const openAddAddressModal = () => {
  isEditingAddress.value = false;
  addressForm.value = { 
    id: null, 
    ten_nguoi_nhan: profileData.value?.name || '', 
    so_dien_thoai: profileData.value?.khach_hang?.so_dien_thoai || profileData.value?.phone || '', 
    dia_chi_cu_the: '', 
    la_mac_dinh: addresses.value.length === 0 
  };
  if (addressModalInstance) addressModalInstance.show();
};

const openEditAddressModal = (address) => {
  isEditingAddress.value = true;
  addressForm.value = { ...address, la_mac_dinh: address.la_mac_dinh == 1 };
  if (addressModalInstance) addressModalInstance.show();
};

// Save Address Mutation (Create or Update)
const saveAddressMutation = useMutation({
  mutationFn: (data) => {
    if (isEditingAddress.value) {
      return axiosClient.put('/client/profile/addresses/' + data.id, data);
    } else {
      return axiosClient.post('/client/profile/addresses', data);
    }
  },
  
  onError: (error) => { if (error.response?.status === 422) { Object.values(error.response.data.errors).forEach(err => toaster.error(err[0])); } else { toaster.error("Có lỗi xảy ra khi lưu địa chỉ!"); } },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['clientAddresses'] });
  },
  onSuccess: () => {
    toaster.success(isEditingAddress.value ? "Đã sửa địa chỉ!" : "Đã thêm địa chỉ!");
    if (addressModalInstance) addressModalInstance.hide();
  }
});
const isSavingAddress = computed(() => saveAddressMutation.isPending.value);

const saveAddress = () => {
  saveAddressMutation.mutate(addressForm.value);
};

// Delete Address Mutation
const deleteAddressMutation = useMutation({
  mutationFn: (id) => axiosClient.delete('/client/profile/addresses/' + id),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ['clientAddresses'] });
    const previousAddresses = queryClient.getQueryData(['clientAddresses']);
    
    queryClient.setQueryData(['clientAddresses'], (old) => {
      const list = Array.isArray(old) ? old : [];
      return list.filter(item => item.id !== id);
    });
    
    return { previousAddresses };
  },
  onError: (err, id, context) => {
    queryClient.setQueryData(['clientAddresses'], context.previousAddresses);
    toaster.error("Không thể xóa địa chỉ lúc này!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['clientAddresses'] });
  },
  onSuccess: () => {
    toaster.success("Đã xóa địa chỉ!");
  }
});

const deleteAddress = async (id) => {
  const result = await Swal.fire({
    title: 'Bạn có chắc chắn?',
    text: "Địa chỉ này sẽ bị xóa vĩnh viễn!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f82249',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Đồng ý, xóa!',
    cancelButtonText: 'Hủy'
  });
  if (result.isConfirmed) {
    deleteAddressMutation.mutate(id);
  }
};

// Set Default Address Mutation
const setDefaultMutation = useMutation({
  mutationFn: (id) => axiosClient.put('/client/profile/addresses/' + id + '/set-default'),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ['clientAddresses'] });
    const previousAddresses = queryClient.getQueryData(['clientAddresses']);
    
    queryClient.setQueryData(['clientAddresses'], (old) => {
      const list = Array.isArray(old) ? old : [];
      return list.map(item => ({
        ...item,
        la_mac_dinh: item.id === id ? 1 : 0
      }));
    });
    
    return { previousAddresses };
  },
  onError: (err, id, context) => {
    queryClient.setQueryData(['clientAddresses'], context.previousAddresses);
    toaster.error("Có lỗi xảy ra!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['clientAddresses'] });
  },
  onSuccess: () => {
    toaster.success("Đã đặt làm mặc định!");
  }
});

const setDefault = (id) => {
  setDefaultMutation.mutate(id);
};
</script>

<style scoped>

.tk-card {
  box-shadow: var(--shadow-sm);
  border-radius: 4px;
}
.tk-card-header {
  padding: 20px 24px;
}
.tk-heading {
  color: var(--heading-color);
  font-weight: 700;
}
.tk-page-title {
  color: var(--heading-color);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: clamp(1.8rem, 3vw, 2.2rem);
  margin-bottom: 8px;
}
.tk-form-label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #7e8299;
}
.tk-form-input {
  font-size: 15px;
  color: var(--heading-color);
}
.sidebar-menu-item {
  padding: 12px 24px;
  font-weight: 500;
  transition: color 0.3s;
}
.sidebar-menu-item-active {
  padding: 12px 24px;
  font-weight: 600;
}

.active-sidebar-link {
  color: var(--accent-color) !important;
  background-color: rgba(239, 35, 60, 0.04) !important;
  border-left: 3px solid var(--accent-color) !important;
}

.list-group-item-action:hover {
  background-color: rgba(0,0,0,0.02);
  color: var(--heading-color) !important;
}

.hover-red:hover {
  color: var(--accent-color) !important;
}
.form-control-plaintext::placeholder {
  color: var(--heading-color) !important;
  opacity: 0.4 !important;
  font-weight: 500 !important;
}
</style>


