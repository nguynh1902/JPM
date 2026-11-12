<template>
  <div class="main-page">
    <section class="section inner-page-section product-detail-section bg-white">
      <div class="container">
        
        <!-- SKELETON LOADING -->
        <div v-if="isLoading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3 text-muted">Đang tải thông tin phụ tùng...</p>
        </div>

        <!-- ERROR / NOT FOUND -->
        <div v-else-if="isError || !phuTung" class="text-center py-5">
          <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
          <h4 class="mt-3">Không tìm thấy phụ tùng</h4>
          <p class="text-muted">Phụ tùng này có thể không tồn tại hoặc mã không hợp lệ.</p>
          <router-link to="/" class="btn btn-outline-jpm mt-3">Về trang chủ</router-link>
        </div>

        <template v-else>
          <!-- HEADER & BREADCRUMB -->
          <div class="mb-4" data-aos="fade-down">
            <!-- Title chuẩn hóa đồng bộ với các trang khác -->
            <div class="page-title-box mb-2">
              <h2 class="jpm-page-title">
                Chi Tiết <span style="color: var(--jpm-color-red-marine, #F82249);">Phụ Tùng</span>
              </h2>
              <p class="text-muted mb-0">
                Tra cứu thông số, sơ đồ lắp ráp và mã phụ tùng động cơ thủy chính hãng.
              </p>
            </div>

            <!-- Modern Breadcrumb & Back to Diagram -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
              <nav aria-label="breadcrumb" class="custom-breadcrumb">
                <ol class="breadcrumb mb-0 align-items-center">
                  <li class="breadcrumb-item">
                    <router-link to="/" class="text-decoration-none text-muted d-flex align-items-center">
                      <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                    </router-link>
                  </li>
                  <li class="breadcrumb-item"><span class="text-muted">Danh mục</span></li>
                  <li class="breadcrumb-item" v-if="phuTung.thuong_hieu">
                    <router-link v-if="phuTung.thuong_hieu?.code" :to="`/thuong-hieu/${phuTung.thuong_hieu.code.toLowerCase()}`" class="text-decoration-none text-muted text-uppercase">
                      {{ phuTung.thuong_hieu.name }}
                    </router-link>
                    <span v-else class="text-muted text-uppercase">{{ phuTung.thuong_hieu.name }}</span>
                  </li>
                  <li class="breadcrumb-item active fw-bold" style="color: var(--heading-color);" aria-current="page">
                    {{ phuTung.nhom_phu_tung ? phuTung.nhom_phu_tung.ten_nhom : 'Phụ tùng' }}
                  </li>
                </ol>
              </nav>

              <button 
                v-if="fromModelId" 
                @click="goBack" 
                class="btn-back-diagram"
                title="Quay lại sơ đồ động cơ"
              >
                <i class="bi bi-arrow-left"></i>
                <span>Quay lại sơ đồ</span>
              </button>
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
                      alt="Hình ảnh phụ tùng" 
                      class="canvas-img"
                    >
                    <div v-else class="canvas-placeholder">
                      <i class="bi bi-image text-muted fs-1 mb-2"></i>
                      <span class="text-muted small">Chưa có hình ảnh phụ tùng</span>
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
                
                <!-- PRODUCT HEADER & PRICING BLOCK (Chuyển tên phụ tùng & Part # từ header vào đây) -->
                <div class="product-info-top mb-3">
                  <div class="product-title-block mb-3">
                    <h1 class="product-main-title">
                      {{ phuTung.ten_phu_tung }}
                    </h1>
                    <div class="product-sku-info text-muted">
                      Part #: <span class="fw-bold text-dark font-monospace">{{ phuTung.ma_phu_tung }}</span>
                    </div>
                  </div>

                  <div class="pricing-block">
                    <div class="d-flex align-items-baseline gap-3">
                      <span v-if="phuTung.is_gia_lien_he" class="product-price-val text-danger">Liên hệ nhận báo giá</span>
                      <span v-else class="product-price-val text-danger">{{ formatPrice(phuTung.gia_ban) }}</span>
                    </div>
                  </div>
                </div>

                <!-- CẢNH BÁO MÃ THAY THẾ (SUPERSEDED / REPLACED BY) -->
                <div v-if="phuTung.ma_thay_thes && phuTung.ma_thay_thes.length > 0" class="alert alert-warning border border-warning-subtle rounded-3 p-3 mb-4 shadow-sm">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-arrow-repeat text-warning fs-4 flex-shrink-0"></i>
                    <div>
                      <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Lưu ý mã thay thế (Superseded Part):</div>
                      <div class="small text-secondary mb-2">Mã <strong class="text-dark">{{ phuTung.ma_phu_tung }}</strong> đã được nhà sản xuất nâng cấp và thay thế bằng mã mới:</div>
                      <div class="d-flex flex-wrap gap-2">
                        <router-link 
                          v-for="tt in phuTung.ma_thay_thes" 
                          :key="tt.id" 
                          :to="'/chi-tiet-phu-tung/' + (tt.phu_tung_moi?.ma_phu_tung || '')"
                          class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3 py-1 bg-white"
                        >
                          <i class="bi bi-box-arrow-up-right me-1"></i>
                          {{ tt.phu_tung_moi?.ma_phu_tung }} ({{ tt.phu_tung_moi?.ten_phu_tung }})
                        </router-link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- MÃ THAY THẾ CHO CÁC MÃ CŨ (INTERCHANGE / REPLACES) -->
                <div v-if="phuTung.thay_the_chos && phuTung.thay_the_chos.length > 0" class="alert alert-info border border-info-subtle rounded-3 p-3 mb-4 shadow-sm">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-shield-check text-primary fs-4 flex-shrink-0"></i>
                    <div>
                      <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Mã thay thế tương đương cho phụ tùng cũ:</div>
                      <div class="small text-secondary mb-2">Phụ tùng này thay thế chuẩn OEM cho các mã cũ đã ngưng sản xuất:</div>
                      <div class="d-flex flex-wrap gap-2">
                        <span 
                          v-for="tc in phuTung.thay_the_chos" 
                          :key="tc.id" 
                          class="badge bg-white text-dark border px-2 py-1 font-monospace"
                          style="font-size: 0.85rem;"
                        >
                          {{ tc.phu_tung_cu?.ma_phu_tung }} ({{ tc.phu_tung_cu?.ten_phu_tung }})
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- SPEC CHECKLIST -->
                <div class="spec-checklist mb-3">
                  <div class="spec-check-item">
                    <i class="bi bi-check2 check-icon"></i>
                    <span>Tương thích chính xác theo sơ đồ chi tiết động cơ</span>
                  </div>
                  <div class="spec-check-item">
                    <i class="bi bi-check2 check-icon"></i>
                    <span>Tiêu chuẩn OEM chính hãng mới 100%, kèm CO/CQ khi cần</span>
                  </div>
                  <div class="spec-check-item">
                    <i class="bi bi-check2 check-icon"></i>
                    <span>Hỗ trợ kỹ thuật JPM Marine đối soát mã trước khi giao</span>
                  </div>
                </div>

                <!-- META INFO & SPECS LINK -->
                <div class="product-meta-row mb-3 text-muted small">
                  <div class="mb-2">
                    <span>Hãng sản xuất: </span>
                    <strong class="text-dark">{{ phuTung.thuong_hieu?.name || 'Đang cập nhật' }}</strong>
                  </div>
                  <div>
                    <a href="#product-tabs" class="link-view-specs d-inline-flex align-items-center gap-1" @click.prevent="scrollToTabs">
                      <span>Xem chi tiết cấu hình</span>
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
                          :max="maxStock" 
                          @change="qty = Math.min(Math.max(1, qty), maxStock)"
                        >
                        <button class="qty-btn" @click="qty < maxStock ? qty++ : null" :disabled="qty >= maxStock" title="Tăng số lượng">
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>

                      <!-- Add to Cart -->
                      <button class="btn btn-product-cart flex-grow-1" @click="addToCart(phuTung)">
                        <i class="bi bi-cart2 me-1"></i>
                        <span>Thêm vào giỏ</span>
                      </button>

                      <!-- Garage Button -->
                      <button class="btn btn-product-garage" title="Lưu vào Garage của bạn" @click="openGarageModal">
                        <i class="bi bi-tools me-1"></i>
                        <span>+ Garage</span>
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
                      <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">MÔ TẢ CHI TIẾT</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">THÔNG SỐ KỸ THUẬT</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="diagrams-tab" data-bs-toggle="tab" data-bs-target="#diagrams" type="button" role="tab">SƠ ĐỒ TƯƠNG THÍCH</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">VIDEO</button>
                      </li>
                  </ul>
                  <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active lh-lg" style="color: var(--heading-color);" id="desc" role="tabpanel">
                      <div v-if="phuTung.mo_ta" v-html="phuTung.mo_ta"></div>
                      <div v-else>
                        <p>Phụ tùng chính hãng OEM. Đảm bảo chất lượng cao nhất, chống chịu môi trường làm việc khắc nghiệt.</p>
                        <p>Sản phẩm đạt tiêu chuẩn OEM khắt khe nhất. Chúng tôi cam kết mang đến phụ tùng chất lượng cao, bền bỉ.</p>
                      </div>
                    </div>
                    <div class="tab-pane fade" style="color: var(--heading-color);" id="specs" role="tabpanel">
                      <div v-if="phuTung.thong_so_ky_thuat" v-html="phuTung.thong_so_ky_thuat"></div>
                      <ul v-else class="list-unstyled lh-lg">
                        <li><strong>Đơn vị tính:</strong> {{ phuTung.don_vi_tinh || 'Cái' }}</li>
                        <li v-if="phuTung.thuong_hieu"><strong>Hãng sản xuất:</strong> {{ phuTung.thuong_hieu.name }}</li>
                        <li v-if="phuTung.nhom_phu_tung"><strong>Nhóm:</strong> {{ phuTung.nhom_phu_tung.ten_nhom }}</li>
                      </ul>
                    </div>
                    <div class="tab-pane fade" id="diagrams" role="tabpanel">
                      <div v-if="diagramGroups.length > 0">
                         <div v-for="(group, idx) in diagramGroups" :key="idx" class="mb-5 pb-3 border-bottom">
                            <h5 class="fw-bold text-uppercase mb-3" style="font-size: 1.1rem; color: #333;">{{ group.diagramName }}</h5>
                            <div class="row">
                               <div class="col-md-6 mb-3" v-for="(model, mIdx) in group.models" :key="mIdx">
                                  <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.85rem;">{{ model.name }}</span>
                                  <span class="mx-2 text-muted">—</span>
                                  <span class="text-primary fw-medium" style="font-size: 0.9rem;">
                                    <template v-for="(year, yIdx) in model.years" :key="yIdx">
                                      <span class="badge bg-light text-dark border me-1">{{ year }}</span>
                                    </template>
                                  </span>
                               </div>
                            </div>
                         </div>
                      </div>
                      <div v-else class="p-4 bg-light rounded text-center">
                         <i class="bi bi-diagram-3 fs-1 text-muted mb-2"></i>
                         <p class="text-muted mb-0">Chưa có dữ liệu sơ đồ tương thích cho phụ tùng này.</p>
                      </div>
                      </div>

                      <!-- VIDEO PANE -->
                      <div class="tab-pane fade" style="color: var(--heading-color);" id="videos" role="tabpanel">
                        <div v-if="phuTung && phuTung.video_url" class="row">
                          <div class="col-lg-8">
                            <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
                              <iframe :src="getEmbedUrl(phuTung.video_url)" title="Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                          </div>
                        </div>
                        <div v-else class="py-2 text-muted">
                          <p>Chưa có video giới thiệu cho phụ tùng này.</p>
                        </div>
                      </div>
                  </div>
                </div>
             </div>
          </div>


        </template>
      </div>
    </section>

    <!-- NEW SECTIONS -->
    <section class="section related-products-section py-5 border-top">
      <div class="container">
        <!-- PHỤ TÙNG LIÊN QUAN -->
        <div class="mb-5" data-aos="fade-up" v-if="relatedParts && relatedParts.length > 0">
          <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
            <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
              Những Sản Phẩm Khác
            </h4>
            <router-link to="/san-pham" class="text-decoration-none text-danger fw-bold d-flex align-items-center gap-1 hover-underline" style="font-size: 0.95rem;">
              Xem tất cả <i class="bi bi-arrow-right"></i>
            </router-link>
          </div>
          <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <div class="col" v-for="part in relatedParts.slice(0, 8)" :key="part.id">
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

    <!-- GARAGE MODAL -->
    <div class="modal fade" id="addToGarageModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom-0 bg-light">
            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-tools text-danger me-2"></i>Thêm Phụ Tùng Vào Máy</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            
            <div v-if="isLoadingGarages || isCreatingGarage" class="text-center py-5">
                <div class="spinner-border text-danger" role="status"></div>
                <p class="mt-3 text-muted">{{ isCreatingGarage ? 'Đang tạo máy mới và lưu phụ tùng...' : 'Đang tải dữ liệu...' }}</p>
            </div>
            
            <template v-else>
                <!-- MÁY ĐÃ CÓ -->
                <div class="p-4 bg-light border-bottom" v-if="garages.length > 0">
                    <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.85rem; color: #64748b;">1. LƯU VÀO MÁY ĐANG CÓ (GARAGE CỦA BẠN)</h6>
                    <div class="list-group">
                        <button v-for="g in garages" :key="g.id" type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3" @click="saveToGarage(g.id)">
                            <div class="bg-white rounded p-2 border shadow-sm">
                                <i class="bi bi-engine-fill text-danger fs-4"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="mb-1 fw-bold text-dark">{{ g.ten_may || g.dong_co_model?.ten_model || g.dong_co_model?.model_name || 'Máy không tên' }}</h6>
                                <small class="text-muted">S/N: {{ g.so_serial || 'N/A' }} | Model: {{ g.dong_co_model?.ten_model || g.dong_co_model?.model_name }}</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </button>
                    </div>
                </div>

                <!-- TẠO NHANH MÁY MỚI TỪ MODEL TƯƠNG THÍCH -->
                <div class="p-4">
                    <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.85rem; color: #64748b;">
                        {{ garages.length > 0 ? '2. HOẶC TẠO NHANH MÁY MỚI TỪ MODEL TƯƠNG THÍCH' : 'TẠO NHANH MÁY TỪ MODEL TƯƠNG THÍCH VỚI PHỤ TÙNG NÀY' }}
                    </h6>
                    
                    <div v-if="compatibleModels.length === 0" class="alert alert-light border text-center p-4">
                        <i class="bi bi-info-circle text-muted fs-3 d-block mb-2"></i>
                        Phụ tùng này chưa có dữ liệu tương thích Model nào.
                        <router-link to="/garage" class="d-block mt-3 text-danger fw-bold text-decoration-none" @click="closeGarageModal">
                            <i class="bi bi-plus-circle me-1"></i>Đến Garage để tạo máy thủ công
                        </router-link>
                    </div>
                    
                    <div v-else class="list-group" style="max-height: 350px; overflow-y: auto;">
                        <button v-for="(model, idx) in compatibleModels" :key="idx" type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3" @click="createGarageAndSave(model)">
                            <div class="text-start">
                                <h6 class="mb-1 fw-bold text-dark">{{ model.name }}</h6>
                                <small class="text-muted"><span class="badge bg-light text-dark border me-1">{{ model.brand }}</span> Năm: {{ model.years }}</small>
                            </div>
                            <div class="d-flex align-items-center text-primary fw-bold" style="font-size: 0.85rem;">
                                TẠO MÁY <i class="bi bi-plus-circle-fill ms-2 fs-5"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </template>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';
import { useCartStore } from '../../../stores/cartStore';
import ProductCard from '../../../components/client/ProductCard.vue';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.min.css';

const cartStore = useCartStore();
const toaster = createToaster({ position: 'top-right', duration: 3000 });

const route = useRoute();
const router = useRouter();
const partCode = computed(() => route.params.code);

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

const goBack = () => {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push('/');
  }
};

