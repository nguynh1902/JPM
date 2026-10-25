<template>
  <main class="jpm-news-detail main-page">
    
    <!-- SKELETON LOADING -->
    <div v-if="isFetching" class="container py-5" style="max-width: 1120px;">
      <div class="skeleton-line mb-4" style="width: 120px; height: 16px;"></div>
      <div class="skeleton-line mb-2" style="width: 140px; height: 14px;"></div>
      <div class="skeleton-line mb-3" style="width: 75%; height: 42px;"></div>
      <div class="skeleton-line mb-4" style="width: 60%; height: 18px;"></div>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="skeleton-line" style="width: 180px; height: 36px;"></div>
        <div class="skeleton-line" style="width: 100px; height: 16px;"></div>
      </div>
      <div class="skeleton-box mb-5" style="height: 440px; border-radius: 16px;"></div>
      <div class="row g-5">
        <div class="col-lg-8">
          <div class="skeleton-line mb-3" style="width: 100%; height: 18px;"></div>
          <div class="skeleton-line mb-3" style="width: 95%; height: 18px;"></div>
          <div class="skeleton-line mb-3" style="width: 90%; height: 18px;"></div>
        </div>
        <div class="col-lg-4">
          <div class="skeleton-box" style="height: 280px; border-radius: 16px;"></div>
        </div>
      </div>
    </div>

    <!-- ERROR STATE -->
    <div v-else-if="error" class="container py-5 text-center" style="max-width: 1120px;">
      <i class="bi bi-exclamation-triangle text-danger display-1 mb-3"></i>
      <h2 class="fw-bold">Không tìm thấy bài viết</h2>
      <p class="text-secondary mb-4">Bài viết này không tồn tại hoặc đã bị xóa.</p>
      <router-link to="/tin-tuc" class="btn btn-dark rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
      </router-link>
    </div>

    <!-- ARTICLE CONTENT (WIREFRAME LAYOUT) -->
    <article v-else-if="article" class="article-wrapper py-4 py-lg-5">
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
              class="article-category-label text-decoration-none"
            >
              {{ article.category }}
            </router-link>
          </div>

          <!-- Main Title (H1) -->
          <h1 class="article-main-title fw-bold text-dark mb-3">
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
                <div class="fw-bold text-dark" style="font-size: 0.9rem; line-height: 1.25;">
                  {{ article.author || 'Kỹ sư Trưởng JPM' }}
                </div>
                <div class="text-muted" style="font-size: 0.78rem;">
                  {{ getAuthorRole(article.author, article.category) }}
                </div>
              </div>
            </div>

            <div class="text-secondary d-flex align-items-center gap-3" style="font-size: 0.85rem;">
              <span>{{ formatDate(article.created_at) }}</span>
              <span class="text-muted opacity-50">•</span>
              <span><i class="bi bi-eye me-1"></i>{{ Number(article.views || 0).toLocaleString('vi-VN') }} lượt xem</span>
            </div>
          </div>
        </header>

        <!-- 3. HERO FEATURED IMAGE -->
        <div class="article-hero-img rounded-4 overflow-hidden mb-5">
          <img
            :src="getImageUrl(article.thumbnail) || fallback"
            :alt="article.title"
            class="w-100 h-100 object-fit-cover"
            @error="$event.target.src = fallback"
          >
        </div>

        <!-- 4. TWO-COLUMN BODY: LEFT ARTICLE CONTENT + RIGHT SIDEBAR -->
        <div class="row g-5 align-items-start">
          
          <!-- LEFT COLUMN: MAIN ARTICLE CONTENT -->
          <div class="col-lg-8">
            <div
              class="article-html-content"
              v-html="processedHtmlContent"
            ></div>

            <!-- YouTube Video (nếu bài viết có đính kèm video) -->
            <div v-if="article.youtube_url && getYoutubeEmbedUrl(article.youtube_url)" class="mt-5 rounded-4 overflow-hidden shadow-sm">
              <iframe
                width="100%"
                height="420"
                :src="getYoutubeEmbedUrl(article.youtube_url)"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>

            <!-- Thư viện ảnh phụ (Gallery Lightbox) -->
            <div v-if="galleryImages && galleryImages.length > 0" class="mt-5 pt-3 border-top">
              <h4 class="fw-bold text-dark mb-3" style="font-size: 1.15rem;">Hình ảnh thực tế đính kèm</h4>
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

            <!-- Từ khóa / Tags -->
            <div class="mt-5 pt-4 border-top d-flex flex-wrap gap-2 align-items-center">
              <span class="text-muted small fw-semibold me-1">Chủ đề:</span>
              <router-link
                v-if="article.category"
                :to="{ path: '/tin-tuc', query: { category: article.category } }"
                class="tag-pill text-decoration-none"
              >
                {{ article.category }}
              </router-link>
              <router-link :to="{ path: '/tin-tuc', query: { search: 'Động cơ' } }" class="tag-pill text-decoration-none">
                Động cơ thủy
              </router-link>
              <router-link :to="{ path: '/tin-tuc', query: { category: 'Bảo Dưỡng Động Cơ' } }" class="tag-pill text-decoration-none">
                Bảo dưỡng định kỳ
              </router-link>
              <router-link to="/tra-cuu" class="tag-pill text-decoration-none">
                Phụ tùng OEM JPM
              </router-link>
            </div>
          </div>

          <!-- RIGHT COLUMN: STICKY SIDEBAR (TOC + SHARE + CTA CARD) -->
          <aside class="col-lg-4">
            <div class="sidebar-sticky">
              
              <!-- Table of Contents -->
              <div class="toc-widget mb-4 pb-2">
                <div class="sidebar-label fw-bold text-dark mb-3">
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
                <div class="sidebar-label fw-bold text-dark mb-3">
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

              <!-- Promotional CTA Box ("Smarter Care, Happier Patients" equivalent) -->
              <div class="promo-cta-card rounded-4 p-4">
                <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; line-height: 1.35;">
                  Vận Hành Bền Bỉ, An Tâm Vươn Khơi
                </h4>
                <p class="text-secondary mb-4" style="font-size: 0.84rem; line-height: 1.6;">
                  Tra cứu sơ đồ bóc tách (Exploded View) và đặt mua phụ tùng chính hãng Yamaha, Yanmar, Honda Marine nhanh chóng với độ chính xác tuyệt đối từ JPM.
                </p>
                <router-link
                  to="/tra-cuu"
                  class="btn btn-dark w-100 rounded-2 py-2 fw-semibold"
                  style="font-size: 0.85rem;"
                >
                  Khám phá ngay
                </router-link>
              </div>

            </div>
          </aside>

        </div>

        <!-- 5. RELATED BLOGS SECTION -->
        <div v-if="relatedArticles && relatedArticles.length > 0" class="related-blogs-section mt-5 pt-5 border-top bg-white">
          <h2 class="fw-bold text-dark mb-4" style="font-size: 1.65rem; letter-spacing: -0.4px;">
            Bài viết liên quan
          </h2>

          <div class="row g-4">
            <div
              class="col-12 col-md-4"
              v-for="rel in relatedArticles"
              :key="rel.id"
            >
              <article class="related-blog-card h-100 d-flex flex-column">
                <!-- Thumbnail -->
                <router-link :to="`/tin-tuc/${rel.id}`" class="related-thumb-wrap d-block overflow-hidden rounded-3 mb-3">
                  <img
                    :src="getImageUrl(rel.thumbnail) || fallback"
                    :alt="rel.title"
                    class="w-100 h-100 object-fit-cover hover-zoom"
                    @error="$event.target.src = fallback"
                  >
                </router-link>

                <!-- Category -->
                <div class="mb-1">
                  <span class="related-category text-secondary">
                    {{ rel.category }}
                  </span>
                </div>

                <!-- Title -->
                <h3 class="related-title fw-bold mb-2">
                  <router-link :to="`/tin-tuc/${rel.id}`" class="text-dark text-decoration-none hover-text-danger">
                    {{ rel.title }}
                  </router-link>
                </h3>

                <!-- Excerpt -->
                <p class="related-excerpt text-secondary mb-3 flex-grow-1">
                  {{ rel.summary || 'Tìm hiểu quy trình kỹ thuật và kinh nghiệm bảo dưỡng thiết bị hàng hải chuyên sâu từ đội ngũ kỹ sư JPM Marine.' }}
                </p>

                <!-- Author & Date Footer -->
                <div class="related-meta d-flex align-items-center gap-2 pt-2 mt-auto">
                  <img
                    :src="getAuthorAvatar(rel.author)"
                    :alt="rel.author || 'Chuyên gia JPM'"
                    class="rounded-circle object-fit-cover"
                    style="width: 24px; height: 24px;"
                  >
                  <span class="fw-medium text-dark text-truncate" style="font-size: 0.76rem; max-width: 110px;">
                    {{ rel.author || 'Kỹ sư JPM' }}
                  </span>
                  <span class="text-muted opacity-50" style="font-size: 0.75rem;">|</span>
                  <span class="text-muted" style="font-size: 0.75rem;">
                    {{ formatDate(rel.created_at) }}
                  </span>
                </div>
              </article>
            </div>
          </div>
        </div>

      </div>
    </article>

  </main>
