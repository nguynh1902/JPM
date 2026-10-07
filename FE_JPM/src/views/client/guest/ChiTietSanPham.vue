<template>
  <main class="main-page">
    <section class="section" style="padding-top: 110px;">
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
            <!-- Title -->
            <div class="page-title-box mb-2">
              <h2 class="jpm-page-title text-uppercase" style="font-size: 1.8rem; line-height: 1.3;">
                {{ sanPham.ten_san_pham }} | {{ sanPham.ma_san_pham }}
              </h2>
              <p class="text-muted mb-2 fw-medium" style="font-size: 1.1rem;">
                SKU #: <span class="text-dark fw-bold">{{ sanPham.ma_san_pham }}</span>
              </p>
            </div>

            <!-- Modern Breadcrumb -->
            <nav aria-label="breadcrumb" class="custom-breadcrumb pt-2">
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
          </div>

          <div class="row g-4 mb-5">
            
            <!-- LEFT: IMAGE GALLERY (col-lg-5) -->
            <div class="col-lg-5" data-aos="fade-right">
              <div class="product-gallery position-relative mb-3 text-center">
                
                <!-- Main Image Card -->
                <div class="main-image position-relative border rounded bg-white shadow-sm overflow-hidden" style="width: 100%; height: 350px;">
                  <button class="btn btn-sm btn-light position-absolute border shadow-sm" style="top: 15px; left: 15px; z-index: 10;" title="Chia sẻ">
                    <i class="bi bi-share"></i>
                  </button>
                  <img v-if="currentImage" :src="getImageUrl(currentImage)" alt="Product Image" class="w-100 h-100" style="object-fit: cover; cursor: pointer;" @click="openLightbox">
                  <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                     <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                  </div>
                </div>

                <!-- Thumbnails List -->
                <div class="thumbnail-list d-flex justify-content-center gap-3 mt-3" v-if="images.length > 1">
                  <div 
                    v-for="(img, idx) in images" 
                    :key="idx" 
                    class="thumb-item rounded cursor-pointer overflow-hidden shadow-sm bg-white"
                    :class="{'active-thumb': currentImage === img}"
                    @click="currentImage = img"
                    style="width: 75px; height: 75px;"
                  >
                    <img :src="getImageUrl(img)" class="w-100 h-100" style="object-fit: cover;">
                  </div>
                </div>
              </div>
            </div>

            <!-- MIDDLE: PRODUCT INFO (col-lg-7) -->
            <div class="col-lg-7" data-aos="fade-up">
              <div class="product-info pe-lg-3">
                
                <div class="pricing-block mb-4">
                  <div class="d-flex align-items-end gap-3 mb-1">
                    <span v-if="sanPham.is_gia_lien_he || !sanPham.gia_ban" class="fs-3 fw-bold text-danger">Liên hệ nhận báo giá</span>
                    <span v-else class="fs-3 fw-bold text-danger">{{ formatPrice(sanPham.gia_ban) }} đ</span>
                  </div>
                </div>

                

                <ul class="list-unstyled mb-4 text-muted lh-lg">
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Cam kết hàng chính hãng 100%.</li>
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Đổi trả dễ dàng nếu có lỗi do NSX.</li>
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Đạt tiêu chuẩn an toàn hàng hải.</li>
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Phân phối ủy quyền bởi JPM Marine.</li>
                </ul>

                <a href="#product-tabs" class="text-danger fw-bold text-decoration-none d-inline-block mb-4 hover-underline" @click.prevent="scrollToTabs">Xem bài viết mô tả chi tiết <i class="bi bi-arrow-right"></i></a>

                <div class="meta-info mb-4">
                  <div class="mb-1"><span class="fw-bold text-dark">TRẠNG THÁI:</span> <span :class="sanPham.so_luong_ton > 0 ? 'text-success' : 'text-danger'" class="fw-bold">{{ sanPham.so_luong_ton > 0 ? 'Còn hàng' : 'Tạm hết' }}</span></div>
                  <div><span class="text-muted"><i class="bi bi-headset text-danger me-1"></i> Hỗ trợ tư vấn mua sắm chuyên sâu</span></div>
                </div>

                <div class="purchase-container" style="max-width: 550px;">
                  <div class="action-block mb-4 pt-4 border-top">
                    <div class="d-flex gap-3">
                      <div class="quantity-selector d-flex align-items-center bg-light border rounded" style="width: 140px;">
                        <button class="btn btn-sm bg-transparent border-0 px-3 h-100 shadow-none text-dark" @click="qty > 1 ? qty-- : null" :disabled="qty <= 1"><i class="bi bi-dash fs-5"></i></button>
                        <input type="number" v-model.number="qty" class="form-control border-0 bg-transparent text-center fw-bold h-100 px-0 shadow-none" min="1" :max="sanPham.so_luong_ton || 999" @change="qty = Math.min(Math.max(1, qty), sanPham.so_luong_ton || 999)">
                        <button class="btn btn-sm bg-transparent border-0 px-3 h-100 shadow-none text-dark" @click="qty < (sanPham.so_luong_ton || 999) ? qty++ : null" :disabled="qty >= (sanPham.so_luong_ton || 999)"><i class="bi bi-plus fs-5"></i></button>
                      </div>
                      <button class="btn btn-jpm flex-grow-1 fs-6 fw-bold text-uppercase shadow-none" @click="addToCart(sanPham)">
                        Thêm Vào Giỏ
                      </button>
                      <button class="btn btn-outline-jpm rounded shadow-none fw-bold d-flex align-items-center justify-content-center gap-1 text-uppercase" style="width: 140px; font-size: 0.85rem;" title="Lưu vật tư thường mua" @click="saveToWishlist">
                        <i class="bi bi-bookmark-heart"></i> Lưu Vật Tư
                      </button>
                    </div>
                  </div>

                  <div class="trust-badges d-flex align-items-center justify-content-between text-dark small fw-bold mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-award text-danger fs-5"></i> Chính Hãng OEM</div>
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-shield-check text-danger fs-5"></i> Đổi Trả Linh Hoạt</div>
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-gear-wide-connected text-danger fs-5"></i> Hỗ Trợ 24/7</div>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- End Row 1 -->


          <!-- TABS SECTION -->
          <div class="row mb-5" id="product-tabs" data-aos="fade-up">
             <div class="col-lg-12">
                <div class="product-tabs pt-4 border-top">
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
    <section class="section light-background py-5 border-top">
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
          <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-3" v-for="part in relatedProducts.slice(0, 8)" :key="part.id">
                            <div class="card h-100 premium-product-card group-hover position-relative" style="border: 1px solid #e5e7eb; border-radius: 0; box-shadow: none;">
                  <div class="card-img-wrapper overflow-hidden img-zoom-hover" style="border-radius: 0;">
                    <img
                      :src="part.hinh_anh_chinh ? getImageUrl(part.hinh_anh_chinh) : 'https://images.unsplash.com/photo-1559523161-0fc0d8b38a7a?auto=format&fit=crop&q=80&w=400'"
                      class="card-img-top w-100 transition-transform"
                      style="aspect-ratio: 4/3; object-fit: cover; border-radius: 0;"
                      :alt="part.ten_san_pham"
                      @error="$event.target.src='https://placehold.co/300x200/f8f9fa/a1acb8?text=JPM+Marine'"
                    >
                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                      <button class="btn bg-white shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 4px;" title="Thêm vào giỏ" @click.prevent="addToCart(part, true)">
                        <i class="bi bi-cart-plus" style="color: #dc3545; font-size: 1.1rem;"></i>
                      </button>
                    </div>
                  </div>
                  <div class="card-body text-start p-3 d-flex flex-column bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ part.ma_san_pham }}</span>
                    </div>
                    <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                      <router-link :to="`/chi-tiet-san-pham/${part.slug}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ part.ten_san_pham }}</router-link>
                    </h5>
                    <div class="mt-auto d-flex align-items-center contact-action" style="border-top: 1px solid #f1f5f9; padding-top: 0.75rem; justify-content: flex-end;">
                      <span v-if="part.is_gia_lien_he || !part.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                      <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ formatPrice(part.gia_ban) }} đ</span>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
        
        <!-- CÂU HỎI THƯỜNG GẶP -->
        <div class="mb-5" data-aos="fade-up">
          <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
            <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
              Câu Hỏi Thường Gặp
            </h4>
            <a href="#" class="text-decoration-none text-danger fw-bold d-flex align-items-center gap-1 hover-underline" style="font-size: 0.95rem;">
              Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
          </div>
          
          
            <div class="row g-4" v-if="faqs.length > 0">
              <!-- Cột trái -->
              <div class="col-lg-6">
                <div class="faq-list-container d-flex flex-column">
                  <div v-for="faq in faqs.slice(0, Math.ceil(faqs.length / 2))" :key="faq.id" class="faq-list-item d-flex pb-4 border-bottom position-relative mb-4 h-100">
                    <div class="flex-shrink-0 mt-0">
                      <i class="bi bi-question-circle-fill text-danger fs-4"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                      <h6 class="fw-bold mb-2" style="font-size: 1.05rem; line-height: 1.4;">
                        <a href="#" class="text-dark text-decoration-none stretched-link transition-color" onmouseover="this.style.color='#f82249'" onmouseout="this.style.color=''">{{ faq.question }}</a>
                      </h6>
                      <div class="text-danger fw-bold text-uppercase mt-2" style="letter-spacing: 0.5px; font-size: 0.8rem;">Xem chi tiết <i class="bi bi-arrow-right ms-1"></i></div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Cột phải -->
              <div class="col-lg-6">
                <div class="faq-list-container d-flex flex-column">
                  <div v-for="faq in faqs.slice(Math.ceil(faqs.length / 2))" :key="faq.id" class="faq-list-item d-flex pb-4 border-bottom position-relative mb-4 h-100">
                    <div class="flex-shrink-0 mt-0">
                      <i class="bi bi-question-circle-fill text-danger fs-4"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                      <h6 class="fw-bold mb-2" style="font-size: 1.05rem; line-height: 1.4;">
                        <a href="#" class="text-dark text-decoration-none stretched-link transition-color" onmouseover="this.style.color='#f82249'" onmouseout="this.style.color=''">{{ faq.question }}</a>
                      </h6>
                      <div class="text-danger fw-bold text-uppercase mt-2" style="letter-spacing: 0.5px; font-size: 0.8rem;">Xem chi tiết <i class="bi bi-arrow-right ms-1"></i></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
  </main>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery, useMutation } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';
import { useCartStore } from '../../../stores/cartStore';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.min.css';

const cartStore = useCartStore();
const toaster = createToaster({ position: 'top-right', duration: 3000 });

const route = useRoute();
const productSlug = computed(() => route.params.slug);

const qty = ref(1);
const currentImage = ref(null);
const images = ref([]);

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
  }
}, { immediate: true });

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
.cursor-pointer { cursor: pointer; }

/* Breadcrumb */
.breadcrumb a { color: var(--accent-color); text-decoration: none; font-weight: 500; transition: 0.2s; }
.breadcrumb a:hover { text-decoration: underline; }

/* Product Gallery */
.thumb-item { transition: 0.2s; opacity: 0.5; border: 2px solid transparent; }
.thumb-item:hover { opacity: 0.8; }
.active-thumb { opacity: 1 !important; border-color: var(--accent-color) !important; }

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
</style>









