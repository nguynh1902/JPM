<template>
  <div class="h-100 d-flex flex-column flex-grow-1">
    <div class="card border-0 jpm-card h-100 d-flex flex-column flex-grow-1">
      <!-- Card Header: Đồng bộ chuẩn 100% với các trang tài khoản khác -->
      <div class="card-header jpm-card-header">
        <h5 class="jpm-card-title mb-0">Vật tư thường mua</h5>
      </div>
      
      <!-- Loading State -->
      <div class="card-body px-4 py-3 text-center d-flex flex-column justify-content-center flex-grow-1" v-if="isLoading">
        <div class="spinner-border text-danger mt-4" role="status"></div>
        <p class="mt-2 text-muted">Đang tải danh sách vật tư...</p>
      </div>

      <div class="card-body px-4 py-3 d-flex flex-column flex-grow-1" v-else>
        <!-- Empty State -->
        <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center flex-grow-1 my-auto" v-if="items.length === 0" style="min-height: 320px;">
          <div class="empty-icon-wrap mb-3">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5 class="mb-2 vt-heading">Danh sách vật tư trống</h5>
          <p class="text-muted mb-4" style="font-size: 14.5px; max-width: 440px; line-height: 1.6;">
            Bạn chưa lưu sản phẩm nào. Hãy bấm biểu tượng lưu trên trang sản phẩm để theo dõi phụ tùng thường mua.
          </p>
          <router-link to="/san-pham" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-grid"></i>
            <span>Khám phá sản phẩm</span>
          </router-link>
        </div>

        <!-- Grouped by Category: Mỗi danh mục là 1 nhóm, bên trong là các card sản phẩm tiêu chuẩn -->
        <div class="d-flex flex-column" v-else>
          <div v-for="cat in groupedCategories" :key="cat.id" class="mb-4">
            <!-- Category Group Header: Thanh tiêu đề danh mục -->
            <div class="d-flex align-items-center gap-2 mb-3">
              <div style="width: 4px; height: 18px; background: var(--jpm-color-red-marine, #F82249); border-radius: 2px;"></div>
              <h6 class="mb-0 fw-bold" style="font-size: 15px; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ cat.name }}
              </h6>
              <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-1 fw-semibold" style="font-size: 11.5px;">
                {{ cat.items.length }} sản phẩm
              </span>
            </div>

            <!-- Danh sách các card sản phẩm tiêu chuẩn -->
            <div class="d-flex flex-column gap-3">
              <div
                v-for="item in cat.items"
                :key="item.id"
                class="p-3 border vt-product-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
                @click="router.push(`/chi-tiet-san-pham/${item.san_pham?.slug}`)"
              >
                <!-- Bên trái: Hình ảnh + Thông tin sản phẩm -->
                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                  <!-- Hình ảnh sản phẩm (72x72px rõ ràng, sắc nét) -->
                  <div class="vt-img-wrap rounded-2 border flex-shrink-0 d-flex align-items-center justify-content-center bg-white">
                    <img
                      v-if="item.san_pham?.hinh_anh_chinh"
                      :src="getImageUrl(item.san_pham.hinh_anh_chinh)"
                      :alt="item.san_pham.ten_san_pham"
                      class="vt-product-img"
                    />
                    <i v-else class="bi bi-image text-muted fs-2 opacity-50"></i>
                  </div>

                  <!-- Thông tin chi tiết: Tên, Mã, Giá -->
                  <div class="flex-grow-1 min-w-0">
                    <h6 class="fw-bold mb-2 text-truncate vt-product-title" :title="item.san_pham?.ten_san_pham">
                      {{ item.san_pham?.ten_san_pham }}
                    </h6>
                    <!-- Dòng Mã & Giá: Khoảng cách cố định min-width để mã ngắn/dài đều thẳng hàng -->
                    <div class="d-flex align-items-center flex-wrap" style="column-gap: 20px; row-gap: 6px;">
                      <!-- Mã sản phẩm: Bỏ icon, căn min-width cố định -->
                      <div class="vt-code-box">
                        <span class="part-code-badge">{{ item.san_pham?.ma_san_pham }}</span>
                      </div>

                      <!-- Giá sản phẩm: Vị trí luôn cố định thẳng hàng -->
                      <div class="vt-price-box">
                        <span v-if="item.san_pham?.is_gia_lien_he" class="fw-bold text-danger fs-6">Liên hệ</span>
                        <span v-else-if="item.san_pham?.gia_ban > 0" class="fw-bold text-danger fs-6">
                          {{ formatCurrency(item.san_pham?.gia_ban) }}
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

                <!-- Bên phải: Hai nút thao tác dạng icon đồng bộ 38x38px -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0 pe-1 align-self-end align-self-md-center">
                  <!-- Nút Thêm giỏ: Dạng icon đồng bộ -->
                  <button
                    type="button"
                    class="btn btn-cart-icon"
                    title="Thêm vào giỏ hàng"
                    @click.stop="addToQuote(item.san_pham)"
                  >
                    <i class="bi bi-cart-plus"></i>
                  </button>

                  <!-- Nút Xóa: Dạng icon đồng bộ -->
                  <button
                    type="button"
                    class="btn btn-delete-icon"
                    title="Bỏ lưu vật tư này"
                    @click.stop="removeItem(item.san_pham_id)"
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
import { computed } from "vue";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axiosClient from "../../../api/axiosClient";
import Swal from "sweetalert2";
import { createToaster } from "@meforma/vue-toaster";
import { useRouter } from "vue-router";
import { useCartStore } from "../../../stores/cartStore";

