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
              <div class="card-header bg-white px-4 border-bottom d-flex align-items-center" style="height: 76px;">
                <h4 class="mb-0 jpm-card-title">Loại Động Cơ</h4>
              </div>
              <div class="card-body p-2">
                <div class="d-flex flex-column gap-1">
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
          </div>

          <!-- ================= BÊN PHẢI: BẢNG MODEL & BỘ LỌC ================= -->
          <div class="col-lg-9">
            <div class="card catalog-content-card">
              <!-- Header của Card (Chuẩn height 76px & badge hồng pastel y hệt Giỏ hàng) -->
              <div class="card-header bg-white px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="height: 76px;">
                <div class="d-flex align-items-center">
                  <h4 class="mb-0 jpm-card-title">
                    Danh Sách Model — <span class="text-danger">{{ activeLoaiLabel }}</span>
                  </h4>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                  <span class="badge rounded-pill px-3 py-2 text-nowrap" style="background-color: #fde6ea; color: #f82249; font-weight: 700;">
                    {{ filteredModels.length }} Models
                  </span>
                </div>
              </div>

              <!-- Body của Card: p-0 để bảng tràn viền mượt mà 100% như Giỏ hàng! -->
              <div class="card-body p-0">
                <!-- Thanh Lọc: khung viền phân cách px-4 py-3 border-bottom -->
                <div class="px-4 py-3 bg-white border-bottom">
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

                <!-- BẢNG TRÀN VIỀN LIỀN MẠCH CHUẨN GIỎ HÀNG -->
                <div class="table-responsive">
                  <table class="table align-middle mb-0 custom-table table-jpm">
                    <thead class="bg-white">
                      <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th class="text-center text-uppercase border-0 py-3" style="width: 60px; font-size: 0.82rem; letter-spacing: 0.5px; color: #64748b; padding-left: 1.5rem;">STT</th>
                        <th class="text-uppercase border-0 py-3" style="font-size: 0.82rem; letter-spacing: 0.5px; color: #64748b;">MODEL ĐỘNG CƠ</th>
                        <th class="text-center text-uppercase border-0 py-3" style="width: 140px; font-size: 0.82rem; letter-spacing: 0.5px; color: #64748b;">MÃ LỰC</th>
                        <th class="text-center text-uppercase border-0 py-3" style="width: 120px; font-size: 0.82rem; letter-spacing: 0.5px; color: #64748b;">LOẠI THÌ</th>
                        <th class="text-center text-uppercase border-0 py-3" style="width: 120px; font-size: 0.82rem; letter-spacing: 0.5px; color: #64748b;">NĂM SX</th>
                        <th class="text-center text-uppercase border-0 py-3" style="width: 140px; font-size: 0.82rem; letter-spacing: 0.5px; color: #64748b; padding-right: 1.5rem;">THAO TÁC</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Trạng thái trống khi lọc không ra kết quả -->
                      <tr v-if="filteredModels.length === 0">
                        <td colspan="6" class="text-center py-5 text-muted border-0">
                          <i class="bi bi-search me-2 fs-5"></i>Không tìm thấy model phù hợp.
                        </td>
                      </tr>

                      <!-- Dòng dữ liệu Model: padding dọc py-3 thoáng mát, hover mượt mà -->
                      <tr v-for="(model, index) in filteredModels" :key="model.id" class="table-row-hover" style="transition: all 0.2s ease;">
                        <td class="text-center py-3 text-muted fw-medium" style="padding-left: 1.5rem;">{{ index + 1 }}</td>
                        <td class="py-3">
                          <div class="fw-bold" style="color: var(--heading-color, #0A1128); font-size: 0.95rem;">{{ model.name }}</div>
                          <div v-if="model.code && model.code !== model.name" class="text-muted small fw-medium mt-1">Mã: {{ model.code }}</div>
                        </td>
                        <td class="text-center py-3">
                          <span class="fw-semibold text-dark" style="font-size: 0.95rem;">{{ model.hp || '-' }}</span>
                          <template v-if="model.hp"><span class="text-muted ms-1" style="font-size: 0.85rem;">HP</span></template>
                        </td>
                        <td class="text-center py-3 text-secondary fw-medium" style="font-size: 0.9rem;">
                          {{ model.stroke ? formatStrokeText(model.stroke) : '-' }}
                        </td>
                        <td class="text-center py-3 text-secondary fw-medium" style="font-size: 0.9rem;">
                          {{ model.year || '-' }}
                        </td>
                        <td class="text-center py-3" style="padding-right: 1.5rem;">
                          <router-link
                            :to="`/thuong-hieu/${brandRoute}/${model.loai}/${model.year}/${model.code}/so-do`"
                            class="btn btn-outline-danger btn-sm rounded px-3 py-1 fw-bold text-nowrap d-inline-flex align-items-center justify-content-center shadow-none"
                            title="Xem Sơ Đồ"
                            style="transition: all 0.2s ease;"
                          >
                            Sơ Đồ <i class="bi bi-arrow-right ms-1"></i>
                          </router-link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- CHÂN BẢNG: THỐNG KÊ & CHỈ DẪN (px-4 py-3) -->
                <div class="px-4 py-3 bg-white border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                  <span class="text-muted small">
                    Hiển thị <b class="text-dark">{{ filteredModels.length }}</b> của <b class="text-dark">{{ modelsList.length }}</b> models
                  </span>
                  <span class="text-muted small fst-italic">
                    <i class="bi bi-info-circle me-1 text-danger"></i> Bấm "Sơ Đồ" để tra cứu từng cụm linh kiện chi tiết
                  </span>
                </div>

              </div> <!-- end card-body p-0 -->
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
   2. TIÊU ĐỀ CARD & SIDEBAR (ĐỒNG BỘ 100% GIỎ HÀNG)
   ======================================================== */
.jpm-card-title {
  font-family: var(--default-font);
  font-weight: 700;
  color: var(--heading-color, #0A1128);
  font-size: 1.15rem;
}

.catalog-sidebar-card,
.catalog-content-card {
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
   3. BỘ LỌC ĐỒNG BỘ TRANG CHỦ
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
   4. DATA TABLE CHUẨN GIỎ HÀNG (TRÀN VIỀN, HOVER MƯỢT)
   ======================================================== */
.table-row-hover:hover {
  background-color: #f8fafc;
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
