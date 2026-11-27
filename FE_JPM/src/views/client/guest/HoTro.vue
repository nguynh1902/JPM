<template>
  <div class="support-page pb-5">

    <!-- HERO BANNER HỖ TRỢ DÀI NGANG FULL MÀN HÌNH -->
    <div class="store-hero-banner position-relative overflow-hidden" :class="{ 'banner-no-img': !currentSupportBannerImg }" :style="supportBannerStyle">
      <!-- Lớp phủ Gradient làm trắng bên trái mềm mại -->
      <div class="store-banner-overlay"></div>

      <!-- Watermark họa tiết kỹ thuật/chăm sóc khách hàng khi chưa có ảnh -->
      <div v-if="!currentSupportBannerImg" class="banner-placeholder-pattern">
        <i class="bi bi-headset"></i>
      </div>

      <!-- Nội dung chữ bên trái -->
      <div class="container h-100 position-relative z-2">
        <div class="row h-100 align-items-center">
          <div class="col-12 col-md-9 col-lg-7 store-banner-content py-4 py-lg-0">
            <!-- Badge thương hiệu cao cấp -->
            <div class="d-inline-flex align-items-center gap-2 store-banner-badge mb-2">
              <span class="badge-dot"></span>
              <span>JPM MARINE SUPPORT</span>
              <span class="badge-divider">•</span>
              <span class="badge-sub">TƯ VẤN & BẢO HÀNH 24/7</span>
            </div>
            
            <!-- Tiêu đề lớn -->
            <h1 class="store-banner-title mb-2">
              <template v-if="settings?.support_banner_title">
                {{ settings.support_banner_title }}
              </template>
              <template v-else>
                Trung tâm <span class="text-danger">hỗ trợ &amp; kỹ thuật</span>
              </template>
            </h1>

            <!-- Dòng chữ nhỏ mô tả bên dưới -->
            <p class="store-banner-subtitle mb-0">
              {{ settings?.support_banner_subtitle || 'Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM và hỗ trợ 24/7.' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="container pb-4 pt-4">
      
      <!-- 2. MAIN CONTACT SECTION (2-COLUMN LAYOUT MATCHING REFERENCE) -->
      <section id="contact" class="contact section pt-0 mb-5 pb-5 border-bottom">
        <div class="row align-items-start gx-lg-5 gy-5">
          
          <!-- CỘT TRÁI: GIỮ KẾT NỐI + LƯỚI 2x2 THÔNG TIN + MẠNG XÃ HỘI -->
          <div class="col-lg-6 pe-lg-4">
            <div class="d-flex align-items-center gap-3 mb-2">
              <span class="contact-overline-line"></span>
              <span class="contact-overline-text">LIÊN HỆ</span>
            </div>

            <h2 class="contact-heading mb-3">Giữ kết nối</h2>

            <p class="contact-desc text-muted mb-4">
              JPM luôn sẵn sàng lắng nghe và hỗ trợ bạn trong mọi vấn đề liên quan đến phụ tùng, dịch vụ kỹ thuật và chính sách bảo hành. Đừng ngần ngại liên hệ với chúng tôi.
            </p>

            <!-- Lưới 2x2 thông tin liên hệ (Kết nối Fullstack với Cấu hình Hệ thống) -->
            <div class="row g-4 pt-2 pb-4 mb-4 border-bottom">
              <!-- Địa chỉ -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-geo-alt contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-title">Trụ Sở / Showroom</div>
                    <div class="contact-item-desc">{{ settings.company_address || 'Đang cập nhật địa chỉ' }}</div>
                  </div>
                </div>
              </div>

              <!-- Điện thoại -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-telephone contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-title">Hotline Kỹ Thuật & Mua Hàng</div>
                    <div>
                      <a :href="'tel:' + (settings.company_hotline || '').replace(/\s+/g, '')" class="contact-item-desc text-decoration-none">
                        {{ settings.company_hotline || 'Đang cập nhật' }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-envelope-open contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-title">Hòm Thư Hỗ Trợ</div>
                    <div>
                      <a :href="'mailto:' + (settings.company_email || 'contact@jpm-marine.vn')" class="contact-item-desc text-decoration-none">
                        {{ settings.company_email || 'contact@jpm-marine.vn' }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Giờ làm việc -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-clock contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-title">Thời Gian Làm Việc</div>
                    <div class="contact-item-desc">{{ settings.business_hours || '08:00 - 17:30 (Thứ 2 - Thứ 7)' }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Follow Us (Đồng bộ Link Mạng Xã Hội từ Cấu Hình) -->
            <div>
              <div class="contact-social-label mb-3">
                Theo Dõi Chúng Tôi
              </div>
              <div class="d-flex align-items-center flex-wrap gap-3">
                <a :href="settings.social_facebook || '#'" :target="settings.social_facebook ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a :href="settings.social_youtube || '#'" :target="settings.social_youtube ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="YouTube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                <a :href="settings.social_zalo || '#'" :target="settings.social_zalo ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="Zalo" aria-label="Zalo">
                  <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" aria-hidden="true">
                    <path d="M12.49 10.2722v-.4496h1.3467v6.3218h-.7704a.576.576 0 01-.5763-.5729l-.0006.0005a3.273 3.273 0 01-1.9372.6321c-1.8138 0-3.2844-1.4697-3.2844-3.2823 0-1.8125 1.4706-3.2822 3.2844-3.2822a3.273 3.273 0 011.9372.6321l.0006.0005zM6.9188 7.7896v.205c0 .3823-.051.6944-.2995 1.0605l-.03.0343c-.0542.0615-.1815.206-.2421.2843L2.024 14.8h4.8948v.7682a.5764.5764 0 01-.5767.5761H0v-.3622c0-.4436.1102-.6414.2495-.8476L4.8582 9.23H.1922V7.7896h6.7266zm8.5513 8.3548a.4805.4805 0 01-.4803-.4798v-7.875h1.4416v8.3548H15.47zM20.6934 9.6C22.52 9.6 24 11.0807 24 12.9044c0 1.8252-1.4801 3.306-3.3066 3.306-1.8264 0-3.3066-1.4808-3.3066-3.306 0-1.8237 1.4802-3.3044 3.3066-3.3044zm-10.1412 5.253c1.0675 0 1.9324-.8645 1.9324-1.9312 0-1.065-.865-1.9295-1.9324-1.9295s-1.9324.8644-1.9324 1.9295c0 1.0667.865 1.9312 1.9324 1.9312zm10.1412-.0033c1.0737 0 1.945-.8707 1.945-1.9453 0-1.073-.8713-1.9436-1.945-1.9436-1.0753 0-1.945.8706-1.945 1.9453 0 1.0746.8697 1.9453 1.945 1.9453z"/>
                  </svg>
                </a>
                <a :href="settings.social_instagram || '#'" :target="settings.social_instagram ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a :href="settings.social_tiktok || '#'" :target="settings.social_tiktok ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="TikTok" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
              </div>
            </div>
          </div>

          <!-- CỘT PHẢI: FORM GỬI LỜI NHẮN ("Your Details" EQUIVALENT) -->
          <div class="col-lg-6 ps-lg-4">
            <h2 class="contact-form-heading mb-1">Gửi lời nhắn</h2>
            <p class="text-muted mb-4" style="font-size: 0.9rem;">
              Nếu bạn cần hỗ trợ kỹ thuật hoặc kiểm tra đơn hàng, vui lòng để lại thông tin bên dưới.
            </p>

            <form @submit.prevent="submitForm" class="contact-detail-form">
              <div class="row g-4">
                <div class="col-md-6">
                  <label class="contact-field-label">HỌ VÀ TÊN <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    v-model="formData.ho_ten"
                    class="form-control contact-field-input"
                    placeholder="Nguyễn Văn A"
                    required
                  >
                </div>

                <div class="col-md-6">
                  <label class="contact-field-label">ĐỊA CHỈ EMAIL <span class="text-danger">*</span></label>
                  <input
                    type="email"
                    v-model="formData.email"
                    class="form-control contact-field-input"
                    placeholder="email@domain.com"
                    required
                  >
                </div>

                <div class="col-12">
                  <label class="contact-field-label">SỐ ĐIỆN THOẠI LIÊN HỆ <span class="text-danger">*</span></label>
                  <input
                    type="tel"
                    v-model="formData.so_dien_thoai"
                    class="form-control contact-field-input"
                    placeholder="Nhập số điện thoại của bạn..."
                    required
                  >
                </div>

                <div class="col-12">
                  <label class="contact-field-label">NỘI DUNG CÂU HỎI / YÊU CẦU <span class="text-danger">*</span></label>
                  <textarea
                    v-model="formData.noi_dung"
                    class="form-control contact-field-input"
                    rows="5"
                    placeholder="Nhập nội dung lời nhắn..."
                    required
                  ></textarea>
                </div>

                <div class="col-12 pt-1">
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="btn btn-contact-submit d-inline-flex align-items-center justify-content-center"
                  >
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ isSubmitting ? 'ĐANG GỬI...' : 'GỬI LỜI NHẮN' }}
                  </button>
                </div>
              </div>
            </form>
          </div>

        </div>
      </section>

      <!-- 4. MAP SECTION (TỰ ĐỘNG CHỈ ĐỊNH ĐỊA CHỈ TRÊN BẢN ĐỒ THEO CẤU HÌNH ADMIN) -->
      <section id="map-section" class="mb-5 pb-5 border-bottom">
        <div class="w-100 shadow-sm rounded-3 overflow-hidden border" style="height: 450px; border-radius: var(--jpm-radius-card, 12px);">
          <iframe 
            :src="mapEmbedUrl" 
            frameborder="0" 
            style="border:0; width: 100%; height: 100%;" 
            allowfullscreen="" 
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </section>

      <!-- 4.5. QUY TRÌNH TIẾP NHẬN & HỖ TRỢ KỸ THUẬT 01 -> 04 (Đồng bộ stepper Trang chủ) -->
      <section class="support-process-band pt-0 mb-5 pb-5 border-bottom">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-4">
          <div>
            <div class="d-flex align-items-center gap-3 mb-2">
              <span class="contact-overline-line"></span>
              <span class="contact-overline-text">QUY TRÌNH HỖ TRỢ</span>
            </div>
            <h2 class="contact-heading mb-0">Cách chúng tôi xử lý yêu cầu</h2>
          </div>
          <span class="support-process-sub">Phản hồi kỹ thuật &amp; báo giá trong vòng 24 giờ</span>
        </div>

        <div class="row g-4">
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="support-step-item">
              <div class="support-step-top">
                <span class="support-step-num">01</span>
                <div class="support-step-connector d-none d-lg-flex">
                  <span class="support-step-line"></span>
                  <i class="bi bi-chevron-right support-step-arrow"></i>
                </div>
              </div>
              <h6 class="support-step-title">Tiếp nhận thông tin</h6>
              <p class="support-step-desc">Gửi mã phụ tùng, số máy (Serial), model hoặc ảnh chụp chi tiết qua form / Zalo.</p>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="support-step-item">
              <div class="support-step-top">
                <span class="support-step-num">02</span>
                <div class="support-step-connector d-none d-lg-flex">
                  <span class="support-step-line"></span>
                  <i class="bi bi-chevron-right support-step-arrow"></i>
                </div>
              </div>
              <h6 class="support-step-title">Đối chiếu kỹ thuật</h6>
              <p class="support-step-desc">Kỹ sư JPM kiểm tra sơ đồ rã máy chính hãng và mã thay thế mới nhất.</p>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="support-step-item">
              <div class="support-step-top">
                <span class="support-step-num">03</span>
                <div class="support-step-connector d-none d-lg-flex">
                  <span class="support-step-line"></span>
                  <i class="bi bi-chevron-right support-step-arrow"></i>
                </div>
              </div>
              <h6 class="support-step-title">Xác nhận &amp; Bảo hành</h6>
              <p class="support-step-desc">Báo giá minh bạch, thông báo tiến độ kho hàng và chính sách bảo hành OEM.</p>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="support-step-item">
              <div class="support-step-top">
                <span class="support-step-num">04</span>
              </div>
              <h6 class="support-step-title">Bàn giao &amp; Đồng hành</h6>
              <p class="support-step-desc">Vận chuyển hỏa tốc tận xưởng, tiếp tục hỗ trợ kỹ thuật sau khi lắp đặt.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 5. FAQS SECTION with Search -->
      <section id="faq-section" class="mb-5">
        <div class="row gx-lg-5">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="d-flex align-items-center gap-3 mb-2">
              <span class="contact-overline-line"></span>
              <span class="contact-overline-text">FAQS</span>
            </div>
            <h2 class="contact-heading mb-3">Câu hỏi<br>thường gặp</h2>
            <p class="contact-desc text-muted mb-0">Tổng hợp các câu hỏi và giải đáp chi tiết nhất về dịch vụ, sản phẩm và chính sách của JPM.</p>
            
            <!-- Search Bar for FAQs (Chuẩn Hóa theo search-input-box Client 2026) -->
            <div class="search-input-box mt-4">
              <i class="bi bi-search search-input-icon"></i>
              <input 
                type="text" 
                class="search-input-field" 
                placeholder="Tìm câu hỏi của bạn..." 
                v-model="faqSearchQuery"
              >
              <button 
                v-if="faqSearchQuery" 
                type="button" 
                class="btn-clear-search" 
                @click="faqSearchQuery = ''"
                title="Xóa tìm kiếm"
              >
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>
          </div>
          <div class="col-lg-8">
            <div v-if="filteredFaqs.length > 0" class="accordion accordion-flush client-faq-accordion" id="faqAccordion">
              <div class="accordion-item bg-transparent border-0 border-bottom mb-2" v-for="(faq, index) in filteredFaqs" :key="index">
                <h2 class="accordion-header" :id="'heading' + index">
                  <button class="accordion-button collapsed bg-transparent shadow-none px-0 py-4 fw-bold" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index" aria-expanded="false" :aria-controls="'collapse' + index">
                    {{ faq.question }}
                  </button>
                </h2>
                <div :id="'collapse' + index" class="accordion-collapse collapse" :aria-labelledby="'heading' + index" data-bs-parent="#faqAccordion">
                  <div class="accordion-body px-0 pt-0 pb-4 faq-answer-content" v-html="faq.answer">
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 mb-3 d-block text-black-50"></i>
              Không tìm thấy câu trả lời nào phù hợp với từ khóa của bạn.
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useSiteSettings } from '../../../composables/useSiteSettings';
import Swal from 'sweetalert2';

export default {
  name: 'HoTro',
  setup() {
    const route = useRoute();
    const isSubmitting = ref(false);
    const faqSearchQuery = ref('');
    const { settings } = useSiteSettings();

    const getBannerImageUrl = (path) => {
      if (!path) return '';
      if (typeof path === 'string') {
        if (path.startsWith('http') || path.startsWith('blob:') || path.startsWith('data:') || path.startsWith('/client/') || path.startsWith('/assets/')) {
          return path;
        }
      }
      const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/api\/?$/, '');
      const clean = path.startsWith('/') ? path : '/' + path;
      return `${baseUrl}${clean}`;
    };

    const currentSupportBannerImg = computed(() => {
      return (settings.value && settings.value.support_banner_image) ? settings.value.support_banner_image : '/client/image/hotro_banner.png';
    });

    const supportBannerStyle = computed(() => {
      const rawPath = currentSupportBannerImg.value;
      if (rawPath) {
        const imgUrl = getBannerImageUrl(rawPath);
        return {
          backgroundImage: `url("${imgUrl}")`,
          backgroundSize: 'cover',
          backgroundPosition: 'center center',
          backgroundRepeat: 'no-repeat',
        };
      }
      return {};
    });

    // Tự động map địa chỉ từ Cấu Hình Hệ Thống sang Google Maps Embed
    const mapEmbedUrl = computed(() => {
      const address = settings.value?.company_address;
      if (!address) {
        return 'https://maps.google.com/maps?q=TP+Ho+Chi+Minh&t=&z=14&ie=UTF8&iwloc=&output=embed';
      }
      return `https://maps.google.com/maps?q=${encodeURIComponent(address)}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
    });

    const { data: faqsData } = useQuery({
      queryKey: ['client-faqs'],
      queryFn: async () => {
        const res = await axiosClient.get('/public/faqs');
        return res.data?.data || res.data || [];
      }
    });

    const faqs = computed(() => faqsData.value || []);

    const filteredFaqs = computed(() => {
      const query = faqSearchQuery.value.toLowerCase();
      if (!query) return faqs.value;
      
      return faqs.value.filter(faq => 
        (faq.question && faq.question.toLowerCase().includes(query)) || 
        (faq.answer && faq.answer.toLowerCase().includes(query))
      );
    });

    const scrollToContact = () => {
      const el = document.getElementById('contact');
      if (el) {
        const y = el.getBoundingClientRect().top + window.scrollY - 85;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    };

    const scrollToMap = () => {
      const el = document.getElementById('map-section');
      if (el) {
        const y = el.getBoundingClientRect().top + window.scrollY - 85;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    };

    const scrollToFaq = () => {
      const el = document.getElementById('faq-section');
      if (el) {
        const y = el.getBoundingClientRect().top + window.scrollY - 90;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    };

    const applyRouteParams = () => {
      if (route.query.faq) {
        const faqType = String(route.query.faq).toLowerCase();
        if (faqType === 'bao-hanh') {
          faqSearchQuery.value = 'bảo hành';
        } else if (faqType === 'van-chuyen') {
          faqSearchQuery.value = 'vận chuyển';
        } else if (faqType === 'doi-tra') {
          faqSearchQuery.value = 'đổi trả';
        } else if (faqType === 'mua-hang') {
          faqSearchQuery.value = 'mua hàng';
        } else {
          faqSearchQuery.value = String(route.query.faq);
        }
        setTimeout(scrollToFaq, 350);
      } else if (route.query.q) {
        faqSearchQuery.value = String(route.query.q);
        setTimeout(scrollToFaq, 350);
      }

      if (route.hash === '#faq-section') {
        setTimeout(scrollToFaq, 350);
      } else if (route.hash === '#contact') {
        setTimeout(scrollToContact, 350);
      } else if (route.hash === '#map-section') {
        setTimeout(scrollToMap, 350);
      }
    };

    onMounted(() => {
      applyRouteParams();
    });

    watch([() => route.query.faq, () => route.query.q, () => route.hash], () => {
      applyRouteParams();
    });

    const formData = ref({
      ho_ten: '',
      email: '',
      so_dien_thoai: '',
      noi_dung: ''
    });

    const submitForm = async () => {
      isSubmitting.value = true;
      try {
        await axiosClient.post('/public/lien-he', formData.value);
        
        // Reset form
        formData.value = { ho_ten: '', email: '', so_dien_thoai: '', noi_dung: '' };
        
        Swal.fire({
          title: 'Thành công!',
          text: 'Yêu cầu hỗ trợ của bạn đã được gửi. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.',
          icon: 'success',
          confirmButtonColor: '#0A1128',
          confirmButtonText: 'Đóng'
        });
      } catch (error) {
        Swal.fire({
          title: 'Lỗi!',
          text: error.response?.data?.message || 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau.',
          icon: 'error',
          confirmButtonColor: '#0A1128',
          confirmButtonText: 'Đóng'
        });
      } finally {
        isSubmitting.value = false;
      }
    };

    return { 
      settings,
      currentSupportBannerImg,
      supportBannerStyle,
      mapEmbedUrl,
      faqs, 
      faqSearchQuery, 
      filteredFaqs, 
      scrollToContact,
      scrollToMap,
      scrollToFaq,
      isSubmitting, 
      formData,
      submitForm 
    };
  }
};
</script>

<style scoped>
.support-page {
  min-height: 100vh;
  background-color: var(--jpm-neutral-50, #f8fafc) !important;
}

.support-page section {
  background-color: transparent !important;
}

/* ── 2. LEFT COLUMN: KEEP CLOSE / GET IN TOUCH ── */
.contact-overline-line {
  display: inline-block;
  width: 36px;
  height: 2px;
  background-color: var(--jpm-color-red-marine, #F82249);
}

.contact-overline-text {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: var(--jpm-color-red-marine, #F82249);
}

.contact-heading {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: clamp(1.25rem, 1.8vw, 1.45rem);
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: -0.3px;
  line-height: 1.3;
}

.contact-desc {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.875rem;
  line-height: 1.6;
  color: var(--jpm-neutral-500, #64748B);
}

.contact-grid-icon {
  font-size: 1.55rem;
  color: var(--jpm-color-red-marine, #F82249);
  line-height: 1.1;
  margin-top: 2px;
}

.contact-item-title {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 2px;
}

.contact-item-desc {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.88rem;
  color: var(--jpm-neutral-500, #64748B);
  line-height: 1.5;
  transition: color 0.15s ease;
}

a.contact-item-desc:hover {
  color: var(--jpm-color-red-marine, #F82249) !important;
}

.contact-social-label {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.contact-social-link {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(248, 34, 73, 0.08);
  color: #f82249;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.92rem;
  text-decoration: none;
  transition: all 0.25s ease;
}

.contact-social-link:hover {
  background: #f82249;
  color: #ffffff;
  transform: translateY(-2px);
}

/* ── 3. RIGHT COLUMN: YOUR DETAILS FORM ── */
.contact-form-heading {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: clamp(1.25rem, 1.8vw, 1.45rem);
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: -0.3px;
  line-height: 1.3;
}

.contact-field-label {
  display: block;
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 0.45rem;
}

.contact-field-input {
  font-family: var(--default-font, 'Roboto', sans-serif);
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 7px);
  padding: 0.55rem 0.85rem;
  font-size: 0.96rem;
  color: #1e293b;
  transition: all 0.2s ease;
}

.contact-field-input:not(textarea) {
  height: var(--jpm-control-height, 37px);
  padding: 0 12px;
}

textarea.contact-field-input {
  height: auto !important;
  min-height: 120px !important;
  resize: vertical;
}

.contact-field-input:focus {
  background-color: #ffffff;
  border-color: #f82249;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.15) !important;
  outline: 0;
}

.btn-contact-submit {
  font-family: var(--heading-font, var(--jpm-font-family));
  font-weight: 700;
  font-size: 0.92rem;
  letter-spacing: 0.5px;
  background-color: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  border: none;
  border-radius: var(--jpm-radius-control, 7px);
  height: var(--jpm-control-height, 37px);
  padding: 0 22px;
  box-shadow: none;
  transition: all 0.25s ease;
}

.btn-contact-submit:hover:not(:disabled) {
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  box-shadow: none !important;
  transform: translateY(-1px) !important;
}

.btn-contact-submit:active:not(:disabled) {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
}

.btn-contact-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* ── SEARCH INPUT IN FAQ (CHUẨN HÓA THEO DESIGN SYSTEM 2026) ── */
.search-input-box {
  display: flex;
  align-items: center;
  height: var(--jpm-control-height, 37px);
  border-radius: var(--jpm-radius-control, 7px);
  border: 1px solid #e2e8f0;
  background: #ffffff;
  padding: 0 12px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  width: 100%;
}

.search-input-box:focus-within {
  border-color: #f82249 !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: 0 !important;
}

.search-input-box:hover {
  border-color: #cbd5e1;
}

.search-input-icon {
  font-size: 0.95rem;
  color: #94a3b8;
  margin-right: 10px;
  flex-shrink: 0;
  transition: color 0.15s ease-in-out;
}

.search-input-box:focus-within .search-input-icon {
  color: #f82249;
}

.search-input-field {
  flex: 1;
  border: none !important;
  background: transparent !important;
  outline: none !important;
  box-shadow: none !important;
  font-size: 0.9rem;
  font-family: var(--default-font, 'Roboto', sans-serif);
  color: #0A1128;
  font-weight: 400;
  padding: 0;
  height: 100%;
}

.search-input-field::placeholder {
  color: #94a3b8;
}

.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: color 0.15s ease;
}

.btn-clear-search:hover {
  color: #f82249;
}

/* ── 4.5. QUY TRÌNH HỖ TRỢ 01 -> 04 (Đồng bộ Stepper nối mạch Trang chủ) ── */
.support-process-sub {
  font-size: 0.88rem;
  color: #64748b;
  font-weight: 500;
}

.support-step-item {
  height: 100%;
}

.support-step-top {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.support-step-num {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: var(--jpm-color-navy-dark, #0A1128);
  font-family: var(--jpm-font-mono, 'Roboto Mono', monospace);
  font-size: 0.8rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.support-step-connector {
  flex: 1;
  align-items: center;
  gap: 4px;
}

.support-step-line {
  flex: 1;
  height: 1px;
  background-color: #dbe4ef;
}

.support-step-arrow {
  color: #94a3b8;
  font-size: 0.78rem;
  line-height: 1;
}

.support-step-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 6px;
}

.support-step-desc {
  font-size: 0.9rem;
  color: #475569;
  line-height: 1.55;
  margin-bottom: 0;
}

/* ── FAQ ACCORDION BRAND TWEAKS (CHUẨN HÓA NATIVE CLIENT STYLE) ── */
.client-faq-accordion .accordion-item {
  border-bottom: 1px solid #e2e8f0 !important;
}

.client-faq-accordion .accordion-button {
  font-family: var(--heading-font, var(--jpm-font-family));
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 1.05rem;
  font-weight: 700;
  line-height: 1.5;
}

.client-faq-accordion .accordion-button:not(.collapsed) {
  color: var(--jpm-color-navy-dark, #0A1128) !important;
  background-color: transparent !important;
  box-shadow: none !important;
}

.client-faq-accordion .accordion-button::after {
  background-size: 14px;
}

.client-faq-accordion .faq-answer-content {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.94rem;
  line-height: 1.75;
  color: #475569;
}
</style>
