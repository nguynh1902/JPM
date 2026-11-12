<template>
  <div class="main-page">
    <section class="section inner-page-section product-detail-section bg-white">
      <div class="container">
        
        <!-- SKELETON LOADING -->
        <div v-if="isLoading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3 text-muted">Đang tải thông tin sản phẩm...</p>
        </div>

        <!-- ERROR / NOT FOUND -->
        <div v-else-if="isError || !sanPham" class="text-center py-5">
          <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
          <h4 class="mt-3">Không tìm thấy sản phẩm</h4>
          <p class="text-muted">Sản phẩm này có thể không tồn tại hoặc đã bị gỡ.</p>
          <router-link to="/san-pham" class="btn btn-outline-jpm mt-3">Về cửa hàng</router-link>
        </div>

        <template v-else>
          <!-- HEADER & BREADCRUMB -->
          <div class="mb-4" data-aos="fade-down">
            <!-- Title chuẩn hóa đồng bộ với các trang khác -->
            <div class="page-title-box mb-2">
              <h2 class="jpm-page-title">
                Chi Tiết <span style="color: var(--jpm-color-red-marine, #F82249);">Sản Phẩm</span>
              </h2>
              <p class="text-muted mb-0">
                Thông tin chi tiết, thông số kỹ thuật và báo giá vật tư hàng hải chính hãng.
              </p>
            </div>

            <!-- Modern Breadcrumb & Back to Shop -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
              <nav aria-label="breadcrumb" class="custom-breadcrumb">
                <ol class="breadcrumb mb-0 align-items-center">
                  <li class="breadcrumb-item">
                    <router-link to="/" class="text-decoration-none text-muted d-flex align-items-center">
                      <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                    </router-link>
                  </li>
                  <li class="breadcrumb-item">
                    <router-link to="/san-pham" class="text-decoration-none text-muted">Cửa hàng</router-link>
                  </li>
                  <li class="breadcrumb-item active fw-bold" style="color: var(--heading-color);" aria-current="page">
                    {{ sanPham.danh_muc ? sanPham.danh_muc.ten_danh_muc : 'Sản phẩm' }}
                  </li>
                </ol>
              </nav>

              <router-link 
                to="/san-pham" 
                class="btn-back-diagram"
                title="Quay lại cửa hàng"
              >
                <i class="bi bi-arrow-left"></i>
                <span>Quay lại cửa hàng</span>
              </router-link>
            </div>
          </div>

          <div class="row g-4 mb-3">
            
            <!-- LEFT: IMAGE GALLERY (col-lg-5) -->
            <div class="col-lg-5" data-aos="fade-right">
              <div class="product-gallery">
                
                <!-- Main Image Card (Luxury Marine Canvas) -->
                <div class="main-image-canvas">
                  <!-- Floating Glass Actions -->
                  <div class="gallery-floating-actions">
                    <button 
                      class="btn-gallery-action" 
                      title="Chia sẻ liên kết" 
                      @click="handleShare"
                    >
                      <i class="bi bi-share"></i>
                    </button>
                    <button 
                      class="btn-gallery-action" 
                      title="Phóng to hình ảnh" 
                      @click="openLightbox"
                    >
                      <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                  </div>

                  <!-- Prev / Next Controls (khi có > 1 ảnh) -->
                  <button 
                    v-if="images && images.length > 1" 
                    class="btn-canvas-nav btn-canvas-prev" 
                    @click.stop="prevImage" 
                    title="Ảnh trước"
                  >
                    <i class="bi bi-chevron-left"></i>
                  </button>
                  <button 
                    v-if="images && images.length > 1" 
                    class="btn-canvas-nav btn-canvas-next" 
                    @click.stop="nextImage" 
                    title="Ảnh sau"
                  >
                    <i class="bi bi-chevron-right"></i>
                  </button>

                  <!-- Image Canvas Wrapper -->
                  <div class="canvas-image-wrapper" @click="openLightbox" title="Nhấp để phóng to ảnh">
                    <img 
                      v-if="currentImage" 
                      :src="getImageUrl(currentImage)" 
                      alt="Hình ảnh sản phẩm" 
                      class="canvas-img"
                    >
                    <div v-else class="canvas-placeholder">
                      <i class="bi bi-image text-muted fs-1 mb-2"></i>
                      <span class="text-muted small">Chưa có hình ảnh</span>
                    </div>
                  </div>

                  <!-- Counter Badge -->
                  <div v-if="images && images.length > 1" class="gallery-counter-badge">
                    {{ images.indexOf(currentImage) + 1 }} / {{ images.length }}
                  </div>
                </div>

                <!-- Thumbnails List (Luôn hiển thị ảnh đại diện và thư viện ảnh) -->
                <div class="thumbnails-slider-wrapper position-relative mt-2" v-if="images && images.length > 0">
                  <button 
                    v-if="images.length > 5" 
                    class="btn-thumb-nav btn-thumb-prev" 
                    @click="scrollThumbnails('left')"
                    title="Cuộn ảnh trước"
                  >
                    <i class="bi bi-chevron-left"></i>
                  </button>

                  <div class="thumbnail-filmstrip d-flex justify-content-start gap-2" ref="thumbStripRef">
                    <div 
                      v-for="(img, idx) in images" 
                      :key="idx" 
                      class="thumb-film-item"
                      :class="{'active-thumb': currentImage === img}"
                      @click="selectImage(img)"
                      title="Xem ảnh này"
                    >
                      <img :src="getImageUrl(img)" class="thumb-img" alt="Thumbnail">
                    </div>
                  </div>

                  <button 
                    v-if="images.length > 5" 
                    class="btn-thumb-nav btn-thumb-next" 
                    @click="scrollThumbnails('right')"
                    title="Cuộn ảnh sau"
                  >
                    <i class="bi bi-chevron-right"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- MIDDLE: PRODUCT INFO (col-lg-7) -->
            <div class="col-lg-7 d-flex flex-column" data-aos="fade-up">
              <div class="product-info flex-grow-1 pe-lg-3">
                
                <!-- PRODUCT HEADER & PRICING BLOCK (Chuyển tên SP & SKU từ header vào đây) -->
                <div class="product-info-top mb-3">
                  <div class="product-title-block mb-3">
                    <h1 class="product-main-title">
                      {{ sanPham.ten_san_pham }}
                    </h1>
                    <div class="product-sku-info">
                      <span class="sku-label">Mã:</span>
                      <span class="sku-code font-monospace">{{ sanPham.ma_san_pham }}</span>
                    </div>
                  </div>

                  <div class="pricing-block">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                      <span v-if="sanPham.is_gia_lien_he || !sanPham.gia_ban" class="product-price-val text-danger">Liên hệ nhận báo giá</span>
                      <span v-else class="product-price-val text-danger">{{ formatPrice(sanPham.gia_ban) }} ₫</span>
                    </div>
                  </div>
                </div>

                <!-- SPEC CHECKLIST -->
                <div class="spec-checklist mb-3">
                  <div class="spec-check-item">
                    <i class="bi bi-check2 check-icon"></i>
                    <span>Sản phẩm chính hãng 100%, bảo hành theo tiêu chuẩn nhà sản xuất</span>
                  </div>
                  <div class="spec-check-item">
                    <i class="bi bi-check2 check-icon"></i>
                    <span>Đạt tiêu chuẩn an toàn hàng hải, độ bền cao trong môi trường biển</span>
                  </div>
                  <div class="spec-check-item">
                    <i class="bi bi-check2 check-icon"></i>
                    <span>Đổi trả linh hoạt nếu phát sinh lỗi từ nhà sản xuất</span>
                  </div>
                </div>

                <!-- META INFO & SPECS LINK -->
                <div class="product-meta-row mb-3 text-muted small">
                  <div v-if="sanPham.danh_muc" class="mb-2">
                    <span>Danh mục: </span>
                    <strong class="text-dark">{{ sanPham.danh_muc.ten_danh_muc }}</strong>
                  </div>
                  <div>
                    <a href="#product-tabs" class="link-view-specs d-inline-flex align-items-center gap-1" @click.prevent="scrollToTabs">
                      <span>Xem bài viết mô tả chi tiết</span>
                      <i class="bi bi-arrow-right"></i>
                    </a>
                  </div>
                </div>

                <!-- PURCHASE CONTAINER -->
                <div class="purchase-container" style="max-width: 540px;">
                  <div class="action-block pt-3 border-top">
                    <div class="d-flex align-items-center gap-3">
                      <!-- 44px Quantity Stepper -->
                      <div class="product-qty-stepper">
                        <button class="qty-btn" @click="qty > 1 ? qty-- : null" :disabled="qty <= 1" title="Giảm số lượng">
                          <i class="bi bi-dash"></i>
                        </button>
                        <input 
                          type="number" 
                          v-model.number="qty" 
                          class="qty-input" 
                          min="1" 
                          :max="sanPham.so_luong_ton || 999" 
                          @change="qty = Math.min(Math.max(1, qty), sanPham.so_luong_ton || 999)"
                        >
                        <button class="qty-btn" @click="qty < (sanPham.so_luong_ton || 999) ? qty++ : null" :disabled="qty >= (sanPham.so_luong_ton || 999)" title="Tăng số lượng">
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>

                      <!-- Add to Cart -->
                      <button class="btn btn-product-cart flex-grow-1" @click="addToCart(sanPham)">
                        <i class="bi bi-cart2 me-1"></i>
                        <span>Thêm vào giỏ</span>
                      </button>

                      <!-- Save Wishlist Button -->
                      <button class="btn btn-product-garage" title="Lưu vật tư thường mua" @click="saveToWishlist">
                        <i class="bi bi-bookmark-heart me-1"></i>
                        <span>Lưu vật tư</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- End Row 1 -->


          <!-- TABS SECTION -->
          <div class="row mb-5" id="product-tabs" data-aos="fade-up">
             <div class="col-lg-12">
                <div class="product-tabs pt-3 border-top">
                  <ul class="nav nav-tabs custom-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active fw-bold text-uppercase" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">MÔ TẢ CHI TIẾT</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link fw-bold text-uppercase" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">THÔNG SỐ KỸ THUẬT</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link fw-bold text-uppercase" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">VIDEO</button>
                    </li>
                  </ul>
                  <div class="tab-content mt-4" id="myTabContent">
                    <div class="tab-pane fade show active lh-lg" style="color: var(--heading-color);" id="desc" role="tabpanel">
                      <div v-if="sanPham && sanPham.mo_ta_chi_tiet" v-html="sanPham.mo_ta_chi_tiet"></div>
                      <div v-else class="py-2">
                        <p>Sản phẩm chính hãng. Đảm bảo chất lượng cao nhất, đáp ứng các tiêu chuẩn khắt khe.</p>
                      </div>
                    </div>
                    <div class="tab-pane fade" style="color: var(--heading-color);" id="specs" role="tabpanel">
                      <div v-if="sanPham && sanPham.thong_so_ky_thuat" v-html="sanPham.thong_so_ky_thuat"></div>
                      <ul v-else class="list-unstyled lh-lg">
                        <li><strong>Mã sản phẩm:</strong> {{ sanPham ? sanPham.ma_san_pham : '' }}</li>
                        <li v-if="sanPham && sanPham.danh_muc"><strong>Danh mục:</strong> {{ sanPham.danh_muc.ten_danh_muc }}</li>
                      </ul>
                    </div>
                    <div class="tab-pane fade" style="color: var(--heading-color);" id="videos" role="tabpanel">
                      <div v-if="sanPham && sanPham.video_url" class="row">
                        <div class="col-lg-8">
                          <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
                            <iframe :src="getEmbedUrl(sanPham.video_url)" title="Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                          </div>
                        </div>
                      </div>
                      <div v-else class="py-2 text-muted">
                        <p>Chưa có video giới thiệu cho sản phẩm này.</p>
                      </div>
                    </div>
                  </div>
                </div>
             </div>
          </div>
        </template>
      </div>
    </section>

    <!-- RELATED PRODUCTS & FAQ -->
    <section class="section related-products-section py-5 border-top">
      <div class="container">
        <!-- SẢN PHẨM CÙNG DANH MỤC -->
        <div class="mb-5" data-aos="fade-up" v-if="relatedProducts && relatedProducts.length > 0">
          <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
            <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
              Những Sản Phẩm Khác
            </h4>
            <router-link :to="sanPham?.danh_muc ? { path: '/san-pham', query: { danh_muc: sanPham.danh_muc.slug } } : '/san-pham'" class="text-decoration-none text-danger fw-bold d-flex align-items-center gap-1 hover-underline" style="font-size: 0.95rem;">
              Xem tất cả <i class="bi bi-arrow-right"></i>
            </router-link>
          </div>
          <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <div class="col" v-for="part in relatedProducts.slice(0, 4)" :key="part.id">
              <ProductCard :product="part" />
            </div>
          </div>
        </div>
        
        <!-- CÂU HỎI THƯỜNG GẶP -->
        <div class="mb-5" data-aos="fade-up">
          <div class="mb-4 border-bottom pb-2">
            <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
              Câu Hỏi Thường Gặp
            </h4>
          </div>
          
          <div class="row g-4" v-if="faqs.length > 0">
            <!-- Cột trái -->
            <div class="col-lg-6">
              <div class="accordion accordion-flush client-faq-accordion" id="faqAccordionLeft">
                <div 
                  v-for="(faq, idx) in faqs.slice(0, Math.ceil(faqs.length / 2))" 
                  :key="faq.id" 
                  class="accordion-item bg-transparent border-0 border-bottom"
                >
                  <h6 class="accordion-header" :id="'faqHeadL' + idx">
                    <button 
                      class="accordion-button collapsed bg-transparent shadow-none px-0 py-3 fw-bold" 
                      type="button" 
                      data-bs-toggle="collapse" 
                      :data-bs-target="'#faqCollapseL' + idx" 
                      aria-expanded="false" 
                      :aria-controls="'faqCollapseL' + idx"
                    >
                      {{ faq.question }}
                    </button>
                  </h6>
                  <div 
                    :id="'faqCollapseL' + idx" 
                    class="accordion-collapse collapse" 
                    :aria-labelledby="'faqHeadL' + idx" 
                    data-bs-parent="#faqAccordionLeft"
                  >
                    <div class="accordion-body px-0 pt-0 pb-3 text-muted faq-answer-content" v-html="faq.answer"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cột phải -->
            <div class="col-lg-6">
              <div class="accordion accordion-flush client-faq-accordion" id="faqAccordionRight">
                <div 
                  v-for="(faq, idx) in faqs.slice(Math.ceil(faqs.length / 2))" 
                  :key="faq.id" 
                  class="accordion-item bg-transparent border-0 border-bottom"
                >
                  <h6 class="accordion-header" :id="'faqHeadR' + idx">
                    <button 
                      class="accordion-button collapsed bg-transparent shadow-none px-0 py-3 fw-bold" 
                      type="button" 
                      data-bs-toggle="collapse" 
                      :data-bs-target="'#faqCollapseR' + idx" 
                      aria-expanded="false" 
                      :aria-controls="'faqCollapseR' + idx"
                    >
                      {{ faq.question }}
                    </button>
                  </h6>
                  <div 
                    :id="'faqCollapseR' + idx" 
                    class="accordion-collapse collapse" 
                    :aria-labelledby="'faqHeadR' + idx" 
                    data-bs-parent="#faqAccordionRight"
                  >
                    <div class="accordion-body px-0 pt-0 pb-3 text-muted faq-answer-content" v-html="faq.answer"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </section>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery, useMutation } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';
