<template>
  <div class="interactive-diagram-page main-page">
    <section class="section inner-page-section">
      <div class="container-fluid px-3 px-lg-4 px-xxl-5">
      
      <!-- PAGE TITLE & BREADCRUMB -->
      <div class="mb-4" data-aos="fade-down">
        <div class="page-title-box mb-2">
          <h2 class="jpm-page-title">
            Sơ đồ chi tiết <span class="text-danger">{{ modelName }}</span>
          </h2>
          <p class="text-muted mb-0">
            Chọn cụm chi tiết trên sơ đồ để xem danh sách phụ tùng tương ứng.
          </p>
        </div>

        <nav aria-label="breadcrumb" class="custom-breadcrumb pt-2">
          <ol class="breadcrumb mb-0 align-items-center">
            <li class="breadcrumb-item">
              <router-link to="/" class="text-decoration-none text-muted d-flex align-items-center">
                <i class="bi bi-house-door-fill me-1"></i> Trang chủ
              </router-link>
            </li>
            <li class="breadcrumb-item">
              <span class="text-muted">Danh mục</span>
            </li>
            <li class="breadcrumb-item">
              <router-link :to="`/thuong-hieu/${brandRoute}`" class="text-decoration-none text-muted fw-bold">
                {{ brandName }}
              </router-link>
            </li>
            <li class="breadcrumb-item active fw-bold" style="color: var(--heading-color);" aria-current="page">
              Sơ đồ {{ modelName }}
            </li>
          </ol>
        </nav>
      </div>

      
      
      <!-- LOADING STATE -->
      <template v-if="isLoading">
        <div class="py-5 text-center my-5" style="min-height: 50vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <div class="spinner-border text-danger mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
          </div>
          <h5 class="text-muted fw-bold">Đang tải dữ liệu sơ đồ...</h5>
        </div>
      </template>

      <!-- EMPTY STATE -->
      <template v-else-if="diagramsList.length === 0">

        <div class="p-5 text-center my-5" data-aos="fade-up">
          <div class="py-5">
            <i class="bi bi-diagram-3 text-muted mb-3" style="font-size: 5rem; opacity: 0.3;"></i>
            <h3 class="fw-bold text-dark mb-3">Sơ đồ đang được cập nhật</h3>
            <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto; font-size: 1.05rem; line-height: 1.6;">
              Model <strong class="text-danger">{{ modelName }}</strong> hiện tại chưa có dữ liệu sơ đồ phân rã phụ tùng nào. Vui lòng quay lại sau hoặc liên hệ với bộ phận kỹ thuật của JPM Marine để được hỗ trợ tra cứu trực tiếp.
            </p>
            <router-link to="/san-pham" class="btn btn-danger px-4 py-2 fw-bold text-uppercase mt-2 btn-back-shop">
              <i class="bi bi-cart-dash me-2"></i> Trở về Cửa Hàng
            </router-link>
          </div>
        </div>
      </template>

      <!-- SPLIT VIEW AREA -->
      <div v-else class="jpm-split-layout" id="main-split-container" style="gap: 0;" data-aos="fade-up" data-aos-delay="100">

        
        <!-- CỘT TRÁI: Cụm Chi Tiết -->
        <div class="jpm-split-sidebar dynamic-sidebar mb-4 mb-lg-0 pe-lg-2" :style="{ width: sidebarRatio + '%' }">
          <div class="card sticky-sidebar catalog-sidebar-card shadow-sm" style="height: 660px;">
            <div class="catalog-sidebar-header">
              <h3 class="catalog-sidebar-title mb-0">Cụm Chi Tiết</h3>
            </div>
            <div class="catalog-sidebar-body">
              <div class="catalog-nav-list custom-scrollbar" style="max-height: calc(660px - 80px); overflow-y: auto;">
                <div v-if="isLoading" class="p-3 text-center text-muted">Đang tải...</div>
                <div v-else-if="diagramsList.length === 0" class="p-3 text-center text-muted">Chưa có sơ đồ nào.</div>

                <button 
                  v-for="diagram in diagramsList" 
                  :key="diagram.id"
                  type="button"
                  class="catalog-nav-btn"
                  :class="{ active: activeComponentId === diagram.id }"
                  @click="activeComponentId = diagram.id"
                >
                  <span class="catalog-nav-text" :title="diagram.nhom_phu_tung?.ten_nhom || diagram.ten_so_do">
                    {{ diagram.nhom_phu_tung?.ten_nhom || diagram.ten_so_do || "N/A" }}
                  </span>
                  <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- LEFT RESIZER -->
        <div class="resizer d-none d-lg-flex" @mousedown.prevent="startSidebarDrag" :class="{ 'dragging': isSidebarDragging }" title="Kéo để thay đổi kích thước">
          <div class="resizer-handle"></div>
        </div>

        <!-- CỘT PHẢI: BẢN VẼ & SẢN PHẨM -->
        <div class="jpm-split-content dynamic-content ps-lg-2" :style="{ width: (100 - sidebarRatio) + '%' }">
          
          <div class="d-flex w-100 align-items-stretch" id="split-container" style="min-height: 660px;">
            
            <!-- NỬA TRÁI: BẢN VẼ (Pan + Zoom) -->
            <div :style="{ width: splitRatio + '%' }" class="pe-2">
              <div class="card sticky-sidebar diagram-panel-card" style="height: 660px;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" v-if="activeComponentId">
                  <h4 class="mb-0 jpm-card-title text-truncate">Sơ đồ phân rã: <span class="text-danger">{{ activeComponentName }}</span></h4>
                </div>
                <div class="card-body p-0 position-relative bg-white d-flex align-items-center justify-content-center h-100" style="min-height: 400px;">
                  <h4 class="text-muted" v-if="!activeComponentId">Vui lòng chọn một Cụm Chi Tiết ở cột trái</h4>
                  <div v-else class="w-100 h-100 d-flex flex-column" style="position: relative;">
                    <!-- Floating Toolbar -->
                    <div class="position-absolute shadow-sm d-flex align-items-center" style="top: 0; right: 0; z-index: 1060; background: white; border-radius: 0 0 0 6px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; padding: 4px 6px;">
                      <button class="btn btn-sm btn-icon" @click="zoomOut" title="Thu nhỏ"><i class="bi bi-dash-lg"></i></button>
                      <span class="fw-bold px-2" style="color: var(--jpm-color-navy-dark, #0a1128); font-size: 0.85rem; min-width: 48px; text-align: center; cursor: pointer;" @click="resetZoom" title="Đặt lại tỷ lệ">{{ Math.round(zoomLevel * 100) }}%</span>
                      <button class="btn btn-sm btn-icon" @click="zoomIn" title="Phóng to"><i class="bi bi-plus-lg"></i></button>
                      <div class="vr mx-1 my-1" style="opacity: 0.1; min-height: 20px;"></div>
                      <button class="btn btn-sm btn-icon" @click="openModal" title="Phóng to toàn màn hình (Modal)">
                        <i class="bi bi-arrows-fullscreen"></i>
                      </button>
                    </div>
                    <!-- PAN + ZOOM VIEWPORT (Boats.net style with Interactive Hotspot Pins DEC-02) -->
                    <div 
                      ref="diagramViewport"
                      :class="{ 'is-panning': isPanning }"
                      @mousedown="startPan"
                      @wheel.prevent="handleWheel"
                      style="overflow: hidden; cursor: grab; width: 100%; flex: 1; min-height: 320px; display: flex; align-items: center; justify-content: center; background: #ffffff; user-select: none;"
                    >
                      <div class="diagram-stage-container position-relative" :style="{ transform: `translate(${panX}px, ${panY}px) scale(${zoomLevel})`, transformOrigin: 'center center', transition: isPanning ? 'none' : 'transform 0.1s ease' }">
                        <div v-if="activeDiagram?.hinh_anh" class="position-relative d-inline-block">
                          <img :src="getImageUrl(activeDiagram.hinh_anh)" alt="Sơ đồ phân rã" style="max-width: 100%; max-height: 540px; object-fit: contain; display: block; pointer-events: none;" draggable="false">
                          <!-- HOTSPOTS OVERLAY LAYER -->
                          <div v-if="hotspotParts.length > 0" class="hotspot-overlay-layer">
                            <div 
                              v-for="part in hotspotParts" 
                              :key="part.id"
                              class="hotspot-pin-anchor"
                              :style="{ left: `${getHotspotX(part)}%`, top: `${getHotspotY(part)}%` }"
                            >
                              <button
                                type="button"
                                class="hotspot-pin-btn"
                                :class="{ 
                                  'pin-active': isPartSelected(part),
                                  'pin-hovered': hoveredPartCode === getPartCode(part)
                                }"
                                :title="`Ref #${part.ref_number || ''}: ${part.phu_tung?.ma_phu_tung || ''}`"
                                @click.stop="selectPartFromPin(part)"
                                @mouseenter="hoveredPartCode = getPartCode(part)"
                                @mouseleave="hoveredPartCode = null"
                              >
                                <span class="pin-number">{{ part.ref_number || '•' }}</span>
                                <span v-if="isPartSelected(part)" class="pin-radar-ring"></span>
                              </button>

                              <!-- MINI TOOLTIP ON HOVER OR ACTIVE -->
                              <div 
                                v-if="hoveredPartCode === getPartCode(part) || (isPartSelected(part) && hoveredPartCode === null)" 
                                class="hotspot-tooltip shadow-sm"
                              >
                                <div class="fw-bold d-flex align-items-center gap-1 text-danger">
                                  <span class="badge bg-danger text-white px-1 py-0 me-1" style="font-size: 0.65rem;">#{{ part.ref_number }}</span>
                                  <span>{{ part.phu_tung?.ma_phu_tung }}</span>
                                </div>
                                <div class="text-truncate text-white small" style="max-width: 180px;">{{ part.phu_tung?.ten_phu_tung }}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div v-else class="text-center p-4">
                          <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                          <p class="text-muted mt-2">Bản vẽ của {{ activeComponentName }} đang được cập nhật...</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- THANH KÉO GIỮA (RESIZER) -->
            <div 
              class="resizer" 
              @mousedown.prevent="startDrag"
              :class="{ 'dragging': isDragging }"
              title="Kéo để thay đổi kích thước"
            >
              <div class="resizer-handle"></div>
            </div>

            <!-- NỬA PHẢI: BẢNG SẢN PHẨM -->
            <div :style="{ width: (100 - splitRatio) + '%' }" class="ps-2 d-flex flex-column">
              <div class="card flex-grow-1 diagram-panel-card" v-if="activeComponentId">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="min-height: 60px;">
                  <h4 class="mb-0 jpm-card-title text-truncate" style="max-width: 100%;">Danh sách Phụ Tùng</h4>
                  <div class="d-flex align-items-center gap-2 ms-auto">
                    <span class="badge-item-count">{{ currentParts.length }} Phụ Tùng</span>
                    <button @click="addAllSelectedToCart" v-if="selectedParts.length > 0" class="btn btn-outline-danger btn-sm px-3 py-1 fw-bold text-nowrap d-inline-flex align-items-center justify-content-center btn-rounded-control" title="Thêm tất cả đã chọn vào giỏ">
                      <i class="bi bi-cart-plus me-1"></i> Thêm ({{ selectedParts.length }})
                    </button>
                  </div>
                </div>
                <div class="card-body p-0 d-flex flex-column h-100">
                  <div class="table-responsive flex-grow-1 hide-x-scroll">
                    <table class="table custom-table mb-0 align-middle table-jpm">
                      <thead>
                        <tr>
                          <th class="text-center" style="width: 40px; padding-left: 1rem;">
                            <input class="form-check-input custom-checkbox" type="checkbox" v-model="selectAll">
                          </th>
                          <th class="text-center th-ref" style="width: 60px;">REF</th>
                          <th class="text-start th-part">MÃ & TÊN PHỤ TÙNG</th>
                          <th class="text-center th-qty" style="width: 90px;">SỐ LƯỢNG</th>
                          <th class="text-center th-price" style="width: 130px;">GIÁ BÁN</th>
                          <th class="text-center th-action" style="width: 110px;">THAO TÁC</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="currentParts.length === 0">
                          <td colspan="6" class="text-center py-4 text-muted">Chưa có phụ tùng nào.</td>
                        </tr>
                        <tr 
                          v-for="part in currentParts" 
                          :key="part.id" 
                          class="table-row-hover" 
                          :class="{ 
                            'table-hover-highlight': hoveredPartCode === getPartCode(part)
                          }"
                          :data-part-code="getPartCode(part)"
                          @mouseenter="hoveredPartCode = getPartCode(part)"
                          @mouseleave="hoveredPartCode = null"
                        >
                          <td class="text-center" style="padding-left: 1rem;">
                            <input class="form-check-input custom-checkbox" type="checkbox" :value="part.id" v-model="selectedParts">
                          </td>
                          <td class="text-center">
                            <span class="spec-ref-text">{{ part.ref_number || '•' }}</span>
                          </td>
                          <td class="text-start">
                            <router-link :to="`/chi-tiet-phu-tung/${part.phu_tung?.ma_phu_tung}${diagramResponse?.data?.model?.id ? '?from_model_id=' + diagramResponse.data.model.id : ''}`" class="part-item-link text-decoration-none d-block">
                              <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold model-code">{{ part.phu_tung?.ma_phu_tung }}</span>
                              </div>
                              <div class="part-desc text-muted fw-medium">{{ part.phu_tung?.ten_phu_tung }}</div>
                            </router-link>
                          </td>
                          <td class="text-center">
                            <span class="spec-qty-text">{{ part.qty || 1 }}</span>
                          </td>
                          <td class="text-center">
                            <span v-if="part.phu_tung?.is_gia_lien_he" class="text-danger fw-bold" style="font-size: 0.92rem;">Liên hệ</span>
                            <span v-else class="fw-bold text-nowrap text-danger font-tabular" style="font-size: 0.92rem;">
                              {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(part.phu_tung?.gia_ban || 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <button @click.stop="addToCart(part)" class="btn btn-add-part" title="Thêm vào giỏ">
                              <i class="bi bi-cart-plus me-1"></i> Thêm
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div> <!-- End Cột Phải -->

      </div>
      </div>
    </section>

    <!-- ========================================================
         MODAL PHÓNG TO SƠ ĐỒ TOÀN MÀN HÌNH (BLUEPRINT LIGHTBOX)
         ======================================================== -->
    <Teleport to="body">
      <div 
        v-if="isModalOpen" 
        class="jpm-blueprint-modal-backdrop"
        @click.self="closeModal"
      >
        <div class="jpm-blueprint-modal-container shadow-lg">
          <!-- MODAL HEADER -->
          <div class="jpm-blueprint-modal-header">
            <div class="d-flex align-items-center gap-3">
              <div class="modal-header-icon">
                <i class="bi bi-diagram-3-fill text-danger"></i>
              </div>
              <div>
                <h5 class="mb-0 modal-title">
                  Sơ đồ phân rã: <span class="text-danger">{{ activeComponentName }}</span>
                </h5>
                <div class="modal-subtitle">
                  Model: <strong class="text-dark">{{ modelName }}</strong> • <span>{{ currentParts.length }} Phụ tùng</span>
                </div>
              </div>
            </div>

            <!-- CONTROLS & CLOSE -->
            <div class="d-flex align-items-center gap-2">
              <!-- MODAL ZOOM BAR -->
              <div class="modal-zoom-bar d-flex align-items-center">
                <button class="btn btn-sm btn-icon" @click="modalZoomOut" title="Thu nhỏ"><i class="bi bi-dash-lg"></i></button>
                <span class="fw-bold px-2 font-tabular modal-zoom-text" @click="resetModalZoom" title="Đặt lại tỷ lệ">{{ Math.round(modalZoomLevel * 100) }}%</span>
                <button class="btn btn-sm btn-icon" @click="modalZoomIn" title="Phóng to"><i class="bi bi-plus-lg"></i></button>
                <div class="vr mx-1 my-1" style="opacity: 0.15; min-height: 18px;"></div>
                <button class="btn btn-sm btn-reset-zoom" @click="resetModalZoom" title="Đặt lại 100%">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                </button>
              </div>

              <!-- CLOSE BUTTON -->
              <button class="btn btn-modal-close" @click="closeModal" title="Đóng (ESC)">
                <i class="bi bi-x-lg me-1"></i> Đóng
              </button>
            </div>
          </div>

          <!-- MODAL VIEWPORT -->
          <div 
            ref="modalViewport"
            class="jpm-blueprint-modal-body"
            :class="{ 'is-panning': modalIsPanning }"
            @mousedown="startModalPan"
            @wheel.prevent="handleModalWheel"
          >
            <div 
              class="diagram-stage-container position-relative" 
              :style="{ 
                transform: `translate(${modalPanX}px, ${modalPanY}px) scale(${modalZoomLevel})`, 
                transformOrigin: 'center center', 
                transition: modalIsPanning ? 'none' : 'transform 0.08s ease' 
              }"
            >
              <div v-if="activeDiagram?.hinh_anh" class="position-relative d-inline-block">
                <img 
                  :src="getImageUrl(activeDiagram.hinh_anh)" 
                  alt="Sơ đồ phân rã" 
                  class="modal-blueprint-img"
                  draggable="false"
                >
                <!-- HOTSPOTS OVERLAY LAYER -->
                <div v-if="hotspotParts.length > 0" class="hotspot-overlay-layer">
                  <div 
                    v-for="part in hotspotParts" 
                    :key="part.id"
                    class="hotspot-pin-anchor"
                    :style="{ left: `${getHotspotX(part)}%`, top: `${getHotspotY(part)}%` }"
                  >
                    <button
                      type="button"
                      class="hotspot-pin-btn"
                      :class="{ 
                        'pin-active': isPartSelected(part),
                        'pin-hovered': hoveredPartCode === getPartCode(part)
                      }"
                      :title="`Ref #${part.ref_number || ''}: ${part.phu_tung?.ma_phu_tung || ''}`"
                      @click.stop="selectPartFromPin(part)"
                      @mouseenter="hoveredPartCode = getPartCode(part)"
                      @mouseleave="hoveredPartCode = null"
                    >
                      <span class="pin-number">{{ part.ref_number || '•' }}</span>
                      <span v-if="isPartSelected(part)" class="pin-radar-ring"></span>
                    </button>

                    <!-- MINI TOOLTIP ON HOVER OR ACTIVE -->
                    <div 
                      v-if="hoveredPartCode === getPartCode(part) || (isPartSelected(part) && hoveredPartCode === null)" 
                      class="hotspot-tooltip shadow-sm"
                    >
                      <div class="fw-bold d-flex align-items-center gap-1 text-danger">
                        <span class="badge bg-danger text-white px-1 py-0 me-1" style="font-size: 0.65rem;">#{{ part.ref_number }}</span>
                        <span>{{ part.phu_tung?.ma_phu_tung }}</span>
                      </div>
                      <div class="text-truncate text-white small" style="max-width: 220px;">{{ part.phu_tung?.ten_phu_tung }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center p-4">
                <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                <p class="text-muted mt-2">Bản vẽ của {{ activeComponentName }} đang được cập nhật...</p>
              </div>
            </div>

            <!-- HINT PILL -->
            <div class="modal-hint-pill">
              <i class="bi bi-info-circle me-1"></i> Cuộn chuột để Phóng to/Thu nhỏ • Giữ chuột trái kéo rê (Pan) để xem toàn bộ bản vẽ
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, ref, watch, nextTick, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useCartStore } from '../../../stores/cartStore';
import { createToaster } from '@meforma/vue-toaster';

