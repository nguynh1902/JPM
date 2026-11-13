<template>
  <div class="news-wireframe-page bg-white">

    <!-- Header chuẩn Giỏ Hàng & JPM Kit -->
    <section class="section pb-2 inner-page-section">
      <div class="container">
        <div class="row mb-3">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">Tin Tức <span style="color: var(--jpm-color-red-marine, #F82249);">&amp; Cẩm Nang</span></h2>
            <p class="text-muted">Tổng hợp những kiến thức chuyên sâu về vận hành tàu thuyền, cẩm nang bảo dưỡng động cơ thủy và xu hướng công nghệ hàng hải.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION: TIÊU ĐIỂM CHUYÊN MỤC
         Toolbar (Title + Search & Action) + Dedicated Tabs Row + 4 Landscape Cards
    ════════════════════════════════════════════════════════════════ -->
    <section id="top-destinations" class="pb-5">
      <div class="container">
        <!-- Header Toolbar: Tiêu Đề bên trái + Ô Tìm Kiếm bên phải (Chuẩn 40px, 8px radius) -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
          <div>
            <h2 class="section-editorial-title m-0">Tiêu Điểm Chuyên Mục</h2>
            <p class="text-muted small mb-0 mt-1">Lọc bài viết theo danh mục kỹ thuật và công nghệ hàng hải</p>
          </div>

          <!-- Quick Search Input (Chuẩn hóa search-input-box 40px) -->
          <div class="news-search-box flex-shrink-0">
            <i class="bi bi-search search-input-icon"></i>
            <input
              type="text"
              v-model.trim="searchQuery"
              class="search-input-field"
              placeholder="Tìm kiếm bài viết..."
            />
            <button
              v-if="searchQuery"
              type="button"
              class="btn-clear-search"
              @click="searchQuery = ''"
              title="Xóa tìm kiếm"
            >
              <i class="bi bi-x-circle-fill"></i>
            </button>
          </div>
        </div>

        <!-- Dòng Tabs Danh Mục & Link Xem Thêm Chuẩn JPM Cửa Hàng (border-bottom pb-2) -->
        <div class="news-tabs-wrapper mb-4 border-bottom d-flex align-items-center justify-content-between">
          <nav class="wireframe-tabs d-flex align-items-center gap-1 overflow-x-auto text-nowrap scrollbar-hidden flex-grow-1">
            <button
              v-for="tab in tabs"
              :key="tab"
              type="button"
              class="wireframe-tab-btn"
              :class="{ active: activeTab === tab }"
              @click="selectTab(tab)"
            >
              {{ tab }}
            </button>
          </nav>

          <!-- Link Xem thêm chuẩn SanPham.vue ngay trên viền border-bottom -->
          <button
            v-if="topCategoryArticles.length > 4 && !searchQuery"
            type="button"
            class="btn btn-link text-danger fw-bold text-decoration-none d-flex align-items-center gap-1 p-0 shadow-none hover-underline flex-shrink-0 ms-3 pb-2"
            style="color: var(--jpm-color-red-marine, #F82249) !important; font-size: 0.92rem;"
            @click="showAllTopCategory = !showAllTopCategory"
          >
            {{ showAllTopCategory ? 'Thu gọn' : 'Xem thêm' }}
            <i class="bi" :class="showAllTopCategory ? 'bi-arrow-up' : 'bi-arrow-right'"></i>
          </button>
        </div>

        <!-- Empty State if search/tab has no results -->
        <div v-if="!isFetching && displayedTopCategoryArticles.length === 0" class="text-center py-5 bg-light rounded-3 my-2 border">
          <i class="bi bi-journal-text text-muted fs-1 d-block mb-2"></i>
          <p class="text-muted mb-3">Không tìm thấy bài viết nào khớp với bộ lọc hiện tại.</p>
          <button type="button" class="btn btn-outline-action" @click="resetFilters">Xem tất cả bài viết</button>
        </div>

        <!-- 4 Landscape Cards Grid (Tỉ lệ 16:10, Danh mục xám trên tiêu đề & Ngày đăng có icon) -->
        <div v-else class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
          <div class="col" v-for="item in displayedTopCategoryArticles" :key="item.id">
            <router-link :to="`/tin-tuc/${item.id}`" class="text-decoration-none d-block group-hover h-100">
              <div class="portrait-card-img position-relative overflow-hidden mb-2 bg-light shadow-xs">
                <img :src="getImageUrl(item.img)" :alt="item.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                <div v-if="item.hasVideo" class="play-badge-floating" title="Có video bài viết">
                  <i class="bi bi-play-fill"></i>
                </div>
              </div>
              <!-- Danh mục: Thanh chữ màu xám trên tên bài viết chuẩn card sản phẩm -->
              <div class="news-category-label mb-1 text-truncate" :title="item.cat">
                {{ item.cat }}
              </div>
              <h5 class="fw-bold mb-2 text-truncate-2 hero-title-hover" style="font-size: 0.98rem; line-height: 1.45; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ item.title }}
              </h5>
              <div class="text-muted d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ item.dateFormatted }}</span>
                <span>•</span>
                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-clock"></i>{{ item.read }}</span>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION: BÀI VIẾT MỚI NHẤT (2 HÀNG X 2 CỘT = 4 CARD LỚN)
         Bố cục 2 Card Lớn (Ảnh trên, Chữ dưới) chuẩn phong cách Editorial
    ════════════════════════════════════════════════════════════════ -->
    <section class="py-4 py-md-5" v-if="latestGridArticles.length > 0">
      <div class="container">
        <!-- Section Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h2 class="section-editorial-title m-0">Bài Viết Mới Nhất</h2>
            <p class="text-muted small mb-0 mt-1">Cập nhật tin tức, xu hướng công nghệ hàng hải và kiến thức vận hành mới nhất</p>
          </div>
        </div>

        <!-- 4 Large Cards Grid (2 Hàng x 2 Cột = 4 Card Lớn) -->
        <div class="row g-4 g-lg-5">
          <div
            v-for="item in latestGridArticles"
            :key="item.id"
            class="col-12 col-md-6"
          >
            <article class="middle-feature-card group-hover h-100 d-flex flex-column">
              <!-- 1. Ảnh lớn phía trên (16:9, bo góc tròn, zoom mượt mà) -->
              <router-link
                :to="`/tin-tuc/${item.id}`"
                class="d-block overflow-hidden rounded-3 position-relative middle-card-img-wrap mb-3 shadow-xs"
              >
                <img
                  :src="getImageUrl(item.img)"
                  :alt="item.title"
                  class="w-100 h-100 object-fit-cover transition-transform"
                  @error="onImgError"
                />
                <div
                  v-if="item.hasVideo"
                  class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn shadow"
                  @click.stop.prevent="openVideoModal(item)"
                  title="Kích xem video"
                >
                  <i class="bi bi-play-fill fs-3 ms-1"></i>
                </div>
              </router-link>

              <!-- 2. Danh mục chữ đỏ in hoa -->
              <div class="news-category-label mb-1 text-truncate" :title="item.cat">
                {{ item.cat }}
              </div>

              <!-- 3. Tiêu đề đậm lớn Deep Navy #0A1128 (Tối đa 2 dòng kèm dấu ...) -->
              <h3 class="fw-bold mb-2 hero-title-hover middle-card-title">
                <router-link :to="`/tin-tuc/${item.id}`" class="text-decoration-none">
                  {{ item.title }}
                </router-link>
              </h3>

              <!-- 4. Tóm tắt nội dung bài viết (Đưa lên trước ngày tháng) -->
              <p class="middle-card-excerpt mb-2 text-secondary text-truncate-2">
                {{ item.excerpt || 'Tổng hợp kiến thức kỹ thuật hàng hải, cẩm nang bảo dưỡng và thông tin phụ tùng động cơ thủy chính hãng từ JPM Marine.' }}
              </p>

              <!-- 5. Hàng ngày tháng & thời lượng đọc kèm icon (Đưa xuống dưới nội dung) -->
              <div class="text-muted d-flex align-items-center gap-2 mt-1" style="font-size: 0.82rem;">
                <span class="d-inline-flex align-items-center gap-1">
                  <i class="bi bi-calendar3"></i> {{ item.dateFormatted }}
                </span>
                <span>•</span>
                <span class="d-inline-flex align-items-center gap-1">
                  <i class="bi bi-clock"></i> {{ item.read }}
                </span>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION: BÀI VIẾT KHÁC
         1 Large Story Left + 3 Compact Horizontal Stories Right
    ════════════════════════════════════════════════════════════════ -->
    <section class="py-5 mb-5" v-if="otherMainStory">
      <div class="container">
        <!-- Header Row -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h2 class="section-editorial-title m-0">Bài Viết Khác</h2>
            <p class="text-muted small mb-0 mt-1">Khám phá thêm các cẩm nang kỹ thuật và kinh nghiệm thực tế từ chuyên gia JPM Marine</p>
          </div>
        </div>

        <div class="row g-4 gx-lg-5 align-items-stretch">
          <!-- Left: 1 Large Featured Story -->
          <div class="col-lg-6 d-flex flex-column">
            <router-link :to="`/tin-tuc/${otherMainStory.id}`" class="text-decoration-none d-block group-hover h-100 d-flex flex-column">
              <div class="latest-main-img position-relative rounded-3 overflow-hidden mb-3 bg-light shadow-xs">
                <img :src="getImageUrl(otherMainStory.img)" :alt="otherMainStory.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                <div
                  v-if="otherMainStory.hasVideo"
                  class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn shadow"
                  @click.stop.prevent="openVideoModal(otherMainStory)"
                  title="Kích để xem video"
                >
                  <i class="bi bi-play-fill fs-4 ms-1"></i>
                </div>
              </div>
              <div class="news-category-label mb-1 text-truncate" :title="otherMainStory.cat">
                {{ otherMainStory.cat }}
              </div>
              <h3 class="fw-bold mb-2 hero-title-hover" style="font-size: 1.35rem; line-height: 1.35; color: var(--jpm-color-navy-dark, #0A1128);">
                {{ otherMainStory.title }}
              </h3>
              <p class="text-secondary mb-2 text-truncate-2" style="font-size: 0.92rem; line-height: 1.6;">
                {{ otherMainStory.excerpt }}
              </p>
              <div class="text-muted d-flex align-items-center gap-2 mt-1" style="font-size: 0.78rem;">
                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ otherMainStory.dateFormatted }}</span>
                <span>•</span>
                <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-clock"></i>{{ otherMainStory.read }}</span>
              </div>
            </router-link>
          </div>

          <!-- Right: 3 Stacked Horizontal Cards (Bự ra & Cân xứng chiều cao) -->
          <div class="col-lg-6 d-flex flex-column justify-content-between h-100 gap-3 gap-lg-4">
            <router-link
              v-for="story in otherSideStories"
              :key="story.id"
              :to="`/tin-tuc/${story.id}`"
              class="d-flex gap-3 gap-md-4 align-items-center text-decoration-none group-hover other-side-item"
            >
              <!-- Thumbnail bự ra (16:10, bo góc tròn đẹp) -->
              <div class="latest-side-thumb position-relative rounded-3 overflow-hidden flex-shrink-0 bg-light shadow-xs">
                <img :src="getImageUrl(story.img)" :alt="story.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                <div
                  v-if="story.hasVideo"
                  class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn-sm shadow"
                  @click.stop.prevent="openVideoModal(story)"
                  title="Kích xem video"
                >
                  <i class="bi bi-play-fill fs-5"></i>
                </div>
              </div>

              <!-- Nội dung bài viết to rõ & có trích đoạn mô tả -->
              <div class="flex-grow-1 min-w-0">
                <div class="news-category-label mb-1 text-truncate" :title="story.cat">
                  {{ story.cat }}
                </div>
                <h5 class="fw-bold mb-1 text-truncate-2 hero-title-hover other-side-title">
                  {{ story.title }}
                </h5>
                <p class="text-secondary mb-2 text-truncate-2 other-side-excerpt">
                  {{ story.excerpt || 'Tổng hợp kiến thức kỹ thuật hàng hải, cẩm nang bảo dưỡng và thông tin phụ tùng từ JPM Marine.' }}
                </p>
                <div class="text-muted d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                  <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-calendar3"></i>{{ story.dateFormatted }}</span>
                  <span>•</span>
                  <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-clock"></i>{{ story.read }}</span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         VIDEO MODAL POPUP (Inline YouTube Player cho Card Video)
    ════════════════════════════════════════════════════════════════ -->
    <div
      class="modal fade"
      id="newsVideoModal"
      tabindex="-1"
      aria-labelledby="newsVideoModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden rounded-3 text-white" style="background-color: var(--jpm-color-navy-dark, #0A1128);">
          <div class="modal-header border-0 pb-0 pt-3 px-3 d-flex justify-content-between align-items-center">
            <h6 class="modal-title fw-semibold text-truncate pe-3" id="newsVideoModalLabel">
              <i class="bi bi-play-circle-fill text-danger me-2"></i>{{ activeVideoModalItem?.title }}
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
            <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-black shadow">
              <iframe
                v-if="activeVideoModalItem && activeVideoEmbedUrl"
                :src="activeVideoEmbedUrl"
                title="JPM Marine Video Player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
            <div class="p-2 pt-3 text-white-50 small d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger">{{ activeVideoModalItem?.cat }}</span>
                <span><i class="bi bi-calendar3 me-1"></i>{{ activeVideoModalItem?.dateFormatted }}</span>
              </div>
              <router-link
                v-if="activeVideoModalItem"
                :to="`/tin-tuc/${activeVideoModalItem.id}`"
                class="text-decoration-none text-white fw-semibold hover-accent"
                data-bs-dismiss="modal"
                @click="closeVideoModal"
              >
                Đọc toàn bộ bài viết <i class="bi bi-arrow-right ms-1"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';

