<template>
  <div class="flex-grow-1 d-flex flex-column">

    <!-- Back button + Header -->
    <div class="mb-4">
      <router-link to="/garage" class="d-inline-flex align-items-center gap-2 text-muted mb-3 text-decoration-none hover-red" style="font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-arrow-left"></i> Quay lại Garage của tôi
      </router-link>

      <!-- Skeleton Header -->
      <div v-if="isLoading" class="p-4 border rounded-3 bg-white">
        <div class="placeholder-glow">
          <div class="placeholder col-3 mb-2" style="height: 20px;"></div>
          <div class="placeholder col-2" style="height: 14px;"></div>
        </div>
      </div>

      <!-- Machine Header Card -->
      <div v-else-if="garageData" class="p-4 border machine-header-card bg-white d-flex align-items-start justify-content-between flex-column flex-md-row gap-3">
        <div>
          <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h5 class="garage-detail-title mb-0">{{ garageData.dong_co_model?.ten_model || 'Chưa rõ Model' }}</h5>
            <span class="badge brand-tag-badge">
              {{ garageData.dong_co_model?.thuong_hieu?.name }}
            </span>
          </div>
          
          <div class="d-flex align-items-center gap-4 flex-wrap mt-2" style="font-size: 13.5px; color: #64748b;">
            <span v-if="garageData.dong_co_model?.ma_luc"><i class="bi bi-lightning-charge me-1 text-warning"></i>{{ garageData.dong_co_model.ma_luc }} HP</span>
            <span v-if="garageData.dong_co_model?.ma_model" class="fst-italic"><i class="bi bi-upc-scan me-1"></i>{{ garageData.dong_co_model.ma_model }}</span>
            <span v-if="garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau"><i class="bi bi-calendar3 me-1"></i>Năm: {{ garageData.dong_co_model?.nam_san_xuat || garageData.dong_co_model?.nam_bat_dau || '' }}</span>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0" style="min-height: 38px;">
          <router-link
            :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`"
            class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-2 fw-semibold">
            <i class="bi bi-search"></i>Tra phụ tùng
          </router-link>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="card border-0 flex-grow-1 machine-main-card">
      <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center" style="padding: 16px 24px; min-height: 70px; border-bottom: 1px solid #e2e8f0 !important;">
        <h5 class="mb-0 garage-detail-title">Phụ tùng đã lưu trong động cơ</h5>
      </div>

      <div class="card-body p-4">
        <!-- Loading Skeleton -->
        <div v-if="isLoading">
          <div v-for="i in 2" :key="i" class="mb-4">
            <div class="placeholder-glow mb-3">
              <div class="placeholder col-2" style="height: 16px;"></div>
            </div>
            <div v-for="j in 3" :key="j" class="d-flex align-items-center gap-3 py-3 border-bottom">
              <div class="placeholder rounded-2" style="width: 60px; height: 60px;"></div>
              <div class="flex-grow-1 placeholder-glow">
                <div class="placeholder col-4 mb-1" style="height: 14px;"></div>
                <div class="placeholder col-3" style="height: 12px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="groups.length === 0" class="text-center py-5 d-flex flex-column align-items-center justify-content-center" style="min-height: 360px;">
          <div class="empty-icon-wrap mb-4">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5 class="mb-2 garage-detail-title">Chưa có phụ tùng nào</h5>
          <p class="text-muted mb-4" style="font-size: 14.5px; max-width: 440px; line-height: 1.6;">Xem sơ đồ kỹ thuật chi tiết của động cơ và bấm lưu các phụ tùng cần bảo dưỡng định kỳ.</p>
          <router-link :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-search"></i>
            <span>Tra cứu phụ tùng</span>
          </router-link>
        </div>

        <!-- Grouped Parts -->
        <div v-else class="d-flex flex-column gap-4">
          <div v-for="group in groups" :key="group.nhom_id || group.nhom_ten">

            <!-- Group Header -->
            <div class="d-flex align-items-center gap-2 mb-2">
              <div style="width: 3px; height: 16px; background: var(--jpm-color-red-marine, #F82249); border-radius: 2px;"></div>
              <h6 class="mb-0 fw-bold" style="font-size: 12.5px; letter-spacing: 0.5px; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ group.nhom_ten }}
              </h6>
              <span class="badge" style="background-color: #f1f5f9; color: #64748b; font-size: 11px; border-radius: var(--jpm-radius-badge, 6px);">
                {{ group.items.length }} phụ tùng
              </span>
            </div>

            <!-- Parts List in Group -->
            <div class="border rounded-3 overflow-hidden" style="border-color: #e2e8f0 !important;">
              <div
                v-for="(item, idx) in group.items"
                :key="item.id"
                class="d-flex align-items-center gap-3 p-3 part-row-item"
                :style="idx < group.items.length - 1 ? 'border-bottom: 1px solid #f1f5f9;' : ''"
                @click="router.push('/chi-tiet-phu-tung/' + item.phu_tung?.ma_phu_tung)">

                <!-- Part Image -->
                <div class="flex-shrink-0 d-flex align-items-center justify-content-center border rounded-2" style="width: 60px; height: 60px; background: #fff; border-color: #e2e8f0 !important;">
                  <img
                    v-if="item.phu_tung?.hinh_anh"
                    :src="getImageUrl(item.phu_tung.hinh_anh)"
                    :alt="item.phu_tung.ten_phu_tung"
                    style="max-width: 50px; max-height: 48px; object-fit: contain;">
                  <i v-else class="bi bi-gear text-muted" style="font-size: 1.3rem; opacity: 0.4;"></i>
                </div>

                <!-- Part Info -->
                <div class="flex-grow-1 min-width-0">
                  <router-link
                    :to="`/chi-tiet-phu-tung/${item.phu_tung?.ma_phu_tung}`"
                    @click.stop
                    class="fw-bold d-block text-decoration-none text-truncate hover-red"
                    style="color: var(--jpm-color-navy-dark, #0A1128); font-size: 14.5px;">
                    {{ item.phu_tung?.ten_phu_tung }}
                  </router-link>
                  <div class="d-flex align-items-center gap-3 flex-wrap mt-1" style="font-size: 12.5px; color: #64748b;">
                    <span>Part #: <strong class="text-dark">{{ item.phu_tung?.ma_phu_tung }}</strong></span>
                    <span v-if="item.ghi_chu"><i class="bi bi-info-circle me-1"></i>{{ item.ghi_chu }}</span>
                  </div>
                  <div class="mt-1" style="font-size: 13px;">
                    <span v-if="item.phu_tung?.is_gia_lien_he" class="fw-bold" style="color: var(--jpm-color-red-marine, #F82249);">Liên hệ</span>
                    <span v-else class="fw-bold" style="color: var(--jpm-color-red-marine, #F82249);">
                      {{ formatCurrency(item.phu_tung?.gia_ban) }}
                    </span>
                  </div>
                </div>

                <!-- Quantity & Actions -->
                <div class="d-flex align-items-center gap-3 flex-shrink-0 pe-2">
                  <!-- Add to cart -->
                  <button class="btn p-0 action-btn" title="Thêm giỏ hàng" @click.stop="addOneToCart(item)" style="border: none; background: transparent;">
                    <i class="bi bi-cart-plus" style="font-size: 1.25rem; color: #2563EB;"></i>
                  </button>
                  <!-- Remove -->
                  <button class="btn p-0 action-btn ms-2" title="Xóa khỏi danh sách" @click.stop="removeItem(item.id)" style="border: none; background: transparent;">
                    <i class="bi bi-trash3 hover-red" style="font-size: 1.25rem; color: #94a3b8;"></i>
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
.machine-header-card,
.machine-main-card {
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04) !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: #ffffff;
}
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
.btn-outline-secondary {
  border-color: #cbd5e1 !important;
  color: #475569 !important;
  font-weight: 600;
  border-radius: var(--jpm-radius-control, 8px) !important;
  transition: all 0.2s ease;
}
.btn-outline-secondary:hover {
  background-color: #f1f5f9 !important;
  border-color: #94a3b8 !important;
  color: var(--jpm-color-navy-dark, #0A1128) !important;
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