const route = useRoute();

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('blob:') || path.startsWith('http')) return path;
  let cleanPath = path.replace(/^\/?storage\//, '');
  return 'http://localhost:8000/storage/' + cleanPath;
};

const targetPartCode = computed(() => (route.query.part || '').toString().trim().toUpperCase());

const modelName = computed(() => {
  const raw = route.params.model || route.params.modelCode || '';
  try { return decodeURIComponent(raw); } catch { return raw; }
});

const { data: diagramResponse, isLoading } = useQuery({
  queryKey: computed(() => ['clientDiagrams', modelName.value]),
  queryFn: () => axiosClient.get(`/public/so-do-model/${modelName.value}`),
  enabled: computed(() => !!modelName.value)
});

const brandRoute = computed(() => {
  if (route.params.brand) return route.params.brand;
  return diagramResponse.value?.data?.model?.thuong_hieu?.code?.toLowerCase() || 'mer';
});

const brandNameMap = {
  'mer': 'MERCURY',
  'yam': 'YAMAHA',
  'hon': 'HONDA',
  'suz': 'SUZUKI',
  'toa': 'TOHATSU'
};

const brandName = computed(() => {
  if (diagramResponse.value?.data?.model?.thuong_hieu?.name) {
    return diagramResponse.value.data.model.thuong_hieu.name;
  }
  return brandNameMap[brandRoute.value.toLowerCase()] || brandRoute.value.toUpperCase();
});

