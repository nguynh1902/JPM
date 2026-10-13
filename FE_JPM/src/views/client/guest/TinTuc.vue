<template>
  <main class="news-wireframe-page bg-white">

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 1: HERO BANNER (LEFT TEXT + RIGHT 3-IMAGE COLLAGE)
    ════════════════════════════════════════════════════════════════ -->
    <section class="hero-editorial-section position-relative overflow-hidden pt-5 pb-5">
      <!-- Subtle World/Marine Map Watermark Background -->
      <div class="hero-map-watermark" aria-hidden="true"></div>

      <div class="container position-relative z-1 py-lg-3">
        <div class="row align-items-center gy-5 gx-lg-5">

          <!-- Left Column: Big Editorial Heading + Description + Pill CTA -->
          <div class="col-lg-5 text-start">
            <h1 class="editorial-hero-title mb-4">
              Khám Phá Thế Giới<br />
              Động Cơ &amp; <span class="text-secondary fw-bold">Kỹ Thuật</span><br />
              Hàng Hải
            </h1>
            <p class="editorial-hero-desc text-muted mb-4">
              Cập nhật những kiến thức chuyên sâu về vận hành máy thủy, quy trình bảo dưỡng định kỳ chuẩn hãng, công nghệ tàu thuyền hiện đại và kinh nghiệm tra cứu phụ tùng thực chiến từ đội ngũ kỹ sư JPM Marine.
            </p>
            <div class="d-flex flex-wrap align-items-center gap-3">
              <button type="button" class="btn btn-editorial-dark rounded-pill px-4 py-2 fw-semibold" @click="scrollToSection('top-destinations')">
                Khám phá ngay
              </button>
              <!-- Quick Search Toggle / Input -->
              <div class="hero-search-inline position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.85rem;"></i>
                <input
                  type="text"
                  v-model.trim="searchQuery"
                  class="form-control rounded-pill ps-5 pe-4 py-2"
                  placeholder="Tìm kiếm bài viết..."
                  style="font-size: 0.88rem; min-width: 220px; border-color: #e5e7eb; background-color: #f9fafb;"
                  @keyup.enter="scrollToSection('top-destinations')"
                />
                <button
                  v-if="searchQuery"
                  type="button"
                  class="btn btn-link text-muted p-0 position-absolute top-50 end-0 translate-middle-y me-3"
                  @click="clearSearch"
                >
                  <i class="bi bi-x-circle-fill" style="font-size: 0.85rem;"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Right Column: Staggered 3-Image Collage (1 Tall Left + 2 Stacked Right) -->
          <div class="col-lg-7">
            <div v-if="isFetching && allNews.length === 0" class="row g-4 align-items-center">
              <div class="col-6">
                <div class="skeleton-box rounded-4" style="aspect-ratio: 3/4; width: 100%; background: #eef2f6;"></div>
              </div>
              <div class="col-6 d-flex flex-column gap-4">
                <div class="skeleton-box rounded-4" style="aspect-ratio: 4/3.6; width: 100%; background: #eef2f6;"></div>
                <div class="skeleton-box rounded-4" style="aspect-ratio: 4/3.6; width: 100%; background: #eef2f6;"></div>
              </div>
            </div>

            <div v-else class="row g-3 g-md-4 align-items-center">
              <!-- Collage Left: 1 Tall Portrait Card -->
              <div class="col-6" v-if="heroCollageMain">
                <router-link :to="`/tin-tuc/${heroCollageMain.id}`" class="collage-card collage-card-tall d-block position-relative rounded-4 overflow-hidden shadow-sm group-hover">
                  <img
                    :src="getImageUrl(heroCollageMain.img)"
                    :alt="heroCollageMain.title"
                    class="w-100 h-100 object-fit-cover transition-transform"
                    @error="onImgError"
                  />
                  <div class="collage-overlay d-flex flex-column justify-content-end p-3 p-md-4">
                    <span class="badge bg-danger align-self-start mb-2" style="font-size: 0.68rem;">{{ heroCollageMain.cat }}</span>
                    <h5 class="text-white fw-bold mb-0 text-truncate-2" style="font-size: 1rem; line-height: 1.35;">
                      {{ heroCollageMain.title }}
                    </h5>
                  </div>
                </router-link>
              </div>

              <!-- Collage Right: 2 Stacked Rounded Cards -->
              <div class="col-6 d-flex flex-column gap-3 gap-md-4">
                <router-link
                  v-for="item in heroCollageSide"
                  :key="item.id"
                  :to="`/tin-tuc/${item.id}`"
                  class="collage-card collage-card-stacked d-block position-relative rounded-4 overflow-hidden shadow-sm group-hover"
                >
                  <img
                    :src="getImageUrl(item.img)"
                    :alt="item.title"
                    class="w-100 h-100 object-fit-cover transition-transform"
                    @error="onImgError"
                  />
                  <div class="collage-overlay d-flex flex-column justify-content-end p-3">
                    <span class="badge bg-dark bg-opacity-75 align-self-start mb-1" style="font-size: 0.65rem;">{{ item.cat }}</span>
                    <h6 class="text-white fw-bold mb-0 text-truncate-2" style="font-size: 0.9rem; line-height: 1.3;">
                      {{ item.title }}
                    </h6>
                  </div>
                </router-link>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 2: TOP DESTINATIONS PATTERN -> CHUYÊN MỤC TIÊU ĐIỂM
         (Tabs + Explore All Button + 4 Vertical Portrait Cards)
    ════════════════════════════════════════════════════════════════ -->
    <section id="top-destinations" class="py-5">
      <div class="container">
        <!-- Section Title -->
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h2 class="editorial-sec-title mb-0">Chuyên Mục Tiêu Điểm</h2>
          <span v-if="searchQuery" class="text-muted small">
            Đang lọc theo từ khóa: <strong>"{{ searchQuery }}"</strong>
            <button type="button" class="btn btn-link text-danger p-0 ms-2 small text-decoration-none" @click="clearSearch">Xóa</button>
          </span>
        </div>

        <!-- Category Tabs Row + Right Outline Pill Button -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
          <nav class="editorial-tabs d-flex align-items-center gap-1 overflow-x-auto">
            <button
              v-for="tab in tabs"
              :key="tab"
              type="button"
              class="editorial-tab-btn"
              :class="{ active: activeTab === tab }"
              @click="selectTab(tab)"
            >
              {{ tab }}
            </button>
          </nav>

          <button
            v-if="filteredByTabArticles.length > 4"
            type="button"
            class="btn btn-outline-editorial rounded-pill px-3 py-1 fw-semibold text-nowrap"
            @click="showAllTopCards = !showAllTopCards"
          >
            {{ showAllTopCards ? 'Thu gọn danh sách' : 'Xem tất cả chuyên mục' }}
          </button>
        </div>

        <!-- Empty State when no article matches -->
        <div v-if="!isFetching && filteredByTabArticles.length === 0" class="text-center py-5 my-3 bg-light rounded-4">
          <i class="bi bi-journal-text text-muted display-4 d-block mb-2"></i>
          <h5 class="fw-bold text-dark mb-1">Chưa có bài viết phù hợp</h5>
          <p class="text-muted small mb-3">Không tìm thấy bài viết nào trong mục "{{ activeTab }}".</p>
          <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" @click="resetAllFilters">
            Xem tất cả bài viết
          </button>
        </div>

        <!-- 4 Vertical Portrait Cards Grid -->
        <div v-else class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
          <div class="col" v-for="card in topDestinationCards" :key="card.id">
            <router-link :to="`/tin-tuc/${card.id}`" class="text-decoration-none d-flex flex-column h-100 group-hover">
              <div class="portrait-card-img rounded-4 overflow-hidden mb-3 bg-light shadow-sm img-zoom-hover">
                <img
                  :src="getImageUrl(card.img)"
                  :alt="card.title"
                  class="w-100 h-100 object-fit-cover transition-transform"
                  @error="onImgError"
                />
              </div>
              <h5 class="fw-bold text-dark mb-1 text-truncate-2 hero-title-hover" style="font-size: 1.02rem; line-height: 1.4;">
                {{ card.title }}
              </h5>
              <span class="text-muted" style="font-size: 0.84rem;">{{ card.cat }}</span>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 3: LATEST STORIES PATTERN -> BÀI VIẾT MỚI NHẤT
         (Left 1 Large Landscape Story + Right 3 Horizontal Compact Items)
    ════════════════════════════════════════════════════════════════ -->
    <section class="py-5" v-if="latestMainStory">
      <div class="container">
        <!-- Section Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h2 class="editorial-sec-title mb-0">Bài Viết Mới Nhất</h2>
          <button
            v-if="extraLatestStories.length > 0"
            type="button"
            class="btn btn-outline-editorial rounded-pill px-3 py-1 fw-semibold"
            @click="showMoreLatest = !showMoreLatest"
          >
            {{ showMoreLatest ? 'Thu gọn bài viết' : 'Xem thêm bài viết' }}
          </button>
        </div>

        <div class="row g-4 gx-lg-5 align-items-start">
          <!-- Left Column: 1 Big Story Card -->
          <div class="col-lg-6">
            <router-link :to="`/tin-tuc/${latestMainStory.id}`" class="text-decoration-none d-flex flex-column group-hover">
              <div class="rounded-4 overflow-hidden mb-3 bg-light shadow-sm img-zoom-hover" style="aspect-ratio: 16/10;">
                <img
                  :src="getImageUrl(latestMainStory.img)"
                  :alt="latestMainStory.title"
                  class="w-100 h-100 object-fit-cover transition-transform"
                  @error="onImgError"
                />
              </div>
              <span class="text-muted fw-semibold mb-1" style="font-size: 0.8rem;">{{ latestMainStory.cat }}</span>
              <h3 class="fw-bold text-dark mb-2 hero-title-hover" style="font-size: 1.4rem; line-height: 1.35;">
                {{ latestMainStory.title }}
              </h3>
              <div class="text-muted mb-2" style="font-size: 0.8rem;">
                {{ latestMainStory.dateFormatted }} &bull; {{ latestMainStory.read }}
              </div>
              <p class="text-muted mb-0 text-truncate-3" style="font-size: 0.94rem; line-height: 1.6;">
                {{ latestMainStory.excerpt }}
              </p>
            </router-link>
          </div>

          <!-- Right Column: 3 Compact Horizontal Stories -->
          <div class="col-lg-6 d-flex flex-column gap-4">
            <router-link
              v-for="story in latestSideStories"
              :key="story.id"
              :to="`/tin-tuc/${story.id}`"
              class="d-flex align-items-center gap-3 gap-md-4 text-decoration-none group-hover"
            >
              <div class="rounded-4 overflow-hidden flex-shrink-0 bg-light shadow-sm img-zoom-hover" style="width: 145px; height: 120px;">
                <img
                  :src="getImageUrl(story.img)"
                  :alt="story.title"
                  class="w-100 h-100 object-fit-cover transition-transform"
                  @error="onImgError"
                />
              </div>
              <div class="d-flex flex-column">
                <span class="text-muted fw-semibold mb-1" style="font-size: 0.78rem;">{{ story.cat }}</span>
                <h5 class="fw-bold text-dark mb-2 text-truncate-2 hero-title-hover" style="font-size: 1.05rem; line-height: 1.4;">
                  {{ story.title }}
                </h5>
                <div class="text-muted" style="font-size: 0.78rem;">
                  {{ story.dateFormatted }} &bull; {{ story.read }}
                </div>
              </div>
            </router-link>
          </div>
        </div>

        <!-- Expanded Extra Latest Stories when "Xem thêm bài viết" is clicked -->
        <div v-if="showMoreLatest && extraLatestStories.length > 0" class="row g-4 mt-4 pt-4 border-top">
          <div class="col-12 col-md-6 col-lg-3" v-for="art in extraLatestStories" :key="art.id">
            <router-link :to="`/tin-tuc/${art.id}`" class="text-decoration-none d-flex flex-column h-100 group-hover">
              <div class="rounded-4 overflow-hidden mb-3 bg-light shadow-sm img-zoom-hover" style="aspect-ratio: 16/10;">
                <img
                  :src="getImageUrl(art.img)"
                  :alt="art.title"
                  class="w-100 h-100 object-fit-cover transition-transform"
                  @error="onImgError"
                />
              </div>
              <span class="text-muted fw-semibold mb-1" style="font-size: 0.76rem;">{{ art.cat }}</span>
              <h6 class="fw-bold text-dark mb-2 text-truncate-2 hero-title-hover" style="font-size: 0.98rem; line-height: 1.4;">
                {{ art.title }}
              </h6>
              <div class="text-muted mt-auto" style="font-size: 0.76rem;">
                {{ art.dateFormatted }} &bull; {{ art.read }}
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 4: TREKKER'S HIGHLIGHTS PATTERN -> TIÊU ĐIỂM KỸ THUẬT
         (Left Author Review/Story + Middle Tall Portrait + Right Video/Play Card)
    ════════════════════════════════════════════════════════════════ -->
    <section class="py-5 mb-4" v-if="highlightPrimary">
      <div class="container">
        <h2 class="editorial-sec-title mb-4">Tiêu Điểm Kỹ Thuật</h2>

        <div class="row g-4 gx-lg-5 align-items-center">
          <!-- Column 1 (Left): Author Profile + 5 Stars + Quote Title + Detailed Excerpt -->
          <div class="col-lg-4 text-start">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div
                class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                style="width: 52px; height: 52px; background-color: #0e1b4d; font-size: 1.15rem;"
              >
                {{ highlightPrimary.avLetter }}
              </div>
              <div>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 1rem;">{{ highlightPrimary.author }}</h6>
                <span class="text-muted" style="font-size: 0.82rem;">{{ highlightPrimary.cat }}</span>
              </div>
            </div>

            <!-- 5 Star Rating -->
            <div class="d-flex align-items-center gap-1 mb-2 text-warning" style="font-size: 0.88rem;">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>

            <router-link :to="`/tin-tuc/${highlightPrimary.id}`" class="text-decoration-none group-hover">
              <h4 class="fw-bold text-dark mb-3 hero-title-hover" style="font-size: 1.15rem; line-height: 1.4;">
                {{ highlightPrimary.title }}
              </h4>
            </router-link>

            <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.7;">
              "{{ highlightPrimary.excerpt || 'Quy trình kiểm tra và bảo dưỡng định kỳ đóng vai trò quyết định đến độ bền của động cơ thủy khi hoạt động trong môi trường nước mặn khắc nghiệt. Đội ngũ kỹ sư JPM khuyến nghị tuân thủ nghiêm ngặt lịch thay nhớt hộp số, lọc tách nước và kiểm tra kẽm chống ăn mòn.' }}"
            </p>
            <router-link :to="`/tin-tuc/${highlightPrimary.id}`" class="text-dark fw-bold small text-decoration-underline">
              Đọc chi tiết bài phân tích &rarr;
            </router-link>
          </div>

          <!-- Column 2 (Middle): Tall Portrait Image Card with Slider Dots -->
          <div class="col-md-6 col-lg-4">
            <div class="position-relative rounded-4 overflow-hidden shadow-sm bg-light img-zoom-hover" style="aspect-ratio: 3/3.8;">
              <router-link :to="`/tin-tuc/${highlightPrimary.id}`" class="d-block w-100 h-100">
                <img
                  :src="getImageUrl(highlightPrimary.img)"
                  :alt="highlightPrimary.title"
                  class="w-100 h-100 object-fit-cover transition-transform"
                  @error="onImgError"
                />
              </router-link>
              <!-- Pagination Dots at Bottom of Portrait Image -->
              <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 d-flex align-items-center gap-2 z-2 px-3 py-1 rounded-pill bg-dark bg-opacity-25">
                <button
                  v-for="(item, idx) in highlightPool"
                  :key="item.id"
                  type="button"
                  class="highlight-dot border-0 p-0"
                  :class="{ active: highlightIndex === idx }"
                  :title="item.title"
                  @click="highlightIndex = idx"
                ></button>
              </div>
            </div>
          </div>

          <!-- Column 3 (Right): Video/Media Card with Play Button + Caption + "See more highlights" Button -->
          <div class="col-md-6 col-lg-4 d-flex flex-column justify-content-between h-100">
            <div v-if="highlightSecondary" class="mb-4">
              <router-link :to="`/tin-tuc/${highlightSecondary.id}`" class="text-decoration-none d-block group-hover">
                <div class="position-relative rounded-4 overflow-hidden shadow-sm bg-light mb-3 img-zoom-hover" style="aspect-ratio: 4/3;">
                  <img
                    :src="getImageUrl(highlightSecondary.img)"
                    :alt="highlightSecondary.title"
                    class="w-100 h-100 object-fit-cover transition-transform"
                    @error="onImgError"
                  />
                  <!-- Dark Play Circle Icon -->
                  <div
                    class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center shadow"
                    style="width: 52px; height: 52px; background-color: rgba(17, 24, 39, 0.85); color: #ffffff;"
                  >
                    <i class="bi bi-play-fill fs-3 ms-1"></i>
                  </div>
                </div>
                <h6 class="fw-bold text-dark mb-1 hero-title-hover" style="font-size: 0.98rem; line-height: 1.4;">
                  {{ highlightSecondary.title }}
                </h6>
                <span class="text-muted small">{{ highlightSecondary.cat }}</span>
              </router-link>
            </div>

            <div class="text-md-end mt-2">
              <button
                type="button"
                class="btn btn-outline-editorial rounded-pill px-4 py-2 fw-semibold"
                @click="nextHighlight"
              >
                Xem thêm tiêu điểm
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 5: NEWSLETTER BANNER (BOTTOM FULL-WIDTH CALLOUT)
    ════════════════════════════════════════════════════════════════ -->
    <section class="newsletter-editorial-section py-5 mt-4">
      <div class="container py-4 text-center">
        <div class="row justify-content-center">
          <div class="col-lg-7">
            <h2 class="fw-bold text-white mb-3" style="font-size: clamp(1.6rem, 3vw, 2.25rem); line-height: 1.3;">
              Nhận Cẩm Nang Bảo Dưỡng Máy Thủy<br />
              Trực Tiếp Qua Hộp Thư Của Bạn
            </h2>
            <form @submit.prevent="submitNewsletter" class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2 mt-4 mb-3 mx-auto" style="max-width: 520px;">
              <input
                type="email"
                v-model.trim="newsletterEmail"
                class="form-control rounded-pill px-4 py-2 border-0 shadow-sm"
                placeholder="Nhập địa chỉ email của bạn..."
                required
                style="height: 46px; font-size: 0.92rem;"
              />
              <button
                type="submit"
                class="btn btn-editorial-dark rounded-pill px-4 fw-semibold text-nowrap shadow-sm"
                style="height: 46px;"
              >
                Đăng ký ngay
              </button>
            </form>
            <p class="text-white-50 mb-0" style="font-size: 0.78rem;">
              Bản tin kỹ thuật định kỳ từ đội ngũ kỹ sư JPM Marine. Cam kết bảo mật thông tin khách hàng.
            </p>
          </div>
        </div>
      </div>
    </section>

  </main>
