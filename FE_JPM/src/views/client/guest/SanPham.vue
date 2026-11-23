<template>
  <div class="shop-catalog-page main-page">

    <!-- HERO BANNER CỬA HÀNG DÀI NGANG FULL MÀN HÌNH -->
    <div class="store-hero-banner position-relative overflow-hidden" :style="storeBannerStyle">
      <!-- Lớp phủ Gradient làm trắng bên trái mềm mại -->
      <div class="store-banner-overlay"></div>

      <!-- Nội dung chữ bên trái -->
      <div class="container h-100 position-relative z-2">
        <div class="row h-100 align-items-center">
          <div class="col-12 col-md-9 col-lg-7 store-banner-content py-4 py-lg-0">
            <!-- Badge thương hiệu cao cấp -->
            <div class="d-inline-flex align-items-center gap-2 store-banner-badge mb-2">
              <span class="badge-dot"></span>
              <span>JPM MARINE STORE</span>
              <span class="badge-divider">•</span>
              <span class="badge-sub">CHÍNH HÃNG NHẬT BẢN</span>
            </div>
            
            <!-- Tiêu đề lớn -->
            <h1 class="store-banner-title mb-2">
              <template v-if="settings.store_banner_title">
                {{ settings.store_banner_title }}
              </template>
              <template v-else>
                Cửa Hàng <span class="text-danger">Phụ Tùng</span>
              </template>
            </h1>

            <!-- Dòng chữ nhỏ mô tả bên dưới -->
            <p class="store-banner-subtitle mb-0">
              {{ settings.store_banner_subtitle || 'Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <section id="products-section" class="section pb-5 pt-4">
      <div class="container">

        <!-- MAIN CONTENT: SPLIT LAYOUT -->
        <div class="jpm-split-layout mt-1">
          
          <!-- SIDEBAR -->
          <div class="jpm-split-sidebar mb-4 mb-lg-0">
            <div class="card sticky-sidebar catalog-sidebar-card shadow-sm">
              <div class="catalog-sidebar-header">
                <h3 class="catalog-sidebar-title mb-0">Bộ Lọc</h3>
                <button 
                  type="button"
                  class="btn-reset-sidebar-filter" 
                  :class="{ 'is-hidden': !(selectedCategories.length > 0 || searchInput) }"
                  @click="resetFilters"
                >
                  Xóa lọc
                </button>
              </div>
                
              <div class="catalog-sidebar-body custom-scrollbar">
                <!-- TÌM KIẾM SẢN PHẨM -->
                <div class="search-input-box mb-3">
                  <i class="bi bi-search search-input-icon"></i>
                  <input 
                    type="text" 
                    class="search-input-field" 
                    placeholder="Tìm tên hoặc mã..." 
                    v-model="searchInput" 
                    @keyup.enter="performSearch"
                  >
                  <button 
                    v-if="searchInput" 
                    type="button" 
                    class="btn-clear-search" 
                    @click="searchInput = ''; searchKeyword = ''"
                    title="Xóa tìm kiếm"
                  >
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>

                <!-- DANH MỤC THIẾT BỊ -->
                <div class="sidebar-category-section">
                  <div class="sidebar-section-title mb-2">
                    Danh Mục Thiết Bị
                  </div>
                  <div v-if="isLoadingCategories" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-danger"></div>
                  </div>
                  <div v-else class="catalog-nav-list">
                    <!-- Nút Tất Cả Sản Phẩm -->
                    <button
                      type="button"
                      class="catalog-nav-btn"
                      :class="{ active: selectedCategories.length === 0 }"
                      @click="selectAllCategories"
                    >
                      <span class="catalog-nav-text">Tất cả sản phẩm</span>
                      <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                    </button>

                    <!-- Danh mục lấy từ API -->
                    <button 
                      v-for="cat in categories" 
                      :key="cat.id"
                      type="button"
                      class="catalog-nav-btn"
                      :class="{ active: selectedCategories.includes(cat.slug) }"
                      @click="selectCategory(cat.slug)"
                    >
                      <span class="catalog-nav-text" :title="cat.ten_danh_muc">{{ cat.ten_danh_muc }}</span>
                      <i class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- PRODUCT GRID -->
          <div class="jpm-split-content pl-lg-4">
            
            
            
            <!-- GROUPED MODE (Default, no filters) -->
            <div v-if="isGroupedMode">
              <div v-if="isLoadingGrouped" class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2 g-sm-3">
                <div class="col" v-for="i in 8" :key="i">
                  <ProductCard :loading="true" />
                </div>
              </div>
              <div v-else-if="groupedCategories.length > 0">
                <div v-for="cat in groupedCategories" :key="cat.id" class="mb-5">
                  <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h4 class="fw-bold mb-0 text-capitalize" style="color: var(--heading-color, #0A1128); font-size: 1.25rem; font-family: var(--heading-font, 'Raleway', sans-serif);">{{ cat.ten_danh_muc }}</h4>
                    <button type="button" class="jpm-more-link" @click="viewCategory(cat.slug)">
                      Xem thêm <i class="bi bi-arrow-right"></i>
                    </button>
                  </div>
                  
                  <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2 g-sm-3">
                    <div class="col" v-for="prod in (cat.products ? cat.products.slice(0, 4) : [])" :key="prod.id">
                      <ProductCard :product="prod" @add-to-cart="addToCart" />
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-5 my-4">
                 <div class="empty-icon-circle mb-3">
                   <i class="bi bi-box-seam"></i>
                 </div>
                 <h5 class="empty-state-title mb-1">Chưa Có Sản Phẩm Nào</h5>
                 <p class="empty-state-desc mx-auto mb-0">Danh mục sản phẩm hiện đang được cập nhật.</p>
              </div>
            </div>

            <!-- FILTERED MODE (With search or category selected) -->
            <div v-else>
              <!-- Thanh điều khiển: Tiêu đề + Dropdown Sắp xếp -->
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-2">
                <h4 class="fw-bold mb-0 text-capitalize" style="color: var(--heading-color, #0A1128); font-size: 1.25rem; font-family: var(--heading-font, 'Raleway', sans-serif);">
                  <template v-if="selectedCategories.length === 1 && !searchKeyword">
                    {{ getCategoryName(selectedCategories[0]) }}
                  </template>
                  <template v-else-if="selectedCategories.length > 1 && !searchKeyword">
                    Bộ Lọc ({{ selectedCategories.length }} danh mục)
                  </template>
                  <template v-else-if="searchKeyword">
                    Kết quả: "{{ searchKeyword }}"
                  </template>
                  <template v-else>
                    Tất Cả Sản Phẩm
                  </template>
                </h4>

                <!-- Dropdown Sắp Xếp Chuẩn Luxury (Đồng bộ 100% Thương Hiệu) -->
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted small fw-medium text-nowrap d-none d-sm-inline">Sắp xếp:</span>
                  <div class="custom-select-wrapper" :class="{ 'z-active': isSortDropdownOpen }">
                    <div 
                      class="custom-select-box shop-sort-box" 
                      :class="{ 'is-focused': isSortDropdownOpen }"
                      @click.stop="isSortDropdownOpen = !isSortDropdownOpen"
                    >
                      <span class="text-truncate">{{ currentSortLabel }}</span>
                      <i class="bi bi-chevron-down select-arrow" :class="{ rotated: isSortDropdownOpen }"></i>
                    </div>
                    <ul class="custom-dropdown-list align-right shadow-sm" v-show="isSortDropdownOpen">
                      <li 
                        v-for="opt in sortOptions" 
                        :key="opt.value" 
                        :class="{ active: sortBy === opt.value }"
                        @click="selectSort(opt.value)"
                      >
                        {{ opt.label }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <!-- Loading Skeleton -->
              <div v-if="isLoadingProducts" class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2 g-sm-3">
                <div class="col" v-for="i in 8" :key="i">
                  <ProductCard :loading="true" />
                </div>
              </div>

              <!-- Grid -->
              <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2 g-sm-3" v-else-if="products.length > 0">
                <div class="col" v-for="prod in products" :key="prod.id">
                  <ProductCard :product="prod" @add-to-cart="addToCart" />
                </div>
              </div>
              
              <!-- Empty State (Không nằm trong Card) -->
              <div class="text-center py-5 my-4" v-else>
                <div class="empty-icon-circle mb-3">
                  <i class="bi bi-box-seam"></i>
                </div>
                <h5 class="empty-state-title mb-2">Chưa Có Sản Phẩm Phù Hợp</h5>
                <p class="empty-state-desc mb-4 mx-auto">Rất tiếc, hiện chưa có sản phẩm nào phù hợp với bộ lọc bạn đã chọn.</p>
                <div>
                  <button 
                    type="button"
                    class="btn btn-reset-filters shadow-sm" 
                    @click="resetFilters"
                  >
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa bộ lọc
                  </button>
                </div>
              </div>
              
              <!-- Pagination -->
              <nav v-if="totalPages > 1" class="mt-5 pt-3">
                <ul class="pagination justify-content-center border-0 gap-2 m-0 p-0">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button 
                      class="page-link shadow-none jpm-pagination-btn d-flex align-items-center justify-content-center" 
                      :disabled="currentPage === 1"
                      @click="currentPage--"
                      title="Trang trước"
                    >
                      <i class="bi bi-chevron-left"></i>
                    </button>
                  </li>
                  <li class="page-item" v-for="page in totalPages" :key="page">
                    <button 
                      class="page-link shadow-none jpm-pagination-btn d-flex align-items-center justify-content-center" 
                      :class="{ 'active': currentPage === page }"
                      @click="currentPage = page"
                    >
                      {{ page }}
                    </button>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <button 
                      class="page-link shadow-none jpm-pagination-btn d-flex align-items-center justify-content-center" 
                      :disabled="currentPage === totalPages"
                      @click="currentPage++"
                      title="Trang tiếp"
                    >
                      <i class="bi bi-chevron-right"></i>
                    </button>
                  </li>
                </ul>
              </nav>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useRoute, useRouter } from 'vue-router';
import { useCartStore } from '../../../stores/cartStore';
import { createToaster } from '@meforma/vue-toaster';
import { useSiteSettings } from '../../../composables/useSiteSettings';
import ProductCard from '../../../components/client/ProductCard.vue';

const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();
const toaster = createToaster({ position: 'top-right', duration: 3000 });
const { settings } = useSiteSettings();

// Simple debounce function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

const searchInput = ref(route.query.keyword || '');
const searchKeyword = ref(route.query.keyword || '');
const selectedCategories = ref(route.query.danh_muc ? (Array.isArray(route.query.danh_muc) ? route.query.danh_muc : [route.query.danh_muc]) : []);
const sortBy = ref(route.query.sort || 'newest');
const currentPage = ref(parseInt(route.query.page) || 1);

// Custom Luxury Sort Dropdown
const isSortDropdownOpen = ref(false);
const sortOptions = [
  { value: 'newest', label: 'Mới nhất' },
  { value: 'price_asc', label: 'Giá: Thấp đến cao' },
  { value: 'price_desc', label: 'Giá: Cao đến thấp' },
];
const currentSortLabel = computed(() => {
  return sortOptions.find(opt => opt.value === sortBy.value)?.label || 'Mới nhất';
});
const selectSort = (val) => {
  sortBy.value = val;
  isSortDropdownOpen.value = false;
};
const handleClickOutside = (event) => {
  if (isSortDropdownOpen.value && !event.target.closest('.custom-select-wrapper')) {
    isSortDropdownOpen.value = false;
  }
};
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  isSortDropdownOpen.value = false;
});

