<template>
  <main class="brand-catalog-page main-page">
    <!-- ========================================================
         PAGE HEADER & BREADCRUMB BANNER
         ======================================================== -->
    <section class="brand-header-banner">
      <div class="container">
        <!-- Breadcrumb chuẩn hệ thống -->
        <nav aria-label="breadcrumb" class="custom-breadcrumb mb-3" data-aos="fade-down">
          <ol class="breadcrumb mb-0 align-items-center">
            <li class="breadcrumb-item">
              <router-link to="/" class="breadcrumb-link">
                <i class="bi bi-house-door me-1"></i> Trang chủ
              </router-link>
            </li>
            <li class="breadcrumb-item">
              <router-link to="/#sponsors" class="breadcrumb-link">Thương hiệu</router-link>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
              {{ brandName }}
            </li>
          </ol>
        </nav>

        <!-- Brand Title & Logo Container -->
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3" data-aos="fade-up">
          <div>
            <span class="brand-eyebrow">
              <i class="bi bi-patch-check-fill me-1"></i> CATALOG TRA CỨU CHÍNH HÃNG
            </span>
            <h1 class="brand-page-title mb-2">
              Tra cứu Phụ Tùng <span class="brand-title-accent">{{ brandName }}</span>
            </h1>
            <p class="brand-page-subtitle mb-0">
              Lựa chọn loại động cơ, năm sản xuất và mã lực để tìm kiếm phụ tùng chính xác nhất.
            </p>
          </div>

          <!-- Logo thương hiệu nổi bật trên Card trắng tinh tế -->
          <div v-if="brandLogo" class="brand-logo-badge d-none d-md-flex align-items-center justify-content-center flex-shrink-0">
            <img :src="brandLogo" :alt="brandName" class="brand-logo-img" @error="$event.target.style.display='none'" />
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         MAIN CATALOG AREA (BỐ CỤC 2 CỘT CHUẨN ĐỒNG BỘ 12PX CARD)
         ======================================================== -->
    <section class="catalog-content-section">
      <div class="container">
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
            <div class="card catalog-sidebar-card sticky-sidebar">
              <div class="catalog-sidebar-header">
                <h3 class="catalog-sidebar-title">
                  Loại Động Cơ
                </h3>
              </div>
              <div class="catalog-sidebar-body">
                <button
                  v-for="loai in loaiDongCoList"
                  :key="loai.value"
                  type="button"
                  class="catalog-sidebar-btn"
                  :class="{ active: selectedLoai === loai.value }"
                  @click="selectLoai(loai.value)"
                >
                  <span class="sidebar-btn-text">{{ loai.label }}</span>
                  <span class="sidebar-btn-badge">{{ getLoaiCount(loai.value) }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- ================= BÊN PHẢI: BẢNG MODEL & BỘ LỌC ================= -->
          <div class="col-lg-9">
            <div class="card catalog-content-card">
              <!-- Header của Card -->
              <div class="catalog-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="catalog-card-title mb-0">
                  Danh sách Model — <span class="text-danger">{{ activeLoaiLabel }}</span>
                </h2>
                <span class="catalog-count-badge">{{ filteredModels.length }} Models</span>
              </div>

              <div class="catalog-card-body p-3 p-md-4">
                <!-- FILTER BAR ĐỒNG BỘ CHUẨN 8PX & CAO 44PX -->
                <div class="catalog-filter-bar mb-4">
                  <div class="row g-2 align-items-center">
                    <!-- Ô tìm kiếm mã Model -->
                    <div class="col-12 col-md-5 col-xl-5">
                      <div class="search-input-box">
                        <i class="bi bi-search search-input-icon"></i>
                        <input
                          type="text"
                          class="search-input-field"
                          placeholder="Tìm mã Model (VD: BF150, 300HP...)"
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
                      <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'hp' }">
                        <div
                          class="custom-select-box"
                          :class="{ 'is-focused': openDropdown === 'hp', 'has-value': selectedHp !== '' }"
                          @click="toggleDropdown('hp')"
                        >
                          <span class="text-truncate">{{ selectedHp ? selectedHp + ' HP' : 'Tất cả Mã Lực (HP)' }}</span>
                          <i class="bi bi-chevron-down select-arrow" :class="{ rotated: openDropdown === 'hp' }"></i>
                        </div>
                        <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'hp'">
                          <li @click="selectHp('')" :class="{ active: selectedHp === '' }">Tất cả Mã Lực (HP)</li>
                          <li v-for="hp in hpList" :key="hp" @click="selectHp(hp)" :class="{ active: selectedHp === String(hp) }">
                            {{ hp }} HP
                          </li>
                        </ul>
                      </div>
                    </div>

                    <!-- Dropdown Loại Thì -->
                    <div class="col-6 col-sm-4 col-md-auto filter-dropdown-col">
                      <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'stroke' }">
                        <div
                          class="custom-select-box"
                          :class="{ 'is-focused': openDropdown === 'stroke', 'has-value': selectedStroke !== '' }"
                          @click="toggleDropdown('stroke')"
                        >
                          <span class="text-truncate">{{ selectedStroke ? formatStrokeDropdown(selectedStroke) : 'Tất cả Loại Thì' }}</span>
                          <i class="bi bi-chevron-down select-arrow" :class="{ rotated: openDropdown === 'stroke' }"></i>
                        </div>
                        <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'stroke'">
                          <li @click="selectStroke('')" :class="{ active: selectedStroke === '' }">Tất cả Loại Thì</li>
                          <li v-for="stroke in strokesList" :key="stroke" @click="selectStroke(stroke)" :class="{ active: selectedStroke === stroke }">
                            {{ formatStrokeDropdown(stroke) }}
                          </li>
                        </ul>
                      </div>
                    </div>

                    <!-- Dropdown Năm Sản Xuất -->
                    <div class="col-6 col-sm-4 col-md-auto filter-dropdown-col">
                      <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'nam' }">
                        <div
                          class="custom-select-box"
                          :class="{ 'is-focused': openDropdown === 'nam', 'has-value': selectedNam !== '' }"
                          @click="toggleDropdown('nam')"
                        >
                          <span class="text-truncate">{{ selectedNam ? 'Năm ' + selectedNam : 'Tất cả Năm' }}</span>
                          <i class="bi bi-chevron-down select-arrow" :class="{ rotated: openDropdown === 'nam' }"></i>
                        </div>
                        <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'nam'">
                          <li @click="selectNam('')" :class="{ active: selectedNam === '' }">Tất cả Năm</li>
                          <li v-for="year in yearsList" :key="year" @click="selectNam(year)" :class="{ active: selectedNam === String(year) }">
                            {{ year }}
                          </li>
                        </ul>
                      </div>
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

                <!-- BẢNG DỮ LIỆU MODEL CHUẨN THẨM MỸ HÀNG HẢI -->
                <div class="table-responsive custom-scrollbar">
                  <table class="table catalog-table mb-0 align-middle">
                    <thead>
                      <tr>
                        <th class="th-stt text-center">STT</th>
                        <th class="th-model">Model Động Cơ</th>
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
                            <button class="btn btn-outline-danger btn-sm px-3 py-1 rounded-2" @click="clearFilters">
                              <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa tất cả bộ lọc
                            </button>
                          </div>
                        </td>
                      </tr>

                      <!-- Dòng dữ liệu Model -->
                      <tr v-for="(model, index) in filteredModels" :key="model.id" class="catalog-table-row">
                        <td class="text-center text-muted fw-medium">{{ index + 1 }}</td>
                        <td>
                          <div class="model-name">{{ model.name }}</div>
                          <div v-if="model.code && model.code !== model.name" class="model-code-sub">
                            Mã: {{ model.code }}
                          </div>
                        </td>
                        <td class="text-center">
                          <span v-if="model.hp" class="hp-badge">{{ model.hp }} HP</span>
                          <span v-else class="text-muted">-</span>
                        </td>
                        <td class="text-center">
                          <span class="stroke-badge">{{ model.stroke ? formatStrokeText(model.stroke) : '-' }}</span>
                        </td>
                        <td class="text-center text-secondary fw-semibold" style="font-size: 0.9rem;">
                          {{ model.year || '-' }}
                        </td>
                        <td class="text-center">
                          <router-link
                            :to="`/thuong-hieu/${brandRoute}/${model.loai}/${model.year}/${model.code}/so-do`"
                            class="btn btn-view-diagram"
                            title="Xem sơ đồ phụ tùng rã máy"
                          >
                            <span>Sơ Đồ</span>
                            <i class="bi bi-arrow-right ms-1 arrow-icon"></i>
                          </router-link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- CHÂN BẢNG: THỐNG KÊ & CHỈ DẪN -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                  <span class="text-muted small">
                    Hiển thị <b class="text-dark">{{ filteredModels.length }}</b> trong tổng số <b class="text-dark">{{ modelsList.length }}</b> models của hãng {{ brandName }}
                  </span>
                  <span class="text-muted small fst-italic">
                    <i class="bi bi-info-circle me-1 text-danger"></i> Bấm "Sơ Đồ" để tra cứu từng cụm linh kiện chi tiết
                  </span>
                </div>

              </div> <!-- end card-body -->
            </div> <!-- end card -->
          </div> <!-- END COL-LG-9 -->
        </div> <!-- END ROW -->
      </div> <!-- END CONTAINER -->
    </section>
  </main>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';

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

