<template>
  <div class="brand-catalog-page main-page">
    <!-- ========================================================
         MAIN CATALOG AREA (ĐỒNG BỘ 100% VỚI GIỎ HÀNG)
         ======================================================== -->
    <section class="catalog-content-section pb-5 inner-page-section">
      <div class="container">

        <!-- Header chuẩn Giỏ Hàng -->
        <div class="row mb-4">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">Tra Cứu Phụ Tùng <span class="text-danger">{{ brandName }}</span></h2>
            <p class="text-muted">Lựa chọn loại động cơ, năm sản xuất và mã lực để tìm kiếm phụ tùng chính xác nhất.</p>
          </div>
        </div>
        <!-- Skeleton Loading khi đang tải dữ liệu -->
        <div v-if="isLoading" class="row g-4">
          <div class="col-lg-3">
            <div class="card catalog-sidebar-card border-0 p-3">
              <div class="skeleton-line mb-3 w-50" style="height: 18px;"></div>
              <div class="skeleton-line mb-2 w-100" style="height: 42px; border-radius: 8px;"></div>
              <div class="skeleton-line mb-2 w-100" style="height: 42px; border-radius: 8px;"></div>
            </div>
          </div>
          <div class="col-lg-9">
            <div class="card catalog-content-card border-0 p-4">
              <div class="d-flex justify-content-between mb-4">
                <div class="skeleton-line w-50" style="height: 24px;"></div>
                <div class="skeleton-line w-25" style="height: 24px;"></div>
              </div>
              <div class="skeleton-line mb-4 w-100" style="height: 44px; border-radius: 8px;"></div>
              <div v-for="n in 5" :key="'skel-row-' + n" class="skeleton-line mb-2 w-100" style="height: 48px;"></div>
            </div>
          </div>
        </div>

        <!-- Dữ liệu thật -->
        <div v-else class="row g-4" data-aos="fade-up" data-aos-delay="100">
          <!-- ================= BÊN TRÁI: LOẠI ĐỘNG CƠ ================= -->
          <div class="col-lg-3">
            <div class="card catalog-sidebar-card sticky-sidebar shadow-sm">
              <div class="catalog-sidebar-header">
                <h3 class="catalog-sidebar-title mb-0">
                  Loại Động Cơ
                </h3>
                <button 
                  type="button"
                  class="btn-reset-sidebar-filter" 
                  :class="{ 'is-hidden': !selectedLoai }"
                  @click="selectLoai('')"
                >
                  Đặt lại
                </button>
              </div>
              <div class="catalog-sidebar-body">
                <!-- DANH MỤC LOẠI ĐỘNG CƠ -->
                <div class="catalog-nav-list">
                  <!-- Nút Tất Cả Động Cơ -->
                  <button
                    type="button"
                    class="catalog-nav-btn"
                    :class="{ active: !selectedLoai }"
                    @click="selectLoai('')"
                  >
                    <span class="catalog-nav-text">Tất cả động cơ</span>
                    <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                  </button>

                  <!-- Danh mục loại động cơ từ API -->
                  <button
                    v-for="loai in loaiDongCoList"
                    :key="loai.value"
                    type="button"
                    class="catalog-nav-btn"
                    :class="{ active: selectedLoai === loai.value }"
                    @click="selectLoai(loai.value)"
                  >
                    <span class="catalog-nav-text" :title="loai.label">{{ loai.label }}</span>
                    <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= BÊN PHẢI: BẢNG MODEL & BỘ LỌC ================= -->
          <div class="col-lg-9">
            <div class="card catalog-content-card">
              <!-- 1. Header của Card: Đồng bộ 100% Giỏ Hàng -->
              <div class="card-header bg-white px-4 py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="min-height: 60px;">
                <div class="d-flex align-items-center">
                  <h5 class="mb-0 jpm-card-title">
                    Danh Sách Model — <span class="text-danger">{{ activeLoaiLabel }}</span>
                  </h5>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                  <span class="badge-item-count">{{ filteredModels.length }} Models</span>
                </div>
              </div>

              <!-- 2. Filter Bar: Nằm riêng biệt với px-4 py-3 và border-bottom -->
              <div class="catalog-filter-bar px-4 py-3 border-bottom bg-white">
                <div class="row g-2 align-items-center">
                  <!-- Ô tìm kiếm Model -->
                  <div class="col-12 col-md-5 col-xl-5">
                    <div class="search-input-box">
                      <i class="bi bi-search search-input-icon"></i>
                      <input
                        type="text"
                        class="search-input-field"
                        placeholder="Tìm kiếm Model (VD: BF150, 300HP...)"
                        v-model="searchCode"
                      />
                      <button
                        v-if="searchCode"
                        type="button"
                        class="btn-clear-search"
                        @click="searchCode = ''"
                        title="Xóa tìm kiếm"
                      >
                        <i class="bi bi-x-circle-fill"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Dropdown Mã Lực (HP) -->
                  <div class="col-6 col-sm-4 col-md-auto filter-dropdown-col">
                    <JpmSelect
                      v-model="selectedHp"
                      :options="hpOptions"
                      placeholder="Tất cả Mã Lực (HP)"
                      value-key="value"
                      label-key="label"
                    />
                  </div>

                  <!-- Dropdown Loại Thì -->
                  <div class="col-6 col-sm-4 col-md-auto filter-dropdown-col">
                    <JpmSelect
                      v-model="selectedStroke"
                      :options="strokeOptions"
                      placeholder="Tất cả Loại Thì"
                      value-key="value"
                      label-key="label"
                    />
                  </div>

                  <!-- Dropdown Năm Sản Xuất -->
                  <div class="col-6 col-sm-4 col-md-auto filter-dropdown-col">
                    <JpmSelect
                      v-model="selectedNam"
                      :options="yearOptions"
                      placeholder="Tất cả Năm"
                      value-key="value"
                      label-key="label"
                    />
                  </div>

                  <!-- Nút Xóa Lọc Nhanh -->
                  <div v-if="searchCode || selectedNam || selectedHp || selectedStroke" class="col-auto">
                    <button
                      type="button"
                      class="btn btn-outline-danger btn-reset-filter d-inline-flex align-items-center gap-1 text-nowrap"
                      @click="clearFilters"
                      title="Xóa tất cả bộ lọc"
                    >
                      <i class="bi bi-arrow-counterclockwise"></i>
                      <span>Xóa lọc</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- 3. BẢNG DỮ LIỆU MODEL: p-0 để bảng tràn viền sát mép thẻ Card hệt Giỏ Hàng -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table align-middle mb-0 custom-model-table">
                    <thead>
                      <tr>
                        <th class="th-stt text-center">STT</th>
                        <th class="th-model text-start">Model Động Cơ</th>
                        <th class="th-hp text-center">Mã Lực</th>
                        <th class="th-stroke text-center">Loại Thì</th>
                        <th class="th-year text-center">Năm SX</th>
                        <th class="th-action text-center">Thao Tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Trạng thái trống khi lọc không ra kết quả -->
                      <tr v-if="filteredModels.length === 0">
                        <td colspan="6" class="p-0 border-0">
                          <div class="empty-state text-center py-5">
                            <div class="empty-icon-wrap mb-2">
                              <i class="bi bi-search fs-2 text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Không tìm thấy model phù hợp</h6>
                            <p class="text-muted small mb-3">Vui lòng điều chỉnh hoặc xóa bớt tiêu chí lọc</p>
                            <button class="btn btn-outline-danger btn-sm px-3 py-1" @click="clearFilters">
                              <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa tất cả bộ lọc
                            </button>
                          </div>
                        </td>
                      </tr>

                      <!-- Dòng dữ liệu Model (padding 16px, hover nhẹ #f8fafc, viền mảnh chuẩn Giỏ Hàng) -->
                      <tr v-for="(model, index) in filteredModels" :key="model.id" class="table-row-hover">
                        <td class="th-stt text-center text-muted fw-medium">{{ index + 1 }}</td>
                        <td class="th-model text-start">
                          <router-link
                            :to="`/thuong-hieu/${brandRoute}/${model.loai}/${model.year}/${model.code}/so-do`"
                            class="model-name-link text-decoration-none"
                            title="Xem sơ đồ phụ tùng rã máy"
                          >
                            <span class="model-name">{{ model.name }}</span>
                          </router-link>
                        </td>
                        <td class="th-hp text-center">
                          <span v-if="model.hp" class="spec-text">{{ model.hp }} HP</span>
                          <span v-else class="text-muted">-</span>
                        </td>
                        <td class="th-stroke text-center">
                          <span v-if="model.stroke" class="spec-text">{{ formatStrokeText(model.stroke) }}</span>
                          <span v-else class="text-muted">-</span>
                        </td>
                        <td class="th-year text-center">
                          <span v-if="model.year" class="spec-text">{{ model.year }}</span>
                          <span v-else class="text-muted">-</span>
                        </td>
                        <td class="th-action text-center">
                          <router-link
                            :to="`/thuong-hieu/${brandRoute}/${model.loai}/${model.year}/${model.code}/so-do`"
                            class="btn btn-view-diagram"
                            title="Xem sơ đồ phụ tùng rã máy"
                          >
                            <span>Sơ Đồ</span>
                            <i class="bi bi-arrow-right arrow-icon"></i>
                          </router-link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div> <!-- end card -->
          </div> <!-- END COL-LG-9 -->
        </div> <!-- END ROW -->
      </div> <!-- END CONTAINER -->
    </section>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import JpmSelect from '../../../components/client/JpmSelect.vue';

const route = useRoute();

const brandRoute = computed(() => route.params.brand || 'yamaha');

// Gọi API lấy Chi tiết hãng, loại động cơ, models và mốc năm
const { data: catalogData, isLoading } = useQuery({
  queryKey: ['brand-catalog', brandRoute],
  queryFn: () => axiosClient.get(`/public/thuong-hieu/${brandRoute.value}`).then(res => res.data?.data || res.data || res),
  staleTime: 1000 * 60 * 10
});

const brandName = computed(() => catalogData.value?.brand?.name || brandRoute.value.toUpperCase());
const loaiDongCoList = computed(() => catalogData.value?.loaiDongCoList || []);
const yearsList = computed(() => catalogData.value?.years || []);
const modelsList = computed(() => catalogData.value?.models || []);
const strokesList = computed(() => catalogData.value?.strokes || []);

// Đường dẫn Logo hãng sắc nét
const brandLogo = computed(() => {
  const p = catalogData.value?.brand?.logo_path;
  if (!p) return null;
  if (p.startsWith('http') || p.startsWith('data:')) return p;
  if (p.startsWith('/client/')) return p;
  const baseUrl = import.meta.env.VITE_API_BASE_URL ? import.meta.env.VITE_API_BASE_URL.replace(/\/api\/?$/, '') : 'http://localhost:8000';
  return `${baseUrl}/storage/${p.replace(/^\//, '')}`;
});


// Định dạng Loại thì
const formatStroke = (stroke) => {
  if (!stroke) return '';
  return String(stroke).replace(/[^0-9]/g, '');
};

const formatStrokeText = (stroke) => {
  if (!stroke) return '-';
  const num = formatStroke(stroke);
  return num ? `${num} Thì` : stroke;
};

const formatStrokeDropdown = (stroke) => {
  if (!stroke) return 'Tất cả Loại Thì';
  const num = formatStroke(stroke);
  return num ? `${num} Thì` : stroke;
};

// 🔹 STATE Lọc dữ liệu 🔹
const selectedLoai = ref(route.query.loai || '');
const selectedNam  = ref('');
const selectedHp   = ref('');
const selectedStroke = ref('');
const searchCode   = ref('');

const hasActiveFilters = computed(() => {
  return !!(selectedLoai.value || selectedNam.value || selectedHp.value || selectedStroke.value || searchCode.value);
});

const clearFilters = () => {
  searchCode.value = '';
  selectedNam.value = '';
  selectedHp.value = '';
  selectedStroke.value = '';
};

const clearAllFilters = () => {
  selectedLoai.value = '';
  clearFilters();
};

const selectLoai = (loai) => {
  selectedLoai.value = loai;
  clearFilters();
};

// Tiêu đề trang động (SEO & User Experience)
watch(brandName, (name) => {
  if (name) {
    document.title = `Tra Cứu Phụ Tùng ${name} | Sơ Đồ Máy Thủy JPM Marine`;
  }
}, { immediate: true });

const activeLoaiLabel = computed(() => {
  if (!selectedLoai.value) return 'Tất Cả';
  return loaiDongCoList.value.find(l => l.value === selectedLoai.value)?.label || selectedLoai.value;
});

// Trích xuất danh sách HP duy nhất từ Models hiện tại (đã lọc theo loại)
const hpList = computed(() => {
  if (!modelsList.value.length) return [];
  const hps = modelsList.value
    .filter(m => (selectedLoai.value ? m.loai === selectedLoai.value : true) && m.hp)
    .map(m => m.hp);
  return [...new Set(hps)].sort((a, b) => b - a);
});

const hpOptions = computed(() => [
  { value: '', label: 'Tất cả Mã Lực (HP)' },
  ...hpList.value.map(hp => ({ value: String(hp), label: `${hp} HP` }))
]);

const strokeOptions = computed(() => [
  { value: '', label: 'Tất cả Loại Thì' },
  ...strokesList.value.map(s => ({ value: String(s), label: formatStrokeDropdown(s) }))
]);

const yearOptions = computed(() => [
  { value: '', label: 'Tất cả Năm' },
  ...yearsList.value.map(y => ({ value: String(y), label: `Năm ${y}` }))
]);

// 🔹 Lọc model theo: Loại đang chọn + Năm + HP + Mã + Thì 🔹
const filteredModels = computed(() => {
  if (!modelsList.value) return [];
  
  return modelsList.value.filter(m => {
    const matchLoai = selectedLoai.value ? m.loai === selectedLoai.value : true;
    const matchNam  = selectedNam.value  ? m.year === Number(selectedNam.value)  : true;
    const matchHp   = selectedHp.value   ? m.hp   === Number(selectedHp.value)   : true;
    const matchStroke = selectedStroke.value ? m.stroke === selectedStroke.value : true;
    const matchCode = searchCode.value
      ? (m.code?.toLowerCase().includes(searchCode.value.toLowerCase()) || m.name?.toLowerCase().includes(searchCode.value.toLowerCase()))
      : true;
    return matchLoai && matchNam && matchHp && matchStroke && matchCode;
  });
});
</script>

<style scoped>
/* ========================================================
   TOÀN TRANG: TỔNG THỂ & PADDING CHUẨN DESIGN SYSTEM
   ======================================================== */
.brand-catalog-page {
  min-height: 100vh;
  background-color: #f8fafc;
  padding-bottom: 60px;
}

/* ========================================================
   1. PAGE HEADER (MATCHING GIO HANG)
   ======================================================== */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 800;
  color: #0A1128;
  letter-spacing: -0.5px;
}