// Computed property for mode
const isGroupedMode = computed(() => {
  return selectedCategories.value.length === 0 && !searchKeyword.value;
});

// Fetch Categories
const { data: categoriesData, isLoading: isLoadingCategories } = useQuery({
  queryKey: ['publicCategories'],
  queryFn: async () => {
    const res = await axiosClient.get('/public/danh-muc-san-pham');
    return res.data || [];
  },
  staleTime: 5 * 60 * 1000,
});
const categories = computed(() => categoriesData.value || []);

// Fetch Grouped Data (when no filter is applied)
const { data: groupedData, isLoading: isLoadingGrouped } = useQuery({
  queryKey: ['publicGroupedProducts'],
  queryFn: async () => {
    const res = await axiosClient.get('/public/danh-muc-san-pham/grouped');
    return res.data || [];
  },
  enabled: isGroupedMode,
  staleTime: 5 * 60 * 1000,
});
const groupedCategories = computed(() => groupedData.value || []);


// Fetch Products (when filter is applied)
const fetchProducts = async () => {
  const params = {
    page: currentPage.value,
    sort: sortBy.value,
    per_page: 16,
  };
  if (selectedCategories.value.length > 0) params.danh_muc = selectedCategories.value.join(',');
  if (searchKeyword.value) params.keyword = searchKeyword.value;

  const res = await axiosClient.get('/public/san-pham', { params });
  return res.data || res;
};

