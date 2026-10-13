<template>
  <main class="jpm-news-detail main-page bg-white">
    
    <!-- SKELETON LOADING -->
    <div v-if="isFetching" class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="skeleton-line mb-3" style="width: 20%; height: 16px;"></div>
          <div class="skeleton-line mb-4" style="width: 80%; height: 40px;"></div>
          <div class="skeleton-line mb-5" style="width: 40%; height: 16px;"></div>
          <div class="skeleton-box mb-5" style="height: 400px; border-radius: 16px;"></div>
          <div class="skeleton-line mb-3" style="width: 100%; height: 20px;"></div>
          <div class="skeleton-line mb-3" style="width: 95%; height: 20px;"></div>
          <div class="skeleton-line mb-3" style="width: 90%; height: 20px;"></div>
        </div>
      </div>
    </div>

    <!-- ERROR STATE -->
    <div v-else-if="error" class="container py-5 text-center">
      <i class="bi bi-exclamation-triangle text-danger display-1 mb-3"></i>
      <h2 class="fw-bold">Không tìm thấy bài viết</h2>
      <p class="text-secondary mb-4">Bài viết này không tồn tại hoặc đã bị xóa.</p>
      <router-link to="/tin-tuc" class="btn btn-dark rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
      </router-link>
    </div>

    <!-- ARTICLE CONTENT -->
    <article v-else-if="article" class="article-wrapper pb-5">
      
      <!-- ── ARTICLE HEADER ── -->
      <header class="article-header pt-5 pb-4 bg-light">
        <div class="container pt-4">
          <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
              
              <!-- Breadcrumb -->
              <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb mb-0" style="font-size: 0.9rem;">
                  <li class="breadcrumb-item">
                    <router-link to="/" class="text-decoration-none text-muted">Trang chủ</router-link>
                  </li>
                  <li class="breadcrumb-item">
                    <router-link to="/tin-tuc" class="text-decoration-none text-muted">Tin tức & Kinh nghiệm</router-link>
                  </li>
                  <li class="breadcrumb-item active text-dark fw-medium text-truncate" style="max-width: 200px;" aria-current="page">
                    {{ article.title }}
                  </li>
                </ol>
              </nav>

              <!-- Meta Top -->
              <div class="d-flex align-items-center gap-3 mb-3">
                <router-link :to="{ path: '/tin-tuc', query: { category: article.category } }" class="badge bg-danger bg-opacity-10 text-danger rounded-1 px-3 py-2 fw-bold text-uppercase text-decoration-none" style="letter-spacing: 1px; font-size: 0.75rem;">
                  {{ article.category }}
                </router-link>
                <span class="text-muted fw-medium" style="font-size: 0.9rem;">
                  {{ formatDate(article.created_at) }}
                </span>
              </div>

              <!-- Title -->
              <h1 class="fw-bolder text-dark mb-4 article-main-title" style="letter-spacing: -1px; line-height: 1.3;">
                {{ article.title }}
              </h1>

              <!-- Meta Bottom -->
              <div class="d-flex align-items-center gap-4 text-secondary pb-4 border-bottom" style="font-size: 0.9rem;">
                <div v-if="article.author" class="d-flex align-items-center gap-2">
                  <div class="author-avatar bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    {{ article.author.charAt(0).toUpperCase() }}
                  </div>
                  <span class="fw-medium text-dark">{{ article.author }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-eye"></i>
                  <span>{{ Number(article.views || 0).toLocaleString('vi-VN') }} lượt xem</span>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                  <span class="fw-medium">Chia sẻ:</span>
                  <button class="btn btn-sm btn-light rounded-circle btn-share" title="Chia sẻ Facebook" @click="shareLink('facebook')"><i class="bi bi-facebook text-primary"></i></button>
                  <button class="btn btn-sm btn-light rounded-circle btn-share" title="Sao chép liên kết" @click="copyLink"><i class="bi bi-link-45deg text-dark"></i></button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </header>

      <!-- ── ARTICLE BODY ── -->
      <div class="container mt-n5 position-relative z-1">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <!-- Hero Image -->
            <div class="article-hero-img rounded-4 overflow-hidden shadow-sm mb-5 bg-white p-2">
              <img 
                :src="getImageUrl(article.thumbnail) || fallback" 
                :alt="article.title"
                class="w-100 h-100 object-fit-cover rounded-3"
                @error="$event.target.src = fallback"
              >
            </div>
          </div>
          
          <div class="col-lg-8">
            <!-- Summary (Sapo) -->
            <p class="article-sapo fw-medium text-dark mb-5" style="font-size: 1.25rem; line-height: 1.8;">
              {{ article.summary }}
            </p>

            <!-- Main Content (HTML) -->
            <div class="article-html-content text-secondary" style="font-size: 1.1rem; line-height: 1.8;" v-html="safeHtmlContent">
            </div>

            <!-- YouTube Video -->
            <div v-if="article.youtube_url && getYoutubeEmbedUrl(article.youtube_url)" class="mt-5 rounded-4 overflow-hidden shadow-sm">
              <iframe 
                width="100%" 
                height="450" 
                :src="getYoutubeEmbedUrl(article.youtube_url)" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
              </iframe>
            </div>

            <!-- Gallery -->
            <div v-if="galleryImages && galleryImages.length > 0" class="mt-5">
              <h4 class="fw-bold text-dark mb-4">Thư viện ảnh phụ</h4>
              <div class="row g-3">
                <div class="col-6 col-md-4" v-for="(img, idx) in galleryImages" :key="idx">
                  <div class="rounded-3 overflow-hidden shadow-sm img-zoom-hover" style="aspect-ratio: 4/3; cursor: pointer;">
                    <img :src="getImageUrl(img)" class="w-100 h-100 object-fit-cover transition-transform" data-bs-toggle="modal" :data-bs-target="'#galleryModal' + idx" @error="$event.target.src = fallback">
                  </div>
                  
                  <!-- Modal cho từng ảnh -->
                  <div class="modal fade" :id="'galleryModal' + idx" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                      <div class="modal-content bg-transparent border-0">
                        <div class="modal-header border-0 pb-0">
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                          <img :src="getImageUrl(img)" class="img-fluid rounded-3" style="max-height: 80vh;" @error="$event.target.src = fallback">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tags -->
            <div class="mt-5 pt-4 border-top d-flex flex-wrap gap-2 align-items-center">
              <i class="bi bi-tags text-muted me-2"></i>
              <router-link v-if="article.category" :to="{ path: '/tin-tuc', query: { category: article.category } }" class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 fw-medium rounded-pill text-decoration-none">
                {{ article.category }}
              </router-link>
              <router-link :to="{ path: '/tin-tuc', query: { search: 'Động cơ' } }" class="badge bg-light text-dark border px-3 py-2 fw-normal rounded-pill text-decoration-none">Động cơ thủy</router-link>
              <router-link :to="{ path: '/tin-tuc', query: { category: 'Bảo Dưỡng Động Cơ' } }" class="badge bg-light text-dark border px-3 py-2 fw-normal rounded-pill text-decoration-none">Bảo dưỡng</router-link>
              <router-link to="/tin-tuc" class="badge bg-light text-dark border px-3 py-2 fw-normal rounded-pill text-decoration-none">JPM Marine</router-link>
            </div>
          </div>
        </div>
      </div>
    </article>

    <!-- ── RELATED ARTICLES ── -->
    <section v-if="relatedArticles && relatedArticles.length > 0" class="related-section bg-light py-5 border-top">
      <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-5">
          <h3 class="fw-bolder m-0" style="letter-spacing: -0.5px;">Bài viết cùng chuyên mục</h3>
          <router-link :to="{ path: '/tin-tuc', query: article?.category ? { category: article.category } : {} }" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium">
            Xem tất cả
          </router-link>
        </div>
        
        <div class="row g-5">
          <div class="col-12 col-md-4" v-for="rel in relatedArticles" :key="rel.id">
            <article class="article-card">
              <router-link :to="`/tin-tuc/${rel.id}`" class="d-block overflow-hidden rounded-3 mb-3 shadow-sm" style="aspect-ratio: 4/3;">
                <img
                  :src="getImageUrl(rel.thumbnail) || fallback"
                  :alt="rel.title"
                  class="w-100 h-100 object-fit-cover hover-zoom"
                  @error="$event.target.src = fallback"
                >
              </router-link>
              <div class="mb-2">
                <span class="text-danger fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                  {{ rel.category }}
                </span>
              </div>
              <h4 class="fw-bold mb-0 article-title" style="font-size: 1.1rem;">
                <router-link :to="`/tin-tuc/${rel.id}`" class="text-dark text-decoration-none hover-text-danger">
                  {{ rel.title }}
                </router-link>
              </h4>
            </article>
          </div>
        </div>
      </div>
    </section>

  </main>
</template>

<script>
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';

const toaster = createToaster({ position: 'top-right', duration: 3000 });
const FALLBACK = 'https://images.unsplash.com/photo-1502680390469-be75c86b636f?w=800&auto=format&fit=crop&q=60';

// Mock content in case DB content is empty or plain text
const MOCK_HTML = `
  <p>Thường xuyên kiểm tra và bảo dưỡng động cơ là chìa khóa để đảm bảo hiệu suất hoạt động và kéo dài tuổi thọ cho tàu thuyền của bạn. Dưới đây là những bước quan trọng mà bạn không nên bỏ qua.</p>
  <h3 class="fw-bold text-dark mt-5 mb-3">1. Kiểm tra dầu nhớt định kỳ</h3>
  <p>Dầu nhớt đóng vai trò bôi trơn và làm mát các chi tiết chuyển động bên trong động cơ. Việc thiếu dầu hoặc dầu quá bẩn sẽ dẫn đến ma sát cao, gây mài mòn nhanh chóng.</p>
  <ul>
    <li>Kiểm tra mức dầu trước mỗi chuyến đi.</li>
    <li>Thay dầu định kỳ theo khuyến cáo của nhà sản xuất (thường là sau mỗi 100 giờ hoạt động).</li>
  </ul>
  <h3 class="fw-bold text-dark mt-5 mb-3">2. Vệ sinh hệ thống làm mát</h3>
  <p>Hệ thống làm mát bằng nước biển rất dễ bị tắc nghẽn bởi muối, cát và rác. Nếu không được vệ sinh, động cơ sẽ dễ bị quá nhiệt (overheating).</p>
  <p>Hãy đảm bảo xả nước ngọt qua hệ thống làm mát sau mỗi lần đi biển, đặc biệt là khi tàu hoạt động ở những khu vực nước mặn hoặc bùn lầy.</p>
  <blockquote class="border-start border-4 border-danger ps-4 py-2 my-4 bg-white shadow-sm rounded-end fst-italic">
    "Việc bảo dưỡng phòng ngừa luôn rẻ hơn rất nhiều so với chi phí sửa chữa thay thế khi hỏng hóc xảy ra giữa biển khơi."
  </blockquote>
  <p>Đội ngũ kỹ thuật của <strong>JPM Marine</strong> luôn sẵn sàng tư vấn và hỗ trợ bạn trong việc lựa chọn các phụ tùng bảo dưỡng chính hãng tốt nhất.</p>
`;

export default {
  name: 'ChiTietTinTuc',
  setup() {
    const route = useRoute();
    const articleId = computed(() => route.params.id);

    const { data: fetchResult, isFetching, error, refetch } = useQuery({
      queryKey: ['public-news-detail-v2', articleId],
      queryFn: () => axiosClient.get(`/public/tin-tuc/${articleId.value}`),
      staleTime: 1000 * 60 * 5,
    });

    // Khi route thay đổi (click bài liên quan), scroll lên top và fetch lại
    watch(articleId, () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
      refetch();
    });

    const article = computed(() => fetchResult.value?.data ?? null);
    const relatedArticles = computed(() => fetchResult.value?.related ?? []);

    const safeHtmlContent = computed(() => {
      const raw = article.value?.content || MOCK_HTML;
      return String(raw).replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    });

    const getYoutubeEmbedUrl = (url) => {
      if (!url) return null;
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&]{11})/);
      return match ? 'https://www.youtube.com/embed/' + match[1] : null;
    };
    
    const galleryImages = computed(() => {
      if (!article.value || !article.value.gallery) return [];
      try {
        const arr = typeof article.value.gallery === 'string' ? JSON.parse(article.value.gallery) : article.value.gallery;
        return Array.isArray(arr) ? arr : [];
      } catch (e) { return []; }
    });

    const formatDate = (d) => {
      if (!d) return '';
      const date = new Date(d);
      return `${date.getDate().toString().padStart(2, '0')} Th${(date.getMonth() + 1).toString().padStart(2, '0')}, ${date.getFullYear()}`;
    };

    const copyLink = () => {
      navigator.clipboard.writeText(window.location.href);
      toaster.success('Đã copy đường dẫn bài viết!');
    };

    const shareLink = (platform) => {
      const url = encodeURIComponent(window.location.href);
      if (platform === 'facebook') {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
      }
    };

    return {
      article,
      relatedArticles,
      safeHtmlContent,
      galleryImages,
      getYoutubeEmbedUrl,
      isFetching,
      error,
      formatDate,
      copyLink,
      shareLink,
      fallback: FALLBACK,
      defaultContent: MOCK_HTML
    };
  }
};
</script>

