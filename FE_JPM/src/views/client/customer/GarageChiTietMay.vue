<template>
  <div class="h-100 d-flex flex-column flex-grow-1">
    <!-- Unified Single Card: Engine Model Header + Saved Parts Body -->
    <div class="card border-0 jpm-card h-100 d-flex flex-column flex-grow-1">
      <div class="card-header jpm-card-header">
        <!-- Skeleton Header -->
        <div v-if="isLoading" class="placeholder-glow w-100">
          <div class="placeholder col-4 mb-1" style="height: 18px;"></div>
          <div class="placeholder col-2" style="height: 13px;"></div>
        </div>

        <!-- Integrated Machine Model Header -->
        <div v-else-if="garageData" class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">
          <div class="min-w-0">
            <!-- Dòng trên: Tên Model máy (đã bỏ phần thừa bên trên) -->
            <h5 class="jpm-card-title mb-1" :title="garageData.dong_co_model?.ten_model">
              {{ garageData.dong_co_model?.ten_model || 'Chưa rõ Model' }}
            </h5>

            <!-- Dòng dưới: Thông số tối giản, phân cách thanh lịch bằng vách ngăn | -->
            <div class="garage-specs-row d-flex align-items-center flex-wrap">
              <!-- Chữ Thương hiệu màu đỏ -->
              <span v-if="garageData.dong_co_model?.thuong_hieu?.name" class="fw-bold" style="color: var(--jpm-color-red-marine, #F82249); letter-spacing: 0.3px;">
                {{ garageData.dong_co_model.thuong_hieu.name }}
              </span>

              <!-- Mã lực HP -->
              <template v-if="garageData.dong_co_model?.ma_luc">
                <span v-if="garageData.dong_co_model?.thuong_hieu?.name" class="spec-divider">|</span>
                <span class="spec-item">
                  {{ garageData.dong_co_model.ma_luc }} HP
                </span>
              </template>

              <!-- Năm sản xuất -->
              <template v-if="garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau">
                <span class="spec-divider">|</span>
                <span class="spec-item">
                  Năm {{ garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau || '' }}
                </span>
              </template>

              <!-- Loại động cơ -->
              <template v-if="garageData.dong_co_model?.loai_dong_co?.name">
                <span class="spec-divider">|</span>
                <span class="spec-item">
                  {{ garageData.dong_co_model.loai_dong_co.name }}
                </span>
              </template>

              <!-- Serial -->
              <template v-if="garageData.so_serial">
                <span class="spec-divider">|</span>
                <span class="spec-item text-truncate" style="max-width: 220px;" :title="'Số serial: ' + garageData.so_serial">
                  Số máy: {{ garageData.so_serial }}
                </span>
              </template>
            </div>
          </div>

          <!-- Nút quay lại đồng bộ như các page khác (dạng link text + mũi tên, không đóng khung button) -->
          <router-link
            to="/garage"
            class="btn-back-diagram flex-shrink-0"
            title="Quay lại danh sách động cơ"
          >
            <i class="bi bi-arrow-left"></i>
            <span>Quay lại danh sách</span>
          </router-link>
        </div>
      </div>

      <div class="card-body px-4 py-3 d-flex flex-column flex-grow-1" style="min-height: 480px;">
        <!-- Loading Skeleton -->
        <div v-if="isLoading">
          <div v-for="i in 2" :key="i" class="mb-3">
            <div class="placeholder-glow mb-2">
              <div class="placeholder col-2" style="height: 14px;"></div>
            </div>
            <div v-for="j in 2" :key="j" class="d-flex align-items-center gap-3 py-2 border-bottom">
              <div class="placeholder rounded-2" style="width: 46px; height: 46px;"></div>
              <div class="flex-grow-1 placeholder-glow">
                <div class="placeholder col-4 mb-1" style="height: 14px;"></div>
                <div class="placeholder col-3" style="height: 12px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="groups.length === 0" class="text-center py-4 d-flex flex-column align-items-center justify-content-center flex-grow-1 my-auto" style="min-height: 320px;">
          <div class="empty-icon-wrap mb-3">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5 class="mb-2 garage-detail-title">Chưa có phụ tùng nào</h5>
          <p class="text-muted mb-3" style="font-size: 0.85rem; max-width: 420px; line-height: 1.55;">Xem sơ đồ kỹ thuật chi tiết của động cơ và bấm lưu các phụ tùng cần bảo dưỡng định kỳ.</p>
          <router-link :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-search"></i>
            <span>Tra cứu phụ tùng</span>
          </router-link>
        </div>

        <!-- Grouped Parts -->
        <div v-else class="d-flex flex-column">
          <div v-for="group in groups" :key="group.nhom_id || group.nhom_ten" class="mb-4">

            <!-- Group Header (Thanh đỏ + Tên nhóm, bỏ badge số lượng) -->
            <div class="d-flex align-items-center gap-2 mb-3">
              <div style="width: 4px; height: 18px; background: var(--jpm-color-red-marine, #F82249); border-radius: var(--jpm-radius-badge, 6px);"></div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.95rem; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ group.nhom_ten }}
              </h6>
            </div>

            <!-- Parts List in Group: Danh sách các card phụ tùng tiêu chuẩn -->
            <div class="d-flex flex-column gap-3">
              <div
                v-for="item in group.items"
                :key="item.id"
                class="border part-item-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
                @click="router.push('/chi-tiet-phu-tung/' + (item.phu_tung?.ma_phu_tung || item.phu_tung?.id))"
              >
                <!-- Bên trái: Hình ảnh (58x58px) + Thông tin phụ tùng -->
                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                  <!-- Khung ảnh 58x58px chuẩn -->
                  <div class="part-img-wrap rounded-2 border flex-shrink-0 d-flex align-items-center justify-content-center bg-white">
                    <img
                      v-if="item.phu_tung?.hinh_anh"
                      :src="getImageUrl(item.phu_tung.hinh_anh)"
                      :alt="item.phu_tung.ten_phu_tung"
                      class="part-product-img"
                    />
                    <i v-else class="bi bi-gear text-muted fs-3 opacity-50"></i>
                  </div>

                  <!-- Thông tin chi tiết: Mã phụ tùng (xanh primary) + Tên phụ tùng, Dòng dưới: Tiền căn trái -->
                  <div class="flex-grow-1 min-w-0">
                    <!-- Dòng trên: Mã xong đến Tên phụ tùng (Mã màu xanh primary #0d6efd) -->
                    <div class="d-flex align-items-center mb-1 min-w-0 flex-wrap" style="column-gap: 8px;">
                      <span class="part-sku-code">{{ item.phu_tung?.ma_phu_tung }}</span>
                      <h6 class="fw-bold mb-0 text-truncate part-product-title" :title="item.phu_tung?.ten_phu_tung">
                        {{ item.phu_tung?.ten_phu_tung }}
                      </h6>
                    </div>

                    <!-- Dòng dưới: Tiền căn sang trái & Ghi chú nếu có -->
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                      <div class="part-price-box">
                        <span v-if="item.phu_tung?.is_gia_lien_he" class="fw-bold text-danger fs-6">Liên hệ</span>
                        <span v-else-if="item.phu_tung?.gia_ban > 0" class="fw-bold text-danger fs-6">
                          {{ formatCurrency(item.phu_tung?.gia_ban) }}
                        </span>
                        <span v-else class="text-muted small">Đang cập nhật giá</span>
                      </div>

                      <!-- Ghi chú nếu có -->
                      <div v-if="item.ghi_chu" class="text-muted small text-truncate" style="max-width: 250px;">
                        <span style="color: #cbd5e1;" class="me-2">•</span>
                        <i class="bi bi-stickies me-1"></i>{{ item.ghi_chu }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Bên phải: Hai nút thao tác dạng icon 36x36px đồng bộ 100% -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0 pe-1 align-self-end align-self-md-center">
                  <!-- Nút Thêm giỏ: 36x36px outline Primary Blue #0d6efd -->
                  <button
                    type="button"
                    class="btn btn-cart-icon"
                    title="Thêm vào giỏ hàng"
                    @click.stop="addOneToCart(item)"
                  >
                    <i class="bi bi-cart-plus"></i>
                  </button>

                  <!-- Nút Xóa: 36x36px outline xám hover Đỏ Marine #F82249 -->
                  <button
                    type="button"
                    class="btn btn-delete-icon"
                    title="Xóa phụ tùng khỏi danh sách"
                    @click.stop="removeItem(item.id)"
                  >
                    <i class="bi bi-trash3"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useCartStore } from '../../../stores/cartStore';
import { createToaster } from '@meforma/vue-toaster';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();
const queryClient = useQueryClient();
const toaster = createToaster({ position: 'top-right', duration: 3000 });

import { getImageUrl } from '../../../utils/imageUrl';

const garageId = computed(() => route.params.id);

// ─── FETCH DATA ───────────────────────────────────────────────────────────────
const { data: chiTietData, isLoading } = useQuery({
  queryKey: computed(() => ['garage-chi-tiet', garageId.value]),
  queryFn: () => axiosClient.get(`/client/garage/${garageId.value}/chi-tiet`),
  enabled: computed(() => !!garageId.value)
});

const garageData = computed(() => chiTietData.value?.garage || null);
const groups = computed(() => chiTietData.value?.groups || []);
const totalItems = computed(() => chiTietData.value?.total || 0);

// ─── FORMAT ───────────────────────────────────────────────────────────────────
const formatCurrency = (val) => {
  if (!val) return '0 ₫';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
};

// ─── REMOVE ITEM ─────────────────────────────────────────────────────────────
const removeMutation = useMutation({
  mutationFn: (id) => axiosClient.delete(`/client/garage-phu-tung/${id}`),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ['garage-chi-tiet', garageId.value] });
    const previous = queryClient.getQueryData(['garage-chi-tiet', garageId.value]);
    queryClient.setQueryData(['garage-chi-tiet', garageId.value], (old) => {
      if (!old) return old;
      return {
        ...old,
        groups: old.groups.map(g => ({
          ...g,
          items: g.items.filter(i => i.id !== id)
        })).filter(g => g.items.length > 0),
        total: old.total - 1
      };
    });
    return { previous };
  },
  onError: (err, id, ctx) => {
    queryClient.setQueryData(['garage-chi-tiet', garageId.value], ctx.previous);
    toaster.error('Không thể xóa phụ tùng!');
  },
  onSuccess: () => {
    toaster.success('Đã xóa phụ tùng khỏi danh sách!');
    queryClient.invalidateQueries({ queryKey: ['saved-parts'] });
  }
});

