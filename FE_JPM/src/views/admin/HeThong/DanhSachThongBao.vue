<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ Thống /</span> Trung Tâm Thông Báo & Viễn Thông
    </h4>

    <!-- Tab navigation -->
    <div class="nav-align-top mb-4">
      <ul class="nav nav-pills mb-3 gap-2" role="tablist">
        <li class="nav-item">
          <button
            type="button"
            class="nav-link active fw-bold"
            role="tab"
            data-bs-toggle="tab"
            data-bs-target="#navs-inapp"
            aria-controls="navs-inapp"
            aria-selected="true"
          >
            <i class="bx bx-bell me-1"></i> Thông Báo Nội Bộ Hệ Thống
          </button>
        </li>
        <li class="nav-item">
          <button
            type="button"
            class="nav-link fw-bold"
            role="tab"
            data-bs-toggle="tab"
            data-bs-target="#navs-telecom"
            aria-controls="navs-telecom"
            aria-selected="false"
            @click="fetchTelecomLogs"
          >
            <i class="bx bx-broadcast me-1"></i> Nhật Ký Zalo ZNS / SMS Chành Xe
          </button>
        </li>
      </ul>

      <div class="tab-content border-0 p-0 bg-transparent shadow-none">
        <!-- TAB 1: THÔNG BÁO NỘI BỘ -->
        <div class="tab-pane fade show active" id="navs-inapp" role="tabpanel">
          <div class="card shadow-sm border">
            <div class="card-header border-bottom">
              <h5 class="mb-0 fw-bold text-dark">Tất cả thông báo</h5>
            </div>
            
            <!-- Bộ lọc (Filters) -->
            <div class="card-body pt-3 pb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                  <input type="text" class="form-control" placeholder="Tìm kiếm nội dung thông báo..." v-model="filters.search">
                </div>
                <div style="width: 200px;">
                  <select class="form-select" v-model="filters.status">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="unread">Chưa đọc</option>
                    <option value="read">Đã đọc</option>
                  </select>
                </div>
                <div style="width: 200px;">
                  <select class="form-select" v-model="filters.type">
                    <option value="all">Tất cả phân loại</option>
                    <option value="b2b">Yêu cầu B2B</option>
                    <option value="system">Hệ thống</option>
                    <option value="warehouse">Kho bãi</option>
                  </select>
                </div>
                <div class="ms-auto d-flex gap-2">
                  <button class="btn btn-secondary text-nowrap" @click="resetFilters">
                    Đặt lại
                  </button>
                  <button class="btn btn-outline-primary text-nowrap" @click="markAllAsRead">
                    <i class="bx bx-check-double me-1"></i> Đánh dấu tất cả đã đọc
                  </button>
                </div>
              </div>
            </div>

            <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 15%">Phân Loại</th>
                    <th style="width: 50%">Nội Dung Thông Báo</th>
                    <th style="width: 15%">Thời Gian</th>
                    <th class="text-center" style="width: 15%">Hành Động</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <tr v-if="isLoading">
                    <td colspan="5" class="text-center py-4 text-muted">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredNotifications.length === 0">
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                      Không có dữ liệu
                    </td>
                  </tr>
                  <tr v-for="(item, index) in filteredNotifications" :key="item.id" v-else>
                    <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                    <td>
                      <span class="badge text-uppercase" :class="getBadgeClass(item.type)">
                        {{ getTypeName(item.type) }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                          <img :src="item.avatar" alt="Avatar" class="w-100 h-100 rounded-circle" style="object-fit: cover; border: 1px solid #e1e4e8;" />
                        </div>
                        <div class="d-flex flex-column" style="max-width: 400px;">
                          <span class="text-dark mb-1 text-truncate" :class="{'fw-bold': !item.isRead, 'fw-medium': item.isRead}" style="font-size: 14px;">
                            {{ item.title }}
                          </span>
                          <small class="text-muted text-truncate" style="font-size: 13px;">{{ item.message }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="text-muted" style="font-size: 13px;">{{ item.time }}</span>
                    </td>
                    <td class="text-center">
                      <div class="d-inline-flex align-items-center justify-content-center gap-1">
                        <span v-if="!item.isRead" class="badge badge-dot bg-success me-2" style="width: 8px; height: 8px; animation: pulse 2s infinite;"></span>
                        <button class="action-btn text-primary" title="Đánh dấu đã đọc" @click="markAsRead(item.id)" v-if="!item.isRead">
                          <i class="bx bx-check"></i>
                        </button>
                        <button class="action-btn text-secondary" title="Xem chi tiết">
                          <i class="bx bx-show"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer py-3 border-top">
              <span class="text-muted fw-medium">Tổng cộng: {{ (filteredNotifications && filteredNotifications.length) ? filteredNotifications.length : 0 }} mục</span>
            </div>
          </div>
        </div>

        <!-- TAB 2: NHẬT KÝ ZALO ZNS & SMS VIỄN THÔNG -->
        <div class="tab-pane fade" id="navs-telecom" role="tabpanel">
          <div class="card shadow-sm border">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-0 fw-bold text-dark">Lịch Sử Tin Nhắn Viễn Thông (ZNS / SMS Brandname)</h5>
                <small class="text-muted">Nhật ký tin nhắn gửi tự động cho Chủ tàu, Khách hàng & Tài xế chành xe</small>
              </div>
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="fetchTelecomLogs" :disabled="isLoadingTelecom">
                <i class="bx bx-refresh" :class="{ 'bx-spin': isLoadingTelecom }"></i> Làm mới
              </button>
            </div>

            <!-- Filters Telecom -->
            <div class="card-body pt-3 pb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                  <input type="text" class="form-control" placeholder="Tìm theo Số điện thoại, Tên người nhận, Mã tham chiếu..." v-model="telecomSearch">
                </div>
                <div style="width: 220px;">
                  <select class="form-select" v-model="telecomType">
                    <option value="all">Tất cả kênh gửi</option>
                    <option value="zalo_zns">Zalo ZNS</option>
                    <option value="sms_brandname">SMS Brandname</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
              <table class="table table-hover align-middle mb-0">
                <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                  <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 12%">Kênh Gửi</th>
                    <th style="width: 18%">Người Nhận</th>
                    <th style="width: 15%">Mã Tham Chiếu</th>
                    <th style="width: 30%">Nội Dung Tin Nhắn</th>
                    <th class="text-center" style="width: 10%">Thời Gian</th>
                    <th class="text-center" style="width: 10%">Trạng Thái</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <tr v-if="isLoadingTelecom">
                    <td colspan="7" class="text-center py-5 text-muted">
                      <div class="spinner-border text-primary" role="status"></div>
                      <p class="mt-2 mb-0">Đang tải nhật ký viễn thông...</p>
                    </td>
                  </tr>
                  <tr v-for="(log, idx) in filteredTelecomLogs" :key="log.id" v-else>
                    <td class="text-center"><strong>{{ idx + 1 }}</strong></td>
                    <td>
                      <span v-if="log.loai_tin === 'zalo_zns'" class="badge bg-label-info">
                        <i class="bx bx-chat me-1"></i> ZALO ZNS
                      </span>
                      <span v-else class="badge bg-label-primary">
                        <i class="bx bx-message-square-dots me-1"></i> SMS BRAND
                      </span>
                    </td>
                    <td>
                      <span class="d-block fw-bold text-dark">{{ log.ten_nguoi_nhan || 'Khách hàng' }}</span>
                      <small class="text-primary font-monospace">{{ log.so_dien_thoai }}</small>
                      <span v-if="log.nguoi_nhan_loai === 'tai_xe'" class="badge bg-label-warning ms-1" style="font-size: 0.65rem;">Tài xế</span>
                      <span v-else class="badge bg-label-success ms-1" style="font-size: 0.65rem;">Chủ tàu</span>
                    </td>
                    <td>
                      <span class="fw-bold font-monospace text-dark">{{ log.ma_tham_chieu || 'N/A' }}</span>
                      <small class="text-muted d-block">{{ log.mau_thong_bao }}</small>
                    </td>
                    <td>
                      <span class="d-block fw-medium text-dark text-truncate" style="max-width: 350px;">{{ log.tieu_de }}</span>
                      <small class="text-muted text-truncate d-inline-block" style="max-width: 350px;" :title="log.noi_dung">{{ log.noi_dung }}</small>
                    </td>
                    <td class="text-center text-muted small">
                      {{ formatTime(log.created_at) }}
                    </td>
                    <td class="text-center">
                      <span v-if="log.trang_thai === 'thanh_cong'" class="badge bg-success">ĐÃ GỬI</span>
                      <span v-else-if="log.trang_thai === 'mo_phong'" class="badge bg-warning text-dark">SANDBOX TEST</span>
                      <span v-else class="badge bg-danger">THẤT BẠI</span>
                    </td>
                  </tr>
                  <tr v-if="!isLoadingTelecom && filteredTelecomLogs.length === 0">
                    <td colspan="7" class="text-center py-5 text-muted">
                      <i class="bx bx-message-alt-x text-secondary fs-1 d-block mb-2"></i>
                      Chưa có nhật ký tin nhắn viễn thông nào
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer py-3 border-top">
              <span class="text-muted fw-medium">Tổng cộng: {{ filteredTelecomLogs.length }} tin nhắn viễn thông</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axiosClient from '../../../api/axiosClient';

export default {
  name: "DanhSachThongBao",
  data() {
    return {
      isLoading: false,
      filters: {
        search: "",
        status: "all",
        type: "all"
      },
      notifications: [
        {
          id: 1,
          type: "b2b",
          title: "Yêu cầu báo giá mới (B2B)",
          message: "Khách hàng Công ty TNHH ABC vừa gửi yêu cầu.",
          time: "1 giờ trước",
          avatar: "https://minhthanh.edu.vn/uploads/blog/2021/04/16/z2428581674488-dc7844093f4ecba039f21f1e3fb617fa-1618556637.jpg",
          isRead: false
        },
        {
          id: 2,
          type: "warehouse",
          title: "Hàng từ Nhật cập cảng",
          message: "Lô hàng mã #JP-1024 đã sẵn sàng để nhập kho.",
          time: "3 giờ trước",
          avatar: "https://minhthanh.edu.vn/uploads/blog/2021/04/16/z2428581674488-dc7844093f4ecba039f21f1e3fb617fa-1618556637.jpg",
          isRead: true
        },
        {
          id: 3,
          type: "system",
          title: "Cập nhật hệ thống ERP",
          message: "Hệ thống sẽ bảo trì vào 00:00 đêm nay.",
          time: "1 ngày trước",
          avatar: "https://minhthanh.edu.vn/uploads/blog/2021/04/16/z2428581674488-dc7844093f4ecba039f21f1e3fb617fa-1618556637.jpg",
          isRead: true
        }
      ],
      // Telecom Logs
      telecomLogs: [],
      isLoadingTelecom: false,
      telecomSearch: "",
      telecomType: "all"
    };
  },
  computed: {
    filteredNotifications() {
      const s = (this.filters.search || '').toLowerCase().trim();
      return this.notifications.filter(item => {
        const matchSearch = !s ||
                            String(item.title || '').toLowerCase().includes(s) || 
                            String(item.message || '').toLowerCase().includes(s);
        const matchStatus = this.filters.status === 'all' || 
                            (this.filters.status === 'read' && item.isRead) || 
                            (this.filters.status === 'unread' && !item.isRead);
        const matchType = this.filters.type === 'all' || item.type === this.filters.type;
        
        return matchSearch && matchStatus && matchType;
      });
    },
    filteredTelecomLogs() {
      let list = this.telecomLogs;
      if (this.telecomSearch) {
        const s = this.telecomSearch.toLowerCase().trim();
        list = list.filter(l => 
          (l.so_dien_thoai && String(l.so_dien_thoai).includes(s)) ||
          (l.ten_nguoi_nhan && String(l.ten_nguoi_nhan).toLowerCase().includes(s)) ||
          (l.ma_tham_chieu && String(l.ma_tham_chieu).toLowerCase().includes(s)) ||
          (l.noi_dung && String(l.noi_dung).toLowerCase().includes(s))
        );
      }
      if (this.telecomType !== 'all') {
        list = list.filter(l => l.loai_tin === this.telecomType);
      }
      return list;
    }
  },
  methods: {
    formatTime(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleString('vi-VN');
    },
    async fetchTelecomLogs() {
      this.isLoadingTelecom = true;
      try {
        const res = await axiosClient.get('/admin/thong-bao/lich-su?per_page=50');
        if (res.data && res.data.success) {
          this.telecomLogs = res.data.data.data || [];
        }
      } catch (err) {
        console.error('Không thể tải nhật ký viễn thông:', err);
      } finally {
        this.isLoadingTelecom = false;
      }
    },
    resetFilters() {
      this.filters = { search: "", status: "all", type: "all" };
    },
    getBadgeClass(type) {
      switch (type) {
        case 'b2b': return 'bg-pastel-warning text-warning';
        case 'warehouse': return 'bg-pastel-info text-info';
        case 'system': return 'bg-pastel-danger text-danger';
        default: return 'bg-pastel-secondary text-secondary';
      }
    },
    getTypeName(type) {
      switch (type) {
        case 'b2b': return 'B2B';
        case 'warehouse': return 'KHO BÃI';
        case 'system': return 'HỆ THỐNG';
        default: return 'KHÁC';
      }
    },
    markAsRead(id) {
      const notif = this.notifications.find(n => n.id === id);
      if (notif) notif.isRead = true;
    },
    markAllAsRead() {
      this.notifications.forEach(n => n.isRead = true);
    }
  }
};
</script>

<style scoped>
.action-btn {
  background: none;
  border: none;
  padding: 6px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1.25rem;
}

.action-btn:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(40, 167, 69, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}
</style>
