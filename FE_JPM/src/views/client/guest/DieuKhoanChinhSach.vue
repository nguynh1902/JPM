<template>
  <div class="policy-page bg-white">

    <!-- HERO BANNER ĐỒNG BỘ 100% VỚI CỬA HÀNG, TRA CỨU, TIN TỨC, HỖ TRỢ -->
    <div
      class="store-hero-banner position-relative overflow-hidden"
      :class="{ 'banner-no-img': !currentBannerImg }"
      :style="bannerStyle"
    >
      <div class="store-banner-overlay"></div>

      <div v-if="!currentBannerImg" class="banner-placeholder-pattern">
        <i class="bi bi-file-earmark-text"></i>
      </div>

      <div class="container h-100 position-relative z-2">
        <div class="row h-100 align-items-center">
          <div class="col-12 col-md-9 col-lg-7 store-banner-content py-4 py-lg-0">
            <div class="d-inline-flex align-items-center gap-2 store-banner-badge mb-2">
              <span class="badge-dot"></span>
              <span>JPM MARINE LEGAL</span>
              <span class="badge-divider">•</span>
              <span class="badge-sub">ĐIỀU KHOẢN &amp; CHÍNH SÁCH</span>
            </div>

            <h1 class="store-banner-title mb-2">
              Điều khoản <span class="text-danger">&amp; chính sách</span>
            </h1>

            <p class="store-banner-subtitle mb-0">
              Quy định sử dụng hệ thống, quy trình báo giá 72 giờ, vận chuyển, bảo hành phụ tùng OEM và cam kết bảo mật thông tin tại {{ companyName }}.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- NỘI DUNG CHÍNH: BỐ CỤC EDITORIAL 2 CỘT ĐỒNG BỘ CHITIETTINTUC.VUE & HOTRO.VUE -->
    <section class="section py-5 bg-white">
      <div class="container">
        <div class="row gx-lg-5 gy-5 align-items-start">

          <!-- CỘT TRÁI: VĂN BẢN ĐIỀU KHOẢN & CHÍNH SÁCH -->
          <div class="col-lg-8">

            <!-- Ghi chú phạm vi áp dụng -->
            <div class="policy-lead-note mb-5">
              Bằng việc đăng ký tài khoản, tra cứu sơ đồ phụ tùng hoặc gửi yêu cầu báo giá tại
              <strong>{{ companyName }}</strong>, Quý khách xác nhận đã đọc, hiểu và đồng ý tuân thủ
              các điều khoản sử dụng và chính sách vận hành dưới đây.
            </div>

            <!-- MỤC 01 -->
            <article id="dieu-khoan-su-dung" class="policy-section pb-4 mb-5 border-bottom">
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="policy-overline-line"></span>
                <span class="policy-overline-text">MỤC 01</span>
              </div>
              <h2 class="policy-heading mb-3">Điều khoản sử dụng &amp; tài khoản khách hàng</h2>
              <p class="policy-text">
                Hệ thống {{ companyName }} cung cấp nền tảng tra cứu catalog phụ tùng động cơ thủy theo mã OEM,
                sơ đồ rã máy (Exploded Diagram) và tiếp nhận yêu cầu báo giá, đặt hàng trực tuyến dành cho khách hàng
                cá nhân, thợ máy, chủ tàu và xưởng dịch vụ (B2B).
              </p>
              <ul class="policy-list mb-0">
                <li>
                  Khách hàng cam kết cung cấp chính xác thông tin liên hệ (họ tên, số điện thoại Zalo, địa chỉ bến tàu
                  hoặc chành xe nhận hàng) để kỹ thuật viên đối soát và giao nhận kịp thời.
                </li>
                <li>
                  Chủ tài khoản tự chịu trách nhiệm bảo mật thông tin đăng nhập, lịch sử đơn hàng và danh sách động cơ
                  đã lưu trong <strong>Garage của tôi</strong>.
                </li>
                <li>
                  Nghiêm cấm các hành vi sao chép dữ liệu catalog hàng loạt, gửi yêu cầu giả mạo hoặc can thiệp trái phép
                  vào hệ thống vận hành của {{ companyName }}.
                </li>
              </ul>
            </article>

            <!-- MỤC 02 -->
            <article id="bao-gia-dat-coc" class="policy-section pb-4 mb-5 border-bottom">
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="policy-overline-line"></span>
                <span class="policy-overline-text">MỤC 02</span>
              </div>
              <h2 class="policy-heading mb-3">Quy trình báo giá, đặt cọc &amp; chốt đơn</h2>
              <p class="policy-text">
                Do đặc thù phụ tùng động cơ thủy chính hãng nhập khẩu từ Nhật Bản chịu biến động tỷ giá JPY và cước vận
                chuyển quốc tế, mọi yêu cầu báo giá và đơn hàng được xử lý theo quy chuẩn minh bạch:
              </p>
              <ul class="policy-list mb-0">
                <li>
                  <strong>Hiệu lực báo giá 72 giờ:</strong> Mỗi bảng báo giá chính thức có hiệu lực bảo lưu đơn giá trong
                  <strong>72 giờ</strong> kể từ thời điểm kỹ thuật viên phát hành. Sau thời hạn này, khách hàng có thể bấm
                  <em>Yêu cầu báo giá lại</em> để cập nhật đơn giá mới nhất.
                </li>
                <li>
                  <strong>Đặt cọc giữ hàng / gom đơn Nhật:</strong> Đối với phụ tùng đặt hãng hoặc đơn hàng giá trị lớn,
                  khách hàng chuyển khoản đặt cọc qua mã <strong>VietQR Napas 24/7</strong> hiển thị trên hệ thống kèm đúng
                  nội dung mã đơn.
                </li>
                <li>
                  <strong>Thanh toán phần còn lại:</strong> Số tiền còn lại (COD) được đối soát khi giao hàng tại kho,
                  bến tàu hoặc trước khi bàn giao chành xe liên tỉnh theo thỏa thuận trên đơn.
                </li>
              </ul>
            </article>

            <!-- MỤC 03 -->
            <article id="van-chuyen-giao-nhan" class="policy-section pb-4 mb-5 border-bottom">
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="policy-overline-line"></span>
                <span class="policy-overline-text">MỤC 03</span>
              </div>
              <h2 class="policy-heading mb-3">Chính sách vận chuyển &amp; giao nhận</h2>
              <ul class="policy-list mb-0">
                <li>
                  <strong>Hình thức giao hàng:</strong> Hỗ trợ giao hỏa tốc nội thành, gửi chành xe liên tỉnh theo nhà xe
                  quen thuộc của khách, hoặc chuyển phát nhanh ViettelPost toàn quốc.
                </li>
                <li>
                  <strong>Cước vận chuyển:</strong> Được thông báo minh bạch trên bảng báo giá trước khi chốt đơn
                  (hoặc ghi rõ <em>Khách tự trả cước chành xe</em> khi khách chỉ định nhà xe riêng).
                </li>
                <li>
                  <strong>Kiểm tra khi nhận hàng:</strong> Quý khách vui lòng kiểm tra ngoại quan kiện hàng và đối chiếu
                  <strong>mã OEM in trên tem hãng</strong> ngay khi nhận. Nếu phát sinh sai mã hoặc hư hại do vận chuyển,
                  vui lòng phản hồi cho kỹ thuật viên {{ companyName }} trong vòng <strong>24 giờ</strong>.
                </li>
              </ul>
            </article>

            <!-- MỤC 04 -->
            <article id="bao-hanh-doi-tra" class="policy-section pb-4 mb-5 border-bottom">
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="policy-overline-line"></span>
                <span class="policy-overline-text">MỤC 04</span>
              </div>
              <h2 class="policy-heading mb-3">Chính sách bảo hành &amp; đổi trả phụ tùng OEM</h2>
              <ul class="policy-list mb-0">
                <li>
                  <strong>Cam kết chính hãng:</strong> 100% phụ tùng cung cấp đạt chuẩn OEM mới nguyên hộp từ hãng
                  (Yamaha, Honda, Suzuki, Mercury, Yanmar, Tohatsu...), cung cấp chứng từ xuất xứ khi doanh nghiệp yêu cầu.
                </li>
                <li>
                  <strong>Điều kiện đổi trả trong 07 ngày:</strong> Áp dụng khi sản phẩm giao sai mã so với báo giá đã chốt
                  hoặc có lỗi kỹ thuật từ nhà sản xuất; phụ tùng phải còn nguyên tem nhãn, bao bì, chưa lắp đặt và chưa dính dầu nhớt.
                </li>
                <li>
                  <strong>Trường hợp không hỗ trợ đổi trả:</strong> Phụ tùng đặt nhập riêng từ Nhật Bản theo đúng mã OEM /
                  số khung động cơ khách đã xác nhận trên báo giá, hoặc sản phẩm hư hại do lắp đặt sai kỹ thuật.
                </li>
              </ul>
            </article>

            <!-- MỤC 05 -->
            <article id="bao-mat-thong-tin" class="policy-section pb-4 mb-5 border-bottom">
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="policy-overline-line"></span>
                <span class="policy-overline-text">MỤC 05</span>
              </div>
              <h2 class="policy-heading mb-3">Chính sách bảo mật thông tin khách hàng</h2>
              <p class="policy-text">
                {{ companyName }} tôn trọng và bảo vệ tuyệt đối quyền riêng tư của mọi khách hàng và đối tác:
              </p>
              <ul class="policy-list mb-0">
                <li>
                  <strong>Mục đích sử dụng dữ liệu:</strong> Thông tin tài khoản, địa chỉ giao hàng và hồ sơ động cơ trong
                  Garage chỉ phục vụ việc tra cứu mã phụ tùng tương thích, lập báo giá, giao nhận và chăm sóc bảo hành.
                </li>
                <li>
                  <strong>Cam kết bảo mật:</strong> Tuyệt đối không bán, cho thuê hoặc chia sẻ thông tin cá nhân cho bên thứ ba,
                  ngoại trừ đơn vị vận chuyển / chành xe nhằm phục vụ giao nhận đúng đơn hàng.
                </li>
                <li>
                  <strong>Quyền kiểm soát dữ liệu:</strong> Khách hàng có thể chủ động cập nhật thông tin cá nhân, địa chỉ giao hàng,
                  mật khẩu tại trang <em>Tài khoản</em> hoặc liên hệ bộ phận hỗ trợ để yêu cầu khóa / xóa dữ liệu tài khoản.
                </li>
              </ul>
            </article>

            <!-- THÔNG TIN ĐƠN VỊ VẬN HÀNH (ĐỒNG BỘ LƯỚI THÔNG TIN CỦA HOTRO.VUE) -->
            <div class="policy-contact-block">
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="policy-overline-line"></span>
                <span class="policy-overline-text">THÔNG TIN LIÊN HỆ</span>
              </div>
              <h2 class="policy-heading mb-4">Đơn vị vận hành &amp; tiếp nhận phản hồi</h2>

              <div class="row g-4">
                <div class="col-sm-6">
                  <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-geo-alt policy-contact-icon flex-shrink-0"></i>
                    <div>
                      <div class="policy-contact-title">Trụ Sở / Showroom</div>
                      <div class="policy-contact-desc">{{ settings.company_address || 'Đang cập nhật địa chỉ' }}</div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-telephone policy-contact-icon flex-shrink-0"></i>
                    <div>
                      <div class="policy-contact-title">Hotline Kỹ Thuật &amp; Mua Hàng</div>
                      <a
                        v-if="settings.company_hotline"
                        :href="'tel:' + String(settings.company_hotline).replace(/\s+/g, '')"
                        class="policy-contact-desc text-decoration-none font-monospace"
                      >
                        {{ settings.company_hotline }}
                      </a>
                      <div v-else class="policy-contact-desc">Đang cập nhật</div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-envelope-open policy-contact-icon flex-shrink-0"></i>
                    <div>
                      <div class="policy-contact-title">Hòm Thư Hỗ Trợ</div>
                      <a
                        :href="'mailto:' + (settings.company_email || 'contact@jpm-marine.vn')"
                        class="policy-contact-desc text-decoration-none"
                      >
                        {{ settings.company_email || 'contact@jpm-marine.vn' }}
                      </a>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-clock policy-contact-icon flex-shrink-0"></i>
                    <div>
                      <div class="policy-contact-title">Thời Gian Làm Việc</div>
                      <div class="policy-contact-desc">{{ settings.business_hours || '08:00 - 17:30 (Thứ 2 - Thứ 7)' }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- CỘT PHẢI: STICKY SIDEBAR (ĐỒNG BỘ 100% CHITIETTINTUC.VUE) -->
          <aside class="col-lg-4 no-print">
            <div class="policy-sidebar-sticky">

              <!-- Mục lục đường kẻ trái giống ChiTietTinTuc.vue -->
              <div class="mb-4 pb-2">
                <div class="sidebar-label fw-bold mb-3">Mục lục nội dung</div>
                <ul class="toc-list list-unstyled m-0">
                  <li
                    v-for="sec in sections"
                    :key="sec.id"
                    class="toc-item"
                    :class="{ active: activeSection === sec.id }"
                  >
                    <a
                      :href="'#' + sec.id"
                      class="toc-link text-decoration-none"
                      @click.prevent="scrollToSection(sec.id)"
                    >
                      {{ sec.num }}. {{ sec.shortTitle }}
                    </a>
                  </li>
                </ul>
              </div>

              <!-- Thẻ hỗ trợ nhanh đồng bộ .promo-cta-card của ChiTietTinTuc.vue -->
              <div class="promo-cta-card p-4">
                <h4 class="promo-cta-title fw-bold mb-2">
                  Cần Giải Đáp Chính Sách?
                </h4>
                <p class="promo-cta-desc mb-4">
                  Đội ngũ kỹ thuật viên {{ companyName }} luôn sẵn sàng tư vấn quy trình đặt hàng OEM, vận chuyển chành xe và bảo hành.
                </p>
                <div class="d-grid gap-2">
                  <a
                    :href="zaloUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-danger fw-semibold py-2"
                  >
                    <i class="bi bi-chat-dots me-2"></i>Nhắn Zalo kỹ thuật
                  </a>
                  <router-link
                    to="/ho-tro"
                    class="btn btn-outline-secondary fw-semibold py-2"
                  >
                    Trung tâm hỗ trợ &amp; FAQ
                  </router-link>
                </div>

                <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between">
                  <span class="text-muted" style="font-size: 0.78rem;">Cập nhật: 2026</span>
                  <button type="button" class="btn-print-link" @click="printPolicy">
                    <i class="bi bi-printer me-1"></i>In tài liệu
                  </button>
                </div>
              </div>

            </div>
          </aside>

        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useSiteSettings } from '../../../composables/useSiteSettings'

