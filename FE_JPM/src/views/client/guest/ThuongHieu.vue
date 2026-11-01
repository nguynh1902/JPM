<template>
  <main class="brand-catalog-page main-page">
    <section class="section py-4 py-lg-5" style="padding-top: 110px;">
      <div class="container">

        <!-- ========================================================
             KHỐI 1: HERO BANNER - TRA CỨU PHỤ TÙNG THEO THƯƠNG HIỆU
             ======================================================== -->
        <div class="brand-hero-section" data-aos="fade-down">
          <div class="row align-items-center">
            <!-- Cột Trái: Tiêu đề, Tag & Breadcrumb -->
            <div class="col-lg-8 col-xl-7">
              <!-- Tag danh mục kỹ thuật -->
              <div class="mb-2">
                <span class="brand-hero-tag">MARINE PARTS CATALOG</span>
              </div>

              <!-- Tiêu đề thương hiệu chính -->
              <h1 class="brand-hero-title">
                Tra cứu Phụ Tùng <span class="text-danger">{{ brandName }}</span>
              </h1>

              <!-- Mô tả hướng dẫn người dùng -->
              <p class="brand-hero-subtitle">
                Lựa chọn loại động cơ, năm sản xuất và mã lực để tìm kiếm phụ tùng chính xác nhất.
              </p>

              <!-- Breadcrumb Điều Hướng Chuẩn TheEvent -->
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb brand-breadcrumb mb-0 align-items-center">
                  <li class="breadcrumb-item">
                    <router-link to="/" class="d-flex align-items-center">
                      <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                    </router-link>
                  </li>
                  <li class="breadcrumb-item">
                    <span>Danh mục</span>
                  </li>
                  <li class="breadcrumb-item active" aria-current="page">
                    {{ brandName }}
                  </li>
                </ol>
              </nav>
            </div>

            <!-- Cột Phải: Ảnh Động Cơ Thủy Bắn Nước Đại Diện Hãng -->
            <div class="col-lg-4 col-xl-5 text-center text-lg-end mt-4 mt-lg-0">
              <img
                :src="brandEngineImage"
                :alt="brandName"
                class="brand-hero-engine-img img-fluid"
                @error="onEngineImgError"
              />
            </div>
          </div>
        </div>

        <!-- ========================================================
             KHỐI 2: MAIN CONTENT (Loại Động Cơ & Bảng Danh Sách Model)
             ======================================================== -->
        <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
          
          <!-- CỘT TRÁI (3/12): DANH MỤC LOẠI ĐỘNG CƠ -->
          <div class="col-lg-3">
            <div class="card brand-sidebar-card sticky-sidebar">
              <!-- Header sidebar -->
              <div class="brand-sidebar-header">
                <i class="bi bi-fan brand-sidebar-header-icon"></i>
                <h5>Loại Động Cơ</h5>
              </div>

              <!-- Body sidebar: Danh sách loại động cơ -->
              <div class="brand-sidebar-body">
                <div v-if="isLoading" class="p-3 text-center text-muted">
                  <span class="spinner-border spinner-border-sm text-danger me-2" role="status"></span> Đang tải...
                </div>
                <div v-else-if="loaiDongCoList.length === 0" class="p-3 text-center text-muted">
                  Chưa có loại động cơ nào.
                </div>

                <div v-else class="brand-type-list position-relative z-2">
                  <button
                    v-for="loai in loaiDongCoList"
                    :key="loai.value"
                    class="brand-type-item"
                    :class="{ active: selectedLoai === loai.value }"
                    type="button"
                    @click="selectLoai(loai.value)"
                  >
                    <span>{{ loai.label }}</span>
                    <i class="bi bi-chevron-right chevron-icon"></i>
                  </button>
                </div>

                <!-- Watermark chìm trang trí góc card -->
                <div class="brand-sidebar-watermark">
                  <i class="bi bi-water"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- CỘT PHẢI (9/12): BẢNG DANH SÁCH VÀ BỘ LỌC MODEL -->
          <div class="col-lg-9">
            
            <!-- CARD CHÍNH: DANH SÁCH VÀ BỘ LỌC -->
            <div class="card brand-catalog-card">
              <!-- Header card: Tiêu đề và tổng số lượng models -->
              <div class="brand-catalog-header">
                <h4 class="brand-catalog-title">
                  Danh sách Model — {{ activeLoaiLabel || 'Tất cả' }}
                </h4>
                <span class="brand-badge-count">
                  {{ filteredModels.length }} Models
                </span>
              </div>

              <!-- THANH BỘ LỌC TÌM KIẾM (4 Ô TRÊN 1 HÀNG) -->
              <div class="brand-filter-bar">
                <div class="row g-2 g-xl-3 align-items-center">
                  <!-- Ô 1: Tìm kiếm theo mã model hoặc tên -->
                  <div class="col-12 col-md-5 col-xl-4">
                    <div class="input-group brand-search-box">
                      <span class="input-group-text"><i class="bi bi-search"></i></span>
                      <input
                        type="text"
                        class="form-control"
                        placeholder="Tìm mã Model (VD: BF150)..."
                        v-model="searchCode"
                      />
                    </div>
                  </div>

                  <!-- Ô 2: Dropdown Lọc Mã Lực (HP) -->
                  <div class="col-6 col-md-3 col-xl">
                    <div class="brand-select-wrapper" :class="{ 'z-active': openDropdown === 'hp' }">
                      <div
                        class="brand-select-box"
                        :class="{ 'is-focused': openDropdown === 'hp', 'has-value': selectedHp !== '' }"
                        @click="toggleDropdown('hp')"
                      >
                        <span class="text-truncate">{{ selectedHp ? selectedHp + ' HP' : 'Tất cả Mã Lực (HP)' }}</span>
                        <i class="bi bi-chevron-down select-arrow" :class="{ rotated: openDropdown === 'hp' }"></i>
                      </div>
                      <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'hp'">
                        <li @click="selectHp('')" :class="{ active: selectedHp === '' }">Tất cả Mã Lực (HP)</li>
                        <li v-for="hp in hpList" :key="hp" @click="selectHp(hp)" :class="{ active: selectedHp === String(hp) }">{{ hp }} HP</li>
                      </ul>
                    </div>
                  </div>

                  <!-- Ô 3: Dropdown Lọc Loại Thì (Stroke) -->
                  <div class="col-6 col-md-2 col-xl">
                    <div class="brand-select-wrapper" :class="{ 'z-active': openDropdown === 'stroke' }">
                      <div
                        class="brand-select-box"
                        :class="{ 'is-focused': openDropdown === 'stroke', 'has-value': selectedStroke !== '' }"
                        @click="toggleDropdown('stroke')"
                      >
                        <span class="text-truncate">{{ selectedStroke ? formatStrokeDropdown(selectedStroke) : 'Tất cả Loại Thì' }}</span>
                        <i class="bi bi-chevron-down select-arrow" :class="{ rotated: openDropdown === 'stroke' }"></i>
                      </div>
                      <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'stroke'">
                        <li @click="selectStroke('')" :class="{ active: selectedStroke === '' }">Tất cả Loại Thì</li>
                        <li v-for="stroke in strokesList" :key="stroke" @click="selectStroke(stroke)" :class="{ active: selectedStroke === stroke }">{{ formatStrokeDropdown(stroke) }}</li>
                      </ul>
                    </div>
                  </div>

                  <!-- Ô 4: Dropdown Lọc Năm Sản Xuất -->
                  <div class="col-12 col-md-2 col-xl">
                    <div class="brand-select-wrapper" :class="{ 'z-active': openDropdown === 'nam' }">
                      <div
                        class="brand-select-box"
                        :class="{ 'is-focused': openDropdown === 'nam', 'has-value': selectedNam !== '' }"
                        @click="toggleDropdown('nam')"
                      >
                        <span class="text-truncate">{{ selectedNam ? selectedNam : 'Tất cả Năm' }}</span>
                        <i class="bi bi-chevron-down select-arrow" :class="{ rotated: openDropdown === 'nam' }"></i>
                      </div>
                      <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'nam'">
                        <li @click="selectNam('')" :class="{ active: selectedNam === '' }">Tất cả Năm</li>
                        <li v-for="year in yearsList" :key="year" @click="selectNam(year)" :class="{ active: selectedNam === String(year) }">{{ year }}</li>
                      </ul>
                    </div>
                  </div>

                  <!-- Nút Xóa lọc nếu có điều kiện được chọn -->
                  <div class="col-auto" v-if="searchCode || selectedNam || selectedHp || selectedStroke">
                    <button class="btn btn-link text-danger text-decoration-none p-0 btn-sm shadow-none" @click="clearFilters">
                      <i class="bi bi-x-circle me-1"></i>Xóa lọc
                    </button>
                  </div>
                </div>
              </div>

              <!-- BẢNG DỮ LIỆU DANH SÁCH MODEL -->
              <div class="table-responsive">
                <table class="table custom-model-table align-middle">
                  <thead>
                    <tr>
                      <th class="text-center" style="width: 60px;">STT</th>
                      <th style="min-width: 220px;">MODEL ĐỘNG CƠ</th>
                      <th class="text-center" style="width: 120px;">MÃ LỰC</th>
                      <th class="text-center" style="width: 100px;">LOẠI THÌ</th>
                      <th class="text-center" style="width: 100px;">NĂM SX</th>
                      <th class="text-center" style="width: 130px;">THAO TÁC</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Trạng thái trống khi không tìm thấy model -->
                    <tr v-if="filteredModels.length === 0">
                      <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-search fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        Không tìm thấy model động cơ nào phù hợp với điều kiện lọc.
                      </td>
                    </tr>

                    <!-- Danh sách các model động cơ -->
                    <tr v-for="(model, index) in filteredModels" :key="model.id">
                      <td class="text-center text-muted fw-medium">{{ index + 1 }}</td>
                      <td>
                        <span class="model-name-text">{{ model.name }}</span>
                      </td>
                      <td class="text-center">
                        <span class="fw-bold fs-6 text-dark">{{ model.hp || '-' }}</span>
                        <span v-if="model.hp" class="text-muted small ms-1">HP</span>
                      </td>
                      <td class="text-center text-secondary">
                        {{ model.stroke ? formatStroke(model.stroke) : '-' }}
                      </td>
                      <td class="text-center text-secondary">
                        {{ model.year || '-' }}
                      </td>
                      <td class="text-center">
                        <router-link
                          :to="`/thuong-hieu/${brandRoute}/${model.loai}/${model.year}/${model.code}/so-do`"
                          class="btn-model-action"
                          title="Xem Sơ Đồ Rã Máy"
                        >
                          Sơ Đồ <i class="bi bi-arrow-right"></i>
                        </router-link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- CHÂN BẢNG: HIỂN THỊ TỔNG SỐ -->
              <div class="p-3 px-4 text-muted small border-top">
                Hiển thị {{ filteredModels.length }} của {{ modelsList.length }} models
              </div>
            </div>

            <!-- ========================================================
                 KHỐI 3: BANNER HỖ TRỢ KỸ THUẬT (CARD LIÊN HỆ ĐÁY TRANG)
                 ======================================================== -->
            <div class="card brand-help-card mt-4" data-aos="fade-up" data-aos-delay="150">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="brand-help-icon-box">
                    <i class="bi bi-headset"></i>
                  </div>
                  <div>
                    <h5 class="fw-bold text-dark mb-1">Bạn không tìm thấy Model của mình?</h5>
                    <p class="text-muted mb-0 small">
                      Đừng lo lắng! Đội ngũ kỹ thuật viên của JPM Marine luôn sẵn sàng hỗ trợ bạn tra cứu sơ đồ và phụ tùng chính xác nhất.
                    </p>
                  </div>
                </div>
                <div>
                  <a href="tel:0900000000" class="btn btn-dark-cta">
                    <i class="bi bi-telephone-fill me-1"></i> LIÊN HỆ KỸ THUẬT VIÊN →
                  </a>
                </div>
              </div>
            </div>

          </div> <!-- END CỘT PHẢI (9/12) -->
        </div> <!-- END ROW MAIN -->

      </div> <!-- END CONTAINER -->
    </section>
  </main>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';

