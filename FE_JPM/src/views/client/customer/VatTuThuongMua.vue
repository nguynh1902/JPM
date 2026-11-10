<template>
<div class="flex-grow-1 d-flex flex-column">
    <div class="card border-0 flex-grow-1 vt-main-card">
      <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center" style="padding: 16px 24px; min-height: 70px; border-bottom: 1px solid #e2e8f0 !important;">
        <h5 class="mb-0 vt-heading">Vật tư thường mua</h5>
        <router-link to="/san-pham" class="btn btn-blue-primary btn-sm d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold">
          <i class="bi bi-plus-lg"></i>
          <span>Thêm sản phẩm mới</span>
        </router-link>
      </div>
      
      <!-- Loading State -->
      <div class="card-body p-4 text-center" v-if="isLoading">
        <div class="spinner-border text-danger mt-4" role="status"></div>
        <p class="mt-2 text-muted">Đang tải danh sách vật tư...</p>
      </div>

      <div class="card-body p-4" v-else>
        <!-- Empty State -->
        <div class="text-center py-5 d-flex flex-column align-items-center justify-content-center" v-if="items.length === 0" style="min-height: 380px;">
          <div class="empty-icon-wrap mb-4">
            <i class="bi bi-bookmark-heart"></i>
          </div>
          <h5 class="mb-2 vt-heading">Danh sách vật tư trống</h5>
          <p class="text-muted mb-4" style="font-size: 14.5px; max-width: 440px; line-height: 1.6;">Bạn chưa lưu sản phẩm nào. Hãy bấm biểu tượng lưu trên trang sản phẩm để theo dõi phụ tùng thường mua.</p>
          <router-link to="/san-pham" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-grid"></i>
            <span>Khám phá sản phẩm</span>
          </router-link>
        </div>

        <!-- List State -->
        <div class="d-flex flex-column gap-3" v-else>
          <div v-for="item in items" :key="item.id" 
               class="p-3 border vt-item-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" 
               @click="router.push(`/chi-tiet-san-pham/${item.san_pham?.slug}`)">
            
            <div class="d-flex align-items-center gap-3">
              <!-- Image -->
              <div class="d-flex align-items-center justify-content-center border rounded-2" style="width: 64px; height: 64px; background-color: #fff; flex-shrink: 0;">
                <img v-if="item.san_pham?.hinh_anh_chinh" :src="getImageUrl(item.san_pham.hinh_anh_chinh)" :alt="item.san_pham.ten_san_pham" style="max-height: 48px; max-width: 54px; object-fit: contain;">
                <i v-else class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
              </div>
              
              <!-- Info -->
              <div>
                <!-- Category -->
                <div class="mb-1">
                  <span class="badge category-tag-badge">
                    {{ item.san_pham?.danh_muc?.ten_danh_muc || 'CHƯA PHÂN LOẠI' }}
                  </span>
                </div>
                <!-- Mã & Tên sản phẩm -->
                <h6 class="mb-1 d-flex align-items-center gap-3 flex-wrap" style="font-size: 15px; font-weight: 700; margin-top: 4px;">
                  <span class="text-muted" style="opacity: 0.85; font-size: 13.5px;"><i class="bi bi-upc-scan me-1"></i>{{ item.san_pham?.ma_san_pham }}</span>
                  <span class="hover-red" style="color: var(--jpm-color-navy-deep, #0A1128); transition: color 0.2s;">
                    {{ item.san_pham?.ten_san_pham }}
                  </span>
                </h6>
                <p class="mb-0 mt-1 text-muted" style="font-size: 12.5px;" v-if="item.ghi_chu"><i class="bi bi-stickies me-1"></i>Ghi chú: {{ item.ghi_chu }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="d-flex align-items-center gap-3 pe-2 flex-shrink-0">
              <!-- Nút Thêm giỏ hàng -->
              <button class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2" title="Thêm giỏ hàng" @click.stop="addToQuote(item.san_pham)">
                <i class="bi bi-cart-plus text-primary" style="font-size: 1.1rem;"></i>
                <span>Thêm giỏ</span>
              </button>
              <!-- Nút Xóa -->
              <button class="btn p-0 action-btn ms-1" title="Bỏ lưu" @click.stop="removeItem(item.san_pham_id)" style="border: none; background: transparent;">
                <i class="bi bi-trash3 hover-red" style="font-size: 1.25rem; color: #94a3b8;"></i>
              </button>
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
  return `http://localhost:8000${path.startsWith("/") ? "" : "/"}${path}`;
};

