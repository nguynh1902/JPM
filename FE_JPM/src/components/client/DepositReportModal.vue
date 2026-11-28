<template>
  <template v-if="show">
    <div class="modal-backdrop fade show" style="z-index: 1060;" @click="handleClose"></div>
    <div
      class="modal fade show d-block"
      tabindex="-1"
      style="z-index: 1065;"
      @click.self="handleClose"
    >
      <div class="modal-dialog modal-dialog-centered" style="max-width: 470px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
          <div class="modal-header bg-white border-bottom px-4 py-3">
            <div>
              <h5 class="modal-title fw-bold mb-0" style="color: #0A1128; font-size: 1.05rem;">
                Xác nhận đã chuyển khoản cọc
              </h5>
              <div class="text-muted small mt-1">
                Mã giao dịch: <strong class="text-primary font-monospace">#{{ code }}</strong>
              </div>
            </div>
            <button type="button" class="btn-close" :disabled="submitting" @click="handleClose" aria-label="Đóng"></button>
          </div>

          <div class="modal-body px-4 py-3">
            <div class="rounded-3 p-3 mb-3 border" style="background: #f8fafc;">
              <div class="d-flex justify-content-between align-items-center mb-1 small">
                <span class="text-muted">Số tiền cọc</span>
                <strong class="text-danger fs-6">{{ amountFormatted }}</strong>
              </div>
              <div v-if="transferContent" class="d-flex justify-content-between align-items-center small">
                <span class="text-muted">Nội dung CK</span>
                <code class="lux-transfer-code">{{ transferContent }}</code>
              </div>
            </div>

            <div class="mb-2 d-flex justify-content-between align-items-center">
              <label class="form-label fw-semibold mb-0 small text-dark">
                Ảnh biên lai / màn hình chuyển khoản
              </label>
              <span class="text-muted" style="font-size: 0.74rem;">Không bắt buộc</span>
            </div>

            <input
              ref="fileInput"
              type="file"
              class="d-none"
              accept="image/*,.jpg,.jpeg,.png,.webp"
              @change="onFilePicked"
            />

            <div
              class="deposit-dropzone rounded-3 border bg-light position-relative d-flex flex-column align-items-center justify-content-center p-3 text-center"
              :class="{ 'has-preview': Boolean(previewUrl) }"
              tabindex="0"
              @click="!previewUrl && !submitting ? triggerFilePick() : null"
              @paste="onPasteImage"
            >
              <template v-if="previewUrl">
                <img
                  :src="previewUrl"
                  alt="Ảnh biên lai chuyển khoản"
                  class="rounded border bg-white"
                  style="max-height: 185px; max-width: 100%; object-fit: contain;"
                />
                <button
                  type="button"
                  class="btn btn-sm bg-white text-danger border rounded-circle shadow-sm position-absolute top-0 end-0 m-2"
                  style="width: 28px; height: 28px; padding: 0; line-height: 1;"
                  :disabled="submitting"
                  @click.stop="clearSelectedFile"
                  title="Xóa ảnh"
                >
                  <i class="bi bi-trash3"></i>
                </button>
              </template>

              <template v-else>
                <i class="bi bi-cloud-arrow-up text-secondary fs-2 mb-1"></i>
                <div class="fw-medium text-dark small">
                  Nhấn để chọn ảnh hoặc dán ảnh (Ctrl+V)
                </div>
                <div class="text-muted" style="font-size: 0.75rem;">
                  Hỗ trợ JPG, PNG, WebP (tối đa 5MB)
                </div>
              </template>
            </div>

            <div class="text-muted mt-2" style="font-size: 0.76rem; line-height: 1.45;">
              Nếu chưa tiện gửi ảnh lúc này, bạn vẫn có thể bấm <strong>Xác nhận ngay</strong> — kỹ thuật viên sẽ kiểm tra trực tiếp tài khoản Sacombank và lên đơn cho bạn.
            </div>
          </div>

          <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary px-3" :disabled="submitting" @click="handleClose">
              Đóng
            </button>
            <button type="button" class="btn btn-danger px-4 fw-semibold" :disabled="submitting" @click="submitReport">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <span>{{ submitting ? 'Đang gửi...' : (selectedFile ? 'Gửi biên lai & Xác nhận' : 'Xác nhận đã chuyển khoản') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { compressReceipt } from '../../utils/imageOptimizer';

const props = defineProps({
  show: { type: Boolean, default: false },
  code: { type: String, default: '' },
  amountFormatted: { type: String, default: '0 đ' },
  transferContent: { type: String, default: '' },
  submitting: { type: Boolean, default: false }
});

const emit = defineEmits(['close', 'submit']);

const fileInput = ref(null);
const selectedFile = ref(null);
const previewUrl = ref('');

const revokePreview = () => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = '';
  }
};

const clearSelectedFile = () => {
  selectedFile.value = null;
  revokePreview();
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

watch(() => props.show, (visible) => {
  if (!visible) {
    clearSelectedFile();
  }
});

onBeforeUnmount(() => {
  revokePreview();
});

const processPickedFile = async (rawFile) => {
  if (!rawFile) return;
  try {
    const { file: optimizedFile } = await compressReceipt(rawFile);
    revokePreview();
    selectedFile.value = optimizedFile || rawFile;
    previewUrl.value = URL.createObjectURL(selectedFile.value);
  } catch {
    revokePreview();
    selectedFile.value = rawFile;
    previewUrl.value = URL.createObjectURL(rawFile);
  }
};

const triggerFilePick = () => {
  fileInput.value?.click();
};

const onFilePicked = async (event) => {
  const file = event?.target?.files?.[0];
  if (file) {
    await processPickedFile(file);
  }
};

const onPasteImage = async (event) => {
  const items = event.clipboardData?.items;
  if (!items) return;
  for (const item of items) {
    if (item.type.startsWith('image/')) {
      event.preventDefault();
      const file = item.getAsFile();
      if (file) await processPickedFile(file);
      break;
    }
  }
};

const handleClose = () => {
  if (props.submitting) return;
  emit('close');
};

const submitReport = () => {
  if (props.submitting) return;
  emit('submit', selectedFile.value || null);
};
</script>

<style scoped>
.deposit-dropzone {
  min-height: 132px;
  border-style: dashed !important;
  border-color: #cbd5e1 !important;
  cursor: pointer;
  transition: border-color 0.16s ease, background-color 0.16s ease;
}

.deposit-dropzone:hover,
.deposit-dropzone:focus {
  border-color: #F82249 !important;
  background-color: #fff5f7 !important;
  outline: none;
}

.deposit-dropzone.has-preview {
  border-style: solid !important;
  border-color: #e2e8f0 !important;
  cursor: default;
}
</style>
