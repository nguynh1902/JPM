<template>
  <div class="modal fade" :id="id" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center pt-2 pb-4">
          <div class="mb-3">
            <i class="bx bx-error-circle text-danger" style="font-size: 5rem;"></i>
          </div>
          <h5 class="modal-title mb-2 fw-bold text-dark">{{ title }}</h5>
          <p class="mb-0 fs-6 text-muted">{{ message }}</p>
        </div>
        <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
          <button type="button" class="btn btn-label-secondary px-4 me-2" data-bs-dismiss="modal">
            {{ cancelText || 'Hủy bỏ' }}
          </button>
          <button type="button" :class="['btn px-4 shadow-sm', confirmColor || 'btn-danger']" @click="onConfirm" data-bs-dismiss="modal">
            {{ confirmText || 'Đồng ý' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'ConfirmModal',
  props: {
    id: { type: String, required: true },
    title: { type: String, default: 'Xác nhận' },
    message: { type: String, required: true },
    confirmText: { type: String, default: 'Xác nhận' },
    cancelText: { type: String, default: 'Hủy' },
    confirmColor: { type: String, default: 'btn-danger' }
  },
  methods: {
    onConfirm() {
      this.$emit('confirm');
    }
  }
}
</script>
