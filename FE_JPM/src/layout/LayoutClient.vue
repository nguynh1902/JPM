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
      <a :href="settings.social_facebook" target="_blank" rel="noopener noreferrer" class="contact-btn facebook" aria-label="Facebook Fanpage">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="18" height="22" fill="#ffffff">
          <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 155.9v45.6H32v97.8H80z"/>
        </svg>
      </a>
    </div>

    <div v-if="settings.social_zalo" class="contact-btn-wrap zalo-wrap">
      <div class="contact-btn-tooltip">Chat Zalo</div>
      <a :href="settings.social_zalo" target="_blank" rel="noopener noreferrer" class="contact-btn zalo" aria-label="Chat Zalo">
        <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#ffffff">
          <path d="M12.49 10.2722v-.4496h1.3467v6.3218h-.7704a.576.576 0 01-.5763-.5729l-.0006.0005a3.273 3.273 0 01-1.9372.6321c-1.8138 0-3.2844-1.4697-3.2844-3.2823 0-1.8125 1.4706-3.2822 3.2844-3.2822a3.273 3.273 0 011.9372.6321l.0006.0005zM6.9188 7.7896v.205c0 .3823-.051.6944-.2995 1.0605l-.03.0343c-.0542.0615-.1815.206-.2421.2843L2.024 14.8h4.8948v.7682a.5764.5764 0 01-.5767.5761H0v-.3622c0-.4436.1102-.6414.2495-.8476L4.8582 9.23H.1922V7.7896h6.7266zm8.5513 8.3548a.4805.4805 0 01-.4803-.4798v-7.875h1.4416v8.3548H15.47zM20.6934 9.6C22.52 9.6 24 11.0807 24 12.9044c0 1.8252-1.4801 3.306-3.3066 3.306-1.8264 0-3.3066-1.4808-3.3066-3.306 0-1.8237 1.4802-3.3044 3.3066-3.3044zm-10.1412 5.253c1.0675 0 1.9324-.8645 1.9324-1.9312 0-1.065-.865-1.9295-1.9324-1.9295s-1.9324.8644-1.9324 1.9295c0 1.0667.865 1.9312 1.9324 1.9312zm10.1412-.0033c1.0737 0 1.945-.8707 1.945-1.9453 0-1.073-.8713-1.9436-1.945-1.9436-1.0753 0-1.945.8706-1.945 1.9453 0 1.0746.8697 1.9453 1.945 1.9453z"/>
        </svg>
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









