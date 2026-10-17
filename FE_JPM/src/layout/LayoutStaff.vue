<template>
  <div id="admin-layout" class="layout-wrapper layout-content-navbar staff-layout">
    
    <!-- Preloader to prevent FOUC -->
    <div v-if="isLoading" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: #f4f5fb; z-index: 99999; display: flex; justify-content: center; align-items: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div class="layout-container" v-show="!isLoading">
      <NavbarStaff />
      <div class="layout-page">
        <HeaderStaff />
        <div class="content-wrapper">
          <router-view />
          <FooterStaff />
          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
</template>

<script>
import HeaderStaff from "../components/staff/HeaderStaff.vue";
import NavbarStaff from "../components/staff/NavbarStaff.vue";
import FooterStaff from "../components/staff/FooterStaff.vue";

// CSS URLs
import boxiconsCss from "../assets/admin/template/vendor/fonts/boxicons.css?url";
import coreCss from "../assets/admin/template/vendor/css/core.css?url";
import themeDefaultCss from "../assets/admin/template/vendor/css/theme-default.css?url";
import demoCss from "../assets/admin/template/css/demo.css?url";
import perfectScrollbarCss from "../assets/admin/template/vendor/libs/perfect-scrollbar/perfect-scrollbar.css?url";
import apexChartsCss from "../assets/admin/template/vendor/libs/apex-charts/apex-charts.css?url";

// JS URLs
import helpersJs from "../assets/admin/template/vendor/js/helpers.js?url";
import configJs from "../assets/admin/template/js/config.js?url";
import jqueryJs from "../assets/admin/template/vendor/libs/jquery/jquery.js?url";
import popperJs from "../assets/admin/template/vendor/libs/popper/popper.js?url";
import bootstrapJs from "../assets/admin/template/vendor/js/bootstrap.js?url";
import perfectScrollbarJs from "../assets/admin/template/vendor/libs/perfect-scrollbar/perfect-scrollbar.js?url";
import menuJs from "../assets/admin/template/vendor/js/menu.js?url";
import mainJs from "../assets/admin/template/js/main.js?url";

export default {
  name: "LayoutStaff",

  data() {
    return {
      isLoading: true
    };
  },

  components: {
    NavbarStaff,
    HeaderStaff,
    FooterStaff,
  },

  mounted() {
    // HTML Attributes required by Sneat
    document.documentElement.classList.add("light-style", "layout-menu-fixed");
    document.documentElement.setAttribute("dir", "ltr");
    document.documentElement.setAttribute("data-theme", "theme-default");
    document.documentElement.setAttribute("data-template", "vertical-menu-template-free");

    // CSS
    const cssFiles = [
      { href: boxiconsCss, class: "" },
      { href: coreCss, class: "template-customizer-core-css" },
      { href: themeDefaultCss, class: "template-customizer-theme-css" },
      { href: demoCss, class: "" },
      { href: perfectScrollbarCss, class: "" },
      { href: apexChartsCss, class: "" },
    ];

    const loadCSS = (file) => {
      return new Promise((resolve, reject) => {
        if (document.querySelector(`link[href="${file.href}"]`)) {
          return resolve();
        }
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = file.href;
        if (file.class) link.classList.add(file.class);
        link.setAttribute("data-layout", "staff");
        link.onload = resolve;
        link.onerror = reject;
        document.head.appendChild(link);
      });
    };

    // Load JS Sequentially
    const loadScript = (src) => {
      return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) {
          return resolve();
        }
        const script = document.createElement("script");
        script.src = src;
        script.async = false;
        script.setAttribute("data-layout", "staff");
        script.onload = resolve;
        script.onerror = reject;
        document.body.appendChild(script);
      });
    };

    // Chain the loading process: First all CSS, then all JS
    Promise.all(cssFiles.map(loadCSS))
      .then(() => {
        return loadScript(helpersJs)
          .then(() => loadScript(configJs))
          .then(() => loadScript(jqueryJs))
          .then(() => loadScript(popperJs))
          .then(() => loadScript(bootstrapJs))
          .then(() => loadScript(perfectScrollbarJs))
          .then(() => loadScript(menuJs))
          .then(() => loadScript(mainJs))
          .then(() => loadScript("https://buttons.github.io/buttons.js"))
          .finally(() => {
            this.isLoading = false;
          });
      })
      .catch((err) => {
        console.error("Lỗi khi load assets cho LayoutStaff:", err);
        this.isLoading = false;
      });
  },

  beforeUnmount() {
    // Dọn dẹp thẻ link và script layout staff khi chuyển sang layout khác
    const staffElements = document.querySelectorAll('[data-layout="staff"]');
    staffElements.forEach(el => el.remove());
    document.documentElement.classList.remove("light-style", "layout-menu-fixed");
    document.documentElement.removeAttribute("dir");
    document.documentElement.removeAttribute("data-theme");
    document.documentElement.removeAttribute("data-template");
  }
};
</script>

<style>
/* Override Sneat defaults for Admin & Staff panel */
.layout-wrapper th,
.layout-wrapper .form-label {
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
    letter-spacing: 1px !important;
    color: #435971 !important;
    font-weight: 700 !important;
}

.layout-wrapper .card-header h5,
.layout-wrapper .modal-title {
    font-weight: 700 !important;
    color: #435971 !important;
}
</style>