const handleShare = async () => {
  try {
    if (navigator.clipboard) {
      await navigator.clipboard.writeText(window.location.href);
      toaster.success("Đã sao chép liên kết phụ tùng!");
    } else {
      toaster.info("Liên kết: " + window.location.href);
    }
  } catch (e) {
    toaster.info("Liên kết: " + window.location.href);
  }
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

// Fetch Part Details
const { data, isLoading, isError } = useQuery({
  queryKey: ['partDetail', partCode],
  queryFn: () => axiosClient.get(`/public/phu-tung/${partCode.value}`),
  enabled: !!partCode.value
});

const phuTung = computed(() => data.value?.data?.phu_tung || null);
const maxStock = computed(() => {
  const stock = phuTung.value?.so_luong_ton;
  return (stock !== null && stock !== undefined && stock > 0) ? stock : 999;
});
const relatedParts = computed(() => data.value?.data?.related_parts || []);

  const { data: faqsData } = useQuery({
    queryKey: ['public-faqs'],
    queryFn: () => axiosClient.get('/public/faqs', { params: { limit: 4 } }),
    staleTime: 1000 * 60 * 60
  });
  const faqs = computed(() => faqsData.value?.data || []);


const diagramGroups = computed(() => {
  if (!phuTung.value || !phuTung.value.so_do_phu_tungs) return [];
  
  const groups = {};
  phuTung.value.so_do_phu_tungs.forEach(sodo => {
    const name = sodo.ten_so_do;
    if (!groups[name]) {
      groups[name] = { diagramName: name, models: {} };
    }
    
    const model = sodo.model;
    if (model) {
      const modelName = (model.thuong_hieu?.name || '') + ' ' + (model.ten_model || model.ma_model);
      if (!groups[name].models[modelName]) {
        groups[name].models[modelName] = { name: modelName.trim(), years: new Set() };
      }
      
      if (model.nam_san_xuat) {
        groups[name].models[modelName].years.add(model.nam_san_xuat);
      } else if (model.nam_bat_dau) {
        const start = parseInt(model.nam_bat_dau);
        const end = model.nam_ket_thuc ? parseInt(model.nam_ket_thuc) : new Date().getFullYear();
        for (let y = start; y <= end; y++) {
          groups[name].models[modelName].years.add(y);
        }
      } else {
         groups[name].models[modelName].years.add("Tất cả năm");
      }
    }
  });

  return Object.values(groups).map(g => {
    return {
      diagramName: g.diagramName,
      models: Object.values(g.models).map(m => ({
        name: m.name,
        years: Array.from(m.years).sort()
      }))
    };
  });
});


  const getEmbedUrl = (url) => {
    if (!url) return '';
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

  const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  return 'http://localhost:8000' + path;
};

watch(phuTung, (newPart) => {
  if (newPart) {
    let allImages = [];
    if (newPart.hinh_anh) {
      allImages.push(getImageUrl(newPart.hinh_anh));
    }
    if (newPart.thu_vien_anh && Array.isArray(newPart.thu_vien_anh)) {
      newPart.thu_vien_anh.forEach(img => {
        allImages.push(getImageUrl(img));
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
    const brandName = newPart.thuong_hieu?.name || 'Marine';
    document.title = `${newPart.ma_phu_tung} - ${newPart.ten_phu_tung} | Phụ tùng máy thủy ${brandName} chính hãng - JPM Marine`;
    
    let metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc) {
      metaDesc.setAttribute('content', `Tra cứu và mua phụ tùng máy thủy chính hãng mã ${newPart.ma_phu_tung} (${newPart.ten_phu_tung}) dùng cho động cơ ${brandName}. Sơ đồ rã máy, mã thay thế tương đương tại JPM Marine.`);
    }

    let script = document.getElementById('schema-part-ld');
    if (!script) {
      script = document.createElement('script');
      script.id = 'schema-part-ld';
      script.type = 'application/ld+json';
      document.head.appendChild(script);
    }
    script.textContent = JSON.stringify({
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": `${newPart.ma_phu_tung} - ${newPart.ten_phu_tung}`,
      "image": allImages.length > 0 ? allImages : undefined,
      "description": newPart.mo_ta || `Phụ tùng chính hãng ${brandName} mã ${newPart.ma_phu_tung}. Tra cứu sơ đồ phân rã và đặt hàng tại JPM Marine.`,
      "sku": newPart.ma_phu_tung,
      "mpn": newPart.ma_phu_tung,
      "brand": {
        "@type": "Brand",
        "name": brandName
      },
      "offers": {
        "@type": "Offer",
        "url": window.location.href,
        "priceCurrency": "VND",
        "price": Number(newPart.gia_ban) || 0,
        "availability": (newPart.so_luong_ton > 0) ? "https://schema.org/InStock" : "https://schema.org/PreOrder"
      }
    });
  }
}, { immediate: true });

onUnmounted(() => {
  const script = document.getElementById('schema-part-ld');
  if (script) script.remove();
  let metaDesc = document.querySelector('meta[name="description"]');
  if (metaDesc) {
    metaDesc.setAttribute('content', 'JPM Marine - Đại lý phân phối phụ tùng máy thủy chính hãng Mercury, Yamaha, Honda Marine, Yanmar. Tra cứu mã OEM, sơ đồ bản vẽ rã máy chi tiết toàn quốc.');
  }
});

watch(partCode, () => {
  // Reset state when route changes
  qty.value = 1;
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Format Price helper
const formatPrice = (price) => {
  if (!price) return '0 ₫';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

// Add to Cart Interaction
const addToCart = (item, isRelated = false) => {
    const quantity = isRelated ? 1 : qty.value;
    
    // Add to Pinia cartStore (persists to localStorage)
    cartStore.addItem(item, quantity);

    toaster.success("Thêm sản phẩm thành công");
  };



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

import { useAuthStore } from '../../../stores/authStore';
const authStore = useAuthStore();

const saveToGarageMutation = useMutation({
    mutationFn: (data) => axiosClient.post("/client/garage-phu-tung", data),
    onSuccess: () => {
        toaster.success("Đã thêm phụ tùng vào máy!");
        closeGarageModal();
    },
    onError: () => {
        toaster.error("Không thể lưu lúc này!");
    }
});

const saveToGarage = async (garageId) => {
    if (!phuTung.value) return;
    await saveToGarageMutation.mutateAsync({
        garage_id: garageId,
        phu_tung_id: phuTung.value.id,
        so_luong: qty.value
    });
};

const queryClient = useQueryClient();

const compatibleModels = computed(() => {
  if (!phuTung.value || !phuTung.value.so_do_phu_tungs) return [];
  const modelMap = new Map();
  phuTung.value.so_do_phu_tungs.forEach(sodo => {
    if (sodo.model) {
      const m = sodo.model;
      if (!modelMap.has(m.id)) {
        modelMap.set(m.id, {
          id: m.id,
          name: m.ten_model || m.ma_model,
          brand: m.thuong_hieu?.name || 'Chưa rõ',
          years: m.nam_san_xuat ? String(m.nam_san_xuat) : (m.nam_bat_dau ? `${m.nam_bat_dau} - ${m.nam_ket_thuc || 'Nay'}` : 'Tất cả năm')
        });
      }
    }
  });
  return Array.from(modelMap.values());
});

const isCreatingGarage = ref(false);

const createGarageAndSave = async (model) => {
    if (!phuTung.value) return;
    
    isCreatingGarage.value = true;
    try {
        const res = await axiosClient.post("/client/garage", {
            dong_co_model_id: model.id,
            ten_may: model.name + ' của tôi'
        });
        
        // axiosClient intercepts and returns response.data directly
        const newGarageId = res.may ? res.may.id : res.id;
        queryClient.invalidateQueries({ queryKey: ['clientGarage'] });
        
        await saveToGarageMutation.mutateAsync({
            garage_id: newGarageId,
            phu_tung_id: phuTung.value.id,
            so_luong: qty.value
        });
        
    } catch (error) {
        toaster.error("Không thể tự động tạo máy lúc này!");
        console.error(error);
    } finally {
        isCreatingGarage.value = false;
    }
};

const { data: garagesData, isLoading: isLoadingGarages } = useQuery({
  queryKey: ['clientGarage'],
  queryFn: () => axiosClient.get('/client/garage'),
  enabled: false // Fetch manually when needed
});

// Fallback properly since axiosClient returns response.data
const garages = computed(() => Array.isArray(garagesData.value) ? garagesData.value : (garagesData.value?.data || []));

let garageModalInstance = null;
const fromModelId = computed(() => route.query.from_model_id);

const openGarageModal = async () => {
    if (!authStore.user) {
        toaster.error("Vui lòng đăng nhập để thêm vào Garage!");
        return;
    }

    // Auto-save logic if user came from a specific model diagram
    if (fromModelId.value) {
        const targetModelId = parseInt(fromModelId.value);
        const modelToSave = compatibleModels.value.find(m => parseInt(m.id) === targetModelId);
        
        if (modelToSave) {
            toaster.info("Đang xử lý lưu tự động...");
            try {
                // Fetch user garages to check existence
                const res = await queryClient.fetchQuery({ queryKey: ['clientGarage'], queryFn: () => axiosClient.get('/client/garage') });
                const currentGarages = Array.isArray(res) ? res : (res?.data || []);
                
                // Use parseInt on both sides to avoid type mismatch ("14" !== 14)
                const existingGarage = currentGarages.find(g => parseInt(g.dong_co_model_id) === targetModelId);
                
                if (existingGarage) {
                    await saveToGarage(existingGarage.id);
                } else {
                    await createGarageAndSave(modelToSave);
                }
            } catch (e) {
                toaster.error("Có lỗi xảy ra khi lưu tự động!");
                console.error("Auto-save error:", e);
            }
            return; // Dừng, không mở Modal nữa!
        }
    }

    // Otherwise, open Modal normally
    const el = document.getElementById('addToGarageModal');
    if (el) {
        if(!garageModalInstance) garageModalInstance = new window.bootstrap.Modal(el);
        garageModalInstance.show();
        queryClient.prefetchQuery({ queryKey: ['clientGarage'], queryFn: () => axiosClient.get('/client/garage') });
    }
};

const closeGarageModal = () => {
    if (garageModalInstance) {
        garageModalInstance.hide();
    }
};

watch(compatibleModels, (models) => {
    if (models.length > 0 && route.query.auto_save === 'true' && fromModelId.value) {
        // Clear the query to prevent re-triggering on refresh
        const newQuery = { ...route.query };
        delete newQuery.auto_save;
        router.replace({ query: newQuery });
        
        openGarageModal();
    }
}, { immediate: true });
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

/* Back to Diagram (Minimalist Slate Text Navigation) */
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

/* ── PRODUCT PURCHASING PANEL MINIMALIST UPGRADE ── */
.product-info {
  display: flex;
  flex-direction: column;
}

@media (min-width: 992px) {
  .product-info {
    height: 100%;
    min-height: 448px;
    justify-content: space-between;
  }
  .product-info > .product-info-top,
  .product-info > .spec-checklist,
  .product-info > .product-meta-row {
    margin-bottom: 0 !important;
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
  font-size: 0.95rem;
  font-weight: 500;
  color: #64748b;
}

.product-sku-info .font-monospace {
  letter-spacing: 0.02em;
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

/* Garage Action Button 44px */
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
</style>



















