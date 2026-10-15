<template>
  <main class="main-page bg-white" style="padding-top: 70px;">

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 1: HERO BANNER WITH SUBMERGED WATERMARK IMAGE
    ════════════════════════════════════════════════════════════════ -->
    <section class="hero-editorial-section position-relative overflow-hidden">
      <!-- Submerged Marine Image (Ảnh chìm trong nền) -->
      <div class="hero-submerged-bg"></div>
      <!-- Watermark dots background -->
      <div class="hero-watermark-bg"></div>

      <div class="container position-relative z-1 py-5">
        <div class="row align-items-center py-lg-4">

          <!-- Left Column: Big Title + Description + Pill Button -->
          <div class="col-lg-6 text-start">
            <h1 class="hero-main-heading mb-4">
              Cửa Hàng<br />
              Phụ Tùng <span class="text-secondary fw-bold">&amp; Thiết Bị</span><br />
              Hàng Hải
            </h1>
            <p class="hero-subtext text-muted mb-4">
              Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu: Yamaha, Mercury, Suzuki, Honda, Tohatsu.
            </p>
            <button type="button" class="btn btn-dark-pill px-4 py-2" @click="scrollToProducts">
              Khám phá sản phẩm
            </button>
          </div>

        </div>
      </div>
    </section>

    <section id="products-section" class="section py-4">
      <div class="container">

        <!-- Modern Breadcrumb -->
        <nav aria-label="breadcrumb" class="custom-breadcrumb pt-2 mb-4 border-bottom pb-3">
          <ol class="breadcrumb mb-0 align-items-center" style="font-size: 0.85rem;">
            <li class="breadcrumb-item">
              <router-link to="/" class="text-muted text-decoration-none"><i class="bi bi-house-door-fill me-1"></i> Trang chủ</router-link>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Cửa hàng</li>
          </ol>
        </nav>

        <!-- MAIN CONTENT: SPLIT LAYOUT -->
        <div class="jpm-split-layout mt-2" data-aos="fade-up" data-aos-delay="100">
          
          <!-- SIDEBAR -->
          <div class="jpm-split-sidebar mb-4 mb-lg-0">
            <div class="card sticky-top border-0 shadow-sm" style="border-radius: 16px; overflow: visible; top: 110px; z-index: 10;">
              <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                <h4 class="mb-0 jpm-card-title" style="font-size: 1rem;"><i class="bi bi-funnel-fill me-2" style="color: var(--accent-color);"></i>Bộ Lọc</h4>
                <button class="btn btn-sm btn-link text-danger text-decoration-none p-0 shadow-none fw-bold" @click="resetFilters">Xóa tất cả</button>
                </div>
                
                <div class="card-body p-4 custom-scrollbar" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                <!-- TIM KIEM -->
                <div class="mb-4 pb-4 border-bottom">
                  <h6 class="section-label mb-3 fw-bold" style="font-size: 0.85rem; color: var(--heading-color);"><i class="bi bi-search me-1 text-muted"></i> Tìm Sản Phẩm</h6>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 shadow-none" style="border-radius: 0 8px 8px 0; background-color: #fff;" placeholder="Nhập tên hoặc mã..." v-model="searchInput" @keyup.enter="performSearch">
                  </div>
                </div>
                <div class="mb-4">
                  <h6 class="section-label mb-3 fw-bold" style="font-size: 0.85rem; color: var(--heading-color);"><i class="bi bi-tags-fill me-1 text-muted"></i> Danh Mục</h6>
                  <div v-if="isLoadingCategories" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-secondary" style="color: var(--accent-color) !important;"></div>
                  </div>
                  <div v-else>
                    <div class="d-flex justify-content-between align-items-center mb-3 group-hover list-item-hover p-1 rounded" v-for="cat in categories" :key="cat.id">
                      <label class="form-check m-0 d-flex align-items-center flex-grow-1" style="cursor: pointer;">
                        <input class="form-check-input me-2 mt-0 custom-checkbox shadow-none" type="checkbox" :checked="selectedCategories.includes(cat.slug)" @change="selectCategory(cat.slug)" :id="'cat_'+cat.id" style="border-color: #d1d5db;">
                        <span class="text-secondary fw-medium" style="font-size: 0.95rem; text-transform: capitalize; transition: 0.2s;" :class="{'text-dark fw-bold': selectedCategories.includes(cat.slug)}">
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
              <div v-if="isLoadingGrouped" class="text-center py-5">
                <div class="spinner-border spinner-border-sm text-danger"></div>
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
                  <div class="card h-100 premium-product-card group-hover position-relative" style="border: 1px solid #e5e7eb; border-radius: 0; box-shadow: none;">
                    <div class="card-img-wrapper overflow-hidden img-zoom-hover" style="border-radius: 0;">
                      <img
                        :src="getImageUrl(prod.hinh_anh_chinh)"
                        class="card-img-top w-100 transition-transform"
                        style="aspect-ratio: 4/3; object-fit: cover; border-radius: 0;"
                        :alt="prod.ten_san_pham"
                        @error="$event.target.src='https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM'"
                      >
                      <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <button class="btn bg-white shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 4px;" title="Thêm vào giỏ" @click.prevent="addToCart(prod)">
                          <i class="bi bi-cart-plus" style="color: #dc3545; font-size: 1.1rem;"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body text-start p-3 d-flex flex-column bg-white">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ getCategoryName(prod.danh_muc_san_pham_id) || 'OEM' }}</span>
                      </div>
                      <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                        <router-link :to="`/chi-tiet-san-pham/${prod.slug}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ prod.ten_san_pham }}</router-link>
                      </h5>
                      <div class="mt-auto d-flex align-items-center contact-action" style="border-top: 1px solid #f1f5f9; padding-top: 0.75rem; justify-content: flex-end;">
                        <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                        <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ formatPrice(prod.gia_ban) }} đ</span>
                      </div>
                    </div>
                  </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-5 card border-0 shadow-sm mt-4">
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
              <div class="mb-4 border-bottom pb-2" v-else-if="searchKeyword">
                <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
                  Kết quả tìm kiếm cho: "{{ searchKeyword }}"
                </h4>
              </div>
              
              <!-- Loading Skeleton -->
              <div v-if="isLoadingProducts" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <div class="col" v-for="i in 8" :key="i">
                  <div class="card h-100 premium-product-card border-0 shadow-sm">
                    <div class="card-img-wrapper skeleton-box" style="aspect-ratio: 4/3;"></div>
                    <div class="card-body p-4">
                      <div class="skeleton-line" style="width: 60%; height: 12px; margin-bottom: 12px;"></div>
                      <div class="skeleton-line" style="width: 90%; height: 16px; margin-bottom: 8px;"></div>
                      <div class="skeleton-line" style="width: 75%; height: 16px; margin-bottom: 20px;"></div>
                      <div class="skeleton-line" style="width: 40%; height: 20px;"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Grid -->
              <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" v-else-if="products.length > 0">
                <div class="col" v-for="prod in products" :key="prod.id">
                  
                                                      <!-- PRODUCT CARD -->
                  <div class="card h-100 premium-product-card group-hover position-relative" style="border: 1px solid #e5e7eb; border-radius: 0; box-shadow: none;">
                    <div class="card-img-wrapper overflow-hidden img-zoom-hover" style="border-radius: 0;">
                      <img
                        :src="getImageUrl(prod.hinh_anh_chinh)"
                        class="card-img-top w-100 transition-transform"
                        style="aspect-ratio: 4/3; object-fit: cover; border-radius: 0;"
                        :alt="prod.ten_san_pham"
                        @error="$event.target.src='https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM'"
                      >
                      <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <button class="btn bg-white shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 4px;" title="Thêm vào giỏ" @click.prevent="addToCart(prod)">
                          <i class="bi bi-cart-plus" style="color: #dc3545; font-size: 1.1rem;"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body text-start p-3 d-flex flex-column bg-white">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ getCategoryName(prod.danh_muc_san_pham_id) || 'OEM' }}</span>
                      </div>
                      <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                        <router-link :to="`/chi-tiet-san-pham/${prod.slug}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ prod.ten_san_pham }}</router-link>
                      </h5>
                      <div class="mt-auto d-flex align-items-center contact-action" style="border-top: 1px solid #f1f5f9; padding-top: 0.75rem; justify-content: flex-end;">
                        <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                        <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ formatPrice(prod.gia_ban) }} đ</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Empty State -->
              <div class="text-center py-5 card border-0 p-5 shadow-sm mt-4" style="border-radius: 12px;" v-else>
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                <h5 class="mt-4 fw-bold text-uppercase" style="letter-spacing: 1px; color: var(--heading-color);">Không tìm thấy sản phẩm</h5>
                <p class="text-muted mt-2" style="font-weight: 300;">Rất tiếc, không có sản phẩm nào phù hợp với bộ lọc hiện tại của bạn.</p>
                <div>
                  <button class="btn btn-danger mt-3 px-5 py-2 rounded-pill text-uppercase fw-medium" style="letter-spacing: 1px;" @click="resetFilters">Xóa bộ lọc</button>
                </div>
              </div>
              
              <!-- Pagination -->
              <nav v-if="totalPages > 1" class="mt-5 pt-3">
                <ul class="pagination justify-content-center border-0 gap-2">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button class="page-link shadow-none border-0 rounded-circle text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #f8f9fa;" @click="currentPage--"><i class="bi bi-chevron-left"></i></button>
                  </li>
                  <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                    <button class="page-link shadow-none border-0 rounded-circle fw-bold d-flex align-items-center justify-content-center" :style="currentPage === page ? 'background-color: var(--accent-color); color: #fff; width: 40px; height: 40px;' : 'background-color: transparent; color: var(--default-color); width: 40px; height: 40px;'" @click="currentPage = page">{{ page }}</button>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <button class="page-link shadow-none border-0 rounded-circle text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #f8f9fa;" @click="currentPage++"><i class="bi bi-chevron-right"></i></button>
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

