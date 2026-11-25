<template>
  <div class="index-page auth-page">
    <main class="main">
      <router-view />
    </main>
  </div>
</template>

<script>
import AOS from 'aos'
import 'aos/dist/aos.css'

import GLightbox from 'glightbox'
window.GLightbox = GLightbox

import Swiper from 'swiper/bundle'
import 'swiper/css/bundle'
window.Swiper = Swiper

// Singleton Dynamic CSS Loader
import { loadClientCSS, unloadClientCSS } from '../utils/clientCssLoader';

import '../assets/client/vendor/bootstrap/js/bootstrap.bundle.min.js'
import '../assets/client/vendor/glightbox/js/glightbox.min.js'
import '../assets/client/vendor/php-email-form/validate.js'

export default {
  name: 'LayoutAuth',
  beforeCreate() {
    loadClientCSS();
  },
  mounted() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });

    loadClientCSS().finally(() => {
      import('../assets/client/js/main.js')
        .catch(err => console.warn('Client auth script warning:', err));
    });
  },
  unmounted() {
    if (this.$route.meta?.layout === 'admin') {
      unloadClientCSS();
    }
  }
}
</script>

<style>
/* Font chuẩn hóa duy nhất toàn diện cho Client Auth: Inter */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
</style>