<template>
  <!-- Skeleton Loading State -->
  <div v-if="loading" class="card h-100 product-card jpm-card position-relative border-0 shadow-sm">
    <div class="card-img-wrapper skeleton-box card-img-product" :style="{ aspectRatio: aspectRatio }"></div>
    <div class="card-body p-3 d-flex flex-column bg-white">
      <div class="skeleton-line mb-2" style="width: 45%; height: 12px;"></div>
      <div class="skeleton-line mb-1" style="width: 100%; height: 15px;"></div>
      <div class="skeleton-line mb-2" style="width: 75%; height: 15px;"></div>
      <div class="skeleton-line mb-3" style="width: 55%; height: 13px;"></div>
      <div class="mt-auto pt-1 d-flex justify-content-between align-items-center">
        <div class="skeleton-line" style="width: 50%; height: 20px;"></div>
        <div class="skeleton-box" style="width: 36px; height: 36px; border-radius: var(--jpm-radius-control, 8px);"></div>
      </div>
    </div>
  </div>

  <!-- Real Product Card -->
  <div
    v-else-if="product"
    class="card h-100 product-card jpm-card position-relative"
    :class="{ 'is-quote-only': isQuoteRequired, 'is-out-of-stock': isOutOfStock }"
  >
    <!-- 1. Ảnh sản phẩm & Badges -->
    <div class="card-img-wrapper position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center">
      <img
        :src="displayImageUrl"
        :alt="productName"
        class="card-img-top w-100 transition-transform card-img-product"
        :style="{ aspectRatio: aspectRatio, objectFit: 'cover' }"
        @error="handleImageError"
        loading="lazy"
      />

      <!-- Badge OEM hoặc Khuyến mãi góc trên trái -->
      <span v-if="product.is_oem || product.is_chinh_hang" class="badge bg-danger position-absolute top-0 start-0 m-2 jpm-badge">
        OEM Japan
      </span>
      <span v-else-if="product.giam_gia && product.giam_gia > 0" class="badge bg-warning text-dark position-absolute top-0 start-0 m-2 jpm-badge">
        -{{ product.giam_gia }}%
      </span>

      <!-- Badge Cảnh báo mã thay thế Supersession -->
      <span v-if="product.is_superseded || product.ma_thay_the" class="badge badge-stock-super position-absolute top-0 end-0 m-2 jpm-badge" title="Đã có mã thay thế mới">
        <i class="bi bi-arrow-repeat me-1"></i>Thay thế
      </span>
      <!-- Badge Tạm hết hàng -->
      <span v-else-if="isOutOfStock" class="badge badge-stock-out position-absolute top-0 end-0 m-2 jpm-badge">
        Tạm hết
      </span>
    </div>

    <!-- 2. Thân Card thông tin -->
    <div class="card-body text-start p-3 d-flex flex-column bg-white">
      <!-- Tầng 1: Thương hiệu / Danh mục -->
      <div v-if="showBrand" class="product-category mb-1 text-truncate" :title="brandLabel">
        {{ brandLabel }}
      </div>

      <!-- Tầng 2: Tên sản phẩm / Linh kiện (Stretched-link dẫn chi tiết) -->
      <h5 class="card-title mb-1">
        <router-link :to="detailUrl" class="text-decoration-none stretched-link text-truncate-2">
          {{ productName }}
        </router-link>
      </h5>

      <!-- Tầng 3: Mã sản phẩm / SKU (Chuẩn tra cứu kỹ thuật máy thủy) -->
      <div v-if="showSku" class="product-sku mb-3">
        <template v-if="productSku">
          <span class="sku-label text-muted">Mã:</span>
          <span class="sku-code fw-semibold text-dark">{{ productSku }}</span>
        </template>
        <template v-else>&nbsp;</template>
      </div>

      <!-- Tầng 4: Chân Card - Cân đối đối xứng 100% giữa Giá & Nút hành động -->
      <div class="mt-auto d-flex align-items-center justify-content-between pt-1 gap-2">
        <!-- Khối giá bên trái -->
        <div class="price-container text-truncate">
          <template v-if="isQuoteRequired">
            <span class="text-danger fw-bold price-value price-quote">Liên hệ</span>
            <small class="d-block text-muted" style="font-size: 0.72rem; line-height: 1;">(Báo giá theo máy)</small>
          </template>
          <template v-else>
            <span class="text-danger fw-bold price-value">{{ formattedPrice }}</span>
            <small v-if="product.gia_goc && product.gia_goc > product.gia_ban" class="text-muted text-decoration-line-through d-block" style="font-size: 0.75rem; line-height: 1;">
              {{ Number(product.gia_goc).toLocaleString('vi-VN') }} đ
            </small>
          </template>
        </div>

        <!-- Khối nút thao tác bên phải (Được căn bằng kích thước và đối xứng tuyệt đối) -->
        <div v-if="showAction" class="action-container flex-shrink-0" style="position: relative; z-index: 2;">
          <!-- Trường hợp 1: Sản phẩm có giá -> Nút thêm vào giỏ hàng -->
          <button
            v-if="!isQuoteRequired"
            type="button"
            class="btn btn-cart-action"
            :title="'Thêm ' + productName + ' vào giỏ hàng'"
            :disabled="isOutOfStock"
            @click.stop.prevent="handleAddToCart"
          >
            <i class="bi bi-cart2"></i>
          </button>

          <!-- Trường hợp 2: Sản phẩm cần báo giá -> Nút yêu cầu báo giá kỹ thuật -->
          <button
            v-else
            type="button"
            class="btn btn-quote-action"
            :title="'Yêu cầu báo giá cho ' + productName"
            @click.stop.prevent="handleRequestQuote"
          >
            <i class="bi bi-file-earmark-text"></i>
            <span>Báo giá</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue';
