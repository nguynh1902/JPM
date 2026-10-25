<template>
  <div class="jpm-homepage">
    <!-- ========================================================
         HERO SECTION: Video nền du thuyền & Giới thiệu nhanh
         ======================================================== -->
    <section id="hero" class="hero section dark-background">
      <!-- Video Background (YouTube) -->
      <div class="hero-video-wrap">
        <iframe 
          :src="youtubeEmbedUrl" 
          frameborder="0" 
          allow="autoplay; encrypted-media" 
          class="hero-video-iframe"
        ></iframe>
        <!-- Gradient bên trái -->
        <div class="hero-video-gradient"></div>
      </div>

      <div class="container flex-grow-1 d-flex align-items-center position-relative z-3">
        <div class="row w-100">
          <div class="col-lg-8 text-start">
            <h1 data-aos="fade-up" data-aos-delay="100" class="hero-main-title">
              <span class="hero-title-accent">{{ settings.homepage_hero_title || 'JPM MARINE' }}</span><br />
              <span class="hero-title-white">{{ settings.homepage_hero_subtitle || 'PHỤ TÙNG CHÍNH HÃNG' }}</span>
            </h1>

            <p data-aos="fade-up" data-aos-delay="150" class="hero-subtitle">
              Đồng hành cùng mọi chuyến ra khơi. Cung cấp động cơ, hệ thống lái & thiết bị tàu thủy đạt chuẩn quốc tế.
            </p>

            <div data-aos="fade-up" data-aos-delay="200">
              <router-link to="/tra-cuu" class="btn btn-hero-cta text-white d-inline-flex align-items-center justify-content-center">
                Tra Cứu Phụ Tùng
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- About Info Banner -->
      <div class="about-info mt-auto position-relative">
        <div class="container position-relative" data-aos="fade-up">
          <div class="row">
            <div class="col-lg-6">
              <h2>Giới Thiệu Chung</h2>
              <p>
                JPM Marine tự hào là nhà phân phối thiết bị và phụ kiện hàng hải uy tín. Chúng tôi cam kết mang đến các sản phẩm chính hãng, bền bỉ và tối ưu hiệu suất từ những thương hiệu hàng đầu thế giới, đảm bảo sự an toàn tuyệt đối cho phương tiện của bạn.
              </p>
            </div>
            <div class="col-lg-3">
              <h3>Trụ sở chính</h3>
              <p>123 Đường Bạch Đằng,<br />Quận Bình Thạnh, TP.HCM</p>
            </div>
            <div class="col-lg-3">
              <h3>Thời gian phục vụ</h3>
              <p>Thứ Hai - Thứ Bảy<br />08:00 Sáng - 17:30 Chiều</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 1: TRA CỨU PHỤ TÙNG NHANH (Bộ lọc động cơ thông minh)
         ======================================================== -->
    <section id="search-parts" class="search-parts-section section position-relative z-3">
      <div class="container" data-aos="fade-up">
        <!-- Tiêu đề Section chuẩn TheEvent -->
        <div class="section-title" data-aos="fade-up">
          <h2>Tra Cứu Phụ Tùng Nhanh</h2>
          <p>Chọn thông số phương tiện hoặc nhập mã để tra cứu chính xác phụ tùng cần thiết</p>
        </div>

        <!-- Khung Tra Cứu Sang Trọng, Thống Nhất Phong Cách TheEvent -->
        <div class="filter-console-card position-relative" data-aos="fade-up" data-aos-delay="100">
          <!-- OVERLAY để click ra ngoài đóng dropdown -->
          <div class="dropdown-overlay" v-if="openDropdown" @click="openDropdown = null"></div>

          <!-- 1. Hàng 4 Dropdown Lọc Động Cơ -->
          <div class="row g-3 mb-4">
            <!-- Cột 1: Thương Hiệu -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="filter-label mb-2">
                <i class="bi bi-tag text-secondary me-1"></i> Thương hiệu
              </label>
              <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'brand' }">
                <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'brand', 'has-value': selectedBrand !== '' }" @click="toggleDropdown('brand')">
                  <span class="text-truncate">{{ brandLabel }}</span>
                  <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'brand' }"></i>
                </div>
                <ul class="custom-dropdown-list" v-show="openDropdown === 'brand'">
                  <li @click="selectOption('brand', '')" :class="{ active: selectedBrand === '' }">Tất cả thương hiệu</li>
                  <li v-for="b in brands" :key="b.id" @click="selectOption('brand', b.id)" :class="{ active: selectedBrand === b.id }">{{ b.name }}</li>
                </ul>
              </div>
            </div>

            <!-- Cột 2: Loại Động Cơ -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="filter-label mb-2">
                <i class="bi bi-gear text-secondary me-1"></i> Loại động cơ
              </label>
              <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'engineType' }">
                <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'engineType', 'has-value': selectedEngineType !== '' }" @click="toggleDropdown('engineType')">
                  <span class="text-truncate">{{ partLabel }}</span>
                  <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'engineType' }"></i>
                </div>
                <ul class="custom-dropdown-list" v-show="openDropdown === 'engineType'">
                  <li @click="selectOption('engineType', '')" :class="{ active: selectedEngineType === '' }">Tất cả loại động cơ</li>
                  <li v-for="p in engineTypes" :key="p.id" @click="selectOption('engineType', p.id)" :class="{ active: selectedEngineType === p.id }">{{ p.ten_loai_dong_co }}</li>
                </ul>
              </div>
            </div>

            <!-- Cột 3: Năm Sản Xuất -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="filter-label mb-2">
                <i class="bi bi-calendar3 text-secondary me-1"></i> Năm sản xuất
              </label>
              <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'year' }">
                <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'year', 'has-value': selectedYear !== '' }" @click="toggleDropdown('year')">
                  <span class="text-truncate">{{ yearLabel }}</span>
                  <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'year' }"></i>
                </div>
                <ul class="custom-dropdown-list" v-show="openDropdown === 'year'">
                  <li @click="selectOption('year', '')" :class="{ active: selectedYear === '' }">Tất cả các năm</li>
                  <li v-for="y in years" :key="y" @click="selectOption('year', y)" :class="{ active: selectedYear === y }">{{ y }}</li>
                </ul>
              </div>
            </div>

            <!-- Cột 4: Model Động Cơ -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="filter-label mb-2">
                <i class="bi bi-diagram-3 text-secondary me-1"></i> Model động cơ
              </label>
              <div class="custom-select-wrapper" :class="{ 'z-active': openDropdown === 'model' }">
                <div class="custom-select-box" :class="{ 'is-focused': openDropdown === 'model', 'has-value': selectedModel !== '' }" @click="toggleDropdown('model')">
                  <span class="text-truncate">{{ modelLabel }}</span>
                  <i class="bi bi-chevron-down select-arrow" :class="{ 'rotated': openDropdown === 'model' }"></i>
                </div>
                <ul class="custom-dropdown-list" v-show="openDropdown === 'model'">
                  <li @click="selectOption('model', '')" :class="{ active: selectedModel === '' }">Tất cả model</li>
                  <li v-for="m in engineModels" :key="m.id" @click="selectOption('model', m.id)" :class="{ active: selectedModel === m.id }">{{ m.ten_model }}</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- 2. Hàng Tìm Kiếm Theo Mã & Cụm Nút Thao Tác -->
          <div class="row g-3 align-items-end">
            <!-- Ô Tìm kiếm mã -->
            <div class="col-12 col-lg-8">
              <label class="filter-label mb-2">
                <i class="bi bi-upc-scan text-secondary me-1"></i> Tìm kiếm theo mã (Tùy chọn)
              </label>
              <div class="search-input-box">
                <i class="bi bi-search search-input-icon"></i>
                <input
                  type="text"
                  class="search-input-field"
                  placeholder="Nhập mã phụ tùng / model (VD: F250TUR, 688-45941-01...)"
                  v-model="searchKeyword"
                  @keyup.enter="searchParts"
                />
              </div>
            </div>

            <!-- Cụm Nút Thao Tác -->
            <div class="col-12 col-lg-4">
              <div class="d-flex gap-2">
                <button
                  type="button"
                  class="btn-reset-action"
                  @click="resetSearch"
                  title="Đặt lại các bộ lọc"
                >
                  <i class="bi bi-arrow-counterclockwise"></i>
                  <span>Đặt lại</span>
                </button>
                <button
                  type="button"
                  class="btn-search-action"
                  @click="searchParts"
                >
                  <i class="bi bi-search"></i>
                  <span>Tìm kiếm phụ tùng</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 3. Hàng Từ Khóa Gợi Ý Phổ Biến (Nhẹ nhàng, thanh lịch) -->
          <div class="popular-tags-row">
            <span class="popular-tags-label">
              <i class="bi bi-lightning-charge-fill text-danger me-1"></i>Phổ biến:
            </span>
            <div class="popular-tags-list">
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('F250TUR')">Yamaha F250TUR</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('300HP')">Mercury 300HP V8</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('Bottom Cowling')">Bottom Cowling</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('Accent Panel')">Accent Panel</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('máy phát')">Máy phát Alternator</a>
              <a href="javascript:void(0)" class="quick-pill-tag" @click="quickSearch('8M0142205')">8M0142205</a>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 2: PHỤ TÙNG THEO THƯƠNG HIỆU (Logo nhà sản xuất chính hãng)
         ======================================================== -->
    <section id="sponsors" class="sponsors section bg-white py-5">
      <div class="container section-title pb-3" data-aos="fade-up">
        <h2>Phụ Tùng Theo Thương Hiệu</h2>
        <p>Đa dạng các loại phụ tùng chính hãng từ những thương hiệu hàng đầu thế giới</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-0 clients-wrap">
          <!-- Skeleton loading -->
          <template v-if="isLoadingBrands">
            <div class="col-xl-3 col-md-4 client-logo" v-for="n in 8" :key="'skel-' + n">
              <div class="d-flex align-items-center justify-content-center w-100 h-100">
                <div class="skeleton-box skeleton-box-brand"></div>
              </div>
            </div>
          </template>

          <!-- Dynamic brands from API -->
          <template v-else-if="brands && brands.length > 0">
            <div class="col-xl-3 col-md-4 client-logo" v-for="brand in brands" :key="brand.id">
              <router-link :to="`/thuong-hieu/${brand.code ? brand.code.toLowerCase() : ''}`" class="d-flex align-items-center justify-content-center w-100 h-100">
                <img
                  :src="brand.logo_path ? (brand.logo_path.startsWith('http') ? brand.logo_path : brand.logo_path.startsWith('/client/') ? brand.logo_path : 'http://localhost:8000/storage/' + brand.logo_path) : ''"
                  :alt="brand.name"
                  class="img-fluid"
                  @error="e => e.target.style.display='none'"
                />
              </router-link>
            </div>
          </template>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 3: SẢN PHẨM BÁN CHẠY (Linh kiện & phụ tùng tiêu biểu)
         ======================================================== -->
    <section class="products-section section bg-light py-5">
      <div class="container section-title" data-aos="fade-up">
        <h2>Sản Phẩm Bán Chạy</h2>
        <p>Những linh kiện phụ tùng được khách hàng tin dùng nhiều nhất tại JPM Marine</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
          <!-- Skeleton khi đang load (8 sản phẩm = 2 hàng) -->
          <template v-if="!hotProducts">
            <div class="col" v-for="n in 8" :key="'sk-' + n">
              <div class="card h-100">
                <div class="card-img-wrapper skeleton-box card-img-product"></div>
                <div class="card-body p-3 d-flex flex-column">
                  <!-- Skeleton Dòng 1: Danh mục -->
                  <div class="skeleton-line mb-2" style="width: 45%; height: 12px;"></div>
                  <!-- Skeleton Dòng 2: Tên sản phẩm (2 dòng) -->
                  <div class="skeleton-line mb-1" style="width: 100%; height: 15px;"></div>
                  <div class="skeleton-line mb-2" style="width: 75%; height: 15px;"></div>
                  <!-- Skeleton Dòng 3: Mã sản phẩm -->
                  <div class="skeleton-line mb-3" style="width: 55%; height: 13px;"></div>
                  <!-- Skeleton Dòng 4: Chân card (Giá & Nút giỏ hàng) -->
                  <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                    <div class="skeleton-line" style="width: 50%; height: 20px;"></div>
                    <div class="skeleton-box" style="width: 34px; height: 34px; border-radius: 4px;"></div>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <!-- Data thật -->
          <template v-else>
            <div class="col" v-for="prod in hotProducts" :key="prod.id">
              <div class="card h-100 product-card position-relative">
                <div class="card-img-wrapper position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center">
                  <img
                    :src="getImageUrl(prod.hinh_anh_chinh) || 'https://images.unsplash.com/photo-1559523161-0fc0d8b38a7a?auto=format&fit=crop&q=80&w=400'"
                    class="card-img-top w-100 transition-transform card-img-product"
                    :alt="prod.ten_san_pham"
                    @error="$event.target.src='https://placehold.co/300x200/f8f9fa/a1acb8?text=JPM+Marine'"
                  >
                </div>
                <div class="card-body text-start p-3 d-flex flex-column bg-white">
                  <!-- 1. Danh mục sản phẩm (Đứng riêng một hàng độc lập, không lo bị dài) -->
                  <div class="product-category mb-1 text-truncate" :title="prod.danh_muc ? prod.danh_muc.ten_danh_muc : 'OEM'">
                    {{ prod.danh_muc ? prod.danh_muc.ten_danh_muc : 'OEM' }}
                  </div>

                  <!-- 2. Tên sản phẩm (Chuẩn font Roboto, hiển thị đúng chữ hoa/thường theo Admin nhập) -->
                  <h5 class="card-title mb-1">
                    <router-link :to="`/chi-tiet-san-pham/${prod.slug}`" class="text-decoration-none stretched-link">
                      {{ prod.ten_san_pham }}
                    </router-link>
                  </h5>

                  <!-- 3. Mã sản phẩm (Đặt ngay dưới tên SP: Chuẩn tư duy kỹ thuật & tra cứu phụ tùng) -->
                  <div class="product-sku mb-3">
                    <template v-if="prod.ma_san_pham">
                      <span class="sku-label">Mã:</span> <span class="sku-code">{{ prod.ma_san_pham }}</span>
                    </template>
                    <template v-else>&nbsp;</template>
                  </div>

                  <!-- 4. Chân card: Giá bán & Nút giỏ hàng -->
                  <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                    <div>
                      <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold price-value">Liên hệ</span>
                      <span v-else class="text-danger fw-bold price-value">{{ Number(prod.gia_ban).toLocaleString('vi-VN') }} đ</span>
                    </div>
                    <button
                      v-if="!prod.is_gia_lien_he && prod.gia_ban"
                      class="btn btn-cart-action"
                      title="Thêm vào giỏ hàng"
                      @click.prevent="addToCart(prod)"
                    >
                      <i class="bi bi-cart2"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <div class="text-center mt-5">
          <router-link to="/san-pham" class="text-danger fw-semibold text-decoration-none d-inline-flex align-items-center fs-6">
            Xem tất cả sản phẩm <i class="bi bi-arrow-right ms-2"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 4: TẠI SAO CHỌN JPM MARINE (Thế mạnh & Cam kết dịch vụ)
         ======================================================== -->
    <section class="why-us-section section dark-background py-5">
      <div class="container py-4">
        <div class="row align-items-center">
          <!-- Cột Trái (5 phần): Tiêu đề và Mô tả -->
          <div class="col-lg-5 mb-5 mb-lg-0 pe-lg-5" data-aos="fade-right">
            <div class="mb-3">
              <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-uppercase fw-semibold px-3 py-2 rounded-pill">
                Thế mạnh vượt trội
              </span>
            </div>
            <h2 class="mb-4 text-white display-6 fw-bold">
              <span class="fw-light opacity-75">Tại Sao Chọn</span> <br>
              <span class="text-danger">JPM Marine</span>
            </h2>
            <p class="mb-4 text-white-50">
              Chúng tôi không chỉ cung cấp phụ tùng, chúng tôi mang đến sự an tâm tuyệt đối cho mỗi chuyến ra khơi với dịch vụ tiêu chuẩn quốc tế.
            </p>
            <router-link to="/ho-tro" class="btn btn-outline-danger px-4 py-2 mt-2 fw-semibold rounded-pill">
              Nhận Tư Vấn Ngay <i class="bi bi-arrow-right ms-2"></i>
            </router-link>
          </div>

          <!-- Cột Phải (7 phần): Danh sách điểm mạnh -->
          <div class="col-lg-7">
            <div class="d-flex flex-column gap-3">
              <!-- Item 1 -->
              <div class="d-flex align-items-center p-4 feature-card" data-aos="fade-left" data-aos-delay="100">
                <div class="feature-icon d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div>
                  <h3 class="feature-title">Hàng Chính Hãng 100%</h3>
                  <p class="feature-desc">Cam kết cung cấp phụ tùng chính hãng nhập khẩu trực tiếp từ Yamaha, Honda, Suzuki.</p>
                </div>
              </div>
              <!-- Item 2 -->
              <div class="d-flex align-items-center p-4 feature-card" data-aos="fade-left" data-aos-delay="200">
                <div class="feature-icon d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-truck"></i>
                </div>
                <div>
                  <h3 class="feature-title">Giao Hàng Siêu Tốc</h3>
                  <p class="feature-desc">Mạng lưới phân phối rộng khắp toàn quốc giúp phụ tùng đến tay nhanh chóng, an toàn.</p>
                </div>
              </div>
              <!-- Item 3 -->
              <div class="d-flex align-items-center p-4 feature-card" data-aos="fade-left" data-aos-delay="300">
                <div class="feature-icon d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-headset"></i>
                </div>
                <div>
                  <h3 class="feature-title">Hỗ Trợ Kỹ Thuật 24/7</h3>
                  <p class="feature-desc">Đội ngũ chuyên gia kỹ thuật hàng hải giàu kinh nghiệm luôn sẵn sàng tư vấn chuyên sâu.</p>
                </div>
              </div>
              <!-- Item 4 -->
              <div class="d-flex align-items-center p-4 feature-card" data-aos="fade-left" data-aos-delay="400">
                <div class="feature-icon d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-tools"></i>
                </div>
                <div>
                  <h3 class="feature-title">Dịch Vụ Tận Bến</h3>
                  <p class="feature-desc">Đội ngũ cơ động sẵn sàng di chuyển đến bến neo đậu để kiểm tra và khắc phục sự cố.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 5: TIN TỨC & KINH NGHIỆM (Cẩm nang kỹ thuật hàng hải)
         ======================================================== -->
    <section class="news-section section bg-white py-5">
      <div class="container section-title" data-aos="fade-up">
        <h2>Tin tức & Kinh nghiệm</h2>
        <p>Cập nhật kiến thức bảo dưỡng và tin tức mới nhất về thiết bị tàu thuyền</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gx-lg-5 gy-4">

          <!-- Bài nổi bật (cột trái 5 phần - Dành riêng cho bài viết có Video) -->
          <div class="col-lg-5">
            <!-- Skeleton -->
            <div v-if="!videoArticle" class="d-flex flex-column h-100">
              <div class="skeleton-box skeleton-box-full mb-3"></div>
              <div class="px-1">
                <div class="skeleton-line mb-2 w-50"></div>
                <div class="skeleton-line mb-2 w-100"></div>
                <div class="skeleton-line mb-3 w-75"></div>
                <div class="skeleton-line w-25"></div>
              </div>
            </div>
            <!-- Data thật: Bài viết có video -->
            <div v-else class="d-flex flex-column h-100 group-hover p-2 position-relative">
              <!-- Khung Thumbnail có nút Play Video -->
              <div
                class="overflow-hidden mb-3 position-relative img-zoom-hover rounded skeleton-box-full video-thumb-wrap"
                @click="openVideoModal(videoArticle)"
                title="Bấm để xem video"
              >
                <img
                  :src="getImageUrl(videoArticle.thumbnail) || 'https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                  class="img-fluid object-fit-cover h-100 w-100 transition-transform rounded"
                  :alt="videoArticle.title"
                  @error="$event.target.src='https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                >
                <!-- Badge góc: VIDEO -->
                <span class="badge bg-danger position-absolute top-0 start-0 m-3 d-flex align-items-center gap-1 shadow-sm px-2 py-1 video-badge">
                  <i class="bi bi-play-circle-fill"></i> VIDEO
                </span>

                <!-- Icon Play Video tròn ở chính giữa ảnh -->
                <div class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center video-play-btn shadow">
                  <i class="bi bi-play-fill text-white fs-2 play-icon"></i>
                </div>
              </div>

              <!-- Nội dung thông tin bài viết -->
              <div class="d-flex flex-column flex-grow-1 px-1 position-relative">
                <!-- 1. Danh mục: Chữ đỏ thương hiệu -->
                <div class="news-category mb-1 text-truncate">
                  {{ videoArticle.category || 'CẨM NANG HÀNG HẢI' }}
                </div>
                <!-- 2. Tiêu đề: chuẩn Roboto .news-hero-title, màu deep navy đậm nét -->
                <h5 class="news-hero-title mb-2">
                  <router-link :to="`/tin-tuc/${videoArticle.slug || videoArticle.id}`" class="stretched-link">
                    {{ videoArticle.title }}
                  </router-link>
                </h5>
                <!-- 3. Mô tả tóm tắt (3 dòng) -->
                <p class="text-secondary small mb-3 text-truncate-3">{{ videoArticle.summary }}</p>
                <!-- 4. Ngày đăng: chuẩn .product-sku của Sản phẩm bán chạy -->
                <div class="mt-auto product-sku mb-0">
                  <span class="sku-label me-1"><i class="bi bi-calendar3 me-1"></i>NGÀY:</span>
                  <span class="sku-code">{{ formatDate(videoArticle.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Danh sách bài viết khác (cột phải 7 phần) -->
          <div class="col-lg-7">
            <!-- Skeleton -->
            <div v-if="!sideNews || sideNews.length === 0" class="d-flex flex-column justify-content-between h-100">
              <div v-for="n in 3" :key="'nsk-'+n" class="d-flex align-items-start py-3 border-bottom">
                <div class="skeleton-box skeleton-box-sm flex-shrink-0"></div>
                <div class="ms-3 flex-grow-1">
                  <div class="skeleton-line mb-2 w-25" style="height: 12px;"></div>
                  <div class="skeleton-line mb-2 w-75" style="height: 16px;"></div>
                  <div class="skeleton-line mb-2 w-100" style="height: 13px;"></div>
                  <div class="skeleton-line w-50" style="height: 12px;"></div>
                </div>
              </div>
            </div>
            <!-- Data thật: 3 bài viết khác -->
            <div v-else class="d-flex flex-column justify-content-between h-100">
              <div
                v-for="(item, idx) in sideNews"
                :key="item.id"
                class="d-flex align-items-start position-relative list-item-hover p-2"
                :class="idx < sideNews.length - 1 ? 'border-bottom pb-3 mb-2' : ''"
              >
                <div class="overflow-hidden img-zoom-hover rounded news-img-thumb flex-shrink-0">
                  <img
                    :src="getImageUrl(item.thumbnail) || 'https://placehold.co/400x300/f1f3f5/a1acb8?text=JPM'"
                    class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                    :alt="item.title"
                    @error="$event.target.src='https://placehold.co/400x300/f1f3f5/a1acb8?text=JPM'"
                  >
                </div>
                <div class="ms-3 flex-grow-1 d-flex flex-column align-self-stretch">
                  <!-- 1. Danh mục: Chữ đỏ thương hiệu -->
                  <div class="news-category mb-1 text-truncate">
                    {{ item.category || 'TIN TỨC' }}
                  </div>
                  <!-- 2. Tiêu đề: chuẩn Roboto .news-card-title, màu deep navy đậm nét -->
                  <h6 class="news-card-title fw-semibold mb-1 text-truncate-2">
                    <router-link :to="`/tin-tuc/${item.slug || item.id}`" class="stretched-link">
                      {{ item.title }}
                    </router-link>
                  </h6>
                  <!-- 3. Nội dung tóm tắt dưới tên bài viết (tối đa 3 dòng là ...) -->
                  <p class="text-secondary small mb-2 text-truncate-3">
                    {{ item.summary }}
                  </p>
                  <!-- 4. Ngày đăng: Mặc định cố định ở dưới cùng (mt-auto) -->
                  <div class="mt-auto product-sku mb-0">
                    <span class="sku-label me-1"><i class="bi bi-calendar3 me-1"></i>NGÀY:</span>
                    <span class="sku-code">{{ formatDate(item.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-5">
          <router-link to="/tin-tuc" class="text-danger fw-semibold text-decoration-none d-inline-flex align-items-center fs-6">
            Xem tất cả bài viết <i class="bi bi-arrow-right ms-2"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Modal xem Video YouTube cho bài viết Trang chủ -->
    <div
      class="modal fade"
      id="homeNewsVideoModal"
      tabindex="-1"
      aria-labelledby="homeNewsVideoModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0 shadow-lg overflow-hidden rounded-3 text-white">
          <div class="modal-header border-0 pb-0 pt-3 px-3 d-flex justify-content-between align-items-center">
            <h6 class="modal-title fw-semibold text-truncate pe-3" id="homeNewsVideoModalLabel">
              <i class="bi bi-play-circle-fill text-danger me-2"></i>{{ activeVideoItem?.title }}
            </h6>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="closeVideoModal"
            ></button>
          </div>
          <div class="modal-body p-2 p-md-3">
            <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-black shadow">
              <iframe
                v-if="activeVideoItem && activeVideoEmbedUrl"
                :src="activeVideoEmbedUrl"
                title="JPM Marine Video Player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useCartStore } from '../../../stores/cartStore';
import { useSiteSettings } from '../../../composables/useSiteSettings';

export default {
  name: "TrangChu",
  setup() {
    // ===== 1. KHỞI TẠO STATE, PINIA STORES & SITE SETTINGS =====
    const { settings } = useSiteSettings();
    const cartStore = useCartStore(); 
    const selectedBrand = ref("");
    const selectedEngineType = ref("");
    const selectedYear = ref("");
    const selectedModel = ref("");

    const youtubeEmbedUrl = computed(() => {
      const videoId = settings.value?.homepage_youtube_id || '5CVzOqvqI38';
      return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1&disablekb=1&fs=0&iv_load_policy=3`;
    });

    // ===== 2. TRUY VẤN DỮ LIỆU TỪ BACKEND (VUE QUERY SWR CACHING) =====
    // Lấy danh sách thương hiệu động cơ
    const { data: brands, isLoading: isLoadingBrands } = useQuery({
      queryKey: ['public-brands-v4'],
      queryFn: () => axiosClient.get('/public/thuong-hieu?_t=' + new Date().getTime()).then(res => res.data?.data || res.data || []),
      staleTime: 1000 * 60 * 30
    });

    const { data: engineTypes } = useQuery({
      queryKey: computed(() => ['public-engine-types', selectedBrand.value]),
      queryFn: () => axiosClient.get('/public/loai-dong-co', { params: { thuong_hieu_id: selectedBrand.value } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!selectedBrand.value)
    });

    const { data: engineModels } = useQuery({
      queryKey: computed(() => ['public-models-v6', selectedBrand.value, selectedEngineType.value, selectedYear.value]),
      queryFn: () => axiosClient.get('/public/dong-co-model', { params: { thuong_hieu_id: selectedBrand.value, loai_dong_co_id: selectedEngineType.value, nam_sx: selectedYear.value } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!selectedBrand.value)
    });

    const { data: years } = useQuery({
      queryKey: computed(() => ['public-years-v6', selectedBrand.value, selectedEngineType.value]),
      queryFn: () => axiosClient.get('/public/nam-san-xuat', { params: { thuong_hieu_id: selectedBrand.value, loai_dong_co_id: selectedEngineType.value } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30,
      enabled: computed(() => !!selectedBrand.value)
    });

    // Lấy 8 sản phẩm bán chạy nhất (2 hàng x 4 cột)
    const { data: hotProducts, isLoading: isLoadingHotProducts } = useQuery({
      queryKey: ['public-hot-products', 8],
      queryFn: () => axiosClient.get('/public/san-pham-ban-chay', { params: { limit: 8 } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 5
    });

    // Lấy bài viết kỹ thuật & cẩm nang mới nhất (lấy 8 bài để chọn lọc bài có video cho cột trái và 3 bài cho cột phải)
    const { data: latestNews, isLoading: isLoadingNews } = useQuery({
      queryKey: ['public-latest-news', 8],
      queryFn: () => axiosClient.get('/public/tin-tuc-moi-nhat', { params: { limit: 8 } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 10
    });

    // 1. Cột trái: Dành riêng cho 1 bài viết có Video
    const videoArticle = computed(() => {
      if (!latestNews.value || !Array.isArray(latestNews.value) || latestNews.value.length === 0) return null;
      // Ưu tiên bài viết có youtube_url
      const withVideo = latestNews.value.find(item => item.youtube_url && item.youtube_url.trim() !== '');
      if (withVideo) {
        return {
          ...withVideo,
          hasVideo: true,
        };
      }
      // Fallback: nếu chưa có bài nào có video thì gắn video mẫu cho bài đầu tiên
      return {
        ...latestNews.value[0],
        youtube_url: 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
        hasVideo: true,
      };
    });

    // 2. Cột phải: 3 bài viết thông thường mới nhất (loại bỏ bài video cột trái để tránh trùng lặp)
    const sideNews = computed(() => {
      if (!latestNews.value || !Array.isArray(latestNews.value)) return [];
      const videoId = videoArticle.value?.id;
      return latestNews.value.filter(item => item.id !== videoId).slice(0, 3);
    });

    // ===== 3. QUẢN LÝ POPUP MODAL XEM VIDEO YOUTUBE BÀI VIẾT =====
    const activeVideoItem = ref(null);

    const extractYoutubeId = (url) => {
      if (!url) return null;
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&]{11})/);
      return match ? match[1] : null;
    };

    const activeVideoEmbedUrl = computed(() => {
      if (!activeVideoItem.value?.youtube_url) return null;
      const id = extractYoutubeId(activeVideoItem.value.youtube_url);
      return id ? `https://www.youtube.com/embed/${id}?autoplay=1&rel=0` : null;
    });

    const openVideoModal = (item) => {
      if (!item) return;
      activeVideoItem.value = item;
      const el = document.getElementById('homeNewsVideoModal');
      if (el && window.bootstrap?.Modal) {
        let modalInstance = window.bootstrap.Modal.getInstance(el);
        if (!modalInstance) {
          modalInstance = new window.bootstrap.Modal(el);
        }
        modalInstance.show();
      }
    };

    const closeVideoModal = () => {
      activeVideoItem.value = null;
    };

    onMounted(() => {
      const el = document.getElementById('homeNewsVideoModal');
      if (el) {
        el.addEventListener('hidden.bs.modal', closeVideoModal);
      }
    });

    onUnmounted(() => {
      const el = document.getElementById('homeNewsVideoModal');
      if (el) {
        el.removeEventListener('hidden.bs.modal', closeVideoModal);
      }
    });

    // ===== 4. THEO DÕI THAY ĐỔI ĐỂ TỰ ĐỘNG RESET BỘ LỌC LIÊN ĐỚI (WATCHERS) =====
    watch(selectedBrand, () => { 
      selectedEngineType.value = ""; 
      selectedYear.value = ""; 
      selectedModel.value = ""; 
    });

    watch(selectedEngineType, () => {
      selectedYear.value = "";
      selectedModel.value = "";
    });
    
    watch(selectedYear, () => {
      selectedModel.value = "";
    });

    const isPageLoading = computed(() => isLoadingBrands.value || isLoadingHotProducts.value || isLoadingNews.value);

    const formatDate = (dateStr) => {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    };

    return {
      brands,
      engineTypes,
      engineModels,
      years,
      selectedBrand,
      selectedEngineType,
      selectedYear,
      selectedModel,
      hotProducts,
      latestNews,
      videoArticle,
      sideNews,
      activeVideoItem,
      activeVideoEmbedUrl,
      openVideoModal,
      closeVideoModal,
      formatDate,
      isPageLoading,
      isLoadingBrands, 
      cartStore,
      settings,
      youtubeEmbedUrl,
    };
  },
  data() {
    return {
      searchKeyword: "",
      openDropdown: null,
    };
  },
  computed: {
    brandLabel() {
      if (!this.selectedBrand || !this.brands) return 'Chọn thương hiệu';
      const b = this.brands.find(x => x.id === this.selectedBrand);
      return b ? b.name : 'Chọn thương hiệu';
    },
    partLabel() {
      if (!this.selectedEngineType || !this.engineTypes) return 'Chọn loại động cơ';
      const p = this.engineTypes.find(x => x.id === this.selectedEngineType);
      return p ? p.ten_loai_dong_co : 'Chọn loại động cơ';
    },
    yearLabel() {
      return this.selectedYear || 'Chọn năm sản xuất';
    },
    modelLabel() {
      if (!this.selectedModel || !this.engineModels) return 'Chọn model động cơ';
      const m = this.engineModels.find(x => x.id === this.selectedModel);
      return m ? m.ten_model : 'Chọn model động cơ';
    }
  },
  // ===== 5. CÁC HÀM XỬ LÝ SỰ KIỆN & ĐIỀU HƯỚNG (METHODS) =====
  methods: {
    getImageUrl(path) {
      if (!path) return "";
      if (path.startsWith("blob:") || path.startsWith("http")) return path;
      return `http://localhost:8000${path.startsWith("/") ? "" : "/"}${path}`;
    },
    searchParts() {
      if (!this.searchKeyword && !this.selectedBrand && !this.selectedEngineType && !this.selectedYear && !this.selectedModel) {
        this.$toast.error("Vui lòng nhập từ khóa hoặc chọn ít nhất 1 điều kiện để tìm kiếm!");
        return;
      }
      this.$router.push({
        path: '/tra-cuu',
        query: {
          keyword: this.searchKeyword,
          brand: this.selectedBrand,
          engineType: this.selectedEngineType,
          year: this.selectedYear,
          model: this.selectedModel
        }
      });
    },
    resetSearch() {
      this.searchKeyword = "";
      this.selectedBrand = "";
      this.selectedEngineType = "";
      this.selectedYear = "";
      this.selectedModel = "";
    },
    quickSearch(keyword) {
      this.searchKeyword = keyword;
      this.searchParts();
    },
    toggleDropdown(name) {
      this.openDropdown = this.openDropdown === name ? null : name;
    },
    selectOption(dropdown, value) {
      if (dropdown === 'brand') this.selectedBrand = value;
      else if (dropdown === 'engineType') this.selectedEngineType = value;
      else if (dropdown === 'year') this.selectedYear = value;
      else if (dropdown === 'model') this.selectedModel = value;
      this.openDropdown = null;
    },
    goToCategory(cat) {
      this.$router.push('/danh-sach-phu-tung');
    },
    addToCart(prod) {
      this.cartStore.addItem(prod, 1);
      this.$toast.success("Thêm sản phẩm thành công");
    }
  },
};
</script>

<style scoped>
/* ==============================================================
   TrangChu: Tinh chỉnh cục bộ cho khối Tin tức & Kinh nghiệm
   (Cách ly scoped trong trang này, không viết vào main.css chung)
   ============================================================== */
.news-category {
  color: var(--accent-color, #f82249);
  font-weight: 600;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.news-hero-title {
  font-family: var(--default-font);
  font-size: 1.25rem;
  color: var(--heading-color, #0A1128);
  font-weight: 600;
  line-height: 1.35;
}
.news-hero-title a,
.news-card-title a {
  color: var(--heading-color, #0A1128);
  text-decoration: none;
  transition: color 0.25s ease;
}
.group-hover:hover .news-hero-title a,
.list-item-hover:hover .news-card-title a {
  color: var(--heading-color, #0e1b4d);
}

.news-card-title {
  font-family: var(--default-font);
  font-size: 0.95rem;
  color: var(--heading-color, #0A1128);
  font-weight: 600;
  line-height: 1.4;
  margin-bottom: 0.25rem;
}

.news-img-thumb {
  width: 170px;
  aspect-ratio: 4/3;
  flex-shrink: 0;
}

.skeleton-box-sm {
  border-radius: 4px;
  width: 170px;
  aspect-ratio: 4/3;
}

@media (max-width: 576px) {
  .news-img-thumb,
  .skeleton-box-sm {
    width: 120px;
  }
}

/* ==============================================================
   Icon & Badge Video trên thumbnail bài viết cột trái
   ============================================================== */
.video-thumb-wrap {
  cursor: pointer;
}
.video-badge {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  z-index: 2;
  border-radius: 4px;
}
.video-play-btn {
  width: 58px;
  height: 58px;
  background-color: rgba(10, 17, 40, 0.75);
  color: #ffffff;
  border: 2px solid rgba(255, 255, 255, 0.85);
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  z-index: 2;
  backdrop-filter: blur(4px);
}
.video-play-btn .play-icon {
  margin-left: 3px;
  line-height: 1;
}
.group-hover:hover .video-play-btn,
.video-thumb-wrap:hover .video-play-btn {
  background-color: var(--accent-color, #f82249);
  border-color: #ffffff;
  transform: translate(-50%, -50%) scale(1.15) !important;
  box-shadow: 0 0 25px rgba(248, 34, 73, 0.55);
}

/* ==============================================================
   Khối 1: Tra Cứu Phụ Tùng Nhanh (Smart Filter Console)
   Thống nhất 100% phong cách TheEvent, màu Navy & Red thương hiệu
   ============================================================== */
.search-parts-section {
  padding: 55px 0 65px 0;
  background-color: #f8fafc;
}

.filter-console-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  box-shadow: 0 10px 30px rgba(10, 17, 40, 0.05);
  padding: 32px 36px;
  transition: box-shadow 0.3s ease;
  overflow: visible !important;
  position: relative;
  z-index: 20;
}

.filter-console-card:hover {
  box-shadow: 0 14px 40px rgba(10, 17, 40, 0.08);
}

.filter-label {
  display: flex;
  align-items: center;
  font-family: var(--default-font);
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--heading-color, #0A1128);
  letter-spacing: 0.1px;
}

.filter-label i {
  font-size: 0.9rem;
}

/* Custom Select Box */
.custom-select-wrapper {
  position: relative;
}
.custom-select-wrapper.z-active {
  z-index: 1050;
}
.custom-select-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  height: 46px;
  border-radius: 4px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  font-weight: 400;
  color: #64748b;
  font-family: var(--default-font);
}
.custom-select-box:hover {
  border-color: #94a3b8;
}
.custom-select-box.is-focused {
  border-color: var(--accent-color, #f82249);
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12);
}
.custom-select-box.has-value {
  color: var(--heading-color, #0A1128);
  font-weight: 500;
}
.select-arrow {
  transition: transform 0.2s ease;
  font-size: 11px;
  color: #94a3b8;
}
.select-arrow.rotated,
.custom-select-box.is-focused .select-arrow {
  transform: rotate(180deg);
  color: var(--accent-color, #f82249);
}

/* Dropdown Menu */
.custom-dropdown-list {
  position: absolute;
  min-width: 100%;
  width: max-content;
  max-width: 320px;
  top: calc(100% + 4px);
  list-style: none;
  margin: 0;
  padding: 6px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  box-shadow: 0 14px 35px rgba(10, 17, 40, 0.15);
  z-index: 1060;
  max-height: 320px;
  overflow-y: auto;
  overflow-x: hidden;
  font-family: var(--default-font);
}
.custom-dropdown-list::-webkit-scrollbar {
  width: 6px;
}
.custom-dropdown-list::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 4px;
}
.custom-dropdown-list::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-dropdown-list li {
  white-space: nowrap;
  padding: 9px 14px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 400;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.15s ease;
  border-radius: 4px;
}
.custom-dropdown-list li:hover {
  background: #f1f5f9;
  color: #0f172a;
}
.custom-dropdown-list li.active {
  background: var(--accent-color, #f82249);
  color: #ffffff;
  font-weight: 600;
}

.dropdown-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  background: transparent;
}

/* Search Input Box */
.search-input-box {
  display: flex;
  align-items: center;
  height: 46px;
  border-radius: 4px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  padding: 0 16px;
  transition: all 0.2s ease;
}
.search-input-box:focus-within {
  border-color: var(--accent-color, #f82249);
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12);
}
.search-input-box:hover {
  border-color: #94a3b8;
}
.search-input-icon {
  font-size: 1rem;
  color: #94a3b8;
  margin-right: 10px;
  flex-shrink: 0;
}
.search-input-box:focus-within .search-input-icon {
  color: var(--accent-color, #f82249);
}
.search-input-field {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.875rem;
  font-family: var(--default-font);
  color: var(--heading-color, #0A1128);
  font-weight: 400;
}
.search-input-field::placeholder {
  color: #94a3b8;
}

/* Nút Bấm Thao Tác (Buttons) */
.btn-reset-action {
  height: 46px;
  padding: 0 18px;
  border: 1px solid #d1d5db;
  background: #ffffff;
  color: #475569;
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  white-space: nowrap;
  flex-shrink: 0;
}
.btn-reset-action:hover {
  background: #f8fafc;
  border-color: #94a3b8;
  color: #0f172a;
}

.btn-search-action {
  height: 46px;
  padding: 0 24px;
  border: none;
  background: var(--accent-color, #f82249);
  color: #ffffff;
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 0.2px;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.25s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  white-space: nowrap;
  flex-grow: 1;
}
.btn-search-action:hover {
  background: #d91a3c;
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(248, 34, 73, 0.3);
}

/* Hàng Tags Phổ Biến */
.popular-tags-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 24px;
  padding-top: 18px;
  border-top: 1px dashed #e2e8f0;
}
.popular-tags-label {
  font-family: var(--default-font);
  font-size: 0.8rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  margin-right: 4px;
}
.popular-tags-list {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}
.quick-pill-tag {
  display: inline-flex;
  align-items: center;
  padding: 4px 14px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 500;
  text-decoration: none;
  border-radius: 20px;
  transition: all 0.2s ease;
}
.quick-pill-tag:hover {
  border-color: var(--accent-color, #f82249);
  color: var(--accent-color, #f82249);
  background-color: #fff5f6;
  transform: translateY(-1px);
}

@media (max-width: 991px) {
  .filter-console-card {
    padding: 24px 20px;
  }
}
</style>
