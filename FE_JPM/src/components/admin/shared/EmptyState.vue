<template>
  <div class="text-center py-5">
    <div class="mb-3">
      <i class="bx bx-folder-open text-muted" style="font-size: 3.5rem; opacity: 0.5;"></i>
    </div>
    <h6 class="text-muted fw-normal mb-0">{{ message || 'Chưa có dữ liệu' }}</h6>
  </div>
</template>
<script>
export default {
  name: 'EmptyState',
  props: {
    message: String
  }
}
</script>