</template>

<script>
import { ref, computed, watch } from 'vue';
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

    const { data: fetchResult, isFetching, error, refetch } = useQuery({
      queryKey: ['public-news-detail-v3', articleId],
      queryFn: () => axiosClient.get(`/public/tin-tuc/${articleId.value}`),
      staleTime: 1000 * 60 * 5,
    });

    watch(articleId, () => {
      activeTocId.value = '';
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

      // Nếu nội dung bài viết trong DB chưa có thẻ h2/h3 (chỉ có 1 đoạn ngắn từ seeder),
      // tự động bổ sung bố cục bài viết chuyên sâu để khớp với giao diện mẫu
      if (!/<h[23]\b/i.test(raw)) {
        raw = buildStructuredArticleHtml(article.value, raw);
      }

      const toc = [];
      // Thêm mục mở đầu vào đầu Table of Contents giống ảnh thiết kế
      toc.push({
        id: 'toc-intro',
        text: 'Mở đầu: Khoảng cách giữa thực tế & tiêu chuẩn kỹ thuật',
      });

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
        html: `<div id="toc-intro"></div>${htmlWithIds}`,
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

    const getYoutubeEmbedUrl = (url) => {
      if (!url) return null;
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&]{11})/);
      return match ? 'https://www.youtube.com/embed/' + match[1] : null;
    };

    const galleryImages = computed(() => {
      if (!article.value || !article.value.gallery) return [];
      try {
        const arr = typeof article.value.gallery === 'string' ? JSON.parse(article.value.gallery) : article.value.gallery;
        return Array.isArray(arr) ? arr : [];
      } catch (e) {
        return [];
      }
    });

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
      getYoutubeEmbedUrl,
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
/* ── PAGE BACKGROUND & BASE ── */
.jpm-news-detail {
  background-color: #ffffff;
  color: #111827;
}

