<template>
  <main class="brand-catalog-page main-page">
    <section class="section" style="padding-top: 110px;">
      <div class="container">
      <!-- PAGE TITLE & BREADCRUMB -->
      <div class="mb-3" data-aos="fade-down">
        <!-- Title -->
        <div class="page-title-box mb-2">
          <h2 class="jpm-page-title">
            Tra cứu Phụ Tùng <span class="text-danger">{{ brandName }}</span>
          </h2>
          <p class="text-muted mb-0">
            Lựa chọn loại động cơ, năm sản xuất và mã lực để tìm kiếm phụ tùng chính xác nhất.
          </p>
        </div>

        <!-- Modern Breadcrumb -->
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
            <li class="breadcrumb-item active fw-bold" style="color: var(--heading-color);" aria-current="page">
              {{ brandName }}
            </li>
          </ol>
        </nav>
      </div>

      <!-- MAIN CATALOG AREA -->
      <div class="jpm-split-layout" data-aos="fade-up" data-aos-delay="100">
        <!-- BÊN TRÁI: LOẠI ĐỘNG CƠ -->
        <div class="jpm-split-sidebar mb-4 mb-lg-0">
          <div class="card sticky-sidebar border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 px-4">
              <h4 class="mb-0 jpm-card-title">Loại Động Cơ</h4>
            </div>
            <div class="list-group list-group-flush category-list">
              <button
                v-for="loai in loaiDongCoList" :key="loai.value"
                class="list-group-item list-group-item-action text-start py-3 px-4 d-flex align-items-center"
                :class="{ active: selectedLoai === loai.value }"
                type="button"
                @click="selectLoai(loai.value)"
              >
                <span class="flex-grow-1">{{ loai.label }}</span>
                <i class="bi bi-chevron-right chevron-icon"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- BÊN PHẢI: DANH SÁCH MÃ LỰC -->
        <div class="jpm-split-content">
          <!-- NỘI DUNG THEO LOẠI ĐANG CHỌN -->
          <div>
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: visible;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-radius: 16px 16px 0 0;">
                  <h4 class="mb-0 jpm-card-title me-auto">Danh sách Model — {{ activeLoaiLabel }}</h4>
                  <span class="badge rounded-pill px-3 py-2 text-nowrap" style="background-color: #f1f3f7; color: #0e1b4d; font-weight: 700;">{{ filteredModels.length }} Models</span>
                </div>
                <div class="card-body p-4 pb-3">

                  <!-- FILTER BAR -->
                  <div class="mb-4">
                    <div class="d-flex flex-wrap gap-3">
                      <div class="flex-grow-1" style="min-width: 250px;">
                        <div class="input-group">
                          <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                          <input type="text" class="form-control border-start-0 shadow-none" placeholder="Tìm mã Model (VD: BF150)..." v-model="searchCode" />
                        </div>
                      </div>
                      <div style="width: 200px;">
                          <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'hp' }">
                            <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'hp' }" @click="toggleDropdown('hp')">
                              <span>{{ selectedHp ? selectedHp + ' HP' : 'Tất cả Mã Lực (HP)' }}</span>
                              <i class="bi bi-chevron-down select-arrow"></i>
                            </div>
                            <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'hp'">
                              <li @click="selectHp('')" :class="{ active: selectedHp === '' }">Tất cả Mã Lực (HP)</li>
                              <li v-for="hp in hpList" :key="hp" @click="selectHp(hp)" :class="{ active: selectedHp === String(hp) }">{{ hp }} HP</li>
                            </ul>
                          </div>
                        </div>
                        <div style="width: 200px;">
                          <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'stroke' }">
                            <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'stroke' }" @click="toggleDropdown('stroke')">
                              <span>{{ selectedStroke ? formatStrokeDropdown(selectedStroke) : 'Tất cả Loại Thì' }}</span>
                              <i class="bi bi-chevron-down select-arrow"></i>
                            </div>
                            <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'stroke'">
                              <li @click="selectStroke('')" :class="{ active: selectedStroke === '' }">Tất cả Loại Thì</li>
                              <li v-for="stroke in strokesList" :key="stroke" @click="selectStroke(stroke)" :class="{ active: selectedStroke === stroke }">{{ formatStrokeDropdown(stroke) }}</li>
                            </ul>
                          </div>
                        </div>
                        <div style="width: 200px;">
                          <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'nam' }">
                            <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'nam' }" @click="toggleDropdown('nam')">
                              <span>{{ selectedNam ? selectedNam : 'Tất cả Năm' }}</span>
                              <i class="bi bi-chevron-down select-arrow"></i>
                            </div>
                            <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'nam'">
                              <li @click="selectNam('')" :class="{ active: selectedNam === '' }">Tất cả Năm</li>
                              <li v-for="year in yearsList" :key="year" @click="selectNam(year)" :class="{ active: selectedNam === String(year) }">{{ year }}</li>
                            </ul>
                          </div>
                        </div>
                      <div class="d-flex align-items-center" v-if="searchCode || selectedNam || selectedHp || selectedStroke">
                        <button class="btn btn-link text-danger text-decoration-none p-0 btn-sm shadow-none" @click="clearFilters">
                          <i class="bi bi-x-circle me-1"></i>Xóa lọc
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- DATA TABLE -->
                  <div class="table-responsive hide-x-scroll custom-scrollbar" style="max-height: 550px; overflow-y: auto;">
                    <table class="table custom-table mb-0 align-middle table-hover table-jpm">
                      <thead style="background-color: #f8f9fa; position: sticky; top: 0; z-index: 1;">
                          <tr>
                            <th class="py-2 text-uppercase border-bottom-0 text-center" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; position: sticky; top: 0; background-color: #f8f9fa; width: 5%;">STT</th>
                            <th class="py-2 text-uppercase border-bottom-0" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; position: sticky; top: 0; background-color: #f8f9fa;">Model Động Cơ</th>
                            <th class="py-2 text-uppercase border-bottom-0 text-center" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; position: sticky; top: 0; background-color: #f8f9fa; width: 15%;">Mã Lực</th>
                            <th class="py-2 text-uppercase border-bottom-0 text-center" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; position: sticky; top: 0; background-color: #f8f9fa; width: 10%;">Loại Thì</th>
                            <th class="py-2 text-uppercase border-bottom-0 text-center" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; position: sticky; top: 0; background-color: #f8f9fa; width: 15%;">Năm SX</th>
                            <th class="py-2 text-uppercase border-bottom-0 text-center" style="color: #a1acb8; font-size: 0.75rem; letter-spacing: 0.5px; position: sticky; top: 0; background-color: #f8f9fa; width: 15%;">Thao tác</th>
                          </tr>
                        </thead>
                      <tbody>
                        <tr v-if="filteredModels.length === 0">
                          <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-search me-2"></i>Không tìm thấy model phù hợp.
                          </td>
                        </tr>
                        <tr v-for="(model, index) in filteredModels" :key="model.id" class="table-row-hover" style="transition: all 0.2s ease;">
                            <td class="py-2 text-center text-muted fw-medium">{{ index + 1 }}</td>
                            <td class="py-2">
                              <div class="fw-bold model-code" style="color: var(--heading-color);">{{ model.name }}</div>
                            </td>
                            <td class="py-2 text-center fw-medium text-dark">
                              <span class="fw-semibold text-dark">{{ model.hp || '-' }}</span><template v-if="model.hp"><span class="text-muted ms-1" style="font-size: 0.85rem;">HP</span></template>
                            </td>
                            <td class="py-2 text-center text-secondary" style="font-size: 0.9rem;">{{ model.stroke ? formatStroke(model.stroke) : '-' }}</td>
                            <td class="py-2 text-center text-secondary">{{ model.year || '-' }}</td>
                            <td class="py-2 text-center">
                              <router-link :to="`/thuong-hieu/${brandRoute}/${model.loai}/${model.year}/${model.code}/so-do`" class="btn btn-outline-danger btn-sm rounded px-3 py-1 fw-bold text-nowrap d-inline-flex align-items-center justify-content-center shadow-none" title="Xem Sơ Đồ" style="transition: all 0.3s ease;">
                                Sơ Đồ <i class="bi bi-arrow-right ms-1"></i>
                              </router-link>
                            </td>
                          </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- TABLE FOOTER INFO -->
                  <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <span class="text-muted small">Hiển thị {{ filteredModels.length }} của {{ modelsList.length }} models</span>
                  </div>
                </div> <!-- end card-body -->
              </div> <!-- end card -->

              <!-- CTA BANNER HỖ TRỢ KỸ THUẬT -->
              <div class="mt-3 card border-0 shadow-sm p-4 p-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between jpm-hover-card gap-4" style="border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;" data-aos="fade-up" data-aos-delay="200">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center me-4 flex-shrink-0" style="width: 70px; height: 70px; background-color: rgba(248, 34, 73, 0.1);">
                    <i class="bi bi-headset fs-2" style="color: #f82249;"></i>
                  </div>
                  <div class="pe-md-4">
                    <h4 class="fw-bold mb-1" style="color: #0e1b4d;">Bạn không tìm thấy Model của mình?</h4>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">Đừng lo lắng! Đội ngũ kỹ thuật viên của JPM Marine luôn sẵn sàng hỗ trợ bạn tra cứu sơ đồ và phụ tùng chính xác nhất.</p>
                  </div>
                </div>
                <div class="flex-shrink-0">
                  <a href="tel:0900000000" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-uppercase text-nowrap" style="letter-spacing: 0.5px; background-color: #f82249; border-color: #f82249;">
                    <i class="bi bi-telephone-fill me-2"></i> Liên Hệ Kỹ Thuật Viên
                  </a>
                </div>
              </div>

            </div> <!-- end wrapper div -->
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

