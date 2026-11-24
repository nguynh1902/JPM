<template>
  <div class="jpm-news-detail main-page inner-page-section">
    
    <!-- SKELETON LOADING -->
    <div v-if="isFetching" class="container py-4" style="max-width: 1120px;">
      <div class="skeleton-line mb-4" style="width: 120px; height: 16px;"></div>
      <div class="skeleton-line mb-2" style="width: 140px; height: 14px;"></div>
      <div class="skeleton-line mb-3" style="width: 75%; height: 42px;"></div>
      <div class="skeleton-line mb-4" style="width: 60%; height: 18px;"></div>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="skeleton-line" style="width: 180px; height: 36px;"></div>
        <div class="skeleton-line" style="width: 100px; height: 16px;"></div>
      </div>
      <div class="skeleton-box mb-5" style="height: 440px; border-radius: 12px;"></div>
      <div class="row g-5">
        <div class="col-lg-8">
          <div class="skeleton-line mb-3" style="width: 100%; height: 18px;"></div>
          <div class="skeleton-line mb-3" style="width: 95%; height: 18px;"></div>
          <div class="skeleton-line mb-3" style="width: 90%; height: 18px;"></div>
        </div>
        <div class="col-lg-4">
          <div class="skeleton-box" style="height: 280px; border-radius: 12px;"></div>
        </div>
      </div>
    </div>

    <!-- ERROR STATE -->
    <div v-else-if="error" class="container py-5 text-center" style="max-width: 1120px;">
      <i class="bi bi-exclamation-triangle text-danger display-1 mb-3"></i>
      <h2 class="fw-bold" style="color: var(--jpm-color-navy-dark, #0A1128);">Không tìm thấy bài viết</h2>
      <p class="text-secondary mb-4">Bài viết này không tồn tại hoặc đã bị xóa.</p>
      <router-link to="/tin-tuc" class="btn btn-outline-dark px-4" style="border-radius: var(--jpm-radius-control, 8px); font-weight: 600;">
        <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
      </router-link>
    </div>

    <!-- ARTICLE CONTENT (WIREFRAME LAYOUT) -->
    <article v-else-if="article" class="article-wrapper py-3 py-lg-4">
      <div class="container" style="max-width: 1120px;">
        
        <!-- 1. TOP BACK LINK (< All Blog Posts) -->
        <div class="mb-4">
          <router-link to="/tin-tuc" class="back-link text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="bi bi-chevron-left" style="font-size: 0.75rem;"></i>
            <span>Tất cả bài viết</span>
          </router-link>
        </div>

        <!-- 2. ARTICLE HEADER -->
        <header class="article-header mb-4">
          <!-- Category Label -->
          <div class="mb-2">
            <router-link
              :to="{ path: '/tin-tuc', query: { category: article.category } }"
              class="article-category-label text-decoration-none text-danger fw-semibold"
            >
              {{ article.category }}
            </router-link>
          </div>

          <!-- Main Title (H1) -->
          <h1 class="article-main-title fw-bold mb-3" style="color: var(--jpm-color-navy-dark, #0A1128);">
            {{ article.title }}
          </h1>

          <!-- Subtitle / Summary (Sapo) -->
          <p v-if="article.summary" class="article-subtitle text-secondary mb-4">
            {{ article.summary }}
          </p>

          <!-- Author & Date Row -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-1">
            <div class="d-flex align-items-center gap-3">
              <img
                :src="getAuthorAvatar(article.author)"
                :alt="article.author || 'Chuyên gia JPM'"
                class="author-avatar rounded-circle object-fit-cover"
              >
              <div>
                <div class="fw-bold" style="font-size: 0.9rem; line-height: 1.25; color: var(--jpm-color-navy-dark, #0A1128);">
                  {{ article.author || 'Kỹ sư Trưởng JPM' }}
                </div>
                <div class="text-muted" style="font-size: 0.78rem;">
                  {{ getAuthorRole(article.author, article.category) }}
                </div>
              </div>
            </div>

            <div class="text-secondary d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.85rem;">
              <span>{{ formatDate(article.created_at) }}</span>
              <span class="text-muted opacity-50">•</span>
              <span><i class="bi bi-eye me-1"></i>{{ Number(article.views || 0).toLocaleString('vi-VN') }} lượt xem</span>
              <template v-if="hasVideo">
                <span class="text-muted opacity-50">•</span>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 rounded-pill py-0 px-2 fw-semibold"
                  style="font-size: 0.78rem; height: 26px;"
                  @click="selectVideoMedia"
                  title="Chuyển sang xem video thực tế bài viết"
                >
                  <i class="bi bi-play-circle-fill"></i> Video thực tế
                </button>
              </template>
            </div>
          </div>
        </header>

        <!-- 3. HERO MEDIA HUB (HỢP NHẤT VIDEO THỰC TẾ & SLIDER ĐA ẢNH CHI TIẾT) -->
        <div class="article-hero-wrapper mb-5">
          <div class="article-hero-img rounded-3 overflow-hidden position-relative shadow-sm bg-black">
            
            <!-- A. HIỂN THỊ VIDEO THỰC TẾ (NẰM TRỰC TIẾP TRONG KHUNG HERO KHI ĐANG CHỌN VIDEO) -->
            <div
              v-if="currentMedia.type === 'video'"
              class="w-100 h-100 position-relative d-flex align-items-center justify-content-center bg-black"
            >
              <iframe
                :src="currentMedia.embedUrl"
                :title="currentMedia.title"
                class="w-100 h-100 border-0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
            </div>

            <!-- B. HIỂN THỊ ẢNH CHI TIẾT (SẠCH SẼ 100%, KHÔNG CÓ NÚT VIDEO NÀO ĐÈ LÊN MẶT ẢNH) -->
            <div
              v-else
              class="w-100 h-100 position-relative"
            >
              <img
                :src="getImageUrl(currentMedia.url) || fallback"
                :alt="article.title"
                class="w-100 h-100 object-fit-cover transition-opacity hero-img-active cursor-pointer"
                data-bs-toggle="modal"
                data-bs-target="#heroLightboxModal"
                @error="$event.target.src = fallback"
              >

              <!-- Nút bấm phóng to ảnh Lightbox ở góc trên bên phải (chỉ hiện khi xem ảnh) -->
              <button
                type="button"
                class="hero-zoom-btn position-absolute top-0 end-0 m-3 rounded-circle d-flex align-items-center justify-content-center"
                data-bs-toggle="modal"
                data-bs-target="#heroLightboxModal"
                title="Phóng to ảnh"
                aria-label="Phóng to ảnh"
              >
                <i class="bi bi-arrows-fullscreen"></i>
              </button>
            </div>

            <!-- Carousel Navigation Buttons (Xuất hiện khi bài viết có từ 2 media trở lên) -->
            <template v-if="heroMediaList.length > 1">
              <button
                type="button"
                class="hero-nav-btn hero-nav-prev"
                @click.stop="prevHeroMedia"
                title="Lùi lại"
                aria-label="Previous media"
              >
                <i class="bi bi-chevron-left"></i>
              </button>
              <button
                type="button"
                class="hero-nav-btn hero-nav-next"
                @click.stop="nextHeroMedia"
                title="Kế tiếp"
                aria-label="Next media"
              >
                <i class="bi bi-chevron-right"></i>
              </button>

              <!-- Huy hiệu số đếm & ngữ cảnh media (Góc dưới bên phải) -->
              <div class="hero-counter-badge position-absolute bottom-0 end-0 m-3 px-3 py-1 rounded-pill d-flex align-items-center gap-1 shadow-sm">
                <template v-if="currentMedia.type === 'video'">
                  <i class="bi bi-play-circle-fill text-danger"></i>
                  <span>Video thực tế (1 / {{ heroMediaList.length }})</span>
                </template>
                <template v-else>
                  <i class="bi bi-camera"></i>
                  <span>Ảnh {{ currentHeroIndex + 1 }} / {{ heroMediaList.length }}</span>
                </template>
              </div>

              <!-- Dải chấm chỉ số (Dots) -->
              <div class="hero-dots position-absolute bottom-0 start-50 translate-middle-x mb-3 d-flex gap-2">
                <button
                  v-for="(item, idx) in heroMediaList"
                  :key="idx"
                  type="button"
                  class="hero-dot-btn"
                  :class="{ active: currentHeroIndex === idx, 'is-video-dot': item.type === 'video' }"
                  @click.stop="setHeroMedia(idx)"
                  :title="item.type === 'video' ? 'Xem Video thực tế' : `Xem ảnh ${idx + 1}`"
                ></button>
              </div>
            </template>
          </div>

          <!-- Dải thumbnail nhỏ bên dưới nếu có từ 2 media trở lên -->
          <div v-if="heroMediaList.length > 1" class="hero-thumbs-row mt-2 d-flex gap-2 overflow-x-auto pb-1 scrollbar-hidden">
            <div
              v-for="(item, idx) in heroMediaList"
              :key="idx"
              class="hero-thumb-item rounded-2 overflow-hidden flex-shrink-0 cursor-pointer position-relative"
              :class="{ active: currentHeroIndex === idx, 'is-video-thumb': item.type === 'video' }"
              @click="setHeroMedia(idx)"
            >
              <!-- 1. Thumbnail của Video: có icon Play đỏ và chữ VIDEO nổi bật -->
              <template v-if="item.type === 'video'">
                <img :src="getImageUrl(item.thumbnail) || fallback" class="w-100 h-100 object-fit-cover" @error="$event.target.src = fallback">
                <div class="thumb-video-overlay">
                  <div class="thumb-video-play-btn rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-play-fill"></i>
                  </div>
                  <span class="thumb-video-text mt-1">VIDEO</span>
                </div>
              </template>

              <!-- 2. Thumbnail của Ảnh chi tiết -->
              <template v-else>
                <img :src="getImageUrl(item.url) || fallback" class="w-100 h-100 object-fit-cover" @error="$event.target.src = fallback">
              </template>
            </div>
          </div>

          <!-- Modal Lightbox toàn màn hình cho Ảnh tĩnh -->
          <div class="modal fade" id="heroLightboxModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0 justify-content-between align-items-center">
                  <span class="text-white small fw-medium" v-if="heroOnlyImages.length > 1">
                    Ảnh {{ currentImageOnlyIndex + 1 }} / {{ heroOnlyImages.length }}
                  </span>
                  <span v-else></span>
                  <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center position-relative py-3">
                  <img
                    :src="getImageUrl(currentMedia.type === 'image' ? currentMedia.url : (heroOnlyImages[0]?.url || fallback)) || fallback"
                    class="img-fluid rounded-3 shadow-lg"
                    style="max-height: 85vh; object-fit: contain;"
                    @error="$event.target.src = fallback"
                  >
                  <!-- Nút Next / Prev trong Modal phóng to ảnh -->
                  <template v-if="heroOnlyImages.length > 1">
                    <button
                      type="button"
                      class="lightbox-nav-btn lightbox-nav-prev"
                      @click.stop="prevImageOnly"
                      title="Ảnh trước"
                    >
                      <i class="bi bi-chevron-left fs-3"></i>
                    </button>
                    <button
                      type="button"
                      class="lightbox-nav-btn lightbox-nav-next"
                      @click.stop="nextImageOnly"
                      title="Ảnh tiếp theo"
                    >
                      <i class="bi bi-chevron-right fs-3"></i>
                    </button>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. TWO-COLUMN BODY: LEFT ARTICLE CONTENT + RIGHT SIDEBAR -->
        <div class="row g-5 align-items-start">
          
          <!-- LEFT COLUMN: MAIN ARTICLE CONTENT -->
          <div class="col-lg-8">
            <div
              class="article-html-content"
              v-html="processedHtmlContent"
            ></div>



            <!-- Thư viện ảnh phụ (Gallery Lightbox) -->
            <div v-if="galleryImages && galleryImages.length > 0" class="mt-5 pt-3 border-top">
              <h4 class="fw-bold mb-3" style="font-size: 1.15rem; color: var(--jpm-color-navy-dark, #0A1128);">Hình ảnh thực tế đính kèm</h4>
              <div class="row g-3">
                <div class="col-6 col-md-4" v-for="(img, idx) in galleryImages" :key="idx">
                  <div class="rounded-3 overflow-hidden shadow-sm gallery-thumb" style="aspect-ratio: 4/3; cursor: pointer;">
                    <img
                      :src="getImageUrl(img)"
                      class="w-100 h-100 object-fit-cover hover-zoom"
                      data-bs-toggle="modal"
                      :data-bs-target="'#galleryModal' + idx"
                      @error="$event.target.src = fallback"
                    >
                  </div>

                  <!-- Modal phóng to ảnh -->
                  <div class="modal fade" :id="'galleryModal' + idx" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                      <div class="modal-content bg-transparent border-0">
                        <div class="modal-header border-0 pb-0">
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                          <img :src="getImageUrl(img)" class="img-fluid rounded-3" style="max-height: 82vh;" @error="$event.target.src = fallback">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Từ khóa / Tags (DEC-01: 6px bo góc) -->
            <div class="mt-5 pt-4 border-top d-flex flex-wrap gap-2 align-items-center">
              <span class="text-muted small fw-semibold me-1">Chủ đề:</span>
              <router-link
                v-if="article.category"
                :to="{ path: '/tin-tuc', query: { category: article.category } }"
                class="tag-badge text-decoration-none"
              >
                {{ article.category }}
              </router-link>
              <router-link :to="{ path: '/tin-tuc', query: { search: 'Động cơ' } }" class="tag-badge text-decoration-none">
                Động cơ thủy
              </router-link>
              <router-link :to="{ path: '/tin-tuc', query: { category: 'Bảo Dưỡng Động Cơ' } }" class="tag-badge text-decoration-none">
                Bảo dưỡng định kỳ
              </router-link>
              <router-link to="/tra-cuu" class="tag-badge text-decoration-none">
                Phụ tùng OEM JPM
              </router-link>
            </div>
          </div>

          <!-- RIGHT COLUMN: STICKY SIDEBAR (TOC + SHARE + CTA CARD) -->
          <aside class="col-lg-4">
            <div class="sidebar-sticky">
              
              <!-- Table of Contents (Chỉ hiện khi bài viết có các thẻ đề mục H2/H3) -->
              <div class="toc-widget mb-4 pb-2" v-if="tocItems && tocItems.length > 0">
                <div class="sidebar-label fw-bold mb-3" style="color: var(--jpm-color-navy-dark, #0A1128);">
                  Mục lục bài viết
                </div>
                <ul class="toc-list list-unstyled m-0">
                  <li
                    v-for="(item, index) in tocItems"
                    :key="item.id"
                    class="toc-item"
                    :class="{ active: activeTocId === item.id || (!activeTocId && index === 0) }"
                  >
                    <a
                      :href="`#${item.id}`"
                      class="toc-link text-decoration-none"
                      @click.prevent="scrollToHeading(item.id)"
                    >
                      {{ item.text }}
                    </a>
                  </li>
                </ul>
              </div>

              <!-- Share the article -->
              <div class="share-widget mb-4 pb-2">
                <div class="sidebar-label fw-bold mb-3" style="color: var(--jpm-color-navy-dark, #0A1128);">
                  Chia sẻ bài viết
                </div>
                <div class="d-flex align-items-center gap-2">
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ LinkedIn"
                    @click="shareLink('linkedin')"
                  >
                    <i class="bi bi-linkedin"></i>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ X (Twitter)"
                    @click="shareLink('twitter')"
                  >
                    <i class="bi bi-twitter-x"></i>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ Facebook"
                    @click="shareLink('facebook')"
                  >
                    <i class="bi bi-facebook"></i>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Sao chép liên kết"
                    @click="copyLink"
                  >
                    <i class="bi bi-globe2"></i>
                  </button>
                </div>
              </div>

              <!-- Promotional CTA Box (Chuẩn Hóa 12px Card & Nút Đỏ Marine 8px) -->
              <div class="promo-cta-card rounded-3 p-4">
                <h4 class="fw-bold mb-2" style="font-size: 1.15rem; line-height: 1.35; color: var(--jpm-color-navy-dark, #0A1128);">
                  Vận Hành Bền Bỉ, An Tâm Vươn Khơi
                </h4>
                <p class="text-secondary mb-4" style="font-size: 0.84rem; line-height: 1.6;">
                  Tra cứu sơ đồ bóc tách (Exploded View) và đặt mua phụ tùng chính hãng Yamaha, Yanmar, Honda Marine nhanh chóng với độ chính xác tuyệt đối từ JPM.
                </p>
                <router-link
                  to="/tra-cuu"
                  class="btn btn-danger w-100 py-2 fw-semibold"
                  style="border-radius: var(--jpm-radius-control, 8px); background-color: var(--jpm-color-red-marine, #F82249); font-size: 0.88rem;"
                >
                  Khám phá ngay
                </router-link>
              </div>

            </div>
          </aside>

        </div>

        <!-- 5. RELATED BLOGS SECTION (CHUẨN HÓA ĐỒNG BỘ 100% THEO THẺ CỬA HÀNG & JPM DESIGN SYSTEM) -->
        <div v-if="relatedArticles && relatedArticles.length > 0" class="related-blogs-section mt-5 pt-5 border-top bg-white">
          <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <h4 class="fw-bold mb-0 text-capitalize" style="font-size: 1.25rem;">
              Bài viết liên quan
            </h4>
            <router-link
              to="/tin-tuc"
              class="jpm-more-link"
            >
              Xem thêm <i class="bi bi-arrow-right"></i>
            </router-link>
          </div>

          <div class="row g-4">
            <div
              class="col-12 col-md-4"
              v-for="rel in relatedArticles"
              :key="rel.id"
            >
              <article class="card h-100 related-blog-card product-card jpm-card position-relative overflow-hidden">
                <!-- 1. Ảnh tràn mép trên (Edge-to-edge), không padding bao quanh -->
                <div class="card-img-wrapper position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center">
                  <router-link :to="`/tin-tuc/${rel.id}`" class="d-block w-100 h-100">
                    <img
                      :src="getImageUrl(rel.thumbnail) || fallback"
                      :alt="rel.title"
                      class="card-img-top w-100 transition-transform card-img-product"
                      style="aspect-ratio: 16 / 10; object-fit: cover;"
                      @error="$event.target.src = fallback"
                      loading="lazy"
                    >
                  </router-link>
                </div>

                <!-- 2. Thân card (padding p-3) -->
                <div class="card-body text-start p-3 d-flex flex-column bg-white">
                  <!-- Tầng 1: Chuyên mục chữ đỏ Marine (#F82249) chuẩn bài viết Tin tức JPM -->
                  <div
                    class="news-category-label mb-1 text-truncate"
                    :title="rel.category"
                  >
                    {{ rel.category }}
                  </div>

                  <!-- Tầng 2: Tiêu đề bài viết - Font Raleway, màu Deep Navy #0A1128 -->
                  <h5 class="card-title mb-2">
                    <router-link :to="`/tin-tuc/${rel.id}`" class="text-decoration-none stretched-link text-truncate-2">
                      {{ rel.title }}
                    </router-link>
                  </h5>

                  <!-- Tầng 3: Ngày đăng kèm icon lịch sạch sẽ -->
                  <div class="mt-auto pt-2 news-meta">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ formatDate(rel.created_at) }}</span>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>

      </div>
    </article>

  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';

