import axios from "axios";

// Đặt tên instance dễ hiểu, gắn chặt với baseURL của Backend
const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000/api", // Địa chỉ BE Laravel của dự án
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  timeout: 10000, // Timeout sau 10s
});

// Interceptor: Trước khi gửi Request (Bơm Token vào Header)
axiosClient.interceptors.request.use(
  (config) => {
    // Lấy token từ localStorage (do authStore lưu lại)
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor: Sau khi nhận Response từ BE
axiosClient.interceptors.response.use(
  (response) => {
    // Trả về data (bỏ qua vỏ bọc rườm rà của Axios)
    return response.data;
  },
  (error) => {
    // Xử lý chung các mã lỗi thường gặp
    if (error.response && error.response.status === 401) {
      const hadToken = !!localStorage.getItem("auth_token");
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");
      const path = window.location.pathname || "";
      const isProtectedPath = path.startsWith("/portal") || path.startsWith("/admin") || path.startsWith("/tai-khoan") || path.startsWith("/garage") || path.startsWith("/lich-su");
      if ((hadToken || isProtectedPath) && path !== "/dang-nhap") {
        window.location.href = "/dang-nhap";
      }
    }
    return Promise.reject(error);
  }
);

export default axiosClient;

