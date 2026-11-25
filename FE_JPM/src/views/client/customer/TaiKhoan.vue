<template>
  <div class="h-100 d-flex flex-column flex-grow-1">
            
    <!-- ========================================================
         CARD 1: THÔNG TIN CÁ NHÂN (Đồng bộ 100% Client Design System)
         ======================================================== -->
    <div class="card border-0 jpm-card mb-4">
      <div class="card-header jpm-card-header">
        <h5 class="jpm-card-title">Thông tin cá nhân</h5>
      </div>
      <div class="card-body p-4">
        <form @submit.prevent="saveProfile">
          <div class="row g-4">
            <!-- Họ và tên -->
            <div class="col-md-6">
              <label class="form-label tk-form-label">Họ và tên</label>
              <input 
                type="text" 
                v-model="profileForm.name" 
                :class="[isEditingProfile ? 'form-control' : 'form-control-plaintext']" 
                :readonly="!isEditingProfile" 
                required 
                class="tk-form-input"
              >
            </div>

            <!-- Tên công ty -->
            <div class="col-md-6">
              <label class="form-label tk-form-label">Tên công ty <span class="fw-normal text-muted">(Nếu B2B)</span></label>
              <input 
                type="text" 
                v-model="profileForm.ten_cong_ty" 
                :class="[isEditingProfile ? 'form-control' : 'form-control-plaintext']" 
                :placeholder="isEditingProfile ? 'Nhập tên công ty...' : 'Chưa cập nhật'" 
                :readonly="!isEditingProfile" 
                class="tk-form-input"
              >
            </div>

            <!-- Số điện thoại -->
            <div class="col-md-6">
              <label class="form-label tk-form-label">Số điện thoại</label>
              <input 
                type="tel" 
                v-model="profileForm.phone" 
                :class="[isEditingProfile ? 'form-control' : 'form-control-plaintext']" 
                :readonly="!isEditingProfile" 
                class="tk-form-input tabular-nums"
              >
            </div>

            <!-- Địa chỉ Email -->
            <div class="col-md-6">
              <label class="form-label tk-form-label">Địa chỉ Email</label>
              <input 
                type="email" 
                v-model="profileForm.email" 
                :class="[isEditingProfile ? 'form-control' : 'form-control-plaintext']" 
                :readonly="!isEditingProfile" 
                required 
                class="tk-form-input"
              >
            </div>
            
            <!-- Nhóm nút hành động ở đáy form (Đồng bộ 100% chuẩn Client Design System) -->
            <div class="col-12 mt-4 pt-4 border-top d-flex justify-content-end gap-3">
              <template v-if="!isEditingProfile">
                <button 
                  type="button" 
                  class="btn btn-action-secondary" 
                  data-bs-toggle="modal" 
                  data-bs-target="#changePasswordModal"
                >
                  <i class="bi bi-shield-lock"></i>
                  <span>Đổi mật khẩu</span>
                </button>
                <button 
                  type="button" 
                  @click="startEditProfile" 
                  class="btn btn-action-primary"
                >
                  <i class="bi bi-pencil"></i>
                  <span>Chỉnh sửa</span>
                </button>
              </template>
              <template v-else>
                <button 
                  type="button" 
                  class="btn btn-action-secondary" 
                  @click="cancelEditProfile"
                >
                  <i class="bi bi-x-circle"></i>
                  <span>Hủy bỏ</span>
                </button>
                <button 
                  type="submit" 
                  class="btn btn-action-primary" 
                  :disabled="isUpdatingProfile"
                >
                  <span v-if="isUpdatingProfile" class="spinner-border spinner-border-sm me-2"></span>
                  <i class="bi bi-check-circle" v-else></i>
                  <span>Lưu thay đổi</span>
                </button>
              </template>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================
         CARD 2: ĐỊA CHỈ GIAO HÀNG (Đồng bộ 100% kích thước & màu sắc)
         ======================================================== -->
    <div class="card border-0 jpm-card">
      <div class="card-header jpm-card-header">
        <h5 class="jpm-card-title">Địa chỉ giao hàng</h5>
        <!-- Nút Thêm mới đối tượng: Đồng bộ kích thước 40px & chuẩn Red Marine toàn Client -->
        <button class="btn btn-action-primary" @click="openAddAddressModal">
          <i class="bi bi-plus-lg"></i>
          <span>Thêm địa chỉ mới</span>
        </button>
      </div>
      <div class="card-body p-4">

        <!-- Empty State -->
        <div class="text-center py-5" v-if="addresses.length === 0">
          <div class="empty-icon-wrap mx-auto mb-3">
            <i class="bi bi-geo-alt"></i>
          </div>
          <h6 class="mb-1 tk-heading">Chưa có địa chỉ nào</h6>
          <p class="text-muted small mb-0">Vui lòng thêm địa chỉ để nhận báo giá hoặc giao hàng thuận tiện nhất.</p>
        </div>

        <!-- List địa chỉ: Đồng bộ 100% hình khối & kích thước với Garage và Vật tư thường mua -->
        <div class="d-flex flex-column gap-3" v-else>
          <div
            v-for="address in addresses"
            :key="address.id"
            class="border address-item-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
            :class="{ 'address-item-default': address.la_mac_dinh }"
          >
            <!-- Bên trái: Thông tin chi tiết địa chỉ -->
            <div class="flex-grow-1 min-w-0">
              <!-- Dòng trên: Tên người nhận + SĐT + Badge Mặc định -->
              <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h6 class="fw-bold mb-0 text-truncate address-recipient-name">
                  {{ address.ten_nguoi_nhan }}
                </h6>
                <span class="address-divider">|</span>
                <span class="address-recipient-phone">
                  <i class="bi bi-telephone me-1 opacity-75"></i>{{ address.so_dien_thoai }}
                </span>
                <span v-if="address.la_mac_dinh" class="badge address-default-badge">
                  Mặc định
                </span>
              </div>

              <!-- Dòng dưới: Địa chỉ cụ thể -->
              <p class="mb-0 text-truncate address-text" :title="address.dia_chi_cu_the">
                {{ address.dia_chi_cu_the }}
              </p>
            </div>

            <!-- Bên phải: Các nút thao tác chuẩn 36px đồng bộ 100% với Garage & VatTuThuongMua -->
            <div class="d-flex align-items-center gap-2 pe-1 flex-shrink-0 align-self-end align-self-md-center">
              <!-- Nút Đặt mặc định: Cao 36px, bo góc 8px -->
              <button
                v-if="!address.la_mac_dinh"
                type="button"
                class="btn btn-set-default"
                title="Đặt làm địa chỉ nhận hàng mặc định"
                @click="setDefault(address.id)"
              >
                Đặt mặc định
              </button>

              <!-- Nút Sửa: 36x36px icon chuẩn -->
              <button
                type="button"
                class="btn btn-action-icon btn-edit-icon"
                title="Sửa địa chỉ"
                @click.prevent="openEditAddressModal(address)"
              >
                <i class="bi bi-pencil"></i>
              </button>

              <!-- Nút Xóa: 36x36px icon chuẩn -->
              <button
                type="button"
                class="btn btn-action-icon btn-delete-icon"
                title="Xóa địa chỉ"
                @click.prevent="deleteAddress(address.id)"
              >
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================
         MODAL: ĐỔI MẬT KHẨU
         ======================================================== -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--jpm-radius-card, 12px) !important; overflow: hidden;">
          <div class="modal-header d-flex justify-content-between align-items-center w-100 border-bottom px-4 py-3">
            <h5 class="modal-title tk-heading mb-0">Đổi Mật Khẩu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="changePassword">
              <div class="mb-3">
                <label class="form-label tk-form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                <input type="password" v-model="passwordForm.current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại..." required>
                <div class="text-danger small mt-1" v-if="passwordErrors.current_password">{{ passwordErrors.current_password[0] }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label tk-form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                <input type="password" v-model="passwordForm.new_password" class="form-control" placeholder="Nhập mật khẩu mới..." required>
                <div class="text-danger small mt-1" v-if="passwordErrors.new_password">{{ passwordErrors.new_password[0] }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label tk-form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                <input type="password" v-model="passwordForm.new_password_confirmation" class="form-control" placeholder="Xác nhận lại mật khẩu mới..." required>
              </div>
              <div class="d-flex justify-content-end gap-2 pt-3 mt-4 border-top">
                <button type="button" class="btn btn-secondary-action" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger-action" :disabled="isChangingPassword">
                  <span v-if="isChangingPassword" class="spinner-border spinner-border-sm me-2"></span>
                  Cập nhật mật khẩu
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================
         MODAL: THÊM / SỬA ĐỊA CHỈ
         ======================================================== -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--jpm-radius-card, 12px) !important; overflow: hidden;">
          <div class="modal-header d-flex justify-content-between align-items-center w-100 border-bottom px-4 py-3">
            <h5 class="modal-title tk-heading mb-0">{{ isEditingAddress ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ mới' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveAddress">
              <div class="mb-3">
                <label class="form-label tk-form-label">Họ và tên người nhận <span class="text-danger">*</span></label>
                <input type="text" v-model="addressForm.ten_nguoi_nhan" class="form-control" placeholder="Nhập họ và tên người nhận..." required>
              </div>
              <div class="mb-3">
                <label class="form-label tk-form-label">Số điện thoại / Zalo <span class="text-danger">*</span></label>
                <input type="tel" v-model="addressForm.so_dien_thoai" class="form-control" placeholder="Nhập SĐT / Zalo..." required>
              </div>
              <div class="mb-3">
                <label class="form-label tk-form-label">Địa chỉ nhận hàng / Chành xe <span class="text-danger">*</span></label>
                <textarea v-model="addressForm.dia_chi_cu_the" class="form-control" rows="3" placeholder="Số nhà, đường, tỉnh/thành phố hoặc tên bến chành xe..." required></textarea>
              </div>
              <div class="mb-3 tk-modal-check-row">
                <input type="checkbox" class="form-check-input tk-modal-checkbox" id="defaultAddress" v-model="addressForm.la_mac_dinh">
                <label class="form-check-label fw-medium text-secondary cursor-pointer mb-0" style="font-size: 13.5px;" for="defaultAddress">Đặt làm địa chỉ nhận hàng mặc định</label>
              </div>
              <div class="d-flex justify-content-end gap-2 pt-3 mt-4 border-top">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger px-4" :disabled="isSavingAddress">
                  <span v-if="isSavingAddress" class="spinner-border spinner-border-sm me-2"></span>
                  Lưu địa chỉ
                </button>
              </div>
            </form>
          </div>
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
  onError: (error) => { 
    if (error.response?.status === 422) { 
      Object.values(error.response.data.errors).forEach(err => toaster.error(err[0])); 
    } else { 
      toaster.error("Có lỗi xảy ra khi lưu địa chỉ!"); 
    } 
  },
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
  mutationFn: (id) => axiosClient.put(`/client/profile/addresses/${id}/set-default`),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ['clientAddresses'] });
    const previousAddresses = queryClient.getQueryData(['clientAddresses']);
    
    queryClient.setQueryData(['clientAddresses'], (old) => {
      if (!Array.isArray(old)) return old;
      return old.map(item => ({
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
.tk-heading {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 700;
  letter-spacing: -0.01em;
}

.tk-form-label {
  font-size: 0.8125rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.375rem;
  display: block;
}

.tk-form-input {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.form-control-plaintext {
  padding: 0.375rem 0;
  line-height: 1.5;
}

/* Card địa chỉ: Đồng bộ 100% kích thước & bo góc với Garage & VatTuThuongMua */
.address-item-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: var(--jpm-neutral-0, #FFFFFF);
  border: 1px solid var(--jpm-neutral-200, #E2E8F0) !important;
  box-shadow: none !important;
  padding: 10px 16px !important;
  transition: border-color 0.2s ease, background-color 0.2s ease;
}

.address-item-card:not(.address-item-default):hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: #cbd5e1 !important;
  background-color: #fafbfc;
}

/* Card mặc định: Viền đỏ Marine trang nhã, hover KHÔNG chớp nền, KHÔNG đổi màu */
.address-item-default,
.address-item-default:hover {
  border-color: rgba(248, 34, 73, 0.45) !important;
  background-color: rgba(248, 34, 73, 0.015) !important;
  transform: none !important;
  box-shadow: none !important;
  cursor: default;
}

.address-recipient-name {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 0.95rem;
  line-height: 1.35;
}

.address-divider {
  color: #cbd5e1;
  font-size: 0.75rem;
  user-select: none;
}

.address-recipient-phone {
  font-size: 0.85rem;
  color: #64748b;
  font-weight: 500;
}

.address-default-badge {
  background-color: #ffe2e5;
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
  font-size: 0.72rem;
  padding: 2px 8px;
  border-radius: var(--jpm-radius-badge, 6px);
  letter-spacing: 0.2px;
}

.address-text {
  font-size: 0.85rem;
  color: #475569;
  line-height: 1.4;
}

/* Nút Đặt mặc định: Chiều cao 36px, bo góc 8px đồng bộ với các nút thao tác */
.btn-set-default {
  height: 36px;
  padding: 0 12px;
  font-size: 0.8rem;
  font-weight: 500;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  color: #64748b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-set-default:hover {
  border-color: var(--jpm-color-navy-dark, #0A1128);
  color: var(--jpm-color-navy-dark, #0A1128);
  background-color: #f8fafc;
}

/* Hai nút Sửa và Xóa: 36x36px đồng bộ 100% với Garage và VatTuThuongMua */
.btn-action-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--jpm-radius-control, 8px);
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 0.95rem;
}

.btn-edit-icon {
  color: #475569;
}

.btn-edit-icon:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
  color: var(--jpm-color-navy-dark, #0A1128);
  transform: translateY(-1px);
}

.btn-delete-icon {
  color: #94a3b8;
}

.btn-delete-icon:hover {
  background-color: var(--jpm-color-red-marine, #F82249);
  border-color: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: none !important;
}

/* Empty State Wrap */
.empty-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background-color: rgba(248, 34, 73, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--jpm-color-red-marine, #F82249);
  font-size: 2rem;
}

.form-control-plaintext::placeholder {
  color: var(--jpm-color-navy-deep, #0A1128) !important;
  opacity: 0.4 !important;
  font-weight: 500 !important;
}

/* Standard Unified Action Buttons (Đồng bộ 100% kích thước 40px giữa các Card trên trang) */
.btn-action-primary {
  height: 40px !important;
  min-height: 40px !important;
  max-height: 40px !important;
  padding: 0 18px !important;
  background-color: var(--jpm-color-red-marine, #F82249) !important;
  border: 1px solid var(--jpm-color-red-marine, #F82249) !important;
  color: #ffffff !important;
  font-family: var(--heading-font, 'Raleway', 'Montserrat', sans-serif) !important;
  font-size: 0.875rem !important;
  font-weight: 600 !important;
  border-radius: var(--jpm-radius-control, 8px) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
  white-space: nowrap !important;
  box-sizing: border-box !important;
  transition: all 0.2s ease !important;
  line-height: 1 !important;
  text-decoration: none !important;
}

.btn-action-primary:hover:not(:disabled) {
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  border-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  color: #ffffff !important;
  box-shadow: none !important;
  transform: translateY(-1px) !important;
}

.btn-action-primary:active:not(:disabled) {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  border-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
}

.btn-action-primary:disabled {
  opacity: 0.65 !important;
  cursor: not-allowed !important;
}

.btn-action-secondary {
  height: 40px !important;
  min-height: 40px !important;
  max-height: 40px !important;
  padding: 0 18px !important;
  background-color: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  color: #475569 !important;
  font-family: var(--heading-font, 'Raleway', 'Montserrat', sans-serif) !important;
  font-size: 0.875rem !important;
  font-weight: 600 !important;
  border-radius: var(--jpm-radius-control, 8px) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
  white-space: nowrap !important;
  box-sizing: border-box !important;
  transition: all 0.2s ease !important;
  line-height: 1 !important;
  text-decoration: none !important;
}

.btn-action-secondary:hover {
  background-color: #f1f5f9 !important;
  border-color: #94a3b8 !important;
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  transform: translateY(-1px) !important;
}

.btn-action-primary i,
.btn-action-secondary i {
  font-size: 15px !important;
  line-height: 1 !important;
}

/* Standard Modal Action Buttons */
.btn-danger-action {
  height: 40px;
  padding: 0 22px;
  background-color: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  border: 1px solid var(--jpm-color-red-marine, #F82249);
  border-radius: var(--jpm-radius-control, 8px);
  font-size: 0.875rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-danger-action:hover:not(:disabled) {
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  border-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  color: #ffffff;
  transform: translateY(-1px) !important;
  box-shadow: none !important;
}

.btn-danger-action:active:not(:disabled) {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  border-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
}

.btn-danger-action:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.btn-secondary-action {
  height: 40px;
  padding: 0 18px;
  background-color: #ffffff;
  color: #475569;
  border: 1px solid #cbd5e1;
  border-radius: var(--jpm-radius-control, 8px);
  font-size: 0.875rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  cursor: pointer;
  box-shadow: none !important;
}

.btn-secondary-action:hover {
  background-color: #f1f5f9;
  border-color: #94a3b8;
  color: var(--jpm-color-navy-dark, #0A1128);
  transform: translateY(-1px);
}

.tk-modal-check-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-left: 0;
}

.tk-modal-checkbox {
  width: 17px;
  height: 17px;
  border-color: #cbd5e1;
  border-radius: var(--jpm-radius-badge, 6px);
  margin: 0 !important;
  flex-shrink: 0;
  cursor: pointer;
}

.tk-modal-checkbox:checked {
  background-color: var(--jpm-color-red-marine, #F82249);
  border-color: var(--jpm-color-red-marine, #F82249);
}

.tk-modal-checkbox:focus {
  border-color: var(--jpm-color-red-marine, #F82249);
  box-shadow: 0 0 0 2px rgba(248, 34, 73, 0.2);
}
</style>
