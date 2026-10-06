<template>
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
      Hiển thị {{ totalItems === 0 ? 0 : startIndex }} đến {{ endIndex }} trong số {{ totalItems }} bản ghi
    </div>
    <nav aria-label="Page navigation" v-if="totalPages > 1">
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item prev" :class="{ disabled: currentPage === 1 }">
          <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)"><i class="tf-icon bx bx-chevrons-left"></i></a>
        </li>
        <li class="page-item" v-for="page in pages" :key="page" :class="{ active: currentPage === page }">
          <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
        </li>
        <li class="page-item next" :class="{ disabled: currentPage === totalPages }">
          <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)"><i class="tf-icon bx bx-chevrons-right"></i></a>
        </li>
      </ul>
    </nav>
  </div>
</template>
<script>
export default {
  name: 'Pagination',
  props: {
    currentPage: { type: Number, required: true },
    totalPages: { type: Number, required: true },
    totalItems: { type: Number, required: true },
    itemsPerPage: { type: Number, default: 5 }
  },
  computed: {
    startIndex() { return (this.currentPage - 1) * this.itemsPerPage + 1; },
    endIndex() { return Math.min(this.currentPage * this.itemsPerPage, this.totalItems); },
    pages() {
      const p = [];
      for(let i=1; i<=this.totalPages; i++) p.push(i);
      return p;
    }
  },
  methods: {
    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.$emit('page-changed', page);
      }
    }
  }
}
</script>
