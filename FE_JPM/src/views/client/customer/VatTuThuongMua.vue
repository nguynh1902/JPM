<template>
<div class="flex-grow-1 d-flex flex-column">
    <div class="card border-0 flex-grow-1" style="box-shadow: var(--shadow-sm); border-radius: 12px;">
      <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center" style="padding: 20px 24px;">
        <div>
          <h5 class="mb-0" style="color: var(--heading-color); font-weight: 700;">DANH SÁCH LƯU TRỮ</h5>
          <span class="text-muted" style="font-size: 13px;">Quản lý các sản phẩm bạn thường xuyên mua lại ({{ items.length }} mục)</span>
        </div>
        <router-link to="/san-pham" class="btn btn-danger rounded-1 text-uppercase border-0" style="height: 38px; padding: 0 16px; font-size: 11px; font-weight: 600; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px;">
          <i class="bi bi-plus-lg"></i>Thêm sản phẩm
        </router-link>
      </div>
      
      <!-- Loading State -->
      <div class="card-body p-4 text-center" v-if="isLoading">
        <div class="spinner-border text-danger mt-4" role="status"></div>
        <p class="mt-2 text-muted">Đang tải danh sách...</p>
      </div>

      <div class="card-body p-4" v-else>
        <!-- Empty State -->
        <div class="text-center py-5" v-if="items.length === 0">
          <i class="bi bi-bookmark-heart text-muted" style="font-size: 3.5rem; opacity: 0.35;"></i>
          <h6 class="mt-3 mb-1" style="color: var(--heading-color); font-weight: 700;">Danh sách trống</h6>
          <p class="text-muted mb-0" style="font-size: 14px;">Bạn chưa lưu sản phẩm nào vào danh sách này.</p>
        </div>

        <!-- List State -->
        <div class="d-flex flex-column gap-3" v-else>
          <div v-for="item in items" :key="item.id" 
               class="p-3 border rounded-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" 
               style="background-color: #fff; transition: border-color 0.2s; cursor: pointer;" 
               @mouseenter="$event.currentTarget.style.borderColor='var(--accent-color)'" 
               @mouseleave="$event.currentTarget.style.borderColor='#dee2e6'"
               @click="router.push(`/chi-tiet-san-pham/${item.san_pham?.slug}`)">
            
            <div class="d-flex align-items-center gap-3">
              <!-- Image -->
              <div class="d-flex align-items-center justify-content-center border rounded" style="width: 60px; height: 60px; background-color: #fff; flex-shrink: 0;">
                <img v-if="item.san_pham?.hinh_anh_chinh" :src="item.san_pham.hinh_anh_chinh" :alt="item.san_pham.ten_san_pham" style="max-height: 40px; max-width: 50px; object-fit: contain;">
                <i v-else class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
              </div>
              
              <!-- Info -->
              <div>
                <!-- Category -->
                <div class="mb-1 text-danger fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                  {{ item.san_pham?.danh_muc?.ten_danh_muc || 'CHƯA PHÂN LOẠI' }}
                </div>
                <!-- Mã & Tên sản phẩm -->
                <h6 class="mb-1 d-flex align-items-center gap-4" style="font-size: 15px; font-weight: 700; margin-top: 4px;">
                  <span class="text-muted" style="opacity: 0.85;">{{ item.san_pham?.ma_san_pham }}</span>
                  <span class="hover-red" style="color: #111827; transition: color 0.2s;">
                    {{ item.san_pham?.ten_san_pham }}
                  </span>
                </h6>
                <p class="mb-0 mt-1 text-muted" style="font-size: 12.5px;" v-if="item.ghi_chu"><i class="bi bi-stickies me-1"></i>Ghi chú: {{ item.ghi_chu }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="d-flex align-items-center gap-4 pe-3">
              <!-- Nút Thêm giỏ hàng (An toàn -> Trái) -->
              <button class="btn p-0 action-btn" title="Thêm giỏ hàng" @click.stop="addToQuote(item.san_pham)" style="border: none; background: transparent;">
                <i class="bi bi-cart-plus" style="font-size: 1.25rem; color: #2563EB; transition: color 0.2s;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563EB'"></i>
              </button>
              <!-- Nút Xóa (Nguy hiểm -> Đẩy ra xa nhất bên Phải) -->
              <button class="btn p-0 action-btn" title="Bỏ lưu" @click.stop="removeItem(item.san_pham_id)" style="border: none; background: transparent;">
                <i class="bi bi-trash3 hover-red" style="font-size: 1.25rem; color: #f82249;"></i>
              </button>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
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
.hover-red:hover {
  color: var(--accent-color) !important;
}
</style>










