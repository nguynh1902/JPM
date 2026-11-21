<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Page & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <div>
        <h4 class="fw-bold mb-1 text-dark">
          Cấu Hình Hệ Thống & Thiết Lập Website
        </h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
          Quản lý thông tin chung, video trang chủ và thông tin liên hệ hiển thị tại Client.
        </p>
      </div>
      <div class="d-flex gap-2">
        <button 
          type="button" 
          class="btn btn-secondary text-nowrap"
          @click="resetToInitial"
          :disabled="isSubmitting"
        >
          Đặt lại
        </button>
        <button 
          type="button" 
          class="btn btn-primary shadow-sm text-nowrap"
          @click="saveSettings"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          <i v-else class="bx bx-save me-1"></i>
          {{ isSubmitting ? 'Đang Lưu...' : 'Lưu Thay Đổi' }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isFetching" class="card p-5 text-center mb-4">
      <div class="spinner-border text-primary mx-auto mb-3" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
      <p class="text-muted mb-0">Đang tải dữ liệu cấu hình hệ thống...</p>
    </div>

    <!-- 1 PAGE DUY NHẤT VỚI 3 PHẦN NỐI TIẾP NHAU -->
    <div v-else class="row g-4">
      
      <!-- BANNER TRANG CHỦ & VIDEO -->
      <div class="col-12">
        <div class="card mb-4 shadow-sm border">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
            <div>
              <h5 class="card-title mb-1 fw-bold text-dark">
                <i class="bx bx-movie-play text-primary me-2"></i>Banner Trang Chủ & Video
              </h5>
              <small class="text-muted">Cài đặt video nền YouTube và slogan tại trang chủ Client</small>
            </div>
            <span class="badge bg-label-primary px-3 py-2 rounded">
              <i class="bx bx-video me-1"></i> Video Nền 100vh
            </span>
          </div>

          <div class="card-body pt-4">
            <div class="row g-4">
              <!-- Cột nhập liệu -->
              <div class="col-lg-7">
                <!-- Đường dẫn YouTube -->
                <div class="mb-4">
                  <label for="homepage_youtube_url" class="form-label fw-bold text-dark">
                    Link Carousel Youtube <span class="text-danger">*</span>
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-youtube text-danger"></i></span>
                    <input 
                      type="text" 
                      id="homepage_youtube_url" 
                      class="form-control" 
                      placeholder="https://www.youtube.com/watch?v=5CVzOqvqI38" 
                      v-model="formData.homepage_youtube_url"
                      @input="updateYoutubePreview"
                    />
                  </div>
                  <div class="form-text text-muted mt-1">
                    <i class="bx bx-check-circle text-success me-1"></i>Hệ thống tự nhận Video ID và phát lặp nền tự động.
                  </div>
                </div>

                <!-- Tiêu đề lớn -->
                <div class="mb-4">
                  <label for="homepage_hero_title" class="form-label fw-bold text-dark">
                    Tiêu Đề Thương Hiệu
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-heading"></i></span>
                    <input 
                      type="text" 
                      id="homepage_hero_title" 
                      class="form-control" 
                      placeholder="JPM MARINE" 
                      v-model="formData.homepage_hero_title"
                    />
                  </div>
                  <div class="form-text text-muted">Dòng chữ màu đỏ nổi bật trên banner.</div>
                </div>

                <!-- Slogan phụ -->
                <div class="mb-2">
                  <label for="homepage_hero_subtitle" class="form-label fw-bold text-dark">
                    Slogan Phụ
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-text"></i></span>
                    <input 
                      type="text" 
                      id="homepage_hero_subtitle" 
                      class="form-control" 
                      placeholder="PHỤ TÙNG TÀU THUYỀN NHẬT BẢN" 
                      v-model="formData.homepage_hero_subtitle"
                    />
                  </div>
                  <div class="form-text text-muted">Dòng chữ in nghiêng màu trắng dưới tiêu đề.</div>
                </div>
              </div>

              <!-- Cột Live Preview -->
              <div class="col-lg-5">
                <div class="border rounded p-3 bg-light text-center h-100 d-flex flex-column justify-content-between">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                      <i class="bx bx-tv text-primary me-1"></i>Xem Trước Video
                    </span>
                    <span v-if="previewVideoId" class="badge bg-label-success">Video ID: {{ previewVideoId }}</span>
                    <span v-else class="badge bg-label-danger">Link không hợp lệ</span>
                  </div>

                  <div v-if="previewVideoId" class="ratio ratio-16x9 rounded overflow-hidden shadow-sm border bg-black my-auto">
                    <iframe 
                      :src="'https://www.youtube.com/embed/' + previewVideoId + '?autoplay=0&controls=1&rel=0'" 
                      title="YouTube video player" 
                      frameborder="0" 
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                      allowfullscreen
                    ></iframe>
                  </div>
                  <div v-else class="p-4 bg-white rounded border text-muted my-auto">
                    <i class="bx bx-video-off fs-1 mb-2 d-block text-secondary"></i>
                    Vui lòng nhập đường dẫn YouTube hợp lệ để xem trước.
                  </div>

                  <small class="text-muted mt-2 d-block">
                    <i class="bx bx-info-circle me-1"></i>Video phát nền tự động toàn màn hình tại trang chủ Client.
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BANNER CÁC TRANG CLIENT (CỬA HÀNG, TRA CỨU, TIN TỨC, HỖ TRỢ) -->
      <div class="col-12">
        <div class="card mb-4 shadow-sm border">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
            <div>
              <h5 class="card-title mb-1 fw-bold text-dark">
                <i class="bx bx-image-alt text-primary me-2"></i>Banner Các Trang Client (Cửa Hàng, Tra Cứu, Tin Tức, Hỗ Trợ)
              </h5>
              <small class="text-muted">Cài đặt ảnh nền full banner, tiêu đề và mô tả cho 4 trang chính của website</small>
            </div>
            <span class="badge bg-label-primary px-3 py-2 rounded">
              Chuẩn hiển thị gọn gàng (Chiều cao ~250px - 270px)
            </span>
          </div>

          <div class="card-body pt-4">
            <!-- Navigation Tabs chọn trang cần cấu hình banner -->
            <ul class="nav nav-pills gap-2 p-2 bg-light rounded-3 mb-4">
              <li class="nav-item">
                <button 
                  type="button" 
                  class="nav-link fw-semibold d-inline-flex align-items-center"
                  :class="{ active: activeBannerTab === 'store' }"
                  @click="activeBannerTab = 'store'"
                >
                  <i class="bx bx-store me-1"></i> Trang Cửa Hàng
                  <span v-if="formData.store_banner_image" class="badge bg-success ms-2 p-1" title="Đã có ảnh"><i class="bx bx-check"></i></span>
                </button>
              </li>
              <li class="nav-item">
                <button 
                  type="button" 
                  class="nav-link fw-semibold d-inline-flex align-items-center"
                  :class="{ active: activeBannerTab === 'lookup' }"
                  @click="activeBannerTab = 'lookup'"
                >
                  <i class="bx bx-search-alt me-1"></i> Trang Tra Cứu
                  <span v-if="formData.lookup_banner_image" class="badge bg-success ms-2 p-1" title="Đã có ảnh"><i class="bx bx-check"></i></span>
                </button>
              </li>
              <li class="nav-item">
                <button 
                  type="button" 
                  class="nav-link fw-semibold d-inline-flex align-items-center"
                  :class="{ active: activeBannerTab === 'news' }"
                  @click="activeBannerTab = 'news'"
                >
                  <i class="bx bx-news me-1"></i> Trang Tin Tức
                  <span v-if="formData.news_banner_image" class="badge bg-success ms-2 p-1" title="Đã có ảnh"><i class="bx bx-check"></i></span>
                </button>
              </li>
              <li class="nav-item">
                <button 
                  type="button" 
                  class="nav-link fw-semibold d-inline-flex align-items-center"
                  :class="{ active: activeBannerTab === 'support' }"
                  @click="activeBannerTab = 'support'"
                >
                  <i class="bx bx-support me-1"></i> Trang Hỗ Trợ
                  <span v-if="formData.support_banner_image" class="badge bg-success ms-2 p-1" title="Đã có ảnh"><i class="bx bx-check"></i></span>
                </button>
              </li>
            </ul>

            <div class="row g-4">
              <!-- Cột nhập liệu -->
              <div class="col-lg-6">
                <!-- Chọn file tải lên hoặc link ảnh -->
                <div class="mb-3">
                  <label class="form-label fw-bold text-dark d-flex justify-content-between">
                    <span>Ảnh Nền Banner: {{ currentBanner.name }}</span>
                    <span v-if="!formData[currentBanner.imageKey] && !currentBanner.defaultImg" class="badge bg-label-secondary">Chưa có ảnh (dùng nền mặc định)</span>
                  </label>
                  
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <input 
                      type="file" 
                      ref="bannerFileInput" 
                      class="d-none" 
                      accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif"
                      @change="handleBannerUpload($event, currentBanner.imageKey)"
                    />
                    <button 
                      type="button" 
                      class="btn btn-outline-primary d-inline-flex align-items-center shadow-none"
                      @click="$refs.bannerFileInput.click()"
                      :disabled="isUploadingBanner"
                    >
                      <span v-if="isUploadingBanner" class="spinner-border spinner-border-sm me-2"></span>
                      <i v-else class="bx bx-upload me-2"></i>
                      {{ isUploadingBanner ? 'Đang tải lên...' : 'Tải Ảnh Banner Mới' }}
                    </button>
                    <button 
                      v-if="formData[currentBanner.imageKey]" 
                      type="button" 
                      class="btn btn-outline-danger shadow-none"
                      @click="formData[currentBanner.imageKey] = ''"
                      title="Gỡ ảnh banner"
                    >
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>

                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-image"></i></span>
                    <input 
                      type="text" 
                      class="form-control" 
                      placeholder="VD: /storage/banners/banner.png hoặc /client/image/..." 
                      v-model="formData[currentBanner.imageKey]"
                    />
                  </div>
                  <div class="form-text text-muted mt-1">
                    Hỗ trợ mọi định dạng ảnh, tự động bung full banner và căn giữa chiều dọc. Nếu chưa có ảnh, hệ thống hiển thị nền dải mờ sang trọng.
                  </div>
                </div>

                <!-- Tiêu đề Banner -->
                <div class="mb-3">
                  <label class="form-label fw-bold text-dark">Tiêu Đề Banner {{ currentBanner.name }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-heading"></i></span>
                    <input 
                      type="text" 
                      class="form-control" 
                      :placeholder="currentBanner.defaultTitle" 
                      v-model="formData[currentBanner.titleKey]"
                    />
                  </div>
                  <div class="form-text text-muted">Dòng chữ lớn hiển thị tại khu vực làm trắng bên trái banner.</div>
                </div>

                <!-- Mô tả phụ -->
                <div class="mb-2">
                  <label class="form-label fw-bold text-dark">Mô Tả Phụ Dưới Tiêu Đề</label>
                  <textarea 
                    class="form-control" 
                    rows="2"
                    :placeholder="currentBanner.defaultSubtitle"
                    v-model="formData[currentBanner.subtitleKey]"
                  ></textarea>
                </div>
              </div>

              <!-- Cột Live Preview mô phỏng Client thật -->
              <div class="col-lg-6">
                <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                  <span><i class="bx bx-show text-primary me-1"></i>Xem Trước Hiệu Ứng: {{ currentBanner.name }}</span>
                  <small class="text-muted">Mô phỏng lớp trắng bên trái + ảnh nền</small>
                </label>
                
                <div class="admin-store-banner-preview rounded-3 overflow-hidden border position-relative shadow-sm" :style="adminBannerPreviewStyle">
                  <!-- Lớp phủ trắng bên trái -->
                  <div class="admin-banner-preview-overlay"></div>
                  
                  <!-- Watermark placeholder icon khi chưa có ảnh -->
                  <div v-if="!formData[currentBanner.imageKey] && !currentBanner.defaultImg" class="position-absolute" style="right: 8%; top: 50%; transform: translateY(-50%); font-size: 80px; color: #0A1128; opacity: 0.06; pointer-events: none; z-index: 1;">
                    <i :class="'bx ' + currentBanner.placeholderIcon"></i>
                  </div>

                  <!-- Nội dung chữ bên trái -->
                  <div class="position-relative z-2 p-3 p-md-4" style="max-width: 75%;">
                    <div class="d-inline-flex align-items-center gap-1 mb-1" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #F82249;">
                      <span style="width: 5px; height: 5px; border-radius: 50%; background: #F82249; display: inline-block;"></span>
                      {{ currentBanner.badge }}
                    </div>
                    <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.15rem; line-height: 1.2;">
                      {{ formData[currentBanner.titleKey] || currentBanner.defaultTitle }}
                    </h5>
                    <p class="text-secondary mb-0" style="font-size: 0.75rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                      {{ formData[currentBanner.subtitleKey] || currentBanner.defaultSubtitle }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- THÔNG TIN DOANH NGHIỆP & LIÊN HỆ -->
      <div class="col-12">
        <div class="card mb-4 shadow-sm border">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
            <div>
              <h5 class="card-title mb-1 fw-bold text-dark">
                <i class="bx bx-building-house text-primary me-2"></i>Thông Tin Doanh Nghiệp & Liên Hệ
              </h5>
              <small class="text-muted">Thông tin công ty, hotline, email và giờ mở cửa trên toàn website Client</small>
            </div>
            <span class="badge bg-label-primary px-3 py-2 rounded">
              <i class="bx bx-info-circle me-1"></i> Thông Tin Chung
            </span>
          </div>

          <div class="card-body pt-4">
            <div class="row g-4">
              <!-- Tên Doanh Nghiệp -->
              <div class="col-md-6">
                <label for="company_name" class="form-label fw-bold text-dark">Tên Doanh Nghiệp</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-building"></i></span>
                  <input 
                    type="text" 
                    id="company_name" 
                    class="form-control" 
                    placeholder="JPM Marine" 
                    v-model="formData.company_name"
                  />
                </div>
                <div class="form-text text-muted">Hiển thị tại Header, Footer và các thông báo.</div>
              </div>

              <!-- Hotline -->
              <div class="col-md-6">
                <label for="company_hotline" class="form-label fw-bold text-dark">Hotline CSKH</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-phone-call text-success"></i></span>
                  <input 
                    type="text" 
                    id="company_hotline" 
                    class="form-control" 
                    placeholder="0909 123 456" 
                    v-model="formData.company_hotline"
                  />
                </div>
                <div class="form-text text-muted">Tự động gắn link gọi trực tiếp (tel:).</div>
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <label for="company_email" class="form-label fw-bold text-dark">Email Liên Hệ</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-envelope text-info"></i></span>
                  <input 
                    type="email" 
                    id="company_email" 
                    class="form-control" 
                    placeholder="contact@jpm-marine.vn" 
                    v-model="formData.company_email"
                  />
                </div>
                <div class="form-text text-muted">Tự động gắn link gửi thư (mailto:).</div>
              </div>

              <!-- Giờ làm việc -->
              <div class="col-md-6">
                <label for="business_hours" class="form-label fw-bold text-dark">Giờ Mở Cửa</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-time-five text-warning"></i></span>
                  <input 
                    type="text" 
                    id="business_hours" 
                    class="form-control" 
                    placeholder="Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)" 
                    v-model="formData.business_hours"
                  />
                </div>
                <div class="form-text text-muted">Hiển thị tại Chân trang (Footer) và Trang Hỗ Trợ.</div>
              </div>

              <!-- Địa chỉ -->
              <div class="col-12">
                <label for="company_address" class="form-label fw-bold text-dark">Địa Chỉ Doanh Nghiệp</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-map-pin text-danger"></i></span>
                  <input 
                    type="text" 
                    id="company_address" 
                    class="form-control" 
                    placeholder="123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh" 
                    v-model="formData.company_address"
                  />
                </div>
                <div class="form-text text-muted">Địa chỉ đầy đủ hiển thị ở cột liên hệ Footer.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- MẠNG XÃ HỘI -->
      <div class="col-12">
        <div class="card mb-4 shadow-sm border">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
            <div>
              <h5 class="card-title mb-1 fw-bold text-dark">
                <i class="bx bx-share-alt text-primary me-2"></i>Mạng Xã Hội
              </h5>
              <small class="text-muted">Liên kết các kênh mạng xã hội chính thức của doanh nghiệp</small>
            </div>
            <span class="badge bg-label-primary px-3 py-2 rounded">
              <i class="bx bx-globe me-1"></i> Kênh Truyền Thông
            </span>
          </div>

          <div class="card-body pt-4">
            <div class="row g-4">
              <!-- Facebook -->
              <div class="col-md-6">
                <label for="social_facebook" class="form-label fw-bold text-dark">Facebook Fanpage</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bxl-facebook text-primary"></i></span>
                  <input 
                    type="url" 
                    id="social_facebook" 
                    class="form-control" 
                    placeholder="https://facebook.com/..." 
                    v-model="formData.social_facebook"
                  />
                </div>
              </div>

              <!-- YouTube Channel -->
              <div class="col-md-6">
                <label for="social_youtube" class="form-label fw-bold text-dark">Kênh YouTube</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bxl-youtube text-danger"></i></span>
                  <input 
                    type="url" 
                    id="social_youtube" 
                    class="form-control" 
                    placeholder="https://youtube.com/@..." 
                    v-model="formData.social_youtube"
                  />
                </div>
              </div>

              <!-- Zalo -->
              <div class="col-md-6">
                <label for="social_zalo" class="form-label fw-bold text-dark">Zalo</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-message-rounded-dots text-info"></i></span>
                  <input 
                    type="url" 
                    id="social_zalo" 
                    class="form-control" 
                    placeholder="https://zalo.me/..." 
                    v-model="formData.social_zalo"
                  />
                </div>
              </div>

              <!-- Instagram -->
              <div class="col-md-6">
                <label for="social_instagram" class="form-label fw-bold text-dark">Instagram</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bxl-instagram text-danger"></i></span>
                  <input 
                    type="url" 
                    id="social_instagram" 
                    class="form-control" 
                    placeholder="https://instagram.com/..." 
                    v-model="formData.social_instagram"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TÀI KHOẢN NGÂN HÀNG & MÃ VIETQR NHẬN CỌC -->
      <div class="col-12" style="position: relative; z-index: 5;">
        <div class="card mb-4 shadow-sm border" style="overflow: visible;">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
            <div>
              <h5 class="card-title mb-1 fw-bold text-dark">
                <i class="bx bx-qr-scan text-primary me-2"></i>Tài Khoản Ngân Hàng & Mã VietQR Nhận Cọc
              </h5>
              <small class="text-muted">Cấu hình thông tin tài khoản ngân hàng để tự động tạo mã QR chuyển khoản cọc Napas 24/7 gửi cho khách hàng</small>
            </div>
            <span class="badge bg-label-success px-3 py-2 rounded">
              <i class="bx bx-check-shield me-1"></i> Napas 24/7 Miễn Phí
            </span>
          </div>

          <div class="card-body pt-4">
            <div class="row g-4">
              <!-- Cột nhập liệu thông tin ngân hàng -->
              <div class="col-lg-7">
                <!-- Chọn Ngân Hàng -->
                <div class="mb-3">
                  <label class="form-label fw-bold text-dark">
                    Ngân Hàng Thụ Hưởng <span class="text-danger">*</span>
                  </label>
                  <v-select 
                    v-model="formData.vietqr_bank_id"
                    :options="bankOptions"
                    :searchable="true"
                    :can-clear="false"
                    placeholder="Tìm hoặc chọn ngân hàng..."
                  />
                  <small class="text-muted mt-1 d-block">Chuẩn Napas 24/7 liên ngân hàng.</small>
                </div>

                <!-- Số Tài Khoản -->
                <div class="mb-3">
                  <label for="vietqr_account_no" class="form-label fw-bold text-dark">
                    Số Tài Khoản Nhận Tiền <span class="text-danger">*</span>
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-credit-card text-success"></i></span>
                    <input 
                      type="text" 
                      id="vietqr_account_no" 
                      class="form-control" 
                      placeholder="VD: 108876543210" 
                      v-model="formData.vietqr_account_no"
                    />
                  </div>
                  <small class="text-muted mt-1 d-block">Số tài khoản nhận tiền cọc.</small>
                </div>

                <!-- Tên Chủ Tài Khoản -->
                <div class="mb-3">
                  <label for="vietqr_account_name" class="form-label fw-bold text-dark">
                    Tên Chủ Tài Khoản (In hoa không dấu) <span class="text-danger">*</span>
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-user text-info"></i></span>
                    <input 
                      type="text" 
                      id="vietqr_account_name" 
                      class="form-control text-uppercase" 
                      placeholder="VD: CONG TY PHU TUNG MAY THUY JPM" 
                      v-model="formData.vietqr_account_name"
                    />
                  </div>
                  <small class="text-muted mt-1 d-block">In hoa không dấu theo ngân hàng.</small>
                </div>

                <!-- Tiền Tố Nội Dung Chuyển Khoản -->
                <div class="mb-3">
                  <label for="vietqr_prefix" class="form-label fw-bold text-dark">
                    Tiền Tố Nội Dung Chuyển Khoản
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-purchase-tag-alt text-warning"></i></span>
                    <input 
                      type="text" 
                      id="vietqr_prefix" 
                      class="form-control text-uppercase" 
                      placeholder="JPM hoặc COC" 
                      v-model="formData.vietqr_prefix"
                    />
                  </div>
                  <small class="text-muted mt-1 d-block">
                    Ghép tự động dạng: <strong>[Tiền tố] [Mã đơn]</strong>
                  </small>
                </div>

                <!-- Kiểu hiển thị mã QR (Template) -->
                <div class="mb-3">
                  <label class="form-label fw-bold text-dark">
                    Mẫu Hiển Thị Khung QR
                  </label>
                  <v-select 
                    v-model="formData.vietqr_template"
                    :options="templateOptions"
                    :can-clear="false"
                    placeholder="Chọn kiểu dáng khung QR..."
                  />
                  <small class="text-muted mt-1 d-block">Kiểu dáng hiển thị cho khách quét.</small>
                </div>
              </div>

              <!-- Cột Live Preview Quét Thử Mã QR -->
              <div class="col-lg-5">
                <div class="border rounded p-3 bg-light text-center h-100 d-flex flex-column align-items-center">
                  <!-- Tiêu đề cột xem trước -->
                  <div class="d-flex justify-content-between align-items-center w-100 mb-3 pb-2 border-bottom">
                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">
                      <i class="bx bx-qr-scan text-primary me-1"></i>Xem Trước Mã VietQR Thực Tế
                    </span>
                    <span class="badge bg-label-primary">{{ formData.vietqr_bank_id || 'ICB' }}</span>
                  </div>

                  <!-- Khung hiển thị ảnh QR tự sinh (To rõ, đặt sát tiêu đề, cân đối trên dưới) -->
                  <div class="bg-white p-2 rounded shadow-sm border mb-3 d-inline-block">
                    <img 
                      :src="previewQrUrl" 
                      alt="Preview VietQR Napas" 
                      class="img-fluid rounded"
                      style="max-height: 285px; width: 100%; max-width: 255px; object-fit: contain;"
                    />
                  </div>

                  <!-- Bảng thông tin tóm tắt -->
                  <div class="text-start small bg-white p-3 rounded border w-100 mb-2">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                      <span class="text-muted">Ngân hàng:</span>
                      <span class="fw-bold text-dark">{{ formData.vietqr_bank_name || 'VietinBank' }} ({{ formData.vietqr_bank_id || 'ICB' }})</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                      <span class="text-muted">Số tài khoản:</span>
                      <span class="fw-bold text-primary">{{ formData.vietqr_account_no || 'Chưa nhập' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                      <span class="text-muted">Chủ tài khoản:</span>
                      <span class="fw-bold text-dark">{{ (formData.vietqr_account_name || 'Chưa nhập').toUpperCase() }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                      <span class="text-muted">Nội dung mẫu:</span>
                      <span class="fw-bold text-danger">{{ (formData.vietqr_prefix || 'COC').trim().toUpperCase() }} YCBG-DEMO</span>
                    </div>
                  </div>

                  <!-- Ghi chú quét thử -->
                  <small class="text-muted mt-auto pt-2 d-block text-center" style="font-size: 0.8rem;">
                    <i class="bx bx-mobile-alt me-1 text-primary"></i>Quét thử bằng App ngân hàng để kiểm tra trước khi Lưu.
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- HIỆU NĂNG & REDIS CACHE -->
      <div class="col-12" style="position: relative; z-index: 1;">
        <div class="card mb-4 shadow-sm border">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-3">
            <div>
              <h5 class="card-title mb-1 fw-bold text-dark">
                <i class="bx bx-server text-primary me-2"></i>Hiệu Năng & Bộ Nhớ Đệm (Redis Cache)
              </h5>
              <small class="text-muted">Tối ưu tốc độ tải danh mục Máy thủy, Động cơ, Sơ đồ phụ tùng và giảm tải máy chủ</small>
            </div>
            <span class="badge bg-label-success px-3 py-2 rounded">
              <i class="bx bx-check-shield me-1"></i> Redis Active (Failover Protected)
            </span>
          </div>

          <div class="card-body pt-4">
            <!-- Mô tả kiến trúc cache -->
            <div class="mb-3">
              <p class="text-dark fw-medium mb-1">
                Kiến trúc bộ nhớ đệm: <span class="text-primary fw-semibold">Failover Cache (Redis &rarr; File Driver &rarr; Memory Array)</span>
              </p>
              <p class="text-muted small mb-0" style="line-height: 1.6;">
                Hệ thống tự động lưu trữ sẵn toàn bộ danh mục Thương hiệu, Loại động cơ, Model và Nhóm phụ tùng vào bộ nhớ đệm tốc độ cao, giúp giảm tới <strong>95%</strong> áp lực truy vấn lên cơ sở dữ liệu. Khi Quản trị viên thay đổi hoặc cập nhật dữ liệu phụ tùng, hệ thống sẽ tự động làm mới bộ nhớ đệm liên quan.
              </p>
            </div>

            <!-- Thống kê mục đã cache (khi đã làm ấm) -->
            <div v-if="cacheStats" class="mb-4 p-3 bg-light rounded border">
              <div class="d-flex align-items-center mb-2">
                <i class="bx bx-check-circle text-success fs-5 me-2"></i>
                <span class="fw-semibold text-dark small">Dữ liệu đã nạp sẵn vào bộ nhớ đệm:</span>
              </div>
              <div class="row g-2 text-dark small">
                <div class="col-6 col-sm-3">
                  <div class="p-2 border rounded bg-white text-center">
                    <div class="text-muted small">Thương hiệu</div>
                    <div class="fw-bold fs-6 text-primary">{{ cacheStats.thuong_hieu ?? cacheStats.cached_brands ?? 0 }}</div>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <div class="p-2 border rounded bg-white text-center">
                    <div class="text-muted small">Loại động cơ</div>
                    <div class="fw-bold fs-6 text-primary">{{ cacheStats.loai_dong_co ?? cacheStats.cached_engine_types ?? 0 }}</div>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <div class="p-2 border rounded bg-white text-center">
                    <div class="text-muted small">Model máy</div>
                    <div class="fw-bold fs-6 text-primary">{{ cacheStats.dong_co_model ?? cacheStats.cached_models ?? 0 }}</div>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <div class="p-2 border rounded bg-white text-center">
                    <div class="text-muted small">Nhóm phụ tùng</div>
                    <div class="fw-bold fs-6 text-primary">{{ cacheStats.nhom_phu_tung ?? cacheStats.cached_part_groups ?? 0 }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Thanh công cụ thao tác chuẩn Sneat 1.0.0 (Tách hàng riêng, không đè chữ) -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
              <div class="text-muted small">
                <i class="bx bx-info-circle me-1 text-info"></i>
                Khuyến nghị bấm <strong>Làm Ấm Cache</strong> sau khi xóa trắng để người dùng tra cứu mượt mà nhất.
              </div>
              <div class="d-flex flex-wrap gap-2">
                <button 
                  type="button" 
                  class="btn btn-outline-danger px-4 shadow-sm text-nowrap d-inline-flex align-items-center justify-content-center"
                  style="min-width: 175px;"
                  @click="handleClearCache(false)"
                  :disabled="isClearingCache"
                >
                  <i class="bx bx-trash me-2"></i> Xóa Trắng Cache
                </button>
                <button 
                  type="button" 
                  class="btn btn-primary px-4 shadow-sm text-nowrap d-inline-flex align-items-center justify-content-center"
                  style="min-width: 175px;"
                  @click="handleClearCache(true)"
                  :disabled="isClearingCache"
                >
                  <span v-if="isClearingCache" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i v-else class="bx bx-bolt-circle me-2"></i>
                  {{ isClearingCache ? 'Đang làm ấm...' : 'Làm Ấm Cache' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';
import { compressImage } from '../../../utils/imageOptimizer';

export default {
  name: "CauHinhHeThong",
  setup() {
    const queryClient = useQueryClient();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    const isSubmitting = ref(false);

    // Danh sách các ngân hàng phổ biến tại Việt Nam theo chuẩn Napas247 (Chuẩn v-select options)
    const bankOptions = [
      { value: 'ICB', shortName: 'VietinBank', label: 'VietinBank - Ngân hàng TMCP Công thương Việt Nam' },
      { value: 'VCB', shortName: 'Vietcombank', label: 'Vietcombank - Ngân hàng TMCP Ngoại thương Việt Nam' },
      { value: 'MB', shortName: 'MBBank', label: 'MBBank - Ngân hàng TMCP Quân Đội' },
      { value: 'TCB', shortName: 'Techcombank', label: 'Techcombank - Ngân hàng TMCP Kỹ Thương' },
      { value: 'BIDV', shortName: 'BIDV', label: 'BIDV - Ngân hàng Đầu tư và Phát triển VN' },
      { value: 'VBA', shortName: 'Agribank', label: 'Agribank - Ngân hàng Nông nghiệp & PT Nông thôn' },
      { value: 'ACB', shortName: 'ACB', label: 'ACB - Ngân hàng TMCP Á Châu' },
      { value: 'VPB', shortName: 'VPBank', label: 'VPBank - Ngân hàng TMCP Việt Nam Thịnh Vượng' },
      { value: 'STB', shortName: 'Sacombank', label: 'Sacombank - Ngân hàng Sài Gòn Thương Tín' },
      { value: 'TPB', shortName: 'TPBank', label: 'TPBank - Ngân hàng TMCP Tiên Phong' },
      { value: 'VIB', shortName: 'VIB', label: 'VIB - Ngân hàng Quốc tế Việt Nam' },
      { value: 'SHB', shortName: 'SHB', label: 'SHB - Ngân hàng TMCP Sài Gòn - Hà Nội' },
      { value: 'HDB', shortName: 'HDBank', label: 'HDBank - Ngân hàng TMCP Phát triển TP.HCM' },
      { value: 'MSB', shortName: 'MSB', label: 'MSB - Ngân hàng TMCP Hàng Hải Việt Nam' },
      { value: 'OCB', shortName: 'OCB', label: 'OCB - Ngân hàng TMCP Phương Đông' },
      { value: 'LPB', shortName: 'LPBank', label: 'LPBank - Ngân hàng TMCP Lộc Phát Việt Nam' },
      { value: 'SEAB', shortName: 'SeABank', label: 'SeABank - Ngân hàng TMCP Đông Nam Á' }
    ];

    const templateOptions = [
      { value: 'compact2', label: 'Chuẩn đầy đủ: Khung viền + Logo Napas + Logo Ngân Hàng (Khuyên dùng)' },
      { value: 'compact', label: 'Gọn nhẹ: Kèm logo ngân hàng thu nhỏ' },
      { value: 'qr_only', label: 'Đơn giản: Chỉ hiển thị mã QR vuông' }
    ];

    // Initial default state
    const defaultData = {
      homepage_youtube_url: 'https://www.youtube.com/watch?v=5CVzOqvqI38',
      homepage_hero_title: 'JPM MARINE',
      homepage_hero_subtitle: 'PHỤ TÙNG TÀU THUYỀN NHẬT BẢN',
      store_banner_image: '/client/image/shop_banner.png',
      store_banner_title: 'Cửa Hàng Phụ Tùng',
      store_banner_subtitle: 'Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.',
      lookup_banner_image: '',
      lookup_banner_title: 'Tra Cứu Phụ Tùng',
      lookup_banner_subtitle: 'Lựa chọn chính xác phụ tùng thay thế và sơ đồ rã máy cho động cơ thủy của bạn.',
      news_banner_image: '/client/image/news_banner.png',
      news_banner_title: 'Tin Tức & Cẩm Nang',
      news_banner_subtitle: 'Tổng hợp những kiến thức chuyên sâu về vận hành tàu thuyền, cẩm nang bảo dưỡng động cơ thủy và xu hướng công nghệ hàng hải.',
      support_banner_image: '',
      support_banner_title: 'Trung Tâm Hỗ Trợ & Kỹ Thuật',
      support_banner_subtitle: 'Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM và hỗ trợ 24/7.',
      company_name: 'JPM Marine',
      company_address: '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh, Việt Nam',
      company_hotline: '0909 123 456',
      company_email: 'contact@jpm-marine.vn',
      business_hours: 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)',
      social_facebook: 'https://facebook.com/jpmmarine',
      social_youtube: 'https://youtube.com/@jpmmarine',
      social_zalo: 'https://zalo.me/0909123456',
      social_instagram: 'https://instagram.com/jpmmarine',
      vietqr_bank_id: 'ICB',
      vietqr_bank_name: 'VietinBank',
      vietqr_account_no: '108876543210',
      vietqr_account_name: 'CONG TY PHU TUNG MAY THUY JPM',
      vietqr_template: 'compact2',
      vietqr_prefix: 'COC'
    };

    const formData = reactive({ ...defaultData });
    const initialSnapshot = ref({});

    // Tự động đồng bộ tên ngân hàng khi đổi mã ngân hàng
    watch(() => formData.vietqr_bank_id, (newBankId) => {
      const selected = bankOptions.find(b => b.value === newBankId);
      if (selected) {
        formData.vietqr_bank_name = selected.shortName;
      }
    });

    // Live preview URL mã VietQR
    const previewQrUrl = computed(() => {
      const bankId = formData.vietqr_bank_id || 'ICB';
      const accountNo = (formData.vietqr_account_no || '').trim() || '108876543210';
      const template = formData.vietqr_template || 'compact2';
      const accountName = encodeURIComponent((formData.vietqr_account_name || 'CONG TY PHU TUNG MAY THUY JPM').trim().toUpperCase());
      const prefix = (formData.vietqr_prefix || 'COC').trim().toUpperCase();
      const addInfo = encodeURIComponent(`${prefix} YCBG-DEMO`);
      return `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=1000000&addInfo=${addInfo}&accountName=${accountName}`;
    });

    // Fetch Admin Settings
    const { data: serverConfigs, isFetching } = useQuery({
      queryKey: ['admin-site-settings'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/cau-hinh');
        return Array.isArray(res?.data) ? res.data : (res?.data?.data || []);
      }
    });

    // Trích xuất YouTube Video ID từ link
    const extractYoutubeId = (url) => {
      if (!url) return '';
      const pattern = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
      const match = url.match(pattern);
      if (match && match[1]) return match[1];
      if (url.trim().length === 11 && !url.includes('/')) return url.trim();
      return '';
    };

    const previewVideoId = ref('5CVzOqvqI38');

    const updateYoutubePreview = () => {
      previewVideoId.value = extractYoutubeId(formData.homepage_youtube_url);
    };

    // Khi server trả về dữ liệu -> đổ vào form
    watch(serverConfigs, (newConfigs) => {
      if (Array.isArray(newConfigs) && newConfigs.length > 0) {
        newConfigs.forEach(item => {
          if (formData.hasOwnProperty(item.khoa_cau_hinh)) {
            formData[item.khoa_cau_hinh] = item.gia_tri || '';
          }
        });
        initialSnapshot.value = JSON.parse(JSON.stringify(formData));
        updateYoutubePreview();
      }
    }, { immediate: true });

    // Save Mutation
    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return await axiosClient.post('/admin/cau-hinh', { settings: payload });
      },
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['admin-site-settings'] });
        queryClient.invalidateQueries({ queryKey: ['public-site-settings'] });
        initialSnapshot.value = JSON.parse(JSON.stringify(formData));
        toaster.success('Đã lưu cấu hình hệ thống và cập nhật giao diện Client.');
      },
      onError: (err) => {
        toaster.error(err.response?.data?.message || err.message || 'Không thể lưu cấu hình.');
      }
    });

    const saveSettings = async () => {
      isSubmitting.value = true;
      try {
        await saveMutation.mutateAsync({ ...formData });
      } finally {
        isSubmitting.value = false;
      }
    };

    const resetToInitial = () => {
      if (Object.keys(initialSnapshot.value).length > 0) {
        Object.assign(formData, JSON.parse(JSON.stringify(initialSnapshot.value)));
        updateYoutubePreview();
      }
    };

    const isClearingCache = ref(false);
    const cacheStats = ref(null);

    const handleClearCache = async (warmup = true) => {
      isClearingCache.value = true;
      try {
        const res = await axiosClient.post('/admin/he-thong/clear-cache', { warmup });
        const payload = res?.success !== undefined ? res : res?.data;
        if (payload && payload.success) {
          cacheStats.value = payload.stats || null;
          toaster.success(payload.message || 'Đã xử lý Cache thành công!');
        }
      } catch (err) {
        toaster.error('Không thể xóa bộ nhớ đệm: ' + (err.response?.data?.message || err.message));
      } finally {
        isClearingCache.value = false;
      }
    };

    const isUploadingBanner = ref(false);
    const bannerFileInput = ref(null);
    const activeBannerTab = ref('store');

    const bannerConfigs = {
      store: {
        name: 'Trang Cửa Hàng',
        badge: 'JPM MARINE STORE',
        imageKey: 'store_banner_image',
        titleKey: 'store_banner_title',
        subtitleKey: 'store_banner_subtitle',
        defaultImg: '/client/image/shop_banner.png',
        defaultTitle: 'Cửa Hàng Phụ Tùng',
        defaultSubtitle: 'Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.',
        placeholderIcon: 'bx-store'
      },
      lookup: {
        name: 'Trang Tra Cứu',
        badge: 'JPM MARINE DIAGNOSTICS',
        imageKey: 'lookup_banner_image',
        titleKey: 'lookup_banner_title',
        subtitleKey: 'lookup_banner_subtitle',
        defaultImg: '',
        defaultTitle: 'Tra Cứu Phụ Tùng',
        defaultSubtitle: 'Lựa chọn chính xác phụ tùng thay thế và sơ đồ rã máy cho động cơ thủy của bạn.',
        placeholderIcon: 'bx-compass'
      },
      news: {
        name: 'Trang Tin Tức',
        badge: 'JPM MARINE EDITORIAL',
        imageKey: 'news_banner_image',
        titleKey: 'news_banner_title',
        subtitleKey: 'news_banner_subtitle',
        defaultImg: '/client/image/news_banner.png',
        defaultTitle: 'Tin Tức & Cẩm Nang',
        defaultSubtitle: 'Tổng hợp những kiến thức chuyên sâu về vận hành tàu thuyền, cẩm nang bảo dưỡng động cơ thủy và xu hướng công nghệ hàng hải.',
        placeholderIcon: 'bx-news'
      },
      support: {
        name: 'Trang Hỗ Trợ',
        badge: 'JPM MARINE SUPPORT',
        imageKey: 'support_banner_image',
        titleKey: 'support_banner_title',
        subtitleKey: 'support_banner_subtitle',
        defaultImg: '',
        defaultTitle: 'Trung Tâm Hỗ Trợ & Kỹ Thuật',
        defaultSubtitle: 'Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM và hỗ trợ 24/7.',
        placeholderIcon: 'bx-headphone'
      }
    };

    const currentBanner = computed(() => bannerConfigs[activeBannerTab.value]);

    const handleBannerUpload = async (event, key = 'store_banner_image') => {
      const file = event.target.files?.[0];
      if (!file) return;

      isUploadingBanner.value = true;
      try {
        const compressed = await compressImage(file, { maxWidth: 1920, maxHeight: 600, quality: 0.88 });
        const uploadForm = new FormData();
        uploadForm.append('file', compressed);

        const res = await axiosClient.post('/admin/cau-hinh/upload-banner', uploadForm, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        const payload = res?.data || res;
        if (payload?.success && payload.url) {
          formData[key] = payload.url;
          toaster.success('Đã tải ảnh banner lên thành công!');
        }
      } catch (err) {
        toaster.error(err.response?.data?.message || 'Lỗi khi tải ảnh banner lên');
      } finally {
        isUploadingBanner.value = false;
        event.target.value = '';
      }
    };

    const adminBannerPreviewStyle = computed(() => {
      const cfg = currentBanner.value;
      const raw = formData[cfg.imageKey] || cfg.defaultImg;
      if (raw) {
        const imgUrl = raw.startsWith('http') || raw.startsWith('/client/') || raw.startsWith('/assets/')
          ? raw
          : (raw.startsWith('/') ? raw : '/' + raw);
        return {
          backgroundImage: `url("${imgUrl}")`,
          backgroundSize: 'cover',
          backgroundPosition: 'center center',
          backgroundRepeat: 'no-repeat',
          height: '180px',
          maxHeight: '190px'
        };
      }
      return {
        background: 'linear-gradient(135deg, #ffffff 0%, #f8fafc 40%, #eef4f9 75%, #e2eaf4 100%)',
        height: '180px',
        maxHeight: '190px'
      };
    });

    onMounted(() => {
      updateYoutubePreview();
    });

    return {
      formData,
      isFetching,
      isSubmitting,
      previewVideoId,
      updateYoutubePreview,
      saveSettings,
      resetToInitial,
      isClearingCache,
      cacheStats,
      handleClearCache,
      bankOptions,
      templateOptions,
      previewQrUrl,
      isUploadingBanner,
      bannerFileInput,
      activeBannerTab,
      currentBanner,
      handleBannerUpload,
      adminBannerPreviewStyle
    };
  }
};
</script>

<style scoped>
.admin-store-banner-preview {
  position: relative;
  display: flex;
  align-items: center;
  background-color: #ffffff;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
}

.admin-banner-preview-overlay {
  position: absolute;
  top: 0;
  bottom: 0;
  left: -2px;
  right: 0;
  z-index: 1;
  background: linear-gradient(
    90deg,
    #ffffff 0%,
    #ffffff 24%,
    rgba(255, 255, 255, 0.95) 34%,
    rgba(255, 255, 255, 0.65) 44%,
    rgba(255, 255, 255, 0.18) 54%,
    transparent 62%,
    transparent 100%
  );
  pointer-events: none;
}
</style>