// ===== 1. KHỞI TẠO ROUTE & CÁC HÀM FORMAT THÔNG SỐ =====
const route = useRoute();

const formatStrokeDropdown = (stroke) => {
  const num = formatStroke(stroke);
  return num ? num + ' Thì' : '';
};

const formatStroke = (stroke) => {
  if (!stroke) return '';
  return String(stroke).replace(/[^0-9]/g, '');
};

const brandRoute = computed(() => route.params.brand || 'yamaha');

// ===== 2. TẢI DỮ LIỆU CATALOG THƯƠNG HIỆU TỪ API (SWR CACHING) =====
const { data: catalogData, isLoading } = useQuery({
  queryKey: ['brand-catalog', brandRoute],
  queryFn: () => axiosClient.get(`/public/thuong-hieu/${brandRoute.value}`).then(res => res.data || res),
  staleTime: 1000 * 60 * 10
});

const brandName = computed(() => catalogData.value?.brand?.name || brandRoute.value.toUpperCase());
const loaiDongCoList = computed(() => catalogData.value?.loaiDongCoList || []);
const yearsList = computed(() => catalogData.value?.years || []);
const modelsList = computed(() => catalogData.value?.models || []);
const strokesList = computed(() => catalogData.value?.strokes || []);

// Ánh xạ tự động ảnh động cơ thủy đại diện theo từng thương hiệu
const brandEngineImage = computed(() => {
  const code = (catalogData.value?.brand?.code || brandRoute.value || '').toLowerCase();
  if (code.includes('mercury') || code.includes('mecury')) return '/client/image/mecury_thuonghieu.png';
  if (code.includes('yamaha') || code.includes('yam')) return '/client/image/yamaha_thuonghieu.png';
  if (code.includes('honda') || code.includes('hon')) return '/client/image/honda_marine_thuonghieu.png';
  if (code.includes('suzuki') || code.includes('suz')) return '/client/image/suzuki_marine_thuonghieu.png';
  if (code.includes('tohatsu') || code.includes('toh')) return '/client/image/tohatsu_thuonghieu.png';
  if (code.includes('yanmar') || code.includes('yan')) return '/client/image/yanmar_thuonghieu.png';
  if (code.includes('seadoo') || code.includes('sea')) return '/client/image/seadoo_thuonghieu.png';
  if (code.includes('jetki') || code.includes('jet')) return '/client/image/jetki_thuonghieu.png';
  return '/client/image/mecury_thuonghieu.png';
});

