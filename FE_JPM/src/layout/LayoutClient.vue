<template>
  <div class="index-page">

    <NavbarClient />

    <main class="main d-flex flex-column">
      <router-view />
    </main>
  <!-- Floating Contact Action Buttons -->
  <div class="floating-contact-actions">
    <div v-if="settings.social_instagram" class="contact-btn-wrap instagram-wrap">
      <div class="contact-btn-tooltip">Instagram</div>
      <a :href="settings.social_instagram" target="_blank" rel="noopener noreferrer" class="contact-btn instagram">
        <i class="bi bi-instagram"></i>
      </a>
    </div>

    <div v-if="settings.social_facebook" class="contact-btn-wrap facebook-wrap">
      <div class="contact-btn-tooltip">Facebook Fanpage</div>
      <a :href="settings.social_facebook" target="_blank" rel="noopener noreferrer" class="contact-btn facebook">
        <i class="bi bi-facebook"></i>
      </a>
    </div>

    <div v-if="settings.social_zalo" class="contact-btn-wrap zalo-wrap">
      <div class="contact-btn-tooltip">Chat Zalo</div>
      <a :href="settings.social_zalo" target="_blank" rel="noopener noreferrer" class="contact-btn zalo">
        <span style="font-weight: 900; font-size: 13px; font-family: Arial, sans-serif;">Zalo</span>
      </a>
    </div>

    <div v-if="settings.company_hotline" class="contact-btn-wrap phone-wrap">
      <div class="contact-btn-tooltip">Gọi Ngay: {{ settings.company_hotline }}</div>
      <a :href="'tel:' + settings.company_hotline.replace(/\s+/g, '')" class="contact-btn phone">
        <i class="bi bi-telephone-fill"></i>
      </a>
    </div>
  </div>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" :class="{ 'active': isScrolled }" @click.prevent="scrollToTop">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Preloader -->
    <FooterClient />
    <div id="preloader" v-if="isLoading"></div>
  </div>
</template>

<script>
import NavbarClient from '../components/client/NavbarClient.vue'
import FooterClient from '../components/client/FooterClient.vue'



import AOS from 'aos'
import 'aos/dist/aos.css'
window.AOS = AOS

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


import { useSiteSettings } from '../composables/useSiteSettings'

export default {
  name: 'LayoutClient',

  components: {
    NavbarClient,
    FooterClient
  },
  setup() {
    const { settings } = useSiteSettings();
    return { settings };
  },
  data() {
    return {
      isScrolled: false,
      isLoading: false
    }
  },
  methods: {
    handleScroll() {
      this.isScrolled = window.scrollY > 100;
    },
    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
    
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    })

    import('../assets/client/js/main.js').then(() => { setTimeout(() => { this.isLoading = false; window.dispatchEvent(new Event('load')); }, 100); });
  },
  unmounted() {
    window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>

<style>
/* Fonts */
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import url('https://fonts.gstatic.com');

/* Floating Contact Actions */
.floating-contact-actions {
  position: fixed;
  right: 15px;
  bottom: 85px; /* Above the scroll-to-top button */
  display: flex;
  flex-direction: column;
  gap: 12px;
  z-index: 996;
}

.contact-btn-wrap {
  position: relative;
}

.contact-btn {
  position: relative;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 22px;
  text-decoration: none;
  z-index: 2; /* above the ripples */
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.contact-btn:hover {
  color: #fff;
}

/* Monochrome by default (Luxury UI) */

  .contact-btn.phone { background-color: #00a65a; }
  .contact-btn.zalo { background-color: #0068ff; }
  .contact-btn.facebook { background-color: #1877f2; }
  .contact-btn.instagram { 
    background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); 
  }


/* Reveal colors on hover */
.contact-btn.phone:hover { box-shadow: 0 4px 15px rgba(0, 166, 90, 0.4); }
.contact-btn.zalo:hover { box-shadow: 0 4px 15px rgba(0, 104, 255, 0.4); }
.contact-btn.facebook:hover { box-shadow: 0 4px 15px rgba(24, 119, 242, 0.4); }
.contact-btn.instagram:hover { box-shadow: 0 4px 15px rgba(214, 36, 159, 0.5); }

/* Radar Ripple Effect (ONLY FOR PHONE) */
.contact-btn-wrap.phone-wrap::before, .contact-btn-wrap.phone-wrap::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  z-index: 1;
  opacity: 0;
  background-color: #00a65a;
}
.contact-btn-wrap.phone-wrap::before {
  animation: radar-pulse 2s infinite ease-out;
}
.contact-btn-wrap.phone-wrap::after {
  animation: radar-pulse 2s infinite ease-out 1s; /* stagger */
}

@keyframes radar-pulse {
  0% { transform: translate(-50%, -50%) scale(1); opacity: 0.6; }
  100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
}

/* Tooltip on hover */
.contact-btn-tooltip {
  position: absolute;
  right: 60px;
  top: 50%;
  transform: translateY(-50%) scale(0.8);
  background-color: #333;
  color: #fff;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  z-index: 3;
}
.contact-btn-tooltip::after {
  content: "";
  position: absolute;
  right: -5px;
  top: 50%;
  transform: translateY(-50%);
  border-width: 5px 0 5px 6px;
  border-style: solid;
  border-color: transparent transparent transparent #333;
}
.contact-btn-wrap:hover .contact-btn-tooltip {
  opacity: 1;
  visibility: visible;
  transform: translateY(-50%) scale(1);
}
.contact-btn-wrap:hover .contact-btn {
  transform: scale(1.1) rotate(5deg);
}

/* Shake animation for phone */
@keyframes phone-shake {
  0% { transform: rotate(0) scale(1); }
  10% { transform: rotate(-15deg) scale(1.1); }
  20% { transform: rotate(15deg) scale(1.1); }
  30% { transform: rotate(-15deg) scale(1.1); }
  40% { transform: rotate(15deg) scale(1.1); }
  50% { transform: rotate(0) scale(1); }
  100% { transform: rotate(0) scale(1); }
}
.contact-btn-wrap.phone-wrap .contact-btn i {
  display: inline-block;
  animation: phone-shake 3s infinite ease-in-out;
}
</style>

<style scoped>
/* Đảm bảo khung nội dung luôn chiếm tối thiểu chiều cao màn hình để Footer không bị nhảy lên trên trong lúc load trang */
.main {
  min-height: 85vh;
}
</style>