const diagramsList = computed(() => {
  return diagramResponse.value?.data?.diagrams || [];
});

const toaster = createToaster({ position: "top-right" });
const cartStore = useCartStore();

const addToCart = (part) => {
  if (!part.phu_tung) return;
  cartStore.addItem(part.phu_tung, 1);
  toaster.success("Đã thêm phụ tùng vào giỏ hàng");
};

const addAllSelectedToCart = () => {
  const partsToAdd = currentParts.value.filter(p => selectedParts.value.includes(p.id));
  let count = 0;
  partsToAdd.forEach(part => {
    if (part.phu_tung) {
      cartStore.addItem(part.phu_tung, 1);
      count++;
    }
  });
  if (count > 0) {
    toaster.success(`Đã thêm ${count} phụ tùng vào giỏ hàng`);
    selectedParts.value = []; // Reset selection
  }
};

const activeComponentId = ref(Number(route.query.diagram_id) || null);

const scrollToHighlightedRow = (code) => {
  if (!code) return;
  nextTick(() => {
    setTimeout(() => {
      const targetEl = document.querySelector(`[data-part-code="${code}"]`);
      if (targetEl) {
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 300);
  });
};

watch([diagramsList, targetPartCode], ([newVal, partCode]) => {
  if (!newVal || newVal.length === 0) return;

  if (partCode) {
    const matchingDiagram = newVal.find(d => 
      d.chi_tiets && d.chi_tiets.some(ct => ct.phu_tung?.ma_phu_tung?.toUpperCase() === partCode)
    );
    if (matchingDiagram) {
      activeComponentId.value = matchingDiagram.id;
      scrollToHighlightedRow(partCode);
      return;
    }
  }

  if (!activeComponentId.value) {
    activeComponentId.value = newVal[0].id;
  }
}, { immediate: true });

watch(activeComponentId, () => {
  if (targetPartCode.value) {
    scrollToHighlightedRow(targetPartCode.value);
  }
});

// Dynamic SEO & Structured Data (Schema.org JSON-LD & Buyer Intent)
watch([modelName, brandName, diagramResponse], () => {
  if (modelName.value) {
    document.title = `Sơ đồ phụ tùng ${modelName.value} (${brandName.value}) | Bản vẽ rã máy tra cứu JPM Marine`;
    
    let metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc) {
      metaDesc.setAttribute('content', `Tra cứu sơ đồ phụ tùng máy thủy chính hãng ${brandName.value} model ${modelName.value}. Bản vẽ kỹ thuật phân rã cụm máy, tra mã OEM chuẩn xác tại JPM Marine.`);
    }

    let script = document.getElementById('schema-diagram-ld');
    if (!script) {
      script = document.createElement('script');
      script.id = 'schema-diagram-ld';
      script.type = 'application/ld+json';
      document.head.appendChild(script);
    }
    script.textContent = JSON.stringify({
      "@context": "https://schema.org",
      "@type": "ItemPage",
      "name": `Sơ đồ phân rã phụ tùng ${modelName.value}`,
      "description": `Tra cứu sơ đồ phụ tùng máy thủy chính hãng ${brandName.value} model ${modelName.value}. Bản vẽ kỹ thuật phân rã cụm máy, mã OEM chuẩn xác.`,
      "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
          { "@type": "ListItem", "position": 1, "name": "Trang chủ", "item": window.location.origin },
          { "@type": "ListItem", "position": 2, "name": brandName.value, "item": `${window.location.origin}/thuong-hieu/${brandRoute.value}` },
          { "@type": "ListItem", "position": 3, "name": `Sơ đồ ${modelName.value}`, "item": window.location.href }
        ]
      }
    });
  }
});

