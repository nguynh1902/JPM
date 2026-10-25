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
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-1 fw-bold text-dark">Banner Trang Chủ & Video</h5>
            <small class="text-muted">Cài đặt video nền YouTube và slogan tại trang chủ Client</small>
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
                      placeholder="PHỤ TÙNG CHÍNH HÃNG" 
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

      <!-- THÔNG TIN DOANH NGHIỆP & LIÊN HỆ -->
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-1 fw-bold text-dark">Thông Tin Doanh Nghiệp & Liên Hệ</h5>
            <small class="text-muted">Thông tin công ty, hotline, email và giờ mở cửa trên toàn website Client</small>
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
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-1 fw-bold text-dark">Mạng Xã Hội</h5>
            <small class="text-muted">Liên kết các kênh mạng xã hội chính thức của doanh nghiệp</small>
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

      <!-- HIỆU NĂNG & REDIS CACHE -->
      <div class="col-12">
        <div class="card mb-4">
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
import { ref, reactive, watch, onMounted } from 'vue';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';

export default {
  name: "CauHinhHeThong",
  setup() {
    const queryClient = useQueryClient();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    const isSubmitting = ref(false);

    // Initial default state
    const defaultData = {
      homepage_youtube_url: 'https://www.youtube.com/watch?v=5CVzOqvqI38',
      homepage_hero_title: 'JPM MARINE',
      homepage_hero_subtitle: 'PHỤ TÙNG CHÍNH HÃNG',
      company_name: 'JPM Marine',
      company_address: '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh, Việt Nam',
      company_hotline: '0909 123 456',
      company_email: 'contact@jpm-marine.vn',
      business_hours: 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)',
      social_facebook: 'https://facebook.com/jpmmarine',
      social_youtube: 'https://youtube.com/@jpmmarine',
      social_zalo: 'https://zalo.me/0909123456',
      social_instagram: 'https://instagram.com/jpmmarine'
    };

    const formData = reactive({ ...defaultData });
    const initialSnapshot = ref({});

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
      handleClearCache
    };
  }
};
</script>
