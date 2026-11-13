<template>
  <div class="tra-cuu-results-page main-page" style="position: relative; z-index: 10; min-height: 130vh;">
    <section class="section pb-5 inner-page-section">
      <div class="container">

        <!-- PAGE TITLE -->
        <div class="row mb-4" data-aos="fade-down">
          <div class="col-12">
            <template v-if="isFilterEmpty">
              <h2 class="jpm-page-title">
                Tra Cứu <span class="text-danger">Phụ Tùng</span>
              </h2>
              <p class="text-muted">
                Lựa chọn chính xác phụ tùng thay thế và sơ đồ rã máy cho động cơ thủy của bạn.
              </p>
            </template>
            <template v-else-if="filters.dong_co_model_id && !filters.nhom_phu_tung_id && !filters.keyword">
              <h2 class="jpm-page-title">
                Sơ Đồ Phụ Tùng <span class="text-danger">Model {{ modelLabel }}</span>
              </h2>
              <p class="text-muted">
                Hiển thị danh sách các nhóm sơ đồ phụ tùng chi tiết cho model này.
              </p>
            </template>
            <template v-else>
              <h2 class="jpm-page-title">
                Kết Quả Tra Cứu <span class="text-danger">Phụ Tùng</span>
              </h2>
              <p class="text-muted">
                Tìm thấy <strong class="text-danger">{{ pagination.total }}</strong> kết quả phù hợp cho tìm kiếm của bạn.
              </p>
            </template>
          </div>
        </div>

        <!-- MAIN SPLIT LAYOUT -->
        <div class="jpm-split-layout mt-2" data-aos="fade-up" data-aos-delay="100">

          <!-- BÊN TRÁI: BỘ LỌC SIDEBAR -->
          <div class="jpm-split-sidebar mb-4 mb-lg-0" style="position: relative; z-index: 1040;">
            <div class="card sticky-sidebar catalog-sidebar-card shadow-sm">
              
              <!-- 1. HEADER BỘ LỌC CHUẨN LUXURY MINIMALISM -->
              <div class="catalog-sidebar-header">
                <h3 class="catalog-sidebar-title mb-0">Bộ Lọc</h3>
                <button 
                  type="button"
                  class="btn-reset-sidebar-filter" 
                  :class="{ 'is-hidden': isFilterEmpty }"
                  @click="resetFilters"
                >
                  Xóa tất cả
                </button>
              </div>

              <div class="catalog-sidebar-body custom-scrollbar">

                <!-- 2. TÌM KIẾM THEO SKU -->
                <div class="sidebar-section mb-4">
                  <div class="sidebar-section-title mb-2">Tìm Theo Mã SKU</div>
                  <div class="search-input-box">
                    <i class="bi bi-search search-input-icon"></i>
                    <input 
                      type="text" 
                      class="search-input-field" 
                      placeholder="VD: 31105-PR7-A01..." 
                      v-model="localKeyword"
                    >
                    <button 
                      v-if="localKeyword" 
                      type="button" 
                      class="btn-clear-search" 
                      @click="localKeyword = ''"
                      title="Xóa"
                    >
                      <i class="bi bi-x-circle-fill"></i>
                    </button>
                  </div>
                </div>

                <!-- 3. TƯƠNG THÍCH ĐỘNG CƠ -->
                <div class="sidebar-section mb-4">
                  <div class="sidebar-section-title mb-2">Tương Thích Động Cơ</div>

                  <!-- Hãng sản xuất -->
                  <div class="custom-select-wrapper mb-2" :class="{ 'z-active': openDropdown === 'brand' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'brand', 'has-value': filters.thuong_hieu_id !== '' }" @click="toggleDropdown('brand')">
                      <span class="text-truncate">{{ brandLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'brand' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left custom-scrollbar" v-show="openDropdown === 'brand'">
                      <li @click="selectOption('brand', '')" :class="{ active: filters.thuong_hieu_id === '' }">Tất cả thương hiệu</li>
                      <li v-for="b in brands" :key="b.id" @click="selectOption('brand', b.id)" :class="{ active: filters.thuong_hieu_id === b.id }">{{ b.name }}</li>
                    </ul>
                  </div>

                  <!-- Loại động cơ -->
                  <div class="custom-select-wrapper mb-2" :class="{ 'z-active': openDropdown === 'engineType' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'engineType', 'has-value': filters.loai_dong_co_id !== '' }" @click="toggleDropdown('engineType')">
                      <span class="text-truncate">{{ engineTypeLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'engineType' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left custom-scrollbar" v-show="openDropdown === 'engineType'">
                      <li @click="selectOption('engineType', '')" :class="{ active: filters.loai_dong_co_id === '' }">Tất cả loại động cơ</li>
                      <li v-for="e in engineTypes" :key="e.id" @click="selectOption('engineType', e.id)" :class="{ active: filters.loai_dong_co_id === e.id }">{{ e.ten_loai_dong_co }}</li>
                    </ul>
                  </div>

                  <!-- Năm sản xuất -->
                  <div class="custom-select-wrapper mb-2" :class="{ 'z-active': openDropdown === 'year' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'year', 'has-value': filters.nam_sx !== '' }" @click="toggleDropdown('year')">
                      <span class="text-truncate">{{ yearLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'year' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left custom-scrollbar" v-show="openDropdown === 'year'">
                      <li @click="selectOption('year', '')" :class="{ active: filters.nam_sx === '' }">Tất cả các năm</li>
                      <li v-for="y in years" :key="y" @click="selectOption('year', y)" :class="{ active: filters.nam_sx === y }">{{ y }}</li>
                    </ul>
                  </div>

                  <!-- Model -->
                  <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'model' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'model', 'has-value': filters.dong_co_model_id !== '' }" @click="toggleDropdown('model')">
                      <span class="text-truncate">{{ modelLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'model' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left custom-scrollbar" v-show="openDropdown === 'model'">
                      <li @click="selectOption('model', '')" :class="{ active: filters.dong_co_model_id === '' }">Tất cả model</li>
                      <li v-for="m in engineModels" :key="m.id" @click="selectOption('model', m.id)" :class="{ active: filters.dong_co_model_id === m.id }">{{ m.ten_model }}</li>
                    </ul>
                  </div>
                </div>

                <!-- 4. NHÓM PHỤ TÙNG (ĐỒNG BỘ 100% CỬA HÀNG SANPHAM.VUE, KHÔNG CUỘN) -->
                <div class="sidebar-category-section">
                  <div class="sidebar-section-title mb-2">Nhóm Phụ Tùng</div>

                  <!-- Khi chưa chọn thông số động cơ bên trên -->
                  <div v-if="!hasEngineSelected" class="text-muted mt-1 small fst-italic">
                    Vui lòng chọn thông số động cơ ở trên để tải danh sách nhóm phụ tùng.
                  </div>

                  <!-- Đang tải danh sách nhóm -->
                  <div v-else-if="isFetchingPartGroups" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-danger"></div>
                  </div>

                  <!-- Danh sách nhóm phụ tùng (Không giới hạn chiều cao, không cuộn) -->
                  <div v-else-if="partGroups && partGroups.length > 0" class="catalog-nav-list">
                    <!-- Nút Tất Cả Nhóm Phụ Tùng -->
                    <button
                      type="button"
                      class="catalog-nav-btn"
                      :class="{ active: !filters.nhom_phu_tung_id }"
                      @click="toggleCategory('')"
                    >
                      <span class="catalog-nav-text">Tất cả nhóm phụ tùng</span>
                      <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                    </button>

                    <!-- Từng nhóm phụ tùng lấy từ API -->
                    <button 
                      v-for="cat in partGroups" 
                      :key="cat.id"
                      type="button"
                      class="catalog-nav-btn"
                      :class="{ active: filters.nhom_phu_tung_id === cat.id }"
                      @click="toggleCategory(cat.id)"
                    >
                      <span class="catalog-nav-text" :title="cat.ten_nhom">{{ cat.ten_nhom }}</span>
                      <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                    </button>
                  </div>

                  <!-- Đã chọn thông số nhưng không tìm thấy nhóm nào -->
                  <div v-else class="text-muted mt-1 small fst-italic">
                    Không có nhóm phụ tùng nào cho thông số này.
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- BÊN PHẢI: KẾT QUẢ TRA CỨU -->
          <div class="jpm-split-content pl-lg-4">

            <!-- 1. Trạng thái rỗng khi chưa chọn lọc: Empty Guide State -->
            <div class="text-center py-5" v-if="isFilterEmpty">
              <div class="empty-state-card py-5 px-3">
                <div class="empty-icon-circle mx-auto mb-4">
                  <i class="bi bi-search text-danger" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold mb-2 text-navy" style="color: #0A1128;">Bắt Đầu Tra Cứu Phụ Tùng</h4>
                <p class="text-muted mx-auto mb-0" style="max-width: 520px; font-size: 0.95rem; line-height: 1.6;">
                  Hãy lựa chọn các thông số động cơ ở bộ lọc bên trái (Thương hiệu, Loại máy, Năm, Model) hoặc trực tiếp nhập mã SKU phụ tùng để xem sơ đồ bản vẽ và linh kiện chính hãng.
                </p>
              </div>
            </div>

            <!-- 2. Khi đã chọn bộ lọc: Hiển thị Thanh điều khiển & Danh sách -->
            <div v-else>

              <!-- THANH ĐIỀU KHIỂN CHUẨN SANPHAM.VUE (KHÔNG DÙNG CARD TRẮNG NỔI RỜI RẠC) -->
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-3 border-bottom gap-2">
                <!-- TABS CHUYỂN ĐỔI CHUẨN 2026 -->
                <div class="d-flex align-items-center gap-2" v-if="products.length > 0 || diagramsList.length > 0 || isFetchingGlobal">
                  <button 
                    type="button"
                    class="tab-pill-btn"
                    :class="{ active: activeTab === 'products' }"
                    @click="activeTab = 'products'"
                  >
                    <span>Phụ tùng chi tiết</span>
                    <span class="tab-pill-badge">{{ searchResult?.total || products.length }}</span>
                  </button>
                  <button 
                    type="button"
                    class="tab-pill-btn"
                    :class="{ active: activeTab === 'diagrams' }"
                    @click="activeTab = 'diagrams'"
                  >
                    <span>Sơ đồ bản vẽ máy</span>
                    <span class="tab-pill-badge">{{ searchDiagrams?.total || diagramsList.length }}</span>
                  </button>
                </div>

                <!-- DROPDOWN SẮP XẾP CHUẨN LUXURY -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                  <span class="text-muted small fw-medium text-nowrap d-none d-sm-inline">Sắp xếp:</span>
                  <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'sort' }" style="width: 165px;">
                    <div class="custom-select-box shop-sort-box" :class="{ 'is-focused': openDropdown === 'sort' }" @click="toggleDropdown('sort')">
                      <span class="text-truncate">{{ sortLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'sort' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'sort'">
                      <li @click="selectSort('newest')" :class="{ active: sortBy === 'newest' }">Mới nhất</li>
                      <li @click="selectSort('price_asc')" :class="{ active: sortBy === 'price_asc' }">Giá thấp đến cao</li>
                      <li @click="selectSort('price_desc')" :class="{ active: sortBy === 'price_desc' }">Giá cao đến thấp</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- DÒNG ACTIVE FILTERS TINH GỌN (KHÔNG BỌC CARD NỔI) -->
              <div class="d-flex flex-wrap align-items-center gap-2 mb-4" v-if="activeFilters.length > 0">
                <span class="text-muted small fw-medium">Đang lọc theo:</span>
                <span class="filter-pill-tag" v-for="(filter, index) in activeFilters" :key="index">
                  <span>{{ filter.label }}</span>
                  <button type="button" class="btn-remove-tag" @click="removeFilter(filter.type)">
                    <i class="bi bi-x"></i>
                  </button>
                </span>
                <button type="button" class="btn-clear-all ms-1" @click="resetFilters">
                  Xóa tất cả
                </button>
              </div>

              <!-- LOADING SKELETON -->
              <div v-if="isFetchingGlobal" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <div class="col" v-for="i in 6" :key="i">
                  <ProductCard :loading="true" />
                </div>
              </div>

              <!-- TAB 1: SƠ ĐỒ BẢN VẼ -->
              <div v-else-if="activeTab === 'diagrams'">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" v-if="diagramsList.length > 0">
                  <div class="col" v-for="diag in diagramsList" :key="diag.id">
                    <div class="card h-100 premium-product-card group-hover" @click="goToDiagram(diag)" @mouseenter="prefetchDiagram(diag)" style="cursor: pointer;">
                      <div class="card-img-wrapper overflow-hidden img-zoom-hover">
                        <img 
                          :src="diag.hinh_anh ? diag.hinh_anh : 'https://placehold.co/400x400/f8f9fa/a1acb8?text=No+Image'" 
                          class="card-img-top w-100 transition-transform" 
                          style="aspect-ratio: 4/3; object-fit: cover;" 
                          :alt="diag.ten_so_do"
                        >
                      </div>
                      <div class="card-body text-start p-3 d-flex flex-column bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">SƠ ĐỒ PHỤ TÙNG</span>
                        </div>
                        <h5 class="card-title mb-3 text-uppercase" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                          {{ diag.nhom_phu_tung ? diag.nhom_phu_tung.ten_nhom : diag.ten_so_do }}
                        </h5>
                        <p class="text-muted mb-2" style="font-size: 0.8rem;" v-if="diag.model">MODEL: {{ diag.model.ten_model }}</p>
                        <div class="mt-auto">
                          <hr class="my-2 opacity-25">
                          <div class="d-flex justify-content-end align-items-center">
                            <span class="text-danger fw-bold" style="font-size: 0.9rem;">Xem sơ đồ <i class="bi bi-arrow-right"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Empty Sơ đồ -->
                <div class="text-center py-5" v-else>
                  <div class="empty-icon-circle mx-auto mb-3">
                    <i class="bi bi-diagram-3 text-muted" style="font-size: 2rem;"></i>
                  </div>
                  <h5 class="fw-bold mb-2 text-navy" style="color: #0A1128;">Chưa có sơ đồ phụ tùng cho tìm kiếm này</h5>
                  <p class="text-muted mb-0">Bạn có thể bấm sang tab "Phụ tùng chi tiết" để xem các linh kiện tương thích.</p>
                </div>
              </div>

              <!-- TAB 2: PHỤ TÙNG CHI TIẾT (ĐỒNG BỘ 100% TRANG CHỦ & CỬA HÀNG) -->
              <div v-else-if="activeTab === 'products'">
                <div v-if="groupedProducts.length > 0">
                  <div v-for="group in groupedProducts" :key="group.id" class="mb-5">
                    <!-- HEADER NHÓM PHỤ TÙNG CHUẨN CỬA HÀNG -->
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                      <h4 class="fw-bold mb-0 text-capitalize" style="color: var(--heading-color, #0A1128); font-size: 1.25rem; font-family: var(--heading-font, 'Raleway', sans-serif);">
                        {{ group.name }}
                      </h4>
                      <button 
                        type="button" 
                        class="btn btn-link text-danger fw-bold text-decoration-none d-flex align-items-center gap-1 p-0 shadow-none hover-underline"
                        v-if="group.id !== 'other' && !filters.nhom_phu_tung_id"
                        @click="selectPartGroup(group.id)"
                      >
                        Xem thêm <i class="bi bi-arrow-right"></i>
                      </button>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                      <div class="col" v-for="prod in (filters.nhom_phu_tung_id ? group.items : group.items.slice(0, 3))" :key="prod.id">
                        <ProductCard :product="prod" @add-to-cart="addToCart" />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Empty Phụ Tùng & Banner CSKH -->
                <div class="py-5 text-center" v-else>
                  <div class="empty-icon-circle mx-auto mb-3">
                    <i class="bi bi-search text-muted" style="font-size: 2rem;"></i>
                  </div>
                  <h5 class="fw-bold mb-2 text-navy" style="color: #0A1128;">Không tìm thấy phụ tùng phù hợp</h5>
                  <p class="text-muted mb-0">Vui lòng thay đổi từ khóa hoặc bộ lọc để xem các sản phẩm khác.</p>
                  
                  <div class="border-top mt-4 pt-4 mx-auto" style="max-width: 560px;">
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-3">
                      <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 52px; height: 52px; background-color: rgba(248,34,73,0.1);">
                        <i class="bi bi-headset fs-4" style="color: #f82249;"></i>
                      </div>
                      <div class="text-start">
                        <p class="fw-bold mb-1" style="color: #0A1128; font-size: 0.95rem;">Bạn không tìm thấy phụ tùng mong muốn?</p>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">JPM Marine hỗ trợ tra cứu và đặt hàng từ nước ngoài — <strong>Tư vấn miễn phí</strong>.</p>
                      </div>
                      <a href="tel:0900000000" class="btn btn-danger px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-uppercase text-nowrap flex-shrink-0" style="border-radius: 8px; letter-spacing: 0.5px; background-color: #f82249; border-color: #f82249;">
                        <i class="bi bi-telephone-fill me-2"></i> Liên Hệ Ngay
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer Phân Trang -->
              <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top" v-if="((activeTab === 'products' && filters.nhom_phu_tung_id) || activeTab === 'diagrams') && (activeTab === 'products' ? products.length : diagramsList.length) > 0">
                <span class="text-muted small">Hiển thị {{ activeTab === 'products' ? products.length : diagramsList.length }} của {{ pagination.total }} {{ activeTab === 'products' ? 'phụ tùng' : 'sơ đồ' }}</span>
                <ul class="pagination custom-pagination-rounded mb-0" v-if="pagination.last_page > 1">
                  <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)"><i class="bi bi-chevron-left"></i></a>
                  </li>
                  <li class="page-item" v-for="page in pagination.last_page" :key="page" :class="{ active: pagination.current_page === page }">
                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                  </li>
                  <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)"><i class="bi bi-chevron-right"></i></a>
                  </li>
                </ul>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, reactive, watch, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useCartStore } from '../../../stores/cartStore';
import ProductCard from '../../../components/client/ProductCard.vue';

export default {
  name: "KetQuaTraCuu",
  components: {
    ProductCard
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const queryClient = useQueryClient();
    const cartStore = useCartStore();

    const localKeyword = ref(route.query.keyword || "");
    let searchTimeout = null;
    watch(localKeyword, (val) => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filters.keyword = val;
        filters.page = 1;
      }, 500);
    });

    const filters = reactive({
      keyword: route.query.keyword || "",
      thuong_hieu_id: route.query.brand ? Number(route.query.brand) : '',
      loai_dong_co_id: route.query.engineType ? Number(route.query.engineType) : '',
      nhom_phu_tung_id: route.query.part ? Number(route.query.part) : '',
      dong_co_model_id: route.query.model ? Number(route.query.model) : '',
      nam_sx: route.query.year ? Number(route.query.year) : "",
      page: route.query.page ? parseInt(route.query.page) : 1,
      per_page: 12
    });

    watch(() => route.query, (newQuery) => {
      if (newQuery.keyword !== undefined && newQuery.keyword !== filters.keyword) {
        localKeyword.value = newQuery.keyword || "";
        filters.keyword = newQuery.keyword || "";
      }
      if (newQuery.brand !== undefined) filters.thuong_hieu_id = newQuery.brand ? Number(newQuery.brand) : '';
      if (newQuery.engineType !== undefined) filters.loai_dong_co_id = newQuery.engineType ? Number(newQuery.engineType) : '';
      if (newQuery.part !== undefined) filters.nhom_phu_tung_id = newQuery.part ? Number(newQuery.part) : '';
      if (newQuery.model !== undefined) filters.dong_co_model_id = newQuery.model ? Number(newQuery.model) : '';
      if (newQuery.year !== undefined) filters.nam_sx = newQuery.year ? Number(newQuery.year) : '';
      if (newQuery.page !== undefined) filters.page = newQuery.page ? parseInt(newQuery.page) : 1;
    }, { deep: true });

    const isFilterEmpty = computed(() => {
      return !filters.keyword && !filters.thuong_hieu_id && !filters.loai_dong_co_id && !filters.dong_co_model_id && !filters.nhom_phu_tung_id && !filters.nam_sx;
    });

    const { data: brands } = useQuery({
      queryKey: ['public-brands-v4'],
      queryFn: () => axiosClient.get('/public/thuong-hieu').then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30
    });

    const hasEngineSelected = computed(() => {
      return !!(filters.thuong_hieu_id || filters.loai_dong_co_id || filters.dong_co_model_id || filters.nam_sx);
    });

    const { data: partGroups, isFetching: isFetchingPartGroups } = useQuery({
      queryKey: computed(() => ['public-part-groups', filters.thuong_hieu_id, filters.loai_dong_co_id, filters.dong_co_model_id, filters.nam_sx]),
      queryFn: () => axiosClient.get('/public/nhom-phu-tung', { params: { thuong_hieu_id: filters.thuong_hieu_id, loai_dong_co_id: filters.loai_dong_co_id, dong_co_model_id: filters.dong_co_model_id, nam_sx: filters.nam_sx } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 5,
      enabled: hasEngineSelected
    });

    const { data: engineTypes } = useQuery({
      queryKey: computed(() => ['public-engine-types', filters.thuong_hieu_id]),
      queryFn: () => axiosClient.get('/public/loai-dong-co', { params: { thuong_hieu_id: filters.thuong_hieu_id } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!filters.thuong_hieu_id)
    });

    const { data: engineModels } = useQuery({
      queryKey: computed(() => ['public-models-v6', filters.thuong_hieu_id, filters.loai_dong_co_id, filters.nam_sx]),
      queryFn: () => axiosClient.get('/public/dong-co-model', { params: { thuong_hieu_id: filters.thuong_hieu_id, loai_dong_co_id: filters.loai_dong_co_id, nam_sx: filters.nam_sx } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!filters.thuong_hieu_id)
    });

    const { data: years } = useQuery({
      queryKey: computed(() => ['public-years-v6', filters.thuong_hieu_id, filters.loai_dong_co_id]),
      queryFn: () => axiosClient.get('/public/nam-san-xuat', { params: { thuong_hieu_id: filters.thuong_hieu_id, loai_dong_co_id: filters.loai_dong_co_id } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!filters.thuong_hieu_id)
    });

    // Diagrams Search Query (bản vẽ sơ đồ phụ tùng của máy)
    const { data: searchDiagrams, isFetching: isFetchingDiagrams } = useQuery({
      queryKey: computed(() => ['tra-cuu-so-do', { ...filters }]),
      queryFn: () => axiosClient.get('/public/tra-cuu-so-do', { params: filters }).then(res => res.data || res),
      keepPreviousData: true,
      enabled: computed(() => !isFilterEmpty.value)
    });

    // Parts Search Query (danh sách chi tiết phụ tùng)
    const { data: searchResult, isFetching } = useQuery({
      queryKey: computed(() => ['tra-cuu', { ...filters }]),
      queryFn: () => axiosClient.get('/public/tra-cuu', { params: { ...filters, per_page: filters.nhom_phu_tung_id ? 12 : 60 } }).then(res => res.data || res),
      keepPreviousData: true,
      enabled: computed(() => !isFilterEmpty.value)
    });

    const sortBy = ref('newest');
    const sortLabel = computed(() => {
      if (sortBy.value === 'price_asc') return 'Giá thấp đến cao';
      if (sortBy.value === 'price_desc') return 'Giá cao đến thấp';
      return 'Mới nhất';
    });

    const filteredProducts = computed(() => {
      const list = [...(searchResult.value?.data || [])];
      if (sortBy.value === 'price_asc') {
        return list.sort((a, b) => Number(a.gia_ban || 0) - Number(b.gia_ban || 0));
      }
      if (sortBy.value === 'price_desc') {
        return list.sort((a, b) => Number(b.gia_ban || 0) - Number(a.gia_ban || 0));
      }
      return list;
    });
    const products = filteredProducts;
    const diagramsList = computed(() => searchDiagrams.value?.data || []);
    const isFetchingGlobal = computed(() => isFetching.value || isFetchingDiagrams.value);

    // Nhóm sản phẩm theo Nhóm Phụ Tùng (chuẩn Luxury Minimalism đồng bộ Cửa Hàng)
    const groupedProducts = computed(() => {
      if (!products.value || products.value.length === 0) return [];
      
      const groupsMap = new Map();
      products.value.forEach(prod => {
        const groupId = prod.nhom_phu_tung?.id || prod.nhom_phu_tung_id || 'other';
        const groupName = prod.nhom_phu_tung?.ten_nhom 
          || (partGroups.value?.find(g => g.id === prod.nhom_phu_tung_id)?.ten_nhom)
          || (groupId === 'other' ? 'Phụ Tùng Khác' : `Nhóm #${groupId}`);
          
        if (!groupsMap.has(groupId)) {
          groupsMap.set(groupId, {
            id: groupId,
            name: groupName,
            items: []
          });
        }
        groupsMap.get(groupId).items.push(prod);
      });
      return Array.from(groupsMap.values());
    });

    const selectPartGroup = (id) => {
      filters.nhom_phu_tung_id = id;
      filters.page = 1;
    };

    const activeTab = ref('products');

    // Tự động chuyển tab thông minh nếu tab hiện tại trống mà tab kia có kết quả
    watch([products, diagramsList], ([newProds, newDiags]) => {
      if (newProds.length > 0 && newDiags.length === 0) {
        activeTab.value = 'products';
      } else if (newDiags.length > 0 && newProds.length === 0) {
        activeTab.value = 'diagrams';
      }
    });

    const pagination = computed(() => {
      if (activeTab.value === 'products') {
        return {
          current_page: searchResult.value?.current_page || 1,
          last_page: searchResult.value?.last_page || 1,
          total: searchResult.value?.total || 0,
        };
      }
      return {
        current_page: searchDiagrams.value?.current_page || 1,
        last_page: searchDiagrams.value?.last_page || 1,
        total: searchDiagrams.value?.total || 0,
      };
    });

    const activeFilters = computed(() => {
      const active = [];
      if (filters.thuong_hieu_id && brands.value) {
        const b = brands.value.find(x => x.id === filters.thuong_hieu_id);
        if (b) active.push({ type: 'brand', label: b.name, value: filters.thuong_hieu_id });
      }
      if (filters.loai_dong_co_id && engineTypes.value) {
        const l = engineTypes.value.find(x => x.id === filters.loai_dong_co_id);
        if (l) active.push({ type: 'engineType', label: l.ten_loai_dong_co, value: filters.loai_dong_co_id });
      }
      if (filters.dong_co_model_id && engineModels.value) {
        const m = engineModels.value.find(x => x.id === filters.dong_co_model_id);
        if (m) active.push({ type: 'model', label: m.ten_model, value: filters.dong_co_model_id });
      }
      if (filters.nam_sx) {
        active.push({ type: 'year', label: filters.nam_sx, value: filters.nam_sx });
      }
      if (filters.nhom_phu_tung_id) {
        const cat = partGroups.value?.find(x => x.id === filters.nhom_phu_tung_id);
        active.push({ type: 'part', label: cat ? cat.ten_nhom : `Nhóm #${filters.nhom_phu_tung_id}`, value: filters.nhom_phu_tung_id });
      }
      return active;
    });

    const openDropdown = ref(null);
    const toggleDropdown = (name) => { openDropdown.value = openDropdown.value === name ? null : name; };

    const selectOption = (dropdown, value) => {
      if (dropdown === 'brand') {
        filters.thuong_hieu_id = value;
        filters.loai_dong_co_id = '';
        filters.dong_co_model_id = '';
        filters.nam_sx = '';
      } else if (dropdown === 'engineType') {
        filters.loai_dong_co_id = value;
        filters.dong_co_model_id = '';
      } else if (dropdown === 'model') {
        filters.dong_co_model_id = value;
      } else if (dropdown === 'year') {
        filters.nam_sx = value;
      }
      openDropdown.value = null;
    };

    const toggleCategory = (id) => {
      if (!id || filters.nhom_phu_tung_id === id) {
        filters.nhom_phu_tung_id = '';
      } else {
        filters.nhom_phu_tung_id = id;
      }
      filters.page = 1;
    };

    const removeFilter = (type) => {
      if (type === 'brand') { filters.thuong_hieu_id = ''; filters.loai_dong_co_id = ''; filters.dong_co_model_id = ''; filters.nam_sx = ''; }
      else if (type === 'engineType') { filters.loai_dong_co_id = ''; filters.dong_co_model_id = ''; }
      else if (type === 'model') filters.dong_co_model_id = '';
      else if (type === 'year') filters.nam_sx = '';
      else if (type === 'part') filters.nhom_phu_tung_id = '';
    };

    const resetFilters = () => {
      localKeyword.value = ""; filters.keyword = ""; filters.thuong_hieu_id = ""; filters.loai_dong_co_id = "";
      filters.nhom_phu_tung_id = ''; filters.dong_co_model_id = ""; filters.nam_sx = ""; filters.page = 1;
      router.replace({ path: route.path, query: {} });
    };

    const changePage = (page) => {
      if (page >= 1 && page <= pagination.value.last_page) {
        filters.page = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    };

    watch(filters, (newFilters) => {
      const query = {};
      if (newFilters.keyword) query.keyword = newFilters.keyword;
      if (newFilters.thuong_hieu_id) query.brand = newFilters.thuong_hieu_id;
      if (newFilters.loai_dong_co_id) query.engineType = newFilters.loai_dong_co_id;
      if (newFilters.dong_co_model_id) query.model = newFilters.dong_co_model_id;
      if (newFilters.nam_sx) query.year = newFilters.nam_sx;
      if (newFilters.nhom_phu_tung_id) query.part = newFilters.nhom_phu_tung_id;
      if (newFilters.page > 1) query.page = newFilters.page;
      router.replace({ path: route.path, query });
    }, { deep: true });

    const brandLabel = computed(() => {
      if (!filters.thuong_hieu_id || !brands.value) return '-- Chọn thương hiệu --';
      const b = brands.value.find(x => x.id === filters.thuong_hieu_id);
      return b ? b.name : '-- Chọn thương hiệu --';
    });
    const engineTypeLabel = computed(() => {
      if (filters.loai_dong_co_id && engineTypes.value) {
        const e = engineTypes.value.find(x => x.id === filters.loai_dong_co_id);
        return e ? e.ten_loai_dong_co : '-- Chọn loại động cơ --';
      }
      return '-- Chọn loại động cơ --';
    });
    const modelLabel = computed(() => {
      if (!filters.dong_co_model_id || !engineModels.value) return '-- Chọn model động cơ --';
      const m = engineModels.value.find(x => x.id === filters.dong_co_model_id);
      return m ? m.ten_model : '-- Chọn model động cơ --';
    });
    const yearLabel = computed(() => filters.nam_sx || '-- Chọn năm sản xuất --');

    const goToDiagram = (diag) => {
      const diagBrandId = diag.model?.thuong_hieu_id || filters.thuong_hieu_id;
      const diagLoaiId = diag.model?.loai_dong_co_id || filters.loai_dong_co_id;
      const diagNam = diag.model?.nam_san_xuat || filters.nam_sx;

      const brand = brands.value?.find(b => b.id === diagBrandId);
      const engineType = engineTypes.value?.find(e => e.id === diagLoaiId);
      
      const brandSlug = brand ? (brand.slug || brand.name.toLowerCase().replace(/ /g, '-')) : 'all';
      const loaiSlug = engineType ? (engineType.slug || engineType.ten_loai_dong_co.toLowerCase().replace(/ /g, '-')) : 'all';
      const namStr = diagNam || 'all';
      
      const modelCode = diag.model ? encodeURIComponent(diag.model.ma_model) : 'all';
      
      router.push({
        name: 'SoDoPhuTung',
        params: {
          brand: brandSlug,
          loai: loaiSlug,
          nam: namStr,
          model: modelCode
        },
        query: {
          diagram_id: diag.id
        }
      });
    };

    const prefetchDiagram = (diag) => {
      const modelCodeRaw = diag.model?.ma_model;
      if (!modelCodeRaw) return;
      const modelCode = decodeURIComponent(encodeURIComponent(modelCodeRaw));
      queryClient.prefetchQuery({
        queryKey: ['clientDiagrams', modelCode],
        queryFn: () => axiosClient.get(`/public/so-do-model/${encodeURIComponent(modelCode)}`),
        staleTime: 1000 * 60 * 5
      });
    };

    const selectSort = (val) => {
      sortBy.value = val;
      openDropdown.value = null;
    };

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

    return {
      localKeyword,
      filters, brands, partGroups, engineTypes, engineModels, products, filteredProducts, diagramsList, pagination, isFetchingGlobal,
      groupedProducts, selectPartGroup,
      activeTab, searchResult, searchDiagrams,
      sortBy, sortLabel, selectSort, cartStore,
      goToDiagram, prefetchDiagram,
      resetFilters, changePage, openDropdown, toggleDropdown, selectOption,
      brandLabel, engineTypeLabel, modelLabel, yearLabel, years, activeFilters, removeFilter, toggleCategory, isFilterEmpty,
      hasEngineSelected, isFetchingPartGroups
    };
  },
  methods: {
    addToCart(prod) {
      this.cartStore.addItem({
        ...prod,
        id: prod.id,
        code: prod.ma_phu_tung,
        name: prod.ten_phu_tung,
        price: Number(prod.gia_ban || 0),
        is_gia_lien_he: Boolean(prod.is_gia_lien_he),
        image: prod.hinh_anh,
        loai_san_pham: 'phu_tung',
        stock: prod.so_luong_ton ?? 99
      }, 1);
      this.$toast?.success(`Đã thêm "${prod.ten_phu_tung}" vào giỏ hàng!`);
    }
  }
};
</script>

<style scoped>
.hover-underline:hover {
  text-decoration: underline !important;
}

/* ── TIÊU ĐỀ TRANG & BỐ CỤC ── */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 2rem;
  font-weight: 800;
  color: #0A1128;
  letter-spacing: -0.5px;
  margin-bottom: 0.35rem;
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 90px;
    z-index: 1040;
  }
}

/* ── SIDEBAR CARD CHUẨN LUXURY MINIMALISM ── */
.catalog-sidebar-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.03);
  overflow: visible;
}

.catalog-sidebar-header {
  height: 54px;
  min-height: 54px;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
  box-sizing: border-box;
  border-radius: 12px 12px 0 0;
}

.catalog-sidebar-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.15rem;
  font-weight: 700;
  color: #0A1128;
  margin: 0;
  line-height: 1.2;
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
  padding: 16px 18px;
  overflow: visible;
}

.sidebar-section-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.8125rem;
  font-weight: 800;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: #0A1128;
  padding: 0 2px 8px 2px;
  border-bottom: 1px solid #f1f5f9;
}

/* ── SEARCH INPUT IN SIDEBAR ── */
.search-input-box {
  display: flex;
  align-items: center;
  height: 42px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: none;
  padding: 0 12px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  width: 100%;
}

.search-input-box:focus-within {
  border-color: #f82249 !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: 0 !important;
}

.search-input-box:hover {
  border-color: #cbd5e1;
}

.search-input-icon {
  font-size: 0.9rem;
  color: #94a3b8;
  margin-right: 8px;
  flex-shrink: 0;
}

.search-input-field {
  flex: 1;
  border: none;
  background: transparent;
  padding: 0;
  font-size: 0.88rem;
  color: #1e293b;
  outline: none;
  box-shadow: none;
  width: 100%;
}

.search-input-field::placeholder {
  color: #94a3b8;
  font-size: 0.85rem;
}

.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  transition: color 0.15s ease;
}

.btn-clear-search:hover {
  color: #f82249;
}

/* ── CUSTOM SELECT & DROPDOWN ── */
.custom-select-wrapper {
  position: relative;
  width: 100%;
}

.custom-select-wrapper.z-active {
  z-index: 1060;
}

.custom-select-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 14px;
  height: 40px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.86rem;
  font-weight: 500;
  color: #1e293b;
  user-select: none;
}