const toaster = createToaster({ position: 'top-right', duration: 3000 });
const FALLBACK = 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1000&auto=format&fit=crop&q=80';

const AUTHOR_AVATARS = [
  'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
];

/**
 * Tạo nội dung bài viết có cấu trúc đầy đủ (Mở đầu + các mục lớn H2/H3 + Ví dụ + Kết luận)
 * khi bài viết trong DB chỉ mới có 1 đoạn văn ngắn từ Seeder.
 */
function buildStructuredArticleHtml(article, rawHtml) {
  const cleanIntro = rawHtml && rawHtml.trim()
    ? rawHtml
    : `<p>${article?.summary || 'Trong ngành hàng hải hiện đại, việc nắm vững quy trình kỹ thuật và lựa chọn phụ tùng đạt chuẩn OEM đóng vai trò quyết định đến độ bền động cơ cũng như sự an toàn của mỗi chuyến hải trình.'}</p>`;

  const category = article?.category || 'Công Nghệ Hàng Hải';

  if (category === 'Bảo Dưỡng Động Cơ') {
    return `
      ${cleanIntro}
      <p>Các dòng động cơ thủy hiện đại hoạt động trong môi trường nước mặn khắc nghiệt luôn đòi hỏi chế độ chăm sóc nghiêm ngặt. Sự chênh lệch nhiệt độ lớn, độ ẩm cao và hàm lượng muối biển dễ dàng gây ăn mòn điện hóa nếu chủ tàu chỉ áp dụng các biện pháp bảo trì truyền thống.</p>
      <p>Kết quả là khoảng cách giữa hiệu suất thiết kế của nhà sản xuất và tình trạng vận hành thực tế ngày càng bị thu hẹp nếu thiếu quy trình kiểm tra định kỳ bài bản.</p>

      <h2>Tại sao phương pháp bảo dưỡng truyền thống chưa đủ hiệu quả?</h2>

      <h3>1. Hệ thống làm mát và nguy cơ kết tủa muối biển:</h3>
      <p>Nhiều chủ tàu vẫn giữ thói quen chỉ kiểm tra cánh bơm nước (impeller) khi động cơ đã báo quá nhiệt. Thực tế, muối biển và cặn khoáng tích tụ âm thầm bên trong áo nước làm mát và bộ sinh hàn, làm giảm đáng kể khả năng giải nhiệt ngay cả khi lưu lượng nước đầu ra vẫn có vẻ bình thường.</p>
      <p class="article-example">Ví dụ: Một động cơ máy ngoài 150HP hoạt động liên tục 200 giờ tại vùng biển nước mặn nhưng không được súc rửa nước ngọt sau mỗi chuyến đi có thể giảm tới 30% tiết diện lưu thông nhiệt trong đường ống.</p>

      <h3>2. Bỏ qua chu kỳ thay thế lọc nhiên liệu tách nước:</h3>
      <p>Nhiên liệu lưu trữ tại cảng hoặc trong bồn chứa tàu dài ngày rất dễ nhiễm hơi nước ngưng tụ. Nếu hệ thống lọc tách nước không được xả cặn và thay lõi đúng định kỳ 100 giờ máy, nước lẫn vào buồng đốt sẽ gây rỉ sét kim phun điện tử áp suất cao.</p>
      <p class="article-example">Ví dụ: Chỉ một lượng nhỏ nước nhiễm vào hệ thống Common Rail trên động cơ Diesel hàng hải có thể làm kẹt kim phun, dẫn đến chi phí đại tu lớn gấp nhiều lần giá trị của một lõi lọc chính hãng.</p>

      <h3>3. Thiếu dữ liệu chẩn đoán sớm và phụ tùng đồng bộ:</h3>
      <p>Việc chờ đợi linh kiện hỏng hoàn toàn mới thay thế khiến tàu dễ gặp sự cố đột xuất giữa khơi. Xu hướng bảo dưỡng hiện đại yêu cầu kết hợp đọc thông số ECU động cơ, kiểm tra kẽm chống ăn mòn (Anode) và thay thế đồng bộ gioăng phớt chính hãng theo số giờ vận hành.</p>
      <p class="article-example">Ví dụ: Kiểm tra định kỳ độ mòn của kẽm hy sinh và dầu hộp số chân vịt giúp phát hiện sớm tình trạng hở phớt trục chân vịt trước khi nước biển xâm nhập làm hỏng bánh răng truyền động.</p>

      <h2>Kết luận: Nâng tầm quy trình chăm sóc máy thủy</h2>
      <p>Bảo dưỡng động cơ hàng hải không chỉ đơn thuần là thay dầu nhớt khi đến hạn, mà là sự kết hợp đồng bộ giữa quy trình kiểm tra phòng ngừa, thiết bị chẩn đoán chính xác và nguồn linh kiện OEM đạt chuẩn.</p>
      <p>Tương lai của vận hành tàu thuyền an toàn nằm ở sự chủ động: kiểm soát thông số kỹ thuật, bảo vệ hệ thống trước tác nhân ăn mòn và đồng hành cùng đội ngũ kỹ sư chuyên nghiệp từ JPM Marine.</p>
    `;
  }

  return `
    ${cleanIntro}
    <p>Hệ thống thiết bị và phụ tùng tàu thuyền truyền thống vốn được thiết kế xoay quanh các tiêu chuẩn vận hành cơ khí độc lập. Tuy nhiên, sự phát triển mạnh mẽ của động cơ điều khiển điện tử, thiết bị định vị hàng hải thông minh và tiêu chuẩn tiết kiệm nhiên liệu đã đặt ra những yêu cầu hoàn toàn mới cho các chủ tàu và xưởng dịch vụ.</p>
    <p>Hệ quả là một khoảng cách lớn xuất hiện giữa cách thức quản lý vật tư thủ công trước đây với nhu cầu tra cứu chính xác, đồng bộ hóa linh kiện nhanh chóng trong ngành hàng hải hiện đại.</p>

    <h2>Những thách thức kỹ thuật thường gặp trong vận hành thực tế</h2>

    <h3>1. Sai lệch mã phụ tùng giữa các đời động cơ (Model Year):</h3>
    <p>Nhiều dòng động cơ thủy có vẻ ngoài tương đồng nhưng lại sử dụng các chi tiết cơ khí và cảm biến khác nhau tùy theo năm sản xuất hoặc thị trường phân phối. Việc đặt hàng chỉ dựa vào tên gọi thông thường hoặc đo đạc thủ công rất dễ dẫn đến lắp lẫn không tương thích.</p>
    <p class="article-example">Ví dụ: Cùng một dòng máy 4 kỳ công suất lớn, bộ cánh bơm nước và gioăng quy-lát của phiên bản tiêu chuẩn có thể khác biệt hoàn toàn về thông số rãnh then so với phiên bản chân dài (Ultra Long Shaft).</p>

    <h3>2. Gián đoạn thời gian khai thác tàu do thiếu vật tư dự phòng:</h3>
    <p>Trong hoạt động vận tải biển, du lịch hay đánh bắt xa bờ, mỗi ngày tàu nằm bờ chờ linh kiện đều gây thiệt hại kinh tế đáng kể. Các phương thức tìm kiếm vật tư rời rạc qua nhiều khâu trung gian thường làm chậm trễ tiến độ sửa chữa tại cảng.</p>
    <p class="article-example">Ví dụ: Một tàu dịch vụ phải dừng lịch trình 5 ngày chỉ vì đặt nhầm mã phớt thủy lực lái, trong khi việc tra cứu trực tiếp trên sơ đồ bóc tách (Exploded View) chỉ mất chưa đầy 2 phút để xác định đúng mã OEM.</p>

    <h3>3. Dữ liệu bảo trì chưa được chuẩn hóa theo hồ sơ tàu:</h3>
    <p>Mỗi con tàu sở hữu cấu hình máy chính, máy phát và hệ thống lái riêng biệt. Khi không lưu trữ lịch sử thay thế phụ tùng và mã quy đổi tương đương (Cross-Reference), đội ngũ kỹ thuật sẽ mất nhiều thời gian rà soát lại từ đầu trong các kỳ đại tu tiếp theo.</p>
    <p class="article-example">Ví dụ: Việc lưu trữ cấu hình động cơ trong sổ tay kỹ thuật số (Garage Tàu) giúp chủ tàu đặt lại chính xác bộ lọc nhớt, lọc nhiên liệu và bugi định kỳ chỉ với một thao tác duy nhất.</p>

    <h2>Kết luận: Giải pháp đồng bộ cho ngành hàng hải hiện đại</h2>
    <p>Những khó khăn trong công tác bảo trì và cung ứng vật tư hàng hải hoàn toàn có thể được giải quyết khi chuyển dịch sang quy trình tra cứu chuẩn hóa theo sơ đồ kỹ thuật và sử dụng linh kiện chính hãng có nguồn gốc rõ ràng.</p>
    <p>Hướng tới sự bền bỉ và an toàn trên mọi hải trình, JPM Marine cam kết mang đến hệ sinh thái tra cứu phụ tùng trực quan, kết nối trực tiếp kho vật tư OEM với từng nhu cầu kỹ thuật thực tế của bạn.</p>
  `;
}

