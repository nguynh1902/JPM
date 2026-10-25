<template>
  <main class="news-wireframe-page bg-white">

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 1: HERO BANNER WITH SUBMERGED WATERMARK IMAGE
    ════════════════════════════════════════════════════════════════ -->
    <section class="hero-editorial-section position-relative overflow-hidden">
      <!-- Submerged Marine Image (Ảnh chìm trong nền) -->
      <div class="hero-submerged-bg"></div>

      <div class="container position-relative z-1 py-5">
        <div class="row align-items-center py-lg-4">

          <!-- Left Column: Big Title + Description + Pill Button -->
          <div class="col-lg-6 text-start">
            <h1 class="hero-main-heading mb-4">
              Khám Phá<br />
              Thế Giới <span class="text-secondary fw-bold">Công Nghệ</span><br />
              Hàng Hải
            </h1>
            <p class="hero-subtext text-muted mb-4">
              Tổng hợp những kiến thức chuyên sâu về vận hành tàu thuyền, cẩm nang bảo dưỡng động cơ thủy định kỳ và xu hướng thiết bị hàng hải hiện đại nhất từ đội ngũ kỹ sư JPM Marine.
            </p>
            <button type="button" class="btn btn-dark-pill px-4 py-2" @click="scrollToSection('top-destinations')">
              Khám phá ngay
            </button>
          </div>

        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 2: TOP DESTINATIONS EQUIVALENT ("Tiêu Điểm Chuyên Mục")
         Tabs Row + Outline Pill Button + 4 Portrait Cards
    ════════════════════════════════════════════════════════════════ -->
    <section id="top-destinations" class="py-5">
      <div class="container">
        <!-- Section Title -->
        <h2 class="section-editorial-title mb-3">Tiêu Điểm Chuyên Mục</h2>

        <!-- Tabs Bar + Right Action Pill -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-1">
          <nav class="wireframe-tabs d-flex align-items-center gap-1 overflow-auto">
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

          <div class="d-flex align-items-center gap-2">
            <!-- Quick Search Input -->
            <div class="position-relative" style="width: 210px;">
              <input
                type="text"
                v-model.trim="searchQuery"
                class="form-control form-control-sm rounded-pill ps-3 pe-4 border-secondary-subtle shadow-none"
                placeholder="Tìm kiếm bài viết..."
                style="font-size: 0.82rem; height: 34px;"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="btn btn-link text-muted p-0 position-absolute top-50 end-0 translate-middle-y me-2 text-decoration-none"
                @click="searchQuery = ''"
              >
                <i class="bi bi-x-circle-fill" style="font-size: 0.8rem;"></i>
              </button>
              <i v-else class="bi bi-search text-muted position-absolute top-50 end-0 translate-middle-y me-3" style="font-size: 0.78rem; pointer-events: none;"></i>
            </div>

            <button
              v-if="topCategoryArticles.length > 4"
              type="button"
              class="btn btn-outline-pill"
              @click="showAllTopCategory = !showAllTopCategory"
            >
              {{ showAllTopCategory ? 'Thu gọn danh sách' : 'Khám phá tất cả chuyên mục' }}
            </button>
          </div>
        </div>

        <!-- Empty State if search/tab has no results -->
        <div v-if="!isFetching && displayedTopCategoryArticles.length === 0" class="text-center py-5 bg-light rounded-2 my-2">
          <i class="bi bi-journal-text text-muted fs-1 d-block mb-2"></i>
          <p class="text-muted mb-3">Không tìm thấy bài viết nào khớp với bộ lọc hiện tại.</p>
          <button type="button" class="btn btn-outline-pill" @click="resetFilters">Xem tất cả bài viết</button>
        </div>

        <!-- 4 Tall Portrait Cards Grid -->
        <div v-else class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
          <div class="col" v-for="item in displayedTopCategoryArticles" :key="item.id">
            <router-link :to="`/tin-tuc/${item.id}`" class="text-decoration-none d-block group-hover h-100">
              <div class="portrait-card-img position-relative rounded-2 overflow-hidden mb-3 bg-light shadow-xs">
                <img :src="getImageUrl(item.img)" :alt="item.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
              </div>
              <h5 class="fw-bold text-dark mb-1 text-truncate-2 hero-title-hover" style="font-size: 0.98rem; line-height: 1.4;">
                {{ item.title }}
              </h5>
              <span class="text-muted d-block" style="font-size: 0.82rem;">{{ item.cat }}</span>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 3: LATEST STORIES EQUIVALENT ("Bài Viết Mới Nhất")
         1 Large Story Left + 3 Compact Horizontal Stories Right
    ════════════════════════════════════════════════════════════════ -->
    <section class="py-5" v-if="latestMainStory">
      <div class="container">
        <!-- Header Row -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h2 class="section-editorial-title m-0">Bài Viết Mới Nhất</h2>
          <button
            v-if="moreLatestStories.length > 0"
            type="button"
            class="btn btn-outline-pill"
            @click="showMoreLatest = !showMoreLatest"
          >
            {{ showMoreLatest ? 'Thu gọn bài viết' : 'Xem thêm bài viết' }}
          </button>
        </div>

        <div class="row g-4 gx-lg-5 align-items-start">
          <!-- Left: 1 Large Featured Story -->
          <div class="col-lg-6">
            <router-link :to="`/tin-tuc/${latestMainStory.id}`" class="text-decoration-none d-block group-hover">
              <div class="latest-main-img position-relative rounded-4 overflow-hidden mb-3 bg-light shadow-xs">
                <img :src="getImageUrl(latestMainStory.img)" :alt="latestMainStory.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                <div
                  v-if="latestMainStory.hasVideo"
                  class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn shadow"
                  @click.stop.prevent="openVideoModal(latestMainStory)"
                  title="Kích để xem video"
                >
                  <i class="bi bi-play-fill fs-4 ms-1"></i>
                </div>
              </div>
              <div class="text-muted fw-semibold mb-1" style="font-size: 0.8rem;">
                {{ latestMainStory.cat }}
              </div>
              <h3 class="fw-bold text-dark mb-2 hero-title-hover" style="font-size: 1.35rem; line-height: 1.35;">
                {{ latestMainStory.title }}
              </h3>
              <div class="text-muted mb-2" style="font-size: 0.78rem;">
                {{ latestMainStory.dateFormatted }} &nbsp;•&nbsp; {{ latestMainStory.read }}
              </div>
              <p class="text-secondary mb-0 text-truncate-2" style="font-size: 0.92rem; line-height: 1.6;">
                {{ latestMainStory.excerpt }}
              </p>
            </router-link>
          </div>

          <!-- Right: 3 Stacked Horizontal Cards -->
          <div class="col-lg-6 d-flex flex-column gap-3">
            <router-link
              v-for="story in latestSideStories"
              :key="story.id"
              :to="`/tin-tuc/${story.id}`"
              class="d-flex gap-3 align-items-center text-decoration-none group-hover"
            >
              <div class="latest-side-thumb position-relative rounded-4 overflow-hidden flex-shrink-0 bg-light shadow-xs">
                <img :src="getImageUrl(story.img)" :alt="story.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                <div
                  v-if="story.hasVideo"
                  class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn-sm shadow"
                  @click.stop.prevent="openVideoModal(story)"
                  title="Kích xem video"
                >
                  <i class="bi bi-play-fill"></i>
                </div>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="text-muted fw-semibold mb-1" style="font-size: 0.76rem;">
                  {{ story.cat }}
                </div>
                <h5 class="fw-bold text-dark mb-2 text-truncate-2 hero-title-hover" style="font-size: 1rem; line-height: 1.4;">
                  {{ story.title }}
                </h5>
                <div class="text-muted" style="font-size: 0.76rem;">
                  {{ story.dateFormatted }} &nbsp;•&nbsp; {{ story.read }}
                </div>
              </div>
            </router-link>
          </div>
        </div>

        <!-- Expandable More Latest Stories Grid -->
        <div v-if="showMoreLatest && moreLatestStories.length > 0" class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-3 pt-4 border-top">
          <div class="col" v-for="extra in moreLatestStories" :key="extra.id">
            <router-link :to="`/tin-tuc/${extra.id}`" class="text-decoration-none d-block group-hover h-100">
              <div class="rounded-4 overflow-hidden mb-3 bg-light position-relative" style="aspect-ratio: 16/10;">
                <img :src="getImageUrl(extra.img)" :alt="extra.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                <div
                  v-if="extra.hasVideo"
                  class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn shadow"
                  @click.stop.prevent="openVideoModal(extra)"
                  title="Kích để xem video"
                >
                  <i class="bi bi-play-fill fs-4 ms-1"></i>
                </div>
              </div>
              <div class="text-muted fw-semibold mb-1" style="font-size: 0.75rem;">{{ extra.cat }}</div>
              <h6 class="fw-bold text-dark mb-1 text-truncate-2 hero-title-hover" style="font-size: 0.95rem; line-height: 1.4;">{{ extra.title }}</h6>
              <div class="text-muted" style="font-size: 0.75rem;">{{ extra.dateFormatted }} • {{ extra.read }}</div>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 4: TREKKER'S HIGHLIGHTS EQUIVALENT ("Góc Chuyên Gia Kỹ Thuật")
         Left Author Story + Middle Portrait Card + Right Video Card & Button
    ════════════════════════════════════════════════════════════════ -->
    <section class="py-5" v-if="highlightMain">
      <div class="container">
        <h2 class="section-editorial-title mb-4">Góc Chuyên Gia Kỹ Thuật</h2>

        <div class="row g-4 gx-lg-5 align-items-center">
          <!-- Column 1 (Left): Author Profile + Stars + Story Quote -->
          <div class="col-lg-4 text-start">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="author-circle-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0">
                {{ highlightMain.avLetter }}
              </div>
              <div>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.98rem;">{{ highlightMain.author }}</h6>
                <small class="text-muted" style="font-size: 0.8rem;">Chuyên gia Kỹ thuật JPM Marine</small>
              </div>
            </div>

            <!-- 5 Stars Rating -->
            <div class="d-flex align-items-center gap-1 text-warning mb-2" style="font-size: 0.88rem;">
              <i class="bi bi-star-fill" v-for="s in 5" :key="s"></i>
            </div>

            <router-link :to="`/tin-tuc/${highlightMain.id}`" class="text-decoration-none">
              <h4 class="fw-bold text-dark mb-3 hero-title-hover" style="font-size: 1.12rem; line-height: 1.4;">
                {{ highlightMain.title }}
              </h4>
            </router-link>

            <p class="text-secondary mb-0" style="font-size: 0.9rem; line-height: 1.7; text-align: justify;">
              {{ highlightMain.excerpt }} Quy trình kiểm tra định kỳ đúng tiêu chuẩn nhà sản xuất luôn là chìa khóa vàng giúp động cơ thủy vận hành bền bỉ, tiết kiệm nhiên liệu và an toàn tuyệt đối trên mọi hải trình dài ngày.
            </p>
          </div>

          <!-- Column 2 (Middle): Tall Portrait Image Card -->
          <div class="col-lg-4 col-md-6">
            <router-link :to="`/tin-tuc/${highlightMain.id}`" class="d-block position-relative rounded-4 overflow-hidden shadow-xs group-hover highlight-middle-card">
              <img :src="getImageUrl(highlightMain.img)" :alt="highlightMain.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
              <!-- Pagination Dots Indicator (Wireframe Aesthetic) -->
              <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 d-flex gap-1">
                <span class="dot-indicator active"></span>
                <span class="dot-indicator"></span>
                <span class="dot-indicator"></span>
                <span class="dot-indicator"></span>
              </div>
            </router-link>
          </div>

          <!-- Column 3 (Right): Video Feature Card + Caption + Outline Pill Button -->
          <div class="col-lg-4 col-md-6 d-flex flex-column justify-content-between h-100" v-if="highlightVideo">
            <div>
              <router-link :to="`/tin-tuc/${highlightVideo.id}`" class="d-block text-decoration-none group-hover">
                <div class="position-relative rounded-4 overflow-hidden mb-3 bg-light shadow-xs highlight-video-card">
                  <img :src="getImageUrl(highlightVideo.img)" :alt="highlightVideo.title" class="w-100 h-100 object-fit-cover transition-transform" @error="onImgError" />
                  <div
                    class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center play-circle-btn shadow"
                    @click.stop.prevent="openVideoModal(highlightVideo)"
                    title="Kích để xem video"
                  >
                    <i class="bi bi-play-fill fs-4 ms-1"></i>
                  </div>
                </div>
                <h5 class="fw-bold text-dark mb-1 text-truncate-2 hero-title-hover" style="font-size: 0.98rem; line-height: 1.4;">
                  {{ highlightVideo.title }}
                </h5>
                <span class="text-muted d-block mb-4" style="font-size: 0.8rem;">{{ highlightVideo.cat }}</span>
              </router-link>
            </div>

            <div class="text-end mt-2">
              <button type="button" class="btn btn-outline-pill" @click="selectTabAndScroll('Bảo Dưỡng Động Cơ')">
                Xem thêm tiêu điểm
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 5: NEWSLETTER BANNER ("Get Your Travel Inspiration Straight to Your Inbox")
    ════════════════════════════════════════════════════════════════ -->
    <section class="newsletter-banner-section mt-4">
      <div class="container py-5">
        <div class="row justify-content-center text-center py-3">
          <div class="col-lg-7 col-md-9">
            <h2 class="newsletter-heading text-white fw-bold mb-4">
              Nhận Cẩm Nang Bảo Dưỡng &<br />
              Tin Tức Hàng Hải Mới Nhất Qua Email
            </h2>

            <form @submit.prevent="handleSubscribe" class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2 mx-auto mb-3" style="max-width: 480px;">
              <input
                type="email"
                v-model.trim="newsletterEmail"
                required
                class="form-control rounded-pill px-4 py-2 border-0 shadow-sm"
                placeholder="Nhập địa chỉ email của bạn..."
                style="height: 44px; font-size: 0.9rem;"
              />
              <button type="submit" class="btn btn-dark-pill px-4 flex-shrink-0" style="height: 44px;">
                Đăng ký
              </button>
            </form>

            <p class="text-white-50 mb-0" style="font-size: 0.78rem;">
              Đăng ký ngay để nhận cẩm nang kỹ thuật và ưu đãi phụ tùng mới nhất. Xem thêm
              <router-link to="/ho-tro" class="text-white text-decoration-underline">Chính sách bảo mật</router-link>.
            </p>
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
        <div class="modal-content bg-dark border-0 shadow-lg overflow-hidden rounded-4 text-white">
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

  </main>
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
    const newsletterEmail = ref('');

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
      return `http://localhost:8000/storage/${path}`;
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

    // 1. Section 1: Hero Collage (Top 3 articles)
    const collageCards = computed(() => allNews.value.slice(0, 3));

    // 2. Section 2: Top Category Articles (Filtered by activeTab & searchQuery)
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
      // Khi ở "Tất cả", lấy từ vị trí số 3 để không lặp lại 3 ảnh ở Hero Collage
      if (activeTab.value === 'Tất cả' && topCategoryArticles.value.length >= 7) {
        return topCategoryArticles.value.slice(3, 7);
      }
      return topCategoryArticles.value.slice(0, 4);
    });

    // 3. Section 3: Latest Stories (1 Main Left + 3 Side Right + Expandable More)
    const latestPool = computed(() => {
      return allNews.value.length > 7 ? allNews.value.slice(7) : allNews.value;
    });
    const latestMainStory = computed(() => latestPool.value[0] || allNews.value[0] || null);
    const latestSideStories = computed(() => latestPool.value.slice(1, 4));
    const moreLatestStories = computed(() => latestPool.value.slice(4));

    // 4. Section 4: Trekker's Highlights Equivalent ("Góc Chuyên Gia Kỹ Thuật")
    const techPool = computed(() => {
      const techCats = ['Bảo Dưỡng Động Cơ', 'Kiến thức Tàu Thuyền', 'Công Nghệ Hàng Hải'];
      const filtered = allNews.value.filter(a => techCats.includes(a.cat));
      return filtered.length >= 2 ? filtered : allNews.value;
    });
    const highlightMain = computed(() => techPool.value[0] || null);
    const highlightVideo = computed(() => {
      const videoStory = techPool.value.find(a => a.hasVideo) || allNews.value.find(a => a.hasVideo);
      return videoStory || techPool.value[1] || techPool.value[0] || null;
    });

    // === Actions ===
    const selectTab = (tab) => {
      activeTab.value = tab;
      showAllTopCategory.value = false;
      const query = {};
      if (tab !== 'Tất cả') query.category = tab;
      if (searchQuery.value) query.search = searchQuery.value;
      router.replace({ path: '/tin-tuc', query });
    };

    const selectTabAndScroll = (tab) => {
      selectTab(tab);
      showAllTopCategory.value = true;
      scrollToSection('top-destinations');
    };

    const resetFilters = () => {
      activeTab.value = 'Tất cả';
      searchQuery.value = '';
      showAllTopCategory.value = false;
      router.replace({ path: '/tin-tuc', query: {} });
    };

    const scrollToSection = (id) => {
      const el = document.getElementById(id);
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    };

    const handleSubscribe = () => {
      if (!newsletterEmail.value) return;
      toaster.success(`Đăng ký nhận bản tin thành công cho ${newsletterEmail.value}!`);
      newsletterEmail.value = '';
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
      newsletterEmail,
      activeVideoModalItem,
      activeVideoEmbedUrl,
      openVideoModal,
      closeVideoModal,
      getImageUrl,
      isFetching,
      allNews,
      collageCards,
      topCategoryArticles,
      displayedTopCategoryArticles,
      latestMainStory,
      latestSideStories,
      moreLatestStories,
      highlightMain,
      highlightVideo,
      selectTab,
      selectTabAndScroll,
      resetFilters,
      scrollToSection,
      handleSubscribe,
      onImgError,
    };
  },
};
</script>

