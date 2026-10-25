<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Khách Hàng /</span> Danh Sách Khách Hàng
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh sách Khách Hàng</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input
              type="text"
              class="form-control"
              placeholder="Tìm tên, email, sđt..."
              v-model="filters.search"
            />
          </div>
          <div style="width: 20%">
            <v-select
              v-model="filters.status"
              :options="[
                { value: null, label: 'Tất cả Trạng thái' },
                ...statusOptions,
              ]"
              placeholder="Trạng thái"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <button
            class="btn btn-secondary px-3 text-nowrap"
            @click="resetFilters"
            title="Đặt lại bộ lọc"
          >
            Đặt lại
          </button>
          <button
            v-if="canAddCustomer"
            class="btn btn-primary px-4 shadow-sm text-nowrap"
            data-bs-toggle="modal"
            data-bs-target="#modalCustomer"
            @click="openAddModal"
          >
            <i class="bx bx-plus me-1"></i> Thêm Mới
          </button>
        </div>
      </div>

      <div
        class="table-responsive text-nowrap custom-scroll"
        style="
          max-height: calc(100vh - 260px);
          min-height: 400px;
          overflow-y: auto;
        "
      >
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th class="text-center" style="width: 5%">Avatar</th>
              <th style="width: 10%">Mã KH</th>
              <th
                :style="{ width: canManageCustomer ? '20%' : '30%' }"
              >
                Khách Hàng
              </th>
              <th style="width: 15%">Số điện thoại</th>
              <th style="width: 15%">Email</th>
              <th
                :style="{ width: canManageCustomer ? '10%' : '20%' }"
              >
                Địa Chỉ
              </th>
              <th
                class="text-center"
                style="width: 10%"
                v-if="canManageCustomer"
              >
                Trạng thái
              </th>
              <th
                class="text-center"
                style="width: 10%"
                v-if="canManageCustomer"
              >
                Hành động
              </th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isFetching && filteredCustomers.length === 0">
              <td :colspan="canManageCustomer ? 9 : 7" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredCustomers.length === 0">
              <td :colspan="canManageCustomer ? 9 : 7" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr
              v-for="(item, index) in filteredCustomers"
              :key="item.id || index"
            >
              <td class="text-center">
                <strong>{{ index + 1 }}</strong>
              </td>
              <td class="text-center">
                <div
                  class="d-inline-flex justify-content-center align-items-center border rounded bg-white"
                  style="width: 60px; height: 60px"
                >
                  <img
                    v-if="item.avatar"
                    :src="item.avatar"
                    alt="Avatar"
                    style="
                      max-height: 40px;
                      max-width: 50px;
                      object-fit: contain;
                    "
                  />
                          
                  <i v-else class="bx bx-image text-muted fs-3"></i>
                </div>
              </td>
              <td>
                <span class="fw-bold text-primary">{{ item.user_code }}</span>
              </td>
              <td class="fw-bold text-dark">{{ item.name }}</td>
              <td>{{ item.phone }}</td>
              <td class="fw-semibold text-secondary">{{ item.email }}</td>
              <td class="fw-semibold text-secondary">{{ item.address || "N/A" }}</td>
              <td class="text-center" v-if="canManageCustomer">
                <span
                  class="badge badge-custom"
                  :class="
                    item.status === 1 ? 'bg-pastel-success' : 'bg-pastel-danger'
                  "
                >
                  {{ item.status === 1 ? "HOẠT ĐỘNG" : "ĐÃ KHÓA" }}
                </span>
              </td>
              <td class="text-center" v-if="canManageCustomer">
                <div class="d-flex justify-content-center">
                  <button
                    v-if="canEditCustomer"
                    type="button"
                    class="action-btn text-primary me-2"
                    @click="openEditModal(item)"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCustomer"
                    title="Chỉnh sửa"
                  >
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button
                    v-if="canLockCustomer"
                    type="button"
                    class="action-btn text-danger"
                    @click="confirmDelete(item)"
                    title="Khóa/Xóa"
                  >
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredCustomers && filteredCustomers.length) ? filteredCustomers.length : 0 }} mục</span>
      </div>
    </div>

    <!-- Modal Form -->
    <div
      class="modal fade"
      id="modalCustomer"
      tabindex="-1"
      aria-hidden="true"
      ref="modalCustomer"
    >
      <div class="modal-dialog modal-xl mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4
              class="modal-title fw-bold text-dark mb-0 fs-4"
              id="modalCustomerTitle"
            >
              {{ isEditMode ? "Cập Nhật Khách Hàng" : "Thêm Khách Hàng Mới" }}
            </h4>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pb-2 pt-4">
              <div class="row">
                <!-- Cột trái: Thông tin cơ bản -->
                <div class="col-md-7 pe-md-4">
                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Tên Khách Hàng
                        <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        placeholder="VD: Nguyễn Văn A"
                        v-model="formData.name"
                        required
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Email
                        <span class="text-danger">*</span></label
                      >
                      <input
                        type="email"
                        class="form-control"
                        
                        placeholder="VD: a@gmail.com"
                        v-model="formData.email"
                        required
                        
                      />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Số Điện Thoại
                        <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        
                        placeholder="VD: 0901234567"
                        v-model="formData.phone"
                        required
                        
                        pattern="^[0-9]{9,15}$"
                        title="Số điện thoại phải từ 9-15 chữ số"
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Mật Khẩu
                        <span class="text-danger">*</span></label
                      >
                      <div
                        v-if="!isEditMode"
                        class="input-group input-group-merge"
                      >
                        <input
                          :type="showPassword ? 'text' : 'password'"
                          class="form-control"
                          placeholder="Mặc định: Jpm@2026"
                          v-model="formData.password"
                          minlength="8"
                          maxlength="25"
                        />
                        <span
                          class="input-group-text cursor-pointer"
                          @click="showPassword = !showPassword"
                        >
                          <i
                            class="bx"
                            :class="showPassword ? 'bx-show' : 'bx-hide'"
                          ></i>
                        </span>
                      </div>
                      <small
                        v-if="!isEditMode"
                        class="form-text text-muted mt-1"
                        >Mặc định: <strong>Jpm@2026</strong></small
                      >
                      <div v-else>
                        <button
                          type="button"
                          class="btn btn-outline-warning w-100 shadow-sm"
                          @click="resetPassword(formData.id)"
                          style="font-size: 0.85rem"
                        >
                          <i class="bx bx-reset me-1"></i> Đặt lại về Jpm@2026
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cột phải: Hình ảnh & Trạng thái -->
                <div class="col-md-5 border-start ps-md-4">
                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label for="avatar" class="form-label fw-medium text-dark"
                        >Ảnh Đại Diện</label
                      >
                      <input
                        type="file"
                        id="avatar"
                        class="form-control mb-2"
                        accept="image/*"
                        @change="handleAvatarUpload"
                      />
                      <div
                        class="d-flex justify-content-center align-items-center border rounded bg-light mb-3 position-relative"
                        style="height: 180px"
                      >
                        <img
                          v-if="avatarPreview || formData.avatar"
                          :src="avatarPreview || formData.avatar"
                          style="
                            max-height: 160px;
                            max-width: 100%;
                            object-fit: contain;
                          "
                        />
                          <button v-if="avatarPreview || formData.avatar" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeAvatar" style="width: 28px; height: 28px; padding: 0;" title="Xóa ảnh"><i class="bx bx-trash"></i></button>
                        <span class="text-muted" v-else
                          ><i class="bx bx-image fs-1"></i
                        ></span>
                      </div>
                    </div>
                  </div>

                  <div class="row mt-3">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Trạng Thái <span class="text-danger">*</span></label
                      >
                      <v-select
                        v-model="formData.status"
                        :options="statusOptions"
                        :can-clear="false"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer border-top py-3 mt-2 bg-light">
              <button
                type="button"
                class="btn btn-outline-secondary px-4"
                data-bs-dismiss="modal"
              >
                Hủy
              </button>
              <button
                type="submit"
                class="btn btn-primary px-4 shadow-sm"
                :disabled="isSubmitting"
              >
                <span
                  v-if="isSubmitting"
                  class="spinner-border spinner-border-sm me-1"
                  role="status"
                  aria-hidden="true"
                ></span>
                {{ isEditMode ? "Cập Nhật" : "Thêm Mới" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import { computed } from "vue";
import { useAuthStore } from "../../../stores/authStore";
import vSelect from "@vueform/multiselect";
import Swal from "sweetalert2";
import axiosClient from "../../../api/axiosClient";

export default {
  name: "KhachHang",
  components: {
    vSelect,
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();

    // Fetch danh sách
    const { data: customers, isFetching } = useQuery({
      queryKey: ["khach-hang"],
      queryFn: async () => {
        const response = await axiosClient.get("/admin/khach-hang");
        if (
          response.data &&
          response.data.data &&
          Array.isArray(response.data.data)
        )
          return response.data.data;
        if (response.data && Array.isArray(response.data)) return response.data;
        if (Array.isArray(response)) return response;
        return [];
      },
      staleTime: 5 * 60 * 1000,
      enabled: computed(() => !authStore.loading),
    });

    // Optimistic DELETE mutation
    const deleteMutation = useMutation({
      mutationFn: (id) => axiosClient.delete(`/admin/khach-hang/${id}`),
      onError: (err, id, context) => {
      },
      onSettled: () =>
        queryClient.invalidateQueries({ queryKey: ["khach-hang"] }),
    });

    // Optimistic SAVE mutation
    const saveMutation = useMutation({
      mutationFn: async ({ id, payload, isEditMode }) => {
        if (isEditMode) {
          return await axiosClient.post(
            `/admin/khach-hang/${id}?_method=PUT`,
            payload,
            {
              headers: { "Content-Type": "multipart/form-data" },
            },
          );
        } else {
          return await axiosClient.post("/admin/khach-hang", payload, {
            headers: { "Content-Type": "multipart/form-data" },
          });
        }
      },
      onError: (err, variables, context) => {
      },
      onSettled: () =>
        queryClient.invalidateQueries({ queryKey: ["khach-hang"] }),
    });

    return { authStore, customers, isFetching, queryClient, deleteMutation, saveMutation };
  },
  data() {
    return {
      currentUserRole:
        JSON.parse(
          localStorage.getItem("auth_user") || "{}",
        ).roles?.[0]?.toUpperCase() || "ADMIN",
      filters: {
        search: "",
        status: null,
      },
      isSubmitting: false,
      isEditMode: false,
      showPassword: false,
      avatarFile: null,
      avatarPreview: null,
      formData: {
        id: null,
        user_code: "",
        avatar: "",
        name: "",
        email: "",
        phone: "",
        address: "",
        password: "",
        status: 1,
      },
      statusOptions: [
        { value: 1, label: "Hoạt Động" },
        { value: 0, label: "Đã Khóa" },
      ],
    };
  },
  computed: {
    canAddCustomer() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Thêm Khách hàng');
    },
    canEditCustomer() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Sửa Khách hàng');
    },
    canLockCustomer() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Khóa Khách hàng');
    },
    canManageCustomer() {
      return this.canEditCustomer || this.canLockCustomer;
    },
    filteredCustomers() {
      const list = Array.isArray(this.customers) ? this.customers : [];
      return list.filter((m) => {
        let match = true;
        if (this.filters.search) {
          const s = this.filters.search.toLowerCase().trim();
          match =
            match &&
            (String(m.name || '').toLowerCase().includes(s) ||
              String(m.email || '').toLowerCase().includes(s) ||
              String(m.phone || '').includes(s) ||
              String(m.user_code || '').toLowerCase().includes(s));
        }
        if (this.filters.status !== null && this.filters.status !== undefined && this.filters.status !== '') {
          match = match && Number(m.status) === Number(this.filters.status);
        }
        return match;
      });
    },
  },
  methods: {
    resetFilters() {
      this.filters = {
        search: "",
        status: null,
      };
    },
    openAddModal() {
      this.isEditMode = false;
      this.resetForm();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.resetForm();
      this.formData = { ...item };
      this.formData.password = ""; // Clear password field
      if (this.formData.status === true) this.formData.status = 1;
      else if (this.formData.status === false) this.formData.status = 0;
    },
    async saveData() {
      if (!this.formData.name || !this.formData.email || !this.formData.phone) {
        if (this.$toast)
          this.$toast.error("Vui lòng nhập đầy đủ các thông tin bắt buộc!");
        return;
      }

      this.isSubmitting = true;

      const payload = new FormData();
      payload.append("name", this.formData.name);
      payload.append("email", this.formData.email);
      payload.append("phone", this.formData.phone);
      payload.append("status", this.formData.status);

      if (this.formData.password) {
        payload.append("password", this.formData.password);
      }

      if (this.avatarFile) {
        payload.append("avatar", this.avatarFile);
      } else if (this.isEditMode && this.avatarFile === null && !this.formData.avatar) {
        payload.append("remove_avatar", "1");
        payload.append("avatar", "");
      }

      this.saveMutation.mutate(
        {
          id: this.formData.id,
          payload,
          isEditMode: this.isEditMode,
          rawData: {
            ...this.formData,
            avatar: this.avatarPreview || this.formData.avatar,
          },
        },
        {
          onSuccess: () => {
            if (this.$toast)
              this.$toast.success(
                this.isEditMode ? "Cập nhật thành công!" : "Đã thêm mới!",
              );
            this.closeModal();
          },
          onError: (err) => {
            if (this.$toast) {
              const msg =
                err.response?.data?.message ||
                err.response?.data?.errors?.[
                  Object.keys(err.response.data.errors)[0]
                ]?.[0] ||
                "Đã có lỗi xảy ra!";
              this.$toast.error(msg);
            }
          },
          onSettled: () => {
            this.isSubmitting = false;
          },
        },
      );
    },

    async resetPassword(id) {
      const result = await Swal.fire({
        title: "Xác nhận Reset Mật Khẩu",
        text: "Mật khẩu của khách hàng này sẽ được đặt lại thành Jpm@2026.",
        icon: 'warning',
          iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: "Xác nhận Reset",
        cancelButtonText: "Hủy",
        reverseButtons: true,
        animation: false,
        customClass: {
          confirmButton: "btn btn-warning px-4 shadow-sm",
          cancelButton: "btn btn-outline-secondary px-4 me-3",
        },
        buttonsStyling: false,
      });

      if (result.isConfirmed) {
        try {
          const res = await axiosClient.post(
            `/admin/khach-hang/${id}/reset-password`,
          );
          if (this.$toast)
            this.$toast.success(
              res.data?.message ||
                res.message ||
                "Đã reset mật khẩu thành công!",
            );
        } catch (error) {
          if (this.$toast)
            this.$toast.error(
              error.response?.data?.message || "Đã có lỗi xảy ra!",
            );
        }
      }
    },

    async confirmDelete(item) {
      const result = await Swal.fire({
        title: "Xác nhận xóa tài khoản",
        html: `Bạn có chắc chắn muốn XÓA tài khoản khách hàng <b>"${item.name}"</b>?<br><br><small class="text-danger">CẢNH BÁO: Dữ liệu liên kết có thể bị ảnh hưởng.</small>` ,
        icon: 'warning',
          iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: "XÓA tài khoản",
        cancelButtonText: "Hủy",
        reverseButtons: true,
        animation: false,
        customClass: {
          confirmButton: "btn btn-danger px-4 shadow-sm",
          cancelButton: "btn btn-outline-secondary px-4 me-3",
        },
        buttonsStyling: false,
      });

      if (result.isConfirmed) {
        this.deleteMutation.mutate(item.id, {
          onSuccess: () => {
            if (this.$toast) this.$toast.success("Đã XÓA tài khoản!");
          },
          onError: (err) => {
            if (this.$toast)
              this.$toast.error(
                err.response?.data?.message || "Đã có lỗi xảy ra!",
              );
          },
        });
      }
    },
    closeModal() {
      const modalEl = document.getElementById("modalCustomer");
      if (modalEl) {
        // eslint-disable-next-line no-undef
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      }
      const closeBtn = document.querySelector("#modalCustomer .btn-close");
      if (closeBtn) closeBtn.click();
    },
    resetForm() {
      this.showPassword = false;
      this.formData = {
        id: null,
        user_code: "",
        avatar: "",
        name: "",
        email: "",
        phone: "",
        address: "",
        password: "",
        status: 1,
      };
      this.avatarFile = null;
      this.avatarPreview = null;
      const fileInput = document.getElementById("avatar");
      if (fileInput) fileInput.value = "";
    },
    removeAvatar() {
      this.avatarPreview = null;
      this.formData.avatar = null;
      this.avatarFile = null;
      const fileInput = document.getElementById('avatar');
      if (fileInput) fileInput.value = '';
    },
    handleAvatarUpload(event) {
      const file = event.target.files[0];
      if (file) {
        if (file.size > 5 * 1024 * 1024) {
          if (this.$toast) this.$toast.error("Ảnh quá lớn! Tối đa 5MB.");
          event.target.value = "";
          return;
        }
        this.avatarFile = file;
        this.avatarPreview = URL.createObjectURL(file);
      }
    },
  },
};
</script>

<style scoped></style>




