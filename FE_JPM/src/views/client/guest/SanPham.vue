<template>
  <main class="main-page bg-white">

    <section id="products-section" class="section pb-5" style="padding-top: 110px;">
      <div class="container">

        <!-- Header chuẩn Giỏ Hàng -->
        <div class="row mb-4">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">Cửa Hàng <span class="text-danger">Hàng Hải</span></h2>
            <p class="text-muted">Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.</p>
          </div>
        </div>

        <!-- MAIN CONTENT: SPLIT LAYOUT -->
        <div class="jpm-split-layout mt-2" data-aos="fade-up" data-aos-delay="100">
          
          <!-- SIDEBAR -->
          <div class="jpm-split-sidebar mb-4 mb-lg-0">
            <div class="card sticky-top catalog-sidebar-card shadow-sm" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; top: 110px; z-index: 10; background: #fff;">
              <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0; min-height: 56px;">
                <h4 class="mb-0 jpm-card-title d-flex align-items-center" style="font-size: 1rem; font-weight: 700; color: #1e293b;">
                  <i class="bi bi-funnel-fill me-2" style="color: #f82249;"></i>Bộ Lọc
                </h4>
                <button class="btn btn-sm btn-link text-danger text-decoration-none p-0 shadow-none fw-semibold" style="font-size: 0.85rem;" @click="resetFilters">Xóa tất cả</button>
              </div>
                
              <div class="card-body p-4 custom-scrollbar" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                <!-- TIM KIEM -->
                <div class="mb-4 pb-4 border-bottom">
                  <h6 class="section-label mb-3 fw-bold" style="font-size: 0.85rem; color: #1e293b;"><i class="bi bi-search me-1 text-muted"></i> Tìm Sản Phẩm</h6>
                  <div class="position-relative">
                    <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.85rem; pointer-events: none;"></i>
                    <input 
                      type="text" 
                      class="form-control shop-search-input" 
                      placeholder="Nhập tên hoặc mã..." 
                      v-model="searchInput" 
                      @keyup.enter="performSearch"
                    >
                  </div>
                </div>

                <!-- DANH MUC -->
                <div class="mb-2">
                  <h6 class="section-label mb-3 fw-bold" style="font-size: 0.85rem; color: #1e293b;"><i class="bi bi-tags-fill me-1 text-muted"></i> Danh Mục</h6>
                  <div v-if="isLoadingCategories" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-danger"></div>
                  </div>
                  <div v-else class="d-flex flex-column gap-1">
                    <div 
                      v-for="cat in categories" 
                      :key="cat.id"
                      class="category-filter-item px-2 py-2 rounded-2"
                      :class="{ 'category-item-active': selectedCategories.includes(cat.slug) }"
                    >
                      <label class="form-check m-0 d-flex align-items-center flex-grow-1 user-select-none" style="cursor: pointer;">
                        <input 
                          class="form-check-input me-2 mt-0 custom-category-checkbox shadow-none" 
                          type="checkbox" 
                          :checked="selectedCategories.includes(cat.slug)" 
                          @change="selectCategory(cat.slug)" 
                          :id="'cat_'+cat.id"
                        >
                        <span 
                          class="category-name flex-grow-1"
                          :class="selectedCategories.includes(cat.slug) ? 'fw-bold text-danger' : 'text-secondary'"
                          style="font-size: 0.9rem; text-transform: capitalize; transition: 0.2s;"
                        >
                          {{ cat.ten_danh_muc.toLowerCase() }}
                        </span>
                      </label>
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
              <div v-if="isLoadingGrouped" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <div class="col" v-for="i in 6" :key="i">
                  <div class="card h-100 premium-product-card border-0 shadow-sm">
                    <div class="card-img-wrapper skeleton-box" style="aspect-ratio: 4/3;"></div>
                    <div class="card-body p-3">
                      <div class="skeleton-line" style="width: 50%; height: 12px; margin-bottom: 10px;"></div>
                      <div class="skeleton-line" style="width: 90%; height: 16px; margin-bottom: 8px;"></div>
                      <div class="skeleton-line" style="width: 70%; height: 16px; margin-bottom: 16px;"></div>
                      <div class="d-flex justify-content-end">
                        <div class="skeleton-line" style="width: 40%; height: 22px;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else-if="groupedCategories.length > 0">
                <div v-for="cat in groupedCategories" :key="cat.id" class="mb-5">
                  <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h4 class="fw-bold mb-0" style="color: var(--heading-color); font-size: 1.25rem;">{{ cat.ten_danh_muc }}</h4>
                    <button class="btn btn-link text-danger fw-bold text-decoration-none d-flex align-items-center gap-1 p-0 shadow-none hover-underline" @click="viewCategory(cat.slug)">
                      Xem thêm <i class="bi bi-arrow-right"></i>
                    </button>
                  </div>
                  
                  <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                    <div class="col" v-for="prod in cat.products" :key="prod.id">
                      <!-- PRODUCT CARD -->
                      <div class="card h-100 premium-product-card group-hover position-relative">
                        <div class="card-img-wrapper overflow-hidden img-zoom-hover">
                          <img
                            :src="getImageUrl(prod.hinh_anh_chinh)"
                            class="card-img-top w-100 transition-transform"
                            style="aspect-ratio: 4/3; object-fit: cover;"
                            :alt="prod.ten_san_pham"
                            @error="$event.target.src='https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM'"
                          >
                          <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                            <button
                              class="btn btn-cart-action shadow-sm"
                              :title="!prod.is_gia_lien_he && prod.so_luong_ton !== undefined && prod.so_luong_ton <= 0 ? 'Hết hàng' : (prod.is_gia_lien_he ? 'Thêm vào giỏ báo giá' : 'Thêm vào giỏ')"
                              :disabled="!prod.is_gia_lien_he && prod.so_luong_ton !== undefined && prod.so_luong_ton <= 0"
                              @click.prevent="addToCart(prod)"
                            >
                              <i class="bi bi-cart2"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body text-start p-3 d-flex flex-column bg-white">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ prod.danh_muc?.ten_danh_muc || getCategoryName(prod.danh_muc_san_pham_id) || 'OEM' }}</span>
                          </div>
                          <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                            <router-link :to="`/chi-tiet-san-pham/${prod.slug}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ prod.ten_san_pham }}</router-link>
                          </h5>
                          <div class="mt-auto d-flex align-items-center contact-action" style="padding-top: 0.25rem; justify-content: flex-end;">
                            <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                            <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ formatPrice(prod.gia_ban) }} đ</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-5 card shadow-sm mt-4" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #fff;">
                 <p class="text-muted mb-0">Chưa có sản phẩm nào.</p>
              </div>
            </div>

            <!-- FILTERED MODE (With search or category selected) -->
            <div v-else>
              <div class="mb-4 border-bottom pb-2" v-if="selectedCategories.length === 1 && !searchKeyword">
                <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
                  {{ getCategoryName(selectedCategories[0]) }}
                </h4>
              </div>
              <div class="mb-4 border-bottom pb-2" v-else-if="selectedCategories.length > 1 && !searchKeyword">
                <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
                  Sản phẩm theo bộ lọc ({{ selectedCategories.length }} danh mục)
                </h4>
              </div>
              <div class="mb-4 border-bottom pb-2" v-else-if="searchKeyword">
                <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
                  Kết quả tìm kiếm cho: "{{ searchKeyword }}"
                </h4>
              </div>
              
              <!-- Loading Skeleton -->
              <div v-if="isLoadingProducts" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <div class="col" v-for="i in 6" :key="i">
                  <div class="card h-100 premium-product-card border-0 shadow-sm">
                    <div class="card-img-wrapper skeleton-box" style="aspect-ratio: 4/3;"></div>
                    <div class="card-body p-3">
                      <div class="skeleton-line" style="width: 50%; height: 12px; margin-bottom: 10px;"></div>
                      <div class="skeleton-line" style="width: 90%; height: 16px; margin-bottom: 8px;"></div>
                      <div class="skeleton-line" style="width: 70%; height: 16px; margin-bottom: 16px;"></div>
                      <div class="d-flex justify-content-end">
                        <div class="skeleton-line" style="width: 40%; height: 22px;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Grid -->
              <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" v-else-if="products.length > 0">
                <div class="col" v-for="prod in products" :key="prod.id">
                  <!-- PRODUCT CARD -->
                  <div class="card h-100 premium-product-card group-hover position-relative">
                    <div class="card-img-wrapper overflow-hidden img-zoom-hover">
                      <img
                        :src="getImageUrl(prod.hinh_anh_chinh)"
                        class="card-img-top w-100 transition-transform"
                        style="aspect-ratio: 4/3; object-fit: cover;"
                        :alt="prod.ten_san_pham"
                        @error="$event.target.src='https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM'"
                      >
                      <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <button
                          class="btn btn-cart-action shadow-sm"
                          :title="!prod.is_gia_lien_he && prod.so_luong_ton !== undefined && prod.so_luong_ton <= 0 ? 'Hết hàng' : (prod.is_gia_lien_he ? 'Thêm vào giỏ báo giá' : 'Thêm vào giỏ')"
                          :disabled="!prod.is_gia_lien_he && prod.so_luong_ton !== undefined && prod.so_luong_ton <= 0"
                          @click.prevent="addToCart(prod)"
                        >
                          <i class="bi bi-cart2"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body text-start p-3 d-flex flex-column bg-white">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ prod.danh_muc?.ten_danh_muc || getCategoryName(prod.danh_muc_san_pham_id) || 'OEM' }}</span>
                      </div>
                      <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                        <router-link :to="`/chi-tiet-san-pham/${prod.slug}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ prod.ten_san_pham }}</router-link>
                      </h5>
                      <div class="mt-auto d-flex align-items-center contact-action" style="padding-top: 0.25rem; justify-content: flex-end;">
                        <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                        <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ formatPrice(prod.gia_ban) }} đ</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Empty State -->
              <div class="text-center py-5 card p-5 shadow-sm mt-4" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #fff;" v-else>
                <i class="bi bi-box-seam text-muted" style="font-size: 3.5rem; opacity: 0.4;"></i>
                <h5 class="mt-4 fw-bold text-uppercase" style="letter-spacing: 0.5px; color: #1e293b;">Không tìm thấy sản phẩm</h5>
                <p class="text-muted mt-2" style="font-weight: 400; font-size: 0.95rem;">Rất tiếc, không có sản phẩm nào phù hợp với bộ lọc hiện tại của bạn.</p>
                <div>
                  <button class="btn btn-danger mt-3 px-4 py-2 fw-semibold" style="border-radius: 8px; background-color: #f82249; border-color: #f82249; font-size: 0.9rem;" @click="resetFilters">
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
  </main>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useRoute, useRouter } from 'vue-router';
