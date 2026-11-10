<template>
  <div>
    <!-- Unified Single Card: Engine Model Header + Saved Parts Body -->
    <div class="card border-0 jpm-card">
      <div class="card-header jpm-card-header">
        <!-- Skeleton Header -->
        <div v-if="isLoading" class="placeholder-glow w-100">
          <div class="placeholder col-4 mb-1" style="height: 18px;"></div>
          <div class="placeholder col-2" style="height: 13px;"></div>
        </div>

        <!-- Integrated Machine Model Header -->
        <div v-else-if="garageData" class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
          <div class="d-flex align-items-center gap-3 min-width-0">
            <router-link
              to="/garage"
              class="btn-back-header flex-shrink-0"
              title="Quay lại Garage của tôi">
              <i class="bi bi-arrow-left"></i>
            </router-link>

            <div class="min-width-0">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="jpm-card-title">{{ garageData.dong_co_model?.ten_model || 'Chưa rõ Model' }}</h5>
                <span v-if="garageData.dong_co_model?.thuong_hieu?.name" class="badge brand-tag-badge">
                  {{ garageData.dong_co_model.thuong_hieu.name }}
                </span>
              </div>

              <div
                v-if="garageData.dong_co_model?.ma_luc || garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau"
                class="d-flex align-items-center gap-3 flex-wrap mt-1"
                style="font-size: 13px; color: #64748b; line-height: 1.3;">
                <span v-if="garageData.dong_co_model?.ma_luc"><i class="bi bi-lightning-charge me-1 text-warning"></i>{{ garageData.dong_co_model.ma_luc }} HP</span>
                <span v-if="garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau"><i class="bi bi-calendar3 me-1"></i>Năm: {{ garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau || '' }}</span>
              </div>
            </div>
          </div>

          <!-- Header Actions -->
          <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <router-link
              :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`"
              class="btn btn-outline-secondary btn-action-header">
              <i class="bi bi-search"></i>Tra phụ tùng
            </router-link>
          </div>
        </div>
      </div>

      <div class="card-body px-4 py-3">
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
        <div v-else-if="groups.length === 0" class="text-center py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 260px;">
          <div class="empty-icon-wrap mb-3">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5 class="mb-2 garage-detail-title">Chưa có phụ tùng nào</h5>
          <p class="text-muted mb-3" style="font-size: 14px; max-width: 420px; line-height: 1.5;">Xem sơ đồ kỹ thuật chi tiết của động cơ và bấm lưu các phụ tùng cần bảo dưỡng định kỳ.</p>
          <router-link :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-search"></i>
            <span>Tra cứu phụ tùng</span>
          </router-link>
        </div>

        <!-- Grouped Parts -->
        <div v-else class="d-flex flex-column gap-3">
          <div v-for="group in groups" :key="group.nhom_id || group.nhom_ten">

            <!-- Group Header -->
            <div class="d-flex align-items-center gap-2 mb-1">
              <div style="width: 3px; height: 14px; background: var(--jpm-color-red-marine, #F82249); border-radius: 2px;"></div>
              <h6 class="mb-0 fw-bold" style="font-size: 13px; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ group.nhom_ten }}
              </h6>
              <span class="badge" style="background-color: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 500; padding: 2px 7px; border-radius: var(--jpm-radius-badge, 6px);">
                {{ group.items.length }} phụ tùng
              </span>
            </div>

            <!-- Parts List in Group -->
            <div class="border rounded-2 overflow-hidden" style="border-color: #e2e8f0 !important;">
              <div
                v-for="(item, idx) in group.items"
                :key="item.id"
                class="d-flex align-items-center gap-3 py-2 px-3 part-row-item"
                :style="idx < group.items.length - 1 ? 'border-bottom: 1px solid #f1f5f9;' : ''"
                @click="router.push('/chi-tiet-phu-tung/' + item.phu_tung?.ma_phu_tung)">

                <!-- Part Image -->
                <div class="flex-shrink-0 d-flex align-items-center justify-content-center border rounded-2" style="width: 46px; height: 46px; background: #fff; border-color: #e2e8f0 !important;">
                  <img
                    v-if="item.phu_tung?.hinh_anh"
                    :src="getImageUrl(item.phu_tung.hinh_anh)"
                    :alt="item.phu_tung.ten_phu_tung"
                    style="max-width: 38px; max-height: 38px; object-fit: contain;">
                  <i v-else class="bi bi-gear text-muted" style="font-size: 1.1rem; opacity: 0.4;"></i>
                </div>

                <!-- Part Info (Compact 2-line layout) -->
                <div class="flex-grow-1 min-width-0">
                  <router-link
                    :to="`/chi-tiet-phu-tung/${item.phu_tung?.ma_phu_tung}`"
                    @click.stop
                    class="fw-bold d-block text-decoration-none text-truncate hover-red"
                    style="color: var(--jpm-color-navy-dark, #0A1128); font-size: 14px; line-height: 1.35;">
                    {{ item.phu_tung?.ten_phu_tung }}
                  </router-link>
                  <div class="d-flex align-items-center gap-2 flex-wrap mt-1" style="font-size: 12.5px; line-height: 1.2;">
                    <span class="part-code-badge">{{ item.phu_tung?.ma_phu_tung }}</span>
                    <span v-if="item.phu_tung?.is_gia_lien_he" class="fw-bold" style="color: var(--jpm-color-red-marine, #F82249); font-size: 13px;">Liên hệ</span>
                    <span v-else class="fw-bold" style="color: var(--jpm-color-red-marine, #F82249); font-size: 13px;">
                      {{ formatCurrency(item.phu_tung?.gia_ban) }}
                    </span>
                    <template v-if="item.ghi_chu">
                      <span style="color: #cbd5e1;">•</span>
                      <span class="text-muted" style="font-size: 12px;"><i class="bi bi-info-circle me-1"></i>{{ item.ghi_chu }}</span>
                    </template>
                  </div>
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0 pe-1">
                  <button class="btn btn-cart-row" title="Thêm vào giỏ hàng" @click.stop="addOneToCart(item)">
                    <i class="bi bi-cart-plus"></i>
                  </button>
                  <button class="btn btn-delete-row" title="Xóa khỏi danh sách" @click.stop="removeItem(item.id)">
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

const getImageUrl = (path) => {
  if (!path) return "";
  if (path.startsWith("blob:") || path.startsWith("http") || path.startsWith("data:")) return path;
  const clean = path.startsWith("/") ? path.slice(1) : path;
  if (clean.startsWith("storage/") || clean.startsWith("uploads/")) {
    return `http://localhost:8000/${clean}`;
  }
  return `http://localhost:8000/storage/${clean}`;
};

import { ref, computed } from 'vue';
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
  cartStore.addItem(item.phu_tung, item.so_luong);
  toaster.success(`Đã thêm ${item.so_luong} × ${item.phu_tung.ten_phu_tung} vào giỏ!`);
};


const addSelectedToCart = () => {
  let count = 0;
  selectedParts.value.forEach(item => {
    if (item.phu_tung) {
      cartStore.addItem(item.phu_tung, item.so_luong);
      count++;
    }
  });
  toaster.success(`Đã thêm ${count} phụ tùng vào giỏ hàng!`);
  selectedParts.value = []; // Reset after adding
};
</script>

<style scoped>
.garage-detail-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 700;
  letter-spacing: -0.01em;
}
.brand-tag-badge {
  background-color: rgba(248, 34, 73, 0.08);
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
  font-size: 10px;
  padding: 3px 8px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border-radius: var(--jpm-radius-badge, 6px);
  border: 1px solid rgba(248, 34, 73, 0.15);
}
.part-row-item {
  transition: background-color 0.15s ease;
  cursor: pointer;
}
.part-row-item:hover {
  background-color: #f8fafc;
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
.hover-red:hover {
  color: var(--jpm-color-red-marine, #F82249) !important;
}
</style>

