<template>
  <div class="jpm-homepage">
    <!-- ========================================================
         HERO SECTION: Video nền du thuyền & Giới thiệu nhanh
         ======================================================== -->
    <section id="hero" class="hero section dark-background">
      <!-- Video Background (YouTube) -->
      <div class="hero-video-wrap">
        <iframe 
          :src="youtubeEmbedUrl" 
          frameborder="0" 
          allow="autoplay; encrypted-media" 
          class="hero-video-iframe"
        ></iframe>
        <!-- Gradient bên trái -->
        <div class="hero-video-gradient"></div>
      </div>

      <div class="container flex-grow-1 d-flex align-items-center position-relative z-3">
        <div class="row w-100">
          <div class="col-lg-10 text-start">
            <h1 data-aos="fade-up" data-aos-delay="100" class="hero-main-title">
              <span class="hero-title-accent">{{ settings.homepage_hero_title || 'JPM MARINE' }}</span>
              <span class="hero-title-white">{{ settings.homepage_hero_subtitle || 'PHỤ TÙNG TÀU THUYỀN NHẬT BẢN' }}</span>
            </h1>

            <p data-aos="fade-up" data-aos-delay="150" class="hero-subtitle">
              Đồng hành cùng mọi chuyến ra khơi. Cung cấp động cơ, hệ thống lái & thiết bị tàu thủy đạt chuẩn quốc tế.
            </p>

            <div data-aos="fade-up" data-aos-delay="200">
              <router-link to="/tra-cuu" class="btn btn-hero-cta text-white d-inline-flex align-items-center justify-content-center">
                Tra Cứu Phụ Tùng
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- About Info Banner -->
      <div class="about-info mt-auto position-relative">
        <div class="container position-relative" data-aos="fade-up">
          <div class="row">
            <div class="col-lg-6">
              <h2>Giới Thiệu Chung</h2>
              <p>
                JPM Marine tự hào là nhà phân phối thiết bị và phụ kiện hàng hải uy tín. Chúng tôi cam kết mang đến các sản phẩm chính hãng, bền bỉ và tối ưu hiệu suất từ những thương hiệu hàng đầu thế giới, đảm bảo sự an toàn tuyệt đối cho phương tiện của bạn.
              </p>
            </div>
            <div class="col-lg-3">
              <h3>Trụ sở chính</h3>
              <p>123 Đường Bạch Đằng,<br />Quận Bình Thạnh, TP.HCM</p>
            </div>
            <div class="col-lg-3">
              <h3>Thời gian phục vụ</h3>
              <p>Thứ Hai - Thứ Bảy<br />08:00 Sáng - 17:30 Chiều</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 1: TRA CỨU PHỤ TÙNG NHANH (Bộ lọc động cơ thông minh)
         ======================================================== -->
    <section id="search-parts" class="search-parts-section section home-section-slate position-relative z-3">
      <div class="container" data-aos="fade-up">
        <!-- Tiêu đề Section chuẩn Editorial Luxury -->
        <div class="section-title" data-aos="fade-up">
          <h2>Tra cứu phụ tùng nhanh</h2>
          <p>Chọn thông số phương tiện hoặc nhập mã để tra cứu chính xác phụ tùng cần thiết</p>
        </div>

        <!-- Khung Tra Cứu Sang Trọng, Thống Nhất Chuẩn Editorial Luxury -->
        <div class="card filter-console-card position-relative" data-aos="fade-up" data-aos-delay="100">
          <!-- 1. Hàng 4 Dropdown Lọc Động Cơ (4 cột 3-3-3-3 chuẩn trục dọc) -->
          <div class="row g-3 mb-3">
            <!-- Cột 1: Thương Hiệu -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Thương hiệu"
                v-model="selectedBrand"
                :options="brands || []"
                placeholder="Chọn thương hiệu"
                all-option-label="Tất cả thương hiệu"
                value-key="id"
                label-key="name"
              />
            </div>

            <!-- Cột 2: Loại Động Cơ -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Loại động cơ"
                v-model="selectedEngineType"
                :options="engineTypes || []"
                placeholder="Chọn loại động cơ"
                all-option-label="Tất cả loại động cơ"
                value-key="id"
                label-key="ten_loai_dong_co"
                :disabled="!selectedBrand"
              />
            </div>

            <!-- Cột 3: Năm Sản Xuất -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Năm sản xuất"
                v-model="selectedYear"
                :options="years || []"
                placeholder="Chọn năm sản xuất"
                all-option-label="Tất cả các năm"
                :disabled="!selectedBrand"
              />
            </div>

            <!-- Cột 4: Model Động Cơ -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Model động cơ"
                v-model="selectedModel"
                :options="engineModels || []"
                placeholder="Chọn model động cơ"
                all-option-label="Tất cả model"
                value-key="id"
                label-key="ten_model"
                :disabled="!selectedBrand"
                :searchable="true"
              />
            </div>
          </div>

          <!-- 2. Hàng Tìm Kiếm Theo Mã & Cụm Nút Thao Tác (Khớp thẳng trục dọc 9 - 3 với 4 cột trên) -->
          <div class="row g-3 align-items-end pt-1">
            <!-- Ô Tìm kiếm mã (Khớp đúng độ rộng 3 cột đầu trên màn Desktop) -->
            <div class="col-12 col-lg-8 col-xl-9">
              <label class="filter-label mb-2">
                Tìm kiếm theo mã <span class="filter-label-optional ms-1">(Tùy chọn)</span>
              </label>
              <div class="search-input-box">
                <i class="bi bi-search search-input-icon"></i>
                <input
                  type="text"
                  class="search-input-field"
                  placeholder="Nhập mã phụ tùng OEM hoặc model động cơ (VD: F250TUR, 688-45941-01...)"
                  v-model="searchKeyword"
                  @keyup.enter="searchParts"
                />
              </div>
            </div>

            <!-- Cụm Nút Thao Tác (Dùng 100% class Bootstrap 5 chuẩn ăn theo main.css toàn Client) -->
            <div class="col-12 col-lg-4 col-xl-3">
              <div class="d-flex gap-2">
                <button
                  type="button"
                  class="btn btn-light filter-control-h px-3 flex-shrink-0 text-nowrap"
                  @click="resetSearch"
                  title="Đặt lại bộ lọc"
                >
                  Đặt lại
                </button>
                <button
                  type="button"
                  class="btn btn-danger filter-control-h px-4 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2 text-nowrap"
                  @click="searchParts"
                >
                  <i class="bi bi-search"></i>
                  <span>Tra cứu ngay</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 3. Hàng Từ Khóa Gợi Ý (Không vạch đứt, không icon tia sét, đồng bộ bảng màu Slate & Navy) -->
          <div class="popular-tags-row">
            <span class="popular-tags-label">Tra cứu nhiều:</span>
            <div class="popular-tags-list">
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('F250TUR')">Yamaha F250TUR</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('300HP')">Mercury 300HP V8</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('Bottom Cowling')">Bottom Cowling</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('Accent Panel')">Accent Panel</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('máy phát')">Máy phát Alternator</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('8M0142205')">8M0142205</a>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 2: PHỤ TÙNG THEO THƯƠNG HIỆU (Logo nhà sản xuất chính hãng)
         ======================================================== -->
    <section id="sponsors" class="sponsors section bg-white">
      <div class="container section-title" data-aos="fade-up">
        <h2>Phụ tùng theo thương hiệu</h2>
        <p>Đa dạng các loại phụ tùng chính hãng từ những thương hiệu hàng đầu thế giới</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-0 clients-wrap">
          <!-- Skeleton loading -->
          <template v-if="isLoadingBrands">
            <div class="col-6 col-md-4 col-xl-3 client-logo" v-for="n in 8" :key="'skel-' + n">
              <div class="d-flex align-items-center justify-content-center w-100 h-100">
                <div class="skeleton-box skeleton-box-brand"></div>
              </div>
            </div>
          </template>

          <!-- Dynamic brands from API -->
          <template v-else-if="brands && brands.length > 0">
            <div class="col-6 col-md-4 col-xl-3 client-logo" v-for="brand in brands" :key="brand.id">
              <router-link :to="`/thuong-hieu/${brand.code ? brand.code.toLowerCase() : ''}`" class="d-flex align-items-center justify-content-center w-100 h-100">
                <img
                  :src="brand.logo_path ? (brand.logo_path.startsWith('http') || brand.logo_path.startsWith('/client/') ? brand.logo_path : getImageUrl(brand.logo_path.startsWith('storage/') ? brand.logo_path : 'storage/' + brand.logo_path)) : ''"
                  :alt="brand.name"
                  class="img-fluid"
                  @error="e => e.target.style.display='none'"
                />
              </router-link>
            </div>
          </template>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 3: SẢN PHẨM BÁN CHẠY (Linh kiện & phụ tùng tiêu biểu)
         ======================================================== -->
    <section class="products-section section home-section-slate">
      <div class="container section-title" data-aos="fade-up">
        <h2>Sản phẩm bán chạy</h2>
        <p>Những linh kiện phụ tùng được khách hàng tin dùng nhiều nhất tại JPM Marine</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
          <!-- Skeleton khi đang load (8 sản phẩm = 2 hàng) -->
          <template v-if="!hotProducts || isLoadingHotProducts">
            <div class="col" v-for="n in 8" :key="'sk-' + n">
              <ProductCard :loading="true" />
            </div>
          </template>
          <!-- Data thật -->
          <template v-else>
            <div class="col" v-for="prod in hotProducts" :key="prod.id">
              <ProductCard :product="prod" @add-to-cart="addToCart" />
            </div>
          </template>
        </div>

        <div class="text-center mt-5">
          <router-link to="/san-pham" class="section-more-link">
            Xem tất cả sản phẩm <i class="bi bi-arrow-right ms-2"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 4: BANNER QUẢNG BÁ FULL MÀN (home_banner.png nguyên bản)
         ======================================================== -->
    <section class="home-full-banner">
      <div class="container py-4 py-lg-5">
        <div class="row align-items-center">
          <div class="col-lg-5 col-md-7">
            <h2 class="home-banner-title mb-3">
              Sẵn Sàng Cho Mọi Hải Trình <br />
              <span class="text-danger">Vững Bền Cùng Sóng Gió</span>
            </h2>
            <p class="home-banner-desc mb-4">
              Cung cấp động cơ và phụ tùng tàu thuyền chính hãng Yamaha, Honda Marine, Suzuki, Yanmar. Đảm bảo an toàn tối đa và tối ưu hiệu suất cho từng chuyến ra khơi.
            </p>
            <div class="d-flex flex-wrap gap-3">
              <router-link to="/san-pham" class="btn btn-danger px-4 py-2 fw-semibold d-inline-flex align-items-center">
                Khám Phá Cửa Hàng <i class="bi bi-arrow-right ms-2"></i>
              </router-link>
              <router-link to="/tra-cuu" class="btn btn-outline-dark px-4 py-2 fw-semibold d-inline-flex align-items-center">
                <i class="bi bi-search me-2"></i> Tra Cứu Phụ Tùng
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 5: TIN TỨC & KINH NGHIỆM (Cẩm nang kỹ thuật hàng hải)
         ======================================================== -->
    <section class="news-section section bg-white">
      <div class="container section-title" data-aos="fade-up">
        <h2>Tin tức & Kinh nghiệm</h2>
        <p>Cập nhật kiến thức bảo dưỡng và tin tức mới nhất về thiết bị tàu thuyền</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gx-lg-5 gy-4">

          <!-- Bài nổi bật (cột trái 5 phần - Dành riêng cho bài viết có Video) -->
          <div class="col-lg-5">
            <!-- Skeleton -->
            <div v-if="!videoArticle" class="d-flex flex-column h-100">
              <div class="skeleton-box skeleton-box-full mb-3" style="aspect-ratio: 16/10; border-radius: 12px !important;"></div>
              <div class="px-1">
                <div class="skeleton-line mb-2 w-50"></div>
                <div class="skeleton-line mb-2 w-100"></div>
                <div class="skeleton-line mb-3 w-75"></div>
                <div class="skeleton-line w-25"></div>
              </div>
            </div>
            <!-- Data thật: Bài viết có video -->
            <div v-else class="d-flex flex-column h-100 group-hover p-2 position-relative">
              <!-- Khung Thumbnail có nút Play Video -->
              <div
                class="overflow-hidden mb-3 position-relative img-zoom-hover video-thumb-wrap"
                @click="openVideoModal(videoArticle)"
                title="Bấm để xem video"
              >
                <img
                  :src="getImageUrl(videoArticle.thumbnail) || 'https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                  class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                  :alt="videoArticle.title"
                  @error="$event.target.src='https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                >
                <!-- Badge góc: VIDEO (Nền kính mờ Deep Navy chuẩn Luxury) -->
                <span class="position-absolute top-0 start-0 m-3 d-inline-flex align-items-center gap-1 px-2 py-1 video-badge">
                  <i class="bi bi-play-fill"></i> VIDEO
                </span>

                <!-- Icon Play Video tròn ở chính giữa ảnh -->
                <div class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center video-play-btn shadow">
                  <i class="bi bi-play-fill text-white fs-2 play-icon"></i>
                </div>
              </div>

              <!-- Nội dung thông tin bài viết -->
              <div class="d-flex flex-column flex-grow-1 px-1 position-relative">
                <!-- 1. Danh mục: Xám xanh dịu #8392a5 đồng bộ Product Category bên trên -->
                <div class="news-category mb-1 text-truncate">
                  {{ videoArticle.category || videoArticle.type || videoArticle.loai || 'CẨM NANG HÀNG HẢI' }}
                </div>
                <!-- 2. Tiêu đề: Deep Navy #0A1128 sắc nét -->
                <h5 class="news-hero-title mb-2">
                  <router-link :to="`/tin-tuc/${videoArticle.slug || videoArticle.id}`" class="stretched-link">
                    {{ videoArticle.title }}
                  </router-link>
                </h5>
                <!-- 3. Mô tả tóm tắt: Cool Slate #64748b trong trẻo -->
                <p class="news-summary news-summary-lg mb-3 text-truncate-3">{{ videoArticle.summary }}</p>
                <!-- 4. Ngày đăng: Mảnh nhẹ chuẩn Editorial -->
                <div class="mt-auto news-meta">
                  <i class="bi bi-calendar3 me-1"></i>{{ formatDate(videoArticle.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Danh sách bài viết khác (cột phải 7 phần) -->
          <div class="col-lg-7">
            <!-- Skeleton -->
            <div v-if="!sideNews || sideNews.length === 0" class="d-flex flex-column justify-content-between gap-3 h-100">
              <div v-for="n in 3" :key="'nsk-'+n" class="d-flex align-items-start p-2">
                <div class="skeleton-box skeleton-box-sm flex-shrink-0"></div>
                <div class="ms-3 flex-grow-1">
                  <div class="skeleton-line mb-2 w-25" style="height: 12px;"></div>
                  <div class="skeleton-line mb-2 w-75" style="height: 16px;"></div>
                  <div class="skeleton-line mb-2 w-100" style="height: 13px;"></div>
                  <div class="skeleton-line w-50" style="height: 12px;"></div>
                </div>
              </div>
            </div>
            <!-- Data thật: 3 bài viết khác (Không dùng border-bottom cong mép) -->
            <div v-else class="d-flex flex-column justify-content-between gap-3 h-100">
              <div
                v-for="item in sideNews"
                :key="item.id"
                class="d-flex align-items-start position-relative list-item-hover p-2"
              >
                <div class="overflow-hidden img-zoom-hover news-img-thumb flex-shrink-0">
                  <img
                    :src="getImageUrl(item.thumbnail) || 'https://placehold.co/400x300/f1f3f5/a1acb8?text=JPM'"
                    class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                    :alt="item.title"
                    @error="$event.target.src='https://placehold.co/400x300/f1f3f5/a1acb8?text=JPM'"
                  >
                </div>
                <div class="ms-3 flex-grow-1 d-flex flex-column align-self-stretch">
                  <!-- 1. Danh mục: Xám xanh dịu #8392a5 đồng bộ Product Category -->
                  <div class="news-category mb-1 text-truncate">
                    {{ item.category || item.type || item.loai || 'TIN TỨC' }}
                  </div>
                  <!-- 2. Tiêu đề: Deep Navy #0A1128 -->
                  <h6 class="news-card-title mb-1 text-truncate-2">
                    <router-link :to="`/tin-tuc/${item.slug || item.id}`" class="stretched-link">
                      {{ item.title }}
                    </router-link>
                  </h6>
                  <!-- 3. Nội dung tóm tắt: Cool Slate #64748b -->
                  <p class="news-summary mb-2 text-truncate-2">
                    {{ item.summary }}
                  </p>
                  <!-- 4. Ngày đăng: Mảnh nhẹ chuẩn Editorial -->
                  <div class="mt-auto news-meta">
                    <i class="bi bi-calendar3 me-1"></i>{{ formatDate(item.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-5">
          <router-link to="/tin-tuc" class="section-more-link">
            Xem tất cả bài viết <i class="bi bi-arrow-right ms-2"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Modal xem Video YouTube cho bài viết Trang chủ -->
    <div
      class="modal fade"
      id="homeNewsVideoModal"
      tabindex="-1"
      aria-labelledby="homeNewsVideoModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0 shadow-lg overflow-hidden text-white" style="border-radius: var(--jpm-radius-modal, 12px) !important;">
          <div class="modal-header border-0 pb-0 pt-3 px-3 d-flex justify-content-between align-items-center">
            <h6 class="modal-title fw-semibold text-truncate pe-3" id="homeNewsVideoModalLabel">
              <i class="bi bi-play-circle-fill text-danger me-2"></i>{{ activeVideoItem?.title }}
            </h6>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="closeVideoModal"
            ></button>
          </div>
          <div class="modal-body p-2 p-md-3">
            <div class="ratio ratio-16x9 overflow-hidden bg-black shadow" style="border-radius: var(--jpm-radius-control, 8px);">
              <iframe
                v-if="activeVideoItem && activeVideoEmbedUrl"
                :src="activeVideoEmbedUrl"
                title="JPM Marine Video Player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useCartStore } from '../../../stores/cartStore';
import { useSiteSettings } from '../../../composables/useSiteSettings';
import ProductCard from '../../../components/client/ProductCard.vue';
import JpmSelect from '../../../components/client/JpmSelect.vue';

export default {
  name: "TrangChu",
  components: {
    ProductCard,
    JpmSelect
  },
  setup() {
    // ===== 1. KHỞI TẠO STATE, PINIA STORES & SITE SETTINGS =====
    const { settings } = useSiteSettings();
    const cartStore = useCartStore(); 
    const selectedBrand = ref("");
    const selectedEngineType = ref("");
    const selectedYear = ref("");
    const selectedModel = ref("");

    const youtubeEmbedUrl = computed(() => {
      const videoId = settings.value?.homepage_youtube_id || '5CVzOqvqI38';
      return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1&disablekb=1&fs=0&iv_load_policy=3`;
    });

    // ===== 2. TRUY VẤN DỮ LIỆU TỪ BACKEND (VUE QUERY SWR CACHING) =====
    // Lấy danh sách thương hiệu động cơ
    const { data: brands, isLoading: isLoadingBrands } = useQuery({
      queryKey: ['public-brands-v4'],
      queryFn: () => axiosClient.get('/public/thuong-hieu?_t=' + new Date().getTime()).then(res => res.data?.data || res.data || []),
      staleTime: 1000 * 60 * 30
    });

    const { data: engineTypes } = useQuery({
      queryKey: computed(() => ['public-engine-types', selectedBrand.value]),
      queryFn: () => axiosClient.get('/public/loai-dong-co', { params: { thuong_hieu_id: selectedBrand.value } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!selectedBrand.value)
    });

    const { data: engineModels } = useQuery({
      queryKey: computed(() => ['public-models-v6', selectedBrand.value, selectedEngineType.value, selectedYear.value]),
      queryFn: () => axiosClient.get('/public/dong-co-model', { params: { thuong_hieu_id: selectedBrand.value, loai_dong_co_id: selectedEngineType.value, nam_sx: selectedYear.value } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!selectedBrand.value)
    });

    const { data: years } = useQuery({
      queryKey: computed(() => ['public-years-v6', selectedBrand.value, selectedEngineType.value]),
      queryFn: () => axiosClient.get('/public/nam-san-xuat', { params: { thuong_hieu_id: selectedBrand.value, loai_dong_co_id: selectedEngineType.value } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!selectedBrand.value)
    });

    // Lấy 8 sản phẩm bán chạy nhất (2 hàng x 4 cột)
    const { data: hotProducts, isLoading: isLoadingHotProducts } = useQuery({
      queryKey: ['public-hot-products', 8],
      queryFn: () => axiosClient.get('/public/san-pham-ban-chay', { params: { limit: 8 } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 5
    });

    // Lấy bài viết kỹ thuật & cẩm nang mới nhất (lấy 8 bài để chọn lọc bài có video cho cột trái và 3 bài cho cột phải)
    const { data: latestNews, isLoading: isLoadingNews } = useQuery({
      queryKey: ['public-latest-news', 8],
      queryFn: () => axiosClient.get('/public/tin-tuc-moi-nhat', { params: { limit: 8 } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 10
    });

    // 1. Cột trái: Dành riêng cho 1 bài viết có Video
    const videoArticle = computed(() => {
      if (!latestNews.value || !Array.isArray(latestNews.value) || latestNews.value.length === 0) return null;
      // Ưu tiên bài viết có youtube_url
      const withVideo = latestNews.value.find(item => item.youtube_url && item.youtube_url.trim() !== '');
      if (withVideo) {
        return {
          ...withVideo,
          hasVideo: true,
        };
      }
      // Fallback: nếu chưa có bài nào có video thì gắn video mẫu cho bài đầu tiên
      return {
        ...latestNews.value[0],
        youtube_url: 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
        hasVideo: true,
      };
    });

    // 2. Cột phải: 3 bài viết thông thường mới nhất (loại bỏ bài video cột trái để tránh trùng lặp)
    const sideNews = computed(() => {
      if (!latestNews.value || !Array.isArray(latestNews.value)) return [];
      const videoId = videoArticle.value?.id;
      return latestNews.value.filter(item => item.id !== videoId).slice(0, 3);
    });

    // ===== 3. QUẢN LÝ POPUP MODAL XEM VIDEO YOUTUBE BÀI VIẾT =====
    const activeVideoItem = ref(null);

    const extractYoutubeId = (url) => {
      if (!url) return null;
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&]{11})/);
      return match ? match[1] : null;
    };

    const activeVideoEmbedUrl = computed(() => {
      if (!activeVideoItem.value?.youtube_url) return null;
      const id = extractYoutubeId(activeVideoItem.value.youtube_url);
      return id ? `https://www.youtube.com/embed/${id}?autoplay=1&rel=0` : null;
    });

    const openVideoModal = (item) => {
      if (!item) return;
      activeVideoItem.value = item;
      const el = document.getElementById('homeNewsVideoModal');
      if (el && window.bootstrap?.Modal) {
        let modalInstance = window.bootstrap.Modal.getInstance(el);
        if (!modalInstance) {
          modalInstance = new window.bootstrap.Modal(el);
        }
        modalInstance.show();
      }
    };

    const closeVideoModal = () => {
      activeVideoItem.value = null;
    };

    onMounted(() => {
      const el = document.getElementById('homeNewsVideoModal');
      if (el) {
        el.addEventListener('hidden.bs.modal', closeVideoModal);
      }
    });

    onUnmounted(() => {
      const el = document.getElementById('homeNewsVideoModal');
      if (el) {
        el.removeEventListener('hidden.bs.modal', closeVideoModal);
      }
    });

    // ===== 4. THEO DÕI THAY ĐỔI ĐỂ TỰ ĐỘNG RESET BỘ LỌC LIÊN ĐỚI (WATCHERS) =====
    watch(selectedBrand, () => { 
      selectedEngineType.value = ""; 
      selectedYear.value = ""; 
      selectedModel.value = ""; 
    });

    watch(selectedEngineType, () => {
      selectedYear.value = "";
      selectedModel.value = "";
    });
    
    watch(selectedYear, () => {
      selectedModel.value = "";
    });

    const isPageLoading = computed(() => isLoadingBrands.value || isLoadingHotProducts.value || isLoadingNews.value);

    const formatDate = (dateStr) => {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    };

    return {
      brands,
      engineTypes,
      engineModels,
      years,
      selectedBrand,
      selectedEngineType,
      selectedYear,
      selectedModel,
      hotProducts,
      latestNews,
      videoArticle,
      sideNews,
      activeVideoItem,
      activeVideoEmbedUrl,
      openVideoModal,
      closeVideoModal,
      formatDate,
      isPageLoading,
      isLoadingBrands, 
      cartStore,
      settings,
      youtubeEmbedUrl,
    };
  },
  data() {
    return {
      searchKeyword: "",
    };
  },
  // ===== 5. CÁC HÀM XỬ LÝ SỰ KIỆN & ĐIỀU HƯỚNG (METHODS) =====
  methods: {
    getImageUrl(path) {
      if (!path) return "";
      if (path.startsWith("blob:") || path.startsWith("http")) return path;
      const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
      return `${baseUrl}${path.startsWith("/") ? "" : "/"}${path}`;
    },
    searchParts() {
      if (!this.searchKeyword && !this.selectedBrand && !this.selectedEngineType && !this.selectedYear && !this.selectedModel) {
        this.$toast.error("Vui lòng nhập từ khóa hoặc chọn ít nhất 1 điều kiện để tìm kiếm!");
        return;
      }
      this.$router.push({
        path: '/tra-cuu',
        query: {
          keyword: this.searchKeyword,
          brand: this.selectedBrand,
          engineType: this.selectedEngineType,
          year: this.selectedYear,
          model: this.selectedModel
        }
      });
    },
    resetSearch() {
      this.searchKeyword = "";
      this.selectedBrand = "";
      this.selectedEngineType = "";
      this.selectedYear = "";
      this.selectedModel = "";
    },
    quickSearch(keyword) {
      this.searchKeyword = keyword;
      this.searchParts();
    },
    goToCategory(cat) {
      this.$router.push('/danh-sach-phu-tung');
    },
    addToCart(prod) {
      this.cartStore.addItem(prod, 1);
      this.$toast.success("Thêm sản phẩm thành công");
    }
  }
};
</script>

<style scoped>
/* ==============================================================
   TrangChu: Tinh chỉnh cục bộ cho khối Tin tức & Kinh nghiệm
   Đồng bộ bảng màu Luxury (Deep Navy #0A1128 & Cool Slate) với các phần trên
   ============================================================== */
.news-category {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: #f82249; /* Đỏ Marine Red thương hiệu JPM */
  font-weight: 700;
  font-size: 0.75rem;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  line-height: 1.2;
}

.news-hero-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.3rem;
  color: var(--heading-color, #0A1128);
  font-weight: 700;
  line-height: 1.35;
  letter-spacing: -0.2px;
}

.news-card-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1rem;
  color: var(--heading-color, #0A1128);
  font-weight: 600;
  line-height: 1.42;
  margin-bottom: 0.25rem;
}

.news-hero-title a,
.news-card-title a {
  color: var(--heading-color, #0A1128) !important;
  text-decoration: none;
}
.group-hover:hover .news-hero-title a,
.list-item-hover:hover .news-card-title a,
.news-hero-title a:hover,
.news-card-title a:hover {
  color: var(--heading-color, #0A1128) !important;
}

.news-summary {
  font-family: var(--default-font);
  color: #64748b; /* Cool Slate trong trẻo thay cho #6c757d xỉn màu */
  font-size: 0.85rem;
  font-weight: 400;
  line-height: 1.55;
}
.news-summary-lg {
  font-size: 0.9rem;
}

.news-meta {
  font-family: var(--default-font);
  color: #64748b; /* Xám Slate vừa vặn, sắc nét trên nền trắng */
  font-size: 0.8rem;
  font-weight: 500;
  line-height: 1.3;
  display: inline-flex;
  align-items: center;
}
.news-meta i {
  color: inherit;
  font-size: 0.82rem;
}

.section-more-link {
  color: var(--heading-color, #0A1128);
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: color 0.2s ease;
}
.section-more-link i {
  transition: transform 0.2s ease;
}
.section-more-link:hover {
  color: var(--accent-color, #f82249);
}
.section-more-link:hover i {
  transform: translateX(4px);
}

.news-img-thumb {
  width: 175px;
  aspect-ratio: 4/3;
  flex-shrink: 0;
  border-radius: 12px;
}

.skeleton-box-sm {
  border-radius: 12px !important;
  width: 175px;
  aspect-ratio: 4/3;
}

@media (max-width: 576px) {
  .news-img-thumb,
  .skeleton-box-sm {
    width: 120px;
  }
}

/* ==============================================================
   Icon & Badge Video trên thumbnail bài viết cột trái
   ============================================================== */
.video-thumb-wrap {
  cursor: pointer;
  border-radius: 12px;
  aspect-ratio: 16/10;
}
.video-badge {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.6px;
  color: #ffffff;
  background-color: rgba(10, 17, 40, 0.78);
  border: 1px solid rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(6px);
  z-index: 2;
  border-radius: 6px;
}
.video-play-btn {
  width: 58px;
  height: 58px;
  background-color: rgba(10, 17, 40, 0.75);
  color: #ffffff;
  border: 2px solid rgba(255, 255, 255, 0.85);
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  z-index: 2;
  backdrop-filter: blur(4px);
}
.video-play-btn .play-icon {
  margin-left: 3px;
  line-height: 1;
}
.group-hover:hover .video-play-btn,
.video-thumb-wrap:hover .video-play-btn {
  background-color: var(--accent-color, #f82249);
  border-color: #ffffff;
  transform: translate(-50%, -50%) scale(1.15) !important;
  box-shadow: 0 0 25px rgba(248, 34, 73, 0.55);
}

/* ==============================================================
   Khối 1: Tra Cứu Phụ Tùng Nhanh (Smart Filter Console)
   Thống nhất 100% phong cách TheEvent, màu Navy & Red thương hiệu
   ============================================================== */
.jpm-homepage .section:not(.hero) {
  padding: 64px 0;
}

.home-section-slate {
  background-color: #f8fafc;
}

.filter-console-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(10, 17, 40, 0.05);
  padding: 32px 36px;
  transition: box-shadow 0.3s ease;
  overflow: visible !important;
  position: relative;
  z-index: 20;
}

.filter-console-card:hover {
  box-shadow: 0 14px 40px rgba(10, 17, 40, 0.08);
}

.filter-console-card .filter-label {
  display: inline-flex;
  align-items: center;
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--heading-color, #0A1128);
  letter-spacing: 0.1px;
}

.filter-label-optional {
  font-weight: 400;
  color: #94a3b8;
  font-size: 0.78rem;
}

/* Search Input Box (Sneat-style Bold 2px Crisp Focus & Downward Soft Shadow, Zero Jitter) */
.search-input-box {
  display: flex;
  align-items: center;
  height: 46px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: none;
  padding: 0 16px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.search-input-box:focus-within {
  border-width: 1px !important;
  border-color: var(--accent-color, #f82249) !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: 0 !important;
}
.search-input-box:hover {
  border-color: #cbd5e1;
}
.search-input-icon {
  font-size: 1rem;
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
}
.search-input-field::placeholder {
  color: #94a3b8;
  transition: all 0.25s ease;
}
.search-input-field:focus::placeholder {
  transform: translate(5px);
  transition: all 0.25s ease;
}

/* Đồng bộ chiều cao nút với ô input 46px trong bộ lọc (Màu sắc, bo góc, font lấy 100% từ .btn trong main.css) */
.filter-control-h {
  height: 46px;
}

/* Bố cục hàng Từ Khóa Gợi Ý (Style .popular-tags-label & .quick-pill-tag dùng chung tại main.css) */
.popular-tags-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 18px;
  padding-top: 4px;
}
.popular-tags-list {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

@media (max-width: 991px) {
  .filter-console-card {
    padding: 24px 20px;
  }
}

/* ==============================================================
   Khối 4: Banner Quảng Bá Full Màn (home_banner.png nguyên bản 100%)
   ============================================================== */
.home-full-banner {
  width: 100%;
  aspect-ratio: 3 / 1;
  min-height: 420px;
  background-image: url('/client/image/home_banner.png');
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  position: relative;
}

.home-banner-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 2.1rem;
  font-weight: 800;
  line-height: 1.25;
  color: var(--heading-color, #0A1128);
  letter-spacing: -0.4px;
}

.home-banner-desc {
  font-family: var(--default-font);
  font-size: 0.95rem;
  font-weight: 400;
  line-height: 1.6;
  color: #334155;
  max-width: 480px;
}

@media (max-width: 991px) {
  .home-full-banner {
    min-height: 380px;
    aspect-ratio: auto;
    padding: 50px 0;
  }
  .home-banner-title {
    font-size: 1.7rem;
  }
}

@media (max-width: 576px) {
  .home-banner-title {
    font-size: 1.35rem;
  }
  .home-banner-desc {
    font-size: 0.88rem;
  }
}
</style>