import { useCartStore } from '../../../stores/cartStore';
import ProductCard from '../../../components/client/ProductCard.vue';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.min.css';

const cartStore = useCartStore();
const toaster = createToaster({ position: 'top-right', duration: 3000 });

const route = useRoute();
const productSlug = computed(() => route.params.slug);

const qty = ref(1);
const currentImage = ref(null);
const images = ref([]);
const thumbStripRef = ref(null);

const scrollToActiveThumb = () => {
  nextTick(() => {
    if (!thumbStripRef.value) return;
    const activeEl = thumbStripRef.value.querySelector('.active-thumb');
    if (activeEl) {
      activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
  });
};

const selectImage = (img) => {
  currentImage.value = img;
  scrollToActiveThumb();
};

const prevImage = () => {
  if (!images.value || images.value.length <= 1) return;
  const idx = images.value.indexOf(currentImage.value);
  const newIdx = (idx - 1 + images.value.length) % images.value.length;
  currentImage.value = images.value[newIdx];
  scrollToActiveThumb();
};

const nextImage = () => {
  if (!images.value || images.value.length <= 1) return;
  const idx = images.value.indexOf(currentImage.value);
  const newIdx = (idx + 1) % images.value.length;
  currentImage.value = images.value[newIdx];
  scrollToActiveThumb();
};

const scrollThumbnails = (direction) => {
  if (!thumbStripRef.value) return;
  const scrollAmount = 180;
  thumbStripRef.value.scrollBy({
    left: direction === 'left' ? -scrollAmount : scrollAmount,
    behavior: 'smooth'
  });
};

const openLightbox = () => {
  if (images.value.length === 0) return;
  const elements = images.value.map(img => ({
    href: img,
    type: 'image'
  }));
  const lightbox = GLightbox({ elements, touchNavigation: true, loop: true });
  const startIndex = images.value.indexOf(currentImage.value);
  lightbox.openAt(startIndex > -1 ? startIndex : 0);
};

const handleShare = async () => {
  try {
    if (navigator.clipboard) {
      await navigator.clipboard.writeText(window.location.href);
      toaster.success("Đã sao chép liên kết sản phẩm!");
    } else {
      toaster.info("Liên kết: " + window.location.href);
    }
  } catch (e) {
    toaster.info("Liên kết: " + window.location.href);
  }
};

// Fetch Product Details
const { data, isLoading, isError } = useQuery({
  queryKey: ['productDetail', productSlug],
  queryFn: () => axiosClient.get(`/public/san-pham/${productSlug.value}`),
  enabled: !!productSlug.value
});

const sanPham = computed(() => data.value?.data?.san_pham || null);
const relatedProducts = computed(() => data.value?.data?.related_products || []);

  const { data: faqsData } = useQuery({
    queryKey: ['public-faqs'],
    queryFn: () => axiosClient.get('/public/faqs', { params: { limit: 4 } }),
    staleTime: 1000 * 60 * 60
  });
  const faqs = computed(() => faqsData.value?.data || []);


const getImageUrl = (path) => {
  if (!path) return 'https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM';
  if (path.startsWith('http')) return path;
  return 'http://localhost:8000' + path; // Fallback
};

const getEmbedUrl = (url) => {
  if (!url) return '';
  
  // Handle YouTube
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    let videoId = '';
    if (url.includes('youtu.be/')) {
      videoId = url.split('youtu.be/')[1].split('?')[0];
    } else if (url.includes('youtube.com/watch')) {
      try {
        const urlParams = new URLSearchParams(new URL(url).search);
        videoId = urlParams.get('v');
      } catch (e) {
        videoId = '';
      }
    } else if (url.includes('youtube.com/embed/')) {
      return url; 
    }
    
    if (videoId) {
      return `https://www.youtube.com/embed/${videoId}`;
    }
  }
  
  return url;
};

