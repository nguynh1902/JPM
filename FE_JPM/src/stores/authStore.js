import { defineStore } from "pinia";
import axiosClient from "../api/axiosClient";

const PERMISSION_ALIASES = {
  "quản lý model & động cơ": ["xem động cơ & thương hiệu"],
  "xem động cơ & thương hiệu": ["quản lý model & động cơ"],
  "quản lý thẻ kho": ["xem thẻ kho"],
  "xem thẻ kho": ["quản lý thẻ kho"],
  "quản lý sản phẩm": ["xem sản phẩm"],
  "xem sản phẩm": ["quản lý sản phẩm"],
  "quản lý tin tức & cms": ["xem tin tức & cms"],
  "xem tin tức & cms": ["quản lý tin tức & cms"],
};

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("auth_user") || "null"), // Lưu thông tin cá nhân của User
    token: localStorage.getItem("auth_token") || null, // Token của Sanctum
    loading: false, // Trạng thái đang gọi API
    error: null, // Thông báo lỗi nếu có
  }),

  getters: {
    // Trả về true nếu đã đăng nhập (có token)
    isLoggedIn: (state) => !!state.token,
    
    // Kiểm tra xem User có phải là Super Admin không (Bypass mọi quyền)
    isAdmin: (state) => {
      if (!state.user || !state.user.roles) return false;
      return state.user.roles.some((r) => {
        const current = String(typeof r === 'string' ? r : (r?.name || '')).toLowerCase().trim();
        return current === 'admin' || current === 'super admin' || current === 'quản trị viên';
      });
    },

    // Kiểm tra xem User có phải là nhân sự nội bộ (Admin hoặc Staff hoặc các vai trò nhân sự)
    isStaffOrAdmin: (state) => {
      if (!state.user || !state.user.roles) return false;
      return state.user.roles.some((r) => {
        const current = String(typeof r === 'string' ? r : (r?.name || '')).toLowerCase().trim();
        return current !== 'customer' && current !== 'khách hàng';
      });
    },

    // Lấy danh sách toàn bộ Permissions của User
    userPermissions: (state) => {
      if (!state.user || !state.user.permissions) return [];
      return state.user.permissions.map(p => String(typeof p === 'string' ? p : (p?.name || '')).trim());
    },

    // Kiểm tra xem User có quyền cụ thể nào không (Admin luôn có quyền)
    hasPermission: (state) => (permissionName) => {
      if (!state.user) return false;
      // Admin tối cao luôn có mọi quyền
      if (state.user.roles && state.user.roles.some(r => {
        const cur = String(typeof r === 'string' ? r : (r?.name || '')).toLowerCase().trim();
        return cur === 'admin' || cur === 'super admin';
      })) {
        return true;
      }
      if (!state.user.permissions || !Array.isArray(state.user.permissions)) return false;
      const target = String(permissionName).toLowerCase().trim();
      const validTargets = [target, ...(PERMISSION_ALIASES[target] || [])];
      return state.user.permissions.some(p => {
        const cur = String(typeof p === 'string' ? p : (p?.name || '')).toLowerCase().trim();
        return validTargets.includes(cur);
      });
    },

    // Kiểm tra xem User có ít nhất 1 trong danh sách quyền không
    hasAnyPermission: (state) => (permissionList) => {
      if (!state.user) return false;
      // Admin tối cao luôn có mọi quyền
      if (state.user.roles && state.user.roles.some(r => {
        const cur = String(typeof r === 'string' ? r : (r?.name || '')).toLowerCase().trim();
        return cur === 'admin' || cur === 'super admin';
      })) {
        return true;
      }
      if (!state.user.permissions || !Array.isArray(state.user.permissions)) return false;
      if (!Array.isArray(permissionList) || permissionList.length === 0) return true;
      
      const userPerms = state.user.permissions.map(p => 
        String(typeof p === 'string' ? p : (p?.name || '')).toLowerCase().trim()
      );

      return permissionList.some(item => {
        const target = String(item).toLowerCase().trim();
        const validTargets = [target, ...(PERMISSION_ALIASES[target] || [])];
        return validTargets.some(t => userPerms.includes(t));
      });
    },
    
    // Kiểm tra xem User có quyền/role gì (dùng để chặn Route)
    hasRole: (state) => (roleName) => {
      if (!state.user || !state.user.roles) return false;
      const target = String(roleName).toLowerCase().trim();
      return state.user.roles.some((r) => {
        const current = String(typeof r === 'string' ? r : (r?.name || '')).toLowerCase().trim();
        if (target === 'staff') {
          return current === 'staff' || current.includes('nhân viên') || current === 'nhân viên pim';
        }
        if (target === 'admin') {
          return current === 'admin' || current === 'super admin' || current === 'quản trị viên';
        }
        return current === target;
      });
    },
  },

  actions: {
    /**
     * Hàm xử lý Gọi API Đăng Nhập
     */
    async login(payload) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axiosClient.post("/login", {
          login_id: payload.login_id, 
          password: payload.password
        });
        
        if (response.success) {
          const { token, user } = response.data;
          this.token = token;
          this.user = user;
          localStorage.setItem("auth_token", token);
          localStorage.setItem("auth_user", JSON.stringify(user));
          
          return true;
        }
      } catch (err) {
        this.error = err.response?.data?.message || "Đăng nhập thất bại, vui lòng thử lại!";
        return false;
      } finally {
        this.loading = false;
      }
    },
    /**
     * Hàm gọi API lấy thông tin User hiện tại bằng Token (Cập nhật ngầm)
     */
    /**
     * Hàm gọi API Đăng ký (Mock)
     */
    async register(payload) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axiosClient.post("/register", payload);
        if (response.success) {
          if (response.data?.token && response.data?.user) {
            this.token = response.data.token;
            this.user = response.data.user;
            localStorage.setItem("auth_token", response.data.token);
            localStorage.setItem("auth_user", JSON.stringify(response.data.user));
          }
          return true;
        }
        return false;
      } catch (err) {
        this.error = err.response?.data?.message || "Đăng ký thất bại.";
        return false;
      } finally {
        this.loading = false;
      }
    },

    async fetchProfile() {
      this.loading = true;
      if (!this.token) {
        this.loading = false;
        return;
      }
      try {
        const response = await axiosClient.get('/me');
        if (response.success) {
          this.user = response.data.user;
          localStorage.setItem('auth_user', JSON.stringify(this.user));
        }
      } catch (err) {
        this.logout();
      } finally {
        this.loading = false;
      }
    },

    /**
     * Hàm xử lý Đăng Xuất
     */
    async logout() {
      try {
        if (this.token) {
          await axiosClient.post("/logout");
        }
      } catch (err) {
        console.error("Lỗi khi logout:", err);
      } finally {
        // Xóa sạch State và LocalStorage bất kể gọi API thành công hay không
        this.user = null;
        this.token = null;
        localStorage.removeItem("auth_token");
        localStorage.removeItem("auth_user");
        localStorage.removeItem("jpm_cart");
        window.location.href = "/dang-nhap"; // Đẩy về trang đăng nhập
      }
    }
  }
});







