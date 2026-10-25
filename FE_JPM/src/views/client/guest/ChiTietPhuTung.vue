<template>
  <main class="main-page">
    <section class="section" style="padding-top: 110px;">
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
            <!-- Title -->
            <div class="page-title-box mb-2">
              <h2 class="jpm-page-title" style="font-size: 1.6rem; line-height: 1.3;">
                {{ phuTung.ten_phu_tung }} | {{ phuTung.ma_phu_tung }}
              </h2>
              <p class="text-muted mb-2 fw-medium" style="font-size: 1.1rem;">
                Part #: <span class="text-dark fw-bold">{{ phuTung.ma_phu_tung }}</span>
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
                <li class="breadcrumb-item"><span class="text-muted">Danh mục</span></li>
                  <li class="breadcrumb-item" v-if="phuTung.thuong_hieu">
                    <span class="text-muted text-uppercase">{{ phuTung.thuong_hieu.name }}</span>
                  </li>
                <li class="breadcrumb-item active fw-bold" style="color: var(--heading-color);" aria-current="page">
                  {{ phuTung.nhom_phu_tung ? phuTung.nhom_phu_tung.ten_nhom : 'Phụ tùng' }}
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
                <div class="thumbnail-list d-flex justify-content-center gap-3 mt-3">
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
                    <span v-if="phuTung.is_gia_lien_he" class="fs-3 fw-bold text-danger">Liên hệ nhận báo giá</span>
                    <span v-else class="fs-3 fw-bold text-danger">{{ formatPrice(phuTung.gia_ban) }}</span>
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

                <ul class="list-unstyled mb-4 text-muted lh-lg">
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Đảm bảo hoàn toàn tương thích (Fitment).</li>
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Phụ tùng OEM chính hãng, mới 100%.</li>
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Cung cấp đầy đủ CO, CQ nếu cần.</li>
                  <li><i class="bi bi-check-circle-fill text-danger me-2"></i> Đại lý phân phối ủy quyền JPM Marine.</li>
                </ul>

                <a href="#product-tabs" class="text-danger fw-bold text-decoration-none d-inline-block mb-4 hover-underline" @click.prevent="scrollToTabs">Xem chi tiết cấu hình <i class="bi bi-arrow-right"></i></a>

                <div class="meta-info mb-4">
                  <div class="mb-1"><span class="fw-bold text-dark">HÃNG SẢN XUẤT:</span> <span class="text-muted text-uppercase">{{ phuTung.thuong_hieu ? phuTung.thuong_hieu.name : 'Đang cập nhật' }}</span></div>
                  <div><span class="text-muted"><i class="bi bi-headset text-danger me-1"></i> Hỗ trợ tư vấn kỹ thuật chuyên sâu</span></div>
                </div>

                <div class="purchase-container" style="max-width: 550px;">
                  <div class="action-block mb-4 pt-4 border-top">
                    <div class="d-flex gap-3">
                      <div class="quantity-selector d-flex align-items-center bg-light border rounded" style="width: 140px;">
                        <button class="btn btn-sm bg-transparent border-0 px-3 h-100 shadow-none text-dark" @click="qty > 1 ? qty-- : null" :disabled="qty <= 1"><i class="bi bi-dash fs-5"></i></button>
                        <input type="number" v-model.number="qty" class="form-control border-0 bg-transparent text-center fw-bold h-100 px-0 shadow-none" min="1" :max="phuTung.so_luong_ton || 999" @change="qty = Math.min(Math.max(1, qty), phuTung.so_luong_ton || 999)">
                        <button class="btn btn-sm bg-transparent border-0 px-3 h-100 shadow-none text-dark" @click="qty < (phuTung.so_luong_ton || 999) ? qty++ : null" :disabled="qty >= (phuTung.so_luong_ton || 999)"><i class="bi bi-plus fs-5"></i></button>
                      </div>
                      <button class="btn btn-jpm flex-grow-1 fs-6 fw-bold text-uppercase shadow-none" @click="addToCart(phuTung)">
                        Thêm Vào Giỏ
                      </button>
                      <button class="btn btn-outline-jpm rounded shadow-none fw-bold d-flex align-items-center justify-content-center gap-1 text-uppercase" style="width: 140px; font-size: 0.85rem;" title="Thêm vào Garage" @click="openGarageModal">
                        <i class="bi bi-tools"></i> + GARAGE
                      </button>
                    </div>
                  </div>

                  <div class="trust-badges d-flex align-items-center justify-content-between text-dark small fw-bold mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-award text-danger fs-5"></i> Chính Hãng OEM</div>
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-shield-check text-danger fs-5"></i> Bảo Hành Uy Tín</div>
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-gear-wide-connected text-danger fs-5"></i> Hỗ Trợ Kỹ Thuật</div>
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
                                      <a href="#" class="text-primary text-decoration-none hover-underline">{{ year }}</a><span v-if="yIdx < model.years.length - 1">, </span>
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
    <section class="section light-background py-5 border-top">
      <div class="container">
        <!-- PHỤ TÙNG LIÊN QUAN -->
        <div class="mb-5" data-aos="fade-up" v-if="relatedParts && relatedParts.length > 0">
          <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
            <h4 class="fw-bold text-uppercase mb-0" style="color: var(--heading-color);">
              Những Sản Phẩm Khác
            </h4>
            <router-link to="/danh-sach-phu-tung" class="text-decoration-none text-danger fw-bold d-flex align-items-center gap-1 hover-underline" style="font-size: 0.95rem;">
              Xem tất cả <i class="bi bi-arrow-right"></i>
            </router-link>
          </div>
                    <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-3" v-for="part in relatedParts.slice(0, 8)" :key="part.id">
                            <div class="card h-100 premium-product-card group-hover position-relative">
                  <div class="card-img-wrapper overflow-hidden img-zoom-hover">
                    <img
                      :src="part.hinh_anh ? getImageUrl(part.hinh_anh) : 'https://images.unsplash.com/photo-1559523161-0fc0d8b38a7a?auto=format&fit=crop&q=80&w=400'"
                      class="card-img-top w-100 transition-transform"
                      style="aspect-ratio: 4/3; object-fit: cover;"
                      :alt="part.ten_phu_tung"
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
                      <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ part.nhom_phu_tung ? part.nhom_phu_tung.ten_nhom : part.ma_phu_tung }}</span>
                    </div>
                    <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                      <router-link :to="`/chi-tiet-phu-tung/${part.ma_phu_tung}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ part.ten_phu_tung }}</router-link>
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
                                <h6 class="mb-1 fw-bold text-dark">{{ g.ten_may || g.dong_co_model?.model_name || 'Máy không tên' }}</h6>
                                <small class="text-muted">S/N: {{ g.so_serial || 'N/A' }} | Model: {{ g.dong_co_model?.model_name }}</small>
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
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { createToaster } from '@meforma/vue-toaster';
import { useCartStore } from '../../../stores/cartStore';
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
      
      if (model.nam_bat_dau) {
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
          years: m.nam_bat_dau ? `${m.nam_bat_dau} - ${m.nam_ket_thuc || 'Nay'}` : 'Tất cả năm'
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
.cursor-pointer { cursor: pointer; }

/* Breadcrumb */
.breadcrumb a { color: var(--accent-color); text-decoration: none; font-weight: 500; transition: 0.2s; }
.breadcrumb a:hover { text-decoration: underline; }

/* Product Gallery */
.thumb-item { transition: 0.2s; opacity: 0.5; border: 2px solid transparent; }
.thumb-item:hover { opacity: 0.8; }
.active-thumb { opacity: 1 !important; border-color: var(--accent-color) !important; }

.btn-wishlist { color: var(--heading-color); border-color: #ddd; transition: 0.3s; }
.btn-wishlist:hover { color: var(--accent-color); border-color: var(--accent-color); background: rgba(248,34,73,0.05); }

/* Typography Helpers */
.letter-spacing-1 { letter-spacing: 1px; }
.hover-underline:hover { text-decoration: underline !important; }

/* Related Parts Sidebar */
.sticky-sidebar { position: sticky; top: 110px; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #aaa; }

.part-link { transition: 0.2s; }
.part-link:hover .model-code { color: var(--accent-color); }
.border-danger { border-color: var(--accent-color) !important; }

/* Tabs Customization */
.custom-tabs { border-bottom: 1px solid #ddd; }
.custom-tabs .nav-link { color: var(--heading-color); font-weight: 700; font-size: 0.95rem; border: none; border-bottom: 3px solid transparent; padding: 12px 24px; background: transparent; transition: 0.3s; }
.custom-tabs .nav-link:hover { color: var(--accent-color); }
.custom-tabs .nav-link.active { color: var(--accent-color); border-bottom: 3px solid var(--accent-color); background: transparent; }

/* Help Box */
.help-box { transition: 0.3s; }
.help-box:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
</style>



















