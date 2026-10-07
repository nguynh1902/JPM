<template>
  <main class="news-page" style="background-color: #ffffff !important; width: 100%;"> 

    <!-- ══════════════════════════════════
         CATEGORY TABS
    ══════════════════════════════════ -->
    <div class="cat-bar shadow-sm">
      <div class="container">
        <nav class="cat-scroll">
          <button
            v-for="tab in tabs" :key="tab"
            class="cat-btn" :class="{ active: activeTab === tab }"
            @click="activeTab = tab"
          >{{ tab }}</button>
        </nav>
      </div>
    </div>


    <div class="container news-body">

      <!-- ══════════════════════════════════
           ZONE 1: HERO + SIDE LIST
      ══════════════════════════════════ -->
      <section style="background-color: transparent !important;" class="py-4">
        <div class="row gx-lg-5 gy-4">

          <!-- Left: Big Featured (Hero) -->
          <div class="col-lg-7 pe-lg-4" v-if="heroArticle">
            <router-link :to="`/tin-tuc/${heroArticle.id}`" class="d-flex flex-column text-decoration-none h-100 group-hover" :class="{'opacity-50': isFetching}" style="transition: opacity 0.2s;">
              <!-- Author -->
              <div class="d-flex align-items-center gap-3 mb-2">
                <div class="d-inline-flex justify-content-center align-items-center rounded-circle text-white fw-bold flex-shrink-0" style="width: 40px; height: 40px; background-color: var(--heading-color);">{{ heroArticle.avLetter }}</div>
                <div class="d-flex flex-column">
                  <span class="fw-bold  lh-sm" style="color: var(--heading-color);">{{ heroArticle.author }}</span>
                  <span class="text-muted" style="font-size: 0.8rem;">Author</span>
                </div>
              </div>
              
              <!-- Title -->
              <h1 class="fw-bold  lh-sm mt-2 mb-2 hero-title-hover text-truncate-2" style="color: var(--heading-color); font-size: clamp(1.8rem, 3vw, 2.8rem); line-height: 1.2;">{{ heroArticle.title }}</h1>
              
              <!-- Meta -->
              <div class="d-flex align-items-center gap-2 mb-3" style="font-size: 0.85rem;">
                <span class="fw-bold text-uppercase" style="color: var(--accent-color);">{{ heroArticle.cat }}</span>
                <span class="text-muted">|</span>
                <span class="text-muted">{{ heroArticle.read }}</span>
              </div>
              
              <!-- Image -->
              <div class="mt-4 rounded-3 overflow-hidden img-zoom-hover" style="aspect-ratio: 16/9; background: #e5e7eb;">
                <img :src="getImageUrl(heroArticle.img)" :alt="heroArticle.title" class="w-100 h-100 object-fit-cover transition-transform">
              </div>
            </router-link>
          </div>

          <!-- Right: 4 side articles -->
          <div class="col-lg-5 ps-lg-4">
            <div class="d-flex flex-column justify-content-between h-100" :class="{'opacity-50': isFetching}" style="transition: opacity 0.2s;">
              <router-link :to="`/tin-tuc/${art.id}`" class="d-flex gap-4 align-items-start text-decoration-none group-hover" v-for="(art, index) in sideArticles" :key="index">
                <div class="d-flex flex-column h-100" style="flex: 1;">
                  <h3 class="fw-bold  lh-sm mb-2 hero-title-hover text-truncate-2" style="color: var(--heading-color); font-size: 1.25rem;">{{ art.title }}</h3>
                  <p class="text-muted mb-3 text-truncate-2" style="font-size: 0.95rem; line-height: 1.6;">{{ art.excerpt }}</p>
                  <div class="d-flex align-items-center gap-2 mt-auto" style="font-size: 0.85rem;">
                    <span class="fw-bold text-uppercase" style="color: var(--accent-color);">{{ art.cat }}</span>
                    <span class="text-muted">|</span>
                    <span class="text-muted">{{ art.read }}</span>
                  </div>
                </div>
                <div class="rounded-3 overflow-hidden flex-shrink-0 img-zoom-hover" style="width: 220px; height: 140px; background-color: #e5e7eb;">
                  <img :src="getImageUrl(art.img)" :alt="art.title" class="w-100 h-100 object-fit-cover transition-transform">
                </div>
              </router-link>
            </div>
          </div>

        </div>
      </section>

      <!-- ══════════════════════════════════
           ZONE 2: 2 HIGHLIGHT FULL-IMAGE
      ══════════════════════════════════ -->
      <section style="background-color: transparent !important;" class="py-5" v-if="highlights && highlights.length > 0">
        <div class="row g-4">
          <div class="col-md-6" v-for="h in highlights" :key="h.id">
            <router-link :to="`/tin-tuc/${h.id}`" class="news-overlay-card">
              <img :src="getImageUrl(h.img)" :alt="h.title">
              <div class="overlay">
                <div class="meta">
                  <span class="cat">{{ h.cat }}</span>
                  <span class="mx-2" style="opacity: 0.5;">|</span>
                  <span>{{ h.ago }}</span>
                </div>
                <h3 class="title">{{ h.title }}</h3>
              </div>
            </router-link>
          </div>
        </div>
      </section>

      <!-- ══════════════════════════════════
           ZONE 3: LATEST ARTICLES
      ══════════════════════════════════ -->
      <section style="background-color: transparent !important;" class="py-5 border-top" v-if="latestArticles && latestArticles.length > 0">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h3 class="fw-bold  m-0" style="color: var(--heading-color); font-size: 1.5rem; ">Bài Viết Mới Nhất</h3>
          <a href="#" class="fw-bold text-uppercase text-decoration-none d-inline-flex align-items-center gap-2" style="color: var(--accent-color); font-size: 0.85rem; letter-spacing: 0.5px;">
            Xem thêm <i class="bi bi-arrow-right transition-transform" style="transition: transform .2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'"></i>
          </a>
        </div>
        <div class="row g-4">
          <div class="col-6 col-lg-3" v-for="art in latestArticles" :key="art.id">
            <router-link :to="`/tin-tuc/${art.id}`" class="d-flex flex-column text-decoration-none group-hover">
              <div class="rounded-3 overflow-hidden mb-3 img-zoom-hover" style="aspect-ratio: 4/3; background: #e5e7eb;">
                <img :src="getImageUrl(art.img)" :alt="art.title" class="w-100 h-100 object-fit-cover transition-transform">
              </div>
              <div class="d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="d-inline-flex justify-content-center align-items-center rounded-circle bg-dark text-white fw-bold" style="width: 24px; height: 24px; font-size: 0.75rem;">{{ art.avLetter }}</div>
                  <span class="fw-bold " style="color: var(--heading-color); font-size: 0.8rem;">{{ art.author }}</span>
                  <span class="text-muted" style="font-size: 0.8rem;">· {{ art.ago }}</span>
                </div>
                <h5 class="fw-bold  lh-sm mb-2 text-truncate-3 hero-title-hover" style="color: var(--heading-color); font-size: 1rem;">{{ art.title }}</h5>
                <div class="d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                  <span class="fw-bold text-uppercase" style="color: var(--accent-color);">{{ art.cat }}</span>
                  <span class="text-muted">· {{ art.read }}</span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </section>

      <!-- ══════════════════════════════════
           ZONE 4: KIẾN THỨC KỸ THUẬT
      ══════════════════════════════════ -->
      <section style="background-color: transparent !important;" class="py-5 border-top" v-if="(techSmall && techSmall.length > 0) || techBig">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h3 class="fw-bold  m-0" style="color: var(--heading-color); font-size: 1.75rem; ">Kiến Thức Kỹ Thuật</h3>
          <a href="#" class="fw-bold text-decoration-none d-inline-flex align-items-center gap-2" style="color: var(--accent-color); font-size: 0.9rem;">
            Xem thêm <i class="bi bi-arrow-right transition-transform" style="transition: transform .2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'"></i>
          </a>
        </div>
        <div class="row g-5">
          <!-- Small list left (4 articles) -->
          <div class="col-lg-5 d-flex flex-column gap-4">
            <router-link :to="`/tin-tuc/${v.id}`" class="d-flex gap-3 text-decoration-none group-hover" v-for="v in techSmall" :key="v.id">
              <div class="rounded-3 overflow-hidden flex-shrink-0 position-relative img-zoom-hover" style="width: 160px; aspect-ratio: 16/9; background: #e5e7eb;">
                <img :src="getImageUrl(v.img)" :alt="v.title" class="w-100 h-100 object-fit-cover transition-transform">
                <div class="position-absolute top-50 start-50 translate-middle bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; color: #111827;">
                  <i class="bi bi-play-fill fs-5 ms-1"></i>
                </div>
              </div>
              <div class="d-flex flex-column">
                <h5 class="fw-bold  lh-sm mb-2 text-truncate-2 hero-title-hover" style="color: var(--heading-color); font-size: 1.05rem;">{{ v.title }}</h5>
                <p class="text-muted mb-2 text-truncate-2" style="font-size: 0.85rem; line-height: 1.5;">{{ v.excerpt }}</p>
                <div class="d-flex align-items-center gap-2 mt-auto" style="font-size: 0.8rem;">
                  <span class="fw-bold text-uppercase" style="color: var(--accent-color);">{{ v.cat }}</span>
                  <span class="text-muted mx-1 opacity-50">|</span>
                  <span class="text-muted">{{ v.read }}</span>
                </div>
              </div>
            </router-link>
          </div>
          <!-- Big right (1 featured article) -->
          <div class="col-lg-7" v-if="techBig">
            <router-link :to="`/tin-tuc/${techBig.id}`" class="d-flex flex-column text-decoration-none group-hover h-100" :class="{'opacity-50': isFetching}" style="transition: opacity 0.2s;">
              <div class="rounded-4 overflow-hidden mb-4 position-relative img-zoom-hover" style="aspect-ratio: 16/9; background: #e5e7eb;">
                <img :src="getImageUrl(techBig.img)" class="w-100 h-100 object-fit-cover transition-transform" :alt="techBig.title">
                <div class="position-absolute top-50 start-50 translate-middle bg-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 64px; height: 64px; color: #111827;">
                  <i class="bi bi-play-fill fs-2 ms-1"></i>
                </div>
              </div>
              <h3 class="fw-bold  mb-3 hero-title-hover" style="color: var(--heading-color); font-size: 1.6rem; line-height: 1.3;">{{ techBig.title }}</h3>
              <p class="text-muted mb-3 text-truncate-2" style="font-size: 1.05rem; line-height: 1.6;">{{ techBig.excerpt }}</p>
              <div class="d-flex align-items-center gap-2 mt-auto" style="font-size: 0.9rem;">
                <span class="fw-bold text-uppercase" style="color: var(--accent-color);">{{ techBig.cat }}</span>
                <span class="text-muted mx-1 opacity-50">|</span>
                <span class="text-muted">{{ techBig.read }}</span>
              </div>
            </router-link>
          </div>
        </div>
      </section>



    </div><!-- /container -->
  </main>