onUnmounted(() => {
  const script = document.getElementById('schema-diagram-ld');
  if (script) script.remove();
  document.body.style.overflow = '';
  window.removeEventListener('keydown', handleModalKeyDown);
  document.removeEventListener('mousemove', onModalPan);
  document.removeEventListener('mouseup', stopModalPan);
  document.removeEventListener('mousemove', doPan);
  document.removeEventListener('mouseup', stopPan);
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDrag);
  document.removeEventListener('mousemove', onSidebarDrag);
  document.removeEventListener('mouseup', stopSidebarDrag);
});

const activeDiagram = computed(() => {
  return diagramsList.value.find(d => d.id === activeComponentId.value) || null;
});

const activeComponentName = computed(() => {
  if (!activeDiagram.value) return '';
  return activeDiagram.value.nhom_phu_tung?.ten_nhom || 'N/A';
});

const currentParts = computed(() => {
  if (!activeDiagram.value || !activeDiagram.value.chi_tiets) return [];
  return [...activeDiagram.value.chi_tiets].sort((a, b) => a.ref_number - b.ref_number);
});

const selectedParts = ref([]);

const selectAll = computed({
  get: () => selectedParts.value.length === currentParts.value.length && currentParts.value.length > 0,
  set: (val) => {
    if (val) {
      selectedParts.value = currentParts.value.map(p => p.id);
    } else {
      selectedParts.value = [];
    }
  }
});