const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('vi-VN').format(price);
};

watch(sanPham, (newProduct) => {
  if (newProduct) {
    let allImages = [];
    if (newProduct.hinh_anh_chinh) {
      allImages.push(getImageUrl(newProduct.hinh_anh_chinh));
    }
    if (newProduct.thu_vien_anh && Array.isArray(newProduct.thu_vien_anh)) {
      newProduct.thu_vien_anh.forEach(img => {
        if(img) allImages.push(getImageUrl(img));
      });
    }
    
    if (allImages.length > 0) {
      currentImage.value = allImages[0];
      images.value = allImages;
    } else {
      currentImage.value = null;
      images.value = [];
    }

    // Dynamic Title & Structured Data (SEO JSON-LD & Buyer Intent)
    const catName = newProduct.danh_muc?.ten_danh_muc || 'Vật tư máy thủy';
    document.title = `${newProduct.ten_san_pham} - Giá tốt chính hãng | JPM Marine`;
    let metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc) {
      metaDesc.setAttribute('content', `Mua ${newProduct.ten_san_pham} thuộc danh mục ${catName} tại JPM Marine. Phụ tùng và vật tư máy thủy chất lượng cao, giao hàng nhanh toàn quốc.`);
    }

    let script = document.getElementById('schema-product-ld');
    if (!script) {
      script = document.createElement('script');
      script.id = 'schema-product-ld';
      script.type = 'application/ld+json';
      document.head.appendChild(script);
    }
    script.textContent = JSON.stringify({
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": newProduct.ten_san_pham,
      "image": allImages.length > 0 ? allImages : undefined,
      "description": newProduct.mo_ta_chi_tiet || `Sản phẩm hàng hải chính hãng tại JPM Marine: ${newProduct.ten_san_pham}. Phân phối chính hãng, bảo hành toàn diện.`,
      "sku": newProduct.ma_san_pham || newProduct.slug,
      "mpn": newProduct.ma_san_pham || newProduct.slug,
      "brand": {
        "@type": "Brand",
        "name": "JPM Marine"
      },
      "category": catName,
      "offers": {
        "@type": "Offer",
        "url": window.location.href,
        "priceCurrency": "VND",
        "price": Number(newProduct.gia_khuyen_mai || newProduct.gia_ban) || 0,
        "availability": "https://schema.org/InStock"
      }
    });
  }
}, { immediate: true });