/* ========================================================
   2. SIDEBAR CARD (LOẠI ĐỘNG CƠ)
   ======================================================== */
.catalog-sidebar-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.03);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 90px;
    z-index: 10;
  }
  .catalog-sidebar-card {
    min-height: 260px;
  }
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

.btn-reset-sidebar-filter {
  background: transparent;
  border: none;
  color: #f82249;
  font-size: 0.82rem;
  font-weight: 600;
  padding: 0;
  cursor: pointer;
  line-height: 1;
  transition: opacity 0.2s ease, visibility 0.2s ease, color 0.15s ease;
}

.btn-reset-sidebar-filter.is-hidden {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}

.btn-reset-sidebar-filter:hover {
  color: #d8143a;
  text-decoration: underline;
}

.catalog-sidebar-body {
  padding: 16px;
  display: flex;
  flex-direction: column;
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

/* ── NAVIGATION LIST IN SIDEBAR (Đồng bộ 100% Cửa Hàng SanPham) ── */
.catalog-nav-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 4px 6px 4px 0;
  overflow-x: hidden !important;
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
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  text-align: left;
}

.catalog-nav-btn:hover {
  background: #f1f5f9;
  color: #0A1128;
  font-weight: 600;
  transform: translateX(4px);
}

.catalog-nav-btn.active {
  background: rgba(248, 34, 73, 0.08);
  border-color: rgba(248, 34, 73, 0.2);
  color: #f82249;
  font-weight: 700;
}