// ─── HOTSPOT PINS & REACTIVE BRIDGE (DEC-02) ──────────────────────────────────
const selectedPinPartCode = ref('');
const hoveredPartCode = ref(null);

watch(targetPartCode, (code) => {
  if (code) {
    selectedPinPartCode.value = code;
  }
}, { immediate: true });

const getPartCode = (part) => {
  return part?.phu_tung?.ma_phu_tung ? part.phu_tung.ma_phu_tung.toUpperCase() : '';
};

const getHotspotX = (part) => {
  if (part.x !== null && part.x !== undefined && part.x !== '') return parseFloat(part.x);
  if (part.vi_tri_x !== null && part.vi_tri_x !== undefined && part.vi_tri_x !== '') return parseFloat(part.vi_tri_x);
  return null;
};

const getHotspotY = (part) => {
  if (part.y !== null && part.y !== undefined && part.y !== '') return parseFloat(part.y);
  if (part.vi_tri_y !== null && part.vi_tri_y !== undefined && part.vi_tri_y !== '') return parseFloat(part.vi_tri_y);
  return null;
};

const hotspotParts = computed(() => {
  return currentParts.value.filter(part => getHotspotX(part) !== null && getHotspotY(part) !== null);
});

const isPartSelected = (part) => {
  const code = getPartCode(part);
  if (!code) return false;
  if (selectedPinPartCode.value && selectedPinPartCode.value === code) return true;
  if (targetPartCode.value && targetPartCode.value === code) return true;
  return false;
};

const selectPartFromPin = (part) => {
  const code = getPartCode(part);
  if (!code) return;
  selectedPinPartCode.value = code;
  scrollToHighlightedRow(code);
};

const selectPartFromRow = (part) => {
  const code = getPartCode(part);
  if (!code) return;
  selectedPinPartCode.value = code;
};

// ─── PAN + ZOOM (Boats.net style) ─────────────────────────────────────────────
const zoomLevel = ref(1);
const panX = ref(0);
const panY = ref(0);
const isPanning = ref(false);
const diagramViewport = ref(null);

let _panStartX = 0;
let _panStartY = 0;
let _panOriginX = 0;
let _panOriginY = 0;

const startPan = (e) => {
  if (e.button !== 0) return;
  isPanning.value = true;
  _panStartX = e.clientX;
  _panStartY = e.clientY;
  _panOriginX = panX.value;
  _panOriginY = panY.value;
  document.addEventListener('mousemove', doPan);
  document.addEventListener('mouseup', stopPan);
  e.currentTarget.style.cursor = 'grabbing';
};

const doPan = (e) => {
  if (!isPanning.value) return;
  panX.value = _panOriginX + (e.clientX - _panStartX);
  panY.value = _panOriginY + (e.clientY - _panStartY);
};

const stopPan = (e) => {
  isPanning.value = false;
  document.removeEventListener('mousemove', doPan);
  document.removeEventListener('mouseup', stopPan);
  if (diagramViewport.value) diagramViewport.value.style.cursor = 'grab';
};

