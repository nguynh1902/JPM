import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axiosClient from '../api/axiosClient'

const MAX_CART_ITEMS = 200
const SYNC_DEBOUNCE_MS = 350

export function getCartItemKey(item) {
  if (!item) return ''
  const code = String(item.ma_phu_tung || item.ma_san_pham || '')
  const model = String(item.model_may || '').trim()
  return `${code}::${model}`
}

export const useCartStore = defineStore('cart', () => {
  // ─── STATE ────────────────────────────────────────────────
  let rawData = []
  try {
    rawData = JSON.parse(localStorage.getItem('jpm_cart') || '[]')
    if (!Array.isArray(rawData)) rawData = []
  } catch {
    rawData = []
  }
  const items = ref(rawData.filter(i => i && i.ma_phu_tung && i.ten_phu_tung).slice(0, MAX_CART_ITEMS))

  let syncTimer = null
  let syncSeq = 0

  // ─── GETTERS ──────────────────────────────────────────────
  const totalItems = computed(() => items.value.length)

  const totalQuantity = computed(() =>
    items.value.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0)
  )

  // ─── HELPERS ──────────────────────────────────────────────
  function scheduleCloudSync(immediate = false) {
    if (!localStorage.getItem('auth_token')) return

    const currentSeq = ++syncSeq
    const snapshot = items.value.slice(0, MAX_CART_ITEMS)

    const runSync = () => {
      syncTimer = null
      if (currentSeq !== syncSeq) return
      axiosClient
        .post('/client/profile/cart', { cart_data: snapshot })
        .catch(e => console.error('Lỗi sync giỏ hàng', e))
    }

    if (syncTimer) {
      clearTimeout(syncTimer)
      syncTimer = null
    }

    if (immediate) {
      runSync()
    } else {
      syncTimer = setTimeout(runSync, SYNC_DEBOUNCE_MS)
    }
  }

  function persist({ syncCloud = true, immediate = false } = {}) {
    try {
      localStorage.setItem('jpm_cart', JSON.stringify(items.value))
    } catch (e) {
      console.error('Lỗi lưu localStorage giỏ hàng', e)
    }
    if (syncCloud) {
      scheduleCloudSync(immediate)
    }
  }

  async function fetchFromCloud() {
    if (!localStorage.getItem('auth_token')) return

    try {
      const res = await axiosClient.get('/client/profile/cart')
      const cloudCart = Array.isArray(res?.cart_data) ? [...res.cart_data] : []
      const localCart = items.value

      if (localCart.length === 0) {
        // Không cần POST ngược lên server nếu giỏ cục bộ rỗng
        items.value = cloudCart.slice(0, MAX_CART_ITEMS)
        persist({ syncCloud: false })
        return
      }

      if (cloudCart.length > 0 || localCart.length > 0) {
        localCart.forEach(localItem => {
          const localKey = getCartItemKey(localItem)
          const existing = cloudCart.find(c => getCartItemKey(c) === localKey)
          if (existing) {
            existing.quantity = Math.max(Number(existing.quantity) || 1, Number(localItem.quantity) || 1)
          } else if (cloudCart.length < MAX_CART_ITEMS) {
            cloudCart.push(localItem)
          }
        })

        items.value = cloudCart.slice(0, MAX_CART_ITEMS)
        persist({ syncCloud: true, immediate: true })
      }
    } catch (error) {
      console.error('Lỗi tải giỏ hàng từ cloud', error)
    }
  }

  function findItemIndex(keyOrCode, modelMay = null) {
    const target = String(keyOrCode || '')
    if (modelMay !== null && modelMay !== undefined) {
      const m = String(modelMay || '').trim()
      return items.value.findIndex(
        i => i.ma_phu_tung === target && String(i.model_may || '').trim() === m
      )
    }
    const byKey = items.value.findIndex(i => getCartItemKey(i) === target)
    if (byKey !== -1) return byKey
    return items.value.findIndex(i => i.ma_phu_tung === target)
  }

  // ─── ACTIONS ──────────────────────────────────────────────
  function addItem(product, quantity = 1) {
    const qty = Math.max(1, parseInt(quantity, 10) || 1)
    const ma_item = product.ma_phu_tung || product.ma_san_pham
    if (!ma_item) return
    const modelMay = String(product.model_may || '').trim() || null

    const existing = items.value.find(
      i => i.ma_phu_tung === ma_item && (String(i.model_may || '').trim() || null) === modelMay
    )
    if (existing) {
      existing.quantity = (Number(existing.quantity) || 0) + qty
    } else {
      if (items.value.length >= MAX_CART_ITEMS) {
        return
      }
      const isSanPham = !!(product.ma_san_pham && !product.ma_phu_tung) || product.loai_san_pham === 'san_pham'
      const hasGia = product.gia_ban && parseFloat(product.gia_ban) > 0
      items.value.push({
        id: product.id,
        ma_phu_tung: ma_item,
        model_may: modelMay,
        ten_phu_tung: product.ten_phu_tung || product.ten_san_pham,
        hinh_anh: product.hinh_anh || product.hinh_anh_chinh || null,
        slug: product.slug || null,
        gia_ban: hasGia ? parseFloat(product.gia_ban) : 0,
        is_gia_lien_he: product.is_gia_lien_he !== undefined ? Boolean(product.is_gia_lien_he) : !hasGia,
        loai_san_pham: isSanPham ? 'san_pham' : 'phu_tung',
        quantity: qty,
      })
    }
    persist()
  }

  function updateQuantity(keyOrCode, newQuantity, modelMay = null) {
    const idx = findItemIndex(keyOrCode, modelMay)
    const item = idx !== -1 ? items.value[idx] : null
    if (item) {
      const qty = parseInt(newQuantity, 10) || 0
      if (qty <= 0) {
        removeItem(getCartItemKey(item))
      } else {
        item.quantity = Math.min(qty, 9999)
        persist()
      }
    }
  }

  function removeItem(keyOrCode, modelMay = null) {
    const idx = findItemIndex(keyOrCode, modelMay)
    if (idx !== -1) {
      items.value.splice(idx, 1)
      persist()
    }
  }

  /**
   * Xóa hàng loạt nhiều item key/mã trong 1 lần ghi localStorage & 1 lần sync cloud (O(N), không storm API)
   * @param {String[]} keysOrCodes
   */
  function removeItems(keysOrCodes) {
    if (!Array.isArray(keysOrCodes) || keysOrCodes.length === 0) return
    const keySet = new Set(keysOrCodes)
    items.value = items.value.filter(i => !keySet.has(getCartItemKey(i)) && !keySet.has(i.ma_phu_tung))
    persist({ syncCloud: true, immediate: true })
  }

  function clearCart() {
    items.value = []
    persist({ syncCloud: true, immediate: true })
  }

  return {
    items,
    totalItems,
    totalQuantity,
    addItem,
    updateQuantity,
    removeItem,
    removeItems,
    clearCart,
    fetchFromCloud,
  }
})
