<template>
  <div class="flex-grow-1 d-flex flex-column">

    <!-- Back button + Header -->
    <div class="mb-4">
      <router-link to="/garage" class="d-inline-flex align-items-center gap-2 text-muted mb-3" style="font-size: 13px; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Quay lại Garage
      </router-link>

      <!-- Skeleton Header -->
      <div v-if="isLoading" class="p-4 border rounded-2 bg-white">
        <div class="placeholder-glow">
          <div class="placeholder col-3 mb-2" style="height: 20px;"></div>
          <div class="placeholder col-2" style="height: 14px;"></div>
        </div>
      </div>

      <!-- Machine Header Card -->
      <div v-else-if="garageData" class="p-4 border rounded-2 bg-white d-flex align-items-start justify-content-between flex-column flex-md-row gap-3">
        <div>
          <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h5 class="fw-bold mb-0" style="color: var(--heading-color);">{{ garageData.dong_co_model?.ten_model || 'Chưa rõ Model' }}</h5>
            <span class="badge rounded-pill fw-semibold text-uppercase" style="background-color: rgba(239,35,60,0.1); color: var(--accent-color); font-size: 10px; letter-spacing: 0.5px;">
              {{ garageData.dong_co_model?.thuong_hieu?.name }}
            </span>
          </div>
          
          <div class="d-flex align-items-center gap-3 flex-wrap mt-2" style="font-size: 13px; color: #6c757d;">
            <span v-if="garageData.dong_co_model?.ma_luc"><i class="bi bi-lightning-charge me-1"></i>{{ garageData.dong_co_model.ma_luc }} HP</span>
            <span v-if="garageData.dong_co_model?.ma_model" class="text-muted fst-italic">{{ garageData.dong_co_model.ma_model }}</span>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0" style="min-height: 38px;">
          
          
          <router-link
            :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`"
            class="btn btn-light btn-sm px-3 rounded-pill d-flex align-items-center gap-2" style="font-size: 12px; font-weight: 600; color: #495057; border: 1px solid #dee2e6;">
            <i class="bi bi-search"></i>Tra phụ tùng
          </router-link>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="card border-0 flex-grow-1" style="box-shadow: var(--shadow-sm);">
      <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center" style="padding: 18px 24px;">
        <div>
          <h6 class="mb-0 fw-bold" style="color: var(--heading-color);">PHỤ TÙNG ĐÃ LƯU</h6>
          <span class="text-muted" style="font-size: 13px;">{{ totalItems }} phụ tùng trong {{ groups.length }} nhóm</span>
        </div>
      </div>

      <div class="card-body p-4">
        <!-- Loading Skeleton -->
        <div v-if="isLoading">
          <div v-for="i in 2" :key="i" class="mb-4">
            <div class="placeholder-glow mb-3">
              <div class="placeholder col-2" style="height: 16px;"></div>
            </div>
            <div v-for="j in 3" :key="j" class="d-flex align-items-center gap-3 py-3 border-bottom">
              <div class="placeholder" style="width: 60px; height: 60px; border-radius: 6px;"></div>
              <div class="flex-grow-1 placeholder-glow">
                <div class="placeholder col-4 mb-1" style="height: 14px;"></div>
                <div class="placeholder col-3" style="height: 12px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="groups.length === 0" class="text-center py-5">
          <i class="bi bi-box-seam text-muted" style="font-size: 3.5rem; opacity: 0.35;"></i>
          <h6 class="mt-3 mb-1" style="color: var(--heading-color); font-weight: 700;">Chưa có phụ tùng nào</h6>
          <p class="text-muted mb-3" style="font-size: 14px;">Xem sơ đồ phụ tùng và lưu những phụ tùng bạn cần.</p>
          <router-link :to="`/tra-cuu?brand=${garageData?.dong_co_model?.thuong_hieu_id}&engineType=${garageData?.dong_co_model?.loai_dong_co_id}&model=${garageData?.dong_co_model_id}&year=${garageData?.dong_co_model?.nam_san_xuat || ''}`" class="btn btn-danger rounded-1 text-uppercase" style="font-size: 11px; font-weight: 600; letter-spacing: 1px;">
            <i class="bi bi-search me-2"></i>Tra cứu phụ tùng
          </router-link>
        </div>

        <!-- Grouped Parts -->
        <div v-else class="d-flex flex-column gap-4">
          <div v-for="group in groups" :key="group.nhom_id || group.nhom_ten">

            <!-- Group Header -->
            <div class="d-flex align-items-center gap-2 mb-2">
              <div style="width: 3px; height: 18px; background: var(--accent-color); border-radius: 2px;"></div>
              <h6 class="mb-0 fw-bold text-uppercase" style="font-size: 11.5px; letter-spacing: 0.8px; color: var(--heading-color);">
                {{ group.nhom_ten }}
              </h6>
              <span class="badge rounded-pill" style="background-color: #f1f3f5; color: #6c757d; font-size: 10px;">
                {{ group.items.length }} phụ tùng
              </span>
            </div>

            <!-- Parts List in Group -->
            <div class="border rounded-2 overflow-hidden" style="border-color: #e9ecef !important;">
              <div
                v-for="(item, idx) in group.items"
                :key="item.id"
                class="d-flex align-items-center gap-3 p-3"
                :style="idx < group.items.length - 1 ? 'border-bottom: 1px solid #f1f3f5;' : ''"
                @mouseenter="$event.currentTarget.style.backgroundColor='#fafbfc'"
                @mouseleave="$event.currentTarget.style.backgroundColor=''"
                style="cursor: pointer;"
                @click="router.push('/chi-tiet-phu-tung/' + item.phu_tung?.ma_phu_tung)">

                

                <!-- Part Image -->
                <div class="flex-shrink-0 d-flex align-items-center justify-content-center border rounded" style="width: 60px; height: 60px; background: #fff;">
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
                    class="fw-bold d-block text-decoration-none text-truncate"
                    style="color: var(--heading-color); font-size: 14px;">
                    {{ item.phu_tung?.ten_phu_tung }}
                  </router-link>
                  <div class="d-flex align-items-center gap-3 flex-wrap mt-1" style="font-size: 12px; color: #6c757d;">
                    <span>Part #: <strong class="text-dark">{{ item.phu_tung?.ma_phu_tung }}</strong></span>
                    <span v-if="item.ghi_chu"><i class="bi bi-info-circle me-1"></i>{{ item.ghi_chu }}</span>
                  </div>
                  <div class="mt-1" style="font-size: 12.5px;">
                    <span v-if="item.phu_tung?.is_gia_lien_he" class="fw-bold" style="color: var(--accent-color);">Liên hệ</span>
                    <span v-else class="fw-bold" style="color: var(--accent-color);">
                      {{ formatCurrency(item.phu_tung?.gia_ban) }}
                    </span>
                  </div>
                </div>

                                <!-- Quantity & Actions -->
                <div class="d-flex align-items-center gap-4 flex-shrink-0 pe-2">
                  <!-- Add to cart -->
                  <button class="btn p-0 action-btn" title="Thêm giỏ hàng" @click.stop="addOneToCart(item)" style="border: none; background: transparent;">
                    <i class="bi bi-cart-plus" style="font-size: 1.25rem; color: #2563EB; transition: color 0.2s;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563EB'"></i>
                  </button>
                  <!-- Remove -->
                  <button class="btn p-0 action-btn ms-2" title="Xóa khỏi danh sách" @click.stop="removeItem(item.id)" style="border: none; background: transparent;">
                    <i class="bi bi-trash3 hover-red" style="font-size: 1.25rem; color: #f82249;"></i>
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