const { settings } = useSiteSettings()

const sections = [
  { id: 'dieu-khoan-su-dung', num: '01', shortTitle: 'Điều khoản sử dụng & tài khoản' },
  { id: 'bao-gia-dat-coc', num: '02', shortTitle: 'Quy trình báo giá & đặt cọc' },
  { id: 'van-chuyen-giao-nhan', num: '03', shortTitle: 'Vận chuyển & giao nhận' },
  { id: 'bao-hanh-doi-tra', num: '04', shortTitle: 'Bảo hành & đổi trả OEM' },
  { id: 'bao-mat-thong-tin', num: '05', shortTitle: 'Bảo mật thông tin' },
]

const activeSection = ref(sections[0].id)

const companyName = computed(() => settings.value?.company_name || 'JPM Marine')

const getBannerImageUrl = (path) => {
  if (!path) return ''
  if (typeof path === 'string') {
    if (
      path.startsWith('http') ||
      path.startsWith('blob:') ||
      path.startsWith('data:') ||
      path.startsWith('/client/') ||
      path.startsWith('/assets/')
    ) {
      return path
    }
  }
  const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/api\/?$/, '')
  const clean = path.startsWith('/') ? path : '/' + path
  return `${baseUrl}${clean}`
}

const currentBannerImg = computed(() => {
  if (settings.value?.support_banner_image) {
    return getBannerImageUrl(settings.value.support_banner_image)
  }
  return ''
})

