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
                Cửa hàng <span class="text-danger">phụ tùng</span>
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
            <div class="card sticky-sidebar catalog-sidebar-card">
              <div class="catalog-sidebar-header">
                <h3 class="catalog-sidebar-title mb-0">Bộ lọc</h3>
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
                    Danh mục thiết bị
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

                    <!-- Danh mục 2 cấp lấy từ API -->
                    <div v-for="parent in parentCategories" :key="parent.id" class="catalog-parent-group">
                      <div class="catalog-parent-row d-flex align-items-center">
                        <button
                          type="button"
                          class="catalog-nav-btn flex-grow-1"
                          :class="{
                            active: selectedCategories.includes(parent.slug),
                            'is-parent-active': !selectedCategories.includes(parent.slug) && isParentOrChildActive(parent)
                          }"
                          @click="selectParentCategory(parent)"
                        >
                          <span class="catalog-nav-text" :title="parent.ten_danh_muc">{{ parent.ten_danh_muc }}</span>
                          <i v-if="parent.children.length === 0" class="bi bi-chevron-right catalog-nav-arrow ms-auto"></i>
                        </button>
                        <button
                          v-if="parent.children.length > 0"
                          type="button"
                          class="catalog-expand-btn"
                          :class="{ expanded: isParentExpanded(parent) }"
                          @click.stop="toggleParentExpand(parent.id)"
                          :title="isParentExpanded(parent) ? 'Thu gọn danh mục con' : 'Mở rộng danh mục con'"
                        >
                          <i class="bi bi-chevron-down"></i>
                        </button>
                      </div>

                      <div v-if="parent.children.length > 0 && isParentExpanded(parent)" class="catalog-child-list">
                        <button
                          v-for="child in parent.children"
                          :key="child.id"
                          type="button"
                          class="catalog-child-btn"
                          :class="{ active: selectedCategories.includes(child.slug) }"
                          @click="selectCategory(child.slug)"
                        >
                          <span class="catalog-child-dot"></span>
                          <span class="catalog-nav-text" :title="child.ten_danh_muc">{{ child.ten_danh_muc }}</span>
                        </button>
                      </div>
                    </div>
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
                    <h4 class="jpm-section-heading">{{ cat.ten_danh_muc }}</h4>
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
                <h4 class="jpm-section-heading">
                  <template v-if="selectedCategories.length === 1 && !searchKeyword">
                    {{ getCategoryName(selectedCategories[0]) }}
                  </template>
                  <template v-else-if="selectedCategories.length > 1 && !searchKeyword">
                    Bộ lọc ({{ selectedCategories.length }} danh mục)
                  </template>
                  <template v-else-if="searchKeyword">
                    Kết quả: "{{ searchKeyword }}"
                  </template>
                  <template v-else>
                    Tất cả sản phẩm
                  </template>
                </h4>

                <!-- Dropdown Sắp Xếp Chuẩn Luxury (Đồng bộ 100% qua JpmSelect) -->
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted small fw-medium text-nowrap d-none d-sm-inline">Sắp xếp:</span>
                  <JpmSelect
                    v-model="sortBy"
                    :options="sortOptions"
                    valueKey="value"
                    labelKey="label"
                    placeholder="Sắp xếp"
                    style="min-width: 175px;"
                  />
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
                <h5 class="empty-state-title mb-2">Chưa có sản phẩm phù hợp</h5>
                <p class="empty-state-desc mb-4 mx-auto">Rất tiếc, hiện chưa có sản phẩm nào phù hợp với bộ lọc bạn đã chọn.</p>
                <div>
                  <button 
                    type="button"
                    class="btn btn-reset-filters" 
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
import { ref, watch, computed } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useRoute, useRouter } from 'vue-router';
import { useCartStore } from '../../../stores/cartStore';
import { createToaster } from '../../../utils/notify';
import { useSiteSettings } from '../../../composables/useSiteSettings';
import ProductCard from '../../../components/client/ProductCard.vue';
import JpmSelect from '../../../components/client/JpmSelect.vue';

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

// Sort Options for JpmSelect
const sortOptions = [
  { value: 'newest', label: 'Mới nhất' },
  { value: 'price_asc', label: 'Giá: Thấp đến cao' },
  { value: 'price_desc', label: 'Giá: Cao đến thấp' },
];

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

const parentCategories = computed(() => {
  const list = categories.value || [];
  const parents = list.filter(c => !c.parent_id);
  const parentIds = new Set(parents.map(p => p.id));
  const tree = parents.map(p => ({
    ...p,
    children: list.filter(ch => ch.parent_id === p.id)
  }));
  const orphans = list.filter(c => c.parent_id && !parentIds.has(c.parent_id));
  orphans.forEach(o => tree.push({ ...o, children: [] }));
  return tree;
});

