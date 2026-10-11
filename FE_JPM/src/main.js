import { createApp } from 'vue'
import App from './App.vue'
import './style.css'
import router from './router'
import Admin from './layout/LayoutAdmin.vue'
import Client from './layout/LayoutClient.vue'
import Auth from './layout/LayoutAuth.vue'
import Toaster from "@meforma/vue-toaster"


import { createPinia } from 'pinia'
import { VueQueryPlugin } from '@tanstack/vue-query'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(VueQueryPlugin, {
  queryClientConfig: {
    defaultOptions: {
      queries: {
        refetchOnWindowFocus: false,   // Không refetch khi alt+tab
        refetchOnReconnect: true,      // Refetch khi có mạng trở lại
        retry: 1,                      // Thử lại 1 lần nếu lỗi
        staleTime: 30 * 60 * 1000,    // 30 phút — data "tươi" trong 30p
        gcTime: 60 * 60 * 1000,       // 60 phút — giữ cache trong RAM
      },
    },
  },
})
app.use(router)
app.use(Toaster, {
  position: "top-right"
});

import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
app.component('v-select', Multiselect)

import VueApexCharts from "vue3-apexcharts";
app.use(VueApexCharts);

app.component("admin-layout", Admin);
app.component("client-layout", Client);
app.component("auth-layout", Auth);

app.mixin({
  methods: {
    getImageUrl(path) {
      if (!path) return "";
      if (path.startsWith("blob:") || path.startsWith("http") || path.startsWith("data:")) return path;
      const clean = path.startsWith("/") ? path.slice(1) : path;
      if (clean.startsWith("storage/") || clean.startsWith("uploads/")) {
        return `http://localhost:8000/${clean}`;
      }
      return `http://localhost:8000/storage/${clean}`;
    },
    getBlobUrl(file) {
      if (!file) return "";
      return URL.createObjectURL(file);
    }
  }
});

app.mount("#app")