/* ── TOP BACK LINK ── */
.back-link {
  color: #4b5563;
  font-size: 0.85rem;
  font-weight: 500;
  transition: color 0.2s ease;
}
.back-link:hover {
  color: #111827;
}

/* ── ARTICLE HEADER ── */
.article-category-label {
  color: #374151;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.2px;
  transition: color 0.2s ease;
}
.article-category-label:hover {
  color: #f82249;
}

.article-main-title {
  font-family: var(--heading-font);
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

/* ── HERO IMAGE ── */
.article-hero-img {
  aspect-ratio: 16 / 8.5;
  background-color: #e5e7eb;
  box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
}
@media (max-width: 768px) {
  .article-hero-img {
    aspect-ratio: 16 / 10;
  }
}

/* ── ARTICLE BODY TYPOGRAPHY ── */
.article-html-content {
  font-size: 0.98rem;
  line-height: 1.8;
  color: #374151;
}

.article-html-content :deep(p) {
  margin-bottom: 1.25rem;
}

.article-html-content :deep(h2) {
  color: #111827;
  font-size: 1.38rem;
  font-weight: 700;
  letter-spacing: -0.3px;
  margin-top: 2.25rem;
  margin-bottom: 1rem;
  scroll-margin-top: 100px;
}

.article-html-content :deep(h3) {
  color: #111827;
  font-size: 1.05rem;
  font-weight: 700;
  margin-top: 1.75rem;
  margin-bottom: 0.55rem;
  scroll-margin-top: 100px;
}

.article-html-content :deep(.article-example) {
  color: #4b5563;
  font-size: 0.94rem;
  background: rgba(17, 24, 39, 0.03);
  border-left: 3px solid #111827;
  padding: 0.7rem 1rem;
  border-radius: 0 8px 8px 0;
  margin-bottom: 1.5rem;
}

.article-html-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
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

/* ── TAG PILLS ── */
.tag-pill {
  font-size: 0.78rem;
  color: #374151;
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  transition: all 0.2s ease;
}
.tag-pill:hover {
  border-color: #111827;
  color: #111827;
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
  border-left: 2px solid #e5e7eb;
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
  background-color: #111827;
}

.toc-link {
  display: block;
  font-size: 0.82rem;
  line-height: 1.45;
  color: #6b7280;
  transition: color 0.2s ease;
}

.toc-item.active .toc-link,
.toc-link:hover {
  color: #111827;
  font-weight: 600;
}

/* Social Share Icons */
.social-icon-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: none;
  background-color: #1f2937;
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.82rem;
  transition: all 0.2s ease;
}
.social-icon-btn:hover {
  background-color: #f82249;
  transform: translateY(-2px);
}

/* Promo CTA Card */
.promo-cta-card {
  background-color: #e5e7eb;
  border: 1px solid rgba(17, 24, 39, 0.06);
}

/* ── RELATED BLOGS SECTION ── */
.related-blogs-section {
  background-color: #ffffff !important;
}

.related-blog-card {
  padding: 16px;
  border-radius: 16px;
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  transition: all 0.25s ease;
}

.related-blog-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
  transform: translateY(-3px);
}

.related-thumb-wrap {
  aspect-ratio: 16 / 10;
  background-color: #e5e7eb;
}

.related-category {
  font-size: 0.74rem;
  font-weight: 600;
}

.related-title {
  font-size: 1rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.related-excerpt {
  font-size: 0.82rem;
  line-height: 1.55;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── HOVER UTILITIES ── */
.hover-zoom {
  transition: transform 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.related-blog-card:hover .hover-zoom,
.gallery-thumb:hover .hover-zoom {
  transform: scale(1.05);
}

.hover-text-danger {
  transition: color 0.2s ease;
}
.hover-text-danger:hover {
  color: #f82249 !important;
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