.custom-select-box:hover {
  border-color: #cbd5e1;
}

.custom-select-box.is-focused {
  border-color: #f82249 !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
}

.custom-select-box.has-value {
  color: #0A1128;
  font-weight: 600;
}

.select-arrow {
  transition: transform 0.2s ease;
  font-size: 11px;
  color: #94a3b8;
  margin-left: 8px;
  flex-shrink: 0;
}

.select-arrow.rotated,
.custom-select-box.is-focused .select-arrow {
  transform: rotate(180deg);
  color: #f82249;
}

/* Sidebar Dropdown: Hiển thị dạng in-flow để đẩy các mục dưới và đẩy Card xuống một cách tự nhiên */
.jpm-split-sidebar .custom-dropdown-list {
  position: relative;
  top: 0;
  width: 100%;
  max-height: 220px;
  overflow-y: auto;
  margin-top: 6px;
  margin-bottom: 8px;
  list-style: none;
  padding: 6px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: none;
  z-index: 1;
}

/* Toolbar Dropdown (Sắp xếp): Nổi tuyệt đối trên Toolbar */
.custom-dropdown-list.align-right {
  position: absolute;
  top: calc(100% + 4px);
  right: 0;
  left: auto;
  min-width: 170px;
  width: max-content;
  max-height: 240px;
  overflow-y: auto;
  list-style: none;
  margin: 0;
  padding: 6px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 12px 30px rgba(10, 17, 40, 0.12);
  z-index: 1065;
}

