<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Danh Sách Kho
    </h4>

    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4" v-if="authStore.hasPermission('Quản lý Danh sách kho')">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật Kho' : 'Thêm Mới Kho' }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="mb-4" v-if="isEditMode && formData.ma_kho">
                <label class="form-label fw-medium text-dark">Mã Kho</label>
                <input
                  type="text"
                  class="form-control bg-light fw-bold"
                  :value="formData.ma_kho"
                  disabled
                />
              </div>

              <div class="mb-4">
                <label for="name" class="form-label fw-medium text-dark">Tên Kho <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  class="form-control"
                  placeholder="Nhập tên kho..."
                  v-model.trim="formData.ten_kho"
                  maxlength="100"
                  required
                />
              </div>

              <div class="mb-4">
                <label for="address" class="form-label fw-medium text-dark">Địa Chỉ</label>
                <textarea
                  id="address"
                  class="form-control"
                  rows="4"
                  style="min-height: 96px;"
                  placeholder="Nhập địa chỉ kho (tùy chọn)..."
                  v-model.trim="formData.dia_chi"
                  maxlength="255"
                ></textarea>
              </div>

              <div class="mb-4">
                <label for="status" class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.trang_thai"
                  :options="[{ value: 1, label: 'Hoạt Động' }, { value: 0, label: 'Tạm Khóa' }]"
                  :can-clear="false"
                />
                <small class="text-muted d-block mt-1">
                  <i class="bx bx-info-circle"></i> Kho tạm khóa sẽ không tự động trừ hoặc nhận thêm tồn mới khi phát sinh đơn.
                </small>
              </div>

              <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" @click="resetForm">
                  Hủy
                </button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="saveMutation.isPending.value">
                  <span v-if="saveMutation.isPending.value" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  {{ isEditMode ? 'Cập Nhật' : 'Thêm Mới' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Cột Bảng (Danh sách) -->
      <div :class="authStore.hasPermission('Quản lý Danh sách kho') ? 'col-md-8' : 'col-12'" class="mb-4">
        <div class="card">
          <div class="card-header border-bottom d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Danh Sách Kho Bãi</h5>
            <div style="width: 260px;">
              <input
                type="text"
                class="form-control"
                placeholder="Tìm mã kho, tên kho, địa chỉ..."
                v-model="searchKeyword"
              >
            </div>
          </div>

          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th class="text-center" style="width: 6%">STT</th>
                  <th style="width: 15%">Mã Kho</th>
                  <th style="width: 26%">Tên Kho</th>
                  <th style="width: 28%">Địa chỉ</th>
                  <th class="text-center" style="width: 13%">Trạng thái</th>
                  <th class="text-center" style="width: 12%">Hành động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isLoading">
                  <td colspan="6" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="!filteredWarehouses || filteredWarehouses.length === 0">
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in filteredWarehouses" :key="item.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td>
                    <span class="badge bg-label-primary fw-bold">{{ item.ma_kho || '---' }}</span>
                  </td>
                  <td>
                    <span class="fw-bold text-dark">{{ item.ten_kho }}</span>
                  </td>
                  <td>
                    <span
                      class="text-muted"
                      style="white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                      :title="item.dia_chi"
                    >
                      {{ item.dia_chi || '---' }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span v-if="Number(item.trang_thai) === 1" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                    <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <button
                        v-if="authStore.hasPermission('Quản lý Danh sách kho')"
                        type="button"
                        class="action-btn text-primary me-1"
                        @click="openEditModal(item)"
                        title="Sửa"
                      >
                        <i class="bx bx-edit-alt"></i>
                      </button>
                      <button
                        v-if="authStore.hasPermission('Quản lý Danh sách kho')"
                        type="button"
                        class="action-btn text-danger"
                        @click="confirmDelete(item)"
                        title="Xóa"
                      >
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="card-footer py-3 border-top">
            <span class="text-muted fw-medium">Tổng cộng: {{ filteredWarehouses.length }} kho</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from '../../../utils/notify';
import { useAuthStore } from '../../../stores/authStore';

export default {
  name: "Kho",
  components: {
    vSelect
  },
  setup() {
    const authStore = useAuthStore();
    const queryClient = useQueryClient();
    const toaster = createToaster({ position: "top-right", duration: 3000 });

    const { data: warehouses, isLoading } = useQuery({
      queryKey: ['danh-sach-kho'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/kho');
        return Array.isArray(res.data) ? res.data : [];
      },
      staleTime: 1000 * 60 * 5
    });

    const invalidateAllInventory = () => {
      queryClient.invalidateQueries({ queryKey: ['danh-sach-kho'] });
      queryClient.invalidateQueries({ queryKey: ['ton-kho'] });
      queryClient.invalidateQueries({ queryKey: ['the-kho'] });
      queryClient.invalidateQueries({ queryKey: ['bao-cao-xnt'] });
    };

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete('/admin/kho/' + id),
      onSettled: invalidateAllInventory
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id
          ? axiosClient.put('/admin/kho/' + payload.id, payload)
          : axiosClient.post('/admin/kho', payload);
      },
      onMutate: async (newRecord) => {
        await queryClient.cancelQueries({ queryKey: ['danh-sach-kho'] });
        const previousData = queryClient.getQueryData(['danh-sach-kho']);
        const optimisticId = newRecord.id || Date.now();
        queryClient.setQueryData(['danh-sach-kho'], (old) => {
          const list = Array.isArray(old) ? old : [];
          if (newRecord.id) {
            return list.map(item => item.id === newRecord.id ? { ...item, ...newRecord } : item);
          }
          return [{ ...newRecord, id: optimisticId, ma_kho: 'Đang tạo...' }, ...list];
        });
        return { previousData, optimisticId };
      },
      onError: (_err, _vars, context) => {
        if (context?.previousData !== undefined) {
          queryClient.setQueryData(['danh-sach-kho'], context.previousData);
        }
      },
      onSuccess: (res, vars, context) => {
        if (res?.data) {
          queryClient.setQueryData(['danh-sach-kho'], (old) => {
            const list = Array.isArray(old) ? old : [];
            const targetId = vars.id || context?.optimisticId;
            return list.map(item => item.id === targetId ? res.data : item);
          });
        }
      },
      onSettled: invalidateAllInventory
    });

    return { authStore, warehouses, isLoading, queryClient, deleteMutation, saveMutation, toaster };
  },
  data() {
    return {
      searchKeyword: "",
      isEditMode: false,
      formData: this.getInitialFormData()
    };
  },
  computed: {
    filteredWarehouses() {
      let list = Array.isArray(this.warehouses) ? this.warehouses : [];
      if (this.searchKeyword) {
        const q = this.searchKeyword.toLowerCase().trim();
        list = list.filter(w =>
          String(w.ma_kho || '').toLowerCase().includes(q) ||
          String(w.ten_kho || '').toLowerCase().includes(q) ||
          String(w.dia_chi || '').toLowerCase().includes(q)
        );
      }
      return list;
    }
  },
  methods: {
    getInitialFormData() {
      return {
        id: null,
        ma_kho: "",
        ten_kho: "",
        dia_chi: "",
        trang_thai: 1
      };
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = {
        id: item.id,
        ma_kho: item.ma_kho || "",
        ten_kho: item.ten_kho || "",
        dia_chi: item.dia_chi || "",
        trang_thai: item.trang_thai !== null && item.trang_thai !== undefined ? Number(item.trang_thai) : 1
      };
    },
    async saveData() {
      const tenKho = String(this.formData.ten_kho || '').trim();
      if (!tenKho) {
        this.toaster.error("Vui lòng nhập tên kho!");
        return;
      }
      const payload = {
        id: this.formData.id,
        ten_kho: tenKho,
        dia_chi: String(this.formData.dia_chi || '').trim() || null,
        trang_thai: Number(this.formData.trang_thai ?? 1)
      };

      this.saveMutation.mutate(payload, {
        onSuccess: () => {
          this.toaster.success(this.isEditMode ? "Cập nhật kho thành công!" : "Đã thêm kho mới!");
          this.resetForm();
        },
        onError: (error) => {
          this.toaster.error(error?.response?.data?.message || "Lỗi lưu dữ liệu kho");
        }
      });
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa kho bãi <b>"${item.ten_kho}"</b>?<br><br><small class="text-danger">Lưu ý: Hệ thống sẽ từ chối xóa nếu kho vẫn còn hàng tồn hoặc hàng đang giữ chỗ.</small>`,
        icon: 'warning',
        iconColor: '#dc3545',
        showCancelButton: true,
        confirmButtonText: 'Xóa Kho bãi',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
          popup: 'swal-shift-up shadow-sm',
          confirmButton: 'btn btn-danger px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        this.deleteMutation.mutate(item.id, {
          onSuccess: () => {
            if (this.isEditMode && this.formData.id === item.id) {
              this.resetForm();
            }
            this.toaster.success("Đã xóa kho thành công!");
          },
          onError: (error) => {
            this.toaster.error(error?.response?.data?.message || "Không thể xóa kho này!");
          }
        });
      }
    }
  }
};
</script>

<style scoped>
.action-btn {
  background: transparent;
  border: none;
  padding: 0.25rem 0.5rem;
  font-size: 1.2rem;
  transition: all 0.2s;
  border-radius: 4px;
}
.action-btn:hover {
  background-color: rgba(0,0,0,0.05);
}
.custom-scroll::-webkit-scrollbar {
  width: 6px;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #d9dee3;
  border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: transparent;
}
</style>