.catalog-nav-btn.active:hover {
  background: rgba(248, 34, 73, 0.12);
  color: #f82249;
  transform: translateX(4px);
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
   3. CONTENT CARD (BẢNG MODEL & BỘ LỌC)
   ======================================================== */
.catalog-content-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 20px rgba(10, 17, 40, 0.04);
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

/* ========================================================
   4. FILTER BAR: SEARCH & DROPDOWNS (ĐỒNG BỘ 100% TRANG CHỦ)
   ======================================================== */
.search-input-box {
  display: flex;
  align-items: center;
  height: 44px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: none;
  padding: 0 14px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  width: 100%;
}

.search-input-box:focus-within {
  border-width: 1px !important;
  border-color: var(--accent-color, #f82249) !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: 0 !important;
}

.search-input-box:hover {
  border-color: #cbd5e1;
}

.search-input-icon {
  font-size: 0.95rem;
  color: #94a3b8;
  margin-right: 10px;
  flex-shrink: 0;
  transition: color 0.15s ease-in-out;
}

.search-input-box:focus-within .search-input-icon {
  color: var(--accent-color, #f82249);
}

.search-input-field {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.875rem;
  font-family: var(--default-font);
  color: var(--heading-color, #0A1128);
  font-weight: 400;
  padding: 0;
  height: 100%;
}

.search-input-field::placeholder {
  color: #94a3b8;
  transition: all 0.25s ease;
}

.search-input-field:focus::placeholder {
  transform: translate(5px);
  transition: all 0.25s ease;
}

.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 0.9rem;
  cursor: pointer;
  padding: 0;
  margin-left: 8px;
  line-height: 1;
  flex-shrink: 0;
  transition: color 0.15s ease;
}

.btn-clear-search:hover {
  color: #dc2626;
}

.filter-dropdown-col {
  min-width: 145px;
  flex-grow: 1;
}

.btn-reset-filter {
  height: 44px;
  padding: 0 16px;
  font-size: 0.85rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.2s ease;
}

/* ========================================================
   5. DATA TABLE (ĐỒNG BỘ 100% BẢNG GIỎ HÀNG)
   ======================================================== */
.custom-model-table {
  width: 100%;
}

.custom-model-table thead th {
  background-color: #ffffff !important;
  color: #0A1128 !important;
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.2px;
  text-transform: none;
  border-top: 1px solid #edf2f7 !important;
  border-bottom: 2px solid #e2e8f0 !important;
  padding-top: 13px;
  padding-bottom: 13px;
  vertical-align: middle;
}

.custom-model-table tbody td {
  padding: 14px 12px;
  border-bottom: 1px solid #edf2f7;
  vertical-align: middle;
  transition: background-color 0.15s ease;
}

.custom-model-table tbody tr:nth-child(odd) td {
  background-color: #ffffff;
}

.custom-model-table tbody tr:nth-child(even) td {
  background-color: #f8fafc;
}

.th-stt {
  width: 60px;
  padding-left: 24px !important;
  padding-right: 12px !important;
}

.th-model {
  /* co giãn tự nhiên */
}

.th-hp {
  width: 130px;
  white-space: nowrap !important;
}

.th-stroke {
  width: 120px;
  white-space: nowrap !important;
}

.th-year {
  width: 110px;
  white-space: nowrap !important;
  font-size: 0.9rem;
  font-variant-numeric: tabular-nums;
}

.th-action {
  width: 130px;
  padding-right: 24px !important;
  padding-left: 12px !important;
  white-space: nowrap !important;
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

.table-row-hover:hover .model-name {
  color: #f82249 !important;
}

.table-row-hover:hover .btn-view-diagram {
  background-color: var(--accent-color, #f82249) !important;
  border-color: var(--accent-color, #f82249) !important;
  color: #ffffff !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(248, 34, 73, 0.25);
}

.table-row-hover:hover .btn-view-diagram .arrow-icon {
  transform: translateX(3px);
}

.model-name-link {
  display: inline-block;
  text-decoration: none;
}

.model-name-link:hover .model-name {
  color: #f82249 !important;
}

.model-name {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-weight: 700;
  color: #0A1128;
  font-size: 0.95rem;
  line-height: 1.4;
  transition: color 0.15s ease;
}

/* Thông số kỹ thuật (Mã lực, Loại thì, Năm SX): Màu đen Deep Navy sắc nét, đồng bộ chuẩn kỹ thuật */
.spec-text {
  display: inline-block;
  color: #0A1128;
  font-weight: 600;
  font-size: 0.92rem;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}

/* Nút Thao Tác Sơ Đồ: Chuẩn hóa kích thước đồng bộ 100% với nút Thao Tác bên Sơ Đồ Phụ Tùng */
.btn-view-diagram {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  height: 32px;
  min-width: 88px;
  padding: 0 14px;
  font-size: 0.84rem;
  font-weight: 600;
  border-radius: 8px;
  color: var(--accent-color, #f82249);
  background-color: #ffffff;
  border: 1px solid var(--accent-color, #f82249);
  text-decoration: none;
  white-space: nowrap;
  line-height: 1;
  transition: all 0.2s ease;
}

.btn-view-diagram .arrow-icon {
  transition: transform 0.2s ease;
}

.btn-view-diagram:hover {
  background-color: var(--accent-color, #f82249);
  border-color: var(--accent-color, #f82249);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.22);
}

.btn-view-diagram:hover .arrow-icon {
  transform: translateX(3px);
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Skeleton Loading Helpers */
.skeleton-line {
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
}

@keyframes skeleton-shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