onUnmounted(() => {
  const script = document.getElementById('schema-product-ld');
  if (script) script.remove();
  let metaDesc = document.querySelector('meta[name="description"]');
  if (metaDesc) {
    metaDesc.setAttribute('content', 'JPM Marine - Đại lý phân phối phụ tùng máy thủy chính hãng Mercury, Yamaha, Honda Marine, Yanmar. Tra cứu mã OEM, sơ đồ bản vẽ rã máy chi tiết toàn quốc.');
  }
});

watch(productSlug, () => {
  qty.value = 1;
  window.scrollTo({ top: 0, behavior: 'smooth' });
});


const addToCart = (item, isRelated = false) => {
    const quantity = isRelated ? 1 : qty.value;
    cartStore.addItem(item, quantity);
    toaster.success("Thêm sản phẩm thành công");
  };

const saveMutation = useMutation({ mutationFn: (data) => axiosClient.post("/client/vat-tu-luu-tru", data), onSuccess: () => { toaster.success("Đã lưu vào Vật tư thường mua!"); }, onError: (error) => { if (error.response && error.response.status === 401) { toaster.error("Vui lòng đăng nhập để lưu vật tư!"); } else { toaster.error("Không thể lưu vật tư lúc này."); } } }); const saveToWishlist = () => { if (!sanPham.value) return; saveMutation.mutate({ san_pham_id: sanPham.value.id }); };

