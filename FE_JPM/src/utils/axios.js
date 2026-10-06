import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Request Interceptor: Thêm token vào header
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response Interceptor: Bắt lỗi 401/403
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && (error.response.status === 401 || error.response.status === 403)) {
      // Xóa thông tin xác thực
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      
      // Chuyển hướng về trang đăng nhập
      window.location.href = '/dang-nhap';
    }
    return Promise.reject(error);
  }
);

export default api;