const handleWheel = (e) => {
  const delta = e.deltaY > 0 ? -0.12 : 0.12;
  const newZoom = Math.min(5, Math.max(0.2, zoomLevel.value + delta));
  if (diagramViewport.value) {
    const rect = diagramViewport.value.getBoundingClientRect();
    const mouseX = e.clientX - rect.left - rect.width / 2;
    const mouseY = e.clientY - rect.top - rect.height / 2;
    const scale = newZoom / zoomLevel.value;
    panX.value = mouseX + (panX.value - mouseX) * scale;
    panY.value = mouseY + (panY.value - mouseY) * scale;
  }
  zoomLevel.value = newZoom;
};

const zoomIn = () => { zoomLevel.value = Math.min(5, +(zoomLevel.value + 0.2).toFixed(2)); };
const zoomOut = () => { zoomLevel.value = Math.max(0.2, +(zoomLevel.value - 0.2).toFixed(2)); };
const resetZoom = () => { zoomLevel.value = 1; panX.value = 0; panY.value = 0; };

// ─── BLUEPRINT MODAL CONTROLS (FULLSCREEN LIGHTBOX) ──────────────────────────
const isModalOpen = ref(false);
const modalZoomLevel = ref(1);
const modalPanX = ref(0);
const modalPanY = ref(0);
const modalIsPanning = ref(false);
const modalViewport = ref(null);
let _modalPanStartX = 0;
let _modalPanStartY = 0;
let _modalPanOriginX = 0;
let _modalPanOriginY = 0;

const openModal = () => {
  isModalOpen.value = true;
  modalZoomLevel.value = 1;
  modalPanX.value = 0;
  modalPanY.value = 0;
  document.body.style.overflow = 'hidden';
  window.addEventListener('keydown', handleModalKeyDown);
};

const closeModal = () => {
  isModalOpen.value = false;
  document.body.style.overflow = '';
  window.removeEventListener('keydown', handleModalKeyDown);
  document.removeEventListener('mousemove', onModalPan);
  document.removeEventListener('mouseup', stopModalPan);
};

const handleModalKeyDown = (e) => {
  if (e.key === 'Escape' && isModalOpen.value) {
    closeModal();
  }
};

const modalZoomIn = () => {
  modalZoomLevel.value = Math.min(5, +(modalZoomLevel.value + 0.2).toFixed(2));
};

const modalZoomOut = () => {
  modalZoomLevel.value = Math.max(0.2, +(modalZoomLevel.value - 0.2).toFixed(2));
};

const resetModalZoom = () => {
  modalZoomLevel.value = 1;
  modalPanX.value = 0;
  modalPanY.value = 0;
};

const handleModalWheel = (e) => {
  const delta = e.deltaY > 0 ? -0.15 : 0.15;
  const newZoom = Math.min(5, Math.max(0.2, +(modalZoomLevel.value + delta).toFixed(2)));
  if (modalViewport.value) {
    const rect = modalViewport.value.getBoundingClientRect();
    const mouseX = e.clientX - rect.left - rect.width / 2;
    const mouseY = e.clientY - rect.top - rect.height / 2;
    const scale = newZoom / modalZoomLevel.value;
    modalPanX.value = mouseX + (modalPanX.value - mouseX) * scale;
    modalPanY.value = mouseY + (modalPanY.value - mouseY) * scale;
  }
  modalZoomLevel.value = newZoom;
};

const startModalPan = (e) => {
  if (e.button !== 0) return;
  modalIsPanning.value = true;
  _modalPanStartX = e.clientX;
  _modalPanStartY = e.clientY;
  _modalPanOriginX = modalPanX.value;
  _modalPanOriginY = modalPanY.value;
  document.addEventListener('mousemove', onModalPan);
  document.addEventListener('mouseup', stopModalPan);
  if (modalViewport.value) modalViewport.value.style.cursor = 'grabbing';
};

const onModalPan = (e) => {
  if (!modalIsPanning.value) return;
  modalPanX.value = _modalPanOriginX + (e.clientX - _modalPanStartX);
  modalPanY.value = _modalPanOriginY + (e.clientY - _modalPanStartY);
};

const stopModalPan = () => {
  modalIsPanning.value = false;
  document.removeEventListener('mousemove', onModalPan);
  document.removeEventListener('mouseup', stopModalPan);
  if (modalViewport.value) modalViewport.value.style.cursor = 'grab';
};

// ─── SPLIT RESIZER (Diagram vs Parts) ─────────────────────────────────────────
const splitRatio = ref(45);
const isDragging = ref(false);

const onDrag = (e) => {
  if (!isDragging.value) return;
  const container = document.getElementById('split-container');
  if (container) {
    const rect = container.getBoundingClientRect();
    let newRatio = ((e.clientX - rect.left) / rect.width) * 100;
    if (newRatio < 20) newRatio = 20;
    if (newRatio > 80) newRatio = 80;
    splitRatio.value = newRatio;
  }
};

const stopDrag = () => {
  isDragging.value = false;
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDrag);
  document.body.style.cursor = 'default';
};

const startDrag = () => {
  isDragging.value = true;
  document.addEventListener('mousemove', onDrag);
  document.addEventListener('mouseup', stopDrag);
  document.body.style.cursor = 'col-resize';
};

// ─── SIDEBAR RESIZER (Sidebar vs Right Content) ────────────────────────────────
const sidebarRatio = ref(17);
const isSidebarDragging = ref(false);

const onSidebarDrag = (e) => {
  if (!isSidebarDragging.value) return;
  const container = document.getElementById('main-split-container');
  if (container) {
    const rect = container.getBoundingClientRect();
    let newRatio = ((e.clientX - rect.left) / rect.width) * 100;
    if (newRatio < 12) newRatio = 12;
    if (newRatio > 40) newRatio = 40;
    sidebarRatio.value = newRatio;
  }
};

const stopSidebarDrag = () => {
  isSidebarDragging.value = false;
  document.removeEventListener('mousemove', onSidebarDrag);
  document.removeEventListener('mouseup', stopSidebarDrag);
  document.body.style.cursor = 'default';
};