const { data: productsData, isLoading: isLoadingProducts, refetch } = useQuery({
  queryKey: computed(() => ['publicProducts', searchKeyword.value, [...selectedCategories.value], sortBy.value, currentPage.value]),
  queryFn: fetchProducts,
  keepPreviousData: true,
  enabled: computed(() => !isGroupedMode.value) // only fetch flat products if filters are active
});

const products = computed(() => {
    if (!productsData.value) return [];
    return productsData.value.data ? productsData.value.data : (Array.isArray(productsData.value) ? productsData.value : []);
});
const totalPages = computed(() => productsData.value?.last_page || 1);

const updateUrl = debounce(() => {
  const query = { ...route.query };
  
  if (searchKeyword.value) query.keyword = searchKeyword.value;
  else delete query.keyword;

  if (selectedCategories.value.length > 0) query.danh_muc = selectedCategories.value;
  else delete query.danh_muc;

  if (sortBy.value !== 'newest') query.sort = sortBy.value;
  else delete query.sort;

  if (currentPage.value > 1) query.page = currentPage.value;
  else delete query.page;

  router.replace({ query });
  
  if (!isGroupedMode.value) {
      refetch();
  }
}, 500);

watch([searchKeyword, selectedCategories, sortBy], () => {
  currentPage.value = 1;
  updateUrl();
});

