<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ Thống /</span> Hỗ Trợ Khách Hàng
    </h4>

    <!-- Thống kê nhanh -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card border h-100">
          <div class="card-body py-3">
            <div class="text-muted small fw-semibold mb-1">TỔNG LỜI NHẮN</div>
            <div class="fs-4 fw-bold text-dark">{{ stats.total }}</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card border h-100">
          <div class="card-body py-3">
            <div class="text-muted small fw-semibold mb-1">MỚI TIẾP NHẬN</div>
            <div class="fs-4 fw-bold text-danger">{{ stats.moi }}</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card border h-100">
          <div class="card-body py-3">
            <div class="text-muted small fw-semibold mb-1">ĐANG XỬ LÝ</div>
            <div class="fs-4 fw-bold text-warning">{{ stats.dang_xu_ly }}</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card border h-100">
          <div class="card-body py-3">
            <div class="text-muted small fw-semibold mb-1">ĐÃ PHẢN HỒI</div>
            <div class="fs-4 fw-bold text-success">{{ stats.da_phan_hoi }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Yêu Cầu Hỗ Trợ &amp; Liên Hệ</h5>
      </div>

      <!-- Bộ lọc -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="flex-grow-1" style="min-width: 220px;">
            <input
              type="text"
              class="form-control"
              placeholder="Tìm tên khách hàng, SĐT, email, nội dung..."
              v-model="filters.search"
            />
          </div>
          <div style="min-width: 200px;">
            <v-select
              v-model="filters.trang_thai"
              :options="statusFilterOptions"
              placeholder="Trạng thái"
              :can-clear="false"
            />
          </div>
          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 340px); min-height: 360px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th style="width: 22%">Khách Hàng</th>
              <th style="width: 18%">Liên Hệ</th>
              <th style="width: 30%">Nội Dung Yêu Cầu</th>
              <th class="text-center" style="width: 12%">Trạng Thái</th>
              <th class="text-center" style="width: 13%">Hành Động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isPending">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredItems.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bx bx-support text-secondary fs-1 d-block mb-2"></i>
                Chưa có yêu cầu hỗ trợ nào
              </td>
            </tr>
            <tr v-for="(item, index) in filteredItems" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <div class="fw-bold text-dark">{{ item.ho_ten }}</div>
                <div class="small text-muted">{{ formatDate(item.created_at) }}</div>
              </td>
              <td>
                <div class="fw-medium text-dark">
                  <i class="bx bx-phone me-1 text-primary"></i>
                  <span>{{ item.so_dien_thoai }}</span>
                </div>
                <div class="small text-muted text-truncate" style="max-width: 220px;">
                  <i class="bx bx-envelope me-1"></i>
                  <span>{{ item.email }}</span>
                </div>
              </td>
              <td>
                <div
                  class="text-dark text-wrap"
                  style="max-width: 380px; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                >
                  {{ item.noi_dung }}
                </div>
              </td>
              <td class="text-center">
                <span v-if="item.trang_thai === 'Đã phản hồi'" class="badge badge-custom bg-pastel-success">ĐÃ PHẢN HỒI</span>
                <span v-else-if="item.trang_thai === 'Đang xử lý'" class="badge badge-custom bg-pastel-warning">ĐANG XỬ LÝ</span>
                <span v-else class="badge badge-custom bg-pastel-danger">MỚI</span>
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button
                    type="button"
                    class="action-btn text-primary me-1"
                    @click="openDetailModal(item)"
                    data-bs-toggle="modal"
                    data-bs-target="#modalHoTroDetail"
                    title="Xem & xử lý"
                  >
                    <i class="bx bx-show"></i>
                  </button>
                  <button
                    v-if="canWrite"
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
        <span class="text-muted fw-medium">Tổng cộng: {{ filteredItems.length }} yêu cầu</span>
      </div>
    </div>

    <!-- Modal Chi Tiết & Cập Nhật Trạng Thái -->
    <div class="modal fade" id="modalHoTroDetail" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-5">
              Chi Tiết Yêu Cầu Hỗ Trợ #{{ selectedItem?.id }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveStatus" v-if="selectedItem">
            <div class="modal-body pt-4 pb-2">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Họ và Tên Khách Hàng <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light" :value="selectedItem.ho_ten" readonly />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Thời Gian Gửi <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light" :value="formatDate(selectedItem.created_at)" readonly />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Số Điện Thoại <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light" :value="selectedItem.so_dien_thoai" readonly />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Email <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light" :value="selectedItem.email" readonly />
                </div>
                <div class="col-12">
                  <label class="form-label fw-medium text-dark">Nội Dung Lời Nhắn <span class="text-danger">*</span></label>
                  <textarea class="form-control bg-light" rows="4" :value="selectedItem.noi_dung" readonly></textarea>
                </div>
                <div class="col-md-6" v-if="canWrite">
                  <label class="form-label fw-medium text-dark">Trạng Thái Xử Lý <span class="text-danger">*</span></label>
                  <v-select
                    v-model="selectedStatus"
                    :options="statusUpdateOptions"
                    :can-clear="false"
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer pt-3 border-top">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
              <button v-if="canWrite" type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="updateMutation.isPending.value">
                <span v-if="updateMutation.isPending.value" class="spinner-border spinner-border-sm me-1"></span>
                Cập Nhật Trạng Thái
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from '@meforma/vue-toaster';
import Swal from 'sweetalert2';
import axiosClient from '../../../api/axiosClient';
import { useAuthStore } from '../../../stores/authStore';

