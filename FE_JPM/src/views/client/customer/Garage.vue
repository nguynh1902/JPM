<template>
  <div class="h-100 d-flex flex-column flex-grow-1">
    <div class="card border-0 jpm-card h-100 d-flex flex-column flex-grow-1">
      <div class="card-header jpm-card-header">
        <h5 class="jpm-card-title">Danh sách động cơ</h5>
      </div>
      <div class="card-body px-4 py-3 d-flex flex-column flex-grow-1">

        <!-- Loading Skeleton -->
        <div v-if="isLoadingGarage">
          <div v-for="i in 2" :key="i" class="p-3 border rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-2 bg-white">
            <div class="d-flex align-items-center gap-3">
              <div class="placeholder-glow rounded-3" style="width: 48px; height: 48px;">
                <div class="placeholder w-100 h-100 rounded-3"></div>
              </div>
              <div class="placeholder-glow w-100">
                <div class="placeholder col-4 mb-2" style="height: 14px;"></div>
                <br>
                <div class="placeholder col-8" style="height: 18px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center flex-grow-1 my-auto" v-else-if="garage.length === 0" style="min-height: 320px;">
          <div class="empty-icon-wrap mb-3">
            <i class="bi bi-tools"></i>
          </div>
          <h5 class="mb-2 garage-heading">Garage của bạn đang trống</h5>
          <p class="text-muted mb-4" style="font-size: 0.85rem; max-width: 480px; line-height: 1.55;">
            Hãy tra cứu động cơ và sơ đồ phụ tùng trên trang Tra Cứu, sau đó bấm <strong>"Lưu vào Garage"</strong>. Hệ thống sẽ tự động khởi tạo hồ sơ máy tương ứng cho garage của bạn.
          </p>
          <button class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" @click="router.push('/tra-cuu')">
            <i class="bi bi-search"></i>
            <span>Đến trang Tra cứu sơ đồ</span>
          </button>
        </div>

        <!-- Garage List: Nhóm theo Thương hiệu (Mercury, Yamaha, v.v. - Tương tự như Danh mục của Vật tư thường mua) -->
        <div class="d-flex flex-column" v-else>
          <div v-for="brand in groupedBrands" :key="brand.id" class="mb-4">
            <!-- Brand Group Header: Thanh tiêu đề thương hiệu bên ngoài -->
            <div class="d-flex align-items-center gap-2 mb-3">
              <div style="width: 4px; height: 18px; background: var(--jpm-color-red-marine, #F82249); border-radius: var(--jpm-radius-badge, 6px);"></div>
              <h6 class="mb-0 fw-bold" style="font-size: 0.95rem; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ brand.name }}
              </h6>
            </div>

            <!-- Danh sách các card động cơ tiêu chuẩn -->
            <div class="d-flex flex-column gap-3">
              <div
                v-for="may in brand.items"
                :key="may.id"
                class="border garage-item-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
                @click="router.push(`/garage/${may.id}`)"
              >
                <!-- Bên trái: Thông tin chi tiết động cơ -->
                <div class="flex-grow-1 min-w-0">
                  <!-- Dòng trên: Tên Model máy -->
                  <h6 class="fw-bold mb-1 text-truncate garage-model-title" :title="may.dong_co_model?.ten_model">
                    {{ may.dong_co_model?.ten_model || 'Chưa rõ Model' }}
                  </h6>

                  <!-- Dòng dưới: Thông số tối giản, phân cách thanh lịch bằng vách ngăn | -->
                  <div class="garage-specs-row d-flex align-items-center flex-wrap">
                    <!-- HP -->
                    <span v-if="may.dong_co_model?.ma_luc" class="spec-item">
                      {{ may.dong_co_model.ma_luc }} HP
                    </span>

                    <!-- Năm sản xuất -->
                    <template v-if="may.dong_co_model?.nam_san_xuat || may.dong_co_model?.nam_bat_dau">
                      <span v-if="may.dong_co_model?.ma_luc" class="spec-divider">|</span>
                      <span class="spec-item">
                        Năm {{ may.dong_co_model?.nam_san_xuat || may.dong_co_model?.nam_bat_dau }}
                      </span>
                    </template>

                    <!-- Loại máy -->
                    <template v-if="may.dong_co_model?.loai_dong_co?.name">
                      <span v-if="may.dong_co_model?.ma_luc || may.dong_co_model?.nam_san_xuat || may.dong_co_model?.nam_bat_dau" class="spec-divider">|</span>
                      <span class="spec-item">
                        {{ may.dong_co_model.loai_dong_co.name }}
                      </span>
                    </template>

                    <!-- Serial -->
                    <template v-if="may.so_serial">
                      <span v-if="may.dong_co_model?.ma_luc || may.dong_co_model?.nam_san_xuat || may.dong_co_model?.nam_bat_dau || may.dong_co_model?.loai_dong_co?.name" class="spec-divider">|</span>
                      <span class="spec-item text-truncate" style="max-width: 220px;" :title="'Số serial: ' + may.so_serial">
                        Số máy: {{ may.so_serial }}
                      </span>
                    </template>

                    <!-- Ghi chú nếu có -->
                    <template v-if="may.ghi_chu">
                      <span class="spec-divider">|</span>
                      <span class="spec-item text-truncate text-muted fst-italic" style="max-width: 240px;" :title="may.ghi_chu">
                        {{ may.ghi_chu }}
                      </span>
                    </template>
                  </div>
                </div>

                <!-- Bên phải: Hai nút thao tác dạng icon đồng bộ 36x36px -->
                <div class="d-flex align-items-center gap-2 pe-1 flex-shrink-0 align-self-end align-self-md-center">
                  <!-- Sơ đồ / Tra phụ tùng: Outline Primary Blue #0d6efd -->
                  <button
                    type="button"
                    class="btn btn-search-icon"
                    title="Tra cứu sơ đồ phụ tùng cho động cơ này"
                    @click.stop="router.push('/tra-cuu?brand=' + may.dong_co_model?.thuong_hieu_id + '&engineType=' + may.dong_co_model?.loai_dong_co_id + '&model=' + may.dong_co_model_id + '&year=' + (may.dong_co_model?.nam_san_xuat || ''))"
                  >
                    <i class="bi bi-search"></i>
                  </button>

                  <!-- Delete: Outline xám hover Đỏ Marine #F82249 -->
                  <button
                    type="button"
                    class="btn btn-delete-icon"
                    title="Xóa máy khỏi Garage"
                    @click.stop="deleteMay(may.id)"
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

      <!-- SAVED PARTS MODAL -->
      <div class="modal fade" id="savedPartsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
          <div class="modal-content border-0 shadow-lg" style="border-radius: var(--jpm-radius-card, 12px) !important; overflow: hidden;">
            <div class="modal-header border-bottom-0 pt-4 pb-2 px-4">
              <h5 class="modal-title garage-heading mb-0"><i class="bi bi-tools text-danger me-2"></i>Phụ Tùng Đã Lưu Của Động Cơ</h5>
              <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
              
              <div v-if="isLoadingSavedParts" class="text-center py-5">
                  <div class="spinner-border text-danger" role="status"></div>
              </div>
              
              <div v-else-if="!savedParts || savedParts.length === 0" class="text-center py-5 bg-white rounded-3 border">
                  <i class="bi bi-box-seam text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                  <p class="mt-3 mb-0 text-muted">Động cơ này chưa có phụ tùng nào được lưu.</p>
              </div>
              
              <div v-else class="row g-3">
                  <div class="col-12" v-for="item in savedParts" :key="item.id">
                      <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--jpm-radius-control, 8px) !important;">
                          <img :src="getImageUrl(item.phu_tung?.hinh_anh_chinh) || 'https://placehold.co/100x100'" alt="" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                          <div class="flex-grow-1">
                              <h6 class="fw-bold mb-1"><router-link :to="`/chi-tiet-phu-tung/${item.phu_tung?.ma_phu_tung || item.phu_tung?.id}`" class="text-dark text-decoration-none">{{ item.phu_tung?.ten_phu_tung }}</router-link></h6>
                              <p class="text-muted small mb-0">Part #: <strong>{{ item.phu_tung?.ma_phu_tung }}</strong> | Số lượng: {{ item.so_luong }}</p>
                              <p class="text-muted small mb-0" v-if="item.ghi_chu"><i class="bi bi-info-circle me-1"></i>{{ item.ghi_chu }}</p>
                          </div>
                          <div class="d-flex flex-column gap-2">
                              <button class="btn btn-danger btn-sm fw-semibold" @click="addToCart(item.phu_tung, item.so_luong)"><i class="bi bi-cart-plus me-1"></i>Thêm giỏ</button>
                              <button class="btn btn-outline-secondary btn-sm" @click="removeSavedPart(item.id)"><i class="bi bi-trash3 me-1"></i>Xóa</button>
                          </div>
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
import { ref, computed, onMounted } from "vue";
  import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/authStore";
