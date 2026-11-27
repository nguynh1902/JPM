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
          <div class="col-lg-10 text-start">
            <h1 data-aos="fade-up" data-aos-delay="100" class="hero-main-title">
              <span class="hero-title-accent">{{ settings.homepage_hero_title || 'JPM MARINE' }}</span>
              <span class="hero-title-white">{{ settings.homepage_hero_subtitle || 'PHỤ TÙNG TÀU THUYỀN NHẬT BẢN' }}</span>
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
      <div id="about" class="about-info mt-auto position-relative">
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
    <section id="search-parts" class="search-parts-section section home-section-slate position-relative z-3">
      <div class="container" data-aos="fade-up">
        <!-- Tiêu đề Section chuẩn Editorial Luxury -->
        <div class="section-title" data-aos="fade-up">
          <h2>Tra cứu phụ tùng nhanh</h2>
          <p>Chọn thông số phương tiện hoặc nhập mã để tra cứu chính xác phụ tùng cần thiết</p>
        </div>

        <!-- Khung Tra Cứu Sang Trọng, Thống Nhất Chuẩn Editorial Luxury -->
        <div class="card filter-console-card position-relative" data-aos="fade-up" data-aos-delay="100">
          <!-- 1. Hàng 4 Dropdown Lọc Động Cơ (4 cột 3-3-3-3 chuẩn trục dọc) -->
          <div class="row g-3 mb-3">
            <!-- Cột 1: Thương Hiệu -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Thương hiệu"
                v-model="selectedBrand"
                :options="brands || []"
                placeholder="Chọn thương hiệu"
                all-option-label="Tất cả thương hiệu"
                value-key="id"
                label-key="name"
              />
            </div>

            <!-- Cột 2: Loại Động Cơ -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Loại động cơ"
                v-model="selectedEngineType"
                :options="engineTypes || []"
                placeholder="Chọn loại động cơ"
                all-option-label="Tất cả loại động cơ"
                value-key="id"
                label-key="ten_loai_dong_co"
              />
            </div>

            <!-- Cột 3: Năm Sản Xuất -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Năm sản xuất"
                v-model="selectedYear"
                :options="years || []"
                placeholder="Chọn năm sản xuất"
                all-option-label="Tất cả các năm"
              />
            </div>

            <!-- Cột 4: Model Động Cơ -->
            <div class="col-12 col-sm-6 col-lg-3">
              <JpmSelect
                label="Model động cơ"
                v-model="selectedModel"
                :options="engineModels || []"
                placeholder="Chọn model động cơ"
                all-option-label="Tất cả model"
                value-key="id"
                label-key="ten_model"
              />
            </div>
          </div>

          <!-- 2. Hàng Tìm Kiếm Theo Mã & Cụm Nút Thao Tác (Khớp thẳng trục dọc 9 - 3 với 4 cột trên) -->
          <div class="row g-3 align-items-end pt-1">
            <!-- Ô Tìm kiếm mã (Khớp đúng độ rộng 3 cột đầu trên màn Desktop) -->
            <div class="col-12 col-lg-8 col-xl-9">
              <label class="filter-label mb-2">
                Tìm kiếm theo mã <span class="filter-label-optional ms-1">(Tùy chọn)</span>
              </label>
              <div class="search-input-box">
                <i class="bi bi-search search-input-icon"></i>
                <input
                  type="text"
                  class="search-input-field"
                  placeholder="Nhập mã phụ tùng OEM hoặc model động cơ (VD: F250TUR, 688-45941-01...)"
                  v-model="searchKeyword"
                  @keyup.enter="searchParts"
                />
                <button
                  v-if="searchKeyword"
                  type="button"
                  class="btn-clear-search"
                  @click="searchKeyword = ''"
                  title="Xóa từ khóa"
                  aria-label="Xóa từ khóa"
                >
                  <i class="bi bi-x-circle-fill"></i>
                </button>
              </div>
            </div>

            <!-- Cụm Nút Thao Tác (Dùng 100% class Bootstrap 5 chuẩn ăn theo main.css toàn Client) -->
            <div class="col-12 col-lg-4 col-xl-3">
              <div class="d-flex gap-2">
                <button
                  type="button"
                  class="btn btn-outline-secondary filter-control-h px-3 flex-shrink-0 text-nowrap"
                  @click="resetSearch"
                  title="Đặt lại bộ lọc"
                >
                  Đặt lại
                </button>
                <button
                  type="button"
                  class="btn btn-danger filter-control-h px-4 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2 text-nowrap"
                  @click="searchParts"
                >
                  <i class="bi bi-search"></i>
                  <span>Tra cứu ngay</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 3. Hàng Từ Khóa Gợi Ý (Không vạch đứt, không icon tia sét, đồng bộ bảng màu Slate & Navy) -->
          <div class="popular-tags-row">
            <span class="popular-tags-label">Tra cứu nhiều:</span>
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
    <section id="sponsors" class="sponsors section bg-white">
      <div class="container section-title" data-aos="fade-up">
        <h2>Phụ tùng theo thương hiệu</h2>
        <p>Đa dạng các loại phụ tùng chính hãng từ những thương hiệu hàng đầu thế giới</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-0 clients-wrap">
          <!-- Skeleton loading -->
          <template v-if="isLoadingBrands">
            <div class="col-6 col-md-4 col-xl-3 client-logo" v-for="n in 8" :key="'skel-' + n">
              <div class="d-flex align-items-center justify-content-center w-100 h-100">
                <div class="skeleton-box skeleton-box-brand"></div>
              </div>
            </div>
          </template>

          <!-- Dynamic brands from API -->
          <template v-else-if="brands && brands.length > 0">
            <div class="col-6 col-md-4 col-xl-3 client-logo" v-for="brand in brands" :key="brand.id">
              <router-link :to="`/thuong-hieu/${brand.code ? brand.code.toLowerCase() : ''}`" class="d-flex align-items-center justify-content-center w-100 h-100">
                <img
                  :src="brand.logo_path ? (brand.logo_path.startsWith('http') || brand.logo_path.startsWith('/client/') ? brand.logo_path : getImageUrl(brand.logo_path.startsWith('storage/') ? brand.logo_path : 'storage/' + brand.logo_path)) : ''"
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
    <section class="products-section section home-section-slate">
      <div class="container section-title" data-aos="fade-up">
        <h2>Sản phẩm bán chạy</h2>
        <p>Những linh kiện phụ tùng được khách hàng tin dùng nhiều nhất tại JPM Marine</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
          <!-- Skeleton khi đang load (8 sản phẩm = 2 hàng) -->
          <template v-if="!hotProducts || isLoadingHotProducts">
            <div class="col" v-for="n in 8" :key="'sk-' + n">
              <ProductCard :loading="true" />
            </div>
          </template>
          <!-- Data thật -->
          <template v-else>
            <div class="col" v-for="prod in hotProducts" :key="prod.id">
              <ProductCard :product="prod" @add-to-cart="addToCart" />
            </div>
          </template>
        </div>

        <div class="text-center mt-4 mb-2">
          <router-link to="/san-pham" class="section-more-link">
            Xem tất cả sản phẩm <i class="bi bi-arrow-right ms-2"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 4: GIỚI THIỆU & TẠI SAO CHỌN JPM MARINE (Split + KPI + Quy trình 01→04)
         ======================================================== -->
    <section class="home-about-section bg-white">
      <div class="container-fluid about-wide-container">
        <!-- Tầng trên: Trái thông tin + 4 chỉ số | Phải Card khung ảnh bo góc -->
        <div class="row align-items-center g-4 g-lg-5" data-aos="fade-up">
          <div class="col-lg-5">
            <span class="banner-eyebrow">Về JPM Marine</span>
            <h2 class="home-about-title mb-3">
              Tại sao chọn <span class="banner-title-accent">JPM Marine</span>
            </h2>
            <p class="home-about-desc mb-4">
              JPM Marine là đơn vị chuyên cung ứng phụ tùng động cơ thủy chính hãng OEM từ Nhật Bản và Âu Mỹ, đồng hành cùng xưởng sửa chữa, cảng tàu và chủ phương tiện trên toàn quốc.
            </p>

            <!-- 4 Chỉ số nổi bật -->
            <div class="about-metrics-grid mb-4">
              <div class="about-metric-item">
                <div class="about-metric-val">12+</div>
                <div class="about-metric-label">Năm kinh nghiệm</div>
              </div>
              <div class="about-metric-item">
                <div class="about-metric-val">8+</div>
                <div class="about-metric-label">Thương hiệu OEM</div>
              </div>
              <div class="about-metric-item">
                <div class="about-metric-val">100%</div>
                <div class="about-metric-label">Chuẩn sơ đồ máy</div>
              </div>
              <div class="about-metric-item">
                <div class="about-metric-val">24h</div>
                <div class="about-metric-label">Phản hồi báo giá</div>
              </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
              <router-link to="/san-pham" class="btn btn-about-outline">
                <span>Khám phá cửa hàng</span>
                <i class="bi bi-arrow-right ms-2"></i>
              </router-link>
              <router-link to="/ho-tro" class="btn btn-about-link">
                <span>Tìm hiểu hỗ trợ</span>
              </router-link>
            </div>
          </div>

          <div class="col-lg-7">
            <!-- Card chờ ảnh Showroom / Kho phụ tùng (Khi có ảnh chỉ cần thay vào thẻ img bên trong) -->
            <div class="about-media-card">
              <!-- <img src="/client/image/showroom_jpm.jpg" alt="Kho phụ tùng JPM Marine" class="w-100 h-100 object-fit-cover" /> -->
              <div class="about-media-placeholder">
                <div class="about-media-badge">JPM MARINE SHOWROOM &amp; KHO VẬN</div>
                <div class="about-media-icon-wrap">
                  <i class="bi bi-image"></i>
                </div>
                <div class="about-media-title">Khung Ảnh Giới Thiệu Doanh Nghiệp / Kho Phụ Tùng</div>
                <div class="about-media-hint">Tỉ lệ chuẩn 16:10 (khuyến nghị ảnh ngang 1280×800px)</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tầng dưới: Quy trình đặt hàng & tra cứu kỹ thuật 01 → 04 -->
        <div class="about-process-wrapper mt-5 pt-4" data-aos="fade-up" data-aos-delay="100">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <h3 class="about-process-heading mb-0">Quy trình tra cứu &amp; cung ứng</h3>
            <span class="about-process-sub">Chính xác theo dải số khung &amp; model động cơ</span>
          </div>

          <div class="row g-3 g-lg-4 about-process-grid">
            <div class="col-12 col-sm-6 col-lg-3 about-process-col">
              <div class="about-step-card">
                <div class="about-step-top">
                  <span class="about-step-num">01</span>
                  <i class="bi bi-arrow-right about-step-arrow d-none d-lg-inline-flex"></i>
                </div>
                <h6 class="about-step-title">Chọn hãng &amp; Model</h6>
                <p class="about-step-desc">Lọc nhanh theo thương hiệu, loại động cơ, công suất và năm sản xuất.</p>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 about-process-col">
              <div class="about-step-card">
                <div class="about-step-top">
                  <span class="about-step-num">02</span>
                  <i class="bi bi-arrow-right about-step-arrow d-none d-lg-inline-flex"></i>
                </div>
                <h6 class="about-step-title">Đối chiếu sơ đồ máy</h6>
                <p class="about-step-desc">Kiểm tra trực tiếp vị trí linh kiện và mã thay thế Supersession chuẩn OEM.</p>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 about-process-col">
              <div class="about-step-card">
                <div class="about-step-top">
                  <span class="about-step-num">03</span>
                  <i class="bi bi-arrow-right about-step-arrow d-none d-lg-inline-flex"></i>
                </div>
                <h6 class="about-step-title">Đặt hàng &amp; Báo giá</h6>
                <p class="about-step-desc">Thanh toán linh hoạt sản phẩm có sẵn hoặc gửi yêu cầu báo giá minh bạch.</p>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 about-process-col">
              <div class="about-step-card">
                <div class="about-step-top">
                  <span class="about-step-num">04</span>
                </div>
                <h6 class="about-step-title">Giao tận xưởng &amp; cảng</h6>
                <p class="about-step-desc">Đóng gói chống sốc chuyên dụng, gửi hỏa tốc hoặc chành xe toàn quốc.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         KHỐI 5: TIN TỨC & KINH NGHIỆM (Cẩm nang kỹ thuật hàng hải)
         ======================================================== -->
    <section class="news-section section home-section-slate">
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
                class="overflow-hidden mb-3 position-relative img-zoom-hover video-thumb-wrap"
                @click="openVideoModal(videoArticle)"
                title="Bấm để xem video"
              >
                <img
                  :src="getImageUrl(videoArticle.thumbnail) || 'https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                  class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                  :alt="videoArticle.title"
                  @error="$event.target.src='https://placehold.co/800x500/f1f3f5/a1acb8?text=JPM+Marine'"
                >
                <!-- Badge góc: VIDEO (Nền kính mờ Deep Navy chuẩn Luxury) -->
                <span class="position-absolute top-0 start-0 m-3 d-inline-flex align-items-center gap-1 px-2 py-1 video-badge">
                  <i class="bi bi-play-fill"></i> VIDEO
                </span>

                <!-- Icon Play Video tròn ở chính giữa ảnh -->
                <div class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center video-play-btn shadow">
                  <i class="bi bi-play-fill text-white fs-2 play-icon"></i>
                </div>
              </div>

              <!-- Nội dung thông tin bài viết -->
              <div class="d-flex flex-column flex-grow-1 px-1 position-relative">
                <!-- 1. Danh mục: Đỏ Red Marine #F82249 đồng bộ chuẩn Tin Tức -->
                <div class="news-category mb-1 text-truncate">
                  {{ videoArticle.category || videoArticle.type || videoArticle.loai || 'CẨM NANG HÀNG HẢI' }}
                </div>
                <!-- 2. Tiêu đề: Deep Navy #0A1128 sắc nét -->
                <h4 class="fw-bold news-hero-title mb-2">
                  <router-link :to="`/tin-tuc/${videoArticle.slug || videoArticle.id}`" class="stretched-link">
                    {{ videoArticle.title }}
                  </router-link>
                </h4>
                <!-- 3. Mô tả tóm tắt: Cool Slate #64748b trong trẻo -->
                <p class="news-summary news-summary-lg mb-3 text-truncate-3">{{ videoArticle.summary }}</p>
                <!-- 4. Ngày đăng: Mảnh nhẹ chuẩn Editorial -->
                <div class="mt-auto news-meta">
                  <i class="bi bi-calendar3 me-1"></i>{{ formatDate(videoArticle.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Danh sách bài viết khác (cột phải 7 phần) -->
          <div class="col-lg-7">
            <!-- Skeleton -->
            <div v-if="!sideNews || sideNews.length === 0" class="d-flex flex-column justify-content-between gap-3 h-100">
              <div v-for="n in 3" :key="'nsk-'+n" class="d-flex align-items-start p-2">
                <div class="skeleton-box skeleton-box-sm flex-shrink-0"></div>
                <div class="ms-3 flex-grow-1">
                  <div class="skeleton-line mb-2 w-25" style="height: 12px;"></div>
                  <div class="skeleton-line mb-2 w-75" style="height: 16px;"></div>
                  <div class="skeleton-line mb-2 w-100" style="height: 13px;"></div>
                  <div class="skeleton-line w-50" style="height: 12px;"></div>
                </div>
              </div>
            </div>
            <!-- Data thật: 3 bài viết khác (Không dùng border-bottom cong mép) -->
            <div v-else class="d-flex flex-column justify-content-between gap-3 h-100">
              <div
                v-for="item in sideNews"
                :key="item.id"
                class="d-flex align-items-start position-relative list-item-hover p-2"
              >
                <div class="overflow-hidden img-zoom-hover news-img-thumb flex-shrink-0">
                  <img
                    :src="getImageUrl(item.thumbnail) || 'https://placehold.co/400x300/f1f3f5/a1acb8?text=JPM'"
                    class="img-fluid object-fit-cover h-100 w-100 transition-transform"
                    :alt="item.title"
                    @error="$event.target.src='https://placehold.co/400x300/f1f3f5/a1acb8?text=JPM'"
                  >
                </div>
                <div class="ms-3 flex-grow-1 d-flex flex-column align-self-stretch">
                  <!-- 1. Danh mục: Đỏ Red Marine #F82249 đồng bộ chuẩn Tin Tức -->
                  <div class="news-category mb-1 text-truncate">
                    {{ item.category || item.type || item.loai || 'TIN TỨC' }}
                  </div>
                  <!-- 2. Tiêu đề: Deep Navy #0A1128 -->
                  <h5 class="fw-bold news-card-title mb-1 text-truncate-2">
                    <router-link :to="`/tin-tuc/${item.slug || item.id}`" class="stretched-link">
                      {{ item.title }}
                    </router-link>
                  </h5>
                  <!-- 3. Nội dung tóm tắt: Cool Slate #64748b -->
                  <p class="news-summary mb-2 text-truncate-2">
                    {{ item.summary }}
                  </p>
                  <!-- 4. Ngày đăng: Mảnh nhẹ chuẩn Editorial -->
                  <div class="mt-auto news-meta">
                    <i class="bi bi-calendar3 me-1"></i>{{ formatDate(item.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-4">
          <router-link to="/tin-tuc" class="section-more-link">
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
        <div class="modal-content bg-dark border-0 shadow-lg overflow-hidden text-white video-modal-content">
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
            <div class="ratio ratio-16x9 overflow-hidden bg-black shadow video-ratio-box">
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
import ProductCard from '../../../components/client/ProductCard.vue';
import JpmSelect from '../../../components/client/JpmSelect.vue';

export default {
  name: "TrangChu",
  components: {
    ProductCard,
    JpmSelect
  },
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
    // Lấy danh sách thương hiệu động cơ (Tận dụng TanStack cache & Backend Response Cache)
    const { data: brands, isLoading: isLoadingBrands } = useQuery({
      queryKey: ['public-brands'],
      queryFn: () => axiosClient.get('/public/thuong-hieu').then(res => res.data?.data || res.data || []),
      staleTime: 1000 * 60 * 30
    });

    const { data: engineTypes } = useQuery({
      queryKey: computed(() => ['public-engine-types', selectedBrand.value || 'all']),
      queryFn: () => axiosClient.get('/public/loai-dong-co', { params: { thuong_hieu_id: selectedBrand.value || undefined } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30
    });

    const { data: engineModels } = useQuery({
      queryKey: computed(() => ['public-models-v6', selectedBrand.value || 'all', selectedEngineType.value || 'all', selectedYear.value || 'all']),
      queryFn: () => axiosClient.get('/public/dong-co-model', { params: { thuong_hieu_id: selectedBrand.value || undefined, loai_dong_co_id: selectedEngineType.value || undefined, nam_sx: selectedYear.value || undefined } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30
    });

    const { data: years } = useQuery({
      queryKey: computed(() => ['public-years-v6', selectedBrand.value || 'all', selectedEngineType.value || 'all']),
      queryFn: () => axiosClient.get('/public/nam-san-xuat', { params: { thuong_hieu_id: selectedBrand.value || undefined, loai_dong_co_id: selectedEngineType.value || undefined } }).then(res => res.data?.data || res.data || res),
      staleTime: 1000 * 60 * 30
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
      return `${d.getDate().toString().padStart(2, '0')} Th${(d.getMonth() + 1).toString().padStart(2, '0')}, ${d.getFullYear()}`;
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
    };
  },
  // ===== 5. CÁC HÀM XỬ LÝ SỰ KIỆN & ĐIỀU HƯỚNG (METHODS) =====
  methods: {
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
    addToCart(prod) {
      this.cartStore.addItem(prod, 1);
      this.$toast.success("Thêm sản phẩm thành công");
    }
  }
};
</script>

<style scoped>
/* ==============================================================
   TrangChu: Tinh chỉnh cục bộ cho khối Tin tức & Kinh nghiệm
   Đồng bộ bảng màu Luxury (Deep Navy #0A1128 & Cool Slate) với các phần trên
   ============================================================== */
.news-category {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  color: var(--jpm-color-red-marine, #F82249); /* Chuẩn Đỏ Red Marine đồng bộ Tin Tức & Chi Tiết Tin Tức */
  font-weight: 700;
  font-size: 0.74rem;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  line-height: 1.3;
}

.news-hero-title {
  font-family: var(--heading-font, 'Raleway', sans-serif) !important;
  font-size: 1.35rem;
  color: var(--heading-color, #0A1128);
  font-weight: 700 !important;
  line-height: 1.35;
  letter-spacing: -0.2px;
}

.news-card-title {
  font-family: var(--heading-font, 'Raleway', sans-serif) !important;
  font-size: 1.05rem;
  color: var(--heading-color, #0A1128);
  font-weight: 700 !important;
  line-height: 1.38;
  margin-bottom: 0.25rem;
}

.news-hero-title a,
.news-card-title a {
  font-family: inherit;
  font-weight: inherit;
  color: var(--heading-color, #0A1128) !important;
  text-decoration: none;
}
.group-hover:hover .news-hero-title a,
.list-item-hover:hover .news-card-title a,
.news-hero-title a:hover,
.news-card-title a:hover {
  color: var(--heading-color, #0A1128) !important;
}

.news-summary {
  font-family: var(--default-font);
  color: #64748b; /* Cool Slate trong trẻo thay cho #6c757d xỉn màu */
  font-size: 0.85rem;
  font-weight: 400;
  line-height: 1.55;
}
.news-summary-lg {
  font-size: 0.9rem;
}

.news-meta {
  font-family: var(--default-font);
  color: #64748b; /* Xám Slate vừa vặn, sắc nét trên nền trắng */
  font-size: 0.8rem;
  font-weight: 500;
  line-height: 1.3;
  display: inline-flex;
  align-items: center;
}
.news-meta i {
  color: inherit;
  font-size: 0.82rem;
}

.section-more-link {
  color: var(--heading-color, #0A1128);
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: color 0.2s ease;
}
.section-more-link i {
  transition: transform 0.2s ease;
}
.section-more-link:hover {
  color: var(--accent-color, #f82249);
}
.section-more-link:hover i {
  transform: translateX(4px);
}

.news-img-thumb {
  width: 175px;
  aspect-ratio: 4/3;
  flex-shrink: 0;
  border-radius: 12px;
}

.skeleton-box-sm {
  border-radius: 12px !important;
  width: 175px;
  aspect-ratio: 4/3;
}

.skeleton-box-full {
  border-radius: var(--jpm-radius-card, 12px) !important;
  aspect-ratio: 16/10;
  width: 100%;
}

.video-modal-content {
  border-radius: var(--jpm-radius-modal, 12px) !important;
}

.video-ratio-box {
  border-radius: var(--jpm-radius-control, 8px);
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
  border-radius: 12px;
  aspect-ratio: 16/10;
}
.video-badge {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.6px;
  color: #ffffff;
  background-color: rgba(10, 17, 40, 0.78);
  border: 1px solid rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(6px);
  z-index: 2;
  border-radius: 6px;
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
.jpm-homepage .section:not(.hero) {
  padding: 64px 0;
}

.home-section-slate {
  background-color: #f8fafc;
}

.filter-console-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
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

.filter-console-card .filter-label {
  display: inline-flex;
  align-items: center;
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--heading-color, #0A1128);
  letter-spacing: 0.1px;
}

.filter-label-optional {
  font-weight: 400;
  color: #94a3b8;
  font-size: 0.78rem;
}

/* Search Input Box (Sneat-style Bold 2px Crisp Focus & Downward Soft Shadow, Zero Jitter) */
.search-input-box {
  display: flex;
  align-items: center;
  height: var(--jpm-control-height, 37px);
  border-radius: var(--jpm-radius-control, 7px);
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: none;
  padding: 0 13px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.search-input-box:focus-within {
  border-width: 1px !important;
  border-color: var(--accent-color, #f82249) !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: 0 !important;
}
.search-input-box:hover {
  border-color: #cbd5e1;
}
.search-input-icon {
  font-size: 1rem;
  color: #94a3b8;
  margin-right: 10px;
  flex-shrink: 0;
  transition: color 0.15s ease-in-out;
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
  font-weight: 500;
}
.search-input-field::placeholder {
  color: #64748b;
  font-weight: 500;
  transition: all 0.25s ease;
}
.search-input-field:focus::placeholder {
  transform: translate(5px);
  transition: all 0.25s ease;
}
.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0;
  margin-left: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: color 0.15s ease;
}
.btn-clear-search:hover {
  color: var(--accent-color, #f82249);
}

/* Đồng bộ chiều cao nút với ô input & JpmSelect trong bộ lọc (Màu sắc, bo góc, font lấy 100% từ .btn trong main.css) */
.filter-control-h {
  height: var(--jpm-control-height, 37px);
}

/* Bố cục hàng Từ Khóa Gợi Ý (Style .popular-tags-label & .quick-pill-tag dùng chung tại main.css) */
.popular-tags-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 18px;
  padding-top: 4px;
}
.popular-tags-list {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

@media (max-width: 991px) {
  .filter-console-card {
    padding: 24px 20px;
  }
}

/* ==============================================================
   Khối 4: Giới thiệu & Quy trình JPM Marine (Split + KPI + Card ảnh)
   ============================================================== */
.home-about-section {
  padding: 56px 0;
  border-top: 1px solid #e2e8f0;
  border-bottom: 1px solid #e2e8f0;
}

.about-wide-container {
  width: 100%;
  max-width: 1720px;
  padding-left: clamp(20px, 3vw, 48px) !important;
  padding-right: clamp(20px, 3vw, 48px) !important;
  margin: 0 auto;
}

.banner-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 1.8px;
  text-transform: uppercase;
  color: var(--jpm-color-red-marine, #F82249);
}

.banner-eyebrow::before {
  content: "";
  display: inline-block;
  width: 18px;
  height: 1.5px;
  background: var(--jpm-color-red-marine, #F82249);
}

.home-about-title {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: clamp(1.55rem, 2.2vw, 1.95rem);
  font-weight: 800;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: -0.02em;
  line-height: 1.25;
}

.banner-title-accent {
  color: var(--jpm-color-red-marine, #F82249);
}

.home-about-desc {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 0.96rem;
  color: #475569;
  line-height: 1.65;
  max-width: 560px;
}

.about-metrics-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px 16px;
  padding: 14px 0;
  border-top: 1px solid #e2e8f0;
  border-bottom: 1px solid #e2e8f0;
}

.about-metric-val {
  font-family: var(--jpm-font-mono, 'Roboto Mono', monospace);
  font-size: 1.45rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  line-height: 1.15;
  letter-spacing: -0.02em;
}

.about-metric-label {
  font-size: 0.78rem;
  color: #64748b;
  margin-top: 3px;
  line-height: 1.3;
}

.btn-about-outline {
  height: var(--jpm-control-height, 37px);
  padding: 0 18px;
  border-radius: 50px !important;
  border: 1px solid #cbd5e1 !important;
  background-color: #ffffff !important;
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  font-size: 0.92rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  box-shadow: none !important;
  transition: all 0.2s ease;
}

.btn-about-outline:hover {
  border-color: var(--jpm-color-navy-dark, #0A1128) !important;
  background-color: var(--jpm-color-navy-dark, #0A1128) !important;
  color: #ffffff !important;
}

.btn-about-link {
  height: var(--jpm-control-height, 37px);
  padding: 0 14px;
  font-size: 0.92rem;
  font-weight: 600;
  color: #475569 !important;
  display: inline-flex;
  align-items: center;
  box-shadow: none !important;
}

.btn-about-link:hover {
  color: var(--jpm-color-red-marine, #F82249) !important;
}

/* Card khung chờ ảnh (Tỉ lệ ngang cân đối, thay <img> là khớp ngay) */
.about-media-card {
  width: 100%;
  aspect-ratio: 16 / 9;
  min-height: 270px;
  max-height: 400px;
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid #dbe4ef;
  background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 55%, #e6eef7 100%);
  box-shadow: 0 12px 32px -12px rgba(10, 17, 40, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.about-media-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 28px 24px;
  max-width: 460px;
}

.about-media-badge {
  display: inline-block;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 1.4px;
  color: var(--jpm-color-blue-electric, #0284C7);
  background: rgba(2, 132, 199, 0.1);
  border: 1px solid rgba(2, 132, 199, 0.2);
  padding: 4px 10px;
  border-radius: 50px;
  margin-bottom: 14px;
}

.about-media-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  background: #ffffff;
  border: 1px dashed #cbd5e1;
  color: #64748b;
  font-size: 1.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}

.about-media-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 4px;
}

.about-media-hint {
  font-size: 0.82rem;
  color: #64748b;
}

/* Tầng dưới: Quy trình 01 -> 04 */
.about-process-wrapper {
  border-top: 1px solid #e2e8f0;
}

.about-process-heading {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.about-process-sub {
  font-size: 0.85rem;
  color: #64748b;
  font-weight: 500;
}

.about-step-card {
  height: 100%;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 10px);
  padding: 16px 18px;
}

.about-step-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.about-step-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: var(--jpm-color-navy-dark, #0A1128);
  font-family: var(--jpm-font-mono, 'Roboto Mono', monospace);
  font-size: 0.78rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.about-step-arrow {
  color: #94a3b8;
  font-size: 0.95rem;
}

.about-step-title {
  font-size: 0.96rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 5px;
}

.about-step-desc {
  font-size: 0.86rem;
  color: #64748b;
  line-height: 1.5;
  margin-bottom: 0;
}

@media (max-width: 991.98px) {
  .home-about-section {
    padding: 40px 0;
  }
  .about-media-card {
    min-height: 220px;
  }
}

@media (max-width: 575.98px) {
  .about-metrics-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