const scrollToTabs = () => {
  const el = document.getElementById('product-tabs');
  if(el) {
    window.scrollTo({
      top: el.offsetTop - 100,
      behavior: 'smooth'
    });
  }
};

onMounted(() => {
  window.scrollTo(0, 0);
});
</script>

<style scoped>
/* ── EDITORIAL LUXURY TWO-TONE SECTION ARCHITECTURE (MATCHING HOME PAGE) ── */
.product-detail-section {
  background-color: #ffffff !important;
}

.related-products-section {
  background-color: #f8fafc !important;
  border-top: 1px solid #e2e8f0 !important;
}

.cursor-pointer { cursor: pointer; }

/* Breadcrumb */
.breadcrumb a { color: var(--accent-color); text-decoration: none; font-weight: 500; transition: 0.2s; }
.breadcrumb a:hover { text-decoration: underline; }

/* Back to Shop (Minimalist Slate Text Navigation) */
.btn-back-diagram {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  color: #64748b;
  border: none;
  padding: 0;
  font-size: 0.88rem;
  font-weight: 500;
  box-shadow: none;
  cursor: pointer;
  text-decoration: none;
  transition: color 0.2s ease, opacity 0.15s ease;
}

.btn-back-diagram i {
  color: inherit;
  font-size: 1rem;
  line-height: 1;
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-back-diagram:hover {
  background: transparent;
  color: #0A1128;
  box-shadow: none;
  transform: none;
  text-decoration: none;
}

.btn-back-diagram:hover i {
  transform: translateX(-3px);
}

.btn-back-diagram:active {
  opacity: 0.7;
  transform: none;
}

/* ── FULL-BLEED LUXURY PRODUCT GALLERY (OPTION 2) ── */
.main-image-canvas {
  position: relative;
  width: 100%;
  height: 385px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 768px) {
  .main-image-canvas {
    height: 320px;
  }
}

.main-image-canvas:hover {
  box-shadow: none;
}

.gallery-floating-actions {
  position: absolute;
  top: 12px;
  left: 12px;
  right: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 10;
  pointer-events: none;
}

.btn-gallery-action {
  pointer-events: auto;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  font-size: 0.92rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
  cursor: pointer;
  padding: 0;
}

.btn-gallery-action:hover {
  background: #ffffff;
  color: #0f172a;
  transform: scale(1.06);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.canvas-image-wrapper {
  width: 100%;
  height: 100%;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: zoom-in;
  overflow: hidden;
}

.canvas-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.canvas-image-wrapper:hover .canvas-img {
  transform: scale(1.04);
}

.canvas-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  width: 100%;
  background: #f1f5f9;
}

/* Canvas Prev/Next Navigation */
.btn-canvas-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  font-size: 0.92rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
  cursor: pointer;
  z-index: 10;
  opacity: 0;
  padding: 0;
}