import { useCartStore } from "../../../stores/cartStore";


import axiosClient from "../../../api/axiosClient";
import Swal from "sweetalert2";
import { createToaster } from "@meforma/vue-toaster";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

const authStore = useAuthStore();
const toaster = createToaster({ position: "top-right" });
const queryClient = useQueryClient();
  const router = useRouter();

// ===================== AVATAR =====================
const userAvatar = computed(() => {
  const user = authStore.user;
  if (user?.avatar) return user.avatar;
  return "https://ui-avatars.com/api/?name=" + encodeURIComponent(user?.name || "User") + "&size=140&background=0A1128&color=fff";
});

// ===================== GARAGE DATA =====================
const { data: garageData, isLoading: isLoadingGarage } = useQuery({
  queryKey: ["clientGarage"],
  queryFn: async () => await axiosClient.get("/client/garage"),
  staleTime: 5 * 60 * 1000,
});
const garage = computed(() => garageData.value || []);

// Nhóm động cơ theo Thương hiệu (Mercury, Yamaha, Suzuki...) tương tự như Danh mục của Vật tư thường mua
const groupedBrands = computed(() => {
  if (!garage.value || garage.value.length === 0) return [];

  const map = new Map();
  garage.value.forEach((may) => {
    const brandId = may.dong_co_model?.thuong_hieu?.id || "other";
    const brandName = may.dong_co_model?.thuong_hieu?.name || "Động cơ khác";

    if (!map.has(brandId)) {
      map.set(brandId, {
        id: brandId,
        name: brandName,
        items: [],
      });
    }
    map.get(brandId).items.push(may);
  });

  return Array.from(map.values());
});

