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
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'pending_quotes' }"
            @click="switchTab('pending_quotes')"
            role="tab"
            title="Yêu cầu báo giá mới từ khách"
          >
            <i class="bx bx-time-five me-1"></i>
            <span>Chờ Báo Giá</span>
            <span class="tab-badge-top">{{ countPendingQuotes }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'quoted_quotes' }"
            @click="switchTab('quoted_quotes')"
            role="tab"
            title="Báo giá đã gửi, đang chờ khách chốt đơn hoặc đặt cọc"
          >
            <i class="bx bx-send me-1"></i>
            <span>Đã Báo Giá</span>
            <span class="tab-badge-top">{{ countQuotedQuotes }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'processing' }"
            @click="switchTab('processing')"
            role="tab"
            title="Đơn hàng đang chuẩn bị, xuất kho hoặc gửi chành xe"
          >
            <i class="bx bx-package me-1"></i>
            <span>Đang Xử Lý & Giao</span>
            <span class="tab-badge-top">{{ countProcessing }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'completed' }"
            @click="switchTab('completed')"
            role="tab"
            title="Đơn hàng hoàn tất và đối soát 100%"
          >
            <i class="bx bx-check-circle me-1"></i>
            <span>Hoàn Thành</span>
            <span class="tab-badge-top">{{ countCompleted }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'cancelled' }"
            @click="switchTab('cancelled')"
            role="tab"
            title="Yêu cầu hoặc đơn hàng đã bị hủy"
          >
            <i class="bx bx-x-circle me-1"></i>
            <span>Đã Hủy</span>
            <span class="tab-badge-top">{{ countCancelled }}</span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'all' }"
            @click="switchTab('all')"
            role="tab"
            title="Toàn bộ báo giá và đơn hàng"
          >
            <i class="bx bx-list-ul me-1"></i>
            <span>Tất Cả</span>
            <span class="tab-badge-top">{{ countAll }}</span>
          </button>
        </li>

        <li class="nav-item ms-auto">
          <button 
            type="button"
            class="nav-link position-relative" 
            :class="{ active: currentTab === 'transactions' }"
            @click="switchTab('transactions')"
            role="tab"
            title="Lịch sử giao dịch & đối soát VietQR IPN"
          >
            <i class="bx bx-qr-scan me-1"></i>
            <span>Đối Soát VietQR</span>
            <span class="tab-badge-top">{{ transactions.length }}</span>
          </button>
        </li>
      </ul>

      <!-- MAIN CONTENT AREA (Chuẩn Sneat Admin) -->
      <!-- SECTION A: PIPELINE TABS (Báo Giá & Đơn Hàng) -->
      <div v-if="currentTab !== 'transactions'">
        <div class="card">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">{{ tabTitle }}</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <button 
                type="button" 
                class="btn btn-secondary text-nowrap"
                @click="refreshData"
                :disabled="isFetchingOrders || isFetchingQuotes"
                title="Làm mới danh sách"
              >
                <i class="bx bx-refresh me-1" :class="{ 'bx-spin': isFetchingOrders || isFetchingQuotes }"></i>
                Làm mới
              </button>
              <button 
                type="button" 
                class="btn btn-primary text-nowrap shadow-sm" 
                @click="exportJapanPO" 
                title="Xuất file CSV UTF-8 đơn đặt hàng nhà cung cấp Nhật"
              >
                <i class="bx bx-download me-1"></i> Xuất Excel Đặt Hàng Nhật (PO)
              </button>
            </div>
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
              <div style="width: 200px;" class="flex-shrink-0" v-if="currentTab === 'all' || currentTab === 'processing' || currentTab === 'cancelled'">
                <select class="form-select" v-model="typeFilter">
                  <option value="all">Tất cả phân loại</option>
                  <option value="quote">Chỉ Báo Giá</option>
                  <option value="order">Chỉ Đơn Hàng</option>
                </select>
              </div>
              <button class="btn btn-secondary text-nowrap flex-shrink-0" @click="searchQuery = ''; typeFilter = 'all'">
                Đặt lại
              </button>
            </div>
          </div>

          <!-- Bảng dữ liệu (Table) chuẩn Admin Sneat -->
          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center text-uppercase" style="width: 5%">STT</th>
                  <th class="text-uppercase text-center" style="width: 10%">Phân Loại</th>
                  <th class="text-uppercase" style="width: 14%">Mã Giao Dịch</th>
                  <th class="text-uppercase" style="width: 20%">Khách Hàng</th>
                  <th class="text-uppercase" style="width: 13%">Thời Gian</th>
                  <th class="text-end text-uppercase" style="width: 14%">Tổng Tiền</th>
                  <th class="text-end text-uppercase" style="width: 12%">Đã Thu / Cọc</th>
                  <th class="text-center text-uppercase" style="width: 12%">Trạng Thái</th>
                  <th class="text-center text-uppercase" style="width: 10%">Hành Động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-for="(item, index) in filteredTabList" :key="item._type + item.id">
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td class="text-center">
                    <span v-if="item._type === 'quote'" class="badge bg-label-info">
                      BÁO GIÁ
                    </span>
                    <span v-else class="badge bg-label-primary">
                      ĐƠN HÀNG
                    </span>
                  </td>
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
                    <span v-if="item._type === 'order' && item.paid_amount > 0" class="fw-bold text-success">
                      {{ formatCurrency(item.paid_amount) }}
                    </span>
                    <span v-else-if="item.deposit_required > 0" class="text-warning fw-medium" style="font-size: 0.85rem;">
                      Cọc: {{ formatCurrency(item.deposit_required) }}
                    </span>
                    <span v-else class="text-muted" style="font-size: 0.85rem;">-</span>
                  </td>
                  <td class="text-center">
                    <!-- Trạng thái Báo Giá (Chuẩn Sneat bg-label-*) -->
                    <template v-if="item._type === 'quote'">
                      <span v-if="item.status === 0" class="badge bg-label-warning">CHỜ BÁO GIÁ</span>
                      <span v-else-if="item.status === 1" class="badge bg-label-info">ĐÃ GỬI BÁO GIÁ</span>
                      <span v-else-if="item.status === 2" class="badge bg-label-success">ĐÃ CHỐT</span>
                      <span v-else-if="item.status === 3" class="badge bg-label-danger">ĐÃ HỦY</span>
                      <span v-else class="badge bg-label-secondary">KHÁC</span>
                    </template>
                    <!-- Trạng thái Đơn Hàng (Chuẩn Sneat bg-label-*) -->
                    <template v-else>
                      <span v-if="item.status === 0 && Number(item.payment_status) === 0" class="badge bg-label-warning">CHỜ DUYỆT & CỌC</span>
                      <span v-else-if="item.status === 0" class="badge bg-label-info">CHỜ DUYỆT</span>
                      <span v-else-if="item.status === 1" class="badge bg-label-primary">ĐANG CHUẨN BỊ</span>
                      <span v-else-if="item.status === 2" class="badge bg-label-info">ĐANG GIAO</span>
                      <span v-else-if="item.status === 3" class="badge bg-label-success">HOÀN THÀNH</span>
                      <span v-else-if="item.status === 4" class="badge bg-label-danger">ĐÃ HỦY</span>
                      <span v-else class="badge bg-label-secondary">ĐANG XỬ LÝ</span>
                    </template>
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                      <button 
                        type="button" 
                        class="action-btn text-primary" 
                        @click="handleAction(item)" 
                        data-bs-toggle="modal" 
                        :data-bs-target="item._type === 'quote' ? '#quoteModal' : '#orderModal'" 
                        :title="canProcessOrders ? 'Xử lý' : 'Xem chi tiết'"
                      >
                        <i :class="canProcessOrders ? 'bx bx-edit-alt' : 'bx bx-show'"></i>
                      </button>
                      <button 
                        v-if="item._type === 'quote' && item.customer_phone" 
                        type="button" 
                        class="action-btn text-info" 
                        @click="quickOpenZalo(item)" 
                        title="Gửi Zalo cho khách"
                      >
                        <i class="bx bxl-telegram"></i>
                      </button>
                      <button 
                        v-if="canDeleteOrders" 
                        type="button" 
                        class="action-btn text-danger" 
                        @click="handleDelete(item)" 
                        title="Xóa"
                      >
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredTabList.length === 0">
                  <td colspan="9" class="text-center py-5 text-muted">
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

      <!-- SECTION B: VIETQR IPN TRANSACTIONS TAB (Chuẩn Sneat Admin) -->
      <div v-else>
        <div class="card">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">Lịch Sử Giao Dịch & Đối Soát Ngân Hàng (VietQR IPN)</h5>
            <button type="button" class="btn btn-primary text-nowrap shadow-sm" @click="openSimulateModal(null)">
              <i class="bx bx-plus me-1"></i> Giả Lập Giao Dịch Sandbox
            </button>
          </div>
          <div class="card-body pt-3 pb-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <div class="flex-grow-1">
                <input type="text" class="form-control" placeholder="Tìm theo Mã tham chiếu (DH/YCBG), Mã GD, Nội dung..." v-model="txSearch">
              </div>
              <div style="width: 200px;" class="flex-shrink-0">
                <select class="form-select" v-model="txStatus">
                  <option value="all">Tất cả trạng thái</option>
                  <option value="thanh_cong">Thành công</option>
                  <option value="mo_phong">Mô phỏng (Sandbox)</option>
                  <option value="that_bai">Thất bại</option>
                </select>
              </div>
              <button type="button" class="btn btn-secondary text-nowrap flex-shrink-0" @click="loadTransactions" :disabled="isFetchingTransactions">
                <i class="bx bx-refresh me-1" :class="{ 'bx-spin': isFetchingTransactions }"></i> Làm mới
              </button>
            </div>
          </div>
          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center text-uppercase" style="width: 5%">STT</th>
                  <th class="text-uppercase" style="width: 15%">Mã Giao Dịch</th>
                  <th class="text-uppercase" style="width: 15%">Cổng / Ngân Hàng</th>
                  <th class="text-uppercase" style="width: 15%">Mã Đơn / Báo Giá</th>
                  <th class="text-end text-uppercase" style="width: 15%">Số Tiền Nhận</th>
                  <th class="text-uppercase" style="width: 20%">Nội Dung Chuyển Khoản</th>
                  <th class="text-center text-uppercase" style="width: 15%">Thời Gian</th>
                  <th class="text-center text-uppercase" style="width: 10%">Trạng Thái</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isFetchingTransactions">
                  <td colspan="8" class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 mb-0">Đang tải lịch sử đối soát giao dịch...</p>
                  </td>
                </tr>
                <tr v-for="(tx, index) in filteredTransactions" :key="tx.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td><span class="fw-bold text-dark font-monospace">{{ tx.ma_giao_dich }}</span></td>
                  <td><span class="badge bg-label-info">{{ tx.gateway || 'VietQR Bank' }}</span></td>
                  <td>
                    <span class="fw-bold text-primary">{{ tx.ma_tham_chieu || 'Chưa nhận diện' }}</span>
                  </td>
                  <td class="text-end">
                    <span class="fw-bold text-success fs-6">+{{ formatCurrency(tx.so_tien) }}</span>
                  </td>
                  <td><span class="text-truncate d-inline-block" style="max-width: 250px;" :title="tx.noi_dung">{{ tx.noi_dung }}</span></td>
                  <td class="text-center text-muted" style="font-size: 0.85rem;">{{ tx.thoi_gian_giao_dich || tx.created_at }}</td>
                  <td class="text-center">
                    <span v-if="tx.trang_thai === 'thanh_cong'" class="badge bg-label-success">THÀNH CÔNG</span>
                    <span v-else-if="tx.trang_thai === 'mo_phong'" class="badge bg-label-warning">SANDBOX TEST</span>
                    <span v-else class="badge bg-label-danger">THẤT BẠI</span>
                  </td>
                </tr>
                <tr v-if="!isFetchingTransactions && filteredTransactions.length === 0">
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bx bx-receipt text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer py-3 border-top">
            <span class="text-muted fw-medium">Tổng cộng: {{ (filteredTransactions && filteredTransactions.length) ? filteredTransactions.length : 0 }} mục</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Chi Tiết & Báo Giá (Từ YeuCauBaoGia) -->
    <div class="modal fade" id="quoteModal" tabindex="-1" aria-hidden="true" ref="quoteModal">
      <div class="modal-dialog modal-xl mt-5" role="document">
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
                <!-- Cột trái: Thông tin & Ghi chú -->
                <div class="col-md-4 mb-4 mb-md-0 border-end">
                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Thông tin Khách Hàng</h6>
                  <div class="mb-4">
                    <p class="mb-1 fw-bold text-dark">{{ selectedQuote.customer_name }}</p>
                    <p class="mb-1 text-dark"><i class="bx bx-phone text-primary me-2"></i>{{ selectedQuote.customer_phone }}</p>
                    <p class="mb-0 text-dark"><i class="bx bx-envelope text-primary me-2"></i>{{ selectedQuote.customer_email }}</p>
                  </div>

                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Ghi Chú (Khách Hàng)</h6>
                  <div class="mb-4 p-3 bg-label-secondary rounded">
                    <p class="mb-0 text-dark" style="font-size: 0.9rem; white-space: pre-wrap;">{{ selectedQuote.customer_notes || 'N/A.' }}</p>
                  </div>

                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Ghi Chú Zalo (Nội Bộ)</h6>
                  <div class="mb-4">
                    <textarea v-if="!isQuoteReadOnly" class="form-control" rows="3" v-model="selectedQuote.zalo_notes" placeholder="Ghi chú kết quả thương lượng Zalo..."></textarea>
                      <div v-else class="p-3 bg-light rounded text-dark border" style="white-space: pre-wrap; font-size: 0.85rem;">{{ selectedQuote.zalo_notes || 'N/A' }}</div>
                  </div>
                  
                  <div class="row align-items-center mb-3">
                    <label class="col-md-4 col-form-label fw-bold text-uppercase text-primary" style="font-size: 0.75rem;">Trạng Thái</label>
                    <div class="col-md-8">
                      <v-select
                        v-model="selectedQuote.status" :disabled="isQuoteReadOnly"
                        :options="[
                          {value: 0, label: 'Chờ Xử Lý'},
                          {value: 1, label: 'Đã Báo Giá'},
                          {value: 2, label: 'Hoàn Tất'},
                          {value: 3, label: 'Đã Hủy'}
                        ]"
                        :can-clear="false"
                      />
                    </div>
                  </div>
                </div>

                <!-- Cột phải: Danh sách Phụ Tùng -->
                <div class="col-md-8">
                  <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Chi Tiết Sản Phẩm Cần Báo Giá</h6>
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-4">
                      <thead class="table-light">
                        <tr>
                          <th class="text-center text-uppercase" style="width: 5%">STT</th>
                          <th class="text-uppercase" style="width: 40%">Tên Phụ Tùng</th>
                          <th class="text-center text-uppercase" style="width: 15%">Số Lượng</th>
                          <th class="text-uppercase" style="width: 25%">Đơn Giá (VNĐ)</th>
                          <th class="text-end text-uppercase" style="width: 15%">Thành Tiền</th>
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
                          <td class="text-center">{{ idx + 1 }}</td>
                          <td>
                            <span class="d-block fw-bold" style="font-size: 0.85rem;">{{ detail.part_name }}</span>
                            <span class="text-dark fw-medium" style="font-size: 0.75rem;">Mã: {{ detail.part_code }}</span>
                          </td>
                          <td class="text-center">{{ detail.qty }}</td>
                          <td>
                            <input v-if="!isQuoteReadOnly" type="number" class="form-control form-control-sm text-end fw-bold text-dark" v-model.number="detail.price" placeholder="Nhập giá..." />
                              <span v-else class="fw-bold text-dark d-block text-end">{{ formatCurrency(detail.price || 0) }}</span>
                          </td>
                          <td class="text-end fw-bold text-danger">
                            {{ formatCurrency(detail.qty * (detail.price || 0)) }}
                          </td>
                        </tr>
                      </tbody>
                      <tfoot class="table-light">
                        <tr>
                          <td colspan="4" class="text-end text-muted align-middle">Tạm Tính:</td>
                          <td class="text-end fw-bold text-danger align-middle">{{ formatCurrency(calculateTotal(selectedQuote.items)) }}</td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-end text-muted align-middle">Phí Vận Chuyển:</td>
                          <td class="text-end">
                            <div class="d-flex flex-column align-items-end">
                              <input type="number" class="form-control form-control-sm text-end text-danger fw-bold d-inline-block mb-1" style="width: 150px;" v-model.number="selectedQuote.shipping_fee" :disabled="isQuoteReadOnly || selectedQuote.freight_collect" placeholder="0">
                              <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" v-model="selectedQuote.freight_collect" :disabled="isQuoteReadOnly" @change="selectedQuote.freight_collect ? selectedQuote.shipping_fee = 0 : null">
                                <label class="form-check-label text-muted" style="font-size: 0.75rem;">Khách trả cước</label>
                              </div>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-end text-muted align-middle">Yêu Cầu Cọc:</td>
                          <td class="text-end">
                            <input v-if="!isQuoteReadOnly" type="number" class="form-control form-control-sm text-end text-primary fw-bold d-inline-block" style="width: 150px;" v-model.number="selectedQuote.deposit_required" placeholder="0">
                              <span v-else class="fw-bold text-primary fs-6">{{ formatCurrency(selectedQuote.deposit_required || 0) }}</span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-end fw-bold text-dark fs-6 align-middle">Tổng Cộng:</td>
                          <td class="text-end fw-bold text-danger fs-6 align-middle">{{ formatCurrency(calculateTotal(selectedQuote.items) + (selectedQuote.freight_collect ? 0 : (selectedQuote.shipping_fee || 0))) }}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer border-top py-2 mt-2 d-flex justify-content-between align-items-center">
              <div>
                <button type="button" class="btn btn-primary px-3 me-2 shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly && canProcessOrders" @click.prevent="openZaloMessage">
                  <i class="bx bx-chat me-1"></i> Mở Zalo Gửi Khách
                </button>
                <button type="button" class="btn btn-success px-3 me-2 shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly && canProcessOrders" @click.prevent="copyMagicLink">
                  <i class="bx bx-link me-1"></i> Copy Link Zalo
                </button>
                <button type="button" class="btn btn-info px-3 text-white shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly && canProcessOrders" @click.prevent="adminChotDon">
                  <i class="bx bx-check-double me-1"></i> Khách Đã Chốt (Tạo Đơn)
                </button>
              </div>
              <div>
                <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" v-if="selectedQuote && !isQuoteReadOnly && canProcessOrders">Lưu Báo Giá</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Chi Tiết Đơn Hàng -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
              Chi Tiết Đơn Hàng - <span class="text-primary">{{ selectedOrder?.code }}</span>
            </h4>
            <div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="quoteModalCloseBtn"></button>
            </div>
          </div>
          <div class="modal-body pb-2" v-if="selectedOrder">
            <div class="row">
              <div class="col-md-4 mb-4 mb-md-0 border-end">
                <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Thông tin Khách Hàng</h6>
                <div class="mb-4">
                  <p class="mb-1 fw-bold text-dark">{{ selectedOrder.customer_name }}</p>
                  <p class="mb-1 text-dark"><i class="bx bx-phone text-primary me-2"></i>{{ selectedOrder.customer_phone }}</p>
                  <p class="mb-0 text-dark"><i class="bx bx-map text-primary me-2"></i>{{ selectedOrder.address }}</p>
                </div>

                <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Phân Loại & Thanh Toán</h6>
                <div class="row align-items-center mb-3">
                  <label class="col-md-4 col-form-label fw-bold text-uppercase text-primary" style="font-size: 0.75rem;">Loại Đơn</label>
                  <div class="col-md-8">
                    <v-select
                      v-model="selectedOrder.order_type" :disabled="isOrderReadOnly"
                      :options="[
                        {value: 'b2c', label: 'B2C (Lẻ)'},
                        {value: 'b2b', label: 'B2B (Đại Lý)'}
                      ]"
                      :can-clear="false"
                    />
                  </div>
                </div>
                <div class="row align-items-center mb-3">
                  <label class="col-md-4 col-form-label fw-bold text-uppercase text-primary" style="font-size: 0.75rem;">Thanh Toán</label>
                  <div class="col-md-8">
                    <v-select
                      v-model="selectedOrder.payment_status" :disabled="isOrderReadOnly"
                      :options="[
                        {value: 0, label: 'Chưa Thanh Toán'},
                        {value: 1, label: 'Đã Đặt Cọc'},
                        {value: 2, label: 'Đã Thanh Toán'}
                      ]"
                      :can-clear="false"
                    />
                  </div>
                </div>
                <div class="row align-items-center mb-3">
                  <label class="col-md-4 col-form-label fw-bold text-uppercase text-primary" style="font-size: 0.75rem;">Xử Lý Đơn</label>
                  <div class="col-md-8">
                    <v-select
                      v-model="selectedOrder.status" :disabled="isOrderReadOnly"
                      :options="[
                        {value: 0, label: 'Chờ Xác Nhận'},
                        {value: 1, label: 'Đang Xử Lý'},
                        {value: 2, label: 'Hoàn Tất'},
                        {value: 3, label: 'Đã Hủy'}
                      ]"
                      :can-clear="false"
                    />
                  </div>
                </div>
              </div>

              <div class="col-md-8">
                <h6 class="fw-bold mb-3 text-uppercase text-primary" style="font-size: 0.75rem;">Sản Phẩm Trong Đơn</h6>
                <div class="table-responsive">
                  <table class="table table-bordered table-sm mb-4">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center text-uppercase" style="width: 5%">STT</th>
                        <th class="text-uppercase" style="width: 40%">Tên Phụ Tùng</th>
                        <th class="text-center text-uppercase" style="width: 15%">Số Lượng</th>
                        <th class="text-uppercase" style="width: 20%">Đơn Giá</th>
                        <th class="text-end text-uppercase" style="width: 20%">Thành Tiền</th>
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
                        <td class="text-center">{{ idx + 1 }}</td>
                        <td>
                          <span class="d-block fw-bold" style="font-size: 0.85rem;">{{ detail.part_name }}</span>
                          <span class="text-dark fw-medium" style="font-size: 0.75rem;">Mã: {{ detail.part_code }}</span>
                        </td>
                        <td class="text-center align-middle">
                          <span class="fw-bold fs-5 text-dark">{{ detail.qty }}</span>
                          <span v-if="detail.in_stock" class="d-block text-secondary" style="font-size: 0.75rem;">Có sẵn</span>
                          <span v-else class="d-block text-danger" style="font-size: 0.75rem;">Hết hàng</span>
                        </td>
                        <td class="text-end">{{ formatCurrency(detail.price) }}</td>
                        <td class="text-end fw-bold text-danger">
                          {{ formatCurrency(detail.qty * detail.price) }}
                        </td>
                      </tr>
                    </tbody>
                    <tfoot class="table-light">
                      <tr>
                        <td colspan="4" class="text-end text-muted">Tạm Tính:</td>
                        <td class="text-end fw-bold text-danger">{{ formatCurrency(calculateTotal(selectedOrder.items)) }}</td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-end text-muted align-middle">Phí Vận Chuyển:</td>
                        <td class="text-end">
                          <div class="d-flex flex-column align-items-end">
                            <input type="number" class="form-control form-control-sm text-end text-danger fw-bold d-inline-block mb-1" style="width: 150px;" v-model.number="selectedOrder.shipping_fee" :disabled="isOrderReadOnly || selectedOrder.freight_collect" placeholder="0">
                            <div class="form-check form-switch mb-0">
                              <input class="form-check-input" type="checkbox" v-model="selectedOrder.freight_collect" :disabled="isOrderReadOnly" @change="selectedOrder.freight_collect ? selectedOrder.shipping_fee = 0 : null">
                              <label class="form-check-label text-muted" style="font-size: 0.75rem;">Khách trả cước</label>
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-end text-muted align-middle">Yêu Cầu Cọc:</td>
                        <td class="text-end">
                          <input v-if="!isOrderReadOnly" type="number" class="form-control form-control-sm text-end text-primary fw-bold d-inline-block" style="width: 150px;" v-model.number="selectedOrder.deposit_required" placeholder="0">
                          <span v-else class="fw-bold text-primary fs-6">{{ formatCurrency(selectedOrder.deposit_required || 0) }}</span>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-end text-muted align-middle">Đã Thu:</td>
                        <td class="text-end">
                          <input v-if="!isOrderReadOnly" type="number" class="form-control form-control-sm text-end text-success fw-bold d-inline-block" style="width: 150px;" v-model.number="selectedOrder.paid_amount" placeholder="0">
                          <span v-else class="fw-bold text-success fs-6">{{ formatCurrency(selectedOrder.paid_amount || 0) }}</span>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-end fw-bold text-dark fs-6 align-middle">Cần Thanh Toán:</td>
                        <td class="text-end fw-bold text-danger fs-6 align-middle">
                          {{ formatCurrency((calculateTotal(selectedOrder.items) + (selectedOrder.freight_collect ? 0 : (selectedOrder.shipping_fee || 0))) - (selectedOrder.paid_amount || 0)) }}
                          <div class="text-muted fw-normal" style="font-size: 0.75rem;" v-if="selectedOrder.paid_amount > 0">
                            (Tổng đơn: {{ formatCurrency(calculateTotal(selectedOrder.items) + (selectedOrder.freight_collect ? 0 : (selectedOrder.shipping_fee || 0))) }})
                          </div>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top py-3 mt-2 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-primary px-3 shadow-sm" @click="openVietQrModal(selectedOrder)" title="Xem mã VietQR tự động">
                <i class="bx bx-qr-scan me-1"></i> Mã VietQR
              </button>
              <button type="button" class="btn btn-outline-info px-3 shadow-sm" @click="openShippingModal(selectedOrder)" title="Gửi Zalo ZNS / SMS xuất bến chành xe">
                <i class="bx bxs-truck me-1"></i> Gửi Tin Chành Xe
              </button>
              <button type="button" class="btn btn-outline-warning px-3 shadow-sm" @click="openSimulateModal(selectedOrder)" title="Thử nghiệm đối soát sandbox">
                <i class="bx bx-money-withdraw me-1"></i> Test VietQR
              </button>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary px-4 shadow-sm" @click="saveOrder" v-if="!isOrderReadOnly && canEditOrders">Lưu Thay Đổi</button>
              <button type="button" class="btn btn-primary px-4 shadow-sm">
                <i class="bx bx-printer me-1"></i> In Hóa Đơn
              </button>
            </div>
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
              <i class="bx bx-qr-scan me-2"></i>Mã VietQR Napas247 - Đơn Hàng
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

    <!-- Modal 3: Giả Lập / Mô Phỏng Đối Soát VietQR Sandbox -->
    <div class="modal fade" id="adminSimulateModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-warning text-dark py-3">
            <h5 class="modal-title fw-bold mb-0">
              <i class="bx bx-test-tube me-2"></i>Mô Phỏng Giao Dịch Chuyển Khoản Ngân Hàng (Sandbox)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitSimulatePayment">
            <div class="modal-body p-4">
              <p class="text-muted small">
                Tính năng dành cho kiểm thử: Giả lập webhook của SePay/Casso/Ngân hàng gửi về hệ thống để kiểm tra đối soát tự động cập nhật số tiền đã thu và kích hoạt thông báo.
              </p>
              <div class="mb-3">
                <label class="form-label fw-bold">Mã Tham Chiếu (Đơn hàng DH... hoặc Báo giá YCBG...)</label>
                <input type="text" class="form-control" v-model="simulateForm.reference_code" placeholder="VD: DH-261011-F605" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Số Tiền Chuyển Vào (VND)</label>
                <input type="number" class="form-control" v-model.number="simulateForm.amount" min="1000" step="1000" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Nội Dung Chuyển Khoản Ngân Hàng</label>
                <input type="text" class="form-control" v-model="simulateForm.content" placeholder="VD: JPM DH-261011-F605 TIEN COC">
              </div>
            </div>
            <div class="modal-footer py-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
              <button type="submit" class="btn btn-warning text-dark fw-bold shadow-sm" :disabled="isSimulating">
                <span v-if="isSimulating" class="spinner-border spinner-border-sm me-1" role="status"></span>
                <i v-else class="bx bx-check-double me-1"></i>
                {{ isSimulating ? 'Đang gửi...' : 'Kích Hoạt Đối Soát Ngay' }}
              </button>
            </div>
          </form>
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
import Swal from 'sweetalert2';

