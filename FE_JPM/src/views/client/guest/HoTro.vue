<template>
  <main class="support-page pb-5">

    <!-- Header chuẩn Giỏ Hàng -->
    <section class="section pb-2" style="padding-top: 110px;">
      <div class="container">
        <div class="row mb-3">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">Trung Tâm <span class="text-danger">Hỗ Trợ &amp; Kỹ Thuật</span></h2>
            <p class="text-muted">Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM và hỗ trợ 24/7.</p>
          </div>
        </div>
      </div>
    </section>

    <div class="container pb-4">
      
      <!-- 2. MAIN CONTACT SECTION (2-COLUMN LAYOUT MATCHING REFERENCE) -->
      <section id="contact" class="contact section mb-5 pb-5 border-bottom">
        <div class="row align-items-start gx-lg-5 gy-5">
          
          <!-- CỘT TRÁI: GIỮ KẾT NỐI + LƯỚI 2x2 THÔNG TIN + MẠNG XÃ HỘI -->
          <div class="col-lg-6 pe-lg-4">
            <div class="d-flex align-items-center gap-3 mb-2">
              <span class="contact-overline-line"></span>
              <span class="contact-overline-text">LIÊN HỆ</span>
            </div>

            <h2 class="contact-heading mb-3">Giữ Kết Nối</h2>

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
                    <div class="contact-item-line fw-semibold text-dark">Trụ Sở / Showroom</div>
                    <div class="contact-item-line text-muted">{{ settings.company_address || 'Đang cập nhật địa chỉ' }}</div>
                  </div>
                </div>
              </div>

              <!-- Điện thoại -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-telephone contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-line fw-semibold text-dark">Hotline Kỹ Thuật & Mua Hàng</div>
                    <div>
                      <a :href="'tel:' + (settings.company_hotline || '').replace(/\s+/g, '')" class="contact-item-line text-muted text-decoration-none">
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
                    <div class="contact-item-line fw-semibold text-dark">Hòm Thư Hỗ Trợ</div>
                    <div>
                      <a :href="'mailto:' + (settings.company_email || 'contact@jpm-marine.vn')" class="contact-item-line text-muted text-decoration-none">
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
                    <div class="contact-item-line fw-semibold text-dark">Thời Gian Làm Việc</div>
                    <div class="contact-item-line text-muted">{{ settings.business_hours || '08:00 - 17:30 (Thứ 2 - Thứ 7)' }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Follow Us (Đồng bộ Link Mạng Xã Hội từ Cấu Hình) -->
            <div>
              <div class="text-uppercase fw-bold mb-3" style="font-size: 0.78rem; letter-spacing: 1.5px; color: var(--jpm-color-navy-dark, #0A1128);">
                Theo Dõi Chúng Tôi
              </div>
              <div class="d-flex align-items-center gap-3">
                <a :href="settings.social_facebook || '#'" :target="settings.social_facebook ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                <a :href="settings.social_instagram || '#'" :target="settings.social_instagram ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                <a :href="settings.social_zalo || '#'" :target="settings.social_zalo ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="Zalo"><i class="bi bi-chat-dots-fill"></i></a>
                <a :href="settings.social_youtube || '#'" :target="settings.social_youtube ? '_blank' : '_self'" rel="noopener noreferrer" class="contact-social-link" title="YouTube"><i class="bi bi-youtube"></i></a>
              </div>
            </div>
          </div>

          <!-- CỘT PHẢI: FORM GỬI LỜI NHẮN ("Your Details" EQUIVALENT) -->
          <div class="col-lg-6 ps-lg-4">
            <h2 class="contact-form-heading mb-1">Gửi Lời Nhắn</h2>
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
                    class="btn-contact-submit d-inline-flex align-items-center justify-content-center"
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
      <section class="mb-5 pb-5 border-bottom">
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

      <!-- 5. FAQS SECTION with Search -->
      <section id="faq-section" class="mb-5">
        <div class="row gx-lg-5">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <h6 class="text-uppercase fw-bold mb-3 text-danger" style="letter-spacing: 2px;">FAQs</h6>
            <h2 class="fw-bold mb-4 display-6" style="color: var(--jpm-color-navy-dark, #0A1128);">Câu Hỏi<br>Thường Gặp</h2>
            <p class="text-muted" style="line-height: 1.8;">Tổng hợp các câu hỏi và giải đáp chi tiết nhất về dịch vụ, sản phẩm và chính sách của JPM.</p>
            
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
            <div v-if="filteredFaqs.length > 0" class="accordion accordion-flush" id="faqAccordion">
              <div class="accordion-item bg-transparent border-0 border-bottom mb-2" v-for="(faq, index) in filteredFaqs" :key="index" style="border-color: #dee2e6 !important;">
                <h2 class="accordion-header" :id="'heading' + index">
                  <button class="accordion-button collapsed bg-transparent shadow-none px-0 py-4 fw-bold" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index" aria-expanded="false" :aria-controls="'collapse' + index" style="color: var(--jpm-color-navy-dark, #0A1128); font-size: 1.15rem;">
                    {{ faq.question }}
                  </button>
                </h2>
                <div :id="'collapse' + index" class="accordion-collapse collapse" :aria-labelledby="'heading' + index" data-bs-parent="#faqAccordion">
                  <div class="accordion-body px-0 pt-0 pb-4 text-muted" style="line-height: 1.8;" v-html="faq.answer">
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
  </main>
</template>

<script>
import { ref, computed } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { useSiteSettings } from '../../../composables/useSiteSettings';
import Swal from 'sweetalert2';

export default {
  name: 'HoTro',
  setup() {
    const isSubmitting = ref(false);
    const faqSearchQuery = ref('');
    const { settings } = useSiteSettings();

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

    const scrollToFaq = () => {
      const el = document.getElementById('faq-section');
      if (el) {
        const y = el.getBoundingClientRect().top + window.scrollY - 90;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    };

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
      mapEmbedUrl,
      faqs, 
      faqSearchQuery, 
      filteredFaqs, 
      scrollToContact,
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
  background-color: #ffffff;
}

.support-page section {
  background-color: #ffffff !important;
}

/* ── 2. LEFT COLUMN: KEEP CLOSE / GET IN TOUCH ── */
.contact-overline-line {
  display: inline-block;
  width: 36px;
  height: 2px;
  background-color: #f82249;
}

.contact-overline-text {
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  color: #f82249;
}

.contact-heading {
  font-family: var(--heading-font);
  font-size: clamp(1.5rem, 2.2vw, 1.85rem);
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: normal;
}

.contact-desc {
  font-size: 0.94rem;
  line-height: 1.75;
}

.contact-grid-icon {
  font-size: 1.55rem;
  color: #f82249;
  line-height: 1.1;
  margin-top: 2px;
}

.contact-item-line {
  font-size: 0.86rem;
  line-height: 1.5;
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
  font-family: var(--heading-font);
  font-size: clamp(1.4rem, 2vw, 1.7rem);
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: normal;
}

.contact-field-label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--jpm-color-navy-dark, #0A1128);
  margin-bottom: 0.45rem;
}

.contact-field-input {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
  padding: 0.7rem 0.95rem;
  font-size: 0.9rem;
  color: #1e293b;
  transition: all 0.2s ease;
}

textarea.contact-field-input {
  height: auto !important;
  min-height: 150px !important;
  resize: vertical;
}

.contact-field-input:focus {
  background-color: #ffffff;
  border-color: #f82249;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.15) !important;
  outline: 0;
}

.btn-contact-submit {
  background-color: #f82249;
  color: #ffffff;
  border: none;
  border-radius: var(--jpm-radius-control, 8px);
  padding: 0.78rem 2.2rem;
  font-size: 0.88rem;
  font-weight: 600;
  letter-spacing: 0.3px;
  box-shadow: 0 4px 12px rgba(248, 34, 73, 0.25);
  transition: all 0.25s ease;
}

.btn-contact-submit:hover:not(:disabled) {
  background-color: #d91639;
  box-shadow: 0 6px 16px rgba(248, 34, 73, 0.35);
  transform: translateY(-2px);
}

.btn-contact-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* ── SEARCH INPUT IN FAQ (CHUẨN HÓA THEO DESIGN SYSTEM 2026) ── */
.search-input-box {
  display: flex;
  align-items: center;
  height: 44px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #e2e8f0;
  background: #ffffff;
  padding: 0 14px;
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
</style>