// Tính số lượng model theo loại
const getLoaiCount = (loaiValue) => {
  if (!modelsList.value) return 0;
  return modelsList.value.filter(m => m.loai === loaiValue).length;
};

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
const selectedLoai = ref('');
const selectedNam  = ref('');
const selectedHp   = ref('');
const selectedStroke = ref('');
const searchCode   = ref('');

// Tự động chọn Loại động cơ đầu tiên nếu danh sách có data và chưa có lựa chọn
watch(loaiDongCoList, (newList) => {
  if (newList && newList.length > 0 && !selectedLoai.value) {
    selectedLoai.value = newList[0].value;
  }
}, { immediate: true });

const clearFilters = () => {
  searchCode.value = '';
  selectedNam.value = '';
  selectedHp.value = '';
  selectedStroke.value = '';
};

const selectLoai = (loai) => {
  selectedLoai.value = loai;
  clearFilters();
};

const openDropdown = ref(null);
const toggleDropdown = (type) => {
  openDropdown.value = openDropdown.value === type ? null : type;
};

const selectNam = (val) => {
  selectedNam.value = String(val);
  openDropdown.value = null;
};
const selectHp = (val) => {
  selectedHp.value = String(val);
  openDropdown.value = null;
};
const selectStroke = (val) => {
  selectedStroke.value = String(val);
  openDropdown.value = null;
};

