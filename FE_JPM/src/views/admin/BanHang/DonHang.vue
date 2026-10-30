<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Kinh Doanh & Bán Hàng /</span> Yêu Cầu & Đơn Khách
    </h4>

    <div class="nav-align-top mb-4">
      <!-- SNEAT ADMIN STANDARD NAV PILLS -->
      <ul class="nav nav-pills mb-3 gap-2 flex-wrap pt-2" style="column-gap: 0.75rem !important;" role="tablist">
        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'pending_quotes' }"
            @click="switchTab('pending_quotes')"
            role="tab"
            title="Yêu cầu báo giá mới từ khách"
          >
            <i class="bx bx-time-five me-1"></i>
            <span>Chờ Báo Giá</span>
            <span v-if="countPendingQuotes > 0" class="tab-count-badge ms-2">{{ countPendingQuotes }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'quoted_quotes' }"
            @click="switchTab('quoted_quotes')"
            role="tab"
            title="Báo giá đã gửi, đang chờ khách chốt đơn hoặc đặt cọc"
          >
            <i class="bx bx-send me-1"></i>
            <span>Đã Báo Giá</span>
            <span v-if="countQuotedQuotes > 0" class="tab-count-badge ms-2">{{ countQuotedQuotes }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'processing' }"
            @click="switchTab('processing')"
            role="tab"
            title="Đơn hàng đang gom phụ tùng, chuẩn bị xuất kho hoặc lên đơn Nhật"
          >
            <i class="bx bx-package me-1"></i>
            <span>Đang Xử Lý</span>
            <span v-if="countProcessing > 0" class="tab-count-badge ms-2">{{ countProcessing }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'shipping' }"
            @click="switchTab('shipping')"
            role="tab"
            title="Đơn hàng đã bàn giao vận chuyển (ViettelPost / Chành xe), đang trên đường giao"
          >
            <i class="bx bxs-truck me-1"></i>
            <span>Đang Giao</span>
            <span v-if="countShipping > 0" class="tab-count-badge ms-2">{{ countShipping }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'completed' }"
            @click="switchTab('completed')"
            role="tab"
            title="Đơn hàng hoàn tất và đối soát 100%"
          >
            <i class="bx bx-check-circle me-1"></i>
            <span>Hoàn Thành</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'cancelled' }"
            @click="switchTab('cancelled')"
            role="tab"
            title="Yêu cầu hoặc đơn hàng đã bị hủy"
          >
            <i class="bx bx-x-circle me-1"></i>
            <span>Đã Hủy</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'all' }"
            @click="switchTab('all')"
            role="tab"
            title="Toàn bộ báo giá và đơn hàng"
          >
            <i class="bx bx-list-ul me-1"></i>
            <span>Tất Cả</span>
          </button>
        </li>
      </ul>

      <!-- MAIN CONTENT AREA (Chuẩn Sneat Admin) -->
      <div>
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ tabTitle }}</h5>
          </div>

          <!-- Bộ lọc (Filters) chuẩn Admin Sneat -->
          <div class="card-body pt-3 pb-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <div class="flex-grow-1">
                <input 
                  type="text" 
                  class="form-control" 
                  placeholder="Tìm kiếm mã giao dịch, tên khách hàng, số điện thoại..." 
                  v-model="searchQuery"
                >
              </div>
              <div style="width: 200px;" class="flex-shrink-0" v-if="currentTab === 'all'">
                <v-select
                  v-model="typeFilter"
                  :options="[
                    { value: 'all', label: 'Tất cả phân loại' },
                    { value: 'quote', label: 'Chỉ Báo Giá' },
                    { value: 'order', label: 'Chỉ Đơn Hàng' }
                  ]"
                  placeholder="Phân loại"
                  :can-clear="false"
                />
              </div>
              <button class="btn btn-secondary text-nowrap flex-shrink-0" @click="searchQuery = ''; typeFilter = 'all'">
                Đặt lại
              </button>
              <button 
                v-if="currentTab === 'processing'"
                type="button" 
                class="btn btn-primary text-nowrap shadow-sm flex-shrink-0" 
                @click="exportJapanPO" 
                title="Xuất file CSV UTF-8 đơn đặt hàng nhà cung cấp Nhật"
              >
                <i class="bx bx-download me-1"></i> Xuất Excel Đặt Hàng Nhật (PO)
              </button>
            </div>
          </div>

          <!-- Bảng dữ liệu (Table) chuẩn Admin Sneat -->
          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center text-uppercase" style="width: 5%">STT</th>
                  <th class="text-uppercase" style="width: 16%">Mã Giao Dịch</th>
                  <th class="text-uppercase" style="width: 21%">Khách Hàng</th>
                  <th class="text-uppercase" style="width: 13%">Thời Gian</th>
                  <th class="text-end text-uppercase" style="width: 13%">Tổng Tiền</th>
                  <th class="text-end text-uppercase" style="width: 13%">Tiền Cọc</th>
                  <th class="text-center text-uppercase" style="width: 11%">Trạng Thái</th>
                  <th class="text-center text-uppercase" style="width: 8%; min-width: 100px;">Hành Động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-for="(item, index) in filteredTabList" :key="item._type + item.id">
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td>
                    <div class="d-flex align-items-center gap-1">
                      <span class="fw-bold text-dark">{{ item.code }}</span>
                      <button type="button" class="btn btn-sm btn-icon p-0 text-muted" @click="copyText(item.code, 'Đã copy mã!')" title="Sao chép mã">
                        <i class="bx bx-copy" style="font-size: 0.85rem;"></i>
                      </button>
                    </div>
                  </td>
                  <td>
                    <span class="fw-bold text-dark d-block mb-1">{{ item.customer_name }}</span>
                    <div class="text-muted" style="font-size: 0.8rem;">
                      {{ item.customer_phone }}
                    </div>
                    <div v-if="item.address" class="text-muted text-truncate mt-1" style="font-size: 0.75rem; max-width: 220px;" :title="item.address">
                      {{ item.address }}
                    </div>
                  </td>
                  <td>
                    <span class="d-block text-dark fw-medium mb-1" style="font-size: 0.85rem;">
                      {{ item.date }}
                    </span>
                    <span class="text-muted" style="font-size: 0.75rem;">
                      {{ item.time }}
                    </span>
                  </td>
                  <td class="text-end">
                    <span class="fw-bold text-danger">{{ formatCurrency(item.total_amount) }}</span>
                    <div v-if="item.freight_collect" class="text-muted" style="font-size: 0.72rem;">(Chưa gồm cước)</div>
                  </td>
                  <td class="text-end">
                    <!-- Phân loại Đơn Hàng -->
                    <template v-if="item._type === 'order'">
                      <span v-if="item.paid_amount > 0" class="fw-bold text-success">{{ formatCurrency(item.paid_amount) }}</span>
                      <span v-else-if="item.deposit_required > 0" class="fw-bold text-dark">{{ formatCurrency(item.deposit_required) }}</span>
                      <span v-else class="text-muted fw-bold d-block text-center">-</span>
                    </template>

                    <!-- Phân loại Báo Giá -->
                    <template v-else>
                      <span v-if="item.status === 0" class="text-muted fw-bold d-block text-center">-</span>
                      <span v-else-if="item.da_thu > 0" class="fw-bold text-success">{{ formatCurrency(item.da_thu) }}</span>
                      <span v-else-if="item.deposit_required > 0" class="fw-bold text-dark">{{ formatCurrency(item.deposit_required) }}</span>
                      <span v-else class="text-muted fw-bold d-block text-center">-</span>
                    </template>
                  </td>
                  <td class="text-center">
                    <!-- Trạng thái Báo Giá -->
                    <template v-if="item._type === 'quote'">
                      <span v-if="item.status === 0" class="badge badge-custom bg-pastel-warning">CHỜ BÁO GIÁ</span>
                      <template v-else-if="item.status === 1">
                        <span v-if="item.deposit_required > 0 && Number(item.da_thu || 0) === 0" class="badge badge-custom bg-pastel-warning">CHỜ CỌC</span>
                        <span v-else class="badge badge-custom bg-pastel-info">ĐÃ BÁO GIÁ</span>
                      </template>
                      <span v-else-if="item.status === 2" class="badge badge-custom bg-pastel-warning">ĐANG XỬ LÝ</span>
                      <span v-else-if="item.status === 3" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                      <span v-else-if="item.status === 4" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                      <span v-else class="badge badge-custom bg-pastel-secondary">KHÁC</span>
                    </template>
                    <!-- Trạng thái Đơn Hàng -->
                    <template v-else>
                      <template v-if="item.status === 0">
                        <span v-if="item.deposit_required > 0 && Number(item.paid_amount || 0) === 0" class="badge badge-custom bg-pastel-warning">CHỜ CỌC</span>
                        <span v-else class="badge badge-custom bg-pastel-info">CHỜ DUYỆT</span>
                      </template>
                      <span v-else-if="item.status === 1" class="badge badge-custom bg-pastel-warning">ĐANG XỬ LÝ</span>
                      <span v-else-if="item.status === 2" class="badge badge-custom bg-pastel-info">ĐANG GIAO HÀNG</span>
                      <span v-else-if="item.status === 3" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                      <span v-else-if="item.status === 4" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                      <span v-else class="badge badge-custom bg-pastel-secondary">KHÁC</span>
                    </template>
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                      <!-- Nút Xử lý / Xem chi tiết -->
                      <button 
                        type="button" 
                        class="action-btn text-primary" 
                        @click="handleAction(item)" 
                        data-bs-toggle="modal" 
                        :data-bs-target="item._type === 'quote' ? '#quoteModal' : '#orderModal'" 
                        :title="canProcessOrders ? (item._type === 'quote' && item.status === 0 ? 'Báo giá' : 'Xử lý') : 'Xem chi tiết'"
                      >
                        <i :class="canProcessOrders ? 'bx bx-edit-alt' : 'bx bx-show'"></i>
                      </button>

                      <!-- Nút Copy Link Báo Giá / Đơn Hàng (Chỉ hiển thị ở tab ĐÃ BÁO GIÁ để gửi khách) -->
                      <button 
                        v-if="(item._type === 'quote' && item.status === 1 && item.token) || (item._type === 'order' && item.status === 0)" 
                        type="button" 
                        class="action-btn text-info" 
                        @click="copyQuoteLink(item)" 
                        :title="item._type === 'quote' ? 'Sao chép link báo giá gửi khách' : 'Sao chép link tra cứu đơn gửi khách'"
                      >
                        <i class="bx bx-link"></i>
                      </button>

                      <!-- Nút Đã Cọc & Lên Đơn (Báo giá hoặc Đơn chờ cọc) -->
                      <button 
                        v-if="(item._type === 'quote' && item.status === 1) || (item._type === 'order' && item.status === 0)" 
                        type="button" 
                        class="action-btn text-success" 
                        @click="confirmDepositBtn(item)" 
                        title="Đã cọc -> Bắt đầu lên đơn xử lý"
                      >
                        <i class="bx bx-check-circle"></i>
                      </button>

                      <!-- Nút Gom Đơn Xong -> Chuyển Sang Đang Giao (Đang xử lý / gom hàng) -->
                      <button 
                        v-if="(item._type === 'order' && item.status === 1) || (item._type === 'quote' && item.status === 2)" 
                        type="button" 
                        class="action-btn text-success" 
                        @click="startShippingBtn(item)" 
                        title="Gom đơn xong -> Chuyển sang Đang Giao"
                      >
                        <i class="bx bx-check-circle"></i>
                      </button>

                      <!-- Nút Khách Hàng Đã Nhận Hàng & Chuyển Tiền (Đang giao hàng) -->
                      <button 
                        v-if="item._type === 'order' && item.status === 2" 
                        type="button" 
                        class="action-btn text-success" 
                        @click="completeOrderBtn(item)" 
                        title="Khách hàng đã nhận hàng & chuyển tiền -> Hoàn thành đơn"
                      >
                        <i class="bx bx-check-circle"></i>
                      </button>

                      <!-- Nút Hủy Đơn / Hủy Yêu Cầu -->
                      <button 
                        v-if="(item._type === 'quote' && item.status !== 3 && item.status !== 4) || (item._type === 'order' && item.status !== 4 && item.status !== 3)" 
                        type="button" 
                        class="action-btn text-danger" 
                        @click="handleCancel(item)" 
                        :title="item._type === 'quote' ? 'Hủy yêu cầu báo giá này' : 'Hủy đơn hàng này'"
                      >
                        <i class="bx bx-x-circle"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredTabList.length === 0">
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer py-3 border-top">
            <span class="text-muted fw-medium">Tổng cộng: {{ (filteredTabList && filteredTabList.length) ? filteredTabList.length : 0 }} mục</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Chi Tiết & Báo Giá (Từ YeuCauBaoGia) -->
    <div class="modal fade" id="quoteModal" tabindex="-1" aria-hidden="true" ref="quoteModal">
      <div class="modal-dialog modal-xl mt-3 mt-lg-4" style="max-width: 1400px; width: 95%;" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
              Báo Giá Yêu Cầu - <span class="text-primary">{{ selectedQuote?.code }}</span>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="quoteModalCloseBtn"></button>
          </div>
          <form @submit.prevent="saveQuote">
            <div class="modal-body pb-2" v-if="selectedQuote">
              <div class="row">
                <!-- Cột 1 (Trái): Thông tin Khách Hàng & Ghi chú -->
                <div class="col-lg-3 col-12 mb-3 mb-lg-0 border-end d-flex flex-column">
                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Thông tin Khách Hàng</h6>
                  <div class="mb-3">
                    <p class="mb-1 fw-bold text-dark"><i class="bx bx-user text-primary me-2"></i>{{ selectedQuote.customer_name }}</p>
                    <p class="mb-1 text-dark"><i class="bx bx-phone text-primary me-2"></i>{{ selectedQuote.customer_phone }}</p>
                    <p class="mb-2 text-dark" v-if="selectedQuote.customer_email"><i class="bx bx-envelope text-primary me-2"></i>{{ selectedQuote.customer_email }}</p>
                    
                    <div class="mt-2">
                      <label class="form-label text-dark small mb-1 fw-bold"><i class="bx bx-map text-primary me-1"></i>Địa Chỉ Giao Hàng:</label>
                      <textarea 
                        v-if="!isQuoteReadOnly"
                        class="form-control form-control-sm text-dark" 
                        rows="2"
                        v-model="selectedQuote.address" 
                        placeholder="Số nhà, đường, bến tàu, chành xe..."
                      ></textarea>
                      <p v-else class="mb-0 text-dark small" style="white-space: pre-wrap;">
                        <i class="bx bx-map text-primary me-1"></i>{{ selectedQuote.address || 'Chưa cung cấp địa chỉ' }}
                      </p>
                    </div>
                  </div>

                  <!-- Thông Tin Vận Chuyển: Khi Đang Xử Lý (status = 2) hiển thị ô nhập; Khi Đã Giao/Hoàn Thành hiển thị thông tin -->
                  <div class="mb-3 p-2 bg-light border rounded" v-if="selectedQuote.status === 2">
                    <h6 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 0.72rem;">
                      <i class="bx bxs-truck me-1"></i>Thông Tin Vận Chuyển
                    </h6>
                    <div class="mb-2">
                      <label class="form-label text-dark small mb-1 fw-semibold">Đơn vị vận chuyển:</label>
                      <input 
                        type="text" 
                        class="form-control form-control-sm text-dark bg-white" 
                        v-model="selectedQuote.ten_don_vi_van_chuyen" 
                        placeholder="ViettelPost, GHTK, Chành xe Tô Châu, Phương Trang..."
                      />
                    </div>
                    <div class="mb-1">
                      <label class="form-label text-dark small mb-1 fw-semibold">Mã vận đơn:</label>
                      <input 
                        type="text" 
                        class="form-control form-control-sm text-danger fw-bold bg-white" 
                        v-model="selectedQuote.ma_van_don" 
                        placeholder="Nhập mã vận đơn (nếu có)..."
                      />
                    </div>
                  </div>

                  <div class="mb-3 p-2 bg-light border rounded" v-else-if="selectedQuote.status >= 3 && (selectedQuote.ten_don_vi_van_chuyen || selectedQuote.ma_van_don)">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <h6 class="fw-bold mb-0 text-uppercase text-primary" style="font-size: 0.72rem;">
                        <i class="bx bxs-truck me-1"></i>Vận Chuyển
                      </h6>
                      <span class="badge bg-label-info" style="font-size: 0.65rem;">
                        {{ selectedQuote.status === 4 ? 'Đã Giao Xong' : 'Đang Vận Chuyển' }}
                      </span>
                    </div>
                    <div class="small text-dark mb-1">
                      <span class="text-muted">Đơn vị:</span> 
                      <span class="fw-bold ms-1">{{ selectedQuote.ten_don_vi_van_chuyen || 'Chành xe / Giao trực tiếp' }}</span>
                    </div>
                    <div class="small text-dark d-flex align-items-center justify-content-between">
                      <div>
                        <span class="text-muted">Mã vận đơn:</span> 
                        <span class="fw-bold text-danger ms-1">{{ selectedQuote.ma_van_don || 'Chưa có mã' }}</span>
                      </div>
                      <button 
                        v-if="selectedQuote.ma_van_don" 
                        type="button" 
                        class="btn btn-sm btn-icon btn-link p-0 text-primary" 
                        @click="copyText(selectedQuote.ma_van_don, 'Đã sao chép mã vận đơn!')"
                        title="Sao chép mã vận đơn"
                      >
                        <i class="bx bx-copy"></i>
                      </button>
                    </div>
                  </div>

                  <h6 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 0.75rem;">Ghi Chú (Khách Hàng)</h6>
                  <div class="p-3 bg-label-secondary rounded flex-grow-1 mb-3 overflow-auto" style="min-height: 140px; max-height: 240px;">
                    <p class="mb-0 text-dark" style="font-size: 0.85rem; white-space: pre-wrap;">{{ selectedQuote.customer_notes || 'N/A.' }}</p>
                  </div>

                  <div class="mt-auto">
                    <h6 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 0.75rem;">Ghi Chú Zalo (Nội Bộ)</h6>
                    <div class="mb-0">
                      <textarea v-if="!isQuoteReadOnly" class="form-control" rows="2" v-model="selectedQuote.zalo_notes" placeholder="Ghi chú kết quả thương lượng Zalo..."></textarea>
                      <div v-else class="p-2 bg-light rounded text-dark border" style="white-space: pre-wrap; font-size: 0.85rem;">{{ selectedQuote.zalo_notes || 'N/A' }}</div>
                    </div>
                  </div>
                </div>

                <!-- Cột 2 (Giữa): Danh sách Phụ Tùng (Cuộn dọc) -->
                <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Chi Tiết Sản Phẩm Cần Báo Giá</h6>
                  
                  <!-- Bảng danh sách phụ tùng (cuộn bên trong khi có nhiều sản phẩm) -->
                  <div class="table-responsive border rounded custom-scroll" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-bordered table-sm mb-0 table-sticky-header">
                      <thead class="table-light sticky-top">
                        <tr>
                          <th class="text-center text-uppercase bg-light" style="width: 7%">STT</th>
                          <th class="text-uppercase bg-light" style="width: 38%">Tên Phụ Tùng</th>
                          <th class="text-center text-uppercase bg-light" style="width: 12%">SL</th>
                          <th class="text-uppercase bg-light" style="width: 23%">Đơn Giá</th>
                          <th class="text-end text-uppercase bg-light" style="width: 20%">Thành Tiền</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="!selectedQuote.items || selectedQuote.items.length === 0">
                          <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bx bx-folder-open text-secondary fs-2 d-block mb-1"></i>
                            Không có dữ liệu
                          </td>
                        </tr>
                        <tr v-for="(detail, idx) in selectedQuote.items" :key="idx">
                          <td class="text-center align-middle">{{ idx + 1 }}</td>
                          <td class="align-middle">
                            <span class="d-block fw-bold" style="font-size: 0.85rem;">{{ detail.part_name }}</span>
                            <span class="text-dark fw-medium" style="font-size: 0.75rem;">Mã: {{ detail.part_code }}</span>
                          </td>
                          <td class="text-center align-middle">{{ detail.qty }}</td>
                          <td class="align-middle" style="min-width: 140px;">
                            <input 
                              v-if="!isQuoteReadOnly" 
                              type="number" 
                              class="form-control form-control-sm text-end fw-bold text-dark" 
                              :value="detail.price ? detail.price : ''" 
                              @input="detail.price = $event.target.value === '' ? 0 : Number($event.target.value)" 
                              min="0"
                              placeholder="0" 
                            />
                            <span v-else class="fw-bold text-dark d-block text-end">{{ formatCurrency(detail.price || 0) }}</span>
                          </td>
                          <td class="text-end fw-bold text-danger align-middle">
                            {{ formatCurrency(detail.qty * (detail.price || 0)) }}
                            <div v-if="detail.qty > 1" class="text-dark fw-bold" style="font-size: 0.75rem;">
                              x{{ detail.qty }} cái
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                </div>

                <!-- Cột 3 (Phải): Tổng Hợp Chi Phí & Thanh Toán -->
                <div class="col-lg-3 col-12 border-start ps-lg-3 pe-lg-3 d-flex flex-column justify-content-between">
                  <div>
                    <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Tổng Hợp Chi Phí</h6>
                    
                    <!-- Tạm Tính -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                      <span class="text-dark fw-bold" style="font-size: 0.88rem;">Tạm Tính:</span>
                      <span class="fw-bold text-dark fs-6">{{ formatCurrency(calculateTotal(selectedQuote.items)) }}</span>
                    </div>

                    <!-- Phí Vận Chuyển -->
                    <div class="mb-3 pb-2 border-bottom">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="text-dark fw-bold mb-0" style="font-size: 0.88rem;">Phí Vận Chuyển:</label>
                        <div style="width: 140px;" v-if="!isQuoteReadOnly">
                          <input 
                            type="number" 
                            class="form-control form-control-sm text-end text-danger fw-bold" 
                            :value="selectedQuote.shipping_fee ? selectedQuote.shipping_fee : ''" 
                            @input="selectedQuote.shipping_fee = $event.target.value === '' ? 0 : Number($event.target.value)" 
                            min="0"
                            :disabled="selectedQuote.freight_collect" 
                            placeholder="0"
                          >
                        </div>
                        <span v-else class="fw-bold text-danger fs-6">
                          {{ selectedQuote.freight_collect ? 'Khách trả cước' : formatCurrency(selectedQuote.shipping_fee || 0) }}
                        </span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mt-1" v-if="!isQuoteReadOnly">
                        <div class="form-check form-switch mb-0">
                          <input class="form-check-input" type="checkbox" id="freightCollectQuote" v-model="selectedQuote.freight_collect" @change="selectedQuote.freight_collect ? selectedQuote.shipping_fee = 0 : null">
                          <label class="form-check-label text-dark fw-semibold" for="freightCollectQuote" style="font-size: 0.8rem;">Khách trả cước</label>
                        </div>
                        <span v-if="!selectedQuote.freight_collect" class="text-danger fw-semibold" style="font-size: 0.75rem;">
                          {{ formatCurrency(selectedQuote.shipping_fee || 0) }}
                        </span>
                      </div>
                    </div>

                    <!-- Yêu Cầu Cọc -->
                    <div class="mb-3 pb-2 border-bottom">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="text-dark fw-bold mb-0" style="font-size: 0.88rem;">Yêu Cầu Cọc:</label>
                        <div style="width: 140px;" v-if="!isQuoteReadOnly">
                          <input 
                            type="number" 
                            class="form-control form-control-sm text-end text-danger fw-bold" 
                            :value="selectedQuote.deposit_required ? selectedQuote.deposit_required : ''" 
                            @input="selectedQuote.deposit_required = $event.target.value === '' ? 0 : Number($event.target.value)" 
                            min="0"
                            placeholder="0" 
                          >
                        </div>
                        <span v-else class="fw-bold text-danger fs-6">{{ formatCurrency(selectedQuote.deposit_required || 0) }}</span>
                      </div>
                      <div v-if="!isQuoteReadOnly" class="text-end">
                        <span class="text-danger fw-semibold" style="font-size: 0.75rem;">
                          {{ formatCurrency(selectedQuote.deposit_required || 0) }}
                        </span>
                      </div>
                    </div>

                    <!-- KHU VỰC BIÊN LAI CHUYỂN TIỀN (1 ROW 2 PHẦN - Chỉ hiển thị khi ĐÃ BÁO GIÁ status >= 1, ẩn khi CHỜ BÁO GIÁ) -->
                    <div class="mt-3 pt-2 border-top mb-3" v-if="selectedQuote && selectedQuote.status >= 1">
                      <!-- Input file ẩn để kích hoạt khi bấm nút hoặc bấm ô ảnh -->
                      <input type="file" ref="quoteReceiptSlot1" class="d-none" accept="image/*" @change="onFileSelected($event, 'quote', 1)">
                      <input type="file" ref="quoteReceiptSlot2" class="d-none" accept="image/*" @change="onFileSelected($event, 'quote', 2)">

                      <div class="mb-2">
                        <label class="form-label fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.75rem;">
                          <i class="bx bx-receipt me-1 text-primary"></i>Biên Lai Chuyển Tiền
                        </label>
                      </div>

                      <!-- 1 ROW GỒM 2 PHẦN CHO 2 ẢNH -->
                      <div class="row g-2">
                        <!-- Phần 1: Ảnh 1 (Cọc) -->
                        <div class="col-6">
                          <div 
                            class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                            style="height: 105px; cursor: pointer;"
                            :style="!selectedQuote.receipt_image ? 'border-style: dashed !important;' : ''"
                            @paste="onPasteReceipt($event, 'quote', 1)"
                            @click="!selectedQuote.receipt_image && !isQuoteReadOnly ? triggerAddReceiptSlot('quote', 1) : null"
                            tabindex="0"
                            :title="selectedQuote.receipt_image ? 'Nhấp để xem phóng to' : 'Nhấp để tải ảnh hoặc Ctrl+V để dán'"
                          >
                            <div v-if="uploadingKey === 'quote_1'" class="text-center">
                              <span class="spinner-border spinner-border-sm text-primary"></span>
                              <div class="text-muted small mt-1" style="font-size: 0.65rem;">Đang tải...</div>
                            </div>
                            <template v-else-if="selectedQuote.receipt_image">
                              <img 
                                :src="getImageUrl(selectedQuote.receipt_image)" 
                                alt="Biên lai 1" 
                                class="rounded" 
                                style="max-height: 95px; max-width: 100%; object-fit: contain;" 
                                @click.stop="openReceiptViewer(selectedQuote.receipt_image)" 
                                title="Nhấp để xem phóng to"
                              />
                              <button 
                                v-if="!isQuoteReadOnly" 
                                type="button" 
                                class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm border" 
                                @click.stop="removeReceipt('quote', 1)" 
                                style="width: 24px; height: 24px; padding: 0; line-height: 1;" 
                                title="Xóa ảnh"
                              >
                                <i class="bx bx-trash" style="font-size: 13px;"></i>
                              </button>
                            </template>
                            <div v-else class="text-center text-muted">
                              <i class="bx bx-image-add fs-3 d-block text-secondary mb-1"></i>
                              <span style="font-size: 0.68rem;">Ảnh 1</span>
                            </div>
                          </div>
                        </div>

                        <!-- Phần 2: Ảnh 2 (Còn lại) -->
                        <div class="col-6">
                          <div 
                            class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                            style="height: 105px; cursor: pointer;"
                            :style="!selectedQuote.receipt_image_2 ? 'border-style: dashed !important;' : ''"
                            @paste="onPasteReceipt($event, 'quote', 2)"
                            @click="!selectedQuote.receipt_image_2 && !isQuoteReadOnly ? triggerAddReceiptSlot('quote', 2) : null"
                            tabindex="0"
                            :title="selectedQuote.receipt_image_2 ? 'Nhấp để xem phóng to' : 'Nhấp để tải ảnh hoặc Ctrl+V để dán'"
                          >
                            <div v-if="uploadingKey === 'quote_2'" class="text-center">
                              <span class="spinner-border spinner-border-sm text-primary"></span>
                              <div class="text-muted small mt-1" style="font-size: 0.65rem;">Đang tải...</div>
                            </div>
                            <template v-else-if="selectedQuote.receipt_image_2">
                              <img 
                                :src="getImageUrl(selectedQuote.receipt_image_2)" 
                                alt="Biên lai 2" 
                                class="rounded" 
                                style="max-height: 95px; max-width: 100%; object-fit: contain;" 
                                @click.stop="openReceiptViewer(selectedQuote.receipt_image_2)" 
                                title="Nhấp để xem phóng to"
                              />
                              <button 
                                v-if="!isQuoteReadOnly" 
                                type="button" 
                                class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm border" 
                                @click.stop="removeReceipt('quote', 2)" 
                                style="width: 24px; height: 24px; padding: 0; line-height: 1;" 
                                title="Xóa ảnh"
                              >
                                <i class="bx bx-trash" style="font-size: 13px;"></i>
                              </button>
                            </template>
                            <div v-else class="text-center text-muted">
                              <i class="bx bx-image-add fs-3 d-block text-secondary mb-1"></i>
                              <span style="font-size: 0.68rem;">Ảnh 2</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Cụm Tổng Cộng & Thu COD ở đáy -->
                  <div class="mt-3 pt-2 bg-light p-3 rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="fw-bold text-dark" style="font-size: 0.9rem;">Tổng Cộng:</span>
                      <span class="fw-bold text-danger fs-5">{{ formatCurrency(calculateTotal(selectedQuote.items) + (selectedQuote.freight_collect ? 0 : (selectedQuote.shipping_fee || 0))) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                      <span class="text-dark fw-bold" style="font-size: 0.9rem;">Thu COD:</span>
                      <span class="fw-bold text-dark fs-5">
                        {{ formatCurrency(Math.max(0, (calculateTotal(selectedQuote.items) + (selectedQuote.freight_collect ? 0 : (selectedQuote.shipping_fee || 0))) - (selectedQuote.deposit_required || 0))) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer border-top py-3 mt-2 bg-light d-flex justify-content-end align-items-center gap-2">
              <!-- Nút Hủy Đơn: Đặt bên phải cùng với các nút khác, chuẩn btn-outline-danger -->
              <button 
                type="button" 
                class="btn btn-outline-danger px-4 shadow-sm" 
                v-if="selectedQuote && selectedQuote.status !== 3 && !isQuoteReadOnly" 
                @click.prevent="cancelQuoteFromModal(selectedQuote)"
                title="Hủy yêu cầu báo giá này"
              >
                Hủy Đơn
              </button>

              <!-- Tiện ích Copy Link & Mã VietQR (khi ĐÃ BÁO GIÁ status >= 1) -->
              <template v-if="selectedQuote && selectedQuote.status >= 1">
                <button 
                  type="button" 
                  class="btn btn-outline-secondary shadow-sm d-inline-flex align-items-center justify-content-center" 
                  style="width: 38px; height: 38px; padding: 0;"
                  v-if="selectedQuote.token" 
                  @click.prevent="copyQuoteLink(selectedQuote)"
                  title="Sao chép link báo giá gửi khách"
                >
                  <i class="bx bx-link fs-5"></i>
                </button>

                <button 
                  type="button" 
                  class="btn btn-outline-secondary shadow-sm d-inline-flex align-items-center justify-content-center" 
                  style="width: 38px; height: 38px; padding: 0;"
                  @click.prevent="openVietQrModal(selectedQuote)"
                  title="Xem mã VietQR chuyển tiền cọc"
                >
                  <i class="bx bx-qr-scan fs-5"></i>
                </button>
              </template>

              <!-- Nút Đóng: Chuẩn btn-outline-secondary đồng bộ với các trang khác -->
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>

              <!-- Khi đang CHỜ BÁO GIÁ (Status 0): Nút Lưu Tạm và Nút Báo Giá -->
              <template v-if="selectedQuote && selectedQuote.status === 0 && !isQuoteReadOnly">
                <button 
                  type="button" 
                  class="btn btn-outline-primary px-4 shadow-sm" 
                  @click.prevent="saveDraftQuote"
                >
                  Lưu Tạm
                </button>
                <button 
                  type="button" 
                  class="btn btn-primary px-4 shadow-sm" 
                  @click.prevent="sendQuoteToCustomer"
                >
                  Báo Giá
                </button>
              </template>

              <!-- Khi ĐÃ BÁO GIÁ (Status 1): Nút Lưu và Nút Xác Nhận -->
              <template v-else-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly">
                <button 
                  type="button" 
                  class="btn btn-outline-primary px-4 shadow-sm" 
                  @click.prevent="saveDraftQuote"
                >
                  Lưu
                </button>
                <button 
                  type="button" 
                  class="btn btn-primary px-4 shadow-sm" 
                  @click.prevent="confirmDepositFromModal(selectedQuote)"
                  title="Khách đã cọc -> Bắt đầu lên đơn xử lý"
                >
                  Đã Cọc & Lên Đơn
                </button>
              </template>

              <!-- Khi ĐANG XỬ LÝ (Status 2): Nút Gom Đơn Xong -> Chuyển Sang Đang Giao -->
              <button 
                type="button" 
                class="btn btn-primary px-4 shadow-sm" 
                v-if="selectedQuote && selectedQuote.status === 2"
                @click.prevent="startShippingBtn(selectedQuote)"
                title="Đã gom xong hàng -> Bắt đầu giao hàng"
              >
                Gom Đơn Xong -> Chuyển Sang Đang Giao
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Chi Tiết Đơn Hàng -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl mt-3 mt-lg-4" style="max-width: 1400px; width: 95%;" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
              Chi Tiết Đơn Hàng - <span class="text-primary">{{ selectedOrder?.code }}</span>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="orderModalCloseBtn"></button>
          </div>
          <div class="modal-body pb-2" v-if="selectedOrder">
            <div class="row">
              <!-- Cột 1 (Trái): Thông tin Khách Hàng, Vận Chuyển & Ghi Chú -->
              <div class="col-lg-3 col-12 mb-3 mb-lg-0 border-end d-flex flex-column">
                <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Thông tin Khách Hàng</h6>
                <div class="mb-3">
                  <p class="mb-1 fw-bold text-dark"><i class="bx bx-user text-primary me-2"></i>{{ selectedOrder.customer_name }}</p>
                  <p class="mb-1 text-dark"><i class="bx bx-phone text-primary me-2"></i>{{ selectedOrder.customer_phone }}</p>
                  <p class="mb-2 text-dark" v-if="selectedOrder.customer_email"><i class="bx bx-envelope text-primary me-2"></i>{{ selectedOrder.customer_email }}</p>
                  <div class="mt-2">
                    <label class="form-label text-dark small mb-1 fw-bold"><i class="bx bx-map text-primary me-1"></i>Địa Chỉ Giao Hàng:</label>
                    <textarea 
                      v-if="!isOrderInfoReadOnly"
                      class="form-control form-control-sm text-dark" 
                      rows="2"
                      v-model="selectedOrder.address" 
                      placeholder="Số nhà, đường, phường/xã, bến tàu, chành xe..."
                    ></textarea>
                    <p v-else class="mb-0 text-dark small" style="white-space: pre-wrap;">
                      <i class="bx bx-map text-primary me-1"></i>{{ selectedOrder.address || 'Chưa có địa chỉ' }}
                    </p>
                  </div>
                </div>

                <!-- Thông Tin Vận Chuyển: Khi Đang Xử Lý (status = 1), hiển thị ô nhập; Khi Đang Giao / Hoàn Thành, hiển thị thông tin -->
                <div class="mb-3 p-2 bg-light border rounded" v-if="selectedOrder.status === 1">
                  <h6 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 0.72rem;">
                    <i class="bx bxs-truck me-1"></i>Thông Tin Vận Chuyển
                  </h6>
                  <div class="mb-2">
                    <label class="form-label text-dark small mb-1 fw-semibold">Đơn vị vận chuyển:</label>
                    <input 
                      type="text" 
                      class="form-control form-control-sm text-dark bg-white" 
                      v-model="selectedOrder.ten_don_vi_van_chuyen" 
                      placeholder="ViettelPost, GHTK, Chành xe Tô Châu, Phương Trang..."
                    />
                  </div>
                  <div class="mb-1">
                    <label class="form-label text-dark small mb-1 fw-semibold">Mã vận đơn:</label>
                    <input 
                      type="text" 
                      class="form-control form-control-sm text-danger fw-bold bg-white" 
                      v-model="selectedOrder.ma_van_don" 
                      placeholder="Nhập mã vận đơn (nếu có)..."
                    />
                  </div>
                </div>

                <div class="mb-3 p-2 bg-light border rounded" v-else-if="selectedOrder.status >= 2 || selectedOrder.ten_don_vi_van_chuyen || selectedOrder.ma_van_don">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold mb-0 text-uppercase text-primary" style="font-size: 0.72rem;">
                      <i class="bx bxs-truck me-1"></i>Vận Chuyển
                    </h6>
                    <span class="badge bg-label-info" style="font-size: 0.65rem;">
                      {{ selectedOrder.status === 3 ? 'Đã Giao Xong' : 'Đang Vận Chuyển' }}
                    </span>
                  </div>
                  <div class="small text-dark mb-1">
                    <span class="text-muted">Đơn vị:</span> 
                    <span class="fw-bold ms-1">{{ selectedOrder.ten_don_vi_van_chuyen || 'Chành xe / Giao trực tiếp' }}</span>
                  </div>
                  <div class="small text-dark d-flex align-items-center justify-content-between">
                    <div>
                      <span class="text-muted">Mã vận đơn:</span> 
                      <span class="fw-bold text-danger ms-1">{{ selectedOrder.ma_van_don || 'Chưa có mã' }}</span>
                    </div>
                    <button 
                      v-if="selectedOrder.ma_van_don" 
                      type="button" 
                      class="btn btn-sm btn-icon btn-link p-0 text-primary" 
                      @click="copyText(selectedOrder.ma_van_don, 'Đã sao chép mã vận đơn!')"
                      title="Sao chép mã vận đơn"
                    >
                      <i class="bx bx-copy"></i>
                    </button>
                  </div>
                </div>

                <!-- Ghi Chú Khách Hàng: Đồng bộ hoàn toàn với Modal Báo Giá -->
                <div class="flex-grow-1 d-flex flex-column mb-2">
                  <h6 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 0.75rem;">Ghi Chú (Khách Hàng)</h6>
                  <div class="p-3 bg-label-secondary rounded flex-grow-1 overflow-auto" style="min-height: 120px; max-height: 240px;">
                    <p class="mb-0 text-dark" style="font-size: 0.85rem; white-space: pre-wrap;">{{ selectedOrder.customer_notes || 'Không có ghi chú.' }}</p>
                  </div>
                </div>
              </div>

              <!-- Cột 2 (Giữa): Sản Phẩm Trong Đơn (Cuộn dọc) -->
              <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Sản Phẩm Trong Đơn</h6>
                <div class="table-responsive border rounded custom-scroll" style="max-height: 480px; overflow-y: auto;">
                  <table class="table table-bordered table-sm mb-0 table-sticky-header">
                    <thead class="table-light sticky-top">
                      <tr>
                        <th class="text-center text-uppercase bg-light" style="width: 7%">STT</th>
                        <th class="text-uppercase bg-light" style="width: 38%">Tên Phụ Tùng</th>
                        <th class="text-center text-uppercase bg-light" style="width: 15%">SL</th>
                        <th class="text-uppercase bg-light" style="width: 20%">Đơn Giá</th>
                        <th class="text-end text-uppercase bg-light" style="width: 20%">Thành Tiền</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="!selectedOrder.items || selectedOrder.items.length === 0">
                        <td colspan="5" class="text-center py-4 text-muted">
                          <i class="bx bx-folder-open text-secondary fs-2 d-block mb-1"></i>
                          Không có dữ liệu
                        </td>
                      </tr>
                      <tr v-for="(detail, idx) in selectedOrder.items" :key="idx">
                        <td class="text-center align-middle">{{ idx + 1 }}</td>
                        <td class="align-middle">
                          <span class="d-block fw-bold" style="font-size: 0.85rem;">{{ detail.part_name }}</span>
                          <span class="text-dark fw-medium" style="font-size: 0.75rem;">Mã: {{ detail.part_code }}</span>
                        </td>
                        <td class="text-center align-middle">
                          <span class="fw-bold fs-6 text-dark">{{ detail.qty }}</span>
                        </td>
                        <td class="text-end align-middle">{{ formatCurrency(detail.price) }}</td>
                        <td class="text-end fw-bold text-danger align-middle">
                          {{ formatCurrency(detail.qty * (detail.price || 0)) }}
                          <div v-if="detail.qty > 1" class="text-dark fw-bold" style="font-size: 0.75rem;">
                            x{{ detail.qty }} cái
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Cột 3 (Phải): Chi Phí & Thanh Toán -->
              <div class="col-lg-3 col-12 border-start ps-lg-3 pe-lg-3 d-flex flex-column justify-content-between">
                <div>
                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Tổng Hợp Chi Phí</h6>
                  
                  <!-- Tạm Tính -->
                  <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-dark fw-bold" style="font-size: 0.88rem;">Tạm Tính:</span>
                    <span class="fw-bold text-dark fs-6">{{ formatCurrency(calculateTotal(selectedOrder.items)) }}</span>
                  </div>

                  <!-- Phí Vận Chuyển -->
                  <div class="mb-3 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="text-dark fw-bold mb-0" style="font-size: 0.88rem;">Phí Vận Chuyển:</label>
                      <div style="width: 140px;" v-if="!isOrderInfoReadOnly">
                        <input 
                          type="number" 
                          class="form-control form-control-sm text-end text-danger fw-bold" 
                          :value="selectedOrder.shipping_fee ? selectedOrder.shipping_fee : ''" 
                          @input="selectedOrder.shipping_fee = $event.target.value === '' ? 0 : Number($event.target.value)" 
                          min="0"
                          :disabled="selectedOrder.freight_collect" 
                          placeholder="0"
                        >
                      </div>
                      <span v-else class="fw-bold text-danger fs-6">
                        {{ selectedOrder.freight_collect ? 'Khách trả cước' : formatCurrency(selectedOrder.shipping_fee || 0) }}
                      </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1" v-if="!isOrderInfoReadOnly">
                      <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="freightCollectOrder" v-model="selectedOrder.freight_collect" @change="selectedOrder.freight_collect ? selectedOrder.shipping_fee = 0 : null">
                        <label class="form-check-label text-dark fw-semibold" for="freightCollectOrder" style="font-size: 0.8rem;">Khách trả cước</label>
                      </div>
                      <span v-if="!selectedOrder.freight_collect" class="text-danger fw-semibold" style="font-size: 0.75rem;">
                        {{ formatCurrency(selectedOrder.shipping_fee || 0) }}
                      </span>
                    </div>
                  </div>

                  <!-- Yêu Cầu Cọc -->
                  <div class="mb-3 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="text-dark fw-bold mb-0" style="font-size: 0.88rem;">Yêu Cầu Cọc:</label>
                      <div style="width: 140px;" v-if="!isOrderInfoReadOnly">
                        <input 
                          type="number" 
                          class="form-control form-control-sm text-end text-danger fw-bold" 
                          :value="selectedOrder.deposit_required ? selectedOrder.deposit_required : ''" 
                          @input="selectedOrder.deposit_required = $event.target.value === '' ? 0 : Number($event.target.value)" 
                          min="0"
                          placeholder="0" 
                        >
                      </div>
                      <span v-else class="fw-bold text-danger fs-6">{{ formatCurrency(selectedOrder.deposit_required || 0) }}</span>
                    </div>
                    <div v-if="!isOrderInfoReadOnly" class="text-end">
                      <span class="text-danger fw-semibold" style="font-size: 0.75rem;">
                        {{ formatCurrency(selectedOrder.deposit_required || 0) }}
                      </span>
                    </div>
                  </div>

                  <!-- Đã Thu -->
                  <div class="mb-3 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="text-dark fw-bold mb-0" style="font-size: 0.88rem;">Đã Thu (Cọc / Trả trước):</label>
                      <div style="width: 140px;" v-if="!isOrderInfoReadOnly">
                        <input 
                          type="number" 
                          class="form-control form-control-sm text-end text-success fw-bold" 
                          :value="selectedOrder.paid_amount ? selectedOrder.paid_amount : ''" 
                          @input="selectedOrder.paid_amount = $event.target.value === '' ? 0 : Number($event.target.value)" 
                          min="0"
                          placeholder="0" 
                        >
                      </div>
                      <span v-else class="fw-bold text-success fs-6">{{ formatCurrency(selectedOrder.paid_amount || 0) }}</span>
                    </div>
                    <div v-if="!isOrderInfoReadOnly" class="text-end">
                      <span class="text-success fw-semibold" style="font-size: 0.75rem;">
                        {{ formatCurrency(selectedOrder.paid_amount || 0) }}
                      </span>
                    </div>
                  </div>

                  <!-- KHU VỰC BIÊN LAI CHUYỂN TIỀN (1 ROW 2 PHẦN) -->
                  <div class="mt-3 pt-2 border-top mb-3">
                    <!-- Input file ẩn để kích hoạt khi bấm nút hoặc bấm ô ảnh -->
                    <input type="file" ref="orderReceiptSlot1" class="d-none" accept="image/*" @change="onFileSelected($event, 'order', 1)">
                    <input type="file" ref="orderReceiptSlot2" class="d-none" accept="image/*" @change="onFileSelected($event, 'order', 2)">

                    <div class="mb-2">
                      <label class="form-label fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.75rem;">
                        <i class="bx bx-receipt me-1 text-primary"></i>Biên Lai Chuyển Tiền
                      </label>
                    </div>

                    <!-- 1 ROW GỒM 2 PHẦN CHO 2 ẢNH -->
                    <div class="row g-2">
                      <!-- Phần 1: Ảnh 1 (Cọc) -->
                      <div class="col-6">
                        <div 
                          class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                          style="height: 105px; cursor: pointer;"
                          :style="!selectedOrder.receipt_image ? 'border-style: dashed !important;' : ''"
                          @paste="onPasteReceipt($event, 'order', 1)"
                          @click="!selectedOrder.receipt_image && !isOrderReceiptReadOnly ? triggerAddReceiptSlot('order', 1) : null"
                          tabindex="0"
                          :title="selectedOrder.receipt_image ? 'Nhấp để xem phóng to' : 'Nhấp để tải ảnh hoặc Ctrl+V để dán'"
                        >
                          <div v-if="uploadingKey === 'order_1'" class="text-center">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <div class="text-muted small mt-1" style="font-size: 0.65rem;">Đang tải...</div>
                          </div>
                          <template v-else-if="selectedOrder.receipt_image">
                            <img 
                              :src="getImageUrl(selectedOrder.receipt_image)" 
                              alt="Biên lai 1" 
                              class="rounded" 
                              style="max-height: 95px; max-width: 100%; object-fit: contain;" 
                              @click.stop="openReceiptViewer(selectedOrder.receipt_image)" 
                              title="Nhấp để xem phóng to"
                            />
                            <button 
                              v-if="!isOrderReceiptReadOnly" 
                              type="button" 
                              class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm border" 
                              @click.stop="removeReceipt('order', 1)" 
                              style="width: 24px; height: 24px; padding: 0; line-height: 1;" 
                              title="Xóa ảnh"
                            >
                              <i class="bx bx-trash" style="font-size: 13px;"></i>
                            </button>
                          </template>
                          <div v-else class="text-center text-muted">
                            <i class="bx bx-image-add fs-3 d-block text-secondary mb-1"></i>
                            <span style="font-size: 0.68rem;">Ảnh 1</span>
                          </div>
                        </div>
                      </div>

                      <!-- Phần 2: Ảnh 2 (Còn lại) -->
                      <div class="col-6">
                        <div 
                          class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                          style="height: 105px; cursor: pointer;"
                          :style="!selectedOrder.receipt_image_2 ? 'border-style: dashed !important;' : ''"
                          @paste="onPasteReceipt($event, 'order', 2)"
                          @click="!selectedOrder.receipt_image_2 && !isOrderReceiptReadOnly ? triggerAddReceiptSlot('order', 2) : null"
                          tabindex="0"
                          :title="selectedOrder.receipt_image_2 ? 'Nhấp để xem phóng to' : 'Nhấp để tải ảnh hoặc Ctrl+V để dán'"
                        >
                          <div v-if="uploadingKey === 'order_2'" class="text-center">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <div class="text-muted small mt-1" style="font-size: 0.65rem;">Đang tải...</div>
                          </div>
                          <template v-else-if="selectedOrder.receipt_image_2">
                            <img 
                              :src="getImageUrl(selectedOrder.receipt_image_2)" 
                              alt="Biên lai 2" 
                              class="rounded" 
                              style="max-height: 95px; max-width: 100%; object-fit: contain;" 
                              @click.stop="openReceiptViewer(selectedOrder.receipt_image_2)" 
                              title="Nhấp để xem phóng to"
                            />
                            <button 
                              v-if="!isOrderReceiptReadOnly" 
                              type="button" 
                              class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm border" 
                              @click.stop="removeReceipt('order', 2)" 
                              style="width: 24px; height: 24px; padding: 0; line-height: 1;" 
                              title="Xóa ảnh"
                            >
                              <i class="bx bx-trash" style="font-size: 13px;"></i>
                            </button>
                          </template>
                          <div v-else class="text-center text-muted">
                            <i class="bx bx-image-add fs-3 d-block text-secondary mb-1"></i>
                            <span style="font-size: 0.68rem;">Ảnh 2</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cụm Tổng Cộng & Thu COD ở đáy (Đồng bộ 100% với Modal Báo Giá) -->
                <div class="mt-3 pt-2 bg-light p-3 rounded border">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">Tổng Cộng:</span>
                    <span class="fw-bold text-danger fs-5">{{ formatCurrency(calculateTotal(selectedOrder.items) + (selectedOrder.freight_collect ? 0 : (selectedOrder.shipping_fee || 0))) }}</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-dark fw-bold" style="font-size: 0.9rem;">Thu COD:</span>
                    <span :class="selectedOrder.status === 3 ? 'fw-bold text-success fs-5' : 'fw-bold text-dark fs-5'">
                      {{ formatCurrency(Math.max(0, (calculateTotal(selectedOrder.items) + (selectedOrder.freight_collect ? 0 : (selectedOrder.shipping_fee || 0))) - (selectedOrder.paid_amount || 0))) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top py-3 mt-2 bg-light d-flex justify-content-end align-items-center gap-2">
            <!-- Nút Hủy Đơn (Chỉ hủy được khi đơn chưa giao hàng) -->
            <button 
              type="button" 
              class="btn btn-outline-danger px-4 shadow-sm" 
              v-if="selectedOrder && selectedOrder.status < 2" 
              @click.prevent="handleCancel(selectedOrder)"
              title="Hủy đơn hàng này"
            >
              Hủy Đơn
            </button>
            <button 
              type="button" 
              class="btn btn-outline-secondary shadow-sm d-inline-flex align-items-center justify-content-center" 
              style="width: 38px; height: 38px; padding: 0;"
              @click.prevent="copyQuoteLink(selectedOrder)" 
              title="Sao chép link tra cứu đơn gửi khách"
            >
              <i class="bx bx-link fs-5"></i>
            </button>
            <button 
              type="button" 
              class="btn btn-outline-secondary shadow-sm d-inline-flex align-items-center justify-content-center" 
              style="width: 38px; height: 38px; padding: 0;"
              @click="openVietQrModal(selectedOrder)" 
              title="Xem mã VietQR"
            >
              <i class="bx bx-qr-scan fs-5"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-outline-primary px-4 shadow-sm" @click="saveOrder" v-if="!isOrderInfoReadOnly && canEditOrders">
              Lưu
            </button>
            <button 
              type="button" 
              class="btn btn-primary px-4 shadow-sm" 
              v-if="selectedOrder && selectedOrder.status === 0" 
              @click="confirmDepositBtn(selectedOrder)"
              title="Khách đã cọc -> Bắt đầu lên đơn xử lý"
            >
              Đã Cọc & Lên Đơn
            </button>
            <button 
              type="button" 
              class="btn btn-primary px-4 shadow-sm" 
              v-if="selectedOrder && selectedOrder.status === 1" 
              @click="startShippingBtn(selectedOrder)" 
              title="Đã gom xong hàng -> Bắt đầu giao hàng"
            >
              Gom Đơn Xong -> Chuyển Sang Đang Giao
            </button>
            <button 
              type="button" 
              class="btn btn-success px-4 shadow-sm" 
              v-if="selectedOrder && selectedOrder.status === 2" 
              @click="completeOrderBtn(selectedOrder)" 
              title="Khách hàng đã nhận hàng & chuyển tiền -> Hoàn thành đơn"
            >
              Khách Hàng Đã Nhận Hàng & Chuyển Tiền
            </button>
            <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" @click="printOrder">
              In Hóa Đơn
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 1: Hiển Thị Mã VietQR Cho Admin & Thu Ngân -->
    <div class="modal fade" id="adminVietQrModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title fw-bold text-white mb-0">
              <i class="bx bx-qr-scan me-2"></i>Mã VietQR Napas247 - {{ adminVietQr?.quote_code ? 'Tiền Cọc Báo Giá' : 'Đơn Hàng' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center p-4">
            <div v-if="isLoadingQr" class="py-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="text-muted mt-2">Đang kết nối cổng VietQR...</p>
            </div>
            <div v-else-if="adminVietQr">
              <div class="bg-white p-3 rounded-4 shadow-sm border d-inline-block mb-3">
                <img :src="adminVietQr.qr_url" alt="VietQR" class="img-fluid" style="max-width: 250px; border-radius: 8px;">
              </div>
              <div class="text-start bg-light p-3 rounded-3 border">
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Ngân Hàng:</span>
                  <span class="fw-bold text-dark">{{ adminVietQr.bank_name }} ({{ adminVietQr.bank_id }})</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Số Tài Khoản:</span>
                  <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-primary">{{ adminVietQr.account_no }}</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" @click="copyText(adminVietQr.account_no, 'Đã copy STK')">
                      <i class="bx bx-copy"></i>
                    </button>
                  </div>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Chủ Tài Khoản:</span>
                  <span class="fw-bold text-dark">{{ adminVietQr.account_name }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Số Tiền:</span>
                  <span class="fw-bold text-danger fs-6">{{ adminVietQr.amount_formatted }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                  <span class="text-muted">Cú Pháp Chuyển:</span>
                  <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-dark bg-white px-2 py-0 rounded border">{{ adminVietQr.transfer_content }}</span>
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1" @click="copyText(adminVietQr.transfer_content, 'Đã copy nội dung')">
                      <i class="bx bx-copy"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 2: Gửi Tin Nhắn Chành Xe (ZNS / SMS) Cho Chủ Tàu & Tài Xế -->
    <div class="modal fade" id="adminShippingModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-info text-white py-3">
            <h5 class="modal-title fw-bold text-white mb-0">
              <i class="bx bxs-truck me-2"></i>Gửi Thông Tin Chành Xe & Xuất Bến (Zalo ZNS / SMS)
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="sendShippingNotice">
            <div class="modal-body p-4" v-if="shippingOrder">
              <div class="alert alert-primary py-2 px-3 small mb-3">
                <i class="bx bx-info-circle me-1"></i> Đơn hàng: <strong>#{{ shippingOrder.code }}</strong> - Khách nhận: <strong>{{ shippingOrder.customer_name }} ({{ shippingOrder.customer_phone }})</strong>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tên Nhà Xe / Chành Xe <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="shippingForm.ten_nha_xe" placeholder="VD: Chành xe Tô Châu, Phương Trang..." required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Biển Số Xe</label>
                  <input type="text" class="form-control" v-model="shippingForm.bien_so_xe" placeholder="VD: 68C-123.45">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tên Tài Xế</label>
                  <input type="text" class="form-control" v-model="shippingForm.ten_tai_xe" placeholder="VD: Bác tài Nguyễn Văn Hùng">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Số Điện Thoại Tài Xế</label>
                  <input type="tel" class="form-control" v-model="shippingForm.sdt_tai_xe" placeholder="VD: 0918123456">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Số Kiện Hàng</label>
                  <input type="number" class="form-control" v-model.number="shippingForm.so_kien_hang" min="1" required>
                </div>
                <div class="col-md-8">
                  <label class="form-label fw-bold">Địa Điểm / Bến Trả Hàng</label>
                  <input type="text" class="form-control" v-model="shippingForm.dia_diem_tra_hang" placeholder="VD: Cảng cá Tắc Cậu, Rạch Giá, Kiên Giang">
                </div>
              </div>

              <hr class="my-4">

              <div class="bg-light p-3 rounded-3 border">
                <h6 class="fw-bold mb-2 text-dark small text-uppercase">Tùy Chọn Kênh Gửi Thông Báo</h6>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="notifyCust" v-model="shippingForm.notify_customer">
                  <label class="form-check-label" for="notifyCust">
                    <strong>Gửi SMS Brandname / Zalo ZNS cho Khách hàng / Chủ tàu</strong> (Kèm tên nhà xe, biển số, SĐT bác tài để ra nhận hàng).
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="notifyDriv" v-model="shippingForm.notify_driver" :disabled="!shippingForm.sdt_tai_xe">
                  <label class="form-check-label" for="notifyDriv">
                    <strong>Gửi SMS điều phối giao hàng cho Bác tài chành xe</strong> (Kèm mã đơn, tên và SĐT chủ tàu để liên hệ khi tới bến cảng).
                  </label>
                </div>
              </div>
            </div>
            <div class="modal-footer py-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-info text-white shadow-sm" :disabled="isSendingShipping">
                <span v-if="isSendingShipping" class="spinner-border spinner-border-sm me-1" role="status"></span>
                <i v-else class="bx bx-paper-plane me-1"></i>
                {{ isSendingShipping ? 'Đang gửi...' : 'Gửi Thông Báo Ngay' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Cập Nhật Vận Đơn Giao Hàng -->
    <div class="modal fade" id="viettelPostModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title fw-bold text-white mb-0">
              <i class="bx bxs-truck me-2"></i>Cập Nhật Vận Đơn Giao Hàng
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveViettelPostShipping">
            <div class="modal-body p-4" v-if="viettelPostOrder">
              <div class="alert alert-primary py-2 px-3 small mb-3">
                <i class="bx bx-info-circle me-1"></i> Đơn hàng: <strong>#{{ viettelPostOrder.code }}</strong> - Người nhận: <strong>{{ viettelPostOrder.customer_name }} ({{ viettelPostOrder.customer_phone }})</strong>
                <div class="mt-1 text-muted" v-if="viettelPostOrder.address">{{ viettelPostOrder.address }}</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Đơn Vị Vận Chuyển</label>
                <input type="text" class="form-control" v-model="viettelPostForm.ten_don_vi_van_chuyen" placeholder="VD: ViettelPost, Giao Hàng Nhanh, Chành xe...">
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Mã Vận Đơn / Ghi Chú Chành Xe</label>
                <input type="text" class="form-control" v-model="viettelPostForm.ma_van_don" placeholder="VD: VP123456789VN, GHN987654, Chành xe Tô Châu...">
                <small class="text-muted">Nhập mã vận đơn hoặc số xe/chành xe (hoặc để trống nếu tự giao).</small>
              </div>

              <div class="bg-light p-3 rounded-3 border">
                <div class="d-flex justify-content-between py-1 small">
                  <span class="text-muted">Tổng đơn hàng:</span>
                  <span class="fw-bold">{{ formatCurrency(viettelPostOrder.total_amount) }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                  <span class="text-muted">Khách đã đặt cọc:</span>
                  <span class="fw-bold text-success">{{ formatCurrency(viettelPostOrder.paid_amount || 0) }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-top mt-1 pt-1">
                  <span class="fw-bold text-dark">Số tiền thu COD khi giao hàng:</span>
                  <span class="fw-bold text-danger fs-6">{{ formatCurrency(Math.max(0, viettelPostOrder.total_amount - (viettelPostOrder.paid_amount || 0))) }}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer py-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSavingViettelPost">
                <span v-if="isSavingViettelPost" class="spinner-border spinner-border-sm me-1" role="status"></span>
                <i v-else class="bx bx-check me-1"></i>
                Lưu & Chuyển Sang Đang Giao
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Phóng To Xem Biên Lai / Ảnh Chuyển Khoản -->
    <div class="modal fade" id="receiptViewerModal" tabindex="-1" aria-hidden="true" ref="receiptViewerModal">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
              <i class="bx bx-receipt text-primary fs-5"></i>
              Ảnh Chụp Màn Hình Chuyển Khoản / Biên Lai
            </h6>
            <div class="d-flex align-items-center gap-2">
              <a 
                v-if="viewingReceiptImage" 
                :href="getImageUrl(viewingReceiptImage)" 
                target="_blank" 
                class="btn btn-sm btn-outline-primary py-1 px-2 d-inline-flex align-items-center gap-1"
                title="Mở tab mới để phóng to kích thước gốc"
              >
                <i class="bx bx-link-external"></i> Mở tab mới
              </a>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          </div>
          <div class="modal-body p-2 text-center bg-dark" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
            <img 
              v-if="viewingReceiptImage" 
              :src="getImageUrl(viewingReceiptImage)" 
              alt="Ảnh chuyển khoản ngân hàng" 
              class="img-fluid rounded" 
              style="max-height: 80vh; max-width: 100%; object-fit: contain; box-shadow: 0 4px 20px rgba(0,0,0,0.5);"
            >
          </div>
          <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
              <i class="bx bx-info-circle me-1"></i>Kiểm tra đối soát mã giao dịch, số tiền và tên người gửi
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import { useQuery, useQueryClient, useMutation } from '@tanstack/vue-query';
import { useAuthStore } from '../../../stores/authStore';
import { createToaster } from "@meforma/vue-toaster";
import Swal from 'sweetalert2';

export default {
  name: "DonHang",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    
    const { data: rawOrders, isFetching: isFetchingOrders } = useQuery({
      queryKey: ['don-hang'],
      queryFn: async () => {
        if (!navigator.onLine) {
           // window.alert("Bạn đang ngoại tuyến! Dữ liệu có thể không mới nhất.");
        }
        const res = await axiosClient.get('/admin/don-hang');
        return res.data;
      },
      staleTime: 1000 * 60 * 5
    });

    const { data: rawQuotes, isFetching: isFetchingQuotes } = useQuery({
      queryKey: ['yeu-cau-bao-gia'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/yeu-cau-bao-gia');
        return res.data;
      },
      staleTime: 1000 * 60 * 5
    });

    // Mutation: Xóa Báo Giá
    const deleteQuoteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/yeu-cau-bao-gia/${id}`),
      onError: (err, id, context) => {
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] })
    });

    // Mutation: Xóa Đơn Hàng
    const deleteOrderMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/don-hang/${id}`),
      onError: (err, id, context) => {
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['don-hang'] })
    });

    // Mutation: Lưu Báo Giá
    const saveQuoteMutation = useMutation({
      mutationFn: async ({ id, payload }) => axiosClient.put(`/admin/yeu-cau-bao-gia/${id}`, payload),
      onError: (err, vars, context) => {
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] })
    });

    // Mutation: Lưu Đơn Hàng
    const saveOrderMutation = useMutation({
      mutationFn: async ({ id, payload }) => axiosClient.put(`/admin/don-hang/${id}`, payload),
      onError: (err, vars, context) => {
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['don-hang'] })
    });

    return { authStore, queryClient, rawOrders, rawQuotes, isFetchingOrders, isFetchingQuotes, deleteQuoteMutation, deleteOrderMutation, saveQuoteMutation, saveOrderMutation, toaster };
  },
  data() {
    return {
      currentUserRole: JSON.parse(localStorage.getItem('auth_user') || '{}').roles?.[0]?.toUpperCase() || 'ADMIN',
      currentTab: 'pending_quotes',
      searchQuery: '',
      typeFilter: 'all',
      selectedQuote: null,
      selectedOrder: null,
      // Shipping Modal (Vận Đơn Giao Hàng)
      viettelPostOrder: null,
      viettelPostForm: {
        ma_van_don: "",
        ten_don_vi_van_chuyen: ""
      },
      isSavingViettelPost: false,
      // VietQR Modal
      adminVietQr: null,
      isLoadingQr: false,
      // Shipping Modal
      shippingOrder: null,
      shippingForm: {
        ten_nha_xe: "",
        bien_so_xe: "",
        ten_tai_xe: "",
        sdt_tai_xe: "",
        so_kien_hang: 1,
        dia_diem_tra_hang: "",
        notify_customer: true,
        notify_driver: true
      },
      isSendingShipping: false,
      // Receipt Viewer & Upload
      viewingReceiptImage: null,
      isUploadingReceipt: false,
      uploadingKey: ''
    };
  },
  computed: {
    canViewOrders() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Xem Đơn hàng');
    },
    canApproveOrders() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Duyệt Đơn hàng');
    },
    canEditOrders() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Sửa Đơn hàng');
    },
    canDeleteOrders() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Xóa Đơn hàng');
    },
    canProcessOrders() {
      return this.authStore?.isAdmin || this.canApproveOrders || this.canEditOrders;
    },
    canSendQuote() {
      if (!this.selectedQuote) return false;
      // Nếu đã lưu hoặc đã gửi báo giá trước đó (status >= 1)
      if (this.selectedQuote.status >= 1) return true;
      // Đối với yêu cầu mới (status = 0): Chỉ hiển thị khi đã định giá TOÀN BỘ phụ tùng (> 0) và đã xác định cước vận chuyển
      const items = this.selectedQuote.items || [];
      if (items.length === 0) return false;
      const allItemsPriced = items.every(item => Number(item.price) > 0);
      const hasShipping = (this.selectedQuote.shipping_fee > 0) || Boolean(this.selectedQuote.freight_collect);
      return allItemsPriced && hasShipping;
    },
    isQuoteReadOnly() {
      return this.selectedQuote && this.selectedQuote.status >= 2;
    },
    isOrderReadOnly() {
      return this.selectedOrder && this.selectedOrder.status >= 3;
    },
    // Đơn hàng khi ở trạng thái "Đang xử lý" trở đi (status >= 1) sẽ khóa toàn bộ thông tin giá/phí/địa chỉ/loại đơn (chỉ cho phép sửa biên lai chuyển tiền)
    isOrderInfoReadOnly() {
      return !this.selectedOrder || Number(this.selectedOrder.status) >= 1;
    },
    // Khu vực biên lai chuyển tiền chỉ khóa khi đơn đã hoàn thành (status 3) hoặc đã hủy (status 4)
    isOrderReceiptReadOnly() {
      return !this.selectedOrder || Number(this.selectedOrder.status) >= 3;
    },

    // PIPELINE LISTS
    pendingQuotes() {
      return this.quotes
        .filter(q => q.status === 0)
        .map(q => ({ ...q, _type: 'quote' }))
        .sort((a, b) => b.id - a.id);
    },
    quotedQuotes() {
      const list = [];
      const quoteCodes = new Set();
      this.quotes
        .filter(q => q.status === 1)
        .forEach(q => {
          list.push({ ...q, _type: 'quote' });
          quoteCodes.add(q.code);
        });
      this.orders
        .filter(o => o.status === 0)
        .forEach(o => {
          const matchQuote = o.customer_notes && Array.from(quoteCodes).some(code => o.customer_notes.includes(code));
          if (!matchQuote) {
            list.push({ ...o, _type: 'order' });
          }
        });
      return list.sort((a, b) => b.id - a.id);
    },
    processingOrders() {
      const list = [];
      const quoteCodes = new Set();
      this.orders.filter(o => o.status === 1).forEach(o => {
        list.push({ ...o, _type: 'order' });
        if (o.customer_notes) {
          const m = o.customer_notes.match(/YCBG-[^\s|]+/);
          if (m) quoteCodes.add(m[0]);
        }
      });
      this.quotes.filter(q => q.status === 2).forEach(q => {
        if (!quoteCodes.has(q.code)) {
          list.push({ ...q, _type: 'quote' });
        }
      });
      return list.sort((a, b) => b.id - a.id);
    },
    shippingOrders() {
      const list = [];
      this.orders.filter(o => o.status === 2).forEach(o => {
        list.push({ ...o, _type: 'order' });
      });
      return list.sort((a, b) => b.id - a.id);
    },
    completedOrders() {
      const list = [];
      const quoteCodes = new Set();
      this.quotes.filter(q => q.status === 4).forEach(q => {
        list.push({ ...q, _type: 'quote' });
        quoteCodes.add(q.code);
      });
      this.orders.filter(o => o.status === 3).forEach(o => {
        const matchQuote = o.customer_notes && Array.from(quoteCodes).some(code => o.customer_notes.includes(code));
        if (!matchQuote) {
          list.push({ ...o, _type: 'order' });
        }
      });
      return list.sort((a, b) => b.id - a.id);
    },
    cancelledItems() {
      const list = [];
      this.quotes.filter(q => q.status === 3).forEach(q => list.push({ ...q, _type: 'quote' }));
      this.orders.filter(o => o.status === 4).forEach(o => list.push({ ...o, _type: 'order' }));
      return list.sort((a, b) => b.id - a.id);
    },
    allTransactionsList() {
      const list = [];
      this.quotes.forEach(q => list.push({ ...q, _type: 'quote' }));
      this.orders.forEach(o => list.push({ ...o, _type: 'order' }));
      return list.sort((a, b) => b.id - a.id);
    },

    // COUNTS
    countPendingQuotes() {
      return this.pendingQuotes.length;
    },
    countQuotedQuotes() {
      return this.quotedQuotes.length;
    },
    countProcessing() {
      return this.processingOrders.length;
    },
    countShipping() {
      return this.shippingOrders.length;
    },
    countCompleted() {
      return this.completedOrders.length;
    },
    countCancelled() {
      return this.cancelledItems.length;
    },
    countAll() {
      return this.allTransactionsList.length;
    },

    // ACTIVE TAB DATA
    currentTabList() {
      switch (this.currentTab) {
        case 'pending_quotes':
          return this.pendingQuotes;
        case 'quoted_quotes':
          return this.quotedQuotes;
        case 'processing':
          return this.processingOrders;
        case 'shipping':
          return this.shippingOrders;
        case 'completed':
          return this.completedOrders;
        case 'cancelled':
          return this.cancelledItems;
        case 'all':
          return this.allTransactionsList;
        default:
          return [];
      }
    },
    filteredTabList() {
      let list = this.currentTabList;
      if (this.typeFilter && this.typeFilter !== 'all') {
        list = list.filter(item => item._type === this.typeFilter);
      }
      if (this.searchQuery) {
        const s = this.searchQuery.toLowerCase().trim();
        list = list.filter(item => 
          (item.code && String(item.code).toLowerCase().includes(s)) ||
          (item.customer_name && String(item.customer_name).toLowerCase().includes(s)) ||
          (item.customer_phone && String(item.customer_phone).includes(s))
        );
      }
      return list;
    },

    // DYNAMIC TAB METADATA
    tabTitle() {
      switch (this.currentTab) {
        case 'pending_quotes': return 'Danh Sách Yêu Cầu Chờ Báo Giá';
        case 'quoted_quotes': return 'Danh Sách Đang Chờ Khách Chốt Giá & Cọc';
        case 'processing': return 'Đơn Hàng Đang Xử Lý & Gom Hàng';
        case 'shipping': return 'Đơn Hàng Đang Vận Chuyển';
        case 'completed': return 'Danh Sách Đơn Hàng Hoàn Thành';
        case 'cancelled': return 'Danh Sách Giao Dịch Đã Hủy';
        case 'all': return 'Toàn Bộ Giao Dịch & Báo Giá';
        default: return 'Danh Sách Giao Dịch';
      }
    },
    tabIcon() {
      switch (this.currentTab) {
        case 'pending_quotes': return 'bx bx-time-five';
        case 'quoted_quotes': return 'bx bx-send';
        case 'processing': return 'bx bx-package';
        case 'shipping': return 'bx bxs-truck';
        case 'completed': return 'bx bx-check-circle';
        case 'cancelled': return 'bx bx-x-circle';
        case 'all': return 'bx bx-list-ul';
        default: return 'bx bx-receipt';
      }
    },
    tabDescription() {
      switch (this.currentTab) {
        case 'pending_quotes': return 'Khách hàng vừa gửi yêu cầu, cần nhân sự kinh doanh kiểm tra linh kiện và gửi giá';
        case 'quoted_quotes': return 'Báo giá đã hoàn tất gửi cho khách, đang chờ khách phản hồi hoặc đặt cọc giữ hàng';
        case 'processing': return 'Đơn hàng đã nhận cọc, đang gom phụ tùng, đóng kiện xuất kho hoặc lên đơn Nhật';
        case 'shipping': return 'Đơn hàng đã bàn giao cho bên vận chuyển (ViettelPost / Chành xe), đang trên đường giao tới khách';
        case 'completed': return 'Đơn hàng khách đã nhận thành công và đối soát thanh toán đủ 100%';
        case 'cancelled': return 'Các yêu cầu báo giá hoặc đơn hàng đã bị khách hủy hoặc nhân viên hủy bỏ';
        case 'all': return 'Tra cứu tổng thể lịch sử giao dịch và yêu cầu báo giá trên toàn hệ thống';
        default: return '';
      }
    },

    quotes() {
      return (this.rawQuotes || []).map(item => {
        const d = new Date(item.created_at);
        return {
          id: item.id,
          code: item.ma_yeu_cau,
          status: item.trang_thai,
          total_amount: item.tong_tien,
          shipping_fee: Number(item.phi_van_chuyen) || 0,
          freight_collect: item.khach_tu_tra_cuoc == 1,
          deposit_required: Number(item.tien_coc_yeu_cau) || 0,
          da_thu: Number(item.da_thu) || 0,
          date: d.toLocaleDateString('vi-VN'),
          time: d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
          customer_name: item.khach_hang_ten || 'Khách Vãng Lai',
          customer_phone: item.khach_hang_sdt || '',
          customer_email: item.khach_hang_email || '',
          customer_notes: item.ghi_chu_khach_hang || '',
          zalo_notes: item.ghi_chu_zalo || '',
          token: item.token,
          ma_van_don: item.ma_van_don || '',
          ten_don_vi_van_chuyen: item.ten_don_vi_van_chuyen || '',
          address: item.dia_chi_giao_hang || item.dia_chi_text || (item.dia_chi ? item.dia_chi.dia_chi_cu_the : '') || '',
          receipt_image: item.anh_chuyen_khoan || '',
          receipt_image_2: item.anh_chuyen_khoan_2 || '',
          items: (item.chi_tiet || []).map(i => ({
            id: i.id,
            part_name: i.ten_phu_tung || '',
            part_code: i.ma_phu_tung || '',
            qty: i.so_luong || 0,
            price: Number(i.don_gia || 0)
          }))
        };
      });
    },
    orders() {
      return (this.rawOrders || []).map(item => {
        const d = new Date(item.created_at);
        return {
          id: item.id,
          code: item.ma_don_hang,
          status: item.trang_thai,
          payment_status: item.trang_thai_thanh_toan || 0,
          order_type: item.loai_don || 'b2c',
          total_amount: item.tong_tien,
          shipping_fee: Number(item.phi_van_chuyen) || 0,
          freight_collect: item.khach_tu_tra_cuoc == 1,
          deposit_required: Number(item.tien_coc_yeu_cau) || 0,
          paid_amount: Number(item.da_thu) || 0,
          ma_van_don: item.ma_van_don || '',
          ten_don_vi_van_chuyen: item.ten_don_vi_van_chuyen || '',
          date: d.toLocaleDateString('vi-VN'),
          time: d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
          customer_name: item.khach_hang_ten || item.khach_hang?.ho_ten || 'Khách Lẻ',
          customer_phone: item.khach_hang_sdt || item.khach_hang?.so_dien_thoai || '',
          customer_email: item.khach_hang_email || item.khach_hang?.user?.email || '',
          customer_notes: item.ghi_chu_khach_hang || '',
          address: (item.dia_chi ? item.dia_chi.dia_chi_cu_the : item.dia_chi_text) || '',
          receipt_image: item.anh_chuyen_khoan || '',
          receipt_image_2: item.anh_chuyen_khoan_2 || '',
          items: (item.chi_tiet || []).map(i => ({
            id: i.id,
            part_name: i.ten_phu_tung || (i.phu_tung ? i.phu_tung.ten_phu_tung : (i.san_pham ? i.san_pham.ten_san_pham : 'Sản phẩm ID: ' + i.id_san_pham)),
            part_code: i.ma_phu_tung || (i.phu_tung ? i.phu_tung.ma_phu_tung : (i.san_pham ? i.san_pham.ma_san_pham : (i.loai_san_pham === 'phu_tung' ? 'PT-' + i.id_san_pham : 'SP-' + i.id_san_pham))),
            qty: i.so_luong || 0,
            price: Number(i.don_gia || 0),
            in_stock: Number(i.phu_tung ? i.phu_tung.so_luong_ton : (i.san_pham ? i.san_pham.so_luong_ton : 0)) >= Number(i.so_luong || 0)
          }))
        };
      });
    },
    
    
  },
  methods: {
    switchTab(tabKey) {
      this.currentTab = tabKey;
    },
    refreshData() {
      this.queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] });
      this.queryClient.invalidateQueries({ queryKey: ['don-hang'] });
    },
    async copyQuoteLink(item) {
      if (!item) return;
      const isQuote = item._type === 'quote' || (item.code && String(item.code).startsWith('YCBG'));
      if (isQuote) {
        if (item.status === 0 && this.selectedQuote && this.selectedQuote.id === item.id) {
          const saved = await this.saveQuoteSilent();
          if (!saved) return;
          item = this.selectedQuote;
        }
        if (!item.token) {
          this.toaster.warning('Báo giá này chưa có liên kết tra cứu.');
          return;
        }
        const link = `${window.location.origin}/bao-gia/${item.token}`;
        navigator.clipboard.writeText(link).then(() => {
          this.toaster.success('Đã sao chép link báo giá gửi khách!');
        });
      } else {
        const phone = item.customer_phone || '';
        const link = `${window.location.origin}/tra-cuu-don-hang?code=${item.code}${phone ? '&phone=' + encodeURIComponent(phone) : ''}`;
        navigator.clipboard.writeText(link).then(() => {
          this.toaster.success('Đã sao chép link tra cứu đơn hàng gửi khách!');
        });
      }
    },
    quickOpenZalo(item) {
      const phone = (item.customer_phone || '').replace(/[^0-9]/g, '');
      if (!phone) {
        this.toaster.warning('Khách hàng chưa có số điện thoại để gửi Zalo.');
        return;
      }
      const link = item.token ? `${window.location.origin}/bao-gia/${item.token}` : '';
      const total = (item.items && item.items.length > 0) ? this.calculateTotal(item.items) : (item.total_amount || 0);
      const deposit = item.deposit_required || 0;
      const shipping = item.freight_collect ? 0 : (item.shipping_fee || 0);
      
      let msg = `Chào ${item.customer_name || 'Quý khách'}, JPM Marine đã gửi bảng báo giá cho yêu cầu [${item.code}]:\n`;
      msg += `• Tiền phụ tùng: ${this.formatCurrency(total)}\n`;
      if (shipping > 0) {
        msg += `• Cước vận chuyển: ${this.formatCurrency(shipping)}\n`;
        msg += `• Tổng cộng đơn hàng: ${this.formatCurrency(total + shipping)}\n`;
      } else if (item.freight_collect) {
        msg += `• Cước vận chuyển: Khách thanh toán cước trực tiếp cho bên giao hàng\n`;
        msg += `• Tổng tiền phụ tùng: ${this.formatCurrency(total)}\n`;
      }
      if (deposit > 0) {
        msg += `• Tiền cọc giữ hàng (đặt Nhật): ${this.formatCurrency(deposit)}\n`;
        msg += `• Số tiền còn lại thu COD khi nhận hàng: ${this.formatCurrency(Math.max(0, (total + shipping) - deposit))}\n`;
      }
      if (link) {
        msg += `• Link xem chi tiết và quét QR cọc: ${link}\n`;
      }
      msg += `(Báo giá có hiệu lực trong 72 giờ theo tỷ giá JPY hiện hành). Trân trọng cảm ơn Quý khách!`;

      window.open(`https://zalo.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
    },
    handleAction(item) {
      if (item._type === 'quote') {
        this.openQuoteModal(item);
      } else {
        this.openOrderModal(item);
      }
    },
    async handleCancel(item) {
      if (!item) return;
      const isQuote = item.record_type === 'quote' || item._type === 'quote';
      const label = isQuote ? 'yêu cầu báo giá' : 'đơn hàng';
      
      const result = await Swal.fire({
        title: `Hủy ${isQuote ? 'Yêu Cầu Báo Giá' : 'Đơn Hàng'}?`,
        html: `Bạn có chắc chắn muốn hủy ${label} mã <b>${item.code}</b>?<br><small class="text-muted">Giao dịch sẽ được chuyển về tab "Đã Hủy" để lưu trữ lịch sử, không bị xóa mất dữ liệu.</small>`,
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Có, Hủy Đơn!',
        cancelButtonText: 'Quay Lại',
        reverseButtons: true,
        animation: false,
        customClass: {
          popup: 'swal-shift-up',
          confirmButton: 'btn btn-danger px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          if (isQuote) {
            const res = await axiosClient.put(`/admin/yeu-cau-bao-gia/${item.id}`, { trang_thai: 3 });
            if (res?.success || res?.data?.success) {
              this.toaster.success('Đã hủy báo giá và chuyển vào mục Đã Hủy!');
              this.closeModal('quoteModal');
              this.refreshData();
            }
          } else {
            const res = await axiosClient.put(`/admin/don-hang/${item.id}`, { status: 4 });
            if (res?.success || res?.data?.success) {
              this.toaster.success('Đã hủy đơn hàng và hoàn kho thành công!');
              this.closeModal('orderModal');
              this.refreshData();
            }
          }
        } catch (err) {
          const msg = err?.response?.data?.message || 'Không thể hủy giao dịch này.';
          this.toaster.error(msg);
        }
      }
    },
    handleDelete(item) {
      return this.handleCancel(item);
    },
    printOrder() {
      window.print();
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      if (isNaN(d.getTime())) return dateStr;
      return d.toLocaleString('vi-VN');
    },
    async openZaloMessage() {
      if (!this.selectedQuote) return;
      if (this.selectedQuote.status === 0) {
        const saved = await this.saveQuoteSilent();
        if (!saved) return;
      }
      this.quickOpenZalo(this.selectedQuote);
    },
    async exportJapanPO() {
      try {
        const res = await axiosClient.get('/admin/don-hang/xuat-excel-po-nhat', { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([res?.data !== undefined ? res.data : res], { type: 'text/csv;charset=utf-8;' }));
        const link = document.createElement('a');
        link.href = url;
        const dateStr = new Date().toISOString().slice(0, 10);
        link.setAttribute('download', `PO_Nhat_JPM_${dateStr}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      } catch (err) {
        this.toaster.error('Không thể xuất file đơn đặt hàng Nhật.');
      }
    },
    copyMagicLink() {
      if (!this.selectedQuote || !this.selectedQuote.token) {
        this.toaster.warning('Báo giá này chưa có Link tra cứu!');
        return;
      }
      const link = `${window.location.origin}/tra-cuu-bao-gia/${this.selectedQuote.token}`;
      const msg = `Chào JPM Marine đã gửi báo giá cho đơn hàng ${this.selectedQuote.code}. Anh/chị xem và chốt đơn tại đây nhé:\n${link}`;
      navigator.clipboard.writeText(msg).then(() => {
        this.toaster.success('Đã copy đường dẫn Zalo vào khay nhớ tạm.');
      });
    },
    async adminChotDon() {
      const result = await Swal.fire({
        title: 'Xác nhận tạo đơn?',
        text: 'Bạn sẽ chuyển yêu cầu báo giá này thành Đơn hàng (Khách đã xác nhận qua miệng/Zalo)?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tạo đơn ngay',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
            popup: 'swal-shift-up',
            confirmButton: 'btn btn-danger px-4 shadow-sm',
            cancelButton: 'btn btn-outline-secondary px-4 me-3'
          },
        buttonsStyling: false
      });
      if (result.isConfirmed) {
        try {
          const res = await axiosClient.post(`/admin/yeu-cau-bao-gia/${this.selectedQuote.id}/chuyen-don`);
          if (res?.success || res?.data?.success) {
            this.toaster.success(res?.message || res?.data?.message || 'Tạo đơn hàng thành công!');
            // Close modal implicitly via reload/invalidation
            this.queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] });
            this.queryClient.invalidateQueries({ queryKey: ['don-hang'] });
            this.$refs.quoteModalCloseBtn?.click();
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Không thể tạo đơn.';
          this.toaster.error(msg);
        }
      }
    },
    async cancelQuoteFromModal(quote) {
      if (!quote) return;
      const result = await Swal.fire({
        title: 'Hủy Yêu Cầu Báo Giá?',
        html: `Bạn có chắc chắn muốn hủy yêu cầu <b>${quote.code}</b>?<br><small class="text-muted">Đơn yêu cầu này sẽ chuyển sang trạng thái "Đã Hủy".</small>`,
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý Hủy',
        cancelButtonText: 'Quay lại',
        reverseButtons: true,
        animation: false,
        customClass: {
          popup: 'swal-shift-up',
          confirmButton: 'btn btn-danger px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const res = await axiosClient.put(`/admin/yeu-cau-bao-gia/${quote.id}`, { trang_thai: 3 });
          if (res?.success || res?.data?.success) {
            this.toaster.success('Đã hủy yêu cầu báo giá thành công!');
            this.closeModal('quoteModal');
            this.refreshData();
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Không thể hủy báo giá.';
          this.toaster.error(msg);
        }
      }
    },
    async deleteQuote(item) {
      const result = await Swal.fire({
        title: 'Xóa Yêu Cầu Báo Giá?',
        html: `Bạn có chắc chắn muốn xóa mã <b>${item.code}</b>?`,
        icon: 'warning',
          iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Có, Xóa!',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
            popup: 'swal-shift-up',
            confirmButton: 'btn btn-danger px-4 shadow-sm',
            cancelButton: 'btn btn-outline-secondary px-4 me-3'
          },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        this.deleteQuoteMutation.mutate(item.id, {
          onSuccess: () => this.toaster.success('Yêu cầu báo giá đã được xóa thành công!'),
          onError: (err) => {
            const msg = err?.response?.data?.message || 'Không thể xóa báo giá này.';
            this.toaster.error(msg);
          }
        });
      }
    },
    async deleteOrder(item) {
      const result = await Swal.fire({
        title: 'Xóa Đơn Hàng?',
        html: `Bạn có chắc chắn muốn xóa đơn mã <b>${item.code}</b>?`,
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Có, Xóa!',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
            popup: 'swal-shift-up',
            confirmButton: 'btn btn-danger px-4 shadow-sm',
            cancelButton: 'btn btn-outline-secondary px-4 me-3'
          },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        this.deleteOrderMutation.mutate(item.id, {
          onSuccess: () => this.toaster.success('Đơn hàng đã được xóa thành công!'),
          onError: (err) => {
            const msg = err?.response?.data?.message || 'Không thể xóa đơn hàng này.';
            this.toaster.error(msg);
          }
        });
      }
    },
    formatCurrency(value) {
      if (!value) return "0 đ";
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(value);
    },
    formatVND(value) {
      return this.formatCurrency(value);
    },
    openQuoteModal(item) {
      this.selectedQuote = JSON.parse(JSON.stringify(item));
    },
    openOrderModal(item) {
      this.selectedOrder = JSON.parse(JSON.stringify(item));
    },
    calculateTotal(items) {
      if (!items || items.length === 0) return 0;
      return items.reduce((total, item) => total + (item.qty * (item.price || 0)), 0);
    },
    async saveDraftQuote() {
      if (!this.selectedQuote) return;
      if (this.selectedQuote.items && this.selectedQuote.items.length > 0) {
        this.selectedQuote.total_amount = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      } else {
        this.selectedQuote.total_amount = (this.selectedQuote.total_amount || 0) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      }

      // Giữ nguyên trạng thái hiện tại của báo giá khi lưu nháp/lưu thay đổi
      let newStatus = this.selectedQuote.status;

      try {
        const payload = {
            tong_tien: this.selectedQuote.total_amount,
            trang_thai: newStatus,
            ghi_chu_zalo: this.selectedQuote.zalo_notes,
            dia_chi_giao_hang: this.selectedQuote.address,
            address: this.selectedQuote.address,
            anh_chuyen_khoan: this.selectedQuote.receipt_image,
            receipt_image: this.selectedQuote.receipt_image,
            anh_chuyen_khoan_2: this.selectedQuote.receipt_image_2,
            receipt_image_2: this.selectedQuote.receipt_image_2,
            shipping_fee: this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0),
            freight_collect: this.selectedQuote.freight_collect ? 1 : 0,
            deposit_required: this.selectedQuote.deposit_required || 0,
            chi_tiet: this.selectedQuote.items
        };
        await this.saveQuoteMutation.mutateAsync({ id: this.selectedQuote.id, payload });
        this.closeModal('quoteModal');
        this.toaster.success('Đã lưu thông tin báo giá thành công!');
      } catch (error) {
        console.error("Lỗi lưu báo giá:", error);
        const msg = error?.response?.data?.message || 'Lỗi lưu thông tin báo giá!';
        this.toaster.error(msg);
      }
    },
    async sendQuoteToCustomer() {
      if (!this.selectedQuote) return;

      const itemsTotal = this.calculateTotal(this.selectedQuote.items);
      if (itemsTotal <= 0) {
        this.toaster.warning('Vui lòng nhập đơn giá cho các phụ tùng trước khi báo giá cho khách hàng!');
        return;
      }

      this.selectedQuote.total_amount = itemsTotal + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      this.selectedQuote.status = 1; // Chuyển sang Đã Báo Giá

      try {
        const payload = {
            tong_tien: this.selectedQuote.total_amount,
            trang_thai: 1, // Đã Báo Giá
            ghi_chu_zalo: this.selectedQuote.zalo_notes,
            dia_chi_giao_hang: this.selectedQuote.address,
            address: this.selectedQuote.address,
            anh_chuyen_khoan: this.selectedQuote.receipt_image,
            receipt_image: this.selectedQuote.receipt_image,
            anh_chuyen_khoan_2: this.selectedQuote.receipt_image_2,
            receipt_image_2: this.selectedQuote.receipt_image_2,
            shipping_fee: this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0),
            freight_collect: this.selectedQuote.freight_collect ? 1 : 0,
            deposit_required: this.selectedQuote.deposit_required || 0,
            chi_tiet: this.selectedQuote.items
        };
        await this.saveQuoteMutation.mutateAsync({ id: this.selectedQuote.id, payload });
        this.closeModal('quoteModal');

        // Chuyển tab sang "Đã Báo Giá"
        this.currentTab = 'quoted_quotes';

        // Mở Zalo gửi khách
        this.quickOpenZalo(this.selectedQuote);

        this.toaster.success('Đã chuyển sang tab "Đã Báo Giá" và mở Zalo gửi khách!');
      } catch (error) {
        console.error("Lỗi gửi báo giá:", error);
        const msg = error?.response?.data?.message || 'Lỗi khi báo giá cho khách!';
        this.toaster.error(msg);
      }
    },
    async saveQuote() {
      return this.saveDraftQuote();
    },
    async saveQuoteSilent() {
      if (!this.selectedQuote) return false;
      if (this.selectedQuote.items && this.selectedQuote.items.length > 0) {
        this.selectedQuote.total_amount = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      } else {
        this.selectedQuote.total_amount = (this.selectedQuote.total_amount || 0) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      }

      try {
        const payload = {
            tong_tien: this.selectedQuote.total_amount,
            trang_thai: this.selectedQuote.status,
            ghi_chu_zalo: this.selectedQuote.zalo_notes,
            dia_chi_giao_hang: this.selectedQuote.address,
            address: this.selectedQuote.address,
            anh_chuyen_khoan: this.selectedQuote.receipt_image,
            receipt_image: this.selectedQuote.receipt_image,
            anh_chuyen_khoan_2: this.selectedQuote.receipt_image_2,
            receipt_image_2: this.selectedQuote.receipt_image_2,
            shipping_fee: this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0),
            freight_collect: this.selectedQuote.freight_collect ? 1 : 0,
            deposit_required: this.selectedQuote.deposit_required || 0,
            chi_tiet: this.selectedQuote.items
        };
        await this.saveQuoteMutation.mutateAsync({ id: this.selectedQuote.id, payload });
        return true;
      } catch (error) {
        console.error("Lỗi tự động lưu báo giá:", error);
        return false;
      }
    },
    async saveOrder() {
      if (this.selectedOrder.items && this.selectedOrder.items.length > 0) {
        this.selectedOrder.total_amount = this.calculateTotal(this.selectedOrder.items) + (this.selectedOrder.freight_collect ? 0 : (this.selectedOrder.shipping_fee || 0));
      } else {
        this.selectedOrder.total_amount = (this.selectedOrder.total_amount || 0) + (this.selectedOrder.freight_collect ? 0 : (this.selectedOrder.shipping_fee || 0));
      }

      try {
        if (!navigator.onLine) {
          throw new Error("Offline Mode");
        }
        
        const payload = {
            status: this.selectedOrder.status,
            payment_status: this.selectedOrder.payment_status,
            total_amount: this.selectedOrder.total_amount,
            shipping_fee: this.selectedOrder.freight_collect ? 0 : (this.selectedOrder.shipping_fee || 0),
            order_type: typeof this.selectedOrder.order_type === 'object' ? this.selectedOrder.order_type.value : this.selectedOrder.order_type,
            deposit_required: this.selectedOrder.deposit_required || 0,
            paid_amount: this.selectedOrder.paid_amount || 0,
            freight_collect: this.selectedOrder.freight_collect ? 1 : 0,
            address: this.selectedOrder.address,
            dia_chi_text: this.selectedOrder.address,
            anh_chuyen_khoan: this.selectedOrder.receipt_image,
            receipt_image: this.selectedOrder.receipt_image,
            anh_chuyen_khoan_2: this.selectedOrder.receipt_image_2,
            receipt_image_2: this.selectedOrder.receipt_image_2
        };
        await this.saveOrderMutation.mutateAsync({ id: this.selectedOrder.id, payload });
        this.closeModal('orderModal');
        this.toaster.success('Cập nhật đơn hàng thành công!');
        localStorage.removeItem('draft_order_' + this.selectedOrder.id);
      } catch (error) {
        console.warn("Lỗi mạng hoặc API, lưu nháp Offline...", error);
        localStorage.setItem('draft_order_' + this.selectedOrder.id, JSON.stringify(this.selectedOrder));
        const msg = error?.response?.data?.message || "Mất mạng hoặc lỗi hệ thống! Đơn hàng được lưu nháp cục bộ.";
        this.toaster.warning(msg);
      }
    },

    closeModal(modalId) {
      const modalEl = document.getElementById(modalId);
      if (modalEl) {
        // eslint-disable-next-line no-undef
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      }
      const closeBtn = document.querySelector(`#${modalId} .btn-close`);
      if (closeBtn) closeBtn.click();
    },

    copyText(text, msg) {
      if (!text) return;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text);
      }
      this.toaster.success(msg || 'Đã sao chép!');
    },

    async confirmDepositBtn(item) {
      const depositAmt = item.deposit_required || item.tien_coc_yeu_cau || 0;
      const result = await Swal.fire({
        title: depositAmt > 0 ? 'Đã Cọc & Bắt Đầu Lên Đơn?' : 'Bắt Đầu Lên Đơn Hàng?',
        html: depositAmt > 0 
          ? `Xác nhận khách đã cọc đủ <b>${this.formatCurrency(depositAmt)}</b> cho mã <b>${item.code}</b>?<br><small class="text-muted">Đơn hàng sẽ chuyển sang tab "Đang Xử Lý" để tiến hành gom hàng & chuẩn bị xuất kho.</small>`
          : `Xác nhận bắt đầu lên đơn <b>${item.code}</b> sang tab "Đang Xử Lý"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: depositAmt > 0 ? 'Đã nhận cọc, bắt đầu lên đơn' : 'Xác nhận lên đơn',
        cancelButtonText: 'Hủy',
        customClass: {
          popup: 'swal-shift-up',
          confirmButton: 'btn btn-primary px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const endpoint = item._type === 'quote'
            ? `/admin/yeu-cau-bao-gia/${item.id}/xac-nhan-coc`
            : `/admin/don-hang/${item.id}/xac-nhan-coc`;
          const res = await axiosClient.post(endpoint, { 
            amount: depositAmt,
            dia_chi_giao_hang: item.address,
            address: item.address,
            dia_chi_text: item.address,
            anh_chuyen_khoan: item.receipt_image,
            receipt_image: item.receipt_image,
            anh_chuyen_khoan_2: item.receipt_image_2,
            receipt_image_2: item.receipt_image_2
          });
          if (res?.success || res?.data?.success) {
            this.toaster.success(res?.message || res?.data?.message || 'Đã xác nhận nhận cọc thành công! Đơn hàng đã chuyển sang tab Đang Xử Lý.');
            this.closeModal('quoteModal');
            this.closeModal('orderModal');
            this.refreshData();
          }
        } catch (err) {
          this.toaster.error(err?.response?.data?.message || 'Không thể xác nhận cọc.');
        }
      }
    },

    async confirmDepositFromModal(quote) {
      if (!quote) return;
      await this.saveQuoteSilent();
      await this.confirmDepositBtn(quote);
    },

    async startShippingBtn(item) {
      if (!item) return;
      let targetOrder = item;
      if (item._type === 'quote') {
        const found = this.orders.find(o => o.customer_notes && o.customer_notes.includes(item.code));
        if (found) {
          targetOrder = found;
          if (item.ten_don_vi_van_chuyen) targetOrder.ten_don_vi_van_chuyen = item.ten_don_vi_van_chuyen;
          if (item.ma_van_don) targetOrder.ma_van_don = item.ma_van_don;
        }
      }

      const carrier = (targetOrder.ten_don_vi_van_chuyen || '').trim();
      const tracking = (targetOrder.ma_van_don || '').trim();

      // Nếu đã có thông tin vận chuyển do người dùng nhập trực tiếp trong modal
      if (carrier || tracking) {
        const code = targetOrder.code || targetOrder.ma_don_hang || targetOrder.ma_yeu_cau;
        const result = await Swal.fire({
          title: 'Bắt Đầu Giao Hàng?',
          html: `Xác nhận chuyển đơn <b>${code}</b> sang trạng thái <b>Đang Giao Hàng</b>?<br><br>
                 <div class="text-start bg-light p-2 rounded border small">
                   <div class="mb-1"><b>Đơn vị vận chuyển:</b> ${carrier || '<span class="text-muted">Chành xe / Giao trực tiếp</span>'}</div>
                   <div><b>Mã vận đơn:</b> <span class="text-danger fw-bold">${tracking || '<span class="text-muted">Chưa có mã</span>'}</span></div>
                 </div><br><small class="text-muted">Đơn hàng sẽ chuyển sang tab "Đang Giao Hàng".</small>`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Xác nhận giao hàng',
          cancelButtonText: 'Kiểm tra lại',
          customClass: {
            popup: 'swal-shift-up',
            confirmButton: 'btn btn-primary px-4 shadow-sm',
            cancelButton: 'btn btn-outline-secondary px-4 me-3'
          },
          buttonsStyling: false
        });

        if (result.isConfirmed) {
          try {
            let orderId = targetOrder.id;
            const payload = {
              ma_van_don: tracking || 'Giao chành xe / Giao trực tiếp',
              ten_don_vi_van_chuyen: carrier || 'Chành xe',
              status: 2
            };
            const res = await axiosClient.post(`/admin/don-hang/${orderId}/cap-nhat-van-chuyen`, payload);
            if (res?.success || res?.data?.success) {
              this.toaster.success('Đã cập nhật vận chuyển và chuyển đơn sang tab Đang Giao Hàng!');
              this.closeModal('orderModal');
              this.closeModal('quoteModal');
              this.refreshData();
            }
          } catch (err) {
            this.toaster.error(err?.response?.data?.message || 'Không thể cập nhật vận chuyển.');
          }
          return;
        } else {
          return;
        }
      }

      this.openViettelPostModal(targetOrder);
    },

    openViettelPostModal(order) {
      if (!order) return;
      this.viettelPostOrder = order;
      this.viettelPostForm = {
        ma_van_don: order.ma_van_don || '',
        ten_don_vi_van_chuyen: order.ten_don_vi_van_chuyen || 'Chành Xe / ViettelPost'
      };
      this.closeModal('orderModal');
      this.closeModal('quoteModal');
      this.$nextTick(() => {
        // eslint-disable-next-line no-undef
        const el = document.getElementById('viettelPostModal');
        if (el) {
          const modalEl = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
          modalEl.show();
        }
      });
    },

    async saveViettelPostShipping() {
      if (!this.viettelPostOrder) return;
      this.isSavingViettelPost = true;
      try {
        let orderId = this.viettelPostOrder.id;
        if (this.viettelPostOrder._type === 'quote') {
          const linked = this.orders.find(o => o.customer_notes && o.customer_notes.includes(this.viettelPostOrder.code));
          if (linked) {
            orderId = linked.id;
          }
        }
        const payload = {
          ma_van_don: this.viettelPostForm.ma_van_don ? this.viettelPostForm.ma_van_don.trim() : 'Giao chành xe / Giao trực tiếp',
          ten_don_vi_van_chuyen: this.viettelPostForm.ten_don_vi_van_chuyen ? this.viettelPostForm.ten_don_vi_van_chuyen.trim() : 'Chành xe',
          status: 2
        };
        const res = await axiosClient.post(`/admin/don-hang/${orderId}/cap-nhat-van-chuyen`, payload);
        if (res?.success || res?.data?.success) {
          this.toaster.success('Đã cập nhật vận chuyển và chuyển đơn hàng sang trạng thái Đang Giao Hàng!');
          this.closeModal('viettelPostModal');
          this.refreshData();
        }
      } catch (err) {
        this.toaster.error(err?.response?.data?.message || 'Không thể cập nhật vận chuyển.');
      } finally {
        this.isSavingViettelPost = false;
      }
    },

    async completeOrderBtn(order) {
      if (!order) return;
      const isQuote = order._type === 'quote' || (order.code && String(order.code).startsWith('YCBG'));
      const paidAmt = Number(order.paid_amount ?? order.da_thu ?? 0);
      const totalAmt = Number(order.total_amount || 0) + (order.freight_collect ? 0 : Number(order.shipping_fee || 0));
      const codAmt = Math.max(0, totalAmt - paidAmt);
      const code = order.code || order.ma_yeu_cau || order.ma_don_hang;
      const carrier = order.ten_don_vi_van_chuyen || 'Đơn vị vận chuyển';

      const result = await Swal.fire({
        title: 'Khách Đã Nhận Hàng & Chuyển Tiền?',
        html: `${carrier} đã giao hàng thành công và khách đã thanh toán đủ số tiền còn lại <b>${this.formatCurrency(codAmt)}</b> cho đơn <b>${code}</b>?<br><small class="text-muted">Đơn hàng sẽ chuyển sang trạng thái "Hoàn Thành" và cập nhật doanh thu 100%.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đã nhận đủ tiền, Hoàn tất đơn',
        cancelButtonText: 'Hủy',
        customClass: {
          popup: 'swal-shift-up',
          confirmButton: 'btn btn-success px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const endpoint = isQuote 
            ? `/admin/yeu-cau-bao-gia/${order.id}/hoan-thanh` 
            : `/admin/don-hang/${order.id}/hoan-thanh`;
          const res = await axiosClient.post(endpoint);
          if (res?.success || res?.data?.success) {
            this.toaster.success(res?.message || res?.data?.message || 'Đơn hàng đã được đánh dấu Hoàn Thành!');
            this.closeModal('orderModal');
            this.closeModal('quoteModal');
            this.refreshData();
          }
        } catch (err) {
          this.toaster.error(err?.response?.data?.message || 'Không thể hoàn thành đơn hàng.');
        }
      }
    },

    async openVietQrModal(item) {
      if (!item) return;
      this.isLoadingQr = true;
      this.adminVietQr = null;
      // eslint-disable-next-line no-undef
      const modalEl = new bootstrap.Modal(document.getElementById('adminVietQrModal'));
      modalEl.show();
      try {
        const isQuote = item._type === 'quote' || (item.code && item.code.startsWith('YCBG'));
        const endpoint = isQuote ? `/public/vietqr/bao-gia/${item.code}` : `/public/vietqr/don-hang/${item.code}`;
        const res = await axiosClient.get(endpoint);
        const payload = res?.qr_url ? res : res?.data;
        if (payload && (res.success || payload.success || payload.qr_url)) {
          this.adminVietQr = payload;
          this.currentVietQr = payload;
        }
      } catch (e) {
        this.toaster.error('Không thể tạo mã VietQR cho yêu cầu này.');
      } finally {
        this.isLoadingQr = false;
      }
    },

    openShippingModal(order) {
      if (!order) return;
      this.shippingOrder = order;
      this.shippingForm = {
        ten_nha_xe: 'Chành Xe Tô Châu',
        bien_so_xe: '',
        ten_tai_xe: '',
        sdt_tai_xe: '',
        so_kien_hang: 1,
        dia_diem_tra_hang: order.address || 'Bến tàu Rạch Giá, Kiên Giang',
        notify_customer: true,
        notify_driver: true
      };
      // eslint-disable-next-line no-undef
      const modalEl = new bootstrap.Modal(document.getElementById('adminShippingModal'));
      modalEl.show();
    },

    async sendShippingNotice() {
      if (!this.shippingOrder) return;
      if (!this.shippingForm.ten_nha_xe) {
        this.toaster.warning('Vui lòng nhập tên nhà xe / chành xe');
        return;
      }
      this.isSendingShipping = true;
      try {
        const res = await axiosClient.post(`/admin/thong-bao/gui-chanh-xe/${this.shippingOrder.id}`, this.shippingForm);
        if (res?.success || res?.data?.success) {
          this.toaster.success('Đã gửi thông báo xuất bến qua Zalo ZNS / SMS Brandname cho chủ tàu và chành xe.');
          // eslint-disable-next-line no-undef
          const modalEl = bootstrap.Modal.getInstance(document.getElementById('adminShippingModal'));
          if (modalEl) modalEl.hide();
        }
      } catch (err) {
        this.toaster.error(err.response?.data?.message || 'Có lỗi xảy ra khi gửi thông báo');
      } finally {
        this.isSendingShipping = false;
      }
    },

    getImageUrl(path) {
      if (!path) return '';
      if (path.startsWith('blob:') || path.startsWith('http:') || path.startsWith('https:') || path.startsWith('data:')) {
        return path;
      }
      const apiBase = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';
      const backendHost = apiBase.replace(/\/api\/?$/, '');
      return `${backendHost}${path.startsWith('/') ? '' : '/'}${path}`;
    },

    openReceiptViewer(imgUrl) {
      if (!imgUrl) return;
      this.viewingReceiptImage = this.getImageUrl(imgUrl);
      // eslint-disable-next-line no-undef
      const modalEl = new bootstrap.Modal(document.getElementById('receiptViewerModal'));
      modalEl.show();
    },

    triggerAddReceiptSlot(type, slot) {
      if (type === 'quote') {
        if (slot === 1) this.$refs.quoteReceiptSlot1?.click();
        else this.$refs.quoteReceiptSlot2?.click();
      } else {
        if (slot === 1) this.$refs.orderReceiptSlot1?.click();
        else this.$refs.orderReceiptSlot2?.click();
      }
    },

    async onFileSelected(event, type, slot = 1) {
      const file = event.target.files?.[0];
      if (!file) return;
      await this.uploadReceiptFile(file, type, slot);
      event.target.value = '';
    },

    async onPasteReceipt(event, type, slot = 1) {
      const items = (event.clipboardData || event.originalEvent?.clipboardData)?.items;
      if (!items) return;
      for (const item of items) {
        if (item.type && item.type.indexOf('image') !== -1) {
          event.preventDefault();
          const file = item.getAsFile();
          if (file) {
            await this.uploadReceiptFile(file, type, slot);
            break;
          }
        }
      }
    },

    async uploadReceiptFile(file, type, slot = 1) {
      const targetObj = type === 'quote' ? this.selectedQuote : this.selectedOrder;
      const id = targetObj?.id;
      if (!id) return;
      this.uploadingKey = `${type}_${slot}`;
      this.isUploadingReceipt = true;
      try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        formData.append('id', id);
        formData.append('slot', slot);

        const res = await axiosClient.post('/admin/sales/upload-bien-lai', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        const url = res?.url || res?.data?.url;
        if (url) {
          const prop = slot === 2 ? 'receipt_image_2' : 'receipt_image';
          if (type === 'quote' && this.selectedQuote) {
            this.selectedQuote[prop] = url;
          } else if (type === 'order' && this.selectedOrder) {
            this.selectedOrder[prop] = url;
          }
          const label = slot === 1 ? 'Ảnh cọc' : 'Ảnh chuyển tiền còn lại';
          this.toaster.success(`Đã tải lên ${label} thành công!`);
          this.refreshData();
        } else {
          this.toaster.error(res?.message || 'Không thể tải ảnh lên.');
        }
      } catch (err) {
        console.error('Lỗi upload biên lai:', err);
        this.toaster.error(err?.response?.data?.message || 'Lỗi khi tải ảnh biên lai!');
      } finally {
        this.uploadingKey = '';
        this.isUploadingReceipt = false;
      }
    },

    async removeReceipt(type, slot = 1) {
      const targetObj = type === 'quote' ? this.selectedQuote : this.selectedOrder;
      const id = targetObj?.id;
      if (!id) return;

      const slotLabel = slot === 1 ? 'ảnh chuyển cọc' : 'ảnh chuyển tiền còn lại';
      const result = await Swal.fire({
        title: 'Gỡ ảnh biên lai?',
        text: `Bạn chắc chắn muốn gỡ ${slotLabel} này?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý gỡ',
        cancelButtonText: 'Hủy',
        customClass: {
          confirmButton: 'btn btn-danger px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (!result.isConfirmed) return;

      try {
        await axiosClient.post('/admin/sales/remove-bien-lai', { type, id, slot });
        const prop = slot === 2 ? 'receipt_image_2' : 'receipt_image';
        if (type === 'quote' && this.selectedQuote) {
          this.selectedQuote[prop] = '';
        } else if (type === 'order' && this.selectedOrder) {
          this.selectedOrder[prop] = '';
        }
        this.toaster.success(`Đã gỡ ${slotLabel}!`);
        this.refreshData();
      } catch (err) {
        this.toaster.error('Không thể gỡ ảnh biên lai.');
      }
    }
  }
};
</script>

<style scoped>
.action-btn {
  background: transparent;
  border: none;
  font-size: 1.25rem;
  padding: 0.25rem;
  line-height: 1;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}
.action-btn:hover {
  background-color: rgba(67, 89, 113, 0.08);
}
.custom-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #d9dee3;
  border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: transparent;
}
/* Ẩn mũi tên tăng giảm số mặc định của trình duyệt để ô input sạch sẽ */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
  -moz-appearance: textfield;
}
/* Cố định tiêu đề bảng khi cuộn danh sách phụ tùng */
.table-sticky-header thead th {
  position: sticky;
  top: 0;
  z-index: 2;
  background-color: #f5f5f9 !important;
  box-shadow: inset 0 -1px 0 #d9dee3;
}
/* Ẩn placeholder khi focus để con trỏ chuột không đè lên số 0 */
input:focus::placeholder,
textarea:focus::placeholder {
  color: transparent !important;
  opacity: 0 !important;
}
input:focus::-webkit-input-placeholder {
  color: transparent !important;
  opacity: 0 !important;
}
input:focus::-moz-placeholder {
  color: transparent !important;
  opacity: 0 !important;
}
</style>



