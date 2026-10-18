<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ Thống /</span> Phân Quyền
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh sách Vai Trò (Roles)</h5>
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
                  :class="(item.is_system || item.name.toLowerCase() === 'admin') ? 'text-danger' : 'text-primary'"
                >
                  {{ item.name }}
                </span>
              </td>
              <td><span :class="!item.description ? 'text-muted' : 'text-secondary'">{{ item.description || "N/A" }}</span></td>
              <td class="text-center">
                <span class="fw-semibold text-secondary">{{ item.user_count }}</span>
              </td>
              <td class="text-center">
                <span v-if="item.status === 1 || item.status === true" class="badge text-uppercase bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge text-uppercase bg-pastel-danger">ĐÃ KHÓA</span>
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button 
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
                    v-if="!item.is_system && item.name.toLowerCase() !== 'admin'"
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

    <!-- Modal Form -->
    <div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true" ref="modalRole">
      <div class="modal-dialog modal-dialog-scrollable mt-5" style="max-width: 800px;" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4" id="modalRoleTitle">
              {{ isEditMode ? 'Cập Nhật Vai Trò' : 'Thêm Vai Trò Mới' }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pb-2">
              <div class="row">
                <div class="col-md-7 mb-3">
                    <label class="form-label fw-medium text-dark">Tên Vai Trò <span class="text-danger">*</span></label>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="VD: Quản lý Kho"
                      v-model="formData.name"
                      :disabled="isEditMode && (formData.is_system || formData.name.toLowerCase() === 'admin')"
                      required
                    />
                    <small v-if="isEditMode && (formData.is_system || formData.name.toLowerCase() === 'admin')" class="text-primary mt-1 d-block fw-semibold">
                      <i class="bx bx-shield-quarter me-1"></i> Vai trò Quản trị viên hệ thống (Super Admin).
                    </small>
                  </div>
                  <div class="col-md-5 mb-3">
                    <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.status"
                      :options="statusOptions"
                      :can-clear="false"
                      :disabled="isEditMode && (formData.is_system || formData.name.toLowerCase() === 'admin')"
                    />
                  </div>
                </div>

              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-medium text-dark">Mô tả</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Mô tả chức năng của vai trò này..."
                    v-model="formData.description"
                  />
                </div>
              </div>

              

              <!-- Danh sách quyền hạn -->
              <h6 class="mt-3 fw-bold text-dark text-uppercase fs-6">Thiết lập Quyền Hạn (Permissions)</h6>
              <div class="border rounded p-3 bg-lighter" style="max-height: 400px; overflow-y: auto;">
                
                <div class="mb-4" v-for="module in permissionModules" :key="module.id">
                  <div class="d-flex align-items-center mb-2 border-bottom pb-2">
                    <div class="form-check me-2 mb-0">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        :id="'module_' + module.id" 
                        :checked="isModuleFullyChecked(module)"
                        @change="toggleModule(module, $event.target.checked)"
                      >
                    </div>
                    <label class="fw-bold text-primary mb-0" :for="'module_' + module.id" style="cursor: pointer;">
                      {{ module.name }}
                    </label>
                  </div>
                  <div class="row ps-4">
                    <div class="col-md-3 col-6 mb-2" v-for="perm in module.permissions" :key="perm.id">
                      <div class="form-check">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          :value="perm.id" 
                          :id="'perm_' + perm.id" 
                          v-model="formData.permissions"
                        >
                        <label class="form-check-label text-dark" :for="'perm_' + perm.id" style="cursor: pointer;">
                          {{ perm.name }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

              </div>

            </div>

            <div class="modal-footer border-top py-3 mt-2">
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
    openAddModal() {
      this.isEditMode = false;
      this.resetForm();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { 
        ...item,
        permissions: [...item.permissions]
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
          onSuccess: () => {
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
          iconColor: '#ff3e1d',
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
.bg-lighter {
  background-color: #f8f9fa;
}
.modal-dialog-scrollable .modal-body {
  overflow-y: auto;
}
</style>









