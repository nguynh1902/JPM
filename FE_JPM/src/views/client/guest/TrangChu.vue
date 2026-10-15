<template>
  <div class="jpm-homepage">
    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">
      <!-- Video Background (YouTube) -->
      <div style="position: absolute; inset: 0; z-index: 1; width: 100%; height: 100%; overflow: hidden; background: #000;">
        <iframe 
          :src="youtubeEmbedUrl" 
          frameborder="0" 
          allow="autoplay; encrypted-media" 
          style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100vw; height: 56.25vw; min-height: 100vh; min-width: 177.77vh; pointer-events: none;"
        ></iframe>
        <!-- Overlay chặn controls YouTube -->
        <div style="position: absolute; inset: 0; z-index: 2; background: transparent;"></div>
        <!-- Gradient bên trái -->
        <div style="position: absolute; inset: 0; z-index: 3; background: linear-gradient(90deg, rgba(6,12,34,0.85) 0%, rgba(6,12,34,0.4) 45%, rgba(6,12,34,0) 100%);"></div>
      </div>
      <div class="container flex-grow-1 d-flex align-items-center" style="position: relative; z-index: 3;">
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
              <router-link to="/tra-cuu" class="btn btn-hero-cta rounded-0 text-white text-uppercase d-inline-flex align-items-center justify-content-center">
                TRA CỨU PHỤ TÙNG
              </router-link>
            </div>
          </div>
        </div>
      </div>

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

    <!-- Parts Search Section -->
    <section id="search-parts" class="search-parts-section section" style="overflow: visible !important; position: relative; z-index: 99;">
      <div class="container" data-aos="fade-up">
        <div class="section-title" data-aos="fade-up">
          <span class="overline">TÌM KIẾM DỄ DÀNG</span>
          <h2>Tra Cứu Phụ Tùng Nhanh</h2>
          <p>Chọn thông số phương tiện của bạn để tìm đúng phụ tùng cần thiết</p>
        </div>

        <div class="search-glass-card" data-aos="fade-up" data-aos-delay="100">
          <!-- OVERLAY để click ra ngoài đóng dropdown -->
          <div class="dropdown-overlay" v-if="openDropdown" @click="openDropdown = null"></div>

          <!-- MAIN FILTER AREA -->
          <div class="search-main-area">
            <div class="filter-row">

              <!-- Thương Hiệu -->
              <div class="filter-group">
                <label><i class="bi bi-tag-fill"></i> Thương Hiệu</label>
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

              <!-- Loại Động Cơ -->
              <div class="filter-group">
                <label><i class="bi bi-puzzle-fill"></i> Loại Động Cơ</label>
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

              <!-- Năm Sản Xuất -->
              <div class="filter-group">
                <label><i class="bi bi-calendar3"></i> Năm Sản Xuất</label>
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

              <!-- Model Động Cơ -->
              <div class="filter-group">
                <label><i class="bi bi-diagram-3-fill"></i> Model Động Cơ</label>
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

            <!-- Search Input + Buttons -->
            <div class="search-footer">
              <div class="search-input-wrap">
                <label><i class="bi bi-search"></i> Tìm kiếm theo mã (Tùy chọn)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input
                      type="text"
                      class="form-control border-start-0 shadow-none"
                      placeholder="Nhập mã phụ tùng / model (VD: F250TUR...)"
                      v-model="searchKeyword"
                      @keyup.enter="searchParts"
                    />
                  </div>
              </div>
              <div class="search-actions">
                <button class="btn-reset" @click="resetSearch">
                  <i class="bi bi-arrow-counterclockwise"></i> Đặt Lại
                </button>
                <button class="btn-search" @click="searchParts">
                  <i class="bi bi-search"></i>
                  <span>Tìm Kiếm Phụ Tùng</span>
                  <span class="btn-search-shine"></span>
                </button>
              </div>
            </div>
          </div>

          <!-- FOOTER: Tags phổ biến -->
          <div class="search-tags-footer">
            <span class="tags-label"><i class="bi bi-lightning-charge-fill"></i> Phổ biến:</span>
            <a href="javascript:void(0)" class="search-tag" @click="quickSearch('Cánh quạt Yamaha F40')">Cánh quạt Yamaha F40</a>
            <a href="javascript:void(0)" class="search-tag" @click="quickSearch('Bugi Honda BF25')">Bugi Honda BF25</a>
            <a href="javascript:void(0)" class="search-tag" @click="quickSearch('Lọc dầu Suzuki DF150')">Lọc dầu Suzuki DF150</a>
            <a href="javascript:void(0)" class="search-tag" @click="quickSearch('Bơm nhiên liệu')">Bơm nhiên liệu</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Sponsors Section -->
    <section id="sponsors" class="sponsors section bg-light py-5">
      <div class="container section-title" data-aos="fade-up">
        <span class="overline">Đối Tác Tin Cậy</span>
        <h2>Phụ Tùng Theo Thương Hiệu</h2>
        <p>Đa dạng các loại phụ tùng chính hãng từ những thương hiệu hàng đầu thế giới</p>
      </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-0 clients-wrap">
          <!-- Skeleton loading -->
          <template v-if="isLoadingBrands">
            <div class="col-xl-3 col-md-4 client-logo" v-for="n in 8" :key="'skel-' + n">
              <div class="d-flex align-items-center justify-content-center w-100 h-100">
                <div style="width: 120px; height: 40px; background: #e2e8f0; border-radius: 4px; animation: pulse-skeleton 1.5s infinite;"></div>
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

    <!-- Sản phẩm nổi bật -->
    <section class="products-section section">
      <div class="container section-title" data-aos="fade-up">
        <span class="overline">Lựa chọn hàng đầu</span>
        <h2>Sản Phẩm Bán Chạy</h2>
        <p>Những linh kiện phụ tùng được khách hàng tin dùng nhiều nhất tại JPM Marine</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
          <!-- Skeleton khi đang load -->
          <template v-if="!hotProducts">
            <div class="col" v-for="n in 4" :key="'sk-' + n">
              <div class="card h-100 premium-product-card">
                <div class="card-img-wrapper skeleton-box" style="aspect-ratio: 4/3;"></div>
                <div class="card-body p-4">
                  <div class="skeleton-line" style="width: 60%; height: 12px; margin-bottom: 12px;"></div>
                  <div class="skeleton-line" style="width: 90%; height: 16px; margin-bottom: 8px;"></div>
                  <div class="skeleton-line" style="width: 75%; height: 16px; margin-bottom: 20px;"></div>
                  <div class="skeleton-line" style="width: 40%; height: 20px;"></div>
                </div>
              </div>
            </div>
          </template>
          <!-- Data thật -->
          <template v-else>
            <div class="col" v-for="prod in hotProducts" :key="prod.id">
              <div class="card h-100 premium-product-card group-hover position-relative" style="border: 1px solid #e5e7eb; border-radius: 0; box-shadow: none;">
                  <div class="card-img-wrapper overflow-hidden img-zoom-hover" style="border-radius: 0;">
                    <img
                      :src="getImageUrl(prod.hinh_anh_chinh) || 'https://images.unsplash.com/photo-1559523161-0fc0d8b38a7a?auto=format&fit=crop&q=80&w=400'"
                      class="card-img-top w-100 transition-transform"
                      style="aspect-ratio: 4/3; object-fit: cover; border-radius: 0;"
                      :alt="prod.ten_san_pham"
                      @error="$event.target.src='https://placehold.co/300x200/f8f9fa/a1acb8?text=JPM+Marine'"
                    >
                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                      <button class="btn bg-white shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 4px;" title="Thêm vào giỏ" @click.prevent="addToCart(prod)">
                        <i class="bi bi-cart-plus" style="color: #dc3545; font-size: 1.1rem;"></i>
                      </button>
                    </div>
                  </div>
                  <div class="card-body text-start p-3 d-flex flex-column bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <span class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">{{ prod.danh_muc ? prod.danh_muc.ten_danh_muc : 'OEM' }}</span>
                    </div>
                    <h5 class="card-title mb-3" style="font-size: 0.95rem; line-height: 1.5; font-weight: 500; color: #1e293b;">
                      <router-link :to="`/chi-tiet-san-pham/${prod.slug}`" class="text-decoration-none stretched-link" style="color: inherit;">{{ prod.ten_san_pham }}</router-link>
                    </h5>
                    <div class="mt-auto d-flex align-items-center contact-action" style="border-top: 1px solid #f1f5f9; padding-top: 0.75rem; justify-content: flex-end;">
                      <span v-if="prod.is_gia_lien_he || !prod.gia_ban" class="text-danger fw-bold" style="font-size: 1.1rem;">Liên hệ</span>
                      <span v-else class="text-danger fw-bold" style="font-size: 1.1rem;">{{ Number(prod.gia_ban).toLocaleString('vi-VN') }} đ</span>
                    </div>
                  </div>
                </div>
            </div>
          </template>
        </div>

        <div class="text-center mt-5">
          <router-link to="/san-pham" class="btn btn-outline-danger rounded-0 px-4 py-2 fw-medium text-uppercase btn-view-all" style="letter-spacing: 2px; border-width: 1px;">Xem tất cả sản phẩm <i class="bi bi-arrow-right ms-2"></i></router-link>
        </div>
      </div>
    </section>


    <!-- Tại sao chọn JPM Marine -->
    <section class="why-us-section section dark-background py-5 py-lg-5">
      <div class="container py-4">
        <div class="row align-items-center">
          <!-- Cột Trái (5 phần): Tiêu đề và Mô tả -->
          <div class="col-lg-5 mb-5 mb-lg-0 pe-lg-5" data-aos="fade-right">
            <div class="mb-3">
              <span style="color: var(--accent-color); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 3px;">Thế mạnh vượt trội</span>
            </div>
            <h2 class="mb-4" style="color: #fff; font-size: 2.8rem; line-height: 1.2; font-family: var(--heading-font);">
              <span class="fw-light" style="opacity: 0.9;">Tại Sao Chọn</span> <br>
              <span class="fw-bold" style="color: var(--accent-color);">JPM Marine</span>
            </h2>
            <p class="mb-4" style="color: rgba(255,255,255,0.85); font-size: 1.05rem; line-height: 1.7; font-weight: 400;">Chúng tôi không chỉ cung cấp phụ tùng, chúng tôi mang đến sự an tâm tuyệt đối cho mỗi chuyến ra khơi với dịch vụ tiêu chuẩn quốc tế.</p>
            <router-link to="/ho-tro" class="btn btn-outline-pulse rounded-0 px-4 py-3 mt-3 fw-medium text-uppercase" style="letter-spacing: 2px; transition: 0.3s; font-size: 0.9rem;">Nhận Tư Vấn Ngay <i class="bi bi-arrow-right ms-2"></i></router-link>
          </div>

          <!-- Cột Phải (7 phần): List 4 Items -->
          <div class="col-lg-7">
            <div class="d-flex flex-column gap-3">
              <!-- Item 1 -->
              <div class="feature-list-card d-flex align-items-center p-4 rounded-4 feature-card" data-aos="fade-left" data-aos-delay="100" >
                <div class="icon feature-icon flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div>
                  <h3 class="feature-title">Hàng Chính Hãng 100%</h3>
                  <p class="feature-desc">Cam kết cung cấp phụ tùng chính hãng nhập khẩu trực tiếp từ Yamaha, Honda, Suzuki.</p>
                </div>
              </div>
              <!-- Item 2 -->
              <div class="feature-list-card d-flex align-items-center p-4 rounded-4 feature-card" data-aos="fade-left" data-aos-delay="200" >
                <div class="icon feature-icon flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-truck"></i>
                </div>
                <div>
                  <h3 class="feature-title">Giao Hàng Siêu Tốc</h3>
                  <p class="feature-desc">Mạng lưới phân phối rộng khắp toàn quốc giúp phụ tùng đến tay nhanh chóng, an toàn.</p>
                </div>
              </div>
              <!-- Item 3 -->
              <div class="feature-list-card d-flex align-items-center p-4 rounded-4 feature-card" data-aos="fade-left" data-aos-delay="300" >
                <div class="icon feature-icon flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle">
                  <i class="bi bi-headset"></i>
                </div>
                <div>
                  <h3 class="feature-title">Hỗ Trợ Kỹ Thuật 24/7</h3>
                  <p class="feature-desc">Đội ngũ chuyên gia kỹ thuật hàng hải giàu kinh nghiệm luôn sẵn sàng tư vấn chuyên sâu.</p>
                </div>
              </div>
              <!-- Item 4 -->
              <div class="feature-list-card d-flex align-items-center p-4 rounded-4 feature-card" data-aos="fade-left" data-aos-delay="400" >
                <div class="icon feature-icon flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle">
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

    <!-- Tin tức & Kinh nghiệm -->
    <section class="news-section section py-5">
      <div class="container section-title" data-aos="fade-up">
        <span class="overline">Cập nhật kiến thức</span>
        <h2>Tin tức & Kinh nghiệm</h2>
        <p>Cập nhật kiến thức bảo dưỡng và tin tức mới nhất về thiết bị tàu thuyền</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gx-lg-5 gy-4">

          <!-- Bài nổi bật (item đầu tiên) -->
          <div class="col-lg-5">
            <!-- Skeleton -->
            <div v-if="!latestNews" class="feature-news-item d-flex flex-column h-100">
              <div class="skeleton-box skeleton-box-full mb-4"></div>
              <div class="px-1">
                <div class="skeleton-line mb-3" style="width:40%;height:22px;"></div>
                <div class="skeleton-line mb-2" style="width:90%;height:18px;"></div>
                <div class="skeleton-line mb-4" style="width:75%;height:18px;"></div>
                <div class="skeleton-line" style="width:30%;height:14px;"></div>
              </div>
            </div>
            <!-- Data thật -->
            <div v-else-if="latestNews.length > 0" class="feature-news-item d-flex flex-column h-100 group-hover p-2">
              <div class="overflow-hidden mb-4 position-relative img-zoom-hover skeleton-box-md">
                <img
                  :src="getImageUrl(latestNews[0].thumbnail) || 'https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                  class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                  :alt="latestNews[0].title"
                  @error="$event.target.src='https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                >
              </div>
              <div class="d-flex flex-column flex-grow-1 px-1">
                <span class="mb-3 align-self-start text-uppercase fw-bold" style="color: var(--accent-color); font-size: 0.8rem; letter-spacing: 2px;">
                  {{ latestNews[0].category || 'Tin tức' }}
                </span>
                <h4 class="fw-semibold mb-3" style="line-height: 1.4; font-family: var(--heading-font);">
                  <router-link :to="`/tin-tuc/${latestNews[0].slug || latestNews[0].id}`" class="text-dark text-decoration-none">{{ latestNews[0].title }}</router-link>
                </h4>
                <p class="text-secondary mb-4" style="font-size: 0.95rem; line-height: 1.6; font-weight: 300;">{{ latestNews[0].summary }}</p>
                <div class="mt-auto text-muted fw-medium text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                  <i class="bi bi-calendar3 me-2"></i>{{ formatDate(latestNews[0].created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Danh sách bài (các item tiếp theo) -->
          <div class="col-lg-7">
            <!-- Skeleton -->
            <div v-if="!latestNews" class="news-list-container d-flex flex-column justify-content-between h-100">
              <div v-for="n in 5" :key="'nsk-'+n" class="news-list-item d-flex align-items-center py-3 border-bottom">
                <div class="skeleton-box skeleton-box-sm flex-shrink-0"></div>
                <div class="ms-4 flex-grow-1">
                  <div class="skeleton-line mb-2" style="width:85%;height:16px;"></div>
                  <div class="skeleton-line mb-2" style="width:60%;height:16px;"></div>
                  <div class="skeleton-line" style="width:30%;height:12px;"></div>
                </div>
              </div>
            </div>
            <!-- Data thật -->
            <div v-else class="news-list-container d-flex flex-column justify-content-between h-100">
              <div
                v-for="(item, idx) in latestNews.slice(1)"
                :key="item.id"
                class="news-list-item d-flex align-items-center position-relative list-item-hover px-2"
                :class="idx === 0 ? 'pt-0 pb-3 border-bottom' : idx === latestNews.slice(1).length - 1 ? 'pt-3 pb-0' : 'py-3 border-bottom'"
              >
                <div class="flex-shrink-0 overflow-hidden img-zoom-hover" style="border-radius: 4px; width: 140px; aspect-ratio: 16/9;">
                  <img
                    :src="getImageUrl(item.thumbnail) || 'https://placehold.co/300x200/f1f3f5/a1acb8?text=JPM'"
                    class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                    :alt="item.title"
                    @error="$event.target.src='https://placehold.co/300x200/f1f3f5/a1acb8?text=JPM'"
                  >
                </div>
                <div class="ms-4 flex-grow-1">
                  <h6 class="fw-medium mb-1 news-title" style="font-size: 1.2rem; line-height: 1.4;">
                    <router-link :to="`/tin-tuc/${item.slug || item.id}`" class="text-dark text-decoration-none stretched-link">{{ item.title }}</router-link>
                  </h6>
                  <div class="text-muted fw-medium text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                    <i class="bi bi-calendar3 me-1"></i>{{ formatDate(item.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-5">
          <router-link to="/tin-tuc" class="btn btn-outline-danger rounded-0 px-4 py-2 fw-medium text-uppercase btn-view-all" style="letter-spacing: 2px; border-width: 1px;">Xem tất cả bài viết <i class="bi bi-arrow-right ms-2"></i></router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useCartStore } from '../../../stores/cartStore';