const bannerStyle = computed(() => {
  if (!currentBannerImg.value) return {}
  return { backgroundImage: `url('${currentBannerImg.value}')` }
})

const zaloUrl = computed(() => {
  const configured = (settings.value?.social_zalo || '').trim()
  if (configured) return configured
  const digits = String(settings.value?.company_hotline || '').replace(/[^0-9]/g, '')
  return digits ? `https://zalo.me/${digits}` : '/ho-tro'
})

const printPolicy = () => {
  window.print()
}

const scrollToSection = (id) => {
  activeSection.value = id
  const el = document.getElementById(id)
  if (el) {
    const top = el.getBoundingClientRect().top + window.scrollY - 96
    window.scrollTo({ top, behavior: 'smooth' })
  }
}

const handleScroll = () => {
  const offset = 150
  for (let i = sections.length - 1; i >= 0; i--) {
    const el = document.getElementById(sections[i].id)
    if (el && el.getBoundingClientRect().top <= offset) {
      activeSection.value = sections[i].id
      break
    }
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.policy-page {
  min-height: 100vh;
}

/* ── OVERLINE & HEADINGS (ĐỒNG BỘ 100% HOTRO.VUE) ── */
.policy-overline-line {
  display: inline-block;
  width: 36px;
  height: 2px;
  background-color: var(--jpm-color-red-marine, #F82249);
}

.policy-overline-text {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: var(--jpm-color-red-marine, #F82249);
}

.policy-heading {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: clamp(1.2rem, 1.7vw, 1.42rem);
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: -0.3px;
  line-height: 1.35;
}

.policy-section {
  scroll-margin-top: 96px;
}

/* ── GHI CHÚ ĐẦU TRANG (ĐỒNG BỘ .article-example CỦA CHITIETTINTUC.VUE) ── */
.policy-lead-note {
  color: #475569;
  font-size: 0.94rem;
  line-height: 1.7;
  background: rgba(10, 17, 40, 0.03);
  border-left: 3px solid var(--jpm-color-navy-dark, #0A1128);
  padding: 0.85rem 1.15rem;
  border-radius: 0 var(--jpm-radius-control, 8px) var(--jpm-radius-control, 8px) 0;
}

/* ── BODY TYPOGRAPHY (ĐỒNG BỘ CHITIETTINTUC.VUE) ── */
.policy-text {
  font-size: 0.95rem;
  line-height: 1.8;
  color: #334155;
  margin-bottom: 1rem;
}

.policy-list {
  padding-left: 1.25rem;
  font-size: 0.95rem;
  line-height: 1.8;
  color: #334155;
}

.policy-list li {
  margin-bottom: 0.65rem;
}

.policy-list li:last-child {
  margin-bottom: 0;
}

.policy-list strong {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 600;
}

/* ── LƯỚI LIÊN HỆ CUỐI TRANG (ĐỒNG BỘ HOTRO.VUE) ── */
.policy-contact-icon {
  font-size: 1.45rem;
  color: var(--jpm-color-red-marine, #F82249);
  line-height: 1.1;
  margin-top: 2px;
}

.policy-contact-title {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 2px;
}

.policy-contact-desc {
  font-size: 0.88rem;
  line-height: 1.55;
  color: #64748b;
}

/* ── RIGHT STICKY SIDEBAR (ĐỒNG BỘ 100% CHITIETTINTUC.VUE) ── */
.policy-sidebar-sticky {
  position: sticky;
  top: 96px;
}

.sidebar-label {
  font-size: 0.84rem;
  color: var(--jpm-color-navy-dark, #0A1128);
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

.toc-item:last-child {
  margin-bottom: 0;
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
  background-color: var(--jpm-color-red-marine, #F82249);
}

.toc-link {
  display: block;
  font-size: 0.86rem;
  line-height: 1.45;
  color: #64748b;
  transition: color 0.2s ease;
}

.toc-item.active .toc-link,
.toc-link:hover {
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
}

.promo-cta-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
}

.promo-cta-title {
  font-size: 1.12rem;
  line-height: 1.35;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.promo-cta-desc {
  font-size: 0.85rem;
  line-height: 1.6;
  color: #475569;
}

.btn-print-link {
  border: none;
  background: transparent;
  padding: 0;
  font-size: 0.8rem;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: color 0.2s ease;
}

.btn-print-link:hover {
  color: var(--jpm-color-navy-dark, #0A1128);
}

@media print {
  .no-print,
  .store-hero-banner .banner-placeholder-pattern {
    display: none !important;
  }

  .store-hero-banner {
    height: auto !important;
    min-height: 0 !important;
    padding: 0 0 16px !important;
    background: #ffffff !important;
  }

  .col-lg-8 {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
  }
}
</style>