import { computed } from "vue";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axiosClient from "../../../api/axiosClient";
import Swal from "sweetalert2";
import { createToaster } from "@meforma/vue-toaster";
import { useRouter } from "vue-router";

const queryClient = useQueryClient();
const toaster = createToaster({ position: "top-right" });
const router = useRouter();

// 1. Fetch data
const { data, isLoading } = useQuery({
  queryKey: ["vatTuLuuTru"],
  queryFn: async () => await axiosClient.get("/client/vat-tu-luu-tru"),
  staleTime: 5 * 60 * 1000,
});

const items = computed(() => data.value || []);

// 2. Remove Item Mutation (Optimistic UI 0ms)
const deleteMutation = useMutation({
  mutationFn: (sanPhamId) => axiosClient.delete(`/client/vat-tu-luu-tru/${sanPhamId}`),
  onMutate: async (sanPhamId) => {
    await queryClient.cancelQueries({ queryKey: ["vatTuLuuTru"] });
    const previousItems = queryClient.getQueryData(["vatTuLuuTru"]);
    
    queryClient.setQueryData(["vatTuLuuTru"], (old) => {
      const list = Array.isArray(old) ? old : [];
      return list.filter(item => item.san_pham_id !== sanPhamId);
    });
    
    return { previousItems };
  },
  onError: (err, sanPhamId, context) => {
    queryClient.setQueryData(["vatTuLuuTru"], context.previousItems);
    toaster.error("Có lỗi xảy ra, không thể xóa!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ["vatTuLuuTru"] });
  },
  onSuccess: () => {
    toaster.success("Đã xóa khỏi danh sách lưu trữ!");
  }
});

const removeItem = async (sanPhamId) => {
  const result = await Swal.fire({
    title: "Xóa khỏi danh sách?",
    text: "Bạn có muốn bỏ lưu vật tư này không?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#f82249",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Bỏ lưu",
    cancelButtonText: "Đóng",
  });
  if (result.isConfirmed) {
    deleteMutation.mutate(sanPhamId);
  }
};

// 3. Add to Quote / Cart helper
import { useCartStore } from "../../../stores/cartStore";
const cartStore = useCartStore();

const addToQuote = (sanPham) => {
  cartStore.addItem(sanPham, 1);
  toaster.success(`Đã thêm ${sanPham.ma_san_pham} vào giỏ hàng!`);
};
</script>

<style scoped>
.vt-main-card {
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04) !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: #ffffff;
}
.vt-heading {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 700;
  letter-spacing: -0.01em;
}
.vt-item-card {
  border-radius: var(--jpm-radius-control, 8px) !important;
  background-color: #ffffff;
  border: 1px solid #e2e8f0 !important;
  transition: all 0.2s ease;
  cursor: pointer;
}
.vt-item-card:hover {
  border-color: rgba(248, 34, 73, 0.4) !important;
  box-shadow: 0 4px 12px rgba(10, 17, 40, 0.04);
}
.category-tag-badge {
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
.btn-blue-primary {
  background-color: #0d6efd !important;
  border-color: #0d6efd !important;
  color: #ffffff !important;
  border-radius: var(--jpm-radius-control, 8px) !important;
  font-weight: 600;
  transition: all 0.2s ease;
}
.btn-blue-primary:hover {
  background-color: #0b5ed7 !important;
  border-color: #0a58ca !important;
  color: #ffffff !important;
}

.hover-red:hover {
  color: var(--jpm-color-red-marine, #F82249) !important;
}
</style>