// ===================== DELETE MUTATION =====================
const deleteMutation = useMutation({
  mutationFn: (id) => axiosClient.delete("/client/garage/" + id),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ["clientGarage"] });
    const previousGarage = queryClient.getQueryData(["clientGarage"]);
    
    queryClient.setQueryData(["clientGarage"], (old) => {
      const list = Array.isArray(old) ? old : [];
      return list.filter(item => item.id !== id);
    });
    
    return { previousGarage };
  },
  onError: (err, id, context) => {
    queryClient.setQueryData(["clientGarage"], context.previousGarage);
    toaster.error("Không thể xóa máy lúc này!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ["clientGarage"] });
  },
  onSuccess: () => {
    toaster.success("Đã xóa máy khỏi Garage!");
  },
});

const deleteMay = async (id) => {
  const result = await Swal.fire({
    title: "Xóa máy này?",
    text: "Máy sẽ bị xóa vĩnh viễn khỏi Garage của bạn.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#f82249",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Đồng ý, xóa!",
    cancelButtonText: "Hủy",
  });
  if (result.isConfirmed) {
    deleteMutation.mutate(id);
  }
};


const activeGarageId = ref(null);

const { data: savedPartsData, isLoading: isLoadingSavedParts, refetch: refetchSavedParts } = useQuery({
  queryKey: ['saved-parts', activeGarageId],
  queryFn: () => axiosClient.get(`/client/garage/${activeGarageId.value}/phu-tung`),
  enabled: computed(() => !!activeGarageId.value)
});