export default {
  name: "DonHang",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();
    
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

    return { authStore, queryClient, rawOrders, rawQuotes, isFetchingOrders, isFetchingQuotes, deleteQuoteMutation, deleteOrderMutation, saveQuoteMutation, saveOrderMutation };
  },
  data() {
    return {
      currentUserRole: JSON.parse(localStorage.getItem('auth_user') || '{}').roles?.[0]?.toUpperCase() || 'ADMIN',
      currentTab: 'pending_quotes',
      searchQuery: '',
      typeFilter: 'all',
      selectedQuote: null,
      selectedOrder: null,
      // Transactions data
      transactions: [],
      isFetchingTransactions: false,
      txSearch: "",
      txStatus: "all",
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
      // Simulation Modal
      simulateForm: {
        reference_code: "",
        amount: 0,
        content: ""
      },
      isSimulating: false
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
    isQuoteReadOnly() {
      return this.selectedQuote && this.selectedQuote.status >= 2;
    },
    isOrderReadOnly() {
      return this.selectedOrder && this.selectedOrder.status >= 3;
    },

    // PIPELINE LISTS
    pendingQuotes() {
      return this.quotes
        .filter(q => q.status === 0)
        .map(q => ({ ...q, _type: 'quote' }))
        .sort((a, b) => b.id - a.id);
    },
    quotedQuotes() {
      return this.quotes
        .filter(q => q.status === 1)
        .map(q => ({ ...q, _type: 'quote' }))
        .sort((a, b) => b.id - a.id);
    },
    processingOrders() {
      const list = [];
      this.quotes.filter(q => q.status === 2).forEach(q => list.push({ ...q, _type: 'quote' }));
      this.orders.filter(o => o.status !== 3 && o.status !== 4).forEach(o => list.push({ ...o, _type: 'order' }));
      return list.sort((a, b) => b.id - a.id);
    },
    completedOrders() {
      return this.orders
        .filter(o => o.status === 3)
        .map(o => ({ ...o, _type: 'order' }))
        .sort((a, b) => b.id - a.id);
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
        case 'quoted_quotes': return 'Danh Sách Đang Chờ Khách Chốt Giá';
        case 'processing': return 'Đơn Hàng Đang Xử Lý & Giao Hàng';
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
        case 'processing': return 'Đơn hàng đang trong tiến trình xuất kho, đóng kiện hoặc đang trên đường chuyển ra chành xe';
        case 'completed': return 'Đơn hàng khách đã nhận thành công và đối soát thanh toán đủ 100%';
        case 'cancelled': return 'Các yêu cầu báo giá hoặc đơn hàng đã bị khách hủy hoặc nhân viên hủy bỏ';
        case 'all': return 'Tra cứu tổng thể lịch sử giao dịch và yêu cầu báo giá trên toàn hệ thống';
        default: return '';
      }
    },

    filteredTransactions() {
      let list = this.transactions || [];
      if (this.txSearch) {
        const s = this.txSearch.toLowerCase().trim();
        list = list.filter(t => 
          (t.ma_giao_dich && String(t.ma_giao_dich).toLowerCase().includes(s)) ||
          (t.ma_tham_chieu && String(t.ma_tham_chieu).toLowerCase().includes(s)) ||
          (t.noi_dung && String(t.noi_dung).toLowerCase().includes(s))
        );
      }
      if (this.txStatus !== 'all') {
        list = list.filter(t => t.trang_thai === this.txStatus);
      }
      return list;
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
          date: d.toLocaleDateString('vi-VN'),
          time: d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
          customer_name: item.khach_hang_ten || 'Khách Vãng Lai',
          customer_phone: item.khach_hang_sdt || '',
          customer_email: item.khach_hang_email || '',
          customer_notes: item.ghi_chu_khach_hang || '',
          zalo_notes: item.ghi_chu_zalo || '',
          token: item.token,
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
          date: d.toLocaleDateString('vi-VN'),
          time: d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
          customer_name: item.khach_hang_ten || item.khach_hang?.ho_ten || 'Khách Lẻ',
          customer_phone: item.khach_hang_sdt || item.khach_hang?.so_dien_thoai || '',
          customer_email: item.khach_hang_email || item.khach_hang?.user?.email || '',
          customer_notes: item.ghi_chu_khach_hang || '',
          address: item.dia_chi ? item.dia_chi.dia_chi_cu_the : (item.dia_chi_text || 'Chưa có địa chỉ'),
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
      if (tabKey === 'transactions') {
        this.loadTransactions();
      }
    },
    refreshData() {
      this.queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] });
      this.queryClient.invalidateQueries({ queryKey: ['don-hang'] });
      if (this.currentTab === 'transactions') {
        this.loadTransactions();
      }
    },
    quickOpenZalo(item) {
      const phone = (item.customer_phone || '').replace(/[^0-9]/g, '');
      if (!phone) {
        Swal.fire({ icon: 'warning', iconColor: '#ff3e1d', title: 'Thiếu số điện thoại', text: 'Khách hàng chưa có số điện thoại để gửi Zalo.' });
        return;
      }
      const link = item.token ? `${window.location.origin}/tra-cuu-bao-gia/${item.token}` : '';
      const total = item.total_amount || 0;
      const deposit = item.deposit_required || 0;
      
      let msg = `Chào ${item.customer_name || 'Quý khách'}, JPM Marine đã gửi bảng báo giá phụ tùng động cơ thủy cho yêu cầu [${item.code}]:\n`;
      msg += `• Tổng tiền tạm tính: ${this.formatCurrency(total)}\n`;
      if (deposit > 0) {
        msg += `• Tiền đặt cọc giữ hàng: ${this.formatCurrency(deposit)}\n`;
      }
      if (link) {
        msg += `• Link xem chi tiết và chốt đơn: ${link}\n`;
      }
      msg += `(Lưu ý: Báo giá có hiệu lực trong 72 giờ theo tỷ giá JPY hiện hành). Trân trọng cảm ơn Quý khách!`;

      window.open(`https://zalo.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
    },
    handleAction(item) {
      if (item._type === 'quote') {
        this.openQuoteModal(item);
      } else {
        this.openOrderModal(item);
      }
    },
    openZaloMessage() {
      if (!this.selectedQuote) return;
      const phone = (this.selectedQuote.customer_phone || '').replace(/[^0-9]/g, '');
      if (!phone) {
        Swal.fire({ icon: 'warning', iconColor: '#ff3e1d', title: 'Thiếu số điện thoại', text: 'Khách hàng chưa có số điện thoại để gửi Zalo.' });
        return;
      }
      const link = this.selectedQuote.token ? `${window.location.origin}/tra-cuu-bao-gia/${this.selectedQuote.token}` : '';
      const total = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      const deposit = this.selectedQuote.deposit_required || 0;
      
      let msg = `Chào ${this.selectedQuote.customer_name || 'Quý khách'}, JPM Marine đã gửi bảng báo giá phụ tùng động cơ thủy cho yêu cầu [${this.selectedQuote.code}]:\n`;
      msg += `• Tổng tiền tạm tính: ${this.formatCurrency(total)}\n`;
      if (deposit > 0) {
        msg += `• Tiền đặt cọc giữ hàng: ${this.formatCurrency(deposit)}\n`;
      }
      if (link) {
        msg += `• Link xem chi tiết và chốt đơn: ${link}\n`;
      }
      msg += `(Lưu ý: Báo giá có hiệu lực trong 72 giờ theo tỷ giá JPY hiện hành). Trân trọng cảm ơn Quý khách!`;

      window.open(`https://zalo.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
    },
    async exportJapanPO() {
      try {
        const res = await axiosClient.get('/admin/don-hang/xuat-excel-po-nhat', { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([res.data], { type: 'text/csv;charset=utf-8;' }));
        const link = document.createElement('a');
        link.href = url;
        const dateStr = new Date().toISOString().slice(0, 10);
        link.setAttribute('download', `PO_Nhat_JPM_${dateStr}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        Swal.fire({
          icon: 'success',
          title: 'Xuất file thành công',
          text: 'Danh sách đơn hàng cần đặt từ Nhật đã được tải về (UTF-8 CSV).',
          timer: 2000,
          showConfirmButton: false
        });
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể xuất file đơn đặt hàng Nhật.' });
      }
    },
    copyMagicLink() {
      if (!this.selectedQuote || !this.selectedQuote.token) {
        Swal.fire({ icon: 'warning',
          iconColor: '#ff3e1d', title: 'Lỗi', text: 'Báo giá này chưa có Link tra cứu!' });
        return;
      }
      const link = `${window.location.origin}/tra-cuu-bao-gia/${this.selectedQuote.token}`;
      const msg = `Chào JPM Marine đã gửi báo giá cho đơn hàng ${this.selectedQuote.code}. Anh/chị xem và chốt đơn tại đây nhé:\n${link}`;
      navigator.clipboard.writeText(msg).then(() => {
        Swal.fire({ icon: 'success', title: 'Đã copy', text: 'Đã copy đường dẫn Zalo vào khay nhớ tạm.', timer: 2000, showConfirmButton: false });
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
          if (res.data.success) {
            Swal.fire({ icon: 'success', title: 'Thành công', text: res.data.message });
            // Close modal implicitly via reload/invalidation
            this.queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] });
            this.queryClient.invalidateQueries({ queryKey: ['don-hang'] });
            this.$refs.quoteModalCloseBtn?.click();
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Không thể tạo đơn.';
          Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
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
          onSuccess: () => Swal.fire({ icon: 'success', title: 'Đã xóa!', text: 'Yêu cầu báo giá đã được xóa thành công.', timer: 2000, showConfirmButton: false }),
          onError: (err) => {
            const msg = err?.response?.data?.message || 'Không thể xóa báo giá này.';
            Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
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
          onSuccess: () => Swal.fire({ icon: 'success', title: 'Đã xóa!', text: 'Đơn hàng đã được xóa thành công.', timer: 2000, showConfirmButton: false }),
          onError: (err) => {
            const msg = err?.response?.data?.message || 'Không thể xóa đơn hàng này.';
            Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
          }
        });
      }
    },
    formatCurrency(value) {
      if (!value) return "0 đ";
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(value);
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
    async saveQuote() {
      if (this.selectedQuote.items && this.selectedQuote.items.length > 0) {
        this.selectedQuote.total_amount = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      } else {
        this.selectedQuote.total_amount = (this.selectedQuote.total_amount || 0) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      }

      if (this.selectedQuote.status === 0 && this.selectedQuote.total_amount > 0) {
        this.selectedQuote.status = 1;
      }
      
      try {
        const payload = {
            tong_tien: this.selectedQuote.total_amount,
            trang_thai: this.selectedQuote.status,
            ghi_chu_zalo: this.selectedQuote.zalo_notes,
            shipping_fee: this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0),
            freight_collect: this.selectedQuote.freight_collect ? 1 : 0,
            deposit_required: this.selectedQuote.deposit_required || 0,
            chi_tiet: this.selectedQuote.items
        };
        await this.saveQuoteMutation.mutateAsync({ id: this.selectedQuote.id, payload });
        this.closeModal('quoteModal');
        Swal.fire({
          icon: 'success',
          title: 'Thành công',
          text: 'Cập nhật báo giá thành công!',
          timer: 1800,
          showConfirmButton: false
        });
      } catch (error) {
        console.error("Lỗi cập nhật báo giá:", error);
        const msg = error?.response?.data?.message || 'Lỗi cập nhật báo giá!';
        Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
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
            freight_collect: this.selectedOrder.freight_collect ? 1 : 0
        };
        await this.saveOrderMutation.mutateAsync({ id: this.selectedOrder.id, payload });
        this.closeModal('orderModal');
        Swal.fire({
          icon: 'success',
          title: 'Thành công',
          text: 'Cập nhật đơn hàng thành công!',
          timer: 1800,
          showConfirmButton: false
        });
        localStorage.removeItem('draft_order_' + this.selectedOrder.id);
      } catch (error) {
        console.warn("Lỗi mạng hoặc API, lưu nháp Offline...", error);
        localStorage.setItem('draft_order_' + this.selectedOrder.id, JSON.stringify(this.selectedOrder));
        const msg = error?.response?.data?.message || "Mất mạng hoặc lỗi hệ thống! Đơn hàng được lưu nháp cục bộ.";
        Swal.fire({
          icon: 'warning',
          title: 'Thông báo',
          text: msg
        });
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
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: msg || 'Đã sao chép!',
        showConfirmButton: false,
        timer: 2000
      });
    },

    async loadTransactions() {
      this.isFetchingTransactions = true;
      try {
        const res = await axiosClient.get('/admin/thanh-toan/giao-dich?per_page=50');
        if (res.data && res.data.success) {
          this.transactions = res.data.data.data || [];
        }
      } catch (err) {
        console.error('Lỗi khi tải danh sách giao dịch:', err);
      } finally {
        this.isFetchingTransactions = false;
      }
    },

    async openVietQrModal(order) {
      if (!order) return;
      this.isLoadingQr = true;
      this.adminVietQr = null;
      // eslint-disable-next-line no-undef
      const modalEl = new bootstrap.Modal(document.getElementById('adminVietQrModal'));
      modalEl.show();
      try {
        const res = await axiosClient.get(`/public/vietqr/don-hang/${order.code}`);
        if (res.data && res.data.success) {
          this.adminVietQr = res.data;
        }
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể tạo mã VietQR cho đơn hàng này' });
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
        Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập tên nhà xe / chành xe' });
        return;
      }
      this.isSendingShipping = true;
      try {
        const res = await axiosClient.post(`/admin/thong-bao/gui-chanh-xe/${this.shippingOrder.id}`, this.shippingForm);
        if (res.data && res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Thành Công!',
            text: 'Đã gửi thông báo xuất bến qua Zalo ZNS / SMS Brandname cho chủ tàu và chành xe.'
          });
          // eslint-disable-next-line no-undef
          const modalEl = bootstrap.Modal.getInstance(document.getElementById('adminShippingModal'));
          if (modalEl) modalEl.hide();
        }
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Lỗi', text: err.response?.data?.message || 'Có lỗi xảy ra khi gửi thông báo' });
      } finally {
        this.isSendingShipping = false;
      }
    },

    openSimulateModal(order) {
      const code = order ? order.code : '';
      const remaining = order ? Math.max(0, (this.calculateTotal(order.items) + (order.freight_collect ? 0 : (order.shipping_fee || 0))) - (order.paid_amount || 0)) : 1000000;
      this.simulateForm = {
        reference_code: code,
        amount: remaining > 0 ? remaining : 500000,
        content: `JPM ${code} TEST THANH TOAN`
      };
      // eslint-disable-next-line no-undef
      const modalEl = new bootstrap.Modal(document.getElementById('adminSimulateModal'));
      modalEl.show();
    },

    async submitSimulatePayment() {
      if (!this.simulateForm.reference_code || this.simulateForm.amount <= 0) {
        Swal.fire({ icon: 'warning', title: 'Chú ý', text: 'Vui lòng nhập mã tham chiếu và số tiền hợp lệ' });
        return;
      }
      this.isSimulating = true;
      try {
        const res = await axiosClient.post('/admin/thanh-toan/mo-phong', this.simulateForm);
        if (res.data && res.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Đối Soát Thành Công!',
            text: `Đã đối soát +${this.formatCurrency(this.simulateForm.amount)} vào ${this.simulateForm.reference_code}`
          });
          // eslint-disable-next-line no-undef
          const modalEl = bootstrap.Modal.getInstance(document.getElementById('adminSimulateModal'));
          if (modalEl) modalEl.hide();
          this.queryClient.invalidateQueries({ queryKey: ['don-hang'] });
          this.loadTransactions();
          if (this.selectedOrder && this.selectedOrder.code === this.simulateForm.reference_code) {
            this.selectedOrder.paid_amount = (Number(this.selectedOrder.paid_amount) || 0) + Number(this.simulateForm.amount);
          }
        }
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Lỗi', text: err.response?.data?.message || 'Đối soát mô phỏng thất bại' });
      } finally {
        this.isSimulating = false;
      }
    }
  }
};
</script>

<style scoped>
.nav-pills .nav-link.position-relative {
  padding-right: 1.4rem;
}
.tab-badge-top {
  position: absolute;
  top: -7px;
  right: -5px;
  background-color: #ff3e1d;
  color: #ffffff;
  font-size: 0.72rem;
  font-weight: 700;
  line-height: 1.2;
  padding: 2px 6px;
  border-radius: 5px;
  border: 1.5px solid #ffffff;
  box-shadow: 0 2px 4px rgba(255, 62, 29, 0.35);
  z-index: 2;
  white-space: nowrap;
}
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
</style>