const toaster = createToaster({ position: 'top-right', duration: 3000 });
const FALLBACK_IMG = 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&auto=format&fit=crop';

export default {
  name: 'TinTuc',
  setup() {
    const route = useRoute();
    const router = useRouter();

    const activeTab = ref(route.query.category ? String(route.query.category) : 'Tất cả');
    const searchQuery = ref(route.query.search ? String(route.query.search) : '');
    const showAllTopCategory = ref(false);
    const showMoreLatest = ref(false);

    // Video modal state
    const activeVideoModalItem = ref(null);

    watch(
      () => route.query,
      (q) => {
        if (q.category) {
          activeTab.value = String(q.category);
          showAllTopCategory.value = true;
        } else {
          activeTab.value = 'Tất cả';
        }
        searchQuery.value = q.search ? String(q.search) : '';
      }
    );

    // === Video Helpers & Modal ===
    const extractYoutubeId = (url) => {
      if (!url) return null;
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&]{11})/);
      return match ? match[1] : null;
    };

    const getYoutubeEmbedUrl = (url) => {
      const id = extractYoutubeId(url);
      return id ? `https://www.youtube.com/embed/${id}?autoplay=1&rel=0` : null;
    };

    const activeVideoEmbedUrl = computed(() => {
      if (!activeVideoModalItem.value?.youtube_url) return null;
      return getYoutubeEmbedUrl(activeVideoModalItem.value.youtube_url);
    });

    const openVideoModal = (item) => {
      if (!item) return;
      activeVideoModalItem.value = item;
      const el = document.getElementById('newsVideoModal');
      if (el && window.bootstrap?.Modal) {
        let modalInstance = window.bootstrap.Modal.getInstance(el);
        if (!modalInstance) {
          modalInstance = new window.bootstrap.Modal(el);
        }
        modalInstance.show();
      }
    };

    const closeVideoModal = () => {
      activeVideoModalItem.value = null;
    };

    onMounted(() => {
      const el = document.getElementById('newsVideoModal');
      if (el) {
        el.addEventListener('hidden.bs.modal', closeVideoModal);
      }
    });

    onUnmounted(() => {
      const el = document.getElementById('newsVideoModal');
      if (el) {
        el.removeEventListener('hidden.bs.modal', closeVideoModal);
      }
    });

    const getImageUrl = (path) => {
      if (!path) return FALLBACK_IMG;
      if (typeof path === 'string' && path.startsWith('http')) return path;
      const baseUrl = import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8000/storage';
      return `${baseUrl.replace(/\/$/, '')}/${String(path).replace(/^\//, '')}`;
    };

    // === Fetch Categories ===
    const { data: categoriesRaw } = useQuery({
      queryKey: ['client-tin-tuc-categories'],
      queryFn: async () => {
        const res = await axiosClient.get('/public/tin-tuc-categories');
        return res.data || [];
      },
      staleTime: 1000 * 60 * 5,
    });

    const tabs = computed(() => {
      const cats = categoriesRaw.value || [];
      return ['Tất cả', ...cats];
    });

    // === Fetch All Published News ===
    const { data: newsResponse, isFetching } = useQuery({
      queryKey: ['client-tin-tuc-all'],
      queryFn: async () => {
        const res = await axiosClient.get('/public/tin-tuc', { params: { per_page: 50 } });
        return res.data?.data || res.data || [];
      },
      staleTime: 1000 * 60 * 5,
    });

    // === Helpers ===
    const formatDateShort = (dateString) => {
      if (!dateString) return '';
      const d = new Date(dateString);
      return `${d.getDate().toString().padStart(2, '0')} Th${(d.getMonth() + 1).toString().padStart(2, '0')}, ${d.getFullYear()}`;
    };

    const estimateReadTime = (art) => {
      const text = `${art.title || ''} ${art.summary || ''}`.trim();
      const words = text ? text.split(/\s+/).length : 0;
      const mins = Math.max(2, Math.min(8, Math.ceil(words / 12)));
      return `${mins} phút đọc`;
    };

    const mapArticle = (art) => {
      const ytUrl = art.youtube_url && !String(art.youtube_url).includes('dQw4w9WgXcQ') ? art.youtube_url : null;
      const ytId = extractYoutubeId(ytUrl);
      const hasVideo = Boolean(ytId);

      return {
        ...art,
        img: art.thumbnail || FALLBACK_IMG,
        cat: art.category || 'Tin tức',
        dateFormatted: formatDateShort(art.created_at),
        excerpt: art.summary || '',
        read: estimateReadTime(art),
        views: Number(art.views || 0).toLocaleString('vi-VN'),
        avLetter: (art.author || 'J').charAt(0).toUpperCase(),
        author: art.author || 'Admin JPM',
        youtube_url: ytUrl,
        youtube_id: ytId,
        hasVideo,
      };
    };

    const allNews = computed(() => {
      const raw = Array.isArray(newsResponse.value) ? newsResponse.value : (newsResponse.value?.data || []);
      return raw.map(mapArticle);
    });

    // 1. Tiêu Điểm Chuyên Mục (Filtered by activeTab & searchQuery)
    const topCategoryArticles = computed(() => {
      return allNews.value.filter((art) => {
        const matchTab = activeTab.value === 'Tất cả' || art.cat === activeTab.value;
        const q = searchQuery.value.toLowerCase();
        const matchSearch = !q ||
          (art.title && art.title.toLowerCase().includes(q)) ||
          (art.excerpt && art.excerpt.toLowerCase().includes(q)) ||
          (art.cat && art.cat.toLowerCase().includes(q));
        return matchTab && matchSearch;
      });
    });

    const displayedTopCategoryArticles = computed(() => {
      if (showAllTopCategory.value || searchQuery.value !== '') {
        return topCategoryArticles.value;
      }
      return topCategoryArticles.value.slice(0, 4);
    });

    // 2. Bài Viết Mới Nhất (2 Hàng x 2 Cột = 4 Card Lớn)
    const latestGridArticles = computed(() => {
      if (allNews.value.length >= 8) {
        return allNews.value.slice(4, 8);
      }
      if (allNews.value.length > 4) {
        return allNews.value.slice(4);
      }
      return allNews.value.slice(0, 4);
    });

    // 3. Bài Viết Khác (1 Main Left + 3 Side Right)
    const otherArticlesPool = computed(() => {
      if (allNews.value.length > 8) {
        return allNews.value.slice(8);
      }
      if (allNews.value.length > 4) {
        return allNews.value.slice(4);
      }
      return allNews.value;
    });
    const otherMainStory = computed(() => otherArticlesPool.value[0] || allNews.value[0] || null);
    const otherSideStories = computed(() => otherArticlesPool.value.slice(1, 4));

    // === Actions ===
    const selectTab = (tab) => {
      activeTab.value = tab;
      showAllTopCategory.value = false;
      const query = {};
      if (tab !== 'Tất cả') query.category = tab;
      if (searchQuery.value) query.search = searchQuery.value;
      router.replace({ path: '/tin-tuc', query });
    };

    const resetFilters = () => {
      activeTab.value = 'Tất cả';
      searchQuery.value = '';
      showAllTopCategory.value = false;
      router.replace({ path: '/tin-tuc', query: {} });
    };

    const onImgError = (e) => {
      if (e.target && e.target.src !== FALLBACK_IMG) {
        e.target.src = FALLBACK_IMG;
      }
    };

    return {
      activeTab,
      tabs,
      searchQuery,
      showAllTopCategory,
      showMoreLatest,
      activeVideoModalItem,
      activeVideoEmbedUrl,
      openVideoModal,
      closeVideoModal,
      getImageUrl,
      isFetching,
      allNews,
      topCategoryArticles,
      displayedTopCategoryArticles,
      latestGridArticles,
      otherMainStory,
      otherSideStories,
      selectTab,
      resetFilters,
      onImgError,
    };
  },
};
</script>