watch(currentPage, () => {
  updateUrl();
});

watch(() => route.query.danh_muc, (newVal) => {
  if (newVal) {
    selectedCategories.value = Array.isArray(newVal) ? newVal : [newVal];
  } else {
    selectedCategories.value = [];
  }
});

const resetFilters = () => {
  searchInput.value = '';
  searchKeyword.value = '';
  selectedCategories.value = [];
  sortBy.value = 'newest';
  currentPage.value = 1;
};

const performSearch = () => {
  if (searchKeyword.value !== searchInput.value) {
    searchKeyword.value = searchInput.value;
  }
};

const viewCategory = (slug) => {
  selectedCategories.value = [slug];
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const selectAllCategories = () => {
  selectedCategories.value = [];
};

const selectCategory = (slug) => {
  if (selectedCategories.value.includes(slug)) {
    selectedCategories.value = [];
  } else {
    selectedCategories.value = [slug];
  }
};

const getCategoryName = (catIdOrSlug) => {
  const cat = categories.value.find(c => c.id == catIdOrSlug || c.slug === catIdOrSlug);
  return cat ? cat.ten_danh_muc : '';
};

// Auto-search when typing (debounced)
watch(searchInput, debounce((newVal) => {
  searchKeyword.value = newVal;
}, 500));

const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('vi-VN').format(price);
};

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  if (path.startsWith('/client/') || path.startsWith('/assets/')) return path;
  const baseUrl = import.meta.env.VITE_API_BASE_URL ? import.meta.env.VITE_API_BASE_URL.replace(/\/api\/?$/, '') : 'http://localhost:8000';
  const cleanPath = path.startsWith('/') ? path : '/' + path;
  return `${baseUrl}${cleanPath}`;
};

const storeBannerStyle = computed(() => {
  const rawPath = (settings.value && settings.value.store_banner_image) ? settings.value.store_banner_image : '/client/image/shop_banner.png';
  const imgUrl = rawPath.startsWith('http') || rawPath.startsWith('/client/') || rawPath.startsWith('/assets/')
    ? rawPath
    : getImageUrl(rawPath);
  return {
    backgroundImage: `url("${imgUrl}")`,
    backgroundSize: 'cover',
    backgroundPosition: 'center center',
    backgroundRepeat: 'no-repeat',
  };
});

const addToCart = (item) => {
  if (!item.is_gia_lien_he && item.so_luong_ton !== undefined && item.so_luong_ton <= 0) {
    toaster.error("Sản phẩm hiện đã hết hàng!");
    return;
  }
  cartStore.addItem({
    ...item,
    is_gia_lien_he: Boolean(item.is_gia_lien_he)
  }, 1);
  toaster.success(item.is_gia_lien_he ? "Đã thêm vào danh sách yêu cầu báo giá!" : "Thêm sản phẩm thành công");
};

const scrollToProducts = () => {
  const el = document.getElementById('products-section');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};
</script>