<style scoped>
.news-wireframe-page {
  min-height: 100vh;
  padding-top: 70px;
}

/* ── SECTION 1: HERO BANNER ── */
.hero-editorial-section {
  padding-top: 30px;
  padding-bottom: 40px;
}

.hero-submerged-bg {
  position: absolute;
  top: 0;
  right: 0;
  width: 65%;
  height: 100%;
  background-image: url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1600&auto=format&fit=crop&q=85');
  background-size: cover;
  background-position: center right;
  opacity: 0.95;
  mask-image: linear-gradient(to right, transparent 0%, transparent 15%, rgba(0, 0, 0, 0.45) 45%, rgba(0, 0, 0, 1) 80%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, transparent 15%, rgba(0, 0, 0, 0.45) 45%, rgba(0, 0, 0, 1) 80%);
  pointer-events: none;
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
  max-width: 460px;
}

.collage-card {
  background-color: #e5e7eb;
}
.collage-card--tall {
  height: 360px;
}
.collage-card--stacked {
  height: 215px;
}

.collage-card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(14, 27, 77, 0.82) 0%, rgba(14, 27, 77, 0.2) 55%, transparent 100%);
  transition: background 0.3s ease;
}
.collage-card:hover .collage-card-overlay {
  background: linear-gradient(to top, rgba(248, 34, 73, 0.85) 0%, rgba(14, 27, 77, 0.25) 60%, transparent 100%);
}