import { useCartStore } from '../../stores/cartStore';

const props = defineProps({
  product: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  },
  aspectRatio: {
    type: String,
    default: '4/3'
  },
  showBrand: {
    type: Boolean,
    default: true
  },
  showSku: {
    type: Boolean,
    default: true
  },
  showAction: {
    type: Boolean,
    default: true
  },
  detailRoute: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['add-to-cart', 'request-quote', 'click']);

const cartStore = useCartStore();
const instance = getCurrentInstance();
const toast = instance?.appContext.config.globalProperties.$toast;

const FALLBACK_IMAGE = 'https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM+Marine';

// ─── COMPUTED LOGIC ──────────────────────────────────────────
const productName = computed(() => {
  return props.product?.ten_san_pham || props.product?.ten_phu_tung || 'Phụ Tùng Máy Thủy';
});

const productSku = computed(() => {
  return props.product?.ma_san_pham || props.product?.ma_phu_tung || '';
});

const brandLabel = computed(() => {
  if (props.product?.thuong_hieu?.name) return props.product.thuong_hieu.name;
  if (props.product?.thuong_hieu_name) return props.product.thuong_hieu_name;
  if (props.product?.danh_muc?.ten_danh_muc) return props.product.danh_muc.ten_danh_muc;
  return 'OEM JAPAN';
});

const isQuoteRequired = computed(() => {
  if (!props.product) return true;
  if (props.product.is_gia_lien_he) return true;
  const price = parseFloat(props.product.gia_ban);
  return isNaN(price) || price <= 0;
});

const isOutOfStock = computed(() => {
  if (!props.product) return false;
  if (props.product.trang_thai === 'het_hang') return true;
  if (props.product.ton_kho !== undefined && props.product.ton_kho <= 0) return true;
  if (props.product.so_luong_ton !== undefined && props.product.so_luong_ton <= 0) return true;
  return false;
});

const formattedPrice = computed(() => {
  if (isQuoteRequired.value) return 'Liên hệ';
  const price = parseFloat(props.product.gia_ban);
  return Number(price).toLocaleString('vi-VN') + ' đ';
});

