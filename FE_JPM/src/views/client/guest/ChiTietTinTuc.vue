<template>
  <div class="jpm-news-detail main-page">
    <section class="section inner-page-section bg-white">
      <div class="container">

    <!-- SKELETON LOADING -->
    <div v-if="isFetching" class="py-2">
      <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
        <div class="skeleton-line" style="width: 280px; height: 18px;"></div>
        <div class="skeleton-line" style="width: 130px; height: 18px;"></div>
      </div>
      <div class="skeleton-line mb-2" style="width: 140px; height: 14px;"></div>
      <div class="skeleton-line mb-3" style="width: 75%; height: 42px;"></div>
      <div class="skeleton-line mb-4" style="width: 60%; height: 18px;"></div>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="skeleton-line" style="width: 180px; height: 36px;"></div>
        <div class="skeleton-line" style="width: 100px; height: 16px;"></div>
      </div>
      <div class="row g-4 g-lg-5">
        <div class="col-lg-8">
          <div class="skeleton-box mb-4" style="height: 440px; border-radius: 12px;"></div>
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
    <div v-else-if="error" class="py-5 text-center">
      <i class="bi bi-exclamation-triangle text-danger display-1 mb-3"></i>
      <h2 class="fw-bold" style="color: var(--jpm-color-navy-dark, #0A1128);">Không tìm thấy bài viết</h2>
      <p class="text-secondary mb-4">Bài viết này không tồn tại hoặc đã bị xóa.</p>
      <router-link to="/tin-tuc" class="btn btn-danger px-4">
        <i class="bi bi-arrow-left me-2"></i> Quay lại tất cả bài viết
      </router-link>
    </div>

    <!-- ARTICLE CONTENT (ĐỒNG BỘ 100% CHUẨN CHI TIẾT CLIENT) -->
    <article v-else-if="article" class="article-wrapper">

        <!-- 1. TOP BAR: BREADCRUMB & NÚT QUAY LẠI CHUẨN CLIENT -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 mb-4 border-bottom">
          <nav aria-label="breadcrumb" class="custom-breadcrumb">
            <ol class="breadcrumb mb-0 align-items-center">
              <li class="breadcrumb-item">
                <router-link to="/" class="text-decoration-none text-muted d-flex align-items-center">
                  <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                </router-link>
              </li>
              <li class="breadcrumb-item">
                <router-link to="/tin-tuc" class="text-decoration-none text-muted">Tin tức &amp; Cẩm nang</router-link>
              </li>
              <li class="breadcrumb-item" v-if="article.category">
                <router-link
                  :to="{ path: '/tin-tuc', query: { category: article.category } }"
                  class="text-decoration-none text-muted"
                >
                  {{ article.category }}
                </router-link>
              </li>
              <li
                class="breadcrumb-item active fw-bold text-truncate"
                style="color: var(--heading-color, #0A1128); max-width: 320px;"
                aria-current="page"
                :title="article.title"
              >
                {{ article.title }}
              </li>
            </ol>
          </nav>

          <router-link
            to="/tin-tuc"
            class="btn-back-diagram"
            title="Quay lại tất cả bài viết"
          >
            <i class="bi bi-arrow-left"></i>
            <span>Tất cả bài viết</span>
          </router-link>
        </div>

        <!-- 2. ARTICLE HEADER -->
        <header class="article-header mb-4 pb-2">
          <!-- Category Label -->
          <div class="mb-2" v-if="article.category">
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

            <div class="text-secondary d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.86rem;">
              <span><i class="bi bi-calendar3 me-1"></i>{{ formatDate(article.created_at) }}</span>
              <span class="text-muted opacity-50">•</span>
              <span><i class="bi bi-eye me-1"></i>{{ Number(article.views || 0).toLocaleString('vi-VN') }} lượt xem</span>
            </div>
          </div>
        </header>

        <!-- 3. TWO-COLUMN BODY: LEFT HERO + CONTENT | RIGHT STICKY SIDEBAR -->
        <div class="row g-4 g-lg-5 align-items-start">

          <!-- LEFT COLUMN: HERO MEDIA + MAIN ARTICLE CONTENT -->
          <div class="col-lg-8">

            <!-- HERO MEDIA HUB (Nằm gọn trong cột nội dung 8/12 cân đối tỷ lệ 16:9) -->
            <div class="article-hero-wrapper mb-4 pb-2">
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
              <div class="hero-counter-badge position-absolute bottom-0 end-0 m-3 px-3 py-1 rounded-2 d-flex align-items-center gap-1 shadow-sm">
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
                <div class="d-flex align-items-center flex-wrap gap-2">
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ Facebook"
                    aria-label="Chia sẻ Facebook"
                    @click="shareLink('facebook')"
                  >
                    <i class="bi bi-facebook"></i>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ Zalo"
                    aria-label="Chia sẻ Zalo"
                    @click="shareLink('zalo')"
                  >
                    <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" aria-hidden="true">
                      <path d="M12.49 10.2722v-.4496h1.3467v6.3218h-.7704a.576.576 0 01-.5763-.5729l-.0006.0005a3.273 3.273 0 01-1.9372.6321c-1.8138 0-3.2844-1.4697-3.2844-3.2823 0-1.8125 1.4706-3.2822 3.2844-3.2822a3.273 3.273 0 011.9372.6321l.0006.0005zM6.9188 7.7896v.205c0 .3823-.051.6944-.2995 1.0605l-.03.0343c-.0542.0615-.1815.206-.2421.2843L2.024 14.8h4.8948v.7682a.5764.5764 0 01-.5767.5761H0v-.3622c0-.4436.1102-.6414.2495-.8476L4.8582 9.23H.1922V7.7896h6.7266zm8.5513 8.3548a.4805.4805 0 01-.4803-.4798v-7.875h1.4416v8.3548H15.47zM20.6934 9.6C22.52 9.6 24 11.0807 24 12.9044c0 1.8252-1.4801 3.306-3.3066 3.306-1.8264 0-3.3066-1.4808-3.3066-3.306 0-1.8237 1.4802-3.3044 3.3066-3.3044zm-10.1412 5.253c1.0675 0 1.9324-.8645 1.9324-1.9312 0-1.065-.865-1.9295-1.9324-1.9295s-1.9324.8644-1.9324 1.9295c0 1.0667.865 1.9312 1.9324 1.9312zm10.1412-.0033c1.0737 0 1.945-.8707 1.945-1.9453 0-1.073-.8713-1.9436-1.945-1.9436-1.0753 0-1.945.8706-1.945 1.9453 0 1.0746.8697 1.9453 1.945 1.9453z"/>
                    </svg>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ Instagram"
                    aria-label="Chia sẻ Instagram"
                    @click="shareLink('instagram')"
                  >
                    <i class="bi bi-instagram"></i>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Chia sẻ TikTok"
                    aria-label="Chia sẻ TikTok"
                    @click="shareLink('tiktok')"
                  >
                    <i class="bi bi-tiktok"></i>
                  </button>
                  <button
                    type="button"
                    class="social-icon-btn"
                    title="Sao chép liên kết bài viết"
                    aria-label="Sao chép liên kết bài viết"
                    @click="copyLink"
                  >
                    <i class="bi bi-link-45deg" style="font-size: 1rem;"></i>
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
                  style="border-radius: var(--jpm-radius-control, 8px); font-size: 0.88rem;"
                >
                  Khám phá ngay
                </router-link>
              </div>

            </div>
          </aside>

        </div>

        <!-- 5. RELATED BLOGS SECTION (ĐỒNG BỘ 100% EDITORIAL CARD CỦA TINTUC.VUE & TRANGCHU.VUE) -->
        <div v-if="relatedArticles && relatedArticles.length > 0" class="related-blogs-section mt-5 pt-5 border-top bg-white">
          <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <h4 class="jpm-section-heading">
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
              <article class="card h-100 product-card jpm-card related-editorial-card position-relative">
                <!-- 1. Ảnh Card bo góc trên 12px đồng bộ ProductCard -->
                <div class="card-img-wrapper related-thumb-wrap position-relative overflow-hidden bg-light">
                  <img
                    :src="getImageUrl(rel.thumbnail) || fallback"
                    :alt="rel.title"
                    class="w-100 h-100 related-thumb-img"
                    @error="$event.target.src = fallback"
                    loading="lazy"
                  >
                </div>

                <!-- 2. Thân Card chuẩn JPM Client (DEC-01: 12px Card) -->
                <div class="card-body p-3 d-flex flex-column bg-white text-start">
                  <div
                    class="news-category-label mb-1 text-truncate"
                    :title="rel.category"
                  >
                    {{ rel.category }}
                  </div>

                  <h5 class="related-editorial-title mb-3">
                    <router-link :to="`/tin-tuc/${rel.id}`" class="text-decoration-none stretched-link text-truncate-2">
                      {{ rel.title }}
                    </router-link>
                  </h5>

                  <div class="mt-auto pt-2 d-flex align-items-center justify-content-between gap-2">
                    <span class="news-meta">
                      <i class="bi bi-calendar3"></i>
                      <span>{{ formatDate(rel.created_at) }}</span>
                    </span>
                    <span class="related-read-more">
                      <span>Đọc tiếp</span>
                      <i class="bi bi-arrow-right"></i>
                    </span>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
    </article>

      </div>
    </section>
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
  if (rawHtml && rawHtml.trim()) {
    return rawHtml;
  }
  if (article?.summary && String(article.summary).trim()) {
    return `<p>${String(article.summary).replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>`;
  }
  return '<p class="text-muted fst-italic">Nội dung bài viết đang được cập nhật.</p>';
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
      return `${date.getDate().toString().padStart(2, '0')} Tháng ${(date.getMonth() + 1).toString().padStart(2, '0')}, ${date.getFullYear()}`;
    };

    const copyCurrentUrl = async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        return true;
      } catch {
        return false;
      }
    };

    const copyLink = async () => {
      await copyCurrentUrl();
      toaster.success('Đã sao chép đường dẫn bài viết!');
    };

    const shareLink = async (platform) => {
      const url = encodeURIComponent(window.location.href);
      if (platform === 'facebook') {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'noopener,noreferrer');
        return;
      }

      await copyCurrentUrl();
      if (platform === 'zalo') {
        toaster.success('Đã sao chép liên kết bài viết để chia sẻ qua Zalo!');
      } else if (platform === 'instagram') {
        toaster.success('Đã sao chép liên kết bài viết để chia sẻ lên Instagram!');
      } else if (platform === 'tiktok') {
        toaster.success('Đã sao chép liên kết bài viết để chia sẻ lên TikTok!');
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
  display: inline-flex;
  align-items: center;
  padding: 5px 12px;
  border-radius: var(--jpm-radius-badge, 6px);
  background-color: rgba(248, 34, 73, 0.08);
  border: 1px solid rgba(248, 34, 73, 0.2);
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.2px;
  line-height: 1.25;
  transition: all 0.2s ease;
}
.article-category-label:hover {
  background-color: rgba(248, 34, 73, 0.14);
  border-color: rgba(248, 34, 73, 0.35);
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

/* ── RELATED BLOGS SECTION (ĐỒNG BỘ 100% CHUẨN JPM CARD & HOVER LIFT) ── */
.related-blogs-section {
  background-color: #ffffff !important;
}

.related-editorial-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid #e2e8f0 !important;
  background-color: #ffffff !important;
  box-shadow: var(--jpm-shadow-sm, 0 1px 3px rgba(10, 17, 40, 0.05)) !important;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease !important;
  overflow: hidden;
  transform: translate3d(0, 0, 0);
  backface-visibility: hidden;
  will-change: transform;
}