const expandedParents = ref({});
const toggleParentExpand = (parentId) => {
  expandedParents.value[parentId] = !isParentExpandedById(parentId);
};
const isParentExpandedById = (parentId) => {
  const parent = parentCategories.value.find(p => p.id === parentId);
  return parent ? isParentExpanded(parent) : false;
};
const isParentExpanded = (parent) => {
  if (expandedParents.value[parent.id] !== undefined) {
    return expandedParents.value[parent.id];
  }
  return selectedCategories.value.includes(parent.slug) ||
    parent.children.some(ch => selectedCategories.value.includes(ch.slug));
};
const isParentOrChildActive = (parent) => {
  return selectedCategories.value.includes(parent.slug) ||
    parent.children.some(ch => selectedCategories.value.includes(ch.slug));
};

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

const selectParentCategory = (parent) => {
  if (selectedCategories.value.includes(parent.slug)) {
    selectedCategories.value = [];
  } else {
    selectedCategories.value = [parent.slug];
    if (parent.children && parent.children.length > 0) {
      expandedParents.value[parent.id] = true;
    }
  }
};

const getCategoryName = (catIdOrSlug) => {
  const cat = categories.value.find(c => c.id == catIdOrSlug || c.slug === catIdOrSlug);
  if (!cat) return '';
  if (cat.parent_id) {
    const parent = categories.value.find(p => p.id === cat.parent_id);
    if (parent) return `${parent.ten_danh_muc} › ${cat.ten_danh_muc}`;
  }
  return cat.ten_danh_muc;
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
/* Hero Banner .store-hero-banner kế thừa 100% chuẩn hệ thống từ main.css */

/* ── SHOP PAGE LUXURY SLATE BACKGROUND ── */
.shop-catalog-page {
  background-color: #f8fafc;
  min-height: 100vh;
}

/* ── PAGE TITLE (MATCHING GIO HANG) ── */
.jpm-page-title {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--heading-color, #0A1128);
  letter-spacing: -0.3px;
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
  padding: 0 12px;
  height: var(--jpm-control-height, 37px);
  min-width: 145px;
  border-radius: var(--jpm-radius-control, 7px);
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.92rem;
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
  box-shadow: 0 10px 28px -6px rgba(10, 17, 40, 0.12) !important;
  display: flex;
  flex-direction: column;
  gap: 3px;
  z-index: 1055;
}

.custom-dropdown-list.align-right {
  right: 0;
  left: auto;
}

.custom-dropdown-list li {
  padding: 8px 12px;
  font-size: 0.88rem;
  font-weight: 500;
  color: #475569;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
  white-space: nowrap;
}

.custom-dropdown-list li:hover {
  background: #f1f5f9;
  color: #0A1128;
}

.custom-dropdown-list li.active {
  background: rgba(248, 34, 73, 0.08);
  color: #f82249;
  font-weight: 600;
}

/* ── SIDEBAR LUXURY STYLING (Kế thừa từ main.css, chỉ giữ giới hạn chiều cao cuộn riêng trang Cửa Hàng) ── */
.catalog-sidebar-card {
  max-height: calc(100vh - 106px);
}

.catalog-parent-group {
  display: flex;
  flex-direction: column;
}

.catalog-parent-row {
  gap: 4px;
}

.catalog-nav-btn.is-parent-active {
  color: #f82249;
  font-weight: 600;
  background: rgba(248, 34, 73, 0.05);
}

.catalog-expand-btn {
  width: 28px;
  height: 28px;
  border: none;
  background: transparent;
  color: #64748b;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.2s ease, background-color 0.15s ease, color 0.15s ease;
}

.catalog-expand-btn:hover {
  background: #f1f5f9;
  color: #0A1128;
}

.catalog-expand-btn.expanded i {
  transform: rotate(180deg);
}

.catalog-expand-btn i {
  font-size: 0.78rem;
  transition: transform 0.2s ease;
}

.catalog-child-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin: 2px 0 6px 14px;
  padding-left: 10px;
  border-left: 1.5px solid #e2e8f0;
}

.catalog-child-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  border-radius: 7px;
  padding: 6px 10px;
  font-size: 0.85rem;
  font-weight: 500;
  color: #475569;
  transition: all 0.15s ease;
}

.catalog-child-btn:hover {
  background: #f8fafc;
  color: #0A1128;
}

.catalog-child-btn.active {
  background: rgba(248, 34, 73, 0.08);
  color: #f82249;
  font-weight: 600;
}

.catalog-child-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: currentColor;
  opacity: 0.55;
  flex-shrink: 0;
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
  font-family: var(--heading-font, var(--jpm-font-family));
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
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  border-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  box-shadow: none !important;
  transform: translateY(-1px) !important;
}

.btn-reset-filters:active {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  border-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
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
  box-shadow: none !important;
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






