<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ Thống /</span> Phân Quyền
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Vai Trò (Roles)</h5>
      </div>
      
      <!-- Bộ lọc (Filters) -->
        <div class="card-body pt-3 pb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="flex-grow-1">
              <input type="text" class="form-control" placeholder="Tìm tên vai trò..." v-model="filters.search">
            </div>
            <div style="width: 20%;">
              <v-select
                v-model="filters.status"
                :options="[{value: null, label: 'Tất cả Trạng thái'}, ...statusOptions]"
                placeholder="Trạng thái"
                :searchable="true"
                :can-clear="false"
              />
            </div>
            <button class="btn btn-secondary px-3 text-nowrap" @click="resetFilters" title="Đặt lại bộ lọc">
              Đặt lại
            </button>
            <div>
              <button class="btn btn-primary px-4 shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalRole" @click="openAddModal">
                <i class="bx bx-plus me-1"></i> Thêm Mới
              </button>
            </div>
          </div>
        </div>
        
        <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th style="width: 25%">Tên Vai Trò</th>
              <th style="width: 35%">Mô tả</th>
              <th class="text-center" style="width: 10%">Số Người Dùng</th>
              <th class="text-center" style="width: 15%">Trạng thái</th>
              <th class="text-center" style="width: 10%">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isPending">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted mt-2">Đang tải dữ liệu...</div>
              </td>
            </tr>
            <tr v-else-if="filteredRoles.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-else v-for="(item, index) in filteredRoles" :key="item.id">
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <span 
                  class="fw-bold text-capitalize"
                  :class="isSystemRoleItem(item) ? 'text-primary' : 'text-dark'"
                >
                  {{ item.name }}
                </span>
              </td>
              <td><span :class="!item.description ? 'text-muted' : 'text-secondary'">{{ item.description || "N/A" }}</span></td>
              <td class="text-center">
                <span class="fw-semibold text-secondary">{{ item.user_count }}</span>
              </td>
              <td class="text-center">
                <span v-if="item.status === 1 || item.status === true" class="badge badge-custom text-uppercase bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge badge-custom text-uppercase bg-pastel-danger">ĐÃ KHÓA</span>
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button 
                    v-if="!isSystemRoleItem(item) || authStore.isAdmin"
                    type="button"
                    class="action-btn text-primary me-1" 
                    @click="openEditModal(item)" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalRole" 
                    title="Chỉnh sửa"
                  >
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button 
                    v-if="!isSystemRoleItem(item)"
                    type="button"
                    class="action-btn text-danger" 
                    @click="confirmDelete(item)" 
                    title="Xóa"
                  >
                    <i class="bx bx-trash"></i>
                  </button>
                  <span v-else class="text-muted ms-1" title="Vai trò hệ thống được bảo vệ">
                    <i class="bx bx-lock-alt text-secondary" style="font-size: 1.1rem;"></i>
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredRoles && filteredRoles.length) ? filteredRoles.length : 0 }} mục</span>
      </div>
    </div>

    <!-- Modal Form (2 Columns) -->
    <div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true" ref="modalRole">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4" id="modalRoleTitle">
              {{ isEditMode ? 'Cập Nhật Vai Trò' : 'Thêm Vai Trò Mới' }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body py-3">
              <div class="row g-4">
                <!-- Cột Trái: Thông tin vai trò -->
                <div class="col-lg-4 role-left-col">
                  <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Tên Vai Trò <span class="text-danger">*</span></label>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="VD: Quản lý Kho"
                      v-model="formData.name"
                      :disabled="isEditMode && isSystemRoleItem(formData)"
                      required
                    />
                    <small v-if="isEditMode && isSystemRoleItem(formData)" class="text-primary mt-1 d-block fw-semibold">
                      <i class="bx bx-shield-quarter me-1"></i> Vai trò Quản trị viên hệ thống (Super Admin).
                    </small>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.status"
                      :options="statusOptions"
                      :can-clear="false"
                      :disabled="isEditMode && isSystemRoleItem(formData)"
                    />
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Mô tả <span class="text-danger">*</span></label>
                    <textarea
                      rows="4"
                      class="form-control"
                      placeholder="Mô tả chức năng của vai trò này..."
                      v-model="formData.description"
                    ></textarea>
                  </div>

                  <div class="p-3 rounded-3 border bg-light-subtle">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="small fw-semibold text-dark">Tổng quyền đã cấp</span>
                      <span class="badge bg-label-primary">{{ selectedPermissionsCount }}/{{ totalPermissionsCount }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                      <div
                        class="progress-bar bg-primary"
                        role="progressbar"
                        :style="{ width: totalPermissionsCount ? Math.round((selectedPermissionsCount / totalPermissionsCount) * 100) + '%' : '0%' }"
                      ></div>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.78rem;">
                      Tích chọn ở cột bên phải để cấp quyền theo từng nhóm chức năng.
                    </small>
                  </div>
                </div>

                <!-- Cột Phải: Thiết lập quyền hạn -->
                <div class="col-lg-8 d-flex flex-column">
                  <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                    <h6 class="fw-bold text-dark text-uppercase fs-6 mb-0">Thiết Lập Quyền Hạn (Permissions)</h6>
                    <div class="d-flex align-items-center gap-2">
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline-primary py-1 px-2" 
                        @click="selectAllPermissions"
                      >
                        <i class="bx bx-check-double me-1"></i> Chọn tất cả
                      </button>
                      <button 
                        type="button" 
                        class="btn btn-sm btn-outline-secondary py-1 px-2" 
                        @click="deselectAllPermissions"
                      >
                        <i class="bx bx-x me-1"></i> Bỏ chọn tất cả
                      </button>
                    </div>
                  </div>

                  <div class="border rounded-3 p-3 bg-light custom-scroll flex-grow-1" style="max-height: 60vh; overflow-y: auto;">
                    <div class="row g-3">
                      <div class="col-md-6" v-for="module in permissionModules" :key="module.id">
                        <div class="card border h-100 shadow-none bg-white rounded-3 overflow-hidden">
                          <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between border-bottom">
                            <div class="form-check mb-0 d-flex align-items-center">
                              <input 
                                class="form-check-input me-2 mt-0" 
                                type="checkbox" 
                                :id="'module_' + module.id" 
                                :checked="isModuleFullyChecked(module)"
                                @change="toggleModule(module, $event.target.checked)"
                              >
                              <label class="fw-bold text-primary mb-0 user-select-none" :for="'module_' + module.id" style="cursor: pointer; font-size: 0.9rem;">
                                {{ module.name }}
                              </label>
                            </div>
                            <span 
                              class="badge" 
                              :class="isModuleFullyChecked(module) ? 'bg-success' : (getModuleCheckedCount(module) > 0 ? 'bg-primary' : 'bg-secondary')"
                            >
                              {{ getModuleCheckedCount(module) }}/{{ module.permissions ? module.permissions.length : 0 }}
                            </span>
                          </div>
                          <div class="card-body p-3">
                            <div class="d-flex flex-column gap-2">
                              <div class="form-check mb-0" v-for="perm in module.permissions" :key="perm.id">
                                <input 
                                  class="form-check-input" 
                                  type="checkbox" 
                                  :value="perm.id" 
                                  :id="'perm_' + perm.id" 
                                  v-model="formData.permissions"
                                >
                                <label class="form-check-label text-dark small" :for="'perm_' + perm.id" style="cursor: pointer;">
                                  {{ perm.name }}
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer border-top py-3 bg-light">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                Hủy
              </button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ isEditMode ? 'Cập Nhật' : 'Thêm Mới' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { computed } from 'vue';
import { useAuthStore } from '../../../stores/authStore';
import vSelect from '@vueform/multiselect';
import Swal from 'sweetalert2';
import axiosClient from '../../../api/axiosClient';

export default {
  name: 'PhanQuyen',
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();

    const { data: queryData, isFetching, isPending } = useQuery({
      queryKey: ['vai-tro'],
      queryFn: async () => {
        const response = await axiosClient.get('/admin/vai-tro');
        return response.data || response;
      },
      staleTime: 5 * 60 * 1000,
      enabled: computed(() => !authStore.loading),
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => {
        return await axiosClient.delete('/admin/vai-tro/' + id);
      },
      onError: (err, id, context) => {
        Swal.fire('Lỗi', err.response?.data?.message || 'Không thể xóa', 'error');
      },
      onSuccess: () => {
        // Success handled silently or by toast below
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['vai-tro'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async ({ isEditMode, id, data }) => {
        if (isEditMode) {
          return await axiosClient.put('/admin/vai-tro/' + id, data);
        } else {
          return await axiosClient.post('/admin/vai-tro', data);
        }
      },
      onError: (err, vars, context) => {
        Swal.fire('Lỗi', err.response?.data?.message || 'Có lỗi xảy ra', 'error');
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['vai-tro'] });
      }
    });

    return {
      authStore,
      queryData, isFetching, isPending,
      deleteMutation,
      saveMutation
    };
  },
  data() {
    return {
      filters: {
        search: '',
        status: null
      },
      isSubmitting: false,
      isEditMode: false,
      formData: {
        id: null,
        name: '',
        description: '',
        permissions: [],
        status: 1
      },
      statusOptions: [
        { value: 1, label: 'Hoạt Động' },
        { value: 0, label: 'Đã Khóa' }
      ]
    };
  },
  computed: {
    roles() {
      return this.queryData?.roles || [];
    },
    permissionModules() {
      return this.queryData?.modules || [];
    },
    totalPermissionsCount() {
      return this.permissionModules.reduce((acc, m) => acc + (m.permissions ? m.permissions.length : 0), 0);
    },
    selectedPermissionsCount() {
      return this.formData.permissions ? this.formData.permissions.length : 0;
    },
    filteredRoles() {
      return this.roles.filter(r => {
        let match = true;
        if (this.filters.search) {
          const s = this.filters.search.toLowerCase().trim();
          match = match && (
            String(r.name || '').toLowerCase().includes(s) || 
            String(r.description || '').toLowerCase().includes(s)
          );
        }
        if (this.filters.status !== null && this.filters.status !== undefined && this.filters.status !== '') {
          match = match && Number(r.status) === Number(this.filters.status);
        }
        return match;
      });
    }
  },
  methods: {
    resetFilters() {
      this.filters = {
        search: '',
        status: null
      };
    },
    isModuleFullyChecked(module) {
      if (!this.formData.permissions || !module.permissions || module.permissions.length === 0) return false;
      return module.permissions.every(p => this.formData.permissions.includes(p.id));
    },
    getModuleCheckedCount(module) {
      if (!this.formData.permissions || !module.permissions) return 0;
      return module.permissions.filter(p => this.formData.permissions.includes(p.id)).length;
    },
    selectAllPermissions() {
      const allIds = [];
      this.permissionModules.forEach(m => {
        if (m.permissions) {
          m.permissions.forEach(p => allIds.push(p.id));
        }
      });
      this.formData.permissions = [...new Set(allIds)];
    },
    deselectAllPermissions() {
      this.formData.permissions = [];
    },
    toggleModule(module, isChecked) {
      if (!this.formData.permissions) this.formData.permissions = [];
      
      module.permissions.forEach(p => {
        const index = this.formData.permissions.indexOf(p.id);
        if (isChecked && index === -1) {
          this.formData.permissions.push(p.id);
        } else if (!isChecked && index !== -1) {
          this.formData.permissions.splice(index, 1);
        }
      });
    },
    isSystemRoleItem(item) {
      const n = String(item?.name || '').toLowerCase().trim();
      return Boolean(item?.is_system || ['admin', 'super admin', 'quản trị viên'].includes(n));
    },
    openAddModal() {
      this.isEditMode = false;
      this.resetForm();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { 
        ...item,
        permissions: Array.isArray(item.permissions) ? [...item.permissions] : []
      };
      if (this.formData.status === true) this.formData.status = 1;
      else if (this.formData.status === false) this.formData.status = 0;
    },
    async saveData() {
      if (!this.formData.name) {
        if (this.$toast) {
          this.$toast.error('Vui lòng nhập Tên Vai Trò!');
        } else {
          Swal.fire('Lỗi', 'Vui lòng nhập Tên Vai Trò!', 'error');
        }
        return;
      }
      
      this.isSubmitting = true;
      
      this.saveMutation.mutate(
        { isEditMode: this.isEditMode, id: this.formData.id, data: this.formData },
        {
          onSuccess: async () => {
            await this.authStore.fetchProfile();
            if (this.$toast) {
              this.$toast.success(this.isEditMode ? 'Cập nhật Vai trò thành công!' : 'Đã thêm Vai trò mới!');
            }
            this.closeModal();
            this.isSubmitting = false;
          },
          onError: () => {
            this.isSubmitting = false;
          }
        }
      );
    },
    async confirmDelete(item) {
      if (item.user_count > 0) {
        if (this.$toast) {
          this.$toast.error('Không thể xóa Vai trò đang có người dùng!');
        } else {
          Swal.fire('Lỗi', 'Không thể xóa Vai trò đang có người dùng!', 'error');
        }
        return;
      }

      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa vai trò <b>"${item.name}"</b>?`,
        icon: 'warning',
          iconColor: '#f82249',
        showCancelButton: true,
        confirmButtonText: 'Xóa Vai trò',
        cancelButtonText: 'Hủy',
          reverseButtons: true,
        customClass: {
          confirmButton: 'btn btn-danger px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-2'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        this.deleteMutation.mutate(item.id, {
          onSuccess: () => {
            if (this.$toast) this.$toast.success('Đã xóa Vai trò!');
          }
        });
      }
    },
    closeModal() {
      const modalEl = document.getElementById('modalRole');
      if (modalEl) {
        // eslint-disable-next-line no-undef
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      }
      const closeBtn = document.querySelector('#modalRole .btn-close');
      if (closeBtn) closeBtn.click();
    },
    resetForm() {
      this.formData = {
        id: null,
        name: '',
        description: '',
        permissions: [],
        status: 1
      };
    }
  }
};
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
  width: 6px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #c7c7d2;
  border-radius: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
  background: #a0a0b2;
}
.card-header {
  border-top-left-radius: inherit;
  border-top-right-radius: inherit;
}
@media (min-width: 992px) {
  .role-left-col {
    border-right: 1px solid #e7e7ec;
    padding-right: 1.5rem;
  }
}
</style>