/* ── TYPOGRAPHY & BUTTONS (WIREFRAME STYLE) ── */
.section-editorial-title {
  font-family: var(--heading-font);
  font-size: clamp(1.4rem, 2.2vw, 1.75rem);
  font-weight: 700;
  color: #111827;
  letter-spacing: normal;
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

.btn-outline-pill {
  background-color: #ffffff;
  color: #1f2937;
  border: 1px solid #d1d5db;
  border-radius: 50rem;
  padding: 6px 18px;
  font-size: 0.8rem;
  font-weight: 600;
  white-space: nowrap;
  transition: all 0.2s ease;
}
.btn-outline-pill:hover {
  border-color: #111827;
  background-color: #111827;
  color: #ffffff;
}

/* ── SECTION 2: TABS & PORTRAIT CARDS ── */
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
  padding: 6px 14px;
  font-size: 0.86rem;
  font-weight: 600;
  color: #6b7280;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s ease;
}
.wireframe-tab-btn:hover {
  color: #111827;
}
.wireframe-tab-btn.active {
  color: #111827;
  font-weight: 700;
  border-bottom-color: #111827;
}

.portrait-card-img {
  aspect-ratio: 3 / 4;
}

/* ── SECTION 3: LATEST STORIES ── */
.latest-main-img {
  aspect-ratio: 16 / 10;
}
.latest-side-thumb {
  width: 135px;
  height: 112px;
}