const displayImageUrl = computed(() => {
  const raw = props.product?.hinh_anh_chinh || props.product?.hinh_anh || props.product?.image;
  if (!raw) return FALLBACK_IMAGE;
  if (raw.startsWith('blob:') || raw.startsWith('http') || raw.startsWith('data:')) return raw;
  const clean = raw.startsWith('/') ? raw.slice(1) : raw;
  const backendBase = (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
  if (clean.startsWith('storage/') || clean.startsWith('uploads/')) {
    return `${backendBase}/${clean}`;
  }
  return `${backendBase}/storage/${clean}`;
});

const detailUrl = computed(() => {
  if (props.detailRoute) return props.detailRoute;
  if (props.product?.slug) return `/chi-tiet-san-pham/${props.product.slug}`;
  if (props.product?.ma_phu_tung) return `/chi-tiet-phu-tung/${props.product.ma_phu_tung}`;
  if (props.product?.id) return `/chi-tiet-san-pham/${props.product.id}`;
  return '/san-pham';
});

// ─── METHODS ──────────────────────────────────────────────────
function handleImageError(event) {
  event.target.src = FALLBACK_IMAGE;
}

function handleAddToCart() {
  if (!props.product || isOutOfStock.value) return;
  cartStore.addItem(props.product);
  if (toast) {
    toast.success(`Đã thêm "${productName.value}" vào giỏ hàng!`);
  }
  emit('add-to-cart', props.product);
}

function handleRequestQuote() {
  if (!props.product) return;
  cartStore.addItem(props.product);
  if (toast) {
    toast.info(`Đã thêm "${productName.value}" vào danh sách yêu cầu báo giá!`);
  }
  emit('request-quote', props.product);
}
</script>

<style scoped>
.product-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid var(--jpm-neutral-200, #E2E8F0) !important;
  box-shadow: var(--jpm-shadow-sm, 0 1px 3px rgba(10, 17, 40, 0.05)) !important;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
  background-color: var(--jpm-neutral-0, #FFFFFF);
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--jpm-shadow-md, 0 4px 12px -2px rgba(10, 17, 40, 0.08)) !important;
  border-color: var(--jpm-neutral-300, #CBD5E1) !important;
}

.card-img-wrapper {
  background-color: var(--jpm-neutral-50, #F8FAFC);
  border-top-left-radius: var(--jpm-radius-card, 12px);
  border-top-right-radius: var(--jpm-radius-card, 12px);
}

.card-img-product {
  transition: transform 0.35s ease;
}

.product-card:hover .card-img-product {
  transform: scale(1.04);
}

.product-category {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: var(--jpm-neutral-500, #64748B);
}

.product-card .card-title {
  font-family: var(--jpm-font-heading, 'Raleway', sans-serif);
  font-size: 0.95rem;
  font-weight: 700;
  line-height: 1.4;
  margin-bottom: 0.25rem;
}

.product-card .card-title a {
  color: var(--jpm-color-navy-dark, #0A1128);
  transition: color 0.15s ease;
}

.product-card:hover .card-title a {
  color: var(--jpm-color-red-marine, #F82249);
}

.product-sku {
  font-size: 0.78rem;
  line-height: 1.3;
}

.sku-label {
  color: var(--jpm-neutral-500, #64748B);
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-right: 4px;
}

.sku-code {
  font-family: var(--jpm-font-mono, 'Roboto Mono', monospace);
}

.price-value {
  font-family: var(--jpm-font-body, 'Roboto', sans-serif);
  font-size: 1rem;
  font-variant-numeric: tabular-nums;
  letter-spacing: -0.2px;
}

.price-quote {
  font-size: 0.95rem;
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.jpm-badge {
  border-radius: var(--jpm-radius-badge, 6px);
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 7px;
  z-index: 2;
}

.is-out-of-stock {
  opacity: 0.75;
}
</style>