<style scoped>
/* ── TYPOGRAPHY & LAYOUT ── */
.article-main-title {
  font-size: clamp(2rem, 4vw, 3rem);
}
.article-hero-img {
  aspect-ratio: 21/9;
}
@media (max-width: 768px) {
  .article-hero-img { aspect-ratio: 16/9; }
}

/* ── HTML CONTENT STYLING ── */
.article-html-content :deep(h2),
.article-html-content :deep(h3),
.article-html-content :deep(h4) {
  color: #111827;
  font-weight: 700;
  margin-top: 2rem;
  margin-bottom: 1rem;
}
.article-html-content :deep(p) {
  margin-bottom: 1.5rem;
}
.article-html-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 2rem 0;
}
.article-html-content :deep(ul),
.article-html-content :deep(ol) {
  margin-bottom: 1.5rem;
  padding-left: 1.5rem;
}
.article-html-content :deep(li) {
  margin-bottom: 0.5rem;
}
.article-html-content :deep(a) {
  color: #f82249;
  text-decoration: underline;
  text-underline-offset: 3px;
}
.article-html-content :deep(a:hover) {
  color: #c8102e;
}

/* ── BUTTONS & HOVERS ── */
.btn-share {
  width: 36px; height: 36px;
  display: inline-flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.btn-share:hover {
  background-color: #e5e7eb;
  transform: translateY(-2px);
}
.hover-zoom {
  transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.article-card:hover .hover-zoom {
  transform: scale(1.05);
}
.hover-text-danger {
  transition: color 0.2s;
}
.hover-text-danger:hover {
  color: #f82249 !important;
}
.article-title {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>