import { useSiteSettings } from '../../../composables/useSiteSettings';

export default {
  name: "TrangChu",
  setup() {
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

    const { data: hotProducts, isLoading: isLoadingHotProducts } = useQuery({
      queryKey: ['public-hot-products'],
      queryFn: () => axiosClient.get('/public/san-pham-ban-chay', { params: { limit: 4 } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 5
    });

    const { data: latestNews, isLoading: isLoadingNews } = useQuery({
      queryKey: ['public-latest-news'],
      queryFn: () => axiosClient.get('/public/tin-tuc-moi-nhat', { params: { limit: 6 } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 10
    });

    // Reset dependent filters when parent filter changes
    watch(selectedBrand, () => { selectedEngineType.value = ""; selectedYear.value = ""; selectedModel.value = ""; });

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
      return d.toLocaleDateString('vi-VN', { day: '2-digit', month: 'long', year: 'numeric' });
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
.feature-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.05);
  transition: 0.3s;
}
.feature-card:hover {
  background: rgba(255,255,255,0.05);
  transform: translateY(-3px);
}
.feature-icon {
  width: 60px;
  height: 60px;
  background: rgba(248, 34, 73, 0.1);
  color: var(--accent-color);
  font-size: 24px;
  margin-right: 20px;
  transition: 0.3s;
}
.feature-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 6px;
}
.feature-desc {
  font-size: 0.95rem;
  color: rgba(255,255,255,0.6);
  margin-bottom: 0;
  line-height: 1.5;
}
.skeleton-box-full {
  border-radius: 4px;
  aspect-ratio: 16/9;
  width: 100%;
}
.skeleton-box-md {
  border-radius: 4px;
  aspect-ratio: 16/9;
}
.skeleton-box-sm {
  border-radius: 4px;
  width: 140px;
  aspect-ratio: 16/9;
}


/* Biến màu sắc */
:root {
  --jpm-red: var(--accent-color);
  --jpm-dark: #060c22;
  --jpm-gray: #f5f6fa;
}

/* ============================
   SEARCH SECTION MOVED TO MAIN.CSS
==============================*/

@media (max-width: 991px) {
  .filter-row { grid-template-columns: repeat(2, 1fr); }
  .search-main-area { padding: 28px 24px 20px; }
  .search-tags-footer { padding: 16px 24px; }
  .search-footer { flex-direction: column; align-items: stretch; }
  .search-actions { justify-content: flex-end; }
  .btn-search, .btn-reset { height: 46px; }
}
@media (max-width: 575px) {
  .filter-row { grid-template-columns: 1fr; }
  .search-actions { flex-direction: column; }
  .btn-search, .btn-reset { width: 100%; justify-content: center; }
}


/* ============================
   WHY CHOOSE US (DARK)
==============================*/
.why-us-section { padding: 80px 0; background: #060c22; color: #fff; }
.feature-box { padding: 40px 30px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-top: 3px solid rgba(248,34,73,0.3); border-radius: 8px; text-align: center; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); height: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
.feature-box:hover { background: rgba(255,255,255,0.06); transform: translateY(-8px); border-color: rgba(255,255,255,0.1); border-top-color: var(--accent-color); box-shadow: 0 15px 40px rgba(248,34,73,0.15); }
.feature-box .icon { display: inline-block; font-size: 44px; color: var(--accent-color); text-shadow: 0 0 25px rgba(255,71,109,0.5); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.feature-box:hover .icon { transform: scale(1.15) translateY(-3px); color: var(--accent-color); text-shadow: 0 0 35px rgba(255,71,109,0.9); }
.feature-box h3 { font-size: 20px; font-weight: 700; margin-bottom: 16px; transition: 0.3s; }
.feature-box:hover h3 { color: var(--accent-color); }
.feature-box p { font-size: 15px; color: rgba(255,255,255,0.85); line-height: 1.6; margin: 0; }

/* ============================
   PRODUCTS SECTION (Minimalist)
==============================*/
.products-section { padding: 60px 0; }




/* ============================
   GLOBAL LINK
==============================*/
.jpm-link-minimal { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: #0e1b4d; text-transform: uppercase; text-decoration: none; transition: 0.2s; letter-spacing: 0.5px; }




@media (min-width: 992px) {
  .news-divider {
    border-right: 1px solid #e0e6f0;
  }
}

/* Fallback/Cache-buster: Ensure hover color applies even if main.css is cached */
.news-list-item:hover h6 a,
.news-list-item:hover .news-title a,
.news-list-item:hover a.stretched-link,
.list-item-hover:hover a {
  color: #0e1b4d !important;
}
</style>




