</template>

<script>
import { ref, computed, watch } from 'vue';
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
    const showAllTopCards = ref(false);
    const showMoreLatest = ref(false);
    const highlightIndex = ref(0);
    const newsletterEmail = ref('');

    // Đồng bộ khi URL query thay đổi (ví dụ điều hướng từ trang ChiTietTinTuc)
    watch(
      () => route.query,
      (q) => {
        activeTab.value = q.category ? String(q.category) : 'Tất cả';
        searchQuery.value = q.search ? String(q.search) : '';
      }
    );

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
        return res.data || {};
      },
      staleTime: 1000 * 60 * 5,
    });

    const newsListRaw = computed(() => {
      const payload = newsResponse.value;
      if (!payload) return [];
      if (Array.isArray(payload)) return payload;
      return payload.data || [];
    });

    // === Helpers ===
    const formatDate = (d) => {
      if (!d) return '';
      const date = new Date(d);
      return `${date.getDate().toString().padStart(2, '0')} Th${(date.getMonth() + 1).toString().padStart(2, '0')}, ${date.getFullYear()}`;
    };

    const estimateReadTime = (art) => {
      const text = `${art.title || ''} ${art.summary || ''}`.trim();
      const words = text ? text.split(/\s+/).length : 0;
      const mins = Math.max(3, Math.min(8, Math.ceil(words / 10)));
      return `${mins} phút đọc`;
    };

    const mapArticle = (art) => ({
      ...art,
      img: art.thumbnail || FALLBACK_IMG,
      cat: art.category || 'Tin tức',
      dateFormatted: formatDate(art.created_at),
      excerpt: art.summary || '',
      read: estimateReadTime(art),
      views: Number(art.views || 0).toLocaleString('vi-VN'),
      avLetter: (art.author || 'J').charAt(0).toUpperCase(),
      author: art.author || 'Kỹ Sư JPM',
    });

    const allNews = computed(() => (newsListRaw.value || []).map(mapArticle));

    // === SECTION 1: HERO 3-IMAGE COLLAGE ===
    const heroCollageMain = computed(() => allNews.value[0] || null);
    const heroCollageSide = computed(() => allNews.value.slice(1, 3));

    // === SECTION 2: TOP DESTINATIONS (FILTERED BY TAB & SEARCH) ===
    const filteredByTabArticles = computed(() => {
      return allNews.value.filter((art) => {
        const matchTab = activeTab.value === 'Tất cả' || art.cat === activeTab.value;
        const q = searchQuery.value.toLowerCase();
        const matchSearch = !q ||
          art.title.toLowerCase().includes(q) ||
          art.excerpt.toLowerCase().includes(q) ||
          art.cat.toLowerCase().includes(q);
        return matchTab && matchSearch;
      });
    });

    const topDestinationCards = computed(() => {
      if (showAllTopCards.value) return filteredByTabArticles.value;
      // Khi ở "Tất cả" và không tìm kiếm, ưu tiên hiển thị từ bài thứ 4 để không trùng 3 ảnh Hero ở trên
      if (activeTab.value === 'Tất cả' && !searchQuery.value && allNews.value.length >= 7) {
        return allNews.value.slice(3, 7);
      }
      return filteredByTabArticles.value.slice(0, 4);
    });

    // === SECTION 3: LATEST STORIES (1 LEFT BIG + 3 RIGHT COMPACT) ===
    const latestStoriesPool = computed(() => {
      if (allNews.value.length >= 11) {
        return allNews.value.slice(7);
      }
      return allNews.value;
    });
    const latestMainStory = computed(() => latestStoriesPool.value[0] || allNews.value[0] || null);
    const latestSideStories = computed(() => {
      const slice = latestStoriesPool.value.slice(1, 4);
      return slice.length > 0 ? slice : allNews.value.slice(1, 4);
    });
    const extraLatestStories = computed(() => latestStoriesPool.value.slice(4));

    // === SECTION 4: TREKKER'S HIGHLIGHTS (TIÊU ĐIỂM KỸ THUẬT) ===
    const highlightPool = computed(() => {
      const techCats = ['Bảo Dưỡng Động Cơ', 'Kiến thức Tàu Thuyền', 'Công Nghệ Hàng Hải'];
      const filtered = allNews.value.filter(a => techCats.includes(a.cat));
      return filtered.length >= 2 ? filtered.slice(0, 5) : allNews.value.slice(0, 5);
    });

    const highlightPrimary = computed(() => {
      const pool = highlightPool.value;
      if (!pool.length) return null;
      return pool[highlightIndex.value % pool.length];
    });

    const highlightSecondary = computed(() => {
      const pool = highlightPool.value;
      if (pool.length < 2) return pool[0] || null;
      return pool[(highlightIndex.value + 1) % pool.length];
    });

    const nextHighlight = () => {
      if (highlightPool.value.length > 1) {
        highlightIndex.value = (highlightIndex.value + 1) % highlightPool.value.length;
      }
    };

    // === Actions ===
    const syncUrlQuery = (tab, search) => {
      const query = {};
      if (tab && tab !== 'Tất cả') query.category = tab;
      if (search) query.search = search;
      router.replace({ path: '/tin-tuc', query });
    };

    const selectTab = (tab) => {
      activeTab.value = tab;
      showAllTopCards.value = false;
      syncUrlQuery(tab, searchQuery.value);
    };

    const clearSearch = () => {
      searchQuery.value = '';
      syncUrlQuery(activeTab.value, '');
    };

    const resetAllFilters = () => {
      activeTab.value = 'Tất cả';
      searchQuery.value = '';
      showAllTopCards.value = false;
      syncUrlQuery('Tất cả', '');
    };

    const scrollToSection = (id) => {
      const el = document.getElementById(id);
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    };

    const submitNewsletter = () => {
      if (!newsletterEmail.value) return;
      toaster.success(`Đã đăng ký nhận bản tin thành công cho ${newsletterEmail.value}!`);
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
      isFetching,
      showAllTopCards,
      showMoreLatest,
      highlightIndex,
      newsletterEmail,
      allNews,
      heroCollageMain,
      heroCollageSide,
      filteredByTabArticles,
      topDestinationCards,
      latestMainStory,
      latestSideStories,
      extraLatestStories,
      highlightPool,
      highlightPrimary,
      highlightSecondary,
      nextHighlight,
      selectTab,
      clearSearch,
      resetAllFilters,
      scrollToSection,
      submitNewsletter,
      onImgError,
    };
  },
};
</script>