const formatStrokeDropdown = (stroke) => {
  const num = formatStroke(stroke);
  return num ? num + ' Thì' : '';
};
const formatStroke = (stroke) => {
  if (!stroke) return '';
  return String(stroke).replace(/[^0-9]/g, '');
};
const brandRoute = computed(() => route.params.brand || 'yamaha');

// Gọi API lấy Chi tiết hãng, loại động cơ, models và mốc năm
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
const closeDropdowns = () => {
  openDropdown.value = null;
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

// Đóng dropdown khi click ra ngoài (không dùng overlay chặn click)
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
    const matchCode = searchCode.value   ? (m.code?.toLowerCase().includes(searchCode.value.toLowerCase()) || m.name?.toLowerCase().includes(searchCode.value.toLowerCase())) : true;
    return matchLoai && matchNam && matchHp && matchStroke && matchCode;
  });
});
</script>

<style scoped>
.brand-catalog-page {
  min-height: 100vh;
  padding-bottom: 50px;
}



/* Breadcrumb */
.custom-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  content: "›";
  font-size: 1.2rem;
  line-height: 1;
  vertical-align: middle;
  color: #ccc;
}
.custom-breadcrumb .breadcrumb-item a:hover {
  color: var(--accent-color) !important;
  transition: color 0.2s;
}

/* Sticky Sidebar */
@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 100px;
  }
}

/* Badges & Text */
.btn-ghost-action i {
  transition: transform 0.2s;
}
.btn-ghost-action:hover i {
  transform: translateX(4px);
}

/* Rounded Pagination */
.custom-pagination-rounded .page-link {
  border: none;
  margin: 0 2px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0e1b4d;
  font-weight: 600;
  border-radius: 50% !important;
  transition: all 0.2s ease;
}
.custom-pagination-rounded .page-item.active .page-link {
  background-color: #f82249;
  color: #fff;
  box-shadow: 0 4px 8px rgba(248, 34, 73, 0.2);
}
.custom-pagination-rounded .page-link:hover:not(.active) {
  background-color: #f1f3f7;
  color: #f82249;
}
</style>