// Đóng dropdown khi click ra ngoài
const handleClickOutside = (event) => {
  if (!openDropdown.value) return;
  if (!event.target.closest('.custom-select-wrapper')) {
    openDropdown.value = null;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  openDropdown.value = null;
});

const activeLoaiLabel = computed(() => {
  return loaiDongCoList.value.find(l => l.value === selectedLoai.value)?.label || '';
});

// Trích xuất danh sách HP duy nhất từ Models hiện tại (đã lọc theo loại)
const hpList = computed(() => {
  if (!modelsList.value.length) return [];
  const hps = modelsList.value
    .filter(m => m.loai === selectedLoai.value && m.hp)
    .map(m => m.hp);
  return [...new Set(hps)].sort((a, b) => b - a);
});

// 🔹 Lọc model theo: Loại đang chọn + Năm + HP + Mã + Thì 🔹
const filteredModels = computed(() => {
  if (!modelsList.value) return [];
  
  return modelsList.value.filter(m => {
    const matchLoai = m.loai === selectedLoai.value;
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
   1. PAGE HEADER & BREADCRUMB BANNER
   ======================================================== */
.brand-header-banner {
  padding-top: 100px;
  padding-bottom: 28px;
  background: #ffffff;
  border-bottom: 1px solid #edf2f7;
  margin-bottom: 30px;
}

.custom-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  content: "›";
  font-size: 1.15rem;
  line-height: 1;
  vertical-align: middle;
  color: #94a3b8;
  padding: 0 8px;
}

.breadcrumb-link {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 500;
  transition: color 0.2s ease;
}

.breadcrumb-link:hover {
  color: var(--accent-color, #f82249) !important;
}

.brand-eyebrow {
  display: inline-flex;
  align-items: center;
  font-family: var(--default-font);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  color: var(--accent-color, #f82249);
  margin-bottom: 6px;
  text-transform: uppercase;
}

.brand-page-title {
  font-family: var(--heading-font);
  font-size: clamp(1.6rem, 3vw, 2.2rem);
  font-weight: 700;
  color: #0A1128;
  letter-spacing: -0.2px;
  line-height: 1.25;
}

.brand-title-accent {
  color: var(--accent-color, #f82249);
  text-transform: uppercase;
}

.brand-page-subtitle {
  font-family: var(--default-font);
  font-size: 0.95rem;
  color: #64748b;
  max-width: 650px;
  line-height: 1.5;
}

.brand-logo-badge {
  width: 140px;
  height: 64px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 8px 16px;
  box-shadow: 0 4px 12px rgba(10, 17, 40, 0.04);
}

.brand-logo-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
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
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 90px;
    z-index: 10;
  }
}

.catalog-sidebar-header {
  padding: 16px 20px;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
}

.catalog-sidebar-title {
  font-family: var(--heading-font);
  font-size: 1rem;
  font-weight: 700;
  color: #0A1128;
  margin: 0;
  display: flex;
  align-items: center;
}

.catalog-sidebar-body {
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.catalog-sidebar-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  border: 1px solid transparent;
  background: transparent;
  padding: 10px 14px;
  border-radius: 8px;
  font-family: var(--default-font);
  font-weight: 500;
  font-size: 0.92rem;
  color: #334155;
  transition: all 0.2s ease;
  cursor: pointer;
  text-align: left;
}

.catalog-sidebar-btn:hover {
  background: #f8fafc;
  color: #0A1128;
}

.catalog-sidebar-btn.active {
  background: rgba(248, 34, 73, 0.07);
  border-color: rgba(248, 34, 73, 0.2);
  color: var(--accent-color, #f82249);
  font-weight: 700;
}

.sidebar-btn-badge {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 999px;
  background: #f1f5f9;
  color: #64748b;
  transition: all 0.2s ease;
}

.catalog-sidebar-btn.active .sidebar-btn-badge {
  background: var(--accent-color, #f82249);
  color: #ffffff;
}

/* ========================================================
   3. CONTENT CARD (BẢNG MODEL & BỘ LỌC)
   ======================================================== */
.catalog-content-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04);
  overflow: visible;
}

.catalog-card-header {
  padding: 16px 24px;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
  border-radius: 12px 12px 0 0;
}

.catalog-card-title {
  font-family: var(--heading-font);
  font-size: 1.15rem;
  font-weight: 700;
  color: #0A1128;
}

.catalog-count-badge {
  font-family: var(--default-font);
  background: #0A1128;
  color: #ffffff;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 999px;
}

/* ========================================================
   4. FILTER BAR: SEARCH & DROPDOWNS (ĐỒNG BỘ 100% TRANG CHỦ)
   ======================================================== */
.search-input-box {
  display: flex;
  align-items: center;
  height: 44px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  box-shadow: inset 0 0 0 0 rgba(248, 34, 73, 0), 0 2px 6px 0 rgba(248, 34, 73, 0);
  padding: 0 14px;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  width: 100%;
}

.search-input-box:focus-within {
  border-width: 1px !important;
  border-color: var(--accent-color, #f82249) !important;
  box-shadow: inset 0 0 0 1px var(--accent-color, #f82249), 0 2px 6px 0 rgba(248, 34, 73, 0.3) !important;
  outline: 0 !important;
}

.search-input-box:hover {
  border-color: #94a3b8;
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

/* Custom Select Controls */
.custom-select-wrapper {
  position: relative;
  width: 100%;
}

.custom-select-wrapper.z-active {
  z-index: 1050;
}

.custom-select-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 14px;
  height: 44px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  box-shadow: inset 0 0 0 0 rgba(248, 34, 73, 0), 0 2px 6px 0 rgba(248, 34, 73, 0);
  cursor: pointer;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  font-size: 0.86rem;
  font-weight: 400;
  color: #64748b;
  font-family: var(--default-font);
  user-select: none;
}

.custom-select-box:hover {
  border-color: #94a3b8;
}

.custom-select-box.is-focused {
  border-width: 1px !important;
  border-color: var(--accent-color, #f82249) !important;
  box-shadow: inset 0 0 0 1px var(--accent-color, #f82249), 0 2px 6px 0 rgba(248, 34, 73, 0.3) !important;
  outline: 0 !important;
}

.custom-select-box.has-value {
  color: #0A1128;
  font-weight: 600;
}

.select-arrow {
  transition: transform 0.2s ease;
  font-size: 11px;
  color: #94a3b8;
  margin-left: 6px;
  flex-shrink: 0;
}

.select-arrow.rotated,
.custom-select-box.is-focused .select-arrow {
  transform: rotate(180deg);
  color: var(--accent-color, #f82249);
}

.custom-dropdown-list {
  position: absolute;
  min-width: 100%;
  width: max-content;
  max-width: 260px;
  top: calc(100% + 4px);
  list-style: none;
  margin: 0;
  padding: 6px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 12px 30px rgba(10, 17, 40, 0.12);
  z-index: 1055;
  max-height: 250px;
  overflow-y: auto;
}

.custom-dropdown-list.align-right {
  right: 0;
  left: auto;
}

.custom-dropdown-list li {
  padding: 8px 12px;
  font-size: 0.85rem;
  color: #334155;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.custom-dropdown-list li:hover {
  background: #f1f5f9;
  color: #0A1128;
}

.custom-dropdown-list li.active {
  background: rgba(248, 34, 73, 0.08);
  color: var(--accent-color, #f82249);
  font-weight: 700;
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
   5. DATA TABLE
   ======================================================== */
.catalog-table {
  border-collapse: separate;
  border-spacing: 0;
}

.catalog-table thead tr th {
  background-color: #f8fafc;
  color: #475569;
  font-family: var(--default-font);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  padding: 12px 14px;
  border-top: none;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.th-stt { width: 6%; }
.th-model { width: 38%; }
.th-hp { width: 14%; }
.th-stroke { width: 12%; }
.th-year { width: 12%; }
.th-action { width: 18%; }

.catalog-table tbody tr {
  transition: background-color 0.18s ease;
}

.catalog-table tbody tr:hover {
  background-color: #f8fafc;
}

.catalog-table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
}

.model-name {
  font-weight: 700;
  color: #0A1128;
  font-size: 0.94rem;
  line-height: 1.35;
}

.model-code-sub {
  font-size: 0.78rem;
  color: #64748b;
  margin-top: 2px;
}

.hp-badge {
  display: inline-block;
  padding: 3px 8px;
  background-color: #eff6ff;
  color: #1d4ed8;
  font-weight: 700;
  font-size: 0.82rem;
  border-radius: 6px;
  border: 1px solid #dbeafe;
}

.stroke-badge {
  display: inline-block;
  padding: 3px 8px;
  background-color: #f8fafc;
  color: #334155;
  font-weight: 600;
  font-size: 0.82rem;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.btn-view-diagram {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 14px;
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: 8px;
  color: var(--accent-color, #f82249);
  background: rgba(248, 34, 73, 0.06);
  border: 1px solid rgba(248, 34, 73, 0.25);
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.25s ease;
}

.btn-view-diagram .arrow-icon {
  transition: transform 0.2s ease;
}

.btn-view-diagram:hover {
  background: var(--accent-color, #f82249);
  border-color: var(--accent-color, #f82249);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(248, 34, 73, 0.25);
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