<style scoped>
.news-wireframe-page {
  min-height: 100vh;
  padding-top: 85px;
}

/* ── SECTION 1: HERO EDITORIAL ── */
.hero-editorial-section {
  background-color: #ffffff;
}
.hero-map-watermark {
  position: absolute;
  top: 5%;
  right: 2%;
  width: 62%;
  height: 90%;
  background-image: radial-gradient(#d1d5db 1.25px, transparent 1.25px);
  background-size: 18px 18px;
  opacity: 0.45;
  mask-image: radial-gradient(ellipse at center, black 40%, transparent 75%);
  -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 75%);
  pointer-events: none;
}

.editorial-hero-title {
  font-size: clamp(2.1rem, 3.6vw, 3.2rem);
  font-weight: 800;
  color: #111827;
  line-height: 1.18;
  letter-spacing: -0.8px;
}

.editorial-hero-desc {
  font-size: 0.96rem;
  line-height: 1.7;
  max-width: 460px;
}

.btn-editorial-dark {
  background-color: #27272a;
  color: #ffffff;
  border: 1px solid #27272a;
  font-size: 0.9rem;
  transition: all 0.25s ease;
}
.btn-editorial-dark:hover {
  background-color: #f82249;
  border-color: #f82249;
  color: #ffffff;
  transform: translateY(-2px);
}