/* ── SECTION 4: TREKKER'S HIGHLIGHTS EQUIVALENT ── */
.author-circle-avatar {
  width: 48px;
  height: 48px;
  background-color: #0e1b4d;
  font-size: 1.1rem;
}

.highlight-middle-card {
  aspect-ratio: 3 / 4;
  max-height: 400px;
  width: 100%;
}

.highlight-video-card {
  aspect-ratio: 4 / 3;
}

.play-circle-btn {
  width: 48px;
  height: 48px;
  background-color: rgba(17, 24, 39, 0.78);
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
  background-color: rgba(17, 24, 39, 0.85);
  color: #ffffff;
  font-size: 0.9rem;
  transition: all 0.25s ease;
  z-index: 2;
}
.group-hover:hover .play-circle-btn-sm {
  background-color: #f82249;
  transform: translate(-50%, -50%) scale(1.15);
}

.dot-indicator {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.55);
}
.dot-indicator.active {
  width: 18px;
  border-radius: 10px;
  background-color: #ffffff;
}

/* ── SECTION 5: NEWSLETTER BANNER ── */
.newsletter-banner-section {
  background: linear-gradient(135deg, #4b5563 0%, #374151 50%, #1f2937 100%);
}
.newsletter-heading {
  font-size: clamp(1.6rem, 2.8vw, 2.25rem);
  line-height: 1.3;
  letter-spacing: -0.5px;
}

/* ── SHARED HOVER & TRUNCATION ── */
.transition-transform {
  transition: transform 0.45s ease;
}
.group-hover:hover .transition-transform {
  transform: scale(1.05);
}
.hero-title-hover {
  transition: color 0.2s ease;
}
.group-hover:hover .hero-title-hover,
.hero-title-hover:hover {
  color: #f82249 !important;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@media (max-width: 767px) {
  .collage-card--tall {
    height: 250px;
  }
  .collage-card--stacked {
    height: 150px;
  }
  .latest-side-thumb {
    width: 105px;
    height: 90px;
  }
}
</style>