const savedParts = computed(() => savedPartsData.value?.data?.data || []);
let savedPartsModalInstance = null;

const openSavedParts = (id) => {
    activeGarageId.value = id;
    const el = document.getElementById('savedPartsModal');
    if (el) {
        if(!savedPartsModalInstance) savedPartsModalInstance = new window.bootstrap.Modal(el);
        savedPartsModalInstance.show();
    }
};

const removeSavedPartMutation = useMutation({
    mutationFn: (id) => axiosClient.delete(`/client/garage-phu-tung/${id}`),
    onMutate: async (id) => {
        await queryClient.cancelQueries({ queryKey: ['saved-parts', activeGarageId.value] });
        const previous = queryClient.getQueryData(['saved-parts', activeGarageId.value]);
        queryClient.setQueryData(['saved-parts', activeGarageId.value], (old) => {
            if(!old || !old.data || !old.data.data) return old;
            return {
                ...old,
                data: {
                    ...old.data,
                    data: old.data.data.filter(item => item.id !== id)
                }
            };
        });
        return { previous };
    },
    onSuccess: () => {
        toaster.success("Đã xóa phụ tùng khỏi máy!");
    },
    onError: (err, id, context) => {
        if (context?.previous) queryClient.setQueryData(['saved-parts', activeGarageId.value], context.previous);
        toaster.error("Có lỗi xảy ra khi xóa.");
    }
});

const removeSavedPart = (id) => {
    Swal.fire({
      title: 'Bỏ lưu phụ tùng?',
      text: "Bạn sẽ xóa phụ tùng này khỏi máy đang chọn?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f82249',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        removeSavedPartMutation.mutate(id);
      }
    });
};

const cartStore = useCartStore();
const addToCart = (item, qty = 1) => {
    cartStore.addItem(item, qty);
    toaster.success("Đã thêm vào giỏ!");
};

const getImageUrl = (path) => {
  if (!path) return null;
  if (path.startsWith("blob:") || path.startsWith("http") || path.startsWith("data:")) return path;
  const clean = path.startsWith("/") ? path.slice(1) : path;
  if (clean.startsWith("storage/") || clean.startsWith("uploads/")) {
    return `http://localhost:8000/${clean}`;
  }
  return `http://localhost:8000/storage/${clean}`;
};
</script>

<style scoped>
.garage-main-card {
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04) !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: #ffffff;
}
.garage-heading {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 700;
  letter-spacing: -0.01em;
}

/* Card động cơ: Đồng bộ 100% với vt-product-card của VatTuThuongMua */
.garage-item-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: var(--jpm-neutral-0, #FFFFFF);
  border: 1px solid var(--jpm-neutral-200, #E2E8F0) !important;
  box-shadow: none !important;
  padding: 10px 16px !important;
  transition: border-color 0.2s ease, background-color 0.2s ease;
  cursor: pointer;
}

.garage-item-card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: #cbd5e1 !important;
  background-color: #fafbfc;
}

.garage-model-title {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 0.95rem;
  line-height: 1.35;
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

/* Hai nút thao tác dạng icon 36x36px đồng bộ 100% với VatTuThuongMua */
/* Nút Tra Phụ Tùng: Outline màu xanh Primary (#0d6efd) y hệt btn-cart-icon */
.btn-search-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--jpm-radius-control, 8px);
  background-color: #ffffff;
  border: 1.5px solid #0d6efd;
  color: #0d6efd;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  transition: all 0.2s ease;
  box-shadow: none !important;
}

.btn-search-icon:hover {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: none !important;
}

/* Nút Xóa: Bình thường xám trung tính tinh tế, khi hover chuyển sang ĐỎ rực rỡ y hệt btn-delete-icon */
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
</style>


















