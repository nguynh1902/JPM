<template>
  <div id="admin-layout" class="layout-wrapper layout-content-navbar">
    
    <!-- Preloader to prevent FOUC -->
    <div v-if="isLoading" class="position-fixed top-0 start-0 w-100 vh-100 d-flex justify-content-center align-items-center bg-light" style="z-index: 99999;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div class="layout-container" v-show="!isLoading">
      <NavbarAdmin />
      <div class="layout-page">
        <HeaderAdmin />
        <div class="content-wrapper">
          <router-view />
          <FooterAdmin />
          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
</template>

<script>
import HeaderAdmin from "../components/admin/HeaderAdmin.vue";
import NavbarAdmin from "../components/admin/NavbarAdmin.vue";
import FooterAdmin from "../components/admin/FooterAdmin.vue";
import { unloadClientCSS } from "../utils/clientCssLoader";

// CSS URLs & Inline Stylesheets (core.css & demo.css dùng ?inline để hỗ trợ Vite HMR tức thì như Client)
import boxiconsCss from "../assets/admin/template/vendor/fonts/boxicons.css?url";
import coreCssContent from "../assets/admin/template/vendor/css/core.css?inline";
import themeDefaultCss from "../assets/admin/template/vendor/css/theme-default.css?url";
import demoCssContent from "../assets/admin/template/css/demo.css?inline";
import perfectScrollbarCss from "../assets/admin/template/vendor/libs/perfect-scrollbar/perfect-scrollbar.css?url";
import apexChartsCss from "../assets/admin/template/vendor/libs/apex-charts/apex-charts.css?url";

let currentCoreCss = coreCssContent;
let currentDemoCss = demoCssContent;

const syncAdminInlineStyle = (id, cssText, extraClass = "") => {
  if (typeof document === "undefined") return null;
  let styleEl = document.getElementById(id);
  if (!styleEl) {
    styleEl = document.createElement("style");
    styleEl.id = id;
    if (extraClass) styleEl.classList.add(extraClass);
    styleEl.setAttribute("data-layout", "admin");
  }
  styleEl.textContent = cssText;
  document.head.appendChild(styleEl);
  return styleEl;
};

if (import.meta.hot) {
  import.meta.hot.accept("../assets/admin/template/vendor/css/core.css?inline", (mod) => {
    if (mod && typeof mod.default === "string") {
      currentCoreCss = mod.default;
      const el = typeof document !== "undefined" ? document.getElementById("admin-core-css") : null;
      if (el) el.textContent = currentCoreCss;
    }
  });
  import.meta.hot.accept("../assets/admin/template/css/demo.css?inline", (mod) => {
    if (mod && typeof mod.default === "string") {
      currentDemoCss = mod.default;
      const el = typeof document !== "undefined" ? document.getElementById("admin-demo-css") : null;
      if (el) el.textContent = currentDemoCss;
    }
  });
}

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
  name: "LayoutAdmin",

  data() {
    return {
      isLoading: true
    };
  },

  components: {
    NavbarAdmin,
    HeaderAdmin,
    FooterAdmin,
  },

  mounted() {
    // Dọn sạch CSS Client để không xung đột style với Sneat Admin
    unloadClientCSS();
    // HTML Attributes required by Sneat
    document.documentElement.classList.add("light-style", "layout-menu-fixed");
    document.documentElement.setAttribute("dir", "ltr");
    document.documentElement.setAttribute("data-theme", "theme-default");
    document.documentElement.setAttribute("data-template", "vertical-menu-template-free");

    // CSS theo đúng thứ tự ưu tiên: boxicons -> core -> theme-default -> demo -> libs
    const loadCSS = (href, className = "") => {
      return new Promise((resolve, reject) => {
        const existing = document.querySelector(`link[href="${href}"]`);
        if (existing) {
          document.head.appendChild(existing);
          return resolve();
        }
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = href;
        if (className) link.classList.add(className);
        link.setAttribute("data-layout", "admin");
        link.onload = resolve;
        link.onerror = reject;
        document.head.appendChild(link);
      });
    };

    const loadAdminStyles = async () => {
      await loadCSS(boxiconsCss);
      syncAdminInlineStyle("admin-core-css", currentCoreCss, "template-customizer-core-css");
      await loadCSS(themeDefaultCss, "template-customizer-theme-css");
      syncAdminInlineStyle("admin-demo-css", currentDemoCss);
      await loadCSS(perfectScrollbarCss);
      await loadCSS(apexChartsCss);
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
        script.setAttribute("data-layout", "admin");
        script.onload = resolve;
        script.onerror = reject;
        document.body.appendChild(script);
      });
    };

    // Chain the loading process: First all CSS, then all JS
    loadAdminStyles()
      .then(() => {
        // If JS vendor files are already loaded in memory, avoid reloading
        if (window.Helpers && window.bootstrap && window.Menu) {
          this.isLoading = false;
          this.$nextTick(() => {
            window.dispatchEvent(new Event('admin-layout-ready'));
          });
          return;
        }

        return loadScript(helpersJs)
          .then(() => loadScript(configJs))
          .then(() => loadScript(jqueryJs))
          .then(() => loadScript(popperJs))
          .then(() => loadScript(bootstrapJs))
          .then(() => loadScript(perfectScrollbarJs))
          .then(() => loadScript(menuJs))
          .then(() => loadScript(mainJs))
          .finally(() => {
            this.isLoading = false;
            this.$nextTick(() => {
              window.dispatchEvent(new Event('admin-layout-ready'));
            });
          });
      })
      .catch((err) => {
        console.error("Error loading admin assets:", err);
        this.isLoading = false;
      });
  },

  unmounted() {
    // Dừng resize auto-update listener của Sneat Helpers khi rời Admin
    try {
      window.Helpers?.setAutoUpdate(false);
    } catch (_) {}

    // Cleanup HTML Attributes when leaving Admin layout
    document.documentElement.classList.remove(
      "light-style",
      "layout-menu-fixed",
      "layout-menu-expanded",
      "layout-menu-collapsed"
    );
    document.documentElement.removeAttribute("dir");
    document.documentElement.removeAttribute("data-theme");
    document.documentElement.removeAttribute("data-template");

    // Cleanup Admin CSS only (keep scripts in memory for instant SPA transitions)
    document.querySelectorAll('link[data-layout="admin"], style[data-layout="admin"]').forEach(el => el.remove());
  },
};
</script>

<style>
/* Font chuẩn hóa duy nhất đồng bộ cho Admin: Inter */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
</style>