<style scoped>
/* Nút Xem thêm chuẩn: Tuyệt đối không gạch chân khi hover, icon mũi tên trượt nhẹ */
.btn-see-more,
.btn-see-more:hover,
.btn-see-more:focus,
.btn-see-more:active {
  text-decoration: none !important;
  color: var(--jpm-color-red-marine, #F82249) !important;
  box-shadow: none !important;
}

.btn-see-more i {
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-see-more:hover i {
  transform: translateX(3px);
}
/* ── STORE HERO BANNER (Dài ngang full màn hình, căn giữa ảnh theo chiều dọc ~250px) ── */
.store-hero-banner {
  width: 100%;
  height: clamp(230px, 18vw, 270px);
  min-height: 220px;
  max-height: 270px;
  background-color: #ffffff;
  background-image: url('/client/image/shop_banner.png');
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  border-top: none !important;
  border-left: none !important;
  border-right: none !important;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: none !important;
  margin-top: -1px;
}

.store-banner-overlay {
  position: absolute;
  top: 0;
  bottom: 0;
  left: -2px;
  right: 0;
  z-index: 1;
  background: linear-gradient(
    90deg,
    #ffffff 0%,
    #ffffff 24%,
    rgba(255, 255, 255, 0.95) 34%,
    rgba(255, 255, 255, 0.65) 44%,
    rgba(255, 255, 255, 0.18) 54%,
    transparent 62%,
    transparent 100%
  );
  pointer-events: none;
}

.store-banner-content {
  animation: bannerFadeInLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes bannerFadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.store-banner-badge {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--accent-color, #F82249);
}

.store-banner-badge .badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--accent-color, #F82249);
  display: inline-block;
  box-shadow: 0 0 8px rgba(248, 34, 73, 0.6);
}

.store-banner-badge .badge-divider {
  color: #cbd5e1;
}

.store-banner-badge .badge-sub {
  color: #64748b;
  font-weight: 600;
  letter-spacing: 1px;
}

.store-banner-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: clamp(1.65rem, 2.4vw, 2.1rem);
  font-weight: 800;
  color: #0a1128;
  line-height: 1.2;
  letter-spacing: -0.5px;
}

.store-banner-subtitle {
  font-size: clamp(0.825rem, 1vw, 0.875rem);
  color: #475569;
  line-height: 1.55;
  max-width: 500px;
}

@media (max-width: 991.98px) {
  .store-hero-banner {
    min-height: 200px;
    height: auto;
    padding: 28px 0;
  }
  .store-banner-overlay {
    background: linear-gradient(
      90deg,
      #ffffff 0%,
      rgba(255, 255, 255, 0.98) 38%,
      rgba(255, 255, 255, 0.85) 52%,
      rgba(255, 255, 255, 0.25) 68%,
      transparent 80%
    );
  }
}

@media (max-width: 767.98px) {
  .store-hero-banner {
    min-height: 180px;
    height: auto;
    padding: 20px 0;
  }
  .store-banner-overlay {
    background: linear-gradient(
      90deg,
      rgba(255, 255, 255, 0.94) 0%,
      rgba(255, 255, 255, 0.88) 60%,
      rgba(255, 255, 255, 0.50) 100%
    );
  }
  .store-banner-subtitle {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
}

/* ── SHOP PAGE LUXURY SLATE BACKGROUND ── */
.shop-catalog-page {
  background-color: #f8fafc;
  min-height: 100vh;
}

/* ── PAGE TITLE (MATCHING GIO HANG) ── */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 800;
  color: #0A1128;
  letter-spacing: -0.5px;
}

/* ── LUXURY CUSTOM SELECT & DROPDOWN (ĐỒNG BỘ 100% THƯƠNG HIỆU) ── */
.custom-select-wrapper {
  position: relative;
  display: inline-block;
}

.custom-select-wrapper.z-active {
  z-index: 1050;
}

.custom-select-box.shop-sort-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 14px;
  height: 38px;
  min-width: 155px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.86rem;
  font-weight: 500;
  color: #1e293b;
  font-family: var(--default-font, sans-serif);
  user-select: none;
}

.custom-select-box.shop-sort-box:hover {
  border-color: #cbd5e1;
}

.custom-select-box.shop-sort-box.is-focused {
  border-color: #f82249 !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: 0 !important;
}

.select-arrow {
  transition: transform 0.2s ease;
  font-size: 11px;
  color: #94a3b8;
  margin-left: 8px;
  flex-shrink: 0;
}