.main-image-canvas:hover .btn-canvas-nav {
  opacity: 1;
}

.btn-canvas-prev {
  left: 10px;
}

.btn-canvas-next {
  right: 10px;
}

.btn-canvas-nav:hover {
  background: #ffffff;
  color: #0f172a;
  transform: translateY(-50%) scale(1.08);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

/* Gallery Counter Badge */
.gallery-counter-badge {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 10px;
  letter-spacing: 0.05em;
  z-index: 9;
  pointer-events: none;
}

/* Thumbnail Filmstrip with Navigation */
.thumbnails-slider-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.btn-thumb-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  color: #334155;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 11;
  padding: 0;
  transition: all 0.2s ease;
}

.btn-thumb-prev {
  left: -8px;
}

.btn-thumb-next {
  right: -8px;
}

.btn-thumb-nav:hover {
  background: #ffffff;
  color: #0f172a;
  transform: translateY(-50%) scale(1.08);
}

/* Thumbnail Filmstrip */
.thumbnail-filmstrip {
  padding: 2px 0;
  overflow-x: auto;
  scrollbar-width: none;
}
.thumbnail-filmstrip::-webkit-scrollbar {
  display: none;
}

.thumb-film-item {
  width: 52px;
  height: 52px;
  border-radius: 7px;
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  box-shadow: none;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 0;
  opacity: 0.7;
  flex-shrink: 0;
}

