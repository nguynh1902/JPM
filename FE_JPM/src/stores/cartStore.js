import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axiosClient from '../api/axiosClient'

export const useCartStore = defineStore('cart', () => {
  // ─── STATE ────────────────────────────────────────────────
  // Restore from localStorage on init, filter out corrupted items
  const rawData = JSON.parse(localStorage.getItem('jpm_cart') || '[]')
  const items = ref(rawData.filter(i => i.ma_phu_tung && i.ten_phu_tung))

  // ─── GETTERS ──────────────────────────────────────────────
  const totalItems = computed(() => items.value.length)

  const totalQuantity = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantity, 0)
  )

  // ─── HELPERS ──────────────────────────────────────────────
  function persist() {
    localStorage.setItem('jpm_cart', JSON.stringify(items.value))
    
    // Sync to cloud if logged in
    if (localStorage.getItem('auth_token')) {
      axiosClient.post('/client/profile/cart', { cart_data: items.value }).catch(e => console.error('Lỗi sync giỏ hàng', e));
    }
  }

  async function fetchFromCloud() {
    if (localStorage.getItem('auth_token')) {
      try {
        const res = await axiosClient.get('/client/profile/cart');
        const cloudCart = res.cart_data || [];
        const localCart = items.value;
        
        if (cloudCart.length > 0 || localCart.length > 0) {
          localCart.forEach(localItem => {
            const existing = cloudCart.find(c => c.ma_phu_tung === localItem.ma_phu_tung);
            if (existing) {
              existing.quantity = Math.max(existing.quantity, localItem.quantity);
            } else {
              cloudCart.push(localItem);
            }
          });
          
          items.value = cloudCart;
          persist();
        }
      } catch (error) {
        console.error('Lỗi tải giỏ hàng từ cloud', error);
      }
    }
  }

  // ─── ACTIONS ──────────────────────────────────────────────
  /**
   * Thêm sản phẩm vào giỏ. Nếu đã có thì tăng số lượng.
   * @param {Object} product - { id, ma_phu_tung, ten_phu_tung, hinh_anh, gia_ban, is_gia_lien_he }
   * @param {Number} quantity - Số lượng thêm (default: 1)
   */
  function addItem(product, quantity = 1) {
    const ma_item = product.ma_phu_tung || product.ma_san_pham;
    const existing = items.value.find(i => i.ma_phu_tung === ma_item)
    if (existing) {
      existing.quantity += quantity
    } else {
      const isSanPham = !!(product.ma_san_pham && !product.ma_phu_tung) || product.loai_san_pham === 'san_pham';
      const hasGia = product.gia_ban && parseFloat(product.gia_ban) > 0;
      items.value.push({
        id: product.id,
        ma_phu_tung: ma_item,
        ten_phu_tung: product.ten_phu_tung || product.ten_san_pham,
        hinh_anh: product.hinh_anh || product.hinh_anh_chinh || null,
        gia_ban: hasGia ? parseFloat(product.gia_ban) : 0,
        is_gia_lien_he: product.is_gia_lien_he !== undefined ? Boolean(product.is_gia_lien_he) : !hasGia,
        loai_san_pham: isSanPham ? 'san_pham' : 'phu_tung',
        quantity,
      })
    }
    persist()
  }

  /**
   * Cập nhật số lượng của 1 item
   * @param {String} ma_phu_tung
   * @param {Number} newQuantity
   */
  function updateQuantity(ma_phu_tung, newQuantity) {
    const item = items.value.find(i => i.ma_phu_tung === ma_phu_tung)
    if (item) {
      if (newQuantity <= 0) {
        removeItem(ma_phu_tung)
      } else {
        item.quantity = newQuantity
        persist()
      }
    }
  }

  /**
   * Xóa 1 item khỏi giỏ
   * @param {String} ma_phu_tung
   */
  function removeItem(ma_phu_tung) {
    items.value = items.value.filter(i => i.ma_phu_tung !== ma_phu_tung)
    persist()
  }

  /**
   * Xóa toàn bộ giỏ hàng (sau khi submit thành công)
   */
  function clearCart() {
    items.value = []
    persist()
  }

  return {
    items,
    totalItems,
    totalQuantity,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
    fetchFromCloud,
  }
})