.related-editorial-card:hover {
  transform: translate3d(0, -4px, 0) !important;
  box-shadow: 0 12px 24px rgba(10, 17, 40, 0.08) !important;
  border-color: #cbd5e1 !important;
}

.related-thumb-wrap {
  aspect-ratio: 16 / 10;
  background-color: #f1f5f9;
  border-radius: 12px 12px 0 0 !important;
}

.related-thumb-img {
  object-fit: cover;
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.related-editorial-card:hover .related-thumb-img {
  transform: scale(1.03);
}

.related-editorial-card .news-category-label {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: var(--jpm-color-red-marine, #F82249);
  line-height: 1.3;
}

.related-editorial-title {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.96rem;
  font-weight: 700;
  line-height: 1.45;
  height: 2.9em;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin: 0;
}

.related-editorial-title a {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
}

.related-editorial-card:hover .related-editorial-title a {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
}

.related-editorial-card .news-meta {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 0.8rem;
  font-weight: 500;
  color: #64748b;
  line-height: 1.3;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.related-editorial-card .news-meta i {
  color: inherit;
  font-size: 0.82rem;
}

.related-read-more {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--jpm-color-red-marine, #F82249);
  white-space: nowrap;
}

.related-read-more i {
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.related-editorial-card:hover .related-read-more i {
  transform: translateX(4px);
}

/* ── HOVER UTILITIES ── */
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