const startSidebarDrag = () => {
  isSidebarDragging.value = true;
  document.addEventListener('mousemove', onSidebarDrag);
  document.addEventListener('mouseup', stopSidebarDrag);
  document.body.style.cursor = 'col-resize';
};
</script>

<style scoped>
.interactive-diagram-page {
  min-height: 100vh;
  padding-bottom: 50px;
}

/* ========================================================
   1. UNIFIED CARD PANELS (DEC-01 STANDARD)
   ======================================================== */
.diagram-panel-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.03);
  overflow: hidden;
}

.jpm-card-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 700;
  font-size: 1rem;
  color: #0A1128;
}

.badge-item-count {
  background: #fff1f2;
  color: #e11d48;
  border: 1px solid #fecdd3;
  font-size: 11.5px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: var(--jpm-radius-badge, 6px);
  display: inline-flex;
  align-items: center;
  letter-spacing: 0.3px;
}

.btn-rounded-control {
  border-radius: var(--jpm-radius-control, 8px) !important;
}

.btn-back-shop {
  border-radius: var(--jpm-radius-control, 8px);
}

/* ========================================================
   2. SIDEBAR NAVIGATION: CỤM CHI TIẾT (ĐỒNG BỘ 100% THUONGHIEU.VUE & SANPHAM.VUE)
   ======================================================== */
.catalog-sidebar-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.03);
  overflow: hidden;
}

.catalog-sidebar-header {
  padding: 16px 20px;
  min-height: 60px;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.catalog-sidebar-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1rem;
  font-weight: 700;
  color: #0A1128;
  margin: 0;
}

.catalog-sidebar-body {
  padding: 14px;
}

.sidebar-section-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.8125rem;
  font-weight: 800;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: #0A1128;
  padding: 0 4px 8px 4px;
  border-bottom: 1px solid #f1f5f9;
}

/* ── NAVIGATION LIST IN SIDEBAR (Đồng bộ 100% Thương Hiệu & Cửa Hàng) ── */
.catalog-nav-list {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding-top: 4px;
}

.catalog-nav-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  border: 1px solid transparent;
  background: transparent;
  padding: 8px 12px;
  border-radius: 8px;
  font-family: var(--default-font, sans-serif);
  font-weight: 500;
  font-size: 0.88rem;
  color: #475569;
  transition: all 0.2s ease;
  cursor: pointer;
  text-align: left;
}

.catalog-nav-btn:hover {
  background: #f1f5f9;
  color: #0A1128;
  font-weight: 600;
  transform: translateX(2px);
}

.catalog-nav-btn.active {
  background: rgba(248, 34, 73, 0.08);
  border-color: rgba(248, 34, 73, 0.2);
  color: #f82249;
  font-weight: 700;
}

