<template>
  <main class="tra-cuu-results-page main-page" style="position: relative; z-index: 10; min-height: 130vh;">
    <section class="section" style="padding-top: 110px; padding-bottom: 60px;">
      <div class="container">

        <!-- PAGE TITLE & BREADCRUMB -->
        <div class="mb-4" data-aos="fade-down">
          <div class="page-title-box mb-2">
            <template v-if="isFilterEmpty">
              <h2 class="jpm-page-title">
                Tra Cứu <span class="text-danger">Phụ Tùng</span>
              </h2>
              <p class="text-muted mb-0">
                Lựa chọn chính xác phụ tùng thay thế cho động cơ của bạn.
              </p>
            </template>
            <template v-else-if="filters.dong_co_model_id && !filters.nhom_phu_tung_id && !filters.keyword">
              <h2 class="jpm-page-title">
                Sơ Đồ Phụ Tùng <span class="text-danger">Model {{ modelLabel }}</span>
              </h2>
              <p class="text-muted mb-0">
                Hiển thị danh sách các nhóm sơ đồ phụ tùng cho model này.
              </p>
            </template>
            <template v-else>
              <h2 class="jpm-page-title">
                Kết Quả Tra Cứu <span class="text-danger">Phụ Tùng</span>
              </h2>
              <p class="text-muted mb-0">
                Tìm thấy <strong class="text-danger">{{ pagination.total }}</strong> kết quả cho tìm kiếm của bạn.
              </p>
            </template>
          </div>
          <nav aria-label="breadcrumb" class="custom-breadcrumb pt-2">
            <ol class="breadcrumb mb-0 align-items-center">
              <li class="breadcrumb-item">
                <router-link to="/" class="text-decoration-none text-muted d-flex align-items-center">
                  <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                </router-link>
              </li>
              <li class="breadcrumb-item">
                <span class="text-muted">Tra Cứu</span>
              </li>
              <li class="breadcrumb-item active fw-bold" style="color: var(--heading-color);" aria-current="page">
                Phụ tùng tàu thuyền
              </li>
            </ol>
          </nav>
        </div>

        <!-- MAIN SPLIT LAYOUT -->
        <div class="jpm-split-layout" data-aos="fade-up" data-aos-delay="100">

          <!-- BÊN TRÁI: BỘ LỌC -->
          <div class="jpm-split-sidebar mb-4 mb-lg-0" style="position: relative; z-index: 1040;">
            <div class="card sticky-sidebar border-0 shadow-sm" style="border-radius: 16px; overflow: visible; min-height: calc(100vh - 130px);">

              <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                <h4 class="mb-0 jpm-card-title"><i class="bi bi-funnel-fill me-2" style="color: var(--accent-color);"></i>Bộ Lọc</h4>
                <button class="btn btn-sm btn-link text-danger text-decoration-none p-0 shadow-none fw-bold" @click="resetFilters">Xóa tất cả</button>
              </div>

              <div class="card-body p-4">

                <!-- TÌM MÃ SKU -->
                <div class="mb-4 pb-4 border-bottom">
                  <h6 class="section-label"><i class="bi bi-search me-1"></i> Tìm Mã SKU / Từ Khóa</h6>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 shadow-none" placeholder="VD: 31105-PR7-A01" v-model="localKeyword">
                  </div>
                </div>

                <!-- TƯƠNG THÍCH ĐỘNG CƠ -->
                <div class="mb-4 pb-4 border-bottom">
                  <h6 class="section-label"><i class="bi bi-gear me-1"></i> Tương Thích Động Cơ</h6>

                  <div class="custom-select-wrapper mb-2" :class="{ 'z-active': openDropdown === 'brand' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'brand', 'has-value': filters.thuong_hieu_id !== '' }" @click="toggleDropdown('brand')">
                      <span class="text-truncate">{{ brandLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'brand' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left" v-show="openDropdown === 'brand'">
                      <li @click="selectOption('brand', '')" :class="{ active: filters.thuong_hieu_id === '' }">Tất cả thương hiệu</li>
                      <li v-for="b in brands" :key="b.id" @click="selectOption('brand', b.id)" :class="{ active: filters.thuong_hieu_id === b.id }">{{ b.name }}</li>
                    </ul>
                  </div>

                  <div class="custom-select-wrapper mb-2" :class="{ 'z-active': openDropdown === 'engineType' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'engineType', 'has-value': filters.loai_dong_co_id !== '' }" @click="toggleDropdown('engineType')">
                      <span class="text-truncate">{{ engineTypeLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'engineType' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left" v-show="openDropdown === 'engineType'">
                      <li @click="selectOption('engineType', '')" :class="{ active: filters.loai_dong_co_id === '' }">Tất cả loại động cơ</li>
                      <li v-for="e in engineTypes" :key="e.id" @click="selectOption('engineType', e.id)" :class="{ active: filters.loai_dong_co_id === e.id }">{{ e.ten_loai_dong_co }}</li>
                    </ul>
                  </div>

                  <div class="custom-select-wrapper mb-2" :class="{ 'z-active': openDropdown === 'year' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'year', 'has-value': filters.nam_sx !== '' }" @click="toggleDropdown('year')">
                      <span class="text-truncate">{{ yearLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'year' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left" v-show="openDropdown === 'year'">
                      <li @click="selectOption('year', '')" :class="{ active: filters.nam_sx === '' }">Tất cả các năm</li>
                      <li v-for="y in years" :key="y" @click="selectOption('year', y)" :class="{ active: filters.nam_sx === y }">{{ y }}</li>
                    </ul>
                  </div>

                  <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'model' }">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'model', 'has-value': filters.dong_co_model_id !== '' }" @click="toggleDropdown('model')">
                      <span class="text-truncate">{{ modelLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'model' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-left" v-show="openDropdown === 'model'">
                      <li @click="selectOption('model', '')" :class="{ active: filters.dong_co_model_id === '' }">Tất cả model</li>
                      <li v-for="m in engineModels" :key="m.id" @click="selectOption('model', m.id)" :class="{ active: filters.dong_co_model_id === m.id }">{{ m.ten_model }}</li>
                    </ul>
                  </div>
                </div>

                <!-- NHÓM PHỤ TÙNG -->
                <div>
                  <h6 class="section-label"><i class="bi bi-puzzle me-1"></i> Nhóm Phụ Tùng</h6>
                  <ul class="filter-list" v-if="partGroups && partGroups.length > 0">
                    <li v-for="cat in partGroups" :key="cat.id">
                      <label class="d-flex align-items-center gap-2 w-100" style="cursor: pointer;">
                        <input type="checkbox" :checked="filters.nhom_phu_tung_id === cat.id" @change="toggleCategory(cat.id)" class="form-check-input shadow-none flex-shrink-0" style="width: 16px; height: 16px; margin-top: 0;">
                        <span class="text-truncate" style="font-size: 13px; color: #495057;">{{ cat.ten_nhom }}</span>
                      </label>
                    </li>
                  </ul>
                  <div v-else class="text-muted mt-3" style="font-size: 13px; font-style: italic; line-height: 1.5;">
                    Vui lòng chọn Hãng, Loại, Năm và Model để tải danh sách nhóm phụ tùng.
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- BÊN PHẢI: KẾT QUẢ -->
          <div class="jpm-split-content">

            <div class="text-center py-5" v-if="isFilterEmpty">
               <div class="py-5 mt-4">
                 <div class="mb-4">
                    <i class="bi bi-search" style="font-size: 64px; color: #ced4da;"></i>
                 </div>
                 <h4 class="fw-bold mb-3" style="color: #495057;">Bắt đầu tra cứu phụ tùng</h4>
                 <p class="text-muted mb-0 mx-auto" style="font-size: 1rem; max-width: 500px; line-height: 1.6;">
                    Hãy lựa chọn các thông số của động cơ như Thương hiệu, Model, Năm sản xuất ở bộ lọc bên trái, hoặc trực tiếp nhập mã SKU để tìm kiếm.
                 </p>
               </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: visible;" v-if="!isFilterEmpty">
              <!-- Toolbar -->
              <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                  <span class="text-muted small fw-bold" v-if="activeFilters.length > 0">Đang lọc theo:</span>
                  <span class="badge-filter" v-for="(filter, index) in activeFilters" :key="index">
                    {{ filter.label }} <i class="bi bi-x" style="cursor: pointer;" @click="removeFilter(filter.type)"></i>
                  </span>
                   <span class="badge rounded-pill px-3 py-2 text-nowrap" style="background-color: #f1f3f7; color: #0e1b4d; font-weight: 700;">
                     {{ pagination.total }} kết quả
                   </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <label class="text-muted text-nowrap mb-0 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sắp xếp:</label>
                  <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'sort' }" style="width: 140px;">
                    <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'sort' }" @click="toggleDropdown('sort')">
                      <span class="text-truncate">Mới nhất</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'sort' }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-right" v-show="openDropdown === 'sort'">
                      <li class="active">Mới nhất</li>
                    </ul>
                  </div>
                </div>
              </div>

              </div>
              <div v-if="!isFilterEmpty">
                
                <!-- TAB SWITCHER: Phụ tùng vs Sơ đồ -->
                <div class="d-flex align-items-center gap-2 mb-4 border-bottom pb-3" v-if="products.length > 0 || diagramsList.length > 0 || isFetchingGlobal">
                  <button 
                    type="button"
                    class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 fw-bold"
                    :class="activeTab === 'products' ? 'btn-danger shadow-sm' : 'btn-light border text-muted'"
                    style="border-radius: 8px;"
                    @click="activeTab = 'products'"
                  >
                    <i class="bi bi-box-seam"></i>
                    <span>Phụ tùng chi tiết</span>
                    <span class="badge" :class="activeTab === 'products' ? 'bg-white text-danger' : 'bg-secondary-subtle text-secondary'">{{ searchResult?.total || products.length }}</span>
                  </button>

                  <button 
                    type="button"
                    class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 fw-bold"
                    :class="activeTab === 'diagrams' ? 'btn-danger shadow-sm' : 'btn-light border text-muted'"
                    style="border-radius: 8px;"
                    @click="activeTab = 'diagrams'"
                  >
                    <i class="bi bi-diagram-3"></i>
                    <span>Sơ đồ bản vẽ máy</span>
                    <span class="badge" :class="activeTab === 'diagrams' ? 'bg-white text-danger' : 'bg-secondary-subtle text-secondary'">{{ searchDiagrams?.total || diagramsList.length }}</span>
                  </button>
                </div>

                <!-- LOADING SKELETON -->
                <div v-if="isFetchingGlobal" class="row g-4">
                  <div class="col-md-4 col-sm-6" v-for="i in 6" :key="i">
                    <div class="skeleton-card">
                      <div class="skeleton-img skeleton-box"></div>
                      <div class="p-3">
                        <div class="skeleton-box mx-auto" style="height: 16px; width: 60%;"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- TAB 1: SƠ ĐỒ BẢN VẼ -->
                <div v-else-if="activeTab === 'diagrams'">
                  <div class="row g-4" v-if="diagramsList.length > 0">
                    <div class="col-md-4 col-sm-6" v-for="diag in diagramsList" :key="diag.id">
                      <div class="card h-100 premium-product-card group-hover" @click="goToDiagram(diag)" @mouseenter="prefetchDiagram(diag)" style="cursor: pointer; border: 1px solid #e5e7eb; border-radius: 0; box-shadow: none;">
                        <div class="card-img-wrapper overflow-hidden img-zoom-hover" style="border-radius: 0;">
                          <img :src="diag.hinh_anh ? diag.hinh_anh : 'https://placehold.co/400x400/f8f9fa/a1acb8?text=No+Image'" class="card-img-top w-100 transition-transform" style="aspect-ratio: 4/3; object-fit: cover; border-radius: 0;" :alt="diag.ten_so_do">
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
                  
                  <div class="text-center py-5" v-else>
                    <i class="bi bi-images text-muted mb-3 d-block" style="font-size: 48px; opacity: 0.3;"></i>
                    <h5 class="fw-bold mb-2" style="color: var(--heading-color);">Chưa có sơ đồ phụ tùng cho tìm kiếm này</h5>
                    <p class="text-muted mb-0">Bạn có thể bấm sang tab "Phụ tùng chi tiết" để xem các linh kiện tương thích.</p>
                  </div>
                </div>

                <!-- TAB 2: PHỤ TÙNG CHI TIẾT -->
                <div v-else-if="activeTab === 'products'">
                  <div class="row g-4" v-if="products.length > 0">
                    <div class="col-md-4 col-sm-6" v-for="prod in products" :key="prod.id">
                      <div class="card h-100 premium-product-card group-hover position-relative" style="border: 1px solid #e5e7eb; border-radius: 0; box-shadow: none;">
                        <div class="card-img-wrapper overflow-hidden img-zoom-hover" style="border-radius: 0;">
                          <img :src="prod.hinh_anh ? prod.hinh_anh : 'https://placehold.co/400x300/f8f9fa/a1acb8?text=No+Image'" class="card-img-top w-100 transition-transform" style="aspect-ratio: 4/3; object-fit: cover; border-radius: 0;" :alt="prod.ten_phu_tung">
                          <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                            <button class="btn bg-white shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 4px;" title="Thêm vào giỏ" @click.prevent="addToCart(prod)">
                              <i class="bi bi-cart-plus" style="color: #dc3545; font-size: 1.1rem;"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body text-start p-3 d-flex flex-column bg-white">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ prod.thuong_hieu ? prod.thuong_hieu.name : 'OEM' }}</span>
                          </div>
                          <h5 class="card-title mb-1" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                            <router-link :to="`/chi-tiet-phu-tung/${prod.ma_phu_tung || prod.id}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ prod.ten_phu_tung }}</router-link>
                          </h5>
                          <p class="text-muted mb-3" style="font-size: 0.75rem;">Mã OEM: <strong style="color: #334155;">{{ prod.ma_phu_tung }}</strong></p>
                          <div class="mt-auto d-flex align-items-center contact-action" style="border-top: 1px solid #f1f5f9; padding-top: 0.75rem; justify-content: flex-end;">
                            <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                            <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ Number(prod.gia_ban).toLocaleString('vi-VN') }} đ</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Empty State cho Products -->
                  <div class="py-5 text-center" v-else>
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(248,34,73,0.07); border-radius: 50%;">
                      <i class="bi bi-search" style="font-size: 36px; color: var(--accent-color); opacity: 0.65;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--heading-color);">Không tìm thấy phụ tùng phù hợp</h5>
                    <p class="text-muted mb-0">Vui lòng thay đổi từ khóa hoặc bộ lọc để xem các sản phẩm khác.</p>
                    <div class="border-top mt-4 pt-4 mx-auto" style="max-width: 560px;">
                      <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-3">
                        <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 52px; height: 52px; background-color: rgba(248,34,73,0.1);">
                          <i class="bi bi-headset fs-4" style="color: #f82249;"></i>
                        </div>
                        <div class="text-start">
                          <p class="fw-bold mb-1" style="color: #0e1b4d; font-size: 0.95rem;">Bạn không tìm thấy phụ tùng mong muốn?</p>
                          <p class="text-muted mb-0" style="font-size: 0.85rem;">JPM Marine hỗ trợ tra cứu và đặt hàng từ nước ngoài — <strong>Tư vấn miễn phí</strong>.</p>
                        </div>
                        <a href="tel:0900000000" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-uppercase text-nowrap flex-shrink-0" style="letter-spacing: 0.5px; background-color: #f82249; border-color: #f82249;">
                          <i class="bi bi-telephone-fill me-2"></i> Liên Hệ Ngay
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Footer Phân Trang -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top" v-if="(activeTab === 'products' ? products.length : diagramsList.length) > 0">
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
  </main>
</template>

<script>
import { ref, reactive, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';

export default {
  name: "KetQuaTraCuu",
  setup() {
    const route = useRoute();
    const router = useRouter();
    const queryClient = useQueryClient();

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

    const { data: partGroups } = useQuery({
      queryKey: computed(() => ['public-part-groups', filters.thuong_hieu_id, filters.loai_dong_co_id, filters.dong_co_model_id, filters.nam_sx]),
      queryFn: () => axiosClient.get('/public/nhom-phu-tung', { params: { thuong_hieu_id: filters.thuong_hieu_id, loai_dong_co_id: filters.loai_dong_co_id, dong_co_model_id: filters.dong_co_model_id, nam_sx: filters.nam_sx } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 5,
        enabled: computed(() => !!filters.thuong_hieu_id)
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
      queryFn: () => axiosClient.get('/public/tra-cuu', { params: filters }).then(res => res.data || res),
      keepPreviousData: true,
      enabled: computed(() => !isFilterEmpty.value)
    });

    const products = computed(() => searchResult.value?.data || []);
    const diagramsList = computed(() => searchDiagrams.value?.data || []);
    const isFetchingGlobal = computed(() => isFetching.value || isFetchingDiagrams.value);

    const activeTab = ref(filters.keyword ? 'products' : 'diagrams');

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
      if (filters.nhom_phu_tung_id && partGroups.value) {
        const cat = partGroups.value.find(x => x.id === filters.nhom_phu_tung_id);
        if (cat) active.push({ type: 'part', label: cat.ten_nhom, value: filters.nhom_phu_tung_id });
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
      filters.nhom_phu_tung_id = filters.nhom_phu_tung_id === id ? '' : id;
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
      // Ưu tiên lấy thông tin từ diag.model, nếu không có mới fallback về filter
      const diagBrandId = diag.model?.thuong_hieu_id || filters.thuong_hieu_id;
      const diagLoaiId = diag.model?.loai_dong_co_id || filters.loai_dong_co_id;
      const diagNam = diag.model?.nam_san_xuat || filters.nam_sx;

      const brand = brands.value?.find(b => b.id === diagBrandId);
      const engineType = engineTypes.value?.find(e => e.id === diagLoaiId);
      
      const brandSlug = brand ? (brand.slug || brand.name.toLowerCase().replace(/ /g, '-')) : 'all';
      const loaiSlug = engineType ? (engineType.slug || engineType.ten_loai_dong_co.toLowerCase().replace(/ /g, '-')) : 'all';
      const namStr = diagNam || 'all';
      
      // Quan trọng nhất: lấy mã model trực tiếp từ diag (sơ đồ) thay vì từ bộ lọc
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

    // Prefetch diagram detail page data on card hover → near-instant navigation
    const prefetchDiagram = (diag) => {
      const modelCodeRaw = diag.model?.ma_model;
      if (!modelCodeRaw) return;
      const modelCode = decodeURIComponent(encodeURIComponent(modelCodeRaw));
      queryClient.prefetchQuery({
        queryKey: ['clientDiagrams', modelCode],
        queryFn: () => axiosClient.get(`/public/so-do-model/${encodeURIComponent(modelCode)}`),
        staleTime: 1000 * 60 * 5 // keep warm 5 minutes
      });
    };

    return {
      localKeyword,
      filters, brands, partGroups, engineTypes, engineModels, products, diagramsList, pagination, isFetchingGlobal,
      activeTab, searchResult, searchDiagrams,
      goToDiagram, prefetchDiagram,
      resetFilters, changePage, openDropdown, toggleDropdown, selectOption,
      brandLabel, engineTypeLabel, modelLabel, yearLabel, years, activeFilters, removeFilter, toggleCategory, isFilterEmpty
    };
  },
  methods: {
    addToCart(prod) {
      this.$toast?.success(`Đã thêm "${prod.ten_phu_tung}" vào giỏ hàng!`);
    }
  }
};
</script>

<style scoped>
/* Breadcrumb */
.custom-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; color: #ccc;
}
.custom-breadcrumb .breadcrumb-item a:hover { color: var(--accent-color) !important; transition: color 0.2s; }

/* Sticky Sidebar */
@media (min-width: 992px) {
  .sticky-sidebar { position: sticky; top: 100px; z-index: 1040; }
}

/* Section Labels */
.section-label { font-size: 0.85rem; font-weight: 600; text-transform: none; color: #1e293b; letter-spacing: normal; margin-bottom: 12px; }

/* Filter List */
.filter-list { list-style: none; padding: 0; margin: 0; }
.filter-list li { margin-bottom: 10px; }
.filter-list .form-check-input:checked { background-color: var(--accent-color); border-color: var(--accent-color); }

/* Active Filter Badges */
.badge-filter {
  background: color-mix(in srgb, var(--accent-color), transparent 90%);
  color: var(--accent-color);
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  border: 1px solid color-mix(in srgb, var(--accent-color), transparent 80%);
  transition: all 0.2s;
}
.badge-filter:hover { background: var(--accent-color); color: #fff; }

/* Skeleton */
.skeleton-card { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #eee; }
.skeleton-img { height: 200px; }
.skeleton-box { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 4px; display: block; }
@keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

/* Pagination */
.custom-pagination-rounded .page-link { border: none; margin: 0 2px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #0e1b4d; font-weight: 600; border-radius: 50% !important; transition: all 0.2s ease; background: #f1f3f7; }
.custom-pagination-rounded .page-item.active .page-link { background-color: #f82249; color: #fff; box-shadow: 0 4px 8px rgba(248, 34, 73, 0.2); }
.custom-pagination-rounded .page-link:hover { background-color: #e9ecef; color: #f82249; }
.custom-pagination-rounded .page-item.disabled .page-link { opacity: 0.4; pointer-events: none; }

/* Khống chế chiều cao menu dropdown Sidebar để không bị quá dài, giữ nguyên hiệu ứng Overlay nổi lên trên */
:deep(.jpm-split-sidebar .custom-dropdown-list) {
  max-height: 260px !important;
  overflow-y: auto !important;
  z-index: 1050 !important;
}
</style>




