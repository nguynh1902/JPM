<template>
  <div class="blank-layout-root index-page">
    <router-view />
  </div>
</template>

<script>
import { loadClientCSS, unloadClientCSS } from '../utils/clientCssLoader';

export default {
  name: 'LayoutBlank',
  beforeCreate() {
    loadClientCSS();
  },
  beforeMount() {
    loadClientCSS();
  },
  unmounted() {
    if (this.$route.meta?.layout === 'admin') {
      unloadClientCSS();
    }
  }
};
</script>

<style scoped>
.blank-layout-root {
  min-height: 100vh;
  background: #f1f5f9;
}
</style>