.custom-dropdown-list li {
  padding: 8px 12px;
  font-size: 0.85rem;
  color: #334155;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.custom-dropdown-list li:hover {
  background: #f1f5f9;
  color: #0A1128;
}

.custom-dropdown-list li.active {
  background: rgba(248, 34, 73, 0.08);
  color: #f82249;
  font-weight: 700;
}

/* ── NAVIGATION LIST IN SIDEBAR (ĐỒNG BỘ 100% CỬA HÀNG SANPHAM.VUE) ── */
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

/* ── TABS CHUYỂN ĐỔI (PHỤ TÙNG / SƠ ĐỒ) ── */
.tab-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 18px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #475569;
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-pill-btn:hover {
  background: #f8fafc;
  color: #0A1128;
  border-color: #cbd5e1;
}

.tab-pill-btn.active {
  background: #0A1128;
  border-color: #0A1128;
  color: #ffffff;
}

.tab-pill-badge {
  background: #f1f5f9;
  color: #475569;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 999px;
  transition: all 0.2s ease;
}

.tab-pill-btn.active .tab-pill-badge {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}

/* ── ACTIVE FILTER PILL TAGS ── */
.filter-pill-tag {
  background: #f1f5f9;
  color: #0A1128;
  border: 1px solid #e2e8f0;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-remove-tag {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0;
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  font-size: 1rem;
  line-height: 1;
}