.thumb-film-item:hover {
  opacity: 1;
  border-color: #cbd5e1;
  transform: translateY(-1px);
}

.thumb-film-item.active-thumb {
  opacity: 1 !important;
  border-color: var(--accent-color, #f82249) !important;
  border-width: 2px !important;
  box-shadow: none !important;
  transform: translateY(-1px);
}

.thumb-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Typography Helpers */
.hover-underline:hover { text-decoration: underline !important; }

/* Tabs Customization */
.custom-tabs { border-bottom: 1px solid #ddd; }
.custom-tabs .nav-link { color: var(--heading-color); font-weight: 700; font-size: 0.95rem; border: none; border-bottom: 3px solid transparent; padding: 12px 24px; background: transparent; transition: 0.3s; }
.custom-tabs .nav-link:hover { color: var(--accent-color); }
.custom-tabs .nav-link.active { color: var(--accent-color); border-bottom: 3px solid var(--accent-color); background: transparent; }

/* Text truncate */
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── SECTION TITLE LUXURY ── */
.section-catalog-title {
  color: var(--heading-color, #0A1128);
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.25rem;
  letter-spacing: -0.2px;
}

/* ── PRODUCT PURCHASING PANEL MINIMALIST UPGRADE ── */
.product-info {
  display: flex;
  flex-direction: column;
}

@media (min-width: 992px) {
  .product-info {
    height: 100%;
    min-height: 448px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
  }
  .product-info > .product-info-top {
    margin-bottom: 20px !important;
  }
  .product-info > .spec-checklist {
    margin-bottom: 20px !important;
  }
  .product-info > .product-meta-row {
    margin-bottom: 24px !important;
  }
  .product-info > .purchase-container {
    margin-top: auto;
  }
}

.product-title-block {
  margin-bottom: 12px;
}

.product-main-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.45rem;
  font-weight: 700;
  color: var(--heading-color, #0A1128);
  line-height: 1.35;
  margin-bottom: 6px;
  letter-spacing: -0.01em;
  white-space: normal;
  word-break: break-word;
  overflow-wrap: break-word;
}

@media (max-width: 768px) {
  .product-main-title {
    font-size: 1.25rem;
  }
}

.product-sku-info {
  display: flex;
  align-items: baseline;
  gap: 6px;
  margin-top: 4px;
}

.product-sku-info .sku-label {
  font-size: 0.92rem;
  font-weight: 500;
  color: #64748b;
}

.product-sku-info .sku-code {
  color: #0d6efd !important;
  font-size: 1.15rem !important;
  font-weight: 700 !important;
  letter-spacing: 0.03em;
}

.product-price-val {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-variant-numeric: lining-nums;
  font-size: 1.85rem;
  font-weight: 700;
  line-height: 1.2;
  letter-spacing: -0.02em;
}

.spec-checklist {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.spec-check-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.92rem;
  color: #475569;
  line-height: 1.45;
}

.spec-check-item .check-icon {
  color: #0d9488;
  font-size: 1.15rem;
  font-weight: 700;
  line-height: 1;
  flex-shrink: 0;
}

.product-meta-row {
  font-size: 0.92rem;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.link-view-specs {
  color: #059669;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.15s ease;
}

.link-view-specs:hover {
  color: #047857;
  text-decoration: underline;
}

.link-view-specs i {
  transition: transform 0.15s ease;
}

.link-view-specs:hover i {
  transform: translateX(3px);
}

/* Stepper 44px */
.product-qty-stepper {
  display: flex;
  align-items: center;
  height: 44px;
  width: 130px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  flex-shrink: 0;
}

.product-qty-stepper:focus-within {
  border-color: #cbd5e1;
  box-shadow: 0 0 0 3px rgba(10, 17, 40, 0.05);
}

.qty-btn {
  background: transparent;
  border: none;
  width: 36px;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
  padding: 0;
}

.qty-btn:hover:not(:disabled) {
  background-color: #f8fafc;
  color: #0A1128;
}

.qty-btn:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.qty-input {
  border: none;
  background: transparent;
  width: calc(100% - 72px);
  height: 100%;
  text-align: center;
  font-weight: 600;
  font-size: 0.95rem;
  color: #0A1128;
  outline: none;
  padding: 0;
  -moz-appearance: textfield;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Add to cart 44px */
.btn-product-cart {
  height: 44px;
  background-color: var(--accent-color, #f82249);
  color: #ffffff !important;
  border: 1px solid var(--accent-color, #f82249);
  border-radius: 8px;
  font-size: 0.92rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 0 20px;
  box-shadow: none;
  transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
}

.btn-product-cart:hover {
  background-color: #e0163c;
  border-color: #e0163c;
  color: #ffffff !important;
  transform: translateY(-1px);
}

.btn-product-cart:active {
  transform: translateY(0);
}

/* Secondary Wishlist Button 44px */
.btn-product-garage {
  height: 44px;
  min-width: 120px;
  background-color: #ffffff;
  color: #1e293b;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.88rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 0 16px;
  box-shadow: none;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.btn-product-garage:hover {
  background-color: #eff6ff;
  border-color: #bfdbfe;
  color: #0d6efd;
  transform: translateY(-1px);
}

.btn-product-garage:active {
  background-color: #dbeafe;
  border-color: #93c5fd;
  color: #0b5ed7;
  transform: translateY(0);
}

/* ── CLIENT FAQ ACCORDION (NATIVE CLIENT STYLE) ── */
.client-faq-accordion .accordion-item {
  border-bottom: 1px solid #e2e8f0 !important;
}

.client-faq-accordion .accordion-button {
  color: var(--heading-color, #0A1128);
  font-size: 0.98rem;
  line-height: 1.5;
  transition: color 0.2s ease;
}

.client-faq-accordion .accordion-button:hover {
  color: var(--accent-color, #f82249);
}

.client-faq-accordion .accordion-button:not(.collapsed) {
  color: var(--accent-color, #f82249);
  background-color: transparent !important;
  box-shadow: none !important;
}

.client-faq-accordion .accordion-button::after {
  background-size: 13px;
  transition: transform 0.25s ease, filter 0.25s ease;
}

.client-faq-accordion .accordion-button:not(.collapsed)::after {
  filter: invert(24%) sepia(85%) saturate(3000%) hue-rotate(335deg);
}

.client-faq-accordion .faq-answer-content {
  font-size: 0.92rem;
  line-height: 1.7;
  color: #475569;
}
</style>