.catalog-nav-text {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.catalog-nav-arrow {
  font-size: 0.72rem;
  opacity: 0;
  transform: translateX(-4px);
  transition: all 0.2s ease;
  color: inherit;
  flex-shrink: 0;
}

.catalog-nav-btn:hover .catalog-nav-arrow {
  opacity: 0.6;
  transform: translateX(0);
}

.catalog-nav-btn.active .catalog-nav-arrow {
  opacity: 1;
  transform: translateX(0);
  color: #f82249;
}

/* ========================================================
   3. DATA TABLE PHỤ TÙNG (ĐỒNG BỘ 100% BẢNG MODEL & GIỎ HÀNG)
   ======================================================== */
.custom-table {
  width: 100%;
}

.custom-table thead th {
  background-color: #f8fafc !important;
  color: #475569 !important;
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border-top: 1px solid #edf2f7 !important;
  border-bottom: 1px solid #e2e8f0 !important;
  padding-top: 11px;
  padding-bottom: 11px;
  vertical-align: middle;
}

.custom-table tbody td {
  padding: 14px 10px;
  border-bottom: 1px solid #edf2f7;
  vertical-align: middle;
  transition: background-color 0.15s ease;
}

.custom-table tbody tr:nth-child(odd) td {
  background-color: #ffffff;
}

.custom-table tbody tr:nth-child(even) td {
  background-color: #f8fafc;
}

.table-row-hover {
  transition: all 0.15s ease;
}

.table-row-hover:hover td {
  background-color: #fff1f2 !important;
  --bs-table-accent-bg: transparent !important;
  --bs-table-bg-state: transparent !important;
}

.table-row-hover:hover td:first-child {
  box-shadow: inset 4px 0 0 #f82249 !important;
}

.table-row-hover:hover .part-item-link .model-code {
  color: #f82249 !important;
}

.table-row-hover:hover .btn-add-part {
  background-color: var(--accent-color, #f82249) !important;
  border-color: var(--accent-color, #f82249) !important;
  color: #ffffff !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.2);
}

.spec-ref-text {
  color: #0A1128;
  font-weight: 700;
  font-size: 0.92rem;
  font-variant-numeric: tabular-nums;
}

.part-item-link {
  color: inherit;
  text-decoration: none;
}

.part-item-link .model-code {
  color: #0A1128;
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.92rem;
  font-weight: 700;
}

.part-item-link:hover .model-code {
  color: #0A1128 !important;
}

.part-desc {
  color: #64748b;
  font-size: 0.835rem;
  line-height: 1.45;
  white-space: normal;
  word-break: break-word;
  margin-top: 3px;
}

.spec-qty-text {
  color: #0A1128;
  font-weight: 600;
  font-size: 0.92rem;
  font-variant-numeric: tabular-nums;
}

.font-tabular {
  font-variant-numeric: tabular-nums;
}

.btn-add-part {
  padding: 5px 14px;
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: var(--jpm-radius-control, 8px);
  color: var(--accent-color, #f82249);
  background-color: #ffffff;
  border: 1px solid var(--accent-color, #f82249);
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-add-part:hover {
  background-color: var(--accent-color, #f82249);
  border-color: var(--accent-color, #f82249);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.2);
}

.price-text {
  color: var(--accent-color);
  font-size: 0.95rem;
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 90px;
    z-index: 10;
  }
}

.category-list-body::-webkit-scrollbar {
  width: 5px;
}
.category-list-body::-webkit-scrollbar-track {
  background: #f8f9fa;
}
.category-list-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.category-list-body::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* ========================================================
   4. BLUEPRINT TECHNICAL MODAL / LIGHTBOX (FULLSCREEN)
   ======================================================== */
.jpm-blueprint-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(10, 17, 40, 0.82);
  backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  animation: modalFadeIn 0.25s ease-out;
}

@keyframes modalFadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.jpm-blueprint-modal-container {
  width: 96vw;
  height: 94vh;
  max-width: 1800px;
  background: #ffffff;
  border-radius: var(--jpm-radius-card, 12px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
  animation: modalScaleUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalScaleUp {
  from { transform: scale(0.96); opacity: 0.8; }
  to { transform: scale(1); opacity: 1; }
}

.jpm-blueprint-modal-header {
  padding: 14px 24px;
  background: #ffffff;
  border-bottom: 1px solid #edf2f7;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.modal-header-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #fff1f2;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.modal-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 700;
  font-size: 1.1rem;
  color: #0A1128;
}

.modal-subtitle {
  font-size: 0.82rem;
  color: #64748b;
}

.modal-zoom-bar {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
  padding: 3px 8px;
}

.modal-zoom-text {
  font-size: 0.85rem;
  color: #0A1128;
  min-width: 52px;
  text-align: center;
  cursor: pointer;
  user-select: none;
}

.btn-reset-zoom {
  font-size: 0.78rem;
  font-weight: 600;
  color: #64748b;
  border: none;
  background: transparent;
  padding: 3px 8px;
  border-radius: 4px;
  transition: all 0.2s;
}

.btn-reset-zoom:hover {
  background: #e2e8f0;
  color: #0A1128;
}

.btn-modal-close {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #334155;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 8px 16px;
  border-radius: var(--jpm-radius-control, 8px);
  transition: all 0.2s;
}

.btn-modal-close:hover {
  background: #f82249;
  border-color: #f82249;
  color: #ffffff;
}

.jpm-blueprint-modal-body {
  flex: 1;
  background: #ffffff;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: grab;
  user-select: none;
}

.jpm-blueprint-modal-body.is-panning {
  cursor: grabbing;
}

.modal-blueprint-img {
  max-width: 88vw;
  max-height: 80vh;
  object-fit: contain;
  display: block;
  pointer-events: none;
  user-select: none;
  filter: none;
}

.modal-hint-pill {
  position: absolute;
  bottom: 16px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(10, 17, 40, 0.85);
  backdrop-filter: blur(6px);
  color: #ffffff;
  font-size: 0.78rem;
  padding: 6px 16px;
  border-radius: 30px;
  pointer-events: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  letter-spacing: 0.2px;
}

.resizer {
  width: 16px;
  cursor: col-resize;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  margin: 0 -8px;
  z-index: 10;
  user-select: none;
}
.resizer-handle {
  width: 4px;
  margin-top: 270px;
  position: sticky;
  top: 100px;
  height: 60px;
  background-color: #dee2e6;
  border-radius: var(--jpm-radius-badge, 6px);
  transition: all 0.2s ease;
}
.resizer:hover .resizer-handle,
.resizer.dragging .resizer-handle {
  background-color: #f82249;
  width: 6px;
  height: 80px;
}

.custom-checkbox {
  transform: scale(1.15);
  cursor: pointer;
}
.custom-checkbox:checked {
  background-color: var(--accent-color);
  border-color: var(--accent-color);
}

.btn-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: #555;
  transition: all 0.2s;
  padding: 0;
}
.btn-icon:hover {
  background: #f1f3f7;
  color: var(--accent-color);
}

@media (max-width: 991px) {
  .dynamic-sidebar, .dynamic-content {
    width: 100% !important;
  }
}

/* HOTSPOTS INTERACTIVE LAYER (DEC-02) */
.hotspot-overlay-layer {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.hotspot-pin-anchor {
  position: absolute;
  transform: translate(-50%, -50%);
  pointer-events: auto;
  z-index: 5;
}

.hotspot-pin-btn {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--jpm-color-navy-dark, #0A1128);
  color: #ffffff;
  border: 1.5px solid #ffffff;
  box-shadow: 0 2px 6px rgba(10, 17, 40, 0.45);
  font-family: 'Roboto Mono', monospace;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  cursor: pointer;
  position: relative;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  outline: none;
}

.hotspot-pin-btn:hover,
.hotspot-pin-btn.pin-hovered {
  background: var(--jpm-color-red-marine, #F82249);
  transform: scale(1.2);
  z-index: 10;
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.4);
}

.hotspot-pin-btn.pin-active {
  background: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  border-color: #ffffff;
  transform: scale(1.3);
  z-index: 15;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.4);
}

.pin-radar-ring {
  position: absolute;
  top: -4px;
  left: -4px;
  right: -4px;
  bottom: -4px;
  border-radius: 50%;
  border: 2px solid var(--jpm-color-red-marine, #F82249);
  animation: radar-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
  pointer-events: none;
}

@keyframes radar-ping {
  0% { transform: scale(1); opacity: 0.9; }
  75%, 100% { transform: scale(2.2); opacity: 0; }
}

.hotspot-tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%) translateY(-6px);
  background: rgba(10, 17, 40, 0.92);
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #ffffff;
  padding: 4px 8px;
  border-radius: 6px;
  white-space: nowrap;
  pointer-events: none;
  z-index: 20;
  font-size: 11px;
}

.table-hover-highlight td {
  background-color: rgba(248, 34, 73, 0.05) !important;
  --bs-table-accent-bg: transparent !important;
  --bs-table-bg-state: transparent !important;
  box-shadow: none !important;
}
</style>