/* 3-Image Staggered Collage */
.collage-card-tall {
  aspect-ratio: 3 / 4.1;
}
.collage-card-stacked {
  aspect-ratio: 4 / 3.4;
}
.collage-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(14, 27, 77, 0.82) 0%, rgba(14, 27, 77, 0.2) 45%, transparent 100%);
  transition: opacity 0.25s ease;
}

/* ── SECTION HEADINGS & BUTTONS ── */
.editorial-sec-title {
  font-size: clamp(1.5rem, 2.4vw, 2rem);
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.5px;
}

.btn-outline-editorial {
  border: 1px solid #d1d5db;
  color: #1f2937;
  background-color: #ffffff;
  font-size: 0.84rem;
  transition: all 0.2s ease;
}
.btn-outline-editorial:hover {
  border-color: #111827;
  background-color: #111827;
  color: #ffffff;
}

/* ── SECTION 2: TABS & PORTRAIT CARDS ── */
.editorial-tabs {
  scrollbar-width: none;
}
.editorial-tabs::-webkit-scrollbar {
  display: none;
}
.editorial-tab-btn {
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  padding: 8px 14px;
  font-size: 0.88rem;
  font-weight: 600;
  color: #6b7280;
  white-space: nowrap;
  transition: all 0.2s ease;
}
.editorial-tab-btn:hover {
  color: #111827;
}
.editorial-tab-btn.active {
  color: #111827;
  font-weight: 700;
  border-bottom-color: #111827;
}

.portrait-card-img {
  aspect-ratio: 3 / 3.9;
}

/* ── SECTION 4: HIGHLIGHT DOTS ── */
.highlight-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.5);
  transition: all 0.25s ease;
}
.highlight-dot.active {
  width: 22px;
  border-radius: 10px;
  background-color: #ffffff;
}

/* ── SECTION 5: NEWSLETTER BANNER ── */
.newsletter-editorial-section {
  background: linear-gradient(135deg, #4b5563 0%, #1f2937 100%);
}

/* ── UTILITIES ── */
.transition-transform {
  transition: transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.group-hover:hover .transition-transform {
  transform: scale(1.05);
}
.hero-title-hover {
  transition: color 0.2s ease;
}
.group-hover:hover .hero-title-hover {
  color: #f82249 !important;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.text-truncate-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
