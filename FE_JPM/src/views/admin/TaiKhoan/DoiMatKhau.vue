<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tài khoản /</span> Đổi mật khẩu</h4>

    <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-md-row mb-4">
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/ho-so"><i class="bx bx-user me-1"></i> Hồ sơ</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link active" to="/admin/doi-mat-khau"><i class="bx bx-lock-alt me-1"></i> Đổi mật khẩu</router-link>
          </li>
        </ul>
        <div class="card mb-4">
          <h5 class="card-header border-bottom py-3 fw-bold">Đổi mật khẩu</h5>
          <div class="card-body pt-4">
            <form id="formAccountSettings" method="POST" @submit.prevent="changePassword">
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="currentPassword">Mật khẩu hiện tại</label>
                  <div class="input-group input-group-merge">
                    <input 
                      class="form-control" 
                      :type="showCurrent ? 'text' : 'password'" 
                      name="currentPassword" 
                      id="currentPassword" 
                      v-model="passwords.current" 
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                      required
                    />
                    <span class="input-group-text cursor-pointer" @click="showCurrent = !showCurrent">
                      <i class="bx" :class="showCurrent ? 'bx-show' : 'bx-hide'"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="newPassword">Mật khẩu mới</label>
                  <div class="input-group input-group-merge">
                    <input 
                      class="form-control" 
                      :type="showNew ? 'text' : 'password'" 
                      id="newPassword" 
                      name="newPassword" 
                      v-model="passwords.new" 
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                      required
                      minlength="8" maxlength="25"
                    />
                    <span class="input-group-text cursor-pointer" @click="showNew = !showNew">
                      <i class="bx" :class="showNew ? 'bx-show' : 'bx-hide'"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="confirmPassword">Xác nhận mật khẩu mới</label>
                  <div class="input-group input-group-merge">
                    <input 
                      class="form-control" 
                      :type="showConfirm ? 'text' : 'password'" 
                      name="confirmPassword" 
                      id="confirmPassword" 
                      v-model="passwords.confirm" 
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                      required
                      minlength="8" maxlength="25"
                    />
                    <span class="input-group-text cursor-pointer" @click="showConfirm = !showConfirm">
                      <i class="bx" :class="showConfirm ? 'bx-show' : 'bx-hide'"></i>
                    </span>
                  </div>
                </div>
                <div class="col-12 mb-4">
                  <p class="fw-semibold mt-2 text-dark">Yêu cầu mật khẩu:</p>
                  <ul class="ps-3 mb-0 text-muted">
                    <li class="mb-1">Tối thiểu 12 ký tự</li>
                    <li class="mb-1">Ít nhất một chữ cái viết thường</li>
                    <li>Ít nhất một chữ số hoặc ký tự đặc biệt</li>
                  </ul>
                </div>
                <div class="col-12 mt-1">
                  <button type="submit" class="btn btn-primary me-2 px-4 shadow-sm" :disabled="isSubmitting">
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Lưu thay đổi
                  </button>
                  <button type="button" class="btn btn-outline-secondary px-4" @click="resetForm" :disabled="isSubmitting">Hủy</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from "@meforma/vue-toaster";
import { useMutation } from '@tanstack/vue-query';

const toaster = createToaster({ position: "top-right", duration: 3000 });

// Toggle password visibility states
const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);

const passwords = ref({
  current: "",
  new: "",
  confirm: ""
});

const resetForm = () => {
  passwords.value = { current: "", new: "", confirm: "" };
  showCurrent.value = false;
  showNew.value = false;
  showConfirm.value = false;
};

const changePasswordMutation = useMutation({
  mutationFn: (payload) => axiosClient.post('/change-password', payload),
  onSuccess: (response) => {
    if (response.success) {
      toaster.success("Đổi mật khẩu thành công!");
      resetForm();
    }
  },
  onError: (error) => {
    const errorMsg = error.response?.data?.message || "Đổi mật khẩu thất bại!";
    toaster.error(errorMsg);
  }
});

const { isPending: isSubmitting } = changePasswordMutation;

const changePassword = () => {
  if (passwords.value.new !== passwords.value.confirm) {
    toaster.error("Mật khẩu mới không khớp!");
    return;
  }
  
  if (passwords.value.new.length < 12) {
    toaster.error("Mật khẩu mới phải dài ít nhất 12 ký tự!");
    return;
  }

  const passwordRegex = /^(?=.*[a-z])(?=.*[\d\W]).+$/;
  if (!passwordRegex.test(passwords.value.new)) {
    toaster.error("Mật khẩu phải chứa ít nhất 1 chữ cái viết thường và 1 chữ số/ký tự đặc biệt!");
    return;
  }

  changePasswordMutation.mutate({
    current_password: passwords.value.current,
    new_password: passwords.value.new,
    new_password_confirmation: passwords.value.confirm
  });
};
</script>

<style scoped></style>








