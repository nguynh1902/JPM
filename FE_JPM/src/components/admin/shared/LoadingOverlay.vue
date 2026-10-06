<template>
  <div v-if="isLoading" class="loading-overlay">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
</template>
<script>
export default {
  name: 'LoadingOverlay',
  props: {
    isLoading: { type: Boolean, default: false }
  }
}
</script>
<style scoped>
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.6);
  z-index: 10;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 0.5rem;
}
</style>