<style scoped>
.news-wireframe-page {
  min-height: 100vh;
  background-color: #ffffff !important;
}

.news-wireframe-page section,
.news-wireframe-page .section {
  background-color: #ffffff !important;
}

/* ── PAGE TITLE (MATCHING GIO HANG) ── */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 800;
  color: #0A1128;
  letter-spacing: -0.5px;
}

/* ── TYPOGRAPHY & BUTTONS (DESIGN SYSTEM 2026) ── */
.section-editorial-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: clamp(1.4rem, 2.2vw, 1.75rem);
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: normal;
}

/* ── SEARCH INPUT IN TABS (CHUẨN HÓA THEO JPM DESIGN SYSTEM 2026: 40PX, 8PX RADIUS) ── */
.news-search-box {
  width: 240px;
  height: 40px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #e2e8f0;
  background: #ffffff;
  padding: 0 12px;
  display: flex;
  align-items: center;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.news-search-box:hover {
  border-color: #cbd5e1;
}

.news-search-box:focus-within {
  border-color: var(--jpm-color-red-marine, #f82249) !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
}

.news-search-box .search-input-icon {
  font-size: 0.9rem;
  color: #94a3b8;
  margin-right: 8px;
  flex-shrink: 0;
  transition: color 0.15s ease;
}

.news-search-box:focus-within .search-input-icon {
  color: var(--jpm-color-red-marine, #f82249);
}

.news-search-box .search-input-field {
  flex: 1;
  border: none !important;
  background: transparent !important;
  outline: none !important;
  box-shadow: none !important;
  font-size: 0.86rem;
  font-family: var(--default-font, 'Roboto', sans-serif);
  color: var(--jpm-color-navy-dark, #0A1128);
  padding: 0;
  height: 100%;
}

.news-search-box .search-input-field::placeholder {
  color: #94a3b8;
}

.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.95rem;
  transition: color 0.15s ease;
}

.btn-clear-search:hover {
  color: var(--jpm-color-red-marine, #f82249);
}

/* ── ACTION BUTTONS (CHUẨN HÓA 40PX, 8PX RADIUS) ── */
.btn-outline-action {
  background-color: #ffffff;
  color: #334155;
  border: 1px solid #cbd5e1;
  border-radius: var(--jpm-radius-control, 8px);
  height: 40px;
  padding: 0 16px;
  font-size: 0.85rem;
  font-weight: 600;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-outline-action:hover {
  border-color: var(--jpm-color-navy-dark, #0A1128);
  background-color: var(--jpm-color-navy-dark, #0A1128);
  color: #ffffff;
  transform: translateY(-1px);
}

/* ── SECTION 2: TABS & CARDS (HỖ TRỢ NHIỀU DANH MỤC TRÀN NGANG) ── */
.news-tabs-wrapper {
  border-color: #E2E8F0 !important;
}

.scrollbar-hidden {
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.scrollbar-hidden::-webkit-scrollbar {
  display: none;
}

.wireframe-tabs {
  scrollbar-width: none;
}
.wireframe-tabs::-webkit-scrollbar {
  display: none;
}
.wireframe-tab-btn {
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  padding: 8px 16px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #64748b;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: -1px; /* Canh sát vào đường kẻ border-bottom */
}
.wireframe-tab-btn:hover {
  color: var(--jpm-color-navy-dark, #0A1128);
}
.wireframe-tab-btn.active {
  color: var(--jpm-color-red-marine, #f82249);
  font-weight: 700;
  border-bottom-color: var(--jpm-color-red-marine, #f82249);
}

.hover-underline {
  transition: opacity 0.2s ease;
}
.hover-underline:hover {
  text-decoration: underline !important;
  opacity: 0.85;
}

.portrait-card-img {
  aspect-ratio: 16 / 10;
  border-radius: var(--jpm-radius-card, 12px);
}

/* ── DANH MỤC DẠNG THANH CHỮ ĐỎ MARINE NỔI BẬT ── */
.news-category-label {
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: var(--jpm-color-red-marine, #F82249);
  line-height: 1.3;
}

.play-badge-floating {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: rgba(10, 17, 40, 0.78);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  z-index: 2;
  transition: background-color 0.25s ease;
}

.group-hover:hover .play-badge-floating {
  background: var(--jpm-color-red-marine, #f82249);
}

/* ── SECTION: MIDDLE SPOTLIGHT CARDS (2 LỚN Ở KHOẢNG GIỮA) ── */
.middle-feature-card {
  transition: transform 0.25s ease;
}

.middle-card-img-wrap {
  aspect-ratio: 16 / 9;
  max-height: 340px;
  background-color: #f1f5f9;
  border-radius: var(--jpm-radius-card, 12px);
}

.middle-card-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: clamp(1.15rem, 1.8vw, 1.35rem);
  font-weight: 700;
  line-height: 1.35;
  color: var(--jpm-color-navy-dark, #0A1128);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.middle-card-title a {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  transition: opacity 0.2s ease;
}

.middle-card-title a:hover,
.middle-feature-card:hover .middle-card-title a {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  opacity: 0.85;
}

.middle-card-excerpt {
  font-size: 0.92rem;
  line-height: 1.55;
  color: #64748b;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── SECTION 3: LATEST STORIES / OTHER STORIES ── */
.latest-main-img {
  aspect-ratio: 16 / 10;
}
.latest-side-thumb {
  width: 200px;
  height: 126px;
  border-radius: var(--jpm-radius-card, 12px);
  background-color: #f1f5f9;
}

.other-side-item {
  transition: transform 0.2s ease;
}

.other-side-item:hover {
  transform: translateX(4px);
}

.other-side-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: clamp(1rem, 1.25vw, 1.15rem);
  font-weight: 700;
  line-height: 1.35;
  color: var(--jpm-color-navy-dark, #0A1128);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.other-side-excerpt {
  font-size: 0.86rem;
  line-height: 1.5;
  color: #64748b;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.play-circle-btn {
  width: 48px;
  height: 48px;
  background-color: rgba(10, 17, 40, 0.78);
  color: #ffffff;
  transition: all 0.25s ease;
  z-index: 2;
}
.group-hover:hover .play-circle-btn {
  background-color: #f82249;
  transform: translate(-50%, -50%) scale(1.1);
}

.play-circle-btn-sm {
  width: 32px;
  height: 32px;
  background-color: rgba(10, 17, 40, 0.85);
  color: #ffffff;
  font-size: 0.9rem;
  transition: all 0.25s ease;
  z-index: 2;
}
.group-hover:hover .play-circle-btn-sm {
  background-color: #f82249;
  transform: translate(-50%, -50%) scale(1.15);
}

/* ── SHARED HOVER & TRUNCATION ── */
a.group-hover,
a.group-hover:hover {
  color: inherit !important;
  text-decoration: none !important;
}
.transition-transform {
  transition: transform 0.45s ease;
}
.group-hover:hover .transition-transform {
  transform: scale(1.05);
}
.hero-title-hover {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  transition: opacity 0.2s ease;
}
.group-hover:hover .hero-title-hover,
.hero-title-hover:hover,
a.group-hover:hover .hero-title-hover,
a:hover .hero-title-hover {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  opacity: 0.88;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@media (max-width: 767px) {
  .latest-side-thumb {
    width: 115px;
    height: 74px;
  }
}
</style>