.btn-remove-tag:hover {
  color: #f82249;
}

.btn-clear-all {
  background: transparent;
  border: none;
  color: #f82249;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
}

.btn-clear-all:hover {
  color: #d8143a;
}

/* ── PRODUCT CARD (ĐỒNG BỘ 100% TRANG CHỦ & CỬA HÀNG) ── */
.product-card {
  border-radius: 12px;
  border: 1px solid #edf2f7;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.03);
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  overflow: hidden;
  background: #ffffff;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(10, 17, 40, 0.08);
  border-color: #cbd5e1;
}

.card-img-wrapper {
  background: #f8fafc;
  overflow: hidden;
}

.card-img-product {
  transition: transform 0.4s ease;
}

.product-card:hover .card-img-product {
  transform: scale(1.04);
}

.product-category {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: #94a3b8;
}

.product-card .card-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.95rem;
  font-weight: 700;
  line-height: 1.4;
  margin-bottom: 0.25rem;
}

.product-card .card-title a {
  color: #0A1128;
  text-decoration: none;
  transition: color 0.15s ease;
}

.product-card:hover .card-title a {
  color: #f82249;
}

.product-sku {
  font-size: 0.78rem;
  color: #64748b;
  line-height: 1.3;
}

.sku-label {
  color: #8392a5;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-right: 4px;
}