const queryClient = useQueryClient();
const toaster = createToaster({ position: "top-right" });
const router = useRouter();
const cartStore = useCartStore();

const getImageUrl = (path) => {
  if (!path) return "";
  if (path.startsWith("blob:") || path.startsWith("http") || path.startsWith("data:")) return path;
  return `http://localhost:8000${path.startsWith("/") ? "" : "/"}${path}`;
};

const formatCurrency = (val) => {
  if (!val || isNaN(val)) return 'Liên hệ';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
};

// 1. Fetch data
const { data, isLoading } = useQuery({
  queryKey: ["vatTuLuuTru"],
  queryFn: async () => await axiosClient.get("/client/vat-tu-luu-tru"),
  staleTime: 5 * 60 * 1000,
});

const items = computed(() => data.value || []);

// 2. Nhóm vật tư theo danh mục (Category)
const groupedCategories = computed(() => {
  if (!items.value || items.value.length === 0) return [];

  const map = new Map();
  items.value.forEach((item) => {
    const catId = item.san_pham?.danh_muc?.id || "other";
    const catName = item.san_pham?.danh_muc?.ten_danh_muc || "Vật tư khác";

    if (!map.has(catId)) {
      map.set(catId, {
        id: catId,
        name: catName,
        items: [],
      });
    }
    map.get(catId).items.push(item);
  });

  return Array.from(map.values());
});

// 3. Remove Item Mutation (Optimistic UI 0ms)
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

// 4. Add to Quote / Cart helper
const addToQuote = (sanPham) => {
  cartStore.addItem(sanPham, 1);
  toaster.success(`Đã thêm ${sanPham.ma_san_pham} vào giỏ hàng!`);
};
</script>

<style scoped>
.vt-heading {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 700;
  letter-spacing: -0.01em;
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

/* Card sản phẩm độc lập tiêu chuẩn, rộng rãi */
.vt-product-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: #ffffff;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 2px 6px rgba(10, 17, 40, 0.03);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  cursor: pointer;
}

.vt-product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(10, 17, 40, 0.06);
  border-color: #cbd5e1 !important;
}

.vt-img-wrap {
  width: 72px;
  height: 72px;
  padding: 6px;
  border-color: #e2e8f0 !important;
}

.vt-product-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.vt-product-title {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 15.5px;
  line-height: 1.4;
}

/* Cột Mã & Cột Giá cố định chiều rộng tối thiểu để luôn thẳng hàng */
.vt-code-box {
  min-width: 110px;
}

.vt-price-box {
  min-width: 120px;
}

/* Hai nút thao tác dạng icon 38x38px đồng bộ 100% */
.btn-cart-icon {
  width: 38px;
  height: 38px;
  border-radius: var(--jpm-radius-control, 8px);
  background-color: var(--jpm-color-red-marine, #F82249);
  border: 1px solid var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(248, 34, 73, 0.2);
}

.btn-cart-icon:hover {
  background-color: #e01a3f;
  border-color: #e01a3f;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.3);
}

.btn-delete-icon {
  width: 38px;
  height: 38px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  color: #94a3b8;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 1.1rem;
}

.btn-delete-icon:hover {
  background-color: #fee2e2;
  border-color: #fca5a5;
  color: var(--jpm-color-red-marine, #F82249);
  transform: translateY(-1px);
}
</style>
