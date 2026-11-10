<template>
<div class="flex-grow-1 d-flex flex-column">
            <div class="card border-0 flex-grow-1 garage-main-card">
              <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center" style="padding: 16px 24px; min-height: 70px; border-bottom: 1px solid #e2e8f0 !important;">
                <h5 class="mb-0 garage-heading">Danh sách động cơ</h5>
              </div>
              <div class="card-body p-4">

                <!-- Loading Skeleton -->
                <div v-if="isLoadingGarage">
                  <div v-for="i in 2" :key="i" class="p-4 border rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                      <div class="placeholder-glow rounded-3" style="width: 60px; height: 60px;">
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
                <div class="text-center py-5 d-flex flex-column align-items-center justify-content-center" v-else-if="garage.length === 0" style="min-height: 400px;">
                  <div class="empty-icon-wrap mb-4">
                    <i class="bi bi-tools"></i>
                  </div>
                  <h5 class="mb-2 garage-heading">Garage của bạn đang trống</h5>
                  <p class="text-muted mb-4" style="font-size: 14.5px; max-width: 480px; line-height: 1.6;">
                    Hãy tra cứu động cơ và sơ đồ phụ tùng trên trang Tra Cứu, sau đó bấm <strong>"Lưu vào Garage"</strong>. Hệ thống sẽ tự động khởi tạo hồ sơ máy tương ứng cho garage của bạn.
                  </p>
                  <button class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" @click="router.push('/tra-cuu')">
                    <i class="bi bi-search"></i>
                    <span>Đến trang Tra cứu sơ đồ</span>
                  </button>
                </div>

                <!-- Garage List -->
                <div class="d-flex flex-column gap-3" v-else>
                  <div v-for="may in garage" :key="may.id" class="p-4 border garage-item-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" @click="router.push(`/garage/${may.id}`)">
                    
                    <!-- Left: Machine Info -->
                    <div>
                      <!-- Model Name & Brand Badge -->
                      <h6 class="mb-1 d-flex align-items-center gap-2 flex-wrap" style="font-size: 16px; font-weight: 700; color: var(--jpm-color-navy-deep, #0A1128);">
                        {{ may.dong_co_model?.ten_model || 'Chưa rõ Model' }}
                        <span v-if="may.dong_co_model?.thuong_hieu?.name" class="badge brand-tag-badge">{{ may.dong_co_model.thuong_hieu.name }}</span>
                      </h6>
                      
                      <!-- Specs -->
                      <div class="d-flex align-items-center gap-4 mt-2 text-muted flex-wrap" style="font-size: 13.5px;">
                        <span v-if="may.dong_co_model?.ma_luc"><i class="bi bi-lightning-charge me-1 text-warning"></i>{{ may.dong_co_model.ma_luc }} HP</span>
                        <span v-if="may.dong_co_model?.ma_model" class="fst-italic text-secondary"><i class="bi bi-upc-scan me-1"></i>{{ may.dong_co_model.ma_model }}</span>
                        <span v-if="may.dong_co_model?.nam_san_xuat || may.dong_co_model?.nam_bat_dau"><i class="bi bi-calendar3 me-1"></i>Năm: {{ may.dong_co_model?.nam_san_xuat || may.dong_co_model?.nam_bat_dau || '' }}</span>
                      </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="d-flex align-items-center gap-3 pe-2 flex-shrink-0 mt-3 mt-md-0">
                      <!-- Sơ đồ / Tra phụ tùng -->
                      <button class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-2 fw-semibold" @click.stop="router.push('/tra-cuu?brand=' + may.dong_co_model?.thuong_hieu_id + '&engineType=' + may.dong_co_model?.loai_dong_co_id + '&model=' + may.dong_co_model_id + '&year=' + (may.dong_co_model?.nam_san_xuat || ''))">
                        <i class="bi bi-search"></i>Tra phụ tùng
                      </button>

                      <!-- Delete -->
                      <button class="btn p-0 action-btn ms-2" title="Xóa máy khỏi Garage" @click.stop="deleteMay(may.id)" style="border: none; background: transparent;">
                        <i class="bi bi-trash3 hover-red" style="font-size: 1.25rem; color: #94a3b8;"></i>
                      </button>
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
                            <h6 class="fw-bold mb-1"><router-link :to="`/chi-tiet-phu-tung/${item.phu_tung?.ma_phu_tung || item.phu_tung?.id}`" class="text-dark text-decoration-none hover-red">{{ item.phu_tung?.ten_phu_tung }}</router-link></h6>
                            <p class="text-muted small mb-0">Part #: <strong>{{ item.phu_tung?.ma_phu_tung }}</strong> | Số lượng: {{ item.so_luong }}</p>
                            <p class="text-muted small mb-0" v-if="item.ghi_chu"><i class="bi bi-info-circle me-1"></i>{{ item.ghi_chu }}</p>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <button class="btn btn-danger btn-sm fw-semibold shadow-sm" @click="addToCart(item.phu_tung, item.so_luong)"><i class="bi bi-cart-plus me-1"></i>Thêm giỏ</button>
                            <button class="btn btn-outline-secondary btn-sm" @click="removeSavedPart(item.id)"><i class="bi bi-trash3 me-1"></i>Xóa</button>
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
.garage-item-card {
  border-radius: var(--jpm-radius-control, 8px) !important;
  background-color: #ffffff;
  border: 1px solid #e2e8f0 !important;
  transition: all 0.2s ease;
  cursor: pointer;
}
.garage-item-card:hover {
  border-color: rgba(248, 34, 73, 0.4) !important;
  box-shadow: 0 4px 12px rgba(10, 17, 40, 0.04);
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


