</template>

<script>
import { ref, computed, watch } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';

export default {
  name: 'TinTuc',
  setup() {
    const activeTab = ref('Tất cả');

    // === Fetch Categories ===
    const { data: categoriesRaw } = useQuery({
      queryKey: ['client-tin-tuc-categories'],
      queryFn: async () => {
        const res = await axiosClient.get('/public/tin-tuc-categories');
        return res.data || [];
      },
      staleTime: 1000 * 60 * 60,
    });

    const tabs = computed(() => {
      const cats = categoriesRaw.value || [];
      return ['Tất cả', ...cats];
    });

    // === Fetch News (re-runs when activeTab changes) ===
    const { data: newsRaw, isFetching } = useQuery({
      queryKey: computed(() => ['client-tin-tuc', activeTab.value]),
      queryFn: async () => {
        const params = { per_page: 24 };
        if (activeTab.value !== 'Tất cả') {
          params.category = activeTab.value;
        }
        const res = await axiosClient.get('/public/tin-tuc', { params });
        // res = { success, data: { current_page, data: [...articles] } }
        return res.data?.data || res.data || [];
      },
      staleTime: 1000 * 60 * 5,
    });

    // === Utility ===
    const formatTimeAgo = (dateString) => {
      if (!dateString) return '';
      const diff = Math.floor((Date.now() - new Date(dateString)) / 1000);
      if (diff < 3600) return `${Math.floor(diff / 60)} phút trước`;
      if (diff < 86400) return `${Math.floor(diff / 3600)} giờ trước`;
      if (diff < 2592000) return `${Math.floor(diff / 86400)} ngày trước`;
      if (diff < 31536000) return `${Math.floor(diff / 2592000)} tháng trước`;
      return `${Math.floor(diff / 31536000)} năm trước`;
    };

    const mapArticle = (art) => ({
      ...art,
      img: art.thumbnail
        ? (art.thumbnail.startsWith('http') ? art.thumbnail : `http://localhost:8000/${art.thumbnail.startsWith('/') ? art.thumbnail.substring(1) : art.thumbnail}`)
        : 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&auto=format&fit=crop',
      cat: art.category || 'Tin tức',
      ago: formatTimeAgo(art.created_at),
      excerpt: art.summary || '',
      read: '3 phút đọc',
      avLetter: (art.author || 'A').charAt(0).toUpperCase(),
      author: art.author || 'Admin',
    });

    // === Slice to zones ===
    // Total ideal: 1+4+2+4+3+1+4 = 19 articles. Falls back gracefully if fewer available.
    const allNews = computed(() => (newsRaw.value || []).map(mapArticle));
    const heroArticle    = computed(() => allNews.value[0] || null);          // [0]
    const sideArticles   = computed(() => allNews.value.slice(1, 5));          // [1-4] = 4 cards
    const highlights     = computed(() => allNews.value.slice(5, 7));          // [5-6]
    const latestArticles = computed(() => allNews.value.slice(7, 11));         // [7-10]
    // Zone 4: techSmall = [11-15] (5 articles), techBig = [16]
    const techSmall      = computed(() => allNews.value.slice(11, 16));
    const techBig        = computed(() => allNews.value[16] || null);
    const promoArticles  = computed(() => allNews.value.slice(15, 19));        // [15-18]

    return {
      activeTab, tabs, isFetching,
      heroArticle, sideArticles, highlights,
      latestArticles, techSmall, techBig, promoArticles,
    };
  },
};
</script>

