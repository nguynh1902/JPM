<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ Thống /</span> Trung Tâm Thông Báo
    </h4>

    <!-- Card danh sách -->
    <div class="card">
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
              <td colspan="5" class="text-center py-4 text-muted">Không có thông báo nào.</td>
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
                  <!-- Chấm xanh nháy cho thông báo chưa đọc -->
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
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredNotifications && filteredNotifications.length) ? filteredNotifications.length : 0 }} mục</span>
      </div>
    </div>
  </div>
</template>

<script>
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
          message: "Lô hàng mã #JP-1024 đã sẵn sàng để Cross-docking.",
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
      ]
    };
  },
  computed: {
    filteredNotifications() {
      return this.notifications.filter(item => {
        const matchSearch = item.title.toLowerCase().includes(this.filters.search.toLowerCase()) || 
                            item.message.toLowerCase().includes(this.filters.search.toLowerCase());
        const matchStatus = this.filters.status === 'all' || 
                            (this.filters.status === 'read' && item.isRead) || 
                            (this.filters.status === 'unread' && !item.isRead);
        const matchType = this.filters.type === 'all' || item.type === this.filters.type;
        
        return matchSearch && matchStatus && matchType;
      });
    }
  },
  methods: {
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