import { useCartStore } from '../../../stores/cartStore';
import { createToaster } from '@meforma/vue-toaster';

const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();
const toaster = createToaster({ position: 'top-right', duration: 3000 });

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
  
  const selectCategory = (slug) => {
    const index = selectedCategories.value.indexOf(slug);
    if (index > -1) {
        selectedCategories.value.splice(index, 1);
    } else {
        selectedCategories.value.push(slug);
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
  if (!path) return 'https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM';
  if (path.startsWith('http')) return path;
  return 'http://localhost:8000' + path; // Fallback
};

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
/* ── PAGE TITLE (MATCHING GIO HANG) ── */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 800;
  color: #0A1128;
  letter-spacing: -0.5px;
}

/* ── SEARCH INPUT IN SIDEBAR ── */
.shop-search-input {
  padding-left: 36px !important;
  border-radius: 8px !important;
  border: 1px solid #e2e8f0 !important;
  font-size: 0.88rem !important;
  background-color: #ffffff !important;
  height: 40px;
  transition: all 0.2s ease;
}
.shop-search-input:focus {
  border-color: #f82249 !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: none !important;
}

/* ── CATEGORY FILTER ITEMS ── */
.category-filter-item {
  border-radius: 8px;
  transition: all 0.2s ease;
}
.category-filter-item:hover {
  background-color: #f8fafc;
}
.category-filter-item.category-item-active {
  background-color: rgba(248, 34, 73, 0.08);
}
.custom-category-checkbox {
  border-color: #cbd5e1 !important;
  border-radius: 4px !important;
  cursor: pointer;
}
.custom-category-checkbox:checked {
  background-color: #f82249 !important;
  border-color: #f82249 !important;
}
.custom-category-checkbox:focus {
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
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
  box-shadow: 0 4px 10px rgba(248, 34, 73, 0.25);
}
.page-item.disabled .jpm-pagination-btn,
.jpm-pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: #f8fafc;
}

/* ── SLEEK SCROLLBAR ── */
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

<style>
.premium-product-card:hover .action-overlay {
  opacity: 1 !important;
  transform: translateX(0) !important;
}
</style>






