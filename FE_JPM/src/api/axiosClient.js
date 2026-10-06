import axios from "axios";

// Đặt tên instance dễ hiểu, gắn chặt với baseURL của Backend
const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api", // Địa chỉ BE Laravel của dự án
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
      // 401: Hết hạn token hoặc không có quyền truy cập
      localStorage.removeItem("auth_token");
      // Tránh lặp vô hạn nếu đang ở trang đăng nhập
      if (window.location.pathname !== '/dang-nhap') {
         window.location.href = "/dang-nhap";
      }
    }
    return Promise.reject(error);
  }
);

export default axiosClient;

