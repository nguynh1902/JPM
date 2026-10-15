<template>
  <main class="support-page pb-5">

    <!-- ════════════════════════════════════════════════════════════════
         SECTION 1: HERO BANNER WITH SUBMERGED WATERMARK IMAGE
    ════════════════════════════════════════════════════════════════ -->
    <section class="hero-editorial-section position-relative overflow-hidden">
      <!-- Submerged Marine Image (Ảnh chìm trong nền) -->
      <div class="hero-submerged-bg"></div>

      <div class="container position-relative z-1 py-5">
        <div class="row align-items-center py-lg-4">

          <!-- Left Column: Big Title + Description + Pill Button -->
          <div class="col-lg-6 text-start">
            <h1 class="hero-main-heading mb-4">
              Trung Tâm<br />
              Hỗ Trợ <span class="text-secondary fw-bold">&amp; Kỹ Thuật</span><br />
              Hàng Hải
            </h1>
            <p class="hero-subtext text-muted mb-4">
              Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM, hướng dẫn bảo dưỡng động cơ và giải đáp chính sách bảo hành 24/7.
            </p>
            <button type="button" class="btn btn-dark-pill px-4 py-2" @click="scrollToContact">
              Gửi yêu cầu ngay
            </button>
          </div>

        </div>
      </div>
    </section>

    <div class="container py-4 my-2">
      
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

            <!-- Lưới 2x2 thông tin liên hệ -->
            <div class="row g-4 pt-2 pb-4 mb-4 border-bottom">
              <!-- Địa chỉ -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-map contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-line fw-semibold text-dark">123 Đường Hàng Hải,</div>
                    <div class="contact-item-line text-muted">Quận 1, TP. Hồ Chí Minh</div>
                  </div>
                </div>
              </div>

              <!-- Điện thoại -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-telephone contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-line fw-semibold text-dark">(+84) 1900 xxxx</div>
                    <div class="contact-item-line text-muted">(+84) 0909 xxx xxx</div>
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-envelope-open contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-line fw-semibold text-dark">support@jpm.vn</div>
                    <div class="contact-item-line text-muted">cskh@jpm.vn</div>
                  </div>
                </div>
              </div>

              <!-- Giờ làm việc -->
              <div class="col-sm-6">
                <div class="d-flex align-items-start gap-3">
                  <i class="bi bi-clock contact-grid-icon flex-shrink-0"></i>
                  <div>
                    <div class="contact-item-line fw-semibold text-dark">Mở cửa 08:00 - 17:30</div>
                    <div class="contact-item-line text-muted">Thứ 2 - Thứ 7 (CN: Nghỉ)</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Follow Us -->
            <div>
              <div class="text-uppercase fw-bold mb-3" style="font-size: 0.78rem; letter-spacing: 1.5px; color: #0e1b4d;">
                Theo Dõi Chúng Tôi
              </div>
              <div class="d-flex align-items-center gap-3">
                <a href="#" class="contact-social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="contact-social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="contact-social-link" title="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="contact-social-link" title="YouTube"><i class="bi bi-youtube"></i></a>
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

      <!-- 4. MAP SECTION -->
      <section class="mb-5 pb-5 border-bottom">
        <div class="w-100 shadow rounded overflow-hidden" style="height: 450px;">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.5!2d106.69!3d10.77!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ2JzEyLjAiTiAxMDbCsDQxJzI0LjAiRQ!5e0!3m2!1svi!2s!4v1620000000000!5m2!1svi!2s" frameborder="0" style="border:0; width: 100%; height: 100%;" allowfullscreen="" loading="lazy"></iframe>
        </div>
      </section>

      <!-- 5. FAQS SECTION with Search -->
      <section id="faq-section" class="mb-5">
        <div class="row gx-lg-5">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <h6 class="text-uppercase fw-bold mb-3 text-danger" style="letter-spacing: 2px;">FAQs</h6>
            <h2 class="fw-bold mb-4 display-6" style="color: #0e1b4d;">Câu Hỏi<br>Thường Gặp</h2>
            <p class="text-muted" style="line-height: 1.8;">Tổng hợp các câu hỏi và giải đáp chi tiết nhất về dịch vụ, sản phẩm và chính sách của JPM.</p>
            
            <!-- Search Bar for FAQs -->
            <div class="input-group mt-4 pt-2">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 shadow-none" placeholder="Tìm câu hỏi của bạn..." v-model="faqSearchQuery">
              </div>
          </div>
          <div class="col-lg-8">
            <div v-if="filteredFaqs.length > 0" class="accordion accordion-flush" id="faqAccordion">
              <div class="accordion-item bg-transparent border-0 border-bottom mb-2" v-for="(faq, index) in filteredFaqs" :key="index" style="border-color: #dee2e6 !important;">
                <h2 class="accordion-header" :id="'heading' + index">
                  <button class="accordion-button collapsed bg-transparent shadow-none px-0 py-4 fw-bold" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index" aria-expanded="false" :aria-controls="'collapse' + index" style="color: #0e1b4d; font-size: 1.15rem;">
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
import Swal from 'sweetalert2';

