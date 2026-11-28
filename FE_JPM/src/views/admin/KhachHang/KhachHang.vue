<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Khách Hàng /</span> Danh Sách Khách Hàng
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Khách Hàng</h5>
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
              <td :colspan="canManageCustomer ? 9 : 8" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredCustomers.length === 0">
              <td :colspan="canManageCustomer ? 9 : 8" class="text-center py-5 text-muted">
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
              <td style="max-width: 260px;">
                <div class="d-flex align-items-center gap-2">
                  <span class="fw-semibold text-secondary text-truncate d-inline-block" style="max-width: 210px;" :title="item.address || 'Chưa cập nhật'">
                    {{ item.address || "Chưa cập nhật" }}
                  </span>
                  <button
                    v-if="(item.addresses_count || 0) > 1 && canEditCustomer"
                    type="button"
                    class="badge bg-pastel-info border-0 flex-shrink-0 cursor-pointer"
                    style="font-size: 0.72rem; padding: 0.25rem 0.5rem;"
                    @click="openEditModal(item, 'addresses')"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCustomer"
                    :title="`Xem tất cả ${item.addresses_count} địa chỉ giao nhận`"
                  >
                    +{{ item.addresses_count - 1 }} địa chỉ
                  </button>
                  <span
                    v-else-if="(item.addresses_count || 0) > 1"
                    class="badge bg-pastel-info flex-shrink-0"
                    style="font-size: 0.72rem; padding: 0.25rem 0.5rem;"
                  >
                    +{{ item.addresses_count - 1 }} địa chỉ
                  </span>
                </div>
              </td>
              <td class="text-center">
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
                    class="action-btn text-warning me-2"
                    @click="resetPassword(item.id)"
                    title="Đặt lại mật khẩu"
                  >
                    <i class="bx bx-key"></i>
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
            <div class="modal-body pb-2 pt-3">
              <!-- Tabs chuyển nhanh giữa Thông tin tài khoản & Sổ địa chỉ (khi Sửa khách hàng) -->
              <ul v-if="isEditMode" class="nav nav-pills mb-4 border-bottom pb-3 gap-2" role="tablist">
                <li class="nav-item">
                  <button
                    type="button"
                    class="nav-link px-3 py-2"
                    :class="{ active: modalTab === 'info' }"
                    @click="modalTab = 'info'"
                  >
                    <i class="bx bx-user me-1"></i> Thông tin tài khoản
                  </button>
                </li>
                <li class="nav-item">
                  <button
                    type="button"
                    class="nav-link px-3 py-2 d-inline-flex align-items-center gap-2"
                    :class="{ active: modalTab === 'addresses' }"
                    @click="modalTab = 'addresses'"
                  >
                    <i class="bx bx-map-pin"></i>
                    <span>Sổ địa chỉ giao nhận</span>
                    <span class="tab-count-badge">{{ customerAddresses.length }}</span>
                  </button>
                </li>
              </ul>

              <!-- TAB 1: THÔNG TIN TÀI KHOẢN -->
              <div v-show="!isEditMode || modalTab === 'info'" class="row">
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
                        :required="!isEditMode || modalTab === 'info'"
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
                        :required="!isEditMode || modalTab === 'info'"
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
                        :required="!isEditMode || modalTab === 'info'"
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
                          placeholder="Để trống để tự sinh ngẫu nhiên"
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
                        >Để trống hệ thống sẽ tự tạo mật khẩu ngẫu nhiên an toàn.</small
                      >
                      <div v-else-if="canLockCustomer">
                        <button
                          type="button"
                          class="btn btn-outline-warning w-100 shadow-sm"
                          @click="resetPassword(formData.id)"
                          style="font-size: 0.85rem"
                        >
                          <i class="bx bx-reset me-1"></i> Đặt lại mật khẩu mới
                        </button>
                      </div>
                      <div v-else>
                        <small class="text-muted">Cần quyền "Khóa Khách hàng" để đặt lại mật khẩu.</small>
                      </div>
                    </div>
                  </div>

                  <!-- Địa chỉ mặc định ban đầu khi Thêm Khách Hàng Mới -->
                  <div v-if="!isEditMode" class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark">
                        Địa Chỉ Giao Hàng Mặc Định <span class="text-muted fw-normal">(Tùy chọn)</span> <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        placeholder="VD: Xưởng tàu Marina, 128 Nguyễn Hữu Cảnh, TP.HCM"
                        v-model="formData.initial_address"
                      />
                    </div>
                  </div>
                </div>

                <!-- Cột phải: Hình ảnh & Trạng thái -->
                <div class="col-md-5 border-start ps-md-4">
                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label for="avatar" class="form-label fw-medium text-dark"
                        >Ảnh Đại Diện <span class="text-danger">*</span></label
                      >
                      <input
                        type="file"
                        id="avatar"
                        class="form-control mb-2"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
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

              <!-- TAB 2: SỔ ĐỊA CHỈ GIAO NHẬN (1-N CHUẨN DOANH NGHIỆP) -->
              <div v-if="isEditMode" v-show="modalTab === 'addresses'" class="pb-2">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                  <div>
                    <h6 class="mb-0 fw-bold text-dark">Danh sách địa chỉ giao hàng ({{ customerAddresses.length }})</h6>
                    <small class="text-muted">Mỗi khách hàng có thể lưu nhiều xưởng sửa chữa, bến tàu hoặc chành xe khác nhau.</small>
                  </div>
                  <button
                    v-if="!showAddressForm"
                    type="button"
                    class="btn btn-primary btn-sm px-3"
                    @click="openNewAddressForm"
                  >
                    <i class="bx bx-plus me-1"></i> Thêm địa chỉ
                  </button>
                </div>

                <!-- Form Thêm / Sửa Địa Chỉ Inline -->
                <div v-if="showAddressForm" class="card border bg-light-subtle mb-3" @keydown.enter.prevent="submitAddressForm">
                  <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                      <span class="fw-bold text-dark small">
                        {{ editingAddressId ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ giao hàng mới' }}
                      </span>
                      <button type="button" class="btn-close" @click="cancelAddressForm" aria-label="Close"></button>
                    </div>
                    <div class="row g-2">
                      <div class="col-md-6">
                        <label class="form-label small fw-semibold mb-1">Tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="addressForm.ten_nguoi_nhan" placeholder="Tên người nhận" />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label small fw-semibold mb-1">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="addressForm.so_dien_thoai" placeholder="Số điện thoại" />
                      </div>
                      <div class="col-12 mt-2">
                        <label class="form-label small fw-semibold mb-1">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="addressForm.dia_chi_cu_the" placeholder="Số nhà, đường, bến cảng, chành xe..." maxlength="255" />
                      </div>
                      <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2 pt-1">
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" id="adminDefaultAddr" v-model="addressForm.la_mac_dinh" />
                          <label class="form-check-label small fw-medium" for="adminDefaultAddr">Đặt làm địa chỉ mặc định</label>
                        </div>
                        <div class="d-flex gap-2">
                          <button type="button" class="btn btn-outline-secondary btn-sm px-3" @click="cancelAddressForm">Hủy</button>
                          <button type="button" class="btn btn-primary btn-sm px-3" :disabled="isSavingAddress" @click="submitAddressForm">
                            <span v-if="isSavingAddress" class="spinner-border spinner-border-sm me-1"></span>
                            {{ editingAddressId ? 'Lưu cập nhật' : 'Lưu địa chỉ' }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Danh sách địa chỉ hiện có -->
                <div v-if="customerAddresses.length === 0" class="text-center py-4 border rounded bg-light">
                  <i class="bx bx-map text-muted fs-2 d-block mb-1"></i>
                  <p class="text-muted small mb-0">Khách hàng chưa có địa chỉ nào trong sổ địa chỉ.</p>
                </div>

                <div v-else class="d-flex flex-column gap-2" style="max-height: 290px; overflow-y: auto;">
                  <div
                    v-for="addr in customerAddresses"
                    :key="addr.id"
                    class="border rounded p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 bg-white"
                  >
                    <div class="min-w-0">
                      <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <span class="fw-bold text-dark">{{ addr.ten_nguoi_nhan }}</span>
                        <span class="text-muted small">|</span>
                        <span class="text-secondary small fw-semibold">{{ addr.so_dien_thoai }}</span>
                        <span v-if="addr.la_mac_dinh" class="badge bg-pastel-danger" style="font-size: 0.7rem; padding: 0.22rem 0.5rem;">
                          Mặc định
                        </span>
                      </div>
                      <div class="text-secondary small">
                        <i class="bx bx-map-pin me-1"></i>{{ addr.dia_chi_cu_the }}
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                      <button
                        v-if="!addr.la_mac_dinh"
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        @click="setDefaultAddress(addr.id)"
                        title="Đặt làm mặc định"
                      >
                        Mặc định
                      </button>
                      <button
                        type="button"
                        class="action-btn text-primary"
                        @click="openEditAddressForm(addr)"
                        title="Sửa địa chỉ"
                      >
                        <i class="bx bx-edit-alt"></i>
                      </button>
                      <button
                        type="button"
                        class="action-btn text-danger"
                        @click="deleteCustomerAddress(addr.id)"
                        title="Xóa địa chỉ"
                      >
                        <i class="bx bx-trash"></i>
                      </button>
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
                {{ isEditMode && modalTab === 'addresses' ? 'Đóng' : 'Hủy' }}
              </button>
              <button
                v-if="!isEditMode || modalTab === 'info'"
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
import { compressAvatar, formatBytes } from "../../../utils/imageOptimizer";

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
        const response = await axiosClient.get("/admin/khach-hang?all=true");
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
      modalTab: "info",
      showPassword: false,
      avatarFile: null,
      avatarPreview: null,
      customerAddresses: [],
      showAddressForm: false,
      isSavingAddress: false,
      editingAddressId: null,
      addressForm: {
        ten_nguoi_nhan: "",
        so_dien_thoai: "",
        dia_chi_cu_the: "",
        la_mac_dinh: false,
      },
      formData: {
        id: null,
        user_code: "",
        avatar: "",
        name: "",
        email: "",
        phone: "",
        address: "",
        initial_address: "",
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
              String(m.user_code || '').toLowerCase().includes(s) ||
              String(m.address || '').toLowerCase().includes(s) ||
              (Array.isArray(m.addresses) && m.addresses.some((a) => String(a.dia_chi_cu_the || '').toLowerCase().includes(s))));
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
      this.modalTab = "info";
      this.resetForm();
    },
    openEditModal(item, tab = "info") {
      this.isEditMode = true;
      this.resetForm();
      this.modalTab = tab;
      this.formData = { ...item, initial_address: "" };
      this.customerAddresses = Array.isArray(item.addresses) ? [...item.addresses] : [];
      this.formData.password = ""; // Clear password field
      if (this.formData.status === true) this.formData.status = 1;
      else if (this.formData.status === false) this.formData.status = 0;
    },
    openNewAddressForm() {
      this.editingAddressId = null;
      this.addressForm = {
        ten_nguoi_nhan: this.formData.name || "",
        so_dien_thoai: this.formData.phone || "",
        dia_chi_cu_the: "",
        la_mac_dinh: this.customerAddresses.length === 0,
      };
      this.showAddressForm = true;
    },
    openEditAddressForm(addr) {
      this.editingAddressId = addr.id;
      this.addressForm = {
        ten_nguoi_nhan: addr.ten_nguoi_nhan || "",
        so_dien_thoai: addr.so_dien_thoai || "",
        dia_chi_cu_the: addr.dia_chi_cu_the || "",
        la_mac_dinh: !!addr.la_mac_dinh,
      };
      this.showAddressForm = true;
    },
    cancelAddressForm() {
      this.showAddressForm = false;
      this.editingAddressId = null;
    },
    syncUpdatedCustomer(res) {
      const updated = res?.data?.data || res?.data || res;
      if (updated && Array.isArray(updated.addresses)) {
        this.customerAddresses = [...updated.addresses];
        this.formData.address = updated.address || "";
      }
      this.queryClient.invalidateQueries({ queryKey: ["khach-hang"] });
    },
    async submitAddressForm() {
      if (!this.addressForm.ten_nguoi_nhan || !this.addressForm.so_dien_thoai || !this.addressForm.dia_chi_cu_the) {
        if (this.$toast) this.$toast.error("Vui lòng nhập đầy đủ tên người nhận, số điện thoại và địa chỉ!");
        return;
      }
      this.isSavingAddress = true;
      try {
        let res;
        if (this.editingAddressId) {
          res = await axiosClient.put(`/admin/khach-hang/${this.formData.id}/dia-chi/${this.editingAddressId}`, this.addressForm);
        } else {
          res = await axiosClient.post(`/admin/khach-hang/${this.formData.id}/dia-chi`, this.addressForm);
        }
        this.syncUpdatedCustomer(res);
        this.cancelAddressForm();
        if (this.$toast) this.$toast.success(res?.data?.message || res?.message || "Đã lưu địa chỉ giao hàng!");
      } catch (err) {
        if (this.$toast) this.$toast.error(err.response?.data?.message || "Không thể lưu địa chỉ lúc này!");
      } finally {
        this.isSavingAddress = false;
      }
    },
    async setDefaultAddress(addressId) {
      try {
        const res = await axiosClient.put(`/admin/khach-hang/${this.formData.id}/dia-chi/${addressId}/mac-dinh`);
        this.syncUpdatedCustomer(res);
        if (this.$toast) this.$toast.success("Đã đặt làm địa chỉ mặc định!");
      } catch (err) {
        if (this.$toast) this.$toast.error(err.response?.data?.message || "Không thể cập nhật mặc định!");
      }
    },
    async deleteCustomerAddress(addressId) {
      const result = await Swal.fire({
        title: "Xóa địa chỉ này?",
        text: "Các đơn hàng trước đây vẫn giữ nguyên địa chỉ đã đặt.",
        icon: "warning",
        iconColor: "#dc3545",
        showCancelButton: true,
        confirmButtonText: "Xóa địa chỉ",
        cancelButtonText: "Hủy",
        reverseButtons: true,
        animation: false,
        customClass: {
          confirmButton: "btn btn-danger px-4 shadow-sm",
          cancelButton: "btn btn-outline-secondary px-4 me-3",
        },
        buttonsStyling: false,
      });
      if (!result.isConfirmed) return;
      try {
        const res = await axiosClient.delete(`/admin/khach-hang/${this.formData.id}/dia-chi/${addressId}`);
        this.syncUpdatedCustomer(res);
        if (this.$toast) this.$toast.success("Đã xóa địa chỉ!");
      } catch (err) {
        if (this.$toast) this.$toast.error(err.response?.data?.message || "Không thể xóa địa chỉ!");
      }
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

      if (!this.isEditMode && this.formData.initial_address) {
        payload.append("initial_address", this.formData.initial_address.trim());
      }

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
          onSuccess: (res) => {
            const tempPw = res?.temporary_password || res?.data?.temporary_password;
            if (!this.isEditMode && tempPw) {
              Swal.fire({
                icon: "success",
                title: "Đã tạo khách hàng mới!",
                html: `Mật khẩu tạm thời: <code class="fs-5 fw-bold text-primary">${tempPw}</code><br><small class="text-muted">Vui lòng sao chép gửi cho khách hàng.</small>`,
                confirmButtonText: "Đã lưu",
              });
            } else if (this.$toast) {
              this.$toast.success(
                this.isEditMode ? "Cập nhật thành công!" : "Đã thêm mới!",
              );
            }
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
        text: "Hệ thống sẽ tạo mật khẩu tạm thời mới ngẫu nhiên và đăng xuất khách hàng khỏi thiết bị hiện tại.",
        icon: 'warning',
          iconColor: '#dc3545',
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
          const tempPw = res?.temporary_password || res?.data?.temporary_password;
          if (tempPw) {
            Swal.fire({
              icon: "success",
              title: "Đã đặt lại mật khẩu!",
              html: `Mật khẩu tạm thời mới: <code class="fs-5 fw-bold text-primary">${tempPw}</code><br><small class="text-muted">Vui lòng sao chép và gửi cho khách hàng.</small>`,
              confirmButtonText: "Đóng",
            });
          } else if (this.$toast) {
            this.$toast.success(
              res.data?.message ||
                res.message ||
                "Đã reset mật khẩu thành công!",
            );
          }
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
          iconColor: '#dc3545',
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
      this.modalTab = "info";
      this.customerAddresses = [];
      this.showAddressForm = false;
      this.editingAddressId = null;
      this.formData = {
        id: null,
        user_code: "",
        avatar: "",
        name: "",
        email: "",
        phone: "",
        address: "",
        initial_address: "",
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
    async handleAvatarUpload(event) {
      const file = event.target.files[0];
      if (file) {
        try {
          const { file: optimizedFile, previewUrl } = await compressAvatar(file);
          this.avatarFile = optimizedFile;
          this.avatarPreview = previewUrl;
        } catch (err) {
          console.warn('Lỗi nén avatar khách hàng, dùng ảnh gốc:', err);
          this.avatarFile = file;
          this.avatarPreview = URL.createObjectURL(file);
        }
      }
    },
  },
};
</script>

<style scoped></style>