const removeItem = async (id) => {
  const result = await Swal.fire({
    title: 'Xóa phụ tùng?',
    text: 'Phụ tùng này sẽ bị xóa khỏi danh sách của bạn.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f82249',
    cancelButtonColor: '#64748B',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
    customClass: { popup: 'rounded-3' }
  });
  if (result.isConfirmed) {
    removeMutation.mutate(id);
  }
};

// ─── CART ─────────────────────────────────────────────────────────────────────
const addOneToCart = (item) => {
  if (!item.phu_tung) return;
  cartStore.addItem(item.phu_tung, item.so_luong || 1);
  toaster.success(`Đã thêm ${item.phu_tung.ten_phu_tung} vào giỏ!`);
};
</script>

<style scoped>
.garage-detail-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 700;
  letter-spacing: -0.01em;
}

.garage-model-code {
  font-family: var(--jpm-font-mono, 'Roboto Mono', monospace);
  color: #0d6efd !important;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  flex-shrink: 0;
}

/* Card phụ tùng phẳng, không hiệu ứng nổi, giảm chiều cao đồng bộ 100% với VatTuThuongMua */
.part-item-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid var(--jpm-neutral-200, #E2E8F0) !important;
  box-shadow: none !important;
  background-color: var(--jpm-neutral-0, #FFFFFF);
  overflow: hidden;
  cursor: pointer;
  padding: 10px 16px !important;
  transition: border-color 0.2s ease, background-color 0.2s ease;
}

.part-item-card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: #cbd5e1 !important;
  background-color: #fafbfc;
}

