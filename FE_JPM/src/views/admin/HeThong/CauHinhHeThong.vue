<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb & Top Actions chuẩn Sneat Admin -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Hệ Thống /</span> Cấu Hình Hệ Thống
      </h4>
      <div class="d-flex gap-2">
        <button 
          type="button" 
          class="btn btn-outline-secondary px-4"
          @click="resetToInitial"
          :disabled="isSubmitting"
        >
          Đặt lại
        </button>
        <button 
          type="button" 
          class="btn btn-primary px-4 shadow-sm"
          @click="saveSettings"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
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

    <div v-else class="row">
      
      <!-- 1. BANNER TRANG CHỦ & VIDEO NỀN -->
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">Banner Trang Chủ & Video Nền</h5>
          </div>

          <div class="card-body pt-4">
            <div class="row g-4">
              <!-- Cột nhập liệu -->
              <div class="col-lg-7">
                <!-- Đường dẫn YouTube -->
                <div class="mb-3">
                  <label for="homepage_youtube_url" class="form-label fw-medium text-dark">
                    Link Carousel Youtube <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="homepage_youtube_url" 
                    class="form-control" 
                    placeholder="https://www.youtube.com/watch?v=5CVzOqvqI38" 
                    v-model="formData.homepage_youtube_url"
                    @input="updateYoutubePreview"
                  />
                </div>

                <!-- Tiêu đề lớn -->
                <div class="mb-3">
                  <label for="homepage_hero_title" class="form-label fw-medium text-dark">
                    Tiêu Đề Thương Hiệu <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="homepage_hero_title" 
                    class="form-control" 
                    placeholder="JPM MARINE" 
                    v-model="formData.homepage_hero_title"
                  />
                </div>

                <!-- Slogan phụ -->
                <div class="mb-3">
                  <label for="homepage_hero_subtitle" class="form-label fw-medium text-dark">
                    Slogan Phụ <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="homepage_hero_subtitle" 
                    class="form-control" 
                    placeholder="PHỤ TÙNG TÀU THUYỀN NHẬT BẢN" 
                    v-model="formData.homepage_hero_subtitle"
                  />
                </div>
              </div>

              <!-- Cột Live Preview -->
              <div class="col-lg-5">
                <div class="border rounded p-3 bg-light text-center h-100 d-flex flex-column justify-content-between">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium text-dark small">
                      <i class="bx bx-tv me-1 text-primary"></i>Xem trước Video
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
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. BANNER CÁC TRANG CLIENT -->
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">Banner Các Trang Client</h5>
          </div>

          <div class="card-body pt-4">
            <!-- Danh sách từng trang tinh gọn -->
            <div 
              v-for="(banner, idx) in bannerList" 
              :key="banner.key"
              :class="{ 'border-top pt-3 mt-3': idx > 0 }"
            >
              <label class="form-label fw-medium text-dark mb-2">
                {{ banner.name }} <span class="text-danger">*</span>
              </label>

              <div class="d-flex align-items-center gap-2">
                <!-- 1. Input chọn tệp độc lập, bo tròn chuẩn form -->
                <input 
                  type="file" 
                  :id="'banner_file_' + banner.key"
                  class="form-control flex-grow-1" 
                  style="height: 38px;"
                  accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif"
                  @change="handleBannerFileUpload($event, banner.imageKey)"
                  :disabled="isUploadingBanner && currentUploadKey === banner.imageKey"
                />

                <!-- 2. Icon thùng rác xóa ảnh độc lập, vị trí cố định không nhảy bố cục -->
                <button 
                  type="button" 
                  class="btn btn-outline-danger d-flex align-items-center justify-content-center flex-shrink-0" 
                  style="width: 38px; height: 38px; padding: 0;"
                  @click="clearBannerImage(banner.imageKey, banner.key)"
                  :disabled="!formData[banner.imageKey] || (isUploadingBanner && currentUploadKey === banner.imageKey)"
                  title="Xóa ảnh tùy chỉnh (quay về mặc định)"
                >
                  <span v-if="isUploadingBanner && currentUploadKey === banner.imageKey" class="spinner-border spinner-border-sm"></span>
                  <i v-else class="bx bx-trash fs-5"></i>
                </button>

                <!-- 3. Bên phải là khung ảnh xem trước cố định -->
                <div class="flex-shrink-0">
                  <div class="border rounded bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 140px; height: 38px;">
                    <img 
                      v-if="formData[banner.imageKey] || banner.defaultImg"
                      :src="getPreviewUrl(formData[banner.imageKey] || banner.defaultImg)" 
                      alt="Preview" 
                      style="width: 100%; height: 100%; object-fit: cover;"
                      @error="$event.target.style.display = 'none'"
                      :title="formData[banner.imageKey] || banner.defaultImg"
                    />
                    <span v-else class="text-muted small"><i class="bx bx-image fs-4"></i></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. THÔNG TIN DOANH NGHIỆP & LIÊN HỆ -->
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">Thông Tin Doanh Nghiệp & Liên Hệ</h5>
          </div>

          <div class="card-body pt-4">
            <div class="row g-3">
              <!-- Tên Doanh Nghiệp -->
              <div class="col-md-6 mb-3">
                <label for="company_name" class="form-label fw-medium text-dark">
                  Tên Doanh Nghiệp <span class="text-danger">*</span>
                </label>
                <input 
                  type="text" 
                  id="company_name" 
                  class="form-control" 
                  placeholder="JPM Marine" 
                  v-model="formData.company_name"
                />
              </div>

              <!-- Hotline -->
              <div class="col-md-6 mb-3">
                <label for="company_hotline" class="form-label fw-medium text-dark">
                  Hotline CSKH <span class="text-danger">*</span>
                </label>
                <input 
                  type="text" 
                  id="company_hotline" 
                  class="form-control" 
                  placeholder="0909 123 456" 
                  v-model="formData.company_hotline"
                />
              </div>

              <!-- Email -->
              <div class="col-md-6 mb-3">
                <label for="company_email" class="form-label fw-medium text-dark">
                  Email Liên Hệ <span class="text-danger">*</span>
                </label>
                <input 
                  type="email" 
                  id="company_email" 
                  class="form-control" 
                  placeholder="contact@jpm-marine.vn" 
                  v-model="formData.company_email"
                />
              </div>

              <!-- Giờ làm việc -->
              <div class="col-md-6 mb-3">
                <label for="business_hours" class="form-label fw-medium text-dark">
                  Giờ Mở Cửa <span class="text-danger">*</span>
                </label>
                <input 
                  type="text" 
                  id="business_hours" 
                  class="form-control" 
                  placeholder="Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)" 
                  v-model="formData.business_hours"
                />
              </div>

              <!-- Địa chỉ -->
              <div class="col-12 mb-3">
                <label for="company_address" class="form-label fw-medium text-dark">
                  Địa Chỉ Doanh Nghiệp <span class="text-danger">*</span>
                </label>
                <input 
                  type="text" 
                  id="company_address" 
                  class="form-control" 
                  placeholder="123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh" 
                  v-model="formData.company_address"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. MẠNG XÃ HỘI -->
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">Liên Kết Mạng XÃ Hội</h5>
          </div>

          <div class="card-body pt-4">
            <div class="row g-3">
              <!-- Facebook -->
              <div class="col-md-6 mb-3">
                <label for="social_facebook" class="form-label fw-medium text-dark">
                  Facebook Fanpage <span class="text-danger">*</span>
                </label>
                <input 
                  type="url" 
                  id="social_facebook" 
                  class="form-control" 
                  placeholder="https://facebook.com/..." 
                  v-model="formData.social_facebook"
                />
              </div>

              <!-- YouTube Channel -->
              <div class="col-md-6 mb-3">
                <label for="social_youtube" class="form-label fw-medium text-dark">
                  Kênh YouTube <span class="text-danger">*</span>
                </label>
                <input 
                  type="url" 
                  id="social_youtube" 
                  class="form-control" 
                  placeholder="https://youtube.com/@..." 
                  v-model="formData.social_youtube"
                />
              </div>

              <!-- Zalo -->
              <div class="col-md-6 mb-3">
                <label for="social_zalo" class="form-label fw-medium text-dark">
                  Zalo <span class="text-danger">*</span>
                </label>
                <input 
                  type="url" 
                  id="social_zalo" 
                  class="form-control" 
                  placeholder="https://zalo.me/..." 
                  v-model="formData.social_zalo"
                />
              </div>

              <!-- Instagram -->
              <div class="col-md-6 mb-3">
                <label for="social_instagram" class="form-label fw-medium text-dark">
                  Instagram <span class="text-danger">*</span>
                </label>
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

      <!-- 5. TÀI KHOẢN NGÂN HÀNG & MÃ VIETQR NHẬN CỌC -->
      <div class="col-12 mb-4" style="position: relative; z-index: 5;">
        <div class="card" style="overflow: visible;">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">Tài Khoản Ngân Hàng & Mã VietQR Nhận Cọc</h5>
          </div>

          <div class="card-body pt-4">
            <div class="row g-4">
              <!-- Cột nhập liệu thông tin ngân hàng -->
              <div class="col-lg-7">
                <!-- Chọn Ngân Hàng -->
                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">
                    Ngân Hàng Thụ Hưởng <span class="text-danger">*</span>
                  </label>
                  <v-select 
                    v-model="formData.vietqr_bank_id"
                    :options="bankOptions"
                    :searchable="true"
                    :can-clear="false"
                    placeholder="Tìm hoặc chọn ngân hàng..."
                  />
                </div>

                <!-- Số Tài Khoản -->
                <div class="mb-3">
                  <label for="vietqr_account_no" class="form-label fw-medium text-dark">
                    Số Tài Khoản Nhận Tiền <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="vietqr_account_no" 
                    class="form-control" 
                    placeholder="VD: 108876543210" 
                    v-model="formData.vietqr_account_no"
                  />
                </div>

                <!-- Tên Chủ Tài Khoản -->
                <div class="mb-3">
                  <label for="vietqr_account_name" class="form-label fw-medium text-dark">
                    Tên Chủ Tài Khoản (In hoa không dấu) <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="vietqr_account_name" 
                    class="form-control text-uppercase" 
                    placeholder="VD: CONG TY PHU TUNG MAY THUY JPM" 
                    v-model="formData.vietqr_account_name"
                  />
                </div>

                <!-- Tiền Tố Nội Dung Chuyển Khoản -->
                <div class="mb-3">
                  <label for="vietqr_prefix" class="form-label fw-medium text-dark">
                    Tiền Tố Nội Dung Chuyển Khoản <span class="text-danger">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="vietqr_prefix" 
                    class="form-control text-uppercase" 
                    placeholder="JPM hoặc COC" 
                    v-model="formData.vietqr_prefix"
                  />
                </div>

                <!-- Kiểu hiển thị mã QR (Template) -->
                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">
                    Mẫu Hiển Thị Khung QR <span class="text-danger">*</span>
                  </label>
                  <v-select 
                    v-model="formData.vietqr_template"
                    :options="templateOptions"
                    :can-clear="false"
                    placeholder="Chọn kiểu dáng khung QR..."
                  />
                </div>
              </div>

              <!-- Cột Live Preview Quét Thử Mã QR -->
              <div class="col-lg-5">
                <div class="border rounded p-3 bg-light text-center h-100 d-flex flex-column align-items-center">
                  <!-- Tiêu đề cột xem trước -->
                  <div class="d-flex justify-content-between align-items-center w-100 mb-3 pb-2 border-bottom">
                    <span class="fw-medium text-dark small">
                      <i class="bx bx-qr-scan me-1 text-primary"></i>Xem trước Mã VietQR Thực Tế
                    </span>
                    <span class="badge bg-label-primary">{{ formData.vietqr_bank_id || 'ICB' }}</span>
                  </div>

                  <!-- Khung hiển thị ảnh QR tự sinh -->
                  <div class="bg-white p-2 rounded shadow-sm border mb-3 d-inline-block">
                    <img 
                      :src="previewQrUrl" 
                      alt="Preview VietQR Napas" 
                      class="img-fluid rounded"
                      style="max-height: 285px; width: 100%; max-width: 255px; object-fit: contain;"
                    />
                  </div>

                  <!-- Bảng thông tin tóm tắt -->
                  <div class="text-start small bg-white p-3 rounded border w-100 mb-2">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                      <span class="text-muted">Ngân hàng:</span>
                      <span class="fw-bold text-dark">{{ formData.vietqr_bank_name || 'VietinBank' }} ({{ formData.vietqr_bank_id || 'ICB' }})</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                      <span class="text-muted">Số tài khoản:</span>
                      <span class="fw-bold text-primary">{{ formData.vietqr_account_no || 'Chưa nhập' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                      <span class="text-muted">Chủ tài khoản:</span>
                      <span class="fw-bold text-dark">{{ (formData.vietqr_account_name || 'Chưa nhập').toUpperCase() }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                      <span class="text-muted">Nội dung mẫu:</span>
                      <span class="fw-bold text-danger">{{ (formData.vietqr_prefix || 'COC').trim().toUpperCase() }} YCBG-DEMO</span>
                    </div>
                  </div>

                  <!-- Ghi chú quét thử -->
                  <small class="text-muted mt-auto pt-2 d-block text-center" style="font-size: 0.8rem;">
                    Quét thử bằng App ngân hàng để kiểm tra trước khi Lưu.
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 6. HIỆU NĂNG & BỘ NHỚ ĐỆM (CACHE) -->
      <div class="col-12 mb-4" style="position: relative; z-index: 1;">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">Bộ Nhớ Đệm & Hiệu Năng (Cache)</h5>
          </div>

          <div class="card-body pt-4">
            <!-- Mô tả kiến trúc cache -->
            <div class="mb-3">
              <p class="text-dark fw-medium mb-1">
                Kiến trúc bộ nhớ đệm: <span class="text-primary fw-semibold">Failover Cache (Redis &rarr; File Driver &rarr; Memory Array)</span>
              </p>
              <p class="text-muted small mb-0" style="line-height: 1.6;">
                Hệ thống tự động lưu trữ sẵn danh mục Thương hiệu, Loại động cơ, Model và Nhóm phụ tùng vào bộ nhớ đệm tốc độ cao, giúp giảm tới <strong>95%</strong> áp lực truy vấn cơ sở dữ liệu.
              </p>
            </div>

            <!-- Thống kê mục đã cache -->
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

            <!-- Thanh công cụ thao tác chuẩn Sneat Admin -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
              <div class="text-muted small">
                Khuyến nghị bấm <strong>Làm Ấm Cache</strong> sau khi xóa trắng để người dùng tra cứu mượt mà nhất.
              </div>
              <div class="d-flex gap-2">
                <button 
                  type="button" 
                  class="btn btn-outline-danger px-4 shadow-sm"
                  @click="handleClearCache(false)"
                  :disabled="isClearingCache"
                >
                  <i class="bx bx-trash me-1"></i> Xóa Trắng Cache
                </button>
                <button 
                  type="button" 
                  class="btn btn-primary px-4 shadow-sm"
                  @click="handleClearCache(true)"
                  :disabled="isClearingCache"
                >
                  <span v-if="isClearingCache" class="spinner-border spinner-border-sm me-1" role="status"></span>
                  <i v-else class="bx bx-bolt-circle me-1"></i>
                  {{ isClearingCache ? 'Đang làm ấm...' : 'Làm Ấm Cache' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Nút Lưu / Đặt Lại Ở Cuối Trang -->
      <div class="col-12">
        <div class="d-flex justify-content-end gap-2 mb-4">
          <button 
            type="button" 
            class="btn btn-outline-secondary px-4"
            @click="resetToInitial"
            :disabled="isSubmitting"
          >
            Đặt lại
          </button>
          <button 
            type="button" 
            class="btn btn-primary px-4 shadow-sm"
            @click="saveSettings"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            <i v-else class="bx bx-save me-1"></i>
            {{ isSubmitting ? 'Đang Lưu...' : 'Lưu Thay Đổi' }}
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '@meforma/vue-toaster';
import { compressImage } from '../../../utils/imageOptimizer';

export default {
  name: "CauHinhHeThong",
  setup() {
    const queryClient = useQueryClient();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    const isSubmitting = ref(false);

    // Danh sách các ngân hàng phổ biến tại Việt Nam theo chuẩn Napas247 (Chuẩn v-select options)
    const bankOptions = [
      { value: 'ICB', shortName: 'VietinBank', label: 'VietinBank - Ngân hàng TMCP Công thương Việt Nam' },
      { value: 'VCB', shortName: 'Vietcombank', label: 'Vietcombank - Ngân hàng TMCP Ngoại thương Việt Nam' },
      { value: 'MB', shortName: 'MBBank', label: 'MBBank - Ngân hàng TMCP Quân Đội' },
      { value: 'TCB', shortName: 'Techcombank', label: 'Techcombank - Ngân hàng TMCP Kỹ Thương' },
      { value: 'BIDV', shortName: 'BIDV', label: 'BIDV - Ngân hàng Đầu tư và Phát triển VN' },
      { value: 'VBA', shortName: 'Agribank', label: 'Agribank - Ngân hàng Nông nghiệp & PT Nông thôn' },
      { value: 'ACB', shortName: 'ACB', label: 'ACB - Ngân hàng TMCP Á Châu' },
      { value: 'VPB', shortName: 'VPBank', label: 'VPBank - Ngân hàng TMCP Việt Nam Thịnh Vượng' },
      { value: 'STB', shortName: 'Sacombank', label: 'Sacombank - Ngân hàng Sài Gòn Thương Tín' },
      { value: 'TPB', shortName: 'TPBank', label: 'TPBank - Ngân hàng TMCP Tiên Phong' },
      { value: 'VIB', shortName: 'VIB', label: 'VIB - Ngân hàng Quốc tế Việt Nam' },
      { value: 'SHB', shortName: 'SHB', label: 'SHB - Ngân hàng TMCP Sài Gòn - Hà Nội' },
      { value: 'HDB', shortName: 'HDBank', label: 'HDBank - Ngân hàng TMCP Phát triển TP.HCM' },
      { value: 'MSB', shortName: 'MSB', label: 'MSB - Ngân hàng TMCP Hàng Hải Việt Nam' },
      { value: 'OCB', shortName: 'OCB', label: 'OCB - Ngân hàng TMCP Phương Đông' },
      { value: 'LPB', shortName: 'LPBank', label: 'LPBank - Ngân hàng TMCP Lộc Phát Việt Nam' },
      { value: 'SEAB', shortName: 'SeABank', label: 'SeABank - Ngân hàng TMCP Đông Nam Á' }
    ];

    const templateOptions = [
      { value: 'compact2', label: 'Chuẩn đầy đủ: Khung viền + Logo Napas + Logo Ngân Hàng (Khuyên dùng)' },
      { value: 'compact', label: 'Gọn nhẹ: Kèm logo ngân hàng thu nhỏ' },
      { value: 'qr_only', label: 'Đơn giản: Chỉ hiển thị mã QR vuông' }
    ];

    // Initial default state
    const defaultData = {
      homepage_youtube_url: 'https://www.youtube.com/watch?v=5CVzOqvqI38',
      homepage_hero_title: 'JPM MARINE',
      homepage_hero_subtitle: 'PHỤ TÙNG TÀU THUYỀN NHẬT BẢN',
      store_banner_image: '/client/image/shop_banner.png',
      store_banner_title: 'Cửa Hàng Phụ Tùng',
      store_banner_subtitle: 'Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.',
      lookup_banner_image: '',
      lookup_banner_title: 'Tra Cứu Phụ Tùng',
      lookup_banner_subtitle: 'Lựa chọn chính xác phụ tùng thay thế và sơ đồ rã máy cho động cơ thủy của bạn.',
      news_banner_image: '/client/image/news_banner.png',
      news_banner_title: 'Tin Tức & Cẩm Nang',
      news_banner_subtitle: 'Tổng hợp những kiến thức chuyên sâu về vận hành tàu thuyền, cẩm nang bảo dưỡng động cơ thủy và xu hướng công nghệ hàng hải.',
      support_banner_image: '',
      support_banner_title: 'Trung Tâm Hỗ Trợ & Kỹ Thuật',
      support_banner_subtitle: 'Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM và hỗ trợ 24/7.',
      company_name: 'JPM Marine',
      company_address: '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh, Việt Nam',
      company_hotline: '0909 123 456',
      company_email: 'contact@jpm-marine.vn',
      business_hours: 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)',
      social_facebook: 'https://facebook.com/jpmmarine',
      social_youtube: 'https://youtube.com/@jpmmarine',
      social_zalo: 'https://zalo.me/0909123456',
      social_instagram: 'https://instagram.com/jpmmarine',
      vietqr_bank_id: 'ICB',
      vietqr_bank_name: 'VietinBank',
      vietqr_account_no: '108876543210',
      vietqr_account_name: 'CONG TY PHU TUNG MAY THUY JPM',
      vietqr_template: 'compact2',
      vietqr_prefix: 'COC'
    };

    const formData = reactive({ ...defaultData });
    const initialSnapshot = ref({});

    // Tự động đồng bộ tên ngân hàng khi đổi mã ngân hàng
    watch(() => formData.vietqr_bank_id, (newBankId) => {
      const selected = bankOptions.find(b => b.value === newBankId);
      if (selected) {
        formData.vietqr_bank_name = selected.shortName;
      }
    });

    // Live preview URL mã VietQR
    const previewQrUrl = computed(() => {
      const bankId = formData.vietqr_bank_id || 'ICB';
      const accountNo = (formData.vietqr_account_no || '').trim() || '108876543210';
      const template = formData.vietqr_template || 'compact2';
      const accountName = encodeURIComponent((formData.vietqr_account_name || 'CONG TY PHU TUNG MAY THUY JPM').trim().toUpperCase());
      const prefix = (formData.vietqr_prefix || 'COC').trim().toUpperCase();
      const addInfo = encodeURIComponent(`${prefix} YCBG-DEMO`);
      return `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=1000000&addInfo=${addInfo}&accountName=${accountName}`;
    });

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

    const isUploadingBanner = ref(false);
    const currentUploadKey = ref('');
    const bannerFileInput = ref(null);

    const bannerList = [
      {
        key: 'store',
        name: 'Banner Cửa Hàng',
        icon: 'bx-store',
        imageKey: 'store_banner_image',
        defaultImg: '/client/image/shop_banner.png'
      },
      {
        key: 'lookup',
        name: 'Banner Tra Cứu',
        icon: 'bx-search-alt',
        imageKey: 'lookup_banner_image',
        defaultImg: ''
      },
      {
        key: 'news',
        name: 'Banner Tin Tức',
        icon: 'bx-news',
        imageKey: 'news_banner_image',
        defaultImg: '/client/image/news_banner.png'
      },
      {
        key: 'support',
        name: 'Banner Hỗ Trợ',
        icon: 'bx-support',
        imageKey: 'support_banner_image',
        defaultImg: ''
      }
    ];

    const handleBannerFileUpload = async (event, key) => {
      const file = event.target.files?.[0];
      if (!file) return;

      isUploadingBanner.value = true;
      currentUploadKey.value = key;
      try {
        const compressed = await compressImage(file, { maxWidth: 1920, maxHeight: 600, quality: 0.88 });
        const uploadForm = new FormData();
        uploadForm.append('file', compressed);

        const res = await axiosClient.post('/admin/cau-hinh/upload-banner', uploadForm, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        const payload = res?.data || res;
        if (payload?.success && payload.url) {
          formData[key] = payload.url;
          toaster.success('Đã tải ảnh banner lên thành công!');
        }
      } catch (err) {
        toaster.error(err.response?.data?.message || 'Lỗi khi tải ảnh banner lên');
      } finally {
        isUploadingBanner.value = false;
        currentUploadKey.value = '';
        event.target.value = '';
      }
    };

    const clearBannerImage = (imageKey, bannerKey) => {
      formData[imageKey] = '';
      const fileInput = document.getElementById('banner_file_' + bannerKey);
      if (fileInput) fileInput.value = '';
    };

    const getPreviewUrl = (url) => {
      if (!url) return '';
      if (url.startsWith('http') || url.startsWith('blob:') || url.startsWith('data:') || url.startsWith('/client/') || url.startsWith('/assets/')) {
        return url;
      }
      const clean = url.startsWith('/') ? url : '/' + url;
      const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/api\/?$/, '');
      return `${baseUrl}${clean}`;
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
      handleClearCache,
      bankOptions,
      templateOptions,
      previewQrUrl,
      isUploadingBanner,
      currentUploadKey,
      bannerList,
      handleBannerFileUpload,
      clearBannerImage,
      getPreviewUrl
    };
  }
};
</script>

<style scoped>
</style>
