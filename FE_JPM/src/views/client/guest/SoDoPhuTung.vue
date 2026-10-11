<template>
  <main class="interactive-diagram-page main-page">
    <section class="section" style="padding-top: 110px;">
      <div class="container">
      
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
            <router-link to="/san-pham" class="btn btn-jpm px-4 py-2 rounded shadow-sm fw-bold text-uppercase mt-2">
              <i class="bi bi-cart-dash me-2"></i> Trở về Cửa Hàng
            </router-link>
          </div>
        </div>
      </template>

      <!-- SPLIT VIEW AREA -->
      <div v-else class="jpm-split-layout" id="main-split-container" style="gap: 0;" data-aos="fade-up" data-aos-delay="100">

        
        <!-- CỘT TRÁI: Cụm Chi Tiết -->
        <div class="jpm-split-sidebar dynamic-sidebar mb-4 mb-lg-0 pe-lg-2" :style="{ width: sidebarRatio + '%' }">
          <div class="card sticky-sidebar border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 px-4">
              <h4 class="mb-0 jpm-card-title">Cụm Chi Tiết</h4>
            </div>
            <div class="list-group list-group-flush category-list">
              <div v-if="isLoading" class="p-3 text-center text-muted">Đang tải...</div>
              <div v-else-if="diagramsList.length === 0" class="p-3 text-center text-muted">Chưa có sơ đồ nào.</div>

              <button 
                v-for="diagram in diagramsList" 
                :key="diagram.id"
                class="list-group-item list-group-item-action text-start py-3 px-4 d-flex align-items-center"
                :class="{ active: activeComponentId === diagram.id }"
                @click="activeComponentId = diagram.id"
              >
                <span class="flex-grow-1">{{ diagram.nhom_phu_tung?.ten_nhom || "N/A" }}</span>
                <i class="bi bi-chevron-right chevron-icon"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- LEFT RESIZER -->
        <div class="resizer d-none d-lg-flex" @mousedown.prevent="startSidebarDrag" :class="{ 'dragging': isSidebarDragging }" title="Kéo để thay đổi kích thước">
          <div class="resizer-handle"></div>
        </div>

        <!-- CỘT PHẢI: BẢN VẼ & SẢN PHẨM -->
        <div class="jpm-split-content dynamic-content ps-lg-2" :style="{ width: (100 - sidebarRatio) + '%' }">
          
          <div class="d-flex w-100 align-items-stretch" id="split-container" style="min-height: 600px;">
            
            <!-- NỬA TRÁI: BẢN VẼ (Pan + Zoom) -->
            <div :style="{ width: splitRatio + '%' }" class="pe-2">
              <div class="card sticky-sidebar border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; height: 600px;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" v-if="activeComponentId">
                  <h4 class="mb-0 jpm-card-title text-truncate">Sơ đồ phân rã: <span class="text-danger">{{ activeComponentName }}</span></h4>
                </div>
                <div class="card-body p-0 position-relative bg-white d-flex align-items-center justify-content-center h-100" style="min-height: 400px;">
                  <h4 class="text-muted" v-if="!activeComponentId">Vui lòng chọn một Cụm Chi Tiết ở cột trái</h4>
                  <div v-else class="w-100 h-100 d-flex flex-column" style="position: relative;">
                    <!-- Floating Toolbar -->
                    <div class="position-absolute shadow-sm d-flex align-items-center" style="top: 0; right: 0; z-index: 1060; background: white; border-radius: 0 0 0 6px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; padding: 4px 6px;">
                      <button class="btn btn-sm btn-icon" @click="zoomOut" title="Thu nhỏ"><i class="bi bi-dash-lg"></i></button>
                      <span class="fw-bold px-2" style="color: #0e1b4d; font-size: 0.85rem; min-width: 48px; text-align: center; cursor: pointer;" @click="resetZoom" title="Đặt lại tỷ lệ">{{ Math.round(zoomLevel * 100) }}%</span>
                      <button class="btn btn-sm btn-icon" @click="zoomIn" title="Phóng to"><i class="bi bi-plus-lg"></i></button>
                      <div class="vr mx-1 my-1" style="opacity: 0.1; min-height: 20px;"></div>
                      <button class="btn btn-sm btn-icon" @click="toggleFullScreen" :title="isFullScreen ? 'Đóng toàn màn hình' : 'Toàn màn hình'" :class="{ 'text-danger': isFullScreen }">
                        <i class="bi" :class="isFullScreen ? 'bi-fullscreen-exit' : 'bi-arrows-fullscreen'"></i>
                      </button>
                    </div>
                    <!-- PAN + ZOOM VIEWPORT (Boats.net style) -->
                    <div 
                      ref="diagramViewport"
                      :class="{ 'is-panning': isPanning, 'fullscreen-mode': isFullScreen }"
                      @mousedown="startPan"
                      @wheel.prevent="handleWheel"
                      style="overflow: hidden; cursor: grab; width: 100%; flex: 1; min-height: 320px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; user-select: none;"
                    >
                      <div :style="{ transform: `translate(${panX}px, ${panY}px) scale(${zoomLevel})`, transformOrigin: 'center center', transition: isPanning ? 'none' : 'transform 0.1s ease' }">
                        <img v-if="activeDiagram?.hinh_anh" :src="getImageUrl(activeDiagram.hinh_anh)" alt="Sơ đồ phân rã" style="max-width: 100%; max-height: 400px; object-fit: contain; display: block; pointer-events: none;" draggable="false">
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
              <div class="card flex-grow-1 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;" v-if="activeComponentId">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                  <h4 class="mb-0 jpm-card-title text-truncate" style="max-width: 100%;">Danh sách Phụ Tùng</h4>
                  <div class="d-flex align-items-center gap-2 ms-auto">
                    <span class="badge rounded-pill px-3 py-2 text-nowrap" style="background-color: #f1f3f7; color: #0e1b4d; font-weight: 700;">{{ currentParts.length }} Phụ Tùng</span>
                    <button @click="addAllSelectedToCart" v-if="selectedParts.length > 0" class="btn btn-outline-jpm btn-sm rounded px-3 py-1 fw-bold text-nowrap d-inline-flex align-items-center justify-content-center shadow-none" style="transition: all 0.3s ease;" title="Thêm tất cả đã chọn vào giỏ">
                      <i class="bi bi-cart-plus me-1"></i> Thêm ({{ selectedParts.length }})
                    </button>
                  </div>
                </div>
                <div class="card-body p-0 d-flex flex-column h-100" style="">
                  <div class="table-responsive flex-grow-1 hide-x-scroll">
                    <table class="table custom-table mb-0 align-middle table-hover table-jpm">
                      <thead style="background-color: #f8f9fa; position: sticky; top: 0; z-index: 2;">
                        <tr>
                          <th class="text-center border-bottom-0" style="width: 40px; padding-left: 1rem; color: #a1acb8; background-color: #f8f9fa;">
                            <input class="form-check-input custom-checkbox" type="checkbox" v-model="selectAll">
                          </th>
                          <th class="text-center text-uppercase border-bottom-0" style="width: 50px; color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #f8f9fa;">Ref</th>
                          <th class="text-uppercase border-bottom-0" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #f8f9fa;">Mã & Tên</th>
                          <th class="text-center text-uppercase border-bottom-0" style="width: 100px; color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #f8f9fa;">Số lượng</th>
                          <th class="text-center text-uppercase border-bottom-0" style="width: 120px; color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #f8f9fa;">Giá bán</th>
                          <th class="text-center text-uppercase border-bottom-0" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #f8f9fa;">Thao tác</th>
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
                          :class="{ 'highlighted-part-row': targetPartCode && part.phu_tung?.ma_phu_tung?.toUpperCase() === targetPartCode }"
                          :data-part-code="part.phu_tung?.ma_phu_tung?.toUpperCase()"
                          style="transition: all 0.2s ease;"
                        >
                          <td class="text-center" style="padding-left: 1rem;">
                            <input class="form-check-input custom-checkbox" type="checkbox" :value="part.id" v-model="selectedParts">
                          </td>
                          <td class="text-center">
                            <span class="fw-bold text-secondary" style="font-size: 0.95rem;">{{ part.ref_number }}</span>
                          </td>
                          <td>
                            <router-link :to="`/chi-tiet-phu-tung/${part.phu_tung?.ma_phu_tung}${diagramResponse?.data?.model?.id ? '?from_model_id=' + diagramResponse.data.model.id : ''}`" class="text-decoration-none d-block">
                              <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold model-code" style="color: var(--heading-color);">{{ part.phu_tung?.ma_phu_tung }}</span>
                                <span v-if="targetPartCode && part.phu_tung?.ma_phu_tung?.toUpperCase() === targetPartCode" class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                  <i class="bi bi-geo-alt-fill me-1"></i>Đang xem
                                </span>
                              </div>
                              <div class="text-muted small fw-medium">{{ part.phu_tung?.ten_phu_tung }}</div>
                            </router-link>
                          </td>
                          <td class="text-center">
                            <span class="fw-semibold text-secondary" style="font-size: 0.95rem;">{{ part.qty || 1 }}</span>
                          </td>
                          <td class="text-center">
                            <span v-if="part.phu_tung?.is_gia_lien_he" class="text-danger fw-bold" style="font-size: 0.95rem;">Liên hệ</span>
                            <span v-else class="fw-bold text-nowrap text-danger" style="font-size: 0.95rem;">
                              {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(part.phu_tung?.gia_ban || 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <button @click="addToCart(part)" class="btn btn-outline-danger btn-sm rounded px-3 py-1 fw-bold text-nowrap d-inline-flex align-items-center justify-content-center shadow-none" title="Thêm vào giỏ" style="transition: all 0.3s ease;">
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
  </main>
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

// ─── PAN + ZOOM (Boats.net style) ─────────────────────────────────────────────
const zoomLevel = ref(1);
const panX = ref(0);
const panY = ref(0);
const isPanning = ref(false);
const isFullScreen = ref(false);
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

const zoomIn = () => { zoomLevel.value = Math.min(5, zoomLevel.value + 0.2); };
const zoomOut = () => { zoomLevel.value = Math.max(0.2, zoomLevel.value - 0.2); };
const resetZoom = () => { zoomLevel.value = 1; panX.value = 0; panY.value = 0; };
const toggleFullScreen = () => {
  isFullScreen.value = !isFullScreen.value;
  if (!isFullScreen.value) resetZoom();
};

// ─── SPLIT RESIZER (Diagram vs Parts) ─────────────────────────────────────────
const splitRatio = ref(42);
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
const sidebarRatio = ref(18);
const isSidebarDragging = ref(false);

const onSidebarDrag = (e) => {
  if (!isSidebarDragging.value) return;
  const container = document.getElementById('main-split-container');
  if (container) {
    const rect = container.getBoundingClientRect();
    let newRatio = ((e.clientX - rect.left) / rect.width) * 100;
    if (newRatio < 15) newRatio = 15;
    if (newRatio > 50) newRatio = 50;
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

.price-text {
  color: var(--accent-color);
  font-size: 0.95rem;
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 100px;
  }
}

.fullscreen-mode {
  position: fixed !important;
  top: 0;
  left: 0;
  width: 100vw !important;
  height: 100vh !important;
  z-index: 1050;
  background-color: #f8f9fa !important;
  border-radius: 0 !important;
  border: none !important;
  margin: 0 !important;
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
  border-radius: 4px;
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

.highlighted-part-row {
  background-color: #fff4e5 !important;
  border-left: 4px solid #f82249 !important;
  box-shadow: inset 0 0 10px rgba(248, 34, 73, 0.12);
  animation: pulse-part-bg 2s infinite alternate ease-in-out;
}

@keyframes pulse-part-bg {
  0% { background-color: #fff4e5; }
  100% { background-color: #ffe8cc; }
}

@media (max-width: 991px) {
  .dynamic-sidebar, .dynamic-content {
    width: 100% !important;
  }
}
</style>