.part-img-wrap {
  width: 58px;
  height: 58px;
  min-width: 58px;
  min-height: 58px;
  padding: 4px;
  border-color: var(--jpm-neutral-200, #E2E8F0) !important;
  overflow: hidden;
}

.part-product-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.part-product-title {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 0.95rem;
  line-height: 1.35;
}

/* Mã phụ tùng: Màu xanh primary #0d6efd đậm, rõ ràng, nằm trước tên */
.part-sku-code {
  font-family: var(--jpm-font-mono, 'Roboto Mono', monospace);
  color: #0d6efd !important;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  flex-shrink: 0;
}

.part-price-box {
  /* Căn lề trái */
}

/* Hai nút thao tác dạng icon 36x36px đồng bộ 100% với VatTuThuongMua */
.btn-cart-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--jpm-radius-control, 8px);
  background-color: #ffffff;
  border: 1.5px solid #0d6efd;
  color: #0d6efd;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  transition: all 0.2s ease;
  box-shadow: none !important;
}

.btn-cart-icon:hover {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: none !important;
}

.btn-delete-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--jpm-radius-control, 8px);
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  color: #94a3b8;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 1.05rem;
}

.btn-delete-icon:hover {
  background-color: var(--jpm-color-red-marine, #F82249);
  border-color: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: none !important;
}

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

/* Dòng thông số kỹ thuật tối giản thanh lịch */
.garage-specs-row {
  font-size: 0.85rem;
  color: #64748b;
  line-height: 1.4;
}

.spec-item {
  color: #475569;
  font-weight: 500;
}

.spec-divider {
  color: #cbd5e1;
  font-size: 0.75rem;
  margin: 0 8px;
  user-select: none;
}

/* Nút quay lại chuẩn hóa đồng bộ với ChiTietSanPham, ChiTietPhuTung, SoDoPhuTung */
.btn-back-diagram {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  color: var(--jpm-color-navy-dark, #0A1128);
  border: none;
  padding: 0;
  font-size: 0.88rem;
  font-weight: 600;
  box-shadow: none;
  cursor: pointer;
  text-decoration: none;
  transition: color 0.2s ease, opacity 0.15s ease;
  line-height: 1.2;
}

.btn-back-diagram i {
  color: inherit;
  font-size: 1rem;
  line-height: 1;
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease;
}

.btn-back-diagram:hover {
  background: transparent;
  color: var(--jpm-color-red-marine, #F82249) !important;
  box-shadow: none;
  transform: none;
  text-decoration: none;
}

.btn-back-diagram:hover i {
  color: var(--jpm-color-red-marine, #F82249) !important;
  transform: translateX(-3px);
}
</style>