export default {
  name: 'HoTro',
  setup() {
    const isSubmitting = ref(false);
    const faqSearchQuery = ref('');

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
          confirmButtonColor: '#0e1b4d',
          confirmButtonText: 'Đóng'
        });
      } catch (error) {
        Swal.fire({
          title: 'Lỗi!',
          text: 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau.',
          icon: 'error',
          confirmButtonColor: '#0e1b4d',
          confirmButtonText: 'Đóng'
        });
      } finally {
        isSubmitting.value = false;
      }
    };

    return { 
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
  padding-top: 70px;
  background-color: #ffffff;
}

.support-page section:not(.hero-editorial-section) {
  background-color: #ffffff !important;
}

/* ── SECTION 1: HERO BANNER WITH SUBMERGED WATERMARK IMAGE ── */
.hero-editorial-section {
  padding-top: 30px;
  padding-bottom: 40px;
}

.hero-submerged-bg {
  position: absolute;
  top: 0;
  right: 0;
  width: 65%;
  height: 100%;
  background-image: url('https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=1600&auto=format&fit=crop&q=85');
  background-size: cover;
  background-position: center right;
  opacity: 0.95;
  mask-image: linear-gradient(to right, transparent 0%, transparent 15%, rgba(0, 0, 0, 0.45) 45%, rgba(0, 0, 0, 1) 80%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, transparent 15%, rgba(0, 0, 0, 0.45) 45%, rgba(0, 0, 0, 1) 80%);
  pointer-events: none;
}

.hero-main-heading {
  font-family: var(--heading-font);
  font-size: clamp(1.8rem, 2.6vw, 2.2rem);
  font-weight: 700;
  color: #111827;
  line-height: 1.25;
  letter-spacing: normal;
}

.hero-subtext {
  font-size: 0.95rem;
  line-height: 1.7;
  max-width: 480px;
}

.btn-dark-pill {
  background-color: #27272a;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.88rem;
  border-radius: 50rem;
  border: none;
  transition: all 0.25s ease;
}
.btn-dark-pill:hover {
  background-color: #f82249;
  color: #ffffff;
  transform: translateY(-2px);
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
  color: #0e1b4d;
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
  color: #0e1b4d;
  letter-spacing: normal;
}

.contact-field-label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #0e1b4d;
  margin-bottom: 0.45rem;
}

.contact-field-input {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.7rem 0.95rem;
  font-size: 0.9rem;
  color: #1e293b;
  box-shadow: none !important;
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
}

.btn-contact-submit {
  background-color: #f82249;
  color: #ffffff;
  border: none;
  border-radius: 4px;
  padding: 0.78rem 2.2rem;
  font-size: 0.88rem;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-contact-submit:hover:not(:disabled) {
  background-color: #d91639;
  transform: translateY(-2px);
}

.btn-contact-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>