const authStore = useAuthStore();
const queryClient = useQueryClient();
const toaster = createToaster({ position: 'top-right', duration: 3000 });

const filters = ref({
  search: '',
  trang_thai: '',
});

const selectedItem = ref(null);
const selectedStatus = ref('Mới');

const statusFilterOptions = [
  { value: '', label: 'Tất cả trạng thái' },
  { value: 'Mới', label: 'Mới' },
  { value: 'Đang xử lý', label: 'Đang xử lý' },
  { value: 'Đã phản hồi', label: 'Đã phản hồi' },
];

const statusUpdateOptions = [
  { value: 'Mới', label: 'Mới' },
  { value: 'Đang xử lý', label: 'Đang xử lý' },
  { value: 'Đã phản hồi', label: 'Đã phản hồi' },
];

const canWrite = computed(() => {
  return authStore.isAdmin || authStore.hasAnyPermission([
    'Duyệt Đơn hàng',
    'Sửa Đơn hàng',
    'Soạn thảo Tin tức & CMS',
    'Cấu hình chung',
  ]);
});

const { data: resData, isPending } = useQuery({
  queryKey: ['admin-lien-he'],
  queryFn: async () => await axiosClient.get('/admin/lien-he', { params: { all: true } }),
});

const items = computed(() => resData.value?.data || []);
const stats = computed(() => resData.value?.stats || { total: 0, moi: 0, dang_xu_ly: 0, da_phan_hoi: 0 });

const filteredItems = computed(() => {
  const kw = filters.value.search.trim().toLowerCase();
  const st = filters.value.trang_thai;
  return items.value.filter((item) => {
    if (st && item.trang_thai !== st) return false;
    if (kw) {
      const hay = `${item.ho_ten || ''} ${item.email || ''} ${item.so_dien_thoai || ''} ${item.noi_dung || ''}`.toLowerCase();
      if (!hay.includes(kw)) return false;
    }
    return true;
  });
});

const resetFilters = () => {
  filters.value = { search: '', trang_thai: '' };
};

const formatDate = (val) => {
  if (!val) return '-';
  const d = new Date(val);
  if (Number.isNaN(d.getTime())) return val;
  return d.toLocaleString('vi-VN');
};

const openDetailModal = (item) => {
  selectedItem.value = item;
  selectedStatus.value = item.trang_thai || 'Mới';
};

const updateMutation = useMutation({
  mutationFn: async ({ id, trang_thai }) => await axiosClient.put(`/admin/lien-he/${id}`, { trang_thai }),
  onSuccess: () => {
    toaster.success('Đã cập nhật trạng thái yêu cầu hỗ trợ!');
    queryClient.invalidateQueries({ queryKey: ['admin-lien-he'] });
    const closeBtn = document.querySelector('#modalHoTroDetail .btn-close');
    if (closeBtn) closeBtn.click();
  },
  onError: (err) => {
    toaster.error(err.response?.data?.message || 'Lỗi khi cập nhật trạng thái!');
  },
});

const saveStatus = () => {
  if (!selectedItem.value) return;
  updateMutation.mutate({ id: selectedItem.value.id, trang_thai: selectedStatus.value });
};

const deleteMutation = useMutation({
  mutationFn: async (id) => await axiosClient.delete(`/admin/lien-he/${id}`),
  onSuccess: () => {
    toaster.success('Đã xóa yêu cầu hỗ trợ!');
    queryClient.invalidateQueries({ queryKey: ['admin-lien-he'] });
  },
  onError: (err) => {
    toaster.error(err.response?.data?.message || 'Lỗi khi xóa yêu cầu!');
  },
});

const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: 'Xác nhận xóa',
    html: `Bạn có chắc chắn muốn xóa lời nhắn của <b>${item.ho_ten}</b>?`,
    icon: 'warning',
    iconColor: '#f82249',
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
    reverseButtons: true,
    animation: false,
    customClass: {
      popup: 'swal-shift-up',
      confirmButton: 'btn btn-danger px-4 shadow-sm',
      cancelButton: 'btn btn-outline-secondary px-4 me-3',
    },
    buttonsStyling: false,
  });

  if (result.isConfirmed) {
    deleteMutation.mutate(item.id);
  }
};
</script>
