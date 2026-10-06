<template>
<div class="flex-grow-1 d-flex flex-column">
            <div class="card border-0 flex-grow-1" style="box-shadow: var(--shadow-sm); border-radius: 12px;">
              <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center" style="padding: 20px 24px;">
                <div>
                  <h5 class="mb-0" style="color: var(--heading-color); font-weight: 700;">DANH SÁCH MÁY THỦY</h5>
                  <span class="text-muted" style="font-size: 13px;">{{ garage.length }} động cơ trong hạm đội</span>
                </div>
                
              </div>
              <div class="card-body p-4">

                <!-- Loading Skeleton -->
                <div v-if="isLoadingGarage">
                  <div v-for="i in 2" :key="i" class="p-3 border rounded-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3" style="background-color: #fff;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="placeholder-glow rounded" style="width: 60px; height: 60px;">
                        <div class="placeholder w-100 h-100 rounded"></div>
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
                  <div class="mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; background-color: rgba(248,34,73,0.05);">
                    <i class="bi bi-tools" style="font-size: 3rem; color: var(--accent-color);"></i>
                  </div>
                  <h5 class="mb-2" style="color: var(--heading-color); font-weight: 700;">Garage của bạn đang trống</h5>
                  <p class="text-muted mb-4" style="font-size: 14.5px; max-width: 450px;">
                    Hãy tìm kiếm phụ tùng bạn cần trên trang Tra Cứu, sau đó bấm "Lưu vào Garage". Hệ thống sẽ tự động tạo hồ sơ máy tương ứng cho bạn.
                  </p>
                  <button class="btn btn-jpm px-4 py-2 fw-bold text-uppercase shadow-none" style="letter-spacing: 1px;" @click="router.push('/tra-cuu')"><i class="bi bi-search me-2"></i>Đến trang Tra cứu</button>
                </div>

                <!-- Garage List -->
                <div class="d-flex flex-column gap-3" v-else>
                  <div v-for="may in garage" :key="may.id" class="p-3 border rounded-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="background-color: #fff; transition: border-color 0.2s; cursor: pointer;" @mouseenter="$event.currentTarget.style.borderColor='var(--accent-color)'" @mouseleave="$event.currentTarget.style.borderColor='#dee2e6'" @click="router.push(`/garage/${may.id}`)">
                    
                                        <!-- Left: Machine Info -->
                    <div class="d-flex align-items-center gap-3">
                      <!-- Model Image -->
                      <div class="d-flex align-items-center justify-content-center border rounded" style="width: 60px; height: 60px; background-color: #fff; flex-shrink: 0;">
                        <img v-if="may.dong_co_model?.hinh_anh" :src="may.dong_co_model.hinh_anh" :alt="may.dong_co_model.ten_model" style="max-height: 50px; max-width: 50px; object-fit: contain;">
                        <img v-else-if="may.dong_co_model?.thuong_hieu?.logo_path" :src="may.dong_co_model.thuong_hieu.logo_path" :alt="may.dong_co_model.thuong_hieu.name" style="max-height: 40px; max-width: 50px; object-fit: contain; opacity: 0.5;">
                        <i v-else class="bi bi-gear text-muted" style="font-size: 1.5rem;"></i>
                      </div>
                      
                      <!-- Details -->
                      <div>
                        <!-- Model Name & Brand Badge -->
                        <h6 class="mb-1 d-flex align-items-center gap-2 flex-wrap" style="font-size: 16px; font-weight: 700; color: #111827;">
                          {{ may.dong_co_model?.ten_model || 'Chưa rõ Model' }}
                          <span v-if="may.dong_co_model?.thuong_hieu?.name" class="badge bg-pastel-danger text-danger text-uppercase" style="font-size: 9px; padding: 3px 6px; letter-spacing: 0.5px;">{{ may.dong_co_model.thuong_hieu.name }}</span>
                        </h6>
                        
                        <!-- Specs -->
                        <div class="d-flex align-items-center gap-4 mt-2 text-muted flex-wrap" style="font-size: 13px;">
                          <span v-if="may.dong_co_model?.ma_luc"><i class="bi bi-lightning-charge me-1"></i>{{ may.dong_co_model.ma_luc }} HP</span>
                          <span v-if="may.dong_co_model?.ma_model" class="fst-italic">{{ may.dong_co_model.ma_model }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="d-flex align-items-center gap-3 pe-2 flex-shrink-0 mt-3 mt-md-0">
                      <!-- Sơ đồ / Tra phụ tùng -->
                      <button class="btn btn-light btn-sm px-3 rounded-pill d-flex align-items-center gap-2" style="font-size: 12px; font-weight: 600; color: #495057; border: 1px solid #dee2e6;" @click.stop="router.push('/tra-cuu?brand=' + may.dong_co_model?.thuong_hieu_id + '&engineType=' + may.dong_co_model?.loai_dong_co_id + '&model=' + may.dong_co_model_id + '&year=' + (may.dong_co_model?.nam_san_xuat || ''))">
                        <i class="bi bi-search" style="font-size: 13px;"></i>Tra phụ tùng
                      </button>

                      <!-- Delete -->
                      <button class="btn p-0 action-btn ms-2" title="Xóa máy khỏi Garage" @click.stop="deleteMay(may.id)" style="border: none; background: transparent;">
                        <i class="bi bi-trash3 hover-red" style="font-size: 1.25rem; color: #f82249;"></i>
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
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom-0 bg-light">
            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-tools text-danger me-2"></i>Phụ Tùng Đã Lưu Của Máy Này</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 bg-light">
            
            <div v-if="isLoadingSavedParts" class="text-center py-5">
                <div class="spinner-border text-danger" role="status"></div>
            </div>
            
            <div v-else-if="!savedParts || savedParts.length === 0" class="text-center py-5 bg-white rounded border">
                <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0 text-muted">Máy này chưa có phụ tùng nào được lưu.</p>
            </div>
            
            <div v-else class="row g-3">
                <div class="col-12" v-for="item in savedParts" :key="item.id">
                    <div class="card border shadow-sm">
                        <div class="card-body p-{{ garage.length }} động cơ trong Garagegn-items-center">
                            <img :src="getImageUrl(item.phu_tung?.hinh_anh_chinh) || 'https://placehold.co/100x100'" alt="" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1"><router-link :to="`/chi-tiet-phu-tung/${item.phu_tung?.slug}`" class="text-dark text-decoration-none hover-red">{{ item.phu_tung?.ten_phu_tung }}</router-link></h6>
                                <p class="text-muted small mb-0">Part #: <strong>{{ item.phu_tung?.ma_phu_tung }}</strong> | Số lượng: {{ item.so_luong }}</p>
                                <p class="text-muted small mb-0" v-if="item.ghi_chu"><i class="bi bi-info-circle me-1"></i>{{ item.ghi_chu }}</p>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-jpm btn-sm fw-bold shadow-sm" @click="addToCart(item.phu_tung, item.so_luong)"><i class="bi bi-cart-plus me-1"></i>Thêm Giỏ</button>
                                <button class="btn btn-light btn-sm text-danger border" @click="removeSavedPart(item.id)"><i class="bi bi-trash3 me-1"></i>Xóa</button>
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
      confirmButtonColor: '#ef233c',
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
  if (path.startsWith('http')) return path;
  return `${import.meta.env.VITE_API_URL.replace('/api', '')}/storage/${path}`;
};
</script>

<style scoped>
.active-sidebar-link {
  color: var(--accent-color) !important;
  background-color: rgba(239, 35, 60, 0.04) !important;
  border-left: 3px solid var(--accent-color) !important;
}
.list-group-item-action:hover {
  background-color: rgba(0, 0, 0, 0.02);
  color: var(--heading-color) !important;
}
.hover-red:hover {
  color: var(--accent-color) !important;
}
</style>


















