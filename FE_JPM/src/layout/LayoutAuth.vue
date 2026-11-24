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

// Dynamic CSS URLs - Cô lập 100% tài nguyên Client, không rò rỉ sang Admin
import bootstrapCss from '../assets/client/vendor/bootstrap/css/bootstrap.min.css?url';
import bootstrapIconsCss from '../assets/client/vendor/bootstrap-icons/bootstrap-icons.css?url';
import glightboxCss from '../assets/client/vendor/glightbox/css/glightbox.min.css?url';
import swiperCss from '../assets/client/vendor/swiper/swiper-bundle.min.css?url';
import mainCss from '../assets/client/css/main.css?url';

import '../assets/client/vendor/bootstrap/js/bootstrap.bundle.min.js'
import '../assets/client/vendor/glightbox/js/glightbox.min.js'
import '../assets/client/vendor/php-email-form/validate.js'

const clientCssFiles = [
  { href: bootstrapCss, class: "client-bootstrap-css" },
  { href: bootstrapIconsCss, class: "client-icons-css" },
  { href: glightboxCss, class: "" },
  { href: swiperCss, class: "" },
  { href: mainCss, class: "client-main-css" },
];

const loadClientCSS = () => {
  return Promise.all(
    clientCssFiles.map((file) => {
      return new Promise((resolve, reject) => {
        if (document.querySelector(`link[href="${file.href}"]`)) {
          return resolve();
        }
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = file.href;
        if (file.class) link.classList.add(file.class);
        link.setAttribute("data-layout", "client");
        link.onload = resolve;
        link.onerror = reject;
        document.head.appendChild(link);
      });
    })
  );
};

export default {
  name: 'LayoutAuth',
  mounted() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });

    loadClientCSS().finally(() => {
      import('../assets/client/js/main.js');
    });
  },
  unmounted() {
    document.querySelectorAll('link[data-layout="client"]').forEach(el => el.remove());
  }
}
</script>

<style>
/* Fonts */
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import url('https://fonts.gstatic.com');
@import url('https://fonts.googleapis.com');
</style>