const getCategoryName = (slug) => {
  const cat = categories.value.find(c => c.slug === slug);
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
    cartStore.addItem(item, 1);
    toaster.success("Thêm sản phẩm thành công");
};

const scrollToProducts = () => {
  const el = document.getElementById('products-section');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};
</script>

<style scoped>
/* ── SECTION 1: HERO BANNER WITH SUBMERGED WATERMARK IMAGE ── */
.hero-editorial-section {
  padding-top: 30px;
  padding-bottom: 40px;
}

.hero-submerged-bg {
  position: absolute;
  top: 0;
  right: 0;
  width: 62%;
  height: 100%;
  background-image: url('https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=1600&auto=format&fit=crop&q=85');
  background-size: cover;
  background-position: center right;
  opacity: 0.75;
  mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.35) 28%, rgba(0, 0, 0, 0.85) 60%, rgba(0, 0, 0, 1) 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.35) 28%, rgba(0, 0, 0, 0.85) 60%, rgba(0, 0, 0, 1) 100%);
  pointer-events: none;
}

.hero-watermark-bg {
  position: absolute;
  top: 5%;
  right: 2%;
  width: 65%;
  height: 90%;
  background-image: radial-gradient(#d1d5db 1.5px, transparent 1.5px);
  background-size: 22px 22px;
  opacity: 0.45;
  pointer-events: none;
  border-radius: 40px;
}

.hero-main-heading {
  font-family: var(--heading-font);
  font-size: clamp(1.8rem, 2.6vw, 2.2rem);
  font-weight: 700;
  color: #111827;
  line-height: 1.25;
  letter-spacing: normal;
}

.hero-subtext {
  font-size: 0.95rem;
  line-height: 1.7;
  max-width: 480px;
}

.btn-dark-pill {
  background-color: #27272a;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.88rem;
  border-radius: 50rem;
  border: none;
  transition: all 0.25s ease;
}
.btn-dark-pill:hover {
  background-color: #f82249;
  color: #ffffff;
  transform: translateY(-2px);
}
</style>

<style>
.premium-product-card:hover .action-overlay {
  opacity: 1 !important;
  transform: translateX(0) !important;
}
</style>