.sku-code {
  font-family: var(--default-font, sans-serif);
  color: #1e293b;
  font-weight: 600;
  letter-spacing: 0.2px;
}

.price-value {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.15rem;
  font-weight: 800;
}

.btn-cart-action {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  border: 1px solid #edf2f7;
  background: #f8fafc;
  color: #0A1128;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  transition: all 0.2s ease;
  padding: 0;
}

.btn-cart-action:hover {
  background: #f82249;
  border-color: #f82249;
  color: #ffffff;
  transform: scale(1.05);
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── EMPTY STATES ── */
.empty-state-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px dashed #cbd5e1;
}

.empty-icon-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(248, 34, 73, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ── SKELETON ── */
.skeleton-box {
  background: linear-gradient(90deg, #f0f0f0 25%, #e4e4e7 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
}

.skeleton-line {
  background: linear-gradient(90deg, #f0f0f0 25%, #e4e4e7 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* ── PHÂN TRANG ── */
.custom-pagination-rounded .page-link {
  border: none;
  margin: 0 3px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0A1128;
  font-weight: 600;
  border-radius: 8px !important;
  transition: all 0.2s ease;
  background: #f1f5f9;
}

.custom-pagination-rounded .page-item.active .page-link {
  background-color: #f82249;
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.25);
}

.custom-pagination-rounded .page-link:hover {
  background-color: #e2e8f0;
  color: #f82249;
}

.custom-pagination-rounded .page-item.disabled .page-link {
  opacity: 0.4;
  pointer-events: none;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
