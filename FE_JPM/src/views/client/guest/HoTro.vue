<template>
  <main class="support-page pb-5">

    <div class="container py-5 my-4">
      
      <!-- CONTACT SECTION (THE EVENT THEME) -->
      <section id="contact" class="contact section mb-5 pb-5 border-bottom">
        
        <!-- SECTION 1: GIỮ KẾT NỐI (Layout 2 cột) -->
        <div class="row align-items-center gx-lg-5 mb-5 pb-5">
          <div class="col-lg-5 mb-5 mb-lg-0 pe-lg-5">
            <div class="section-title text-start pb-0 mb-4">
              <span class="overline">Liên Hệ</span>
              <h2>Giữ Kết Nối</h2>
              <p>JPM luôn sẵn sàng lắng nghe và hỗ trợ bạn trong mọi vấn đề liên quan đến phụ tùng, dịch vụ và chính sách bảo hành. Đừng ngần ngại liên hệ với chúng tôi.</p>
            </div>
            
            <div class="d-flex flex-column gap-3 mt-4">
              <div class="info-item d-flex align-items-center justify-content-start p-3 bg-white border-0 shadow-sm rounded" style="box-shadow: 0px 0px 15px rgba(0,0,0,0.05) !important;">
                <i class="bi bi-geo-alt ms-2 me-4 flex-shrink-0"></i>
                <div>
                  <h3 class="mb-1 text-start" style="font-size: 16px;">Trụ Sở Chính</h3>
                  <p class="text-start mb-0">123 Đường Hàng Hải, Quận 1, TP. HCM</p>
                </div>
              </div>
              <div class="info-item d-flex align-items-center justify-content-start p-3 bg-white border-0 shadow-sm rounded" style="box-shadow: 0px 0px 15px rgba(0,0,0,0.05) !important;">
                <i class="bi bi-telephone ms-2 me-4 flex-shrink-0"></i>
                <div>
                  <h3 class="mb-1 text-start" style="font-size: 16px;">Hotline Hỗ Trợ</h3>
                  <p class="text-start mb-0">1900 xxxx - 0909 xxx xxx</p>
                </div>
              </div>
              <div class="info-item d-flex align-items-center justify-content-start p-3 bg-white border-0 shadow-sm rounded" style="box-shadow: 0px 0px 15px rgba(0,0,0,0.05) !important;">
                <i class="bi bi-envelope ms-2 me-4 flex-shrink-0"></i>
                <div>
                  <h3 class="mb-1 text-start" style="font-size: 16px;">Email</h3>
                  <p class="text-start mb-0">support@jpm.vn</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <!-- Marine Themed Image -->
            <img src="https://images.unsplash.com/photo-1502680390469-be75c86b636f?q=80&w=800&auto=format&fit=crop" alt="Contact Us" class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 500px; box-shadow: 0px 0px 20px rgba(0,0,0,0.1);">
          </div>
        </div>

        <!-- SECTION 2: GỬI LỜI NHẮN (Layout 2 cột) -->
        <div class="row align-items-center gx-lg-5 flex-lg-row">
          
          <!-- Box Form (Bên trái) -->
          <div class="col-lg-6 mb-5 mb-lg-0">
            <form @submit.prevent="submitForm" class="php-email-form rounded" style="border: 1px solid rgba(0,0,0,0.05);">
              <div class="row gy-4">
                <div class="col-12">
                  <input type="text" v-model="formData.ho_ten" class="form-control" placeholder="Họ và tên" required>
                </div>
                <div class="col-md-6">
                  <input type="email" v-model="formData.email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-6">
                  <input type="tel" v-model="formData.so_dien_thoai" class="form-control" placeholder="Số điện thoại" required>
                </div>
                <div class="col-12">
                  <textarea v-model="formData.noi_dung" class="form-control" rows="5" placeholder="Nội dung lời nhắn" required></textarea>
                </div>
                <div class="col-12 text-center mt-4">
                  <button type="submit" :disabled="isSubmitting" class="d-inline-flex align-items-center justify-content-center">
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ isSubmitting ? 'Đang gửi...' : 'Gửi lời nhắn' }}
                  </button>
                </div>
              </div>
            </form>
          </div>

          <!-- Text (Bên phải) -->
          <div class="col-lg-6 ps-lg-5">
            <div class="section-title text-start pb-0 mb-4">
              <span class="overline">Liên Hệ</span>
              <h2>Gửi Lời Nhắn</h2>
              <p>Nếu bạn cần hỗ trợ kỹ thuật, kiểm tra đơn hàng hoặc có bất kỳ câu hỏi nào, vui lòng để lại lời nhắn. Chuyên viên của chúng tôi sẽ phản hồi sớm nhất có thể.</p>
            </div>
            
            <div class="mt-4">
              <h3 style="font-size: 18px; font-weight: 700; color: var(--heading-color); margin-bottom: 10px;">Giờ Làm Việc</h3>
              <p style="color: #6c757d; margin-bottom: 5px;">Thứ Hai - Thứ Bảy: <span style="font-weight: 500; color: var(--heading-color);">8:00 AM - 17:30 PM</span></p>
              <p style="color: #6c757d;">Chủ Nhật: <span style="font-weight: 500; color: var(--accent-color);">Nghỉ</span></p>
            </div>

            <div class="d-flex gap-2 mt-4 social-links">
              <a href="#" class="d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: color-mix(in srgb, var(--accent-color), transparent 90%); color: var(--accent-color); border-radius: 50%; transition: 0.3s;" onmouseover="this.style.background='var(--accent-color)'; this.style.color='#fff';" onmouseout="this.style.background='color-mix(in srgb, var(--accent-color), transparent 90%)'; this.style.color='var(--accent-color)';"><i class="bi bi-facebook"></i></a>
              <a href="#" class="d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: color-mix(in srgb, var(--accent-color), transparent 90%); color: var(--accent-color); border-radius: 50%; transition: 0.3s;" onmouseover="this.style.background='var(--accent-color)'; this.style.color='#fff';" onmouseout="this.style.background='color-mix(in srgb, var(--accent-color), transparent 90%)'; this.style.color='var(--accent-color)';"><i class="bi bi-youtube"></i></a>
              <a href="#" class="d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: color-mix(in srgb, var(--accent-color), transparent 90%); color: var(--accent-color); border-radius: 50%; transition: 0.3s;" onmouseover="this.style.background='var(--accent-color)'; this.style.color='#fff';" onmouseout="this.style.background='color-mix(in srgb, var(--accent-color), transparent 90%)'; this.style.color='var(--accent-color)';"><i class="bi bi-twitter"></i></a>
            </div>
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
      <section class="mb-5">
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
      isSubmitting, 
      formData,
      submitForm 
    };
  }
};
</script>