.select-arrow.rotated,
.custom-select-box.shop-sort-box.is-focused .select-arrow {
  transform: rotate(180deg);
  color: #f82249;
}

.custom-dropdown-list {
  position: absolute;
  min-width: 100%;
  width: max-content;
  max-width: 220px;
  top: calc(100% + 4px);
  list-style: none;
  margin: 0;
  padding: 6px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: none !important;
  z-index: 1055;
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
  color: #f82249;
  font-weight: 700;
}

/* ── SIDEBAR LUXURY STYLING (ĐỒNG BỘ 100% THUONG HIEU) ── */
.catalog-sidebar-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(10, 17, 40, 0.03);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: calc(100vh - 106px);
}

@media (min-width: 992px) {
  .sticky-sidebar {
    position: sticky;
    top: 86px;
    z-index: 100;
  }
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
  flex-shrink: 0;
}

.catalog-sidebar-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.1rem;
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
  color: var(--jpm-color-red-hover, #D9163A);
  text-decoration: underline;
}

.catalog-sidebar-body {
  padding: 14px;
  flex: 1 1 auto;
  overflow-y: auto;
  overflow-x: hidden;
}

.sidebar-section-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.8125rem;
  font-weight: 800;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: #0A1128; /* Deep Navy chuẩn thương hiệu JPM - tương phản sắc nét với danh sách con */
  padding: 0 4px 8px 4px;
  border-bottom: 1px solid #f1f5f9;
}

/* ── SEARCH INPUT IN SIDEBAR (MATCHING THUONG HIEU) ── */
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
  transition: color 0.15s ease-in-out;
}

.search-input-box:focus-within .search-input-icon {
  color: #f82249;
}

.search-input-field {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.875rem;
  font-family: var(--default-font, sans-serif);
  color: #0A1128;
  font-weight: 400;
  padding: 0;
  height: 100%;
}

.search-input-field::placeholder {
  color: #94a3b8;
}

.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 0.9rem;
  cursor: pointer;
  padding: 0;
  margin-left: 6px;
  line-height: 1;
  flex-shrink: 0;
  transition: color 0.15s ease;
}

.btn-clear-search:hover {
  color: var(--accent-color, #F82249);
}

/* ── NAVIGATION LIST IN SIDEBAR ── */
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
  color: #475569; /* Slate trung tính, mềm mại hơn so với tiêu đề Navy */
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

/* ── LUXURY EMPTY STATE ── */
.empty-icon-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #f1f5f9;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 1.85rem;
  transition: all 0.3s ease;
}

.empty-state-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 700;
  font-size: 1.15rem;
  color: #0A1128;
  letter-spacing: -0.2px;
}

.empty-state-desc {
  max-width: 440px;
  font-size: 0.92rem;
  color: #64748b;
  line-height: 1.5;
}

.btn-reset-filters {
  background-color: #f82249 !important;
  border: 1px solid #f82249 !important;
  color: #ffffff !important;
  border-radius: 8px !important;
  font-size: 0.88rem !important;
  font-weight: 600 !important;
  padding: 8px 22px !important;
  transition: all 0.2s ease;
}

.btn-reset-filters:hover {
  background-color: var(--jpm-color-red-hover, #D9163A) !important;
  border-color: var(--jpm-color-red-hover, #D9163A) !important;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) !important;
  transform: translateY(-1px);
}

.btn-reset-filters:focus {
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.15) !important;
}

/* ── PAGINATION BUTTONS ── */
.jpm-pagination-btn {
  width: 38px;
  height: 38px;
  border-radius: 8px !important;
  border: 1px solid #e2e8f0 !important;
  background-color: #ffffff;
  color: #475569;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}
.jpm-pagination-btn:hover:not(:disabled):not(.active) {
  background-color: #f8fafc;
  border-color: #cbd5e1 !important;
  color: #1e293b;
}
.jpm-pagination-btn.active {
  background-color: #f82249 !important;
  border-color: #f82249 !important;
  color: #ffffff !important;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
}
.page-item.disabled .jpm-pagination-btn,
.jpm-pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: #f8fafc;
}

/* ── SLEEK SCROLLBAR ── */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: var(--jpm-radius-badge, 6px);
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>

<style>
.premium-product-card:hover .action-overlay {
  opacity: 1 !important;
  transform: translateX(0) !important;
}
</style>






