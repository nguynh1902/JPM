<template>
  <div class="index-page">
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

import '../assets/client/vendor/bootstrap/css/bootstrap.min.css'
import '../assets/client/vendor/bootstrap-icons/bootstrap-icons.css'

import '../assets/client/vendor/glightbox/css/glightbox.min.css'
import '../assets/client/vendor/swiper/swiper-bundle.min.css'

import '../assets/client/css/main.css'

import '../assets/client/vendor/bootstrap/js/bootstrap.bundle.min.js'

import '../assets/client/vendor/glightbox/js/glightbox.min.js'
import '../assets/client/vendor/php-email-form/validate.js'


export default {
  name: 'LayoutAuth',
    mounted() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    })

    import('../assets/client/js/main.js')

  }
}
</script>

<style>


/* Fonts */
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import url('https://fonts.gstatic.com');
@import url('https://fonts.googleapis.com');
</style>