const onEngineImgError = (e) => {
  e.target.src = '/client/image/mecury_thuonghieu.png';
};

// ===== 3. QUẢN LÝ STATE BỘ LỌC DỮ LIỆU MODEL =====
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

// ===== 4. QUẢN LÝ DROPDOWN TÙY BIẾN =====
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

// Đóng dropdown khi click ra ngoài mà không cần overlay chặn click
const handleClickOutside = (event) => {
  if (!openDropdown.value) return;
  if (!event.target.closest('.brand-select-wrapper')) {
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

// Lọc model theo: Loại đang chọn + Năm + HP + Mã + Thì
const filteredModels = computed(() => {
  if (!modelsList.value) return [];
  
  return modelsList.value.filter(m => {
    const matchLoai = m.loai === selectedLoai.value;
    const matchNam  = selectedNam.value  ? m.year === Number(selectedNam.value)  : true;
    const matchHp   = selectedHp.value   ? m.hp   === Number(selectedHp.value)   : true;
    const matchStroke = selectedStroke.value ? m.stroke === selectedStroke.value : true;
    const matchCode = searchCode.value   ? (m.code?.toLowerCase().includes(searchCode.value.toLowerCase()) || m.name?.toLowerCase().includes(searchCode.value.toLowerCase())) : true;
    return matchLoai && matchNam && matchHp && matchStroke && matchCode;
  });
});
</script>

<style scoped>
/* ==============================================================
   ThuongHieu: Tinh chỉnh cục bộ cho trang thương hiệu
   (Toàn bộ class Design System đã được nạp từ main.css)
   ============================================================== */
.brand-catalog-page {
  min-height: 100vh;
  padding-bottom: 60px;
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 100px;
  }
}
</style>