export default {
  name: 'ChiTietTinTuc',
  setup() {
    const route = useRoute();
    const articleId = computed(() => route.params.id);
    const activeTocId = ref('');
    const currentHeroIndex = ref(0);

    const { data: fetchResult, isFetching, error, refetch } = useQuery({
      queryKey: ['public-news-detail', articleId],
      queryFn: () => axiosClient.get(`/public/tin-tuc/${articleId.value}`),
      staleTime: 0,
      refetchOnWindowFocus: true,
    });

    watch(articleId, () => {
      activeTocId.value = '';
      currentHeroIndex.value = 0;
      window.scrollTo({ top: 0, behavior: 'smooth' });
      refetch();
    });

    const article = computed(() => fetchResult.value?.data ?? null);
    const relatedArticles = computed(() => fetchResult.value?.related ?? []);

    // Xử lý HTML nội dung và tự động trích xuất Mục lục (Table of Contents)
    const contentAndToc = computed(() => {
      if (!article.value) return { html: '', toc: [] };

      let raw = String(article.value.content || '').replace(
        /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
        ''
      );

      // Chỉ bổ sung nội dung mẫu nếu bài viết trong DB hoàn toàn rỗng
      if (!raw || raw.trim().length === 0) {
        raw = buildStructuredArticleHtml(article.value, raw);
      }

      const toc = [];
      let secIdx = 1;
      const htmlWithIds = raw.replace(/<(h[23])([^>]*)>([\s\S]*?)<\/\1>/gi, (match, tag, attrs, inner) => {
        const plainText = inner.replace(/<[^>]+>/g, '').trim();
        const id = `toc-section-${secIdx++}`;
        if (plainText) {
          toc.push({
            id,
            text: plainText.replace(/:$/, ''),
          });
        }
        return `<${tag}${attrs} id="${id}">${inner}</${tag}>`;
      });

      return {
        html: htmlWithIds,
        toc,
      };
    });

    const processedHtmlContent = computed(() => contentAndToc.value.html);
    const tocItems = computed(() => contentAndToc.value.toc);

    const scrollToHeading = (id) => {
      activeTocId.value = id;
      const el = document.getElementById(id);
      if (el) {
        const y = el.getBoundingClientRect().top + window.scrollY - 100;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    };

    const getAuthorAvatar = (authorName) => {
      const str = String(authorName || 'JPM');
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
      }
      return AUTHOR_AVATARS[Math.abs(hash) % AUTHOR_AVATARS.length];
    };

    const getAuthorRole = (authorName, category) => {
      if (authorName === 'Kỹ Thuật') return 'Chuyên gia Kỹ thuật Động cơ Thủy';
      if (authorName === 'Marketing') return 'Chuyên viên Truyền thông Hàng hải';
      if (authorName === 'Ban Giám Đốc') return 'Ban Cố vấn Chiến lược JPM';
      if (category === 'Bảo Dưỡng Động Cơ') return 'Kỹ sư Trưởng Bảo dưỡng Máy thủy';
      return 'Chuyên gia Phân tích Kỹ thuật JPM';
    };

    const extractYoutubeId = (url) => {
      if (!url) return null;
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
      return match ? match[1] : null;
    };

    const hasVideo = computed(() => {
      return Boolean(extractYoutubeId(article.value?.youtube_url));
    });

    const galleryImages = computed(() => {
      if (!article.value || !article.value.gallery) return [];
      try {
        const arr = typeof article.value.gallery === 'string' ? JSON.parse(article.value.gallery) : article.value.gallery;
        return Array.isArray(arr) ? arr : [];
      } catch (e) {
        return [];
      }
    });

    // Dải Media thống nhất: Video (nếu có) là item đầu tiên, tiếp theo là Ảnh đại diện và các ảnh phụ
    const heroMediaList = computed(() => {
      const list = [];
      const yId = extractYoutubeId(article.value?.youtube_url);
      
      // 1. Video YouTube thực tế
      if (yId) {
        list.push({
          type: 'video',
          id: 'media-video',
          title: 'Video thực tế bài viết',
          youtubeId: yId,
          embedUrl: `https://www.youtube-nocookie.com/embed/${yId}?rel=0`,
          thumbnail: article.value?.thumbnail || FALLBACK,
        });
      }

      // 2. Ảnh đại diện chính
      if (article.value?.thumbnail) {
        list.push({
          type: 'image',
          id: 'media-thumb',
          title: article.value?.title || 'Ảnh đại diện',
          url: article.value.thumbnail,
        });
      }

      // 3. Các ảnh phụ trong gallery
      if (galleryImages.value && galleryImages.value.length > 0) {
        galleryImages.value.forEach((img, idx) => {
          if (img && img !== article.value?.thumbnail) {
            list.push({
              type: 'image',
              id: `media-gallery-${idx}`,
              title: `Ảnh chi tiết ${idx + 1}`,
              url: img,
            });
          }
        });
      }

      if (list.length === 0) {
        list.push({
          type: 'image',
          id: 'media-fallback',
          title: 'Hình ảnh',
          url: FALLBACK,
        });
      }

      return list;
    });

    const currentMedia = computed(() => {
      if (currentHeroIndex.value >= heroMediaList.value.length) {
        return heroMediaList.value[0] || { type: 'image', url: FALLBACK };
      }
      return heroMediaList.value[currentHeroIndex.value] || { type: 'image', url: FALLBACK };
    });

    // Danh sách chỉ gồm ảnh tĩnh để phục vụ Modal Lightbox zoom toàn màn hình
    const heroOnlyImages = computed(() => {
      return heroMediaList.value.filter(m => m.type === 'image');
    });

    const currentImageOnlyIndex = computed(() => {
      const cur = currentMedia.value;
      if (!cur || cur.type !== 'image') return 0;
      const idx = heroOnlyImages.value.findIndex(m => m.id === cur.id);
      return idx !== -1 ? idx : 0;
    });

    const prevHeroMedia = () => {
      if (heroMediaList.value.length <= 1) return;
      currentHeroIndex.value = (currentHeroIndex.value - 1 + heroMediaList.value.length) % heroMediaList.value.length;
    };

    const nextHeroMedia = () => {
      if (heroMediaList.value.length <= 1) return;
      currentHeroIndex.value = (currentHeroIndex.value + 1) % heroMediaList.value.length;
    };

    const setHeroMedia = (idx) => {
      currentHeroIndex.value = idx;
    };

    const prevImageOnly = () => {
      if (heroOnlyImages.value.length <= 1) return;
      const nextIdx = (currentImageOnlyIndex.value - 1 + heroOnlyImages.value.length) % heroOnlyImages.value.length;
      const targetMedia = heroOnlyImages.value[nextIdx];
      const targetHeroIdx = heroMediaList.value.findIndex(m => m.id === targetMedia.id);
      if (targetHeroIdx !== -1) currentHeroIndex.value = targetHeroIdx;
    };

    const nextImageOnly = () => {
      if (heroOnlyImages.value.length <= 1) return;
      const nextIdx = (currentImageOnlyIndex.value + 1) % heroOnlyImages.value.length;
      const targetMedia = heroOnlyImages.value[nextIdx];
      const targetHeroIdx = heroMediaList.value.findIndex(m => m.id === targetMedia.id);
      if (targetHeroIdx !== -1) currentHeroIndex.value = targetHeroIdx;
    };

    const selectVideoMedia = () => {
      const vIdx = heroMediaList.value.findIndex(m => m.type === 'video');
      if (vIdx !== -1) {
        currentHeroIndex.value = vIdx;
      }
      const el = document.querySelector('.article-hero-wrapper');
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    };

    const formatDate = (d) => {
      if (!d) return '';
      const date = new Date(d);
      return `${date.getDate().toString().padStart(2, '0')} Th${(date.getMonth() + 1).toString().padStart(2, '0')}, ${date.getFullYear()}`;
    };

    const copyLink = () => {
      navigator.clipboard.writeText(window.location.href);
      toaster.success('Đã sao chép đường dẫn bài viết!');
    };

    const shareLink = (platform) => {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(article.value?.title || '');
      if (platform === 'facebook') {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
      } else if (platform === 'twitter') {
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank');
      } else if (platform === 'linkedin') {
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
      }
    };

    const getImageUrl = (path) => {
      if (!path) return FALLBACK;
      if (typeof path === 'string' && (path.startsWith('http') || path.startsWith('blob:') || path.startsWith('data:'))) {
        return path;
      }
      const clean = String(path).replace(/^\//, '');
      const backendBase = (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
      if (clean.startsWith('storage/') || clean.startsWith('uploads/')) {
        return `${backendBase}/${clean}`;
      }
      return `${backendBase}/storage/${clean}`;
    };

    return {
      article,
      relatedArticles,
      processedHtmlContent,
      tocItems,
      activeTocId,
      scrollToHeading,
      getAuthorAvatar,
      getAuthorRole,
      galleryImages,
      heroMediaList,
      currentMedia,
      heroOnlyImages,
      currentImageOnlyIndex,
      currentHeroIndex,
      prevHeroMedia,
      nextHeroMedia,
      setHeroMedia,
      prevImageOnly,
      nextImageOnly,
      selectVideoMedia,
      hasVideo,
      getImageUrl,
      isFetching,
      error,
      formatDate,
      copyLink,
      shareLink,
      fallback: FALLBACK,
    };
  },
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
/* ── PAGE BACKGROUND & BASE ── */
.jpm-news-detail {
  background-color: #ffffff;
  color: #1E293B;
}

/* ── TOP BACK LINK ── */
.back-link {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 500;
  transition: color 0.2s ease;
}
.back-link:hover {
  color: var(--jpm-color-red-marine, #f82249);
}

/* ── ARTICLE HEADER ── */
.article-category-label {
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.2px;
  transition: color 0.2s ease;
}
.article-category-label:hover {
  color: var(--jpm-color-red-hover, #D9163A) !important;
}

.article-main-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: clamp(1.6rem, 2.6vw, 2.1rem);
  font-weight: 700;
  line-height: 1.25;
  letter-spacing: normal;
  max-width: 920px;
}

.article-subtitle {
  font-size: 1rem;
  line-height: 1.6;
  max-width: 780px;
}

.author-avatar {
  width: 42px;
  height: 42px;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

/* ── HERO IMAGE & SMART SLIDER ── */
.article-hero-img {
  aspect-ratio: 16 / 9;
  background-color: #0A1128;
  box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
}
@media (max-width: 768px) {
  .article-hero-img {
    aspect-ratio: 16 / 10;
  }
}

.hero-img-active {
  transition: opacity 0.25s ease;
}

.cursor-pointer {
  cursor: pointer;
}

.hero-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(4px);
  color: var(--jpm-color-navy-dark, #0A1128);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
  opacity: 0.88;
  transition: all 0.25s ease;
  z-index: 10;
}

.article-hero-img:hover .hero-nav-btn {
  opacity: 1;
}

.hero-nav-btn:hover {
  background: #ffffff;
  color: var(--jpm-color-red-marine, #F82249);
  transform: translateY(-50%) scale(1.08);
}

.hero-nav-prev {
  left: 16px;
}

.hero-nav-next {
  right: 16px;
}

.hero-counter-badge {
  background: rgba(10, 17, 40, 0.82);
  backdrop-filter: blur(6px);
  color: #ffffff;
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.3px;
  z-index: 10;
}

.hero-dots {
  z-index: 10;
}

.hero-dot-btn {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.5);
  transition: all 0.2s ease;
  padding: 0;
}

.hero-dot-btn.active {
  background: #ffffff;
  transform: scale(1.3);
  box-shadow: 0 0 6px rgba(255, 255, 255, 0.8);
}

.hero-dot-btn.is-video-dot {
  background: rgba(248, 34, 73, 0.65);
}

.hero-dot-btn.is-video-dot.active {
  background: var(--jpm-color-red-marine, #F82249);
  transform: scale(1.35);
  box-shadow: 0 0 6px rgba(248, 34, 73, 0.9);
}

.hero-zoom-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(4px);
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 0.85rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  opacity: 0;
  transition: all 0.2s ease;
  z-index: 10;
}

.article-hero-img:hover .hero-zoom-btn {
  opacity: 1;
}

.hero-zoom-btn:hover {
  background: #ffffff;
  color: var(--jpm-color-red-marine, #F82249);
}

.hero-thumb-item {
  width: 80px;
  height: 52px;
  border: 2px solid transparent;
  opacity: 0.7;
  transition: all 0.2s ease;
}

.hero-thumb-item:hover {
  opacity: 0.95;
}

.hero-thumb-item.active {
  border-color: var(--jpm-color-red-marine, #F82249);
  opacity: 1;
  box-shadow: 0 2px 8px rgba(248, 34, 73, 0.25);
}

.thumb-video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(10, 17, 40, 0.48);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
}

.hero-thumb-item:hover .thumb-video-overlay,
.hero-thumb-item.active .thumb-video-overlay {
  background: rgba(10, 17, 40, 0.25);
}

.thumb-video-play-btn {
  width: 22px;
  height: 22px;
  background: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  font-size: 0.8rem;
  padding-left: 2px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
}

.thumb-video-text {
  font-size: 0.62rem;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 0.5px;
  line-height: 1;
}

.lightbox-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 52px;
  height: 52px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease, transform 0.2s ease;
}

.lightbox-nav-btn:hover {
  background: rgba(255, 255, 255, 0.45);
  transform: translateY(-50%) scale(1.05);
}

.lightbox-nav-prev {
  left: 12px;
}

.lightbox-nav-next {
  right: 12px;
}

/* ── ARTICLE BODY TYPOGRAPHY ── */
.article-html-content {
  font-size: 0.98rem;
  line-height: 1.8;
  color: #334155;
}

.article-html-content :deep(p) {
  margin-bottom: 1.25rem;
}

.article-html-content :deep(h2) {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 1.38rem;
  font-weight: 700;
  letter-spacing: -0.3px;
  margin-top: 2.25rem;
  margin-bottom: 1rem;
  scroll-margin-top: 100px;
}

.article-html-content :deep(h3) {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 1.05rem;
  font-weight: 700;
  margin-top: 1.75rem;
  margin-bottom: 0.55rem;
  scroll-margin-top: 100px;
}

.article-html-content :deep(.article-example) {
  color: #4b5563;
  font-size: 0.94rem;
  background: rgba(10, 17, 40, 0.03);
  border-left: 3px solid var(--jpm-color-navy-dark, #0A1128);
  padding: 0.7rem 1rem;
  border-radius: 0 8px 8px 0;
  margin-bottom: 1.5rem;
}

.article-html-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: var(--jpm-radius-card, 12px);
  margin: 1.75rem 0;
}

.article-html-content :deep(ul),
.article-html-content :deep(ol) {
  margin-bottom: 1.35rem;
  padding-left: 1.35rem;
}

.article-html-content :deep(li) {
  margin-bottom: 0.45rem;
}

/* ── TAG BADGES (DEC-01: 6PX BORDER RADIUS) ── */
.tag-badge {
  font-size: 0.8rem;
  font-weight: 500;
  color: #334155;
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  padding: 0.35rem 0.75rem;
  border-radius: var(--jpm-radius-badge, 6px);
  transition: all 0.2s ease;
}
.tag-badge:hover {
  border-color: #f82249;
  color: #f82249;
  background-color: rgba(248, 34, 73, 0.06);
}

/* ── RIGHT SIDEBAR (TOC + SHARE + PROMO CTA) ── */
.sidebar-sticky {
  position: sticky;
  top: 96px;
}

.sidebar-label {
  font-size: 0.82rem;
  letter-spacing: -0.1px;
}

.toc-list {
  border-left: 2px solid #e2e8f0;
}

.toc-item {
  position: relative;
  padding-left: 14px;
  margin-bottom: 0.65rem;
}

.toc-item::before {
  content: '';
  position: absolute;
  left: -2px;
  top: 0;
  bottom: 0;
  width: 2px;
  background-color: transparent;
  transition: background-color 0.2s ease;
}

.toc-item.active::before {
  background-color: var(--jpm-color-red-marine, #f82249);
}

.toc-link {
  display: block;
  font-size: 0.82rem;
  line-height: 1.45;
  color: #64748b;
  transition: color 0.2s ease;
}

.toc-item.active .toc-link,
.toc-link:hover {
  color: var(--jpm-color-red-marine, #f82249);
  font-weight: 600;
}

/* Social Share Icons (DEC-01: 50% CIRCLE) */
.social-icon-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: none;
  background-color: var(--jpm-color-navy-dark, #0A1128);
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  transition: all 0.2s ease;
}
.social-icon-btn:hover {
  background-color: #f82249;
  transform: translateY(-2px);
}

/* Promo CTA Card (DEC-01: 12PX CARD) */
.promo-cta-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
}

/* ── RELATED BLOGS SECTION (CHUẨN HÓA ĐỒNG BỘ THẺ CỬA HÀNG PRODUCT CARD) ── */
.related-blogs-section {
  background-color: #ffffff !important;
}

.related-blog-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  background-color: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: var(--jpm-shadow-sm, 0 1px 3px rgba(10, 17, 40, 0.05)) !important;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease !important;
  padding: 0 !important;
}

.related-blog-card:hover {
  transform: translateY(-4px) !important;
  box-shadow: var(--jpm-shadow-md, 0 4px 12px -2px rgba(10, 17, 40, 0.08)) !important;
  border-color: var(--jpm-neutral-300, #CBD5E1) !important;
}

.related-blog-card .card-img-wrapper {
  background-color: var(--jpm-neutral-50, #F8FAFC);
  border-top-left-radius: var(--jpm-radius-card, 12px);
  border-top-right-radius: var(--jpm-radius-card, 12px);
}

.related-blog-card .card-img-product {
  transition: transform 0.35s ease;
}

.related-blog-card:hover .card-img-product {
  transform: scale(1.04);
}

.related-blog-card .news-category-label,
.related-blog-card .product-category {
  font-family: var(--heading-font, 'Raleway', sans-serif) !important;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: var(--jpm-color-red-marine, #F82249) !important;
  line-height: 1.3;
}

.related-blog-card .news-meta {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.8rem;
  font-weight: 500;
  color: #64748b;
  line-height: 1.3;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.related-blog-card .news-meta i {
  color: inherit;
  font-size: 0.82rem;
}

.related-blog-card .card-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.96rem;
  font-weight: 700;
  line-height: 1.45;
  height: 2.9em;
  margin-bottom: 0.25rem;
}

.related-blog-card .card-title a {
  color: var(--jpm-color-navy-dark, #0A1128);
  transition: color 0.15s ease;
}

.related-blog-card:hover .card-title a {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
}

/* ── HOVER UTILITIES ── */
.hover-zoom {
  transition: transform 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.related-blog-card:hover .hover-zoom,
.gallery-thumb:hover .hover-zoom {
  transform: scale(1.05);
}

.hover-title-navy,
.hover-text-danger {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
}
.hover-title-navy:hover,
.hover-text-danger:hover {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
}

/* ── SKELETON ── */
.skeleton-line,
.skeleton-box {
  background: linear-gradient(90deg, #ececec 25%, #f5f5f5 50%, #ececec 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 6px;
}
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
