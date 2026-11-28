<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Kinh Doanh & Bán Hàng /</span> Yêu Cầu & Đơn Khách
    </h4>

    <div class="nav-align-top mb-4">
      <!-- SNEAT ADMIN STANDARD NAV PILLS (Tinh gọn, hạn chế icon rườm rà) -->
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
            <span>Đã Báo Giá</span>
            <span v-if="countQuotedQuotes > 0" class="tab-count-badge ms-2">{{ countQuotedQuotes }}</span>
            <span v-if="countReportedDeposits > 0" class="badge bg-primary text-white ms-2" style="font-size: 0.68rem;">
              {{ countReportedDeposits }} báo cọc
            </span>
          </button>
        </li>

        <li class="nav-item">
          <button 
            type="button"
            class="nav-link d-flex align-items-center" 
            :class="{ active: currentTab === 'processing' }"
            @click="switchTab('processing')"
            role="tab"
            title="Đơn hàng đã đặt cọc, đang gom phụ tùng hoặc lên đơn Nhật"
          >
            <span>Đã Cọc & Xử Lý</span>
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
            <span>Tất Cả</span>
          </button>
        </li>
      </ul>

      <!-- MAIN CONTENT AREA (Chuẩn Sneat Admin) -->
      <div>
        <div class="card">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">{{ tabTitle }}</h5>
            <button 
              type="button" 
              class="btn btn-primary shadow-sm d-flex align-items-center"
              @click="openCreateOrderModal"
              title="Lên đơn hàng hoặc tạo báo giá mới cho khách Zalo / tại quầy"
            >
              <i class="bx bx-plus me-1 fs-5"></i>
              <span class="fw-semibold">Lên Đơn Mới (Zalo / Quầy)</span>
            </button>
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
              <thead>
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
                      <span class="fw-bold text-primary">{{ item.code }}</span>
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
                        <span v-if="item.da_bao_chuyen_khoan && Number(item.da_thu || 0) === 0" class="badge badge-custom" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd;">KHÁCH BÁO ĐÃ CỌC</span>
                        <span v-else-if="item.khach_da_chot && item.deposit_required > 0 && Number(item.da_thu || 0) === 0" class="badge badge-custom bg-pastel-warning">ĐÃ CHỐT · CHỜ CỌC</span>
                        <span v-else-if="item.khach_da_chot" class="badge badge-custom bg-pastel-info">KHÁCH ĐÃ CHỐT</span>
                        <span v-else class="badge badge-custom bg-pastel-info">ĐÃ BÁO GIÁ</span>
                      </template>
                      <span v-else-if="item.status === 2" class="badge badge-custom bg-pastel-warning">ĐÃ CỌC & XỬ LÝ</span>
                      <span v-else-if="item.status === 3" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                      <span v-else-if="item.status === 4" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                      <span v-else class="badge badge-custom bg-pastel-secondary">KHÁC</span>
                    </template>
                    <!-- Trạng thái Đơn Hàng -->
                    <template v-else>
                      <template v-if="item.status === 0">
                        <span v-if="item.da_bao_chuyen_khoan && Number(item.paid_amount || 0) === 0" class="badge badge-custom" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd;">KHÁCH BÁO ĐÃ CỌC</span>
                        <span v-else-if="item.deposit_required > 0 && Number(item.paid_amount || 0) === 0" class="badge badge-custom bg-pastel-warning">CHỜ CỌC</span>
                        <span v-else class="badge badge-custom bg-pastel-info">CHỜ DUYỆT</span>
                      </template>
                      <span v-else-if="item.status === 1" class="badge badge-custom bg-pastel-warning">ĐÃ CỌC & XỬ LÝ</span>
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
    <div class="modal fade tx-modal" id="quoteModal" tabindex="-1" aria-hidden="true" ref="quoteModal">
      <div class="modal-dialog modal-xl mt-3 mt-lg-4" style="max-width: 1400px; width: 95%;" role="document">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-white border-bottom px-4 py-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
              <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
                Báo Giá Yêu Cầu - <span class="text-primary font-monospace">{{ selectedQuote?.code }}</span>
              </h4>
              <template v-if="selectedQuote">
                <span v-if="selectedQuote.status === 0" class="badge badge-custom bg-pastel-warning">CHỜ BÁO GIÁ</span>
                <span v-else-if="selectedQuote.status === 1 && selectedQuote.deposit_required > 0 && Number(selectedQuote.da_thu || 0) === 0" class="badge badge-custom bg-pastel-warning">CHỜ CỌC</span>
                <span v-else-if="selectedQuote.status === 1" class="badge badge-custom bg-pastel-info">ĐÃ BÁO GIÁ</span>
                <span v-else-if="selectedQuote.status === 2" class="badge badge-custom bg-pastel-warning">ĐÃ CỌC &amp; XỬ LÝ</span>
                <span v-else-if="selectedQuote.status === 3" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                <span v-else-if="selectedQuote.status === 4" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
              </template>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="quoteModalCloseBtn"></button>
          </div>
          <form @submit.prevent="saveQuote">
            <div class="modal-body tx-modal-body" v-if="selectedQuote">
              <div
                v-if="selectedQuote.status === 1 && selectedQuote.da_bao_chuyen_khoan && Number(selectedQuote.da_thu || 0) <= 0"
                class="alert alert-primary d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 py-2 px-3"
              >
                <div class="d-flex align-items-center gap-2 small">
                  <i class="bx bx-transfer-alt fs-5"></i>
                  <span>
                    <strong>Khách hàng đã báo chuyển khoản cọc</strong>
                    <template v-if="selectedQuote.thoi_gian_bao_coc"> lúc {{ formatReportDateTime(selectedQuote.thoi_gian_bao_coc) }}</template>
                    <template v-if="selectedQuote.receipt_image"> (đã đính kèm ảnh biên lai ở cột Biên Lai Cọc)</template>.
                    Vui lòng kiểm tra tài khoản Sacombank rồi bấm <strong>Đã Cọc &amp; Lên Đơn</strong>.
                  </span>
                </div>
                <button
                  v-if="!isQuoteReadOnly"
                  type="button"
                  class="btn btn-sm btn-outline-danger bg-white fw-semibold"
                  @click="rejectDepositReport(selectedQuote, 'quote')"
                >
                  Chưa nhận được tiền
                </button>
              </div>
              <div
                v-else-if="selectedQuote.status === 1 && selectedQuote.khach_da_chot && Number(selectedQuote.da_thu || 0) <= 0"
                class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2 px-3 small"
              >
                <i class="bx bx-check-circle fs-5"></i>
                <span>
                  <strong>Khách đã đồng ý chốt đơn</strong>
                  <template v-if="selectedQuote.thoi_gian_khach_chot"> lúc {{ formatReportDateTime(selectedQuote.thoi_gian_khach_chot) }}</template>
                  — đang chờ khách quét QR chuyển khoản đặt cọc.
                </span>
              </div>
              <div class="row g-3 align-items-stretch">
                <!-- Cột 1 (Trái): Card Thông tin Khách Hàng & Ghi chú -->
                <div class="col-lg-3 col-12">
                  <div class="tx-card">
                    <div class="tx-card-header">
                      <span><i class="bx bx-user text-primary me-1 align-middle"></i> Thông Tin Khách Hàng</span>
                    </div>
                    <div class="tx-card-body">
                      <div class="pb-3 mb-3 border-bottom">
                        <div class="fw-bold mb-1" style="font-size: 0.98rem; color: #0f172a;">{{ selectedQuote.customer_name }}</div>
                        <div class="small mb-1" style="color: #334155;">
                          <i class="bx bx-phone me-1 text-primary"></i>
                          <span class="font-monospace fw-semibold">{{ selectedQuote.customer_phone || 'Chưa có SĐT' }}</span>
                        </div>
                        <div class="small text-break" style="color: #475569;" v-if="selectedQuote.customer_email">
                          <i class="bx bx-envelope me-1 text-primary"></i>{{ selectedQuote.customer_email }}
                        </div>
                        <div class="mt-3">
                          <label class="form-label text-dark small mb-1 fw-bold">Địa Chỉ Giao Hàng <span class="text-danger">*</span></label>
                          <textarea 
                            v-if="!isQuoteReadOnly"
                            class="form-control form-control-sm" 
                            rows="2"
                            style="resize: none;"
                            v-model="selectedQuote.address" 
                            placeholder="Số nhà, đường, bến tàu, chành xe..."
                          ></textarea>
                          <div v-else class="tx-readonly-note py-2 px-2 small">
                            {{ selectedQuote.address || 'Chưa cung cấp địa chỉ' }}
                          </div>
                        </div>
                      </div>

                      <!-- Thông Tin Vận Chuyển -->
                      <div class="mb-3 p-2 bg-light border rounded" v-if="selectedQuote.status === 2">
                        <div class="fw-bold mb-2 text-uppercase" style="font-size: 0.72rem; color: #334155;">
                          <i class="bx bx-package me-1 text-primary"></i>Thông Tin Vận Chuyển
                        </div>
                        <div class="mb-2">
                          <label class="form-label text-dark small mb-1 fw-semibold">Đơn vị vận chuyển <span class="text-danger">*</span></label>
                          <input 
                            type="text" 
                            class="form-control form-control-sm" 
                            v-model="selectedQuote.ten_don_vi_van_chuyen" 
                            placeholder="ViettelPost, GHTK, Chành xe..."
                          />
                        </div>
                        <div class="mb-1">
                          <label class="form-label text-dark small mb-1 fw-semibold">Mã vận đơn <span class="text-danger">*</span></label>
                          <input 
                            type="text" 
                            class="form-control form-control-sm font-monospace fw-bold" 
                            v-model="selectedQuote.ma_van_don" 
                            placeholder="Nhập mã vận đơn..."
                          />
                        </div>
                      </div>

                      <div class="mb-3 p-2 bg-light border rounded" v-else-if="selectedQuote.status >= 3 && (selectedQuote.ten_don_vi_van_chuyen || selectedQuote.ma_van_don)">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                          <span class="fw-bold text-uppercase" style="font-size: 0.72rem; color: #334155;">Vận Chuyển</span>
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
                            <span class="fw-bold text-primary font-monospace ms-1">{{ selectedQuote.ma_van_don || 'Chưa có mã' }}</span>
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

                      <div class="mb-3 flex-grow-1 d-flex flex-column">
                        <label class="form-label text-dark small mb-1 fw-bold">Ghi Chú Của Khách Hàng <span class="text-muted fw-normal">(Chỉ đọc)</span></label>
                        <div class="tx-readonly-note flex-grow-1 custom-scroll overflow-auto" style="min-height: 110px; max-height: 220px;">
                          {{ selectedQuote.customer_notes || 'Không có ghi chú từ khách hàng.' }}
                        </div>
                      </div>

                      <div class="mt-auto">
                        <label class="form-label text-dark small mb-1 fw-bold">Ghi Chú Zalo (Nội Bộ)</label>
                        <textarea v-if="!isQuoteReadOnly" class="form-control form-control-sm" rows="2" style="resize: none;" v-model="selectedQuote.zalo_notes" placeholder="Ghi chú kết quả thương lượng Zalo..."></textarea>
                        <div v-else class="tx-readonly-note py-2 px-2 small">{{ selectedQuote.zalo_notes || 'N/A' }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cột 2 (Giữa): Card Danh sách Phụ Tùng -->
                <div class="col-lg-6 col-12">
                  <div class="tx-card">
                    <div class="tx-card-header">
                      <span><i class="bx bx-list-ul text-primary me-1 align-middle"></i> Chi Tiết Sản Phẩm Cần Báo Giá</span>
                      <span class="badge bg-label-primary">{{ selectedQuote.items?.length || 0 }} dòng</span>
                    </div>
                    <div class="tx-card-body p-0">
                      <div class="table-responsive custom-scroll flex-grow-1" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-hover mb-0 tx-items-table">
                          <thead class="sticky-top">
                            <tr>
                              <th class="text-center" style="width: 7%">STT</th>
                              <th style="width: 39%">Tên Phụ Tùng &amp; Mã OEM</th>
                              <th class="text-center" style="width: 10%">SL</th>
                              <th class="text-end" style="width: 24%">Đơn Giá (VNĐ)</th>
                              <th class="text-end" style="width: 20%">Thành Tiền</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-if="!selectedQuote.items || selectedQuote.items.length === 0">
                              <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bx bx-folder-open text-secondary fs-2 d-block mb-1"></i>
                                Không có dữ liệu
                              </td>
                            </tr>
                            <tr v-for="(detail, idx) in selectedQuote.items" :key="idx">
                              <td class="text-center align-middle fw-semibold text-muted">{{ idx + 1 }}</td>
                              <td class="align-middle">
                                <div class="fw-semibold mb-1" style="font-size: 0.88rem; color: #0f172a; line-height: 1.38;">{{ detail.part_name }}</div>
                                <div style="font-size: 0.79rem; line-height: 1.35;">
                                  <span style="color: #94a3b8;">Mã:</span> <span class="font-monospace fw-bold" style="color: #0284c7;">{{ detail.part_code || 'N/A' }}</span>
                                </div>
                                <div v-if="detail.model_may" style="font-size: 0.73rem; line-height: 1.35; margin-top: 2px;">
                                  <span style="color: #94a3b8;">Model:</span> <span style="color: #64748b; font-weight: 500;">{{ detail.model_may }}</span>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <span class="fw-bold text-dark">{{ detail.qty }}</span>
                              </td>
                              <td class="align-middle" style="min-width: 150px;">
                                <input 
                                  v-if="!isQuoteReadOnly" 
                                  type="number" 
                                  class="form-control form-control-sm text-end fw-bold" 
                                  :value="detail.price ? detail.price : ''" 
                                  @input="detail.price = $event.target.value === '' ? 0 : Number($event.target.value)" 
                                  min="0"
                                  placeholder="0" 
                                />
                                <span v-else class="fw-bold text-dark d-block text-end">{{ formatCurrency(detail.price || 0) }}</span>
                              </td>
                              <td class="text-end fw-bold text-danger align-middle">
                                {{ formatCurrency(detail.qty * (detail.price || 0)) }}
                                <div v-if="detail.qty > 1" class="text-muted fw-semibold" style="font-size: 0.74rem;">
                                  x{{ detail.qty }} cái
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cột 3 (Phải): Card Tổng Hợp Chi Phí & Thanh Toán -->
                <div class="col-lg-3 col-12">
                  <div class="tx-card">
                    <div class="tx-card-header">
                      <span><i class="bx bx-receipt text-primary me-1 align-middle"></i> Tổng Hợp Chi Phí</span>
                    </div>
                    <div class="tx-card-body justify-content-between">
                      <div>
                        <!-- Tạm Tính -->
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                          <span class="text-dark fw-semibold" style="font-size: 0.88rem;">Tạm Tính</span>
                          <span class="fw-bold text-dark fs-6">{{ formatCurrency(calculateTotal(selectedQuote.items)) }}</span>
                        </div>

                        <!-- Phí Vận Chuyển -->
                        <div class="mb-3 pb-3 border-bottom">
                          <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                            <label class="text-dark fw-semibold mb-0 text-nowrap" style="font-size: 0.88rem;">Phí Vận Chuyển</label>
                            <div style="width: 145px;" v-if="!isQuoteReadOnly">
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
                          <div class="d-flex justify-content-between align-items-center" v-if="!isQuoteReadOnly">
                            <div class="form-check form-switch mb-0">
                              <input class="form-check-input" type="checkbox" id="freightCollectQuote" v-model="selectedQuote.freight_collect" @change="selectedQuote.freight_collect ? selectedQuote.shipping_fee = 0 : null">
                              <label class="form-check-label text-dark fw-medium" for="freightCollectQuote" style="font-size: 0.8rem;">Khách trả cước</label>
                            </div>
                            <span v-if="!selectedQuote.freight_collect && selectedQuote.shipping_fee > 0" class="text-danger fw-semibold" style="font-size: 0.76rem;">
                              {{ formatCurrency(selectedQuote.shipping_fee) }}
                            </span>
                          </div>
                        </div>

                        <!-- Yêu Cầu Cọc -->
                        <div class="mb-3 pb-3 border-bottom">
                          <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                            <label class="text-dark fw-semibold mb-0 text-nowrap" style="font-size: 0.88rem;">Yêu Cầu Cọc</label>
                            <div style="width: 145px;" v-if="!isQuoteReadOnly">
                              <input 
                                type="number" 
                                class="form-control form-control-sm text-end text-danger fw-bold" 
                                :class="{ 'is-invalid': isQuoteDepositExceeded }"
                                :value="selectedQuote.deposit_required ? selectedQuote.deposit_required : ''" 
                                @input="handleDepositInput(selectedQuote, 'deposit_required', $event, quoteGrandTotal)" 
                                min="0"
                                :max="quoteGrandTotal"
                                placeholder="0" 
                              >
                            </div>
                            <span v-else class="fw-bold text-danger fs-6">{{ formatCurrency(selectedQuote.deposit_required || 0) }}</span>
                          </div>
                          <div v-if="!isQuoteReadOnly && isQuoteDepositExceeded" class="text-end text-danger fw-semibold" style="font-size: 0.74rem;">
                            Không được vượt quá tổng đơn ({{ formatCurrency(quoteGrandTotal) }})
                          </div>
                          <div v-else-if="!isQuoteReadOnly && selectedQuote.deposit_required > 0" class="text-end">
                            <span class="text-danger fw-semibold" style="font-size: 0.76rem;">
                              {{ formatCurrency(selectedQuote.deposit_required) }}
                            </span>
                          </div>
                        </div>

                        <!-- Đã Thu (Cọc): chỉ hiển thị khi báo giá đã chốt/đã cọc (chế độ xem) -->
                        <div class="mb-3 pb-3 border-bottom" v-if="selectedQuote && isQuoteReadOnly">
                          <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                            <label class="text-dark fw-semibold mb-0" style="font-size: 0.85rem;">Đã Thu (Cọc)</label>
                            <span class="fw-bold text-danger fs-6">{{ formatCurrency(selectedQuote.da_thu || 0) }}</span>
                          </div>
                        </div>

                        <!-- KHU VỰC BIÊN LAI CHUYỂN TIỀN (2 CỘT: CỌC & THU COD) -->
                        <div class="mt-2 mb-3" v-if="selectedQuote && selectedQuote.status >= 1">
                          <input type="file" ref="quoteReceiptSlot1" class="d-none" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" @change="onFileSelected($event, 'quote', 1)">
                          <input type="file" ref="quoteReceiptSlot2" class="d-none" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" @change="onFileSelected($event, 'quote', 2)">

                          <div class="row g-2">
                            <div class="col-6">
                              <div class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #334155;">
                                Biên Lai Cọc
                              </div>
                              <div 
                                class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                                style="height: 100px; cursor: pointer;"
                                :style="!selectedQuote.receipt_image ? 'border-style: dashed !important; border-color: #94a3b8 !important;' : ''"
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
                                    alt="Biên lai cọc" 
                                    class="rounded" 
                                    style="max-height: 90px; max-width: 100%; object-fit: contain;" 
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
                                  <span style="font-size: 0.68rem;">Biên lai cọc</span>
                                </div>
                              </div>
                            </div>

                            <div class="col-6">
                              <div class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #334155;">
                                Biên Lai Thu COD
                              </div>
                              <div 
                                class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                                style="height: 100px; cursor: pointer;"
                                :style="!selectedQuote.receipt_image_2 ? 'border-style: dashed !important; border-color: #94a3b8 !important;' : ''"
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
                                    alt="Biên lai thu COD" 
                                    class="rounded" 
                                    style="max-height: 90px; max-width: 100%; object-fit: contain;" 
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
                                  <span style="font-size: 0.68rem;">Biên lai thu COD</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Cụm Tổng Cộng & Thu COD ở đáy -->
                      <div class="tx-summary-box mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <span class="fw-bold text-dark" style="font-size: 0.9rem;">Tổng Cộng</span>
                          <span class="fw-bold text-danger fs-5">{{ formatCurrency(calculateTotal(selectedQuote.items) + (selectedQuote.freight_collect ? 0 : (selectedQuote.shipping_fee || 0))) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed #fca5a5;">
                          <span class="text-dark fw-bold" style="font-size: 0.9rem;">Thu COD</span>
                          <span class="fw-bold fs-5" style="color: #0f172a;">
                            {{ formatCurrency(Math.max(0, (calculateTotal(selectedQuote.items) + (selectedQuote.freight_collect ? 0 : (selectedQuote.shipping_fee || 0))) - Math.max(selectedQuote.da_thu || 0, selectedQuote.deposit_required || 0))) }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-3 d-flex align-items-center gap-2">
              <button 
                type="button" 
                class="btn btn-outline-danger px-4 me-auto" 
                v-if="selectedQuote && selectedQuote.status !== 3 && !isQuoteReadOnly" 
                @click.prevent="cancelQuoteFromModal(selectedQuote)"
                title="Hủy yêu cầu báo giá này"
              >
                <i class="bx bx-x-circle me-1"></i> Hủy Đơn
              </button>

              <!-- Tiện ích Copy Link & Mã VietQR -->
              <template v-if="selectedQuote && selectedQuote.status >= 1">
                <button 
                  type="button" 
                  class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" 
                  style="width: 38px; height: 38px; padding: 0;"
                  v-if="selectedQuote.token" 
                  @click.prevent="copyQuoteLink(selectedQuote)"
                  title="Sao chép link báo giá gửi khách"
                >
                  <i class="bx bx-link fs-5"></i>
                </button>

                <button 
                  type="button" 
                  class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" 
                  style="width: 38px; height: 38px; padding: 0;"
                  @click.prevent="openVietQrModal(selectedQuote)"
                  title="Xem mã VietQR chuyển tiền cọc"
                >
                  <i class="bx bx-qr-scan fs-5"></i>
                </button>
              </template>

              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>

              <template v-if="selectedQuote && selectedQuote.status === 0 && !isQuoteReadOnly">
                <button 
                  type="button" 
                  class="btn btn-outline-primary px-4" 
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

              <template v-else-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly">
                <button 
                  type="button" 
                  class="btn btn-outline-primary px-4" 
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
                  Đã Cọc &amp; Lên Đơn
                </button>
              </template>

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
    <div class="modal fade tx-modal" id="orderModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl mt-3 mt-lg-4" style="max-width: 1400px; width: 95%;" role="document">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-white border-bottom px-4 py-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
              <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
                Chi Tiết Đơn Hàng - <span class="text-primary font-monospace">{{ selectedOrder?.code }}</span>
              </h4>
              <template v-if="selectedOrder">
                <span v-if="selectedOrder.status === 0 && selectedOrder.deposit_required > 0 && Number(selectedOrder.paid_amount || 0) === 0" class="badge badge-custom bg-pastel-warning">CHỜ CỌC</span>
                <span v-else-if="selectedOrder.status === 0" class="badge badge-custom bg-pastel-info">CHỜ DUYỆT</span>
                <span v-else-if="selectedOrder.status === 1" class="badge badge-custom bg-pastel-warning">ĐÃ CỌC &amp; XỬ LÝ</span>
                <span v-else-if="selectedOrder.status === 2" class="badge badge-custom bg-pastel-info">ĐANG GIAO HÀNG</span>
                <span v-else-if="selectedOrder.status === 3" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                <span v-else-if="selectedOrder.status === 4" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
              </template>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="orderModalCloseBtn"></button>
          </div>
          <div class="modal-body tx-modal-body" v-if="selectedOrder">
            <div
              v-if="selectedOrder.status === 0 && selectedOrder.da_bao_chuyen_khoan && Number(selectedOrder.paid_amount || 0) <= 0"
              class="alert alert-primary d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 py-2 px-3"
            >
              <div class="d-flex align-items-center gap-2 small">
                <i class="bx bx-transfer-alt fs-5"></i>
                <span>
                  <strong>Khách hàng đã báo chuyển khoản cọc</strong>
                  <template v-if="selectedOrder.thoi_gian_bao_coc"> lúc {{ formatReportDateTime(selectedOrder.thoi_gian_bao_coc) }}</template>
                  <template v-if="selectedOrder.receipt_image"> (đã đính kèm ảnh biên lai ở cột Biên Lai Cọc)</template>.
                  Vui lòng kiểm tra tài khoản Sacombank rồi bấm <strong>Đã Cọc &amp; Lên Đơn</strong>.
                </span>
              </div>
              <button
                v-if="!isOrderInfoReadOnly"
                type="button"
                class="btn btn-sm btn-outline-danger bg-white fw-semibold"
                @click="rejectDepositReport(selectedOrder, 'order')"
              >
                Chưa nhận được tiền
              </button>
            </div>
            <div class="row g-3 align-items-stretch">
              <!-- Cột 1 (Trái): Card Thông tin Khách Hàng -->
              <div class="col-lg-3 col-12">
                <div class="tx-card">
                  <div class="tx-card-header">
                    <span><i class="bx bx-user text-primary me-1 align-middle"></i> Thông Tin Khách Hàng</span>
                  </div>
                  <div class="tx-card-body">
                    <div class="pb-3 mb-3 border-bottom">
                      <div class="fw-bold mb-1" style="font-size: 0.98rem; color: #0f172a;">{{ selectedOrder.customer_name }}</div>
                      <div class="small mb-1" style="color: #334155;">
                        <i class="bx bx-phone me-1 text-primary"></i>
                        <span class="font-monospace fw-semibold">{{ selectedOrder.customer_phone || 'Chưa có SĐT' }}</span>
                      </div>
                      <div class="small text-break" style="color: #475569;" v-if="selectedOrder.customer_email">
                        <i class="bx bx-envelope me-1 text-primary"></i>{{ selectedOrder.customer_email }}
                      </div>
                      <div class="mt-3">
                        <label class="form-label text-dark small mb-1 fw-bold">Địa Chỉ Giao Hàng <span class="text-danger">*</span></label>
                        <textarea 
                          v-if="!isOrderInfoReadOnly"
                          class="form-control form-control-sm" 
                          rows="2"
                          style="resize: none;"
                          v-model="selectedOrder.address" 
                          placeholder="Số nhà, đường, phường/xã, bến tàu, chành xe..."
                        ></textarea>
                        <div v-else class="tx-readonly-note py-2 px-2 small">
                          {{ selectedOrder.address || 'Chưa có địa chỉ' }}
                        </div>
                      </div>
                    </div>

                    <!-- Thông Tin Vận Chuyển -->
                    <div class="mb-3 p-2 bg-light border rounded" v-if="selectedOrder.status === 1">
                      <div class="fw-bold mb-2 text-uppercase" style="font-size: 0.72rem; color: #334155;">
                        <i class="bx bx-package me-1 text-primary"></i>Thông Tin Vận Chuyển
                      </div>
                      <div class="mb-2">
                        <label class="form-label text-dark small mb-1 fw-semibold">Đơn vị vận chuyển <span class="text-danger">*</span></label>
                        <input 
                          type="text" 
                          class="form-control form-control-sm" 
                          v-model="selectedOrder.ten_don_vi_van_chuyen" 
                          placeholder="ViettelPost, GHTK, Chành xe..."
                        />
                      </div>
                      <div class="mb-1">
                        <label class="form-label text-dark small mb-1 fw-semibold">Mã vận đơn <span class="text-danger">*</span></label>
                        <input 
                          type="text" 
                          class="form-control form-control-sm font-monospace fw-bold" 
                          v-model="selectedOrder.ma_van_don" 
                          placeholder="Nhập mã vận đơn..."
                        />
                      </div>
                    </div>

                    <div class="mb-3 p-2 bg-light border rounded" v-else-if="selectedOrder.status >= 2 || selectedOrder.ten_don_vi_van_chuyen || selectedOrder.ma_van_don">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-uppercase" style="font-size: 0.72rem; color: #334155;">Vận Chuyển</span>
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
                          <span class="fw-bold text-primary font-monospace ms-1">{{ selectedOrder.ma_van_don || 'Chưa có mã' }}</span>
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

                    <!-- Ghi Chú Khách Hàng -->
                    <div class="flex-grow-1 d-flex flex-column">
                      <label class="form-label text-dark small mb-1 fw-bold">Ghi Chú Của Khách Hàng <span class="text-muted fw-normal">(Chỉ đọc)</span></label>
                      <div class="tx-readonly-note flex-grow-1 custom-scroll overflow-auto" style="min-height: 120px; max-height: 240px;">
                        {{ selectedOrder.customer_notes || 'Không có ghi chú.' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cột 2 (Giữa): Card Sản Phẩm Trong Đơn -->
              <div class="col-lg-6 col-12">
                <div class="tx-card">
                  <div class="tx-card-header">
                    <span><i class="bx bx-list-ul text-primary me-1 align-middle"></i> Sản Phẩm Trong Đơn</span>
                    <span class="badge bg-label-primary">{{ selectedOrder.items?.length || 0 }} dòng</span>
                  </div>
                  <div class="tx-card-body p-0">
                    <div class="table-responsive custom-scroll flex-grow-1" style="max-height: 520px; overflow-y: auto;">
                      <table class="table table-hover mb-0 tx-items-table">
                        <thead class="sticky-top">
                          <tr>
                            <th class="text-center" style="width: 7%">STT</th>
                            <th style="width: 40%">Tên Phụ Tùng &amp; Mã OEM</th>
                            <th class="text-center" style="width: 11%">SL</th>
                            <th class="text-end" style="width: 21%">Đơn Giá</th>
                            <th class="text-end" style="width: 21%">Thành Tiền</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-if="!selectedOrder.items || selectedOrder.items.length === 0">
                            <td colspan="5" class="text-center py-5 text-muted">
                              <i class="bx bx-folder-open text-secondary fs-2 d-block mb-1"></i>
                              Không có dữ liệu
                            </td>
                          </tr>
                          <tr v-for="(detail, idx) in selectedOrder.items" :key="idx">
                            <td class="text-center align-middle fw-semibold text-muted">{{ idx + 1 }}</td>
                            <td class="align-middle">
                              <div class="fw-semibold mb-1" style="font-size: 0.88rem; color: #0f172a; line-height: 1.38;">{{ detail.part_name }}</div>
                              <div style="font-size: 0.79rem; line-height: 1.35;">
                                <span style="color: #94a3b8;">Mã:</span> <span class="font-monospace fw-bold" style="color: #0284c7;">{{ detail.part_code || 'N/A' }}</span>
                              </div>
                              <div v-if="detail.model_may" style="font-size: 0.73rem; line-height: 1.35; margin-top: 2px;">
                                <span style="color: #94a3b8;">Model:</span> <span style="color: #64748b; font-weight: 500;">{{ detail.model_may }}</span>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <span class="fw-bold text-dark">{{ detail.qty }}</span>
                            </td>
                            <td class="text-end align-middle fw-semibold text-dark">{{ formatCurrency(detail.price) }}</td>
                            <td class="text-end fw-bold text-danger align-middle">
                              {{ formatCurrency(detail.qty * (detail.price || 0)) }}
                              <div v-if="detail.qty > 1" class="text-muted fw-semibold" style="font-size: 0.74rem;">
                                x{{ detail.qty }} cái
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cột 3 (Phải): Card Chi Phí & Thanh Toán -->
              <div class="col-lg-3 col-12">
                <div class="tx-card">
                  <div class="tx-card-header">
                    <span><i class="bx bx-receipt text-primary me-1 align-middle"></i> Tổng Hợp Chi Phí</span>
                  </div>
                  <div class="tx-card-body justify-content-between">
                    <div>
                      <!-- Tạm Tính -->
                      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-dark fw-semibold" style="font-size: 0.88rem;">Tạm Tính</span>
                        <span class="fw-bold text-dark fs-6">{{ formatCurrency(calculateTotal(selectedOrder.items)) }}</span>
                      </div>

                      <!-- Phí Vận Chuyển -->
                      <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                          <label class="text-dark fw-semibold mb-0 text-nowrap" style="font-size: 0.88rem;">Phí Vận Chuyển</label>
                          <div style="width: 145px;" v-if="!isOrderInfoReadOnly">
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
                        <div class="d-flex justify-content-between align-items-center" v-if="!isOrderInfoReadOnly">
                          <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="freightCollectOrder" v-model="selectedOrder.freight_collect" @change="selectedOrder.freight_collect ? selectedOrder.shipping_fee = 0 : null">
                            <label class="form-check-label text-dark fw-medium" for="freightCollectOrder" style="font-size: 0.8rem;">Khách trả cước</label>
                          </div>
                          <span v-if="!selectedOrder.freight_collect && selectedOrder.shipping_fee > 0" class="text-danger fw-semibold" style="font-size: 0.76rem;">
                            {{ formatCurrency(selectedOrder.shipping_fee) }}
                          </span>
                        </div>
                      </div>

                      <!-- Yêu Cầu Cọc -->
                      <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                          <label class="text-dark fw-semibold mb-0 text-nowrap" style="font-size: 0.88rem;">Yêu Cầu Cọc</label>
                          <div style="width: 145px;" v-if="!isOrderInfoReadOnly">
                            <input 
                              type="number" 
                              class="form-control form-control-sm text-end text-danger fw-bold" 
                              :class="{ 'is-invalid': isOrderDepositExceeded }"
                              :value="selectedOrder.deposit_required ? selectedOrder.deposit_required : ''" 
                              @input="handleDepositInput(selectedOrder, 'deposit_required', $event, orderGrandTotal)" 
                              min="0"
                              :max="orderGrandTotal"
                              placeholder="0" 
                            >
                          </div>
                          <span v-else class="fw-bold text-danger fs-6">{{ formatCurrency(selectedOrder.deposit_required || 0) }}</span>
                        </div>
                        <div v-if="!isOrderInfoReadOnly && isOrderDepositExceeded" class="text-end text-danger fw-semibold" style="font-size: 0.74rem;">
                          Không được vượt quá tổng đơn ({{ formatCurrency(orderGrandTotal) }})
                        </div>
                        <div v-else-if="!isOrderInfoReadOnly && selectedOrder.deposit_required > 0" class="text-end">
                          <span class="text-danger fw-semibold" style="font-size: 0.76rem;">
                            {{ formatCurrency(selectedOrder.deposit_required) }}
                          </span>
                        </div>
                      </div>

                      <!-- Đã Thu (Cọc): chỉ hiển thị khi đơn đã xác nhận cọc (status >= 1) -->
                      <div class="mb-3 pb-3 border-bottom" v-if="selectedOrder && isOrderInfoReadOnly">
                        <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                          <label class="text-dark fw-semibold mb-0" style="font-size: 0.85rem;">Đã Thu (Cọc)</label>
                          <span class="fw-bold text-danger fs-6">{{ formatCurrency(selectedOrder.paid_amount || 0) }}</span>
                        </div>
                      </div>

                      <!-- KHU VỰC BIÊN LAI CHUYỂN TIỀN (2 CỘT: CỌC & THU COD) -->
                      <div class="mt-2 mb-3">
                        <input type="file" ref="orderReceiptSlot1" class="d-none" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" @change="onFileSelected($event, 'order', 1)">
                        <input type="file" ref="orderReceiptSlot2" class="d-none" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" @change="onFileSelected($event, 'order', 2)">

                        <div class="row g-2">
                          <div class="col-6">
                            <div class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #334155;">
                              Biên Lai Cọc
                            </div>
                            <div 
                              class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                              style="height: 100px; cursor: pointer;"
                              :style="!selectedOrder.receipt_image ? 'border-style: dashed !important; border-color: #94a3b8 !important;' : ''"
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
                                  alt="Biên lai cọc" 
                                  class="rounded" 
                                  style="max-height: 90px; max-width: 100%; object-fit: contain;" 
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
                                <span style="font-size: 0.68rem;">Biên lai cọc</span>
                              </div>
                            </div>
                          </div>

                          <div class="col-6">
                            <div class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #334155;">
                              Biên Lai Thu COD
                            </div>
                            <div 
                              class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                              style="height: 100px; cursor: pointer;"
                              :style="!selectedOrder.receipt_image_2 ? 'border-style: dashed !important; border-color: #94a3b8 !important;' : ''"
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
                                  alt="Biên lai thu COD" 
                                  class="rounded" 
                                  style="max-height: 90px; max-width: 100%; object-fit: contain;" 
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
                                <span style="font-size: 0.68rem;">Biên lai thu COD</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Cụm Tổng Cộng & Thu COD ở đáy -->
                    <div class="tx-summary-box mt-3">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">Tổng Cộng</span>
                        <span class="fw-bold text-danger fs-5">{{ formatCurrency(calculateTotal(selectedOrder.items) + (selectedOrder.freight_collect ? 0 : (selectedOrder.shipping_fee || 0))) }}</span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed #fca5a5;">
                        <span class="text-dark fw-bold" style="font-size: 0.9rem;">Thu COD</span>
                        <span :class="selectedOrder.status === 3 ? 'fw-bold text-primary fs-5' : 'fw-bold fs-5'" style="color: #0f172a;">
                          {{ formatCurrency(Math.max(0, (calculateTotal(selectedOrder.items) + (selectedOrder.freight_collect ? 0 : (selectedOrder.shipping_fee || 0))) - (selectedOrder.paid_amount || 0))) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-white border-top px-4 py-3 d-flex align-items-center gap-2">
            <button 
              type="button" 
              class="btn btn-outline-danger px-4 me-auto" 
              v-if="selectedOrder && selectedOrder.status < 2" 
              @click.prevent="handleCancel(selectedOrder)"
              title="Hủy đơn hàng này"
            >
              <i class="bx bx-x-circle me-1"></i> Hủy Đơn
            </button>
            <button 
              type="button" 
              class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" 
              style="width: 38px; height: 38px; padding: 0;"
              @click.prevent="copyQuoteLink(selectedOrder)" 
              title="Sao chép link tra cứu đơn gửi khách"
            >
              <i class="bx bx-link fs-5"></i>
            </button>
            <button 
              type="button" 
              class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" 
              style="width: 38px; height: 38px; padding: 0;"
              @click="openVietQrModal(selectedOrder)" 
              title="Xem mã VietQR"
            >
              <i class="bx bx-qr-scan fs-5"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-outline-primary px-4" @click="saveOrder" v-if="!isOrderInfoReadOnly && canEditOrders">
              Lưu
            </button>
            <button 
              type="button" 
              class="btn btn-primary px-4 shadow-sm" 
              v-if="selectedOrder && selectedOrder.status === 0" 
              @click="confirmDepositBtn(selectedOrder)"
              title="Khách đã cọc -> Bắt đầu lên đơn xử lý"
            >
              Đã Cọc &amp; Lên Đơn
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
              Khách Hàng Đã Nhận Hàng &amp; Chuyển Tiền
            </button>
            <button type="button" class="btn btn-outline-secondary px-4" @click="printOrder">
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
                <div v-if="adminVietQr.account_name || adminVietQr.account_no" class="mt-1 lh-sm">
                  <div class="fw-bold text-dark" style="font-size: 0.82rem;">{{ adminVietQr.account_name }}</div>
                  <div class="font-monospace fw-semibold text-secondary" style="font-size: 0.84rem;">{{ adminVietQr.account_no }}</div>
                </div>
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
              Gửi Thông Tin Chành Xe & Xuất Bến (Zalo ZNS / SMS)
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
                  <label class="form-label fw-bold">Biển Số Xe <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="shippingForm.bien_so_xe" placeholder="VD: 68C-123.45">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tên Tài Xế <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="shippingForm.ten_tai_xe" placeholder="VD: Bác tài Nguyễn Văn Hùng">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Số Điện Thoại Tài Xế <span class="text-danger">*</span></label>
                  <input type="tel" class="form-control" v-model="shippingForm.sdt_tai_xe" placeholder="VD: 0918123456">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Số Kiện Hàng <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" v-model.number="shippingForm.so_kien_hang" min="1" required>
                </div>
                <div class="col-md-8">
                  <label class="form-label fw-bold">Địa Điểm / Bến Trả Hàng <span class="text-danger">*</span></label>
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
              Cập Nhật Vận Đơn Giao Hàng
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
                <label class="form-label fw-bold">Đơn Vị Vận Chuyển <span class="text-danger">*</span></label>
                <input type="text" class="form-control" v-model="viettelPostForm.ten_don_vi_van_chuyen" placeholder="VD: ViettelPost, Giao Hàng Nhanh, Chành xe...">
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Mã Vận Đơn / Ghi Chú Chành Xe <span class="text-danger">*</span></label>
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
                  <span class="fw-bold text-primary">{{ formatCurrency(viettelPostOrder.paid_amount || 0) }}</span>
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

    <!-- Modal Lên Đơn Hàng / Báo Giá Mới (Zalo / Tại Quầy) -->
    <div class="modal fade tx-modal" id="createOrderModal" tabindex="-1" aria-hidden="true" ref="createOrderModal">
      <div class="modal-dialog modal-xl mt-3 mt-lg-4 modal-dialog-scrollable" style="max-width: 1400px; width: 95%;" role="document">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-white border-bottom px-4 py-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
              Báo Giá Yêu Cầu - <span class="text-primary">Tạo Mới (Zalo / Quầy)</span>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent>
            <div class="modal-body tx-modal-body">
              <div class="row g-3 align-items-stretch">
                <!-- Cột 1 (Trái): Card Thông tin Khách Hàng -->
                <div class="col-lg-3 col-12">
                  <div class="tx-card">
                    <div class="tx-card-header">
                      <span><i class="bx bx-user text-primary me-1 align-middle"></i> Thông Tin Khách Hàng</span>
                    </div>
                    <div class="tx-card-body">
                  
                  <div class="mb-3">
                    <!-- Chọn nhanh khách hàng cũ -->
                    <div class="mb-2">
                      <v-select
                        v-model="selectedCustomerQuick"
                        :options="customersList"
                        placeholder="Chọn khách cũ (nếu có)..."
                        :searchable="true"
                        :can-clear="true"
                        @change="onSelectCustomerQuick"
                      />
                    </div>

                    <!-- Tên khách -->
                    <div class="mb-2">
                      <label class="form-label text-dark small mb-1 fw-bold">Họ Tên Khách Hàng <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        class="form-control form-control-sm text-dark fw-bold" 
                        placeholder="VD: Anh Đức (Cano Tuần Châu)" 
                        v-model="createOrderForm.khach_hang_ten" 
                        required
                      />
                    </div>

                    <!-- SĐT -->
                    <div class="mb-2">
                      <label class="form-label text-dark small mb-1 fw-bold">Số Điện Thoại <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        class="form-control form-control-sm text-dark" 
                        placeholder="VD: 0945678123" 
                        v-model="createOrderForm.khach_hang_sdt" 
                        required
                      />
                    </div>

                    <!-- Email -->
                    <div class="mb-2">
                      <label class="form-label text-dark small mb-1">Email (nếu có): <span class="text-danger">*</span></label>
                      <input 
                        type="email" 
                        class="form-control form-control-sm text-dark" 
                        placeholder="VD: duc.tuanchau@gmail.com" 
                        v-model="createOrderForm.khach_hang_email" 
                      />
                    </div>
                    
                    <div class="mt-2">
                      <label class="form-label text-dark small mb-1 fw-bold">Địa Chỉ Giao Hàng: <span class="text-danger">*</span></label>
                      <textarea 
                        class="form-control form-control-sm text-dark" 
                        rows="2"
                        v-model="createOrderForm.dia_chi_giao_hang" 
                        placeholder="Số nhà, đường, bến tàu, chành xe..."
                      ></textarea>
                    </div>
                  </div>

                  <!-- Thông Tin Vận Chuyển: Y HỆT quoteModal -->
                  <div class="mb-3 p-2 bg-light border rounded">
                    <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 0.72rem; color: #566a7f; letter-spacing: 0.04em;">
                      Thông Tin Vận Chuyển
                    </h6>
                    <div class="mb-2">
                      <label class="form-label text-dark small mb-1 fw-semibold">Đơn vị vận chuyển: <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        class="form-control form-control-sm text-dark bg-white" 
                        v-model="createOrderForm.ten_don_vi_van_chuyen" 
                        placeholder="ViettelPost, GHTK, Chành xe Tô Châu, Phương Trang..."
                      />
                    </div>
                    <div class="mb-1">
                      <label class="form-label text-dark small mb-1 fw-semibold">Mã vận đơn: <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        class="form-control form-control-sm text-primary fw-bold bg-white" 
                        v-model="createOrderForm.ma_van_don" 
                        placeholder="Nhập mã vận đơn (nếu có)..."
                      />
                    </div>
                  </div>

                  <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 0.75rem; color: #566a7f; letter-spacing: 0.04em;">Ghi Chú (Khách Hàng)</h6>
                  <div class="mb-3">
                    <textarea 
                      class="form-control form-control-sm text-dark" 
                      rows="2"
                      v-model="createOrderForm.ghi_chu_khach_hang" 
                      placeholder="Yêu cầu từ khách hàng..."
                    ></textarea>
                  </div>

                      <div class="mt-auto">
                        <label class="form-label text-dark small mb-1 fw-bold">Ghi Chú Zalo (Nội Bộ)</label>
                        <textarea 
                          class="form-control form-control-sm" 
                          rows="2" 
                          style="resize: none;"
                          v-model="createOrderForm.ghi_chu_zalo" 
                          placeholder="Ghi chú kết quả thương lượng Zalo..."
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cột 2 (Giữa): Card Danh sách Phụ Tùng -->
                <div class="col-lg-6 col-12">
                  <div class="tx-card">
                    <div class="tx-card-header">
                      <span><i class="bx bx-list-ul text-primary me-1 align-middle"></i> Chi Tiết Sản Phẩm Cần Báo Giá</span>
                      <button type="button" class="btn btn-xs btn-outline-primary" @click="addCreateOrderItem">
                        <i class="bx bx-plus me-1"></i>Thêm Dòng
                      </button>
                    </div>
                    <div class="tx-card-body">
                      <!-- Chọn nhanh phụ tùng trong kho -->
                      <div class="mb-3">
                        <v-select
                          v-model="selectedPartQuick"
                          :options="partsList"
                          placeholder="Tìm nhanh phụ tùng trong kho để thêm vào bảng..."
                          :searchable="true"
                          :can-clear="true"
                          @change="onSelectPartQuick"
                        />
                      </div>

                      <!-- Bảng danh sách phụ tùng -->
                      <div class="table-responsive border rounded custom-scroll flex-grow-1" style="max-height: 460px; overflow-y: auto;">
                        <table class="table table-hover table-sm mb-0 tx-items-table">
                          <thead class="sticky-top">
                            <tr>
                              <th class="text-center" style="width: 7%">STT</th>
                              <th style="width: 38%">Tên Phụ Tùng &amp; Mã</th>
                              <th class="text-center" style="width: 12%">SL</th>
                              <th class="text-end" style="width: 23%">Đơn Giá (VNĐ)</th>
                              <th class="text-end" style="width: 20%">Thành Tiền</th>
                            </tr>
                          </thead>
                      <tbody>
                        <tr v-for="(detail, idx) in createOrderForm.chi_tiet" :key="idx">
                          <td class="text-center align-middle">
                            {{ idx + 1 }}
                          </td>
                          <td class="align-middle">
                            <input 
                              type="text" 
                              class="form-control form-control-sm border-0 p-0 fw-bold mb-1" 
                              style="font-size: 0.88rem; color: #1e293b;" 
                              placeholder="Tên phụ tùng *" 
                              v-model="detail.ten_phu_tung" 
                              required
                            />
                            <div class="d-flex flex-wrap align-items-center gap-2">
                              <div class="d-flex align-items-center flex-grow-1">
                                <span class="text-muted me-1" style="font-size: 0.78rem;">Mã:</span>
                                <input 
                                  type="text" 
                                  class="form-control form-control-sm border-0 p-0 font-monospace fw-bold text-primary" 
                                  style="font-size: 0.78rem;" 
                                  placeholder="Nhập mã phụ tùng..." 
                                  v-model="detail.ma_phu_tung" 
                                />
                              </div>
                              <div class="d-flex align-items-center flex-grow-1">
                                <span class="text-muted me-1" style="font-size: 0.78rem;">Model:</span>
                                <input 
                                  type="text" 
                                  class="form-control form-control-sm border-0 p-0 fw-semibold text-dark" 
                                  style="font-size: 0.78rem;" 
                                  placeholder="Model máy (tùy chọn)..." 
                                  v-model="detail.model_may" 
                                />
                              </div>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <input 
                              type="number" 
                              class="form-control form-control-sm text-center fw-bold" 
                              v-model.number="detail.so_luong" 
                              min="1" 
                              required
                            />
                          </td>
                          <td class="align-middle" style="min-width: 140px;">
                            <input 
                              type="number" 
                              class="form-control form-control-sm text-end fw-bold text-dark" 
                              :value="detail.don_gia ? detail.don_gia : ''" 
                              @input="detail.don_gia = $event.target.value === '' ? 0 : Number($event.target.value)" 
                              min="0"
                              placeholder="0" 
                            />
                          </td>
                          <td class="text-end fw-bold text-danger align-middle">
                            {{ formatCurrency(detail.so_luong * (detail.don_gia || 0)) }}
                            <div v-if="detail.so_luong > 1" class="text-dark fw-bold" style="font-size: 0.75rem;">
                              x{{ detail.so_luong }} cái
                            </div>
                            <div class="mt-1" v-if="createOrderForm.chi_tiet.length > 1">
                              <button 
                                type="button" 
                                class="btn btn-xs btn-link p-0 text-danger" 
                                @click="removeCreateOrderItem(idx)" 
                                title="Xóa dòng này"
                              >
                                <i class="bx bx-trash" style="font-size: 13px;"></i> Xóa
                              </button>
                            </div>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                    </div>
                  </div>
                </div>

                <!-- Cột 3 (Phải): Card Tổng Hợp Chi Phí & Thanh Toán -->
                <div class="col-lg-3 col-12">
                  <div class="tx-card">
                    <div class="tx-card-header">
                      <span><i class="bx bx-receipt text-primary me-1 align-middle"></i> Tổng Hợp Chi Phí</span>
                    </div>
                    <div class="tx-card-body justify-content-between">
                      <div>
                        <!-- Tạm Tính -->
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                          <span class="text-dark fw-semibold" style="font-size: 0.88rem;">Tạm Tính</span>
                          <span class="fw-bold text-dark fs-6">{{ formatCurrency(createFormSubtotal) }}</span>
                        </div>

                        <!-- Phí Vận Chuyển -->
                        <div class="mb-3 pb-2 border-bottom">
                          <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                            <label class="text-dark fw-semibold mb-0 text-nowrap" style="font-size: 0.88rem;">Phí Vận Chuyển</label>
                            <div style="width: 145px;">
                              <input 
                                type="number" 
                                class="form-control form-control-sm text-end text-danger fw-bold" 
                                :value="createOrderForm.phi_van_chuyen ? createOrderForm.phi_van_chuyen : ''" 
                                @input="createOrderForm.phi_van_chuyen = $event.target.value === '' ? 0 : Number($event.target.value)" 
                                min="0" 
                                placeholder="0"
                              />
                            </div>
                          </div>
                          <div v-if="createOrderForm.phi_van_chuyen > 0" class="text-end">
                            <span class="text-danger fw-semibold" style="font-size: 0.76rem;">
                              {{ formatCurrency(createOrderForm.phi_van_chuyen) }}
                            </span>
                          </div>
                        </div>

                        <!-- Yêu Cầu Cọc -->
                        <div class="mb-3 pb-2 border-bottom">
                          <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                            <label class="text-dark fw-semibold mb-0 text-nowrap" style="font-size: 0.88rem;">Yêu Cầu Cọc</label>
                            <div style="width: 145px;">
                              <input 
                                type="number" 
                                class="form-control form-control-sm text-end text-danger fw-bold" 
                                :class="{ 'is-invalid': isCreateDepositExceeded }"
                                :value="createOrderForm.tien_coc_yeu_cau ? createOrderForm.tien_coc_yeu_cau : ''" 
                                @input="handleDepositInput(createOrderForm, 'tien_coc_yeu_cau', $event, createFormGrandTotal)" 
                                min="0" 
                                :max="createFormGrandTotal"
                                placeholder="0"
                              />
                            </div>
                          </div>
                          <div v-if="isCreateDepositExceeded" class="text-end text-danger fw-semibold" style="font-size: 0.74rem;">
                            Không được vượt quá tổng đơn ({{ formatCurrency(createFormGrandTotal) }})
                          </div>
                          <div v-else-if="createOrderForm.tien_coc_yeu_cau > 0" class="text-end">
                            <span class="text-danger fw-semibold" style="font-size: 0.76rem;">
                              {{ formatCurrency(createOrderForm.tien_coc_yeu_cau) }}
                            </span>
                          </div>
                        </div>

                    <!-- KHU VỰC BIÊN LAI CHUYỂN TIỀN (2 CỘT: CỌC & THU COD) -->
                    <div class="mt-3 pt-2 border-top mb-3">
                      <input type="file" ref="createReceiptSlot1" class="d-none" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" @change="handleUploadCreateReceipt($event, 'anh_chuyen_khoan')">
                      <input type="file" ref="createReceiptSlot2" class="d-none" accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.svg,.bmp,.avif,.heic,.heif,.tiff,.tif,.ico,.jfif" @change="handleUploadCreateReceipt($event, 'anh_chuyen_khoan_2')">

                      <div class="row g-2">
                        <!-- Biên lai cọc -->
                        <div class="col-6">
                          <div class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #334155;">
                            Biên Lai Cọc
                          </div>
                          <div 
                            class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                            style="height: 105px; cursor: pointer;"
                            :style="!createOrderForm.anh_chuyen_khoan ? 'border-style: dashed !important; border-color: #94a3b8 !important;' : ''"
                            @click="!createOrderForm.anh_chuyen_khoan ? $refs.createReceiptSlot1.click() : null"
                            tabindex="0"
                            title="Nhấp để tải ảnh"
                          >
                            <template v-if="createOrderForm.anh_chuyen_khoan">
                              <img 
                                :src="getImageUrl(createOrderForm.anh_chuyen_khoan)" 
                                alt="Biên lai cọc" 
                                class="rounded" 
                                style="max-height: 95px; max-width: 100%; object-fit: contain;" 
                                @click.stop="openReceiptViewer(createOrderForm.anh_chuyen_khoan)" 
                                title="Nhấp để xem phóng to"
                              />
                              <button 
                                type="button" 
                                class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm border" 
                                @click.stop="createOrderForm.anh_chuyen_khoan = ''" 
                                style="width: 24px; height: 24px; padding: 0; line-height: 1;" 
                                title="Xóa ảnh"
                              >
                                <i class="bx bx-trash" style="font-size: 13px;"></i>
                              </button>
                            </template>
                            <div v-else class="text-center text-muted">
                              <i class="bx bx-image-add fs-3 d-block text-secondary mb-1"></i>
                              <span style="font-size: 0.68rem;">Biên lai cọc</span>
                            </div>
                          </div>
                        </div>

                        <!-- Biên lai thu COD -->
                        <div class="col-6">
                          <div class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #334155;">
                            Biên Lai Thu COD
                          </div>
                          <div 
                            class="d-flex flex-column justify-content-center align-items-center border rounded bg-light position-relative p-1" 
                            style="height: 105px; cursor: pointer;"
                            :style="!createOrderForm.anh_chuyen_khoan_2 ? 'border-style: dashed !important; border-color: #94a3b8 !important;' : ''"
                            @click="!createOrderForm.anh_chuyen_khoan_2 ? $refs.createReceiptSlot2.click() : null"
                            tabindex="0"
                            title="Nhấp để tải ảnh"
                          >
                            <template v-if="createOrderForm.anh_chuyen_khoan_2">
                              <img 
                                :src="getImageUrl(createOrderForm.anh_chuyen_khoan_2)" 
                                alt="Biên lai thu COD" 
                                class="rounded" 
                                style="max-height: 95px; max-width: 100%; object-fit: contain;" 
                                @click.stop="openReceiptViewer(createOrderForm.anh_chuyen_khoan_2)" 
                                title="Nhấp để xem phóng to"
                              />
                              <button 
                                type="button" 
                                class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm border" 
                                @click.stop="createOrderForm.anh_chuyen_khoan_2 = ''" 
                                style="width: 24px; height: 24px; padding: 0; line-height: 1;" 
                                title="Xóa ảnh"
                              >
                                <i class="bx bx-trash" style="font-size: 13px;"></i>
                              </button>
                            </template>
                            <div v-else class="text-center text-muted">
                              <i class="bx bx-image-add fs-3 d-block text-secondary mb-1"></i>
                              <span style="font-size: 0.68rem;">Biên lai thu COD</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                      <!-- Cụm Tổng Cộng & Thu COD ở đáy -->
                      <div class="tx-summary-box mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <span class="fw-bold text-dark" style="font-size: 0.9rem;">Tổng Cộng</span>
                          <span class="fw-bold text-danger fs-5">{{ formatCurrency(createFormGrandTotal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed #fca5a5;">
                          <span class="text-dark fw-bold" style="font-size: 0.9rem;">Thu COD</span>
                          <span class="fw-bold fs-5" style="color: #0f172a;">
                            {{ formatCurrency(createFormRemainingCod) }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-end align-items-center gap-2">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                Đóng
              </button>
              <button 
                type="button" 
                class="btn btn-outline-primary px-4 shadow-sm"
                :disabled="isSubmittingCreateOrder"
                @click.prevent="submitCreateOrder('quote')"
              >
                Lưu Báo Giá (Gửi Zalo)
              </button>
              <button 
                type="button" 
                class="btn btn-primary px-4 shadow-sm"
                :disabled="isSubmittingCreateOrder"
                @click.prevent="submitCreateOrder('deposit')"
              >
                <span v-if="isSubmittingCreateOrder" class="spinner-border spinner-border-sm me-1" role="status"></span>
                Lên Đơn Trực Tiếp (Đã Cọc & Xử Lý)
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
import { createToaster } from "@meforma/vue-toaster";
import Swal from 'sweetalert2';
import { compressReceipt, formatBytes } from '../../../utils/imageOptimizer';

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
      uploadingKey: '',
      // Lên đơn thủ công (Zalo / Tại quầy)
      isSubmittingCreateOrder: false,
      selectedCustomerQuick: null,
      selectedPartQuick: null,
      customersList: [],
      partsList: [],
      createOrderForm: {
        khach_hang_ten: '',
        khach_hang_sdt: '',
        khach_hang_email: '',
        dia_chi_giao_hang: '',
        ghi_chu_khach_hang: '',
        ghi_chu_zalo: 'Khách chốt qua Zalo',
        ten_don_vi_van_chuyen: '',
        ma_van_don: '',
        phi_van_chuyen: 0,
        tien_coc_yeu_cau: 0,
        da_thu: 0,
        anh_chuyen_khoan: '',
        anh_chuyen_khoan_2: '',
        chi_tiet: [
          { ten_phu_tung: '', ma_phu_tung: '', model_may: '', so_luong: 1, don_gia: 0 }
        ]
      }
    };
  },
  computed: {
    createFormSubtotal() {
      return (this.createOrderForm?.chi_tiet || []).reduce((sum, item) => {
        return sum + (Number(item.so_luong || 1) * Number(item.don_gia || 0));
      }, 0);
    },
    createFormGrandTotal() {
      return this.createFormSubtotal + Number(this.createOrderForm?.phi_van_chuyen || 0);
    },
    quoteGrandTotal() {
      if (!this.selectedQuote) return 0;
      const itemsTotal = this.calculateTotal(this.selectedQuote.items);
      const ship = this.selectedQuote.freight_collect ? 0 : (Number(this.selectedQuote.shipping_fee) || 0);
      return itemsTotal + ship;
    },
    orderGrandTotal() {
      if (!this.selectedOrder) return 0;
      const itemsTotal = this.calculateTotal(this.selectedOrder.items);
      const ship = this.selectedOrder.freight_collect ? 0 : (Number(this.selectedOrder.shipping_fee) || 0);
      return itemsTotal + ship;
    },
    isQuoteDepositExceeded() {
      return Boolean(this.selectedQuote) && Number(this.selectedQuote.deposit_required || 0) > this.quoteGrandTotal;
    },
    isOrderDepositExceeded() {
      return Boolean(this.selectedOrder) && Number(this.selectedOrder.deposit_required || 0) > this.orderGrandTotal;
    },
    isCreateDepositExceeded() {
      return Boolean(this.createOrderForm) && Number(this.createOrderForm.tien_coc_yeu_cau || 0) > this.createFormGrandTotal;
    },
    createFormRemainingCod() {
      const rem = this.createFormGrandTotal - Number(this.createOrderForm?.da_thu || 0);
      return rem > 0 ? rem : 0;
    },
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
      return list.sort((a, b) => {
        const aRep = a.da_bao_chuyen_khoan && Number((a._type === 'quote' ? a.da_thu : a.paid_amount) || 0) <= 0 ? 1 : 0;
        const bRep = b.da_bao_chuyen_khoan && Number((b._type === 'quote' ? b.da_thu : b.paid_amount) || 0) <= 0 ? 1 : 0;
        if (aRep !== bRep) return bRep - aRep;
        return b.id - a.id;
      });
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
      return list.sort((a, b) => {
        const aRep = a.da_bao_chuyen_khoan && Number((a._type === 'quote' ? a.da_thu : a.paid_amount) || 0) <= 0 ? 1 : 0;
        const bRep = b.da_bao_chuyen_khoan && Number((b._type === 'quote' ? b.da_thu : b.paid_amount) || 0) <= 0 ? 1 : 0;
        if (aRep !== bRep) return bRep - aRep;
        return b.id - a.id;
      });
    },
    shippingOrders() {
      const list = [];
      this.orders.filter(o => o.status === 2).forEach(o => {
        list.push({ ...o, _type: 'order' });
      });
      return list.sort((a, b) => {
        const aRep = a.da_bao_chuyen_khoan && Number((a._type === 'quote' ? a.da_thu : a.paid_amount) || 0) <= 0 ? 1 : 0;
        const bRep = b.da_bao_chuyen_khoan && Number((b._type === 'quote' ? b.da_thu : b.paid_amount) || 0) <= 0 ? 1 : 0;
        if (aRep !== bRep) return bRep - aRep;
        return b.id - a.id;
      });
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
      return list.sort((a, b) => {
        const aRep = a.da_bao_chuyen_khoan && Number((a._type === 'quote' ? a.da_thu : a.paid_amount) || 0) <= 0 ? 1 : 0;
        const bRep = b.da_bao_chuyen_khoan && Number((b._type === 'quote' ? b.da_thu : b.paid_amount) || 0) <= 0 ? 1 : 0;
        if (aRep !== bRep) return bRep - aRep;
        return b.id - a.id;
      });
    },
    cancelledItems() {
      const list = [];
      this.quotes.filter(q => q.status === 3).forEach(q => list.push({ ...q, _type: 'quote' }));
      this.orders.filter(o => o.status === 4).forEach(o => list.push({ ...o, _type: 'order' }));
      return list.sort((a, b) => {
        const aRep = a.da_bao_chuyen_khoan && Number((a._type === 'quote' ? a.da_thu : a.paid_amount) || 0) <= 0 ? 1 : 0;
        const bRep = b.da_bao_chuyen_khoan && Number((b._type === 'quote' ? b.da_thu : b.paid_amount) || 0) <= 0 ? 1 : 0;
        if (aRep !== bRep) return bRep - aRep;
        return b.id - a.id;
      });
    },
    allTransactionsList() {
      const list = [];
      this.quotes.forEach(q => list.push({ ...q, _type: 'quote' }));
      this.orders.forEach(o => list.push({ ...o, _type: 'order' }));
      return list.sort((a, b) => {
        const aRep = a.da_bao_chuyen_khoan && Number((a._type === 'quote' ? a.da_thu : a.paid_amount) || 0) <= 0 ? 1 : 0;
        const bRep = b.da_bao_chuyen_khoan && Number((b._type === 'quote' ? b.da_thu : b.paid_amount) || 0) <= 0 ? 1 : 0;
        if (aRep !== bRep) return bRep - aRep;
        return b.id - a.id;
      });
    },

    // COUNTS
    countPendingQuotes() {
      return this.pendingQuotes.length;
    },
    countQuotedQuotes() {
      return this.quotedQuotes.length;
    },
    countReportedDeposits() {
      return this.quotedQuotes.filter(
        item => item.da_bao_chuyen_khoan && Number((item._type === 'quote' ? item.da_thu : item.paid_amount) || 0) <= 0
      ).length;
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
        case 'processing': return 'Đơn Hàng Đã Cọc & Xử Lý';
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
          khach_da_chot: Boolean(item.khach_da_chot),
          thoi_gian_khach_chot: item.thoi_gian_khach_chot || null,
          da_bao_chuyen_khoan: Boolean(item.da_bao_chuyen_khoan),
          thoi_gian_bao_coc: item.thoi_gian_bao_coc || null,
          items: (item.chi_tiet || []).map(i => ({
            id: i.id,
            part_name: i.ten_phu_tung || '',
            part_code: i.ma_phu_tung || '',
            model_may: i.model_may || '',
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
          khach_da_chot: Boolean(item.khach_da_chot),
          thoi_gian_khach_chot: item.thoi_gian_khach_chot || null,
          da_bao_chuyen_khoan: Boolean(item.da_bao_chuyen_khoan),
          thoi_gian_bao_coc: item.thoi_gian_bao_coc || null,
          items: (item.chi_tiet || []).map(i => ({
            id: i.id,
            part_name: i.ten_phu_tung || (i.phu_tung ? i.phu_tung.ten_phu_tung : (i.san_pham ? i.san_pham.ten_san_pham : 'Sản phẩm ID: ' + i.id_san_pham)),
            part_code: i.ma_phu_tung || (i.phu_tung ? i.phu_tung.ma_phu_tung : (i.san_pham ? i.san_pham.ma_san_pham : (i.loai_san_pham === 'phu_tung' ? 'PT-' + i.id_san_pham : 'SP-' + i.id_san_pham))),
            model_may: i.model_may || '',
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
    openCreateOrderModal() {
      this.resetCreateOrderForm();
      this.fetchCustomersAndParts();
      // eslint-disable-next-line no-undef
      const modalEl = document.getElementById('createOrderModal');
      if (modalEl) {
        // eslint-disable-next-line no-undef
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
      }
    },
    resetCreateOrderForm() {
      this.selectedCustomerQuick = null;
      this.selectedPartQuick = null;
      this.createOrderForm = {
        khach_hang_ten: '',
        khach_hang_sdt: '',
        khach_hang_email: '',
        dia_chi_giao_hang: '',
        ghi_chu_khach_hang: '',
        ghi_chu_zalo: 'Khách chốt qua Zalo',
        ten_don_vi_van_chuyen: '',
        ma_van_don: '',
        phi_van_chuyen: 0,
        tien_coc_yeu_cau: 0,
        da_thu: 0,
        anh_chuyen_khoan: '',
        anh_chuyen_khoan_2: '',
        chi_tiet: [
          { ten_phu_tung: '', ma_phu_tung: '', model_may: '', so_luong: 1, don_gia: 0 }
        ]
      };
    },
    async fetchCustomersAndParts() {
      if (this.customersList.length === 0) {
        try {
          const res = await axiosClient.get('/admin/khach-hang');
          const data = res.data?.data || res.data || [];
          this.customersList = (Array.isArray(data) ? data : []).map(c => ({
            value: c.id,
            label: `${c.ho_ten || c.name || 'Khách'} - ${c.so_dien_thoai || c.phone || 'Không SĐT'}`,
            name: c.ho_ten || c.name,
            phone: c.so_dien_thoai || c.phone,
            email: c.email || c.user?.email || '',
            address: c.dia_chi || c.dia_chi_cu_the || ''
          }));
        } catch (e) {
          console.error("Lỗi tải danh sách khách hàng:", e);
        }
      }
      if (this.partsList.length === 0) {
        try {
          const res = await axiosClient.get('/admin/phu-tung');
          const data = res.data?.data || res.data || [];
          this.partsList = (Array.isArray(data) ? data : []).map(p => ({
            value: p.id,
            label: `${p.ma_phu_tung ? `[${p.ma_phu_tung}] ` : ''}${p.ten_phu_tung}`,
            part_name: p.ten_phu_tung,
            part_code: p.ma_phu_tung || '',
            price: Number(p.gia_ban || 0)
          }));
        } catch (e) {
          console.error("Lỗi tải danh sách phụ tùng:", e);
        }
      }
    },
    onSelectCustomerQuick(val) {
      if (!val) return;
      const found = this.customersList.find(c => c.value === val);
      if (found) {
        this.createOrderForm.khach_hang_ten = found.name || '';
        this.createOrderForm.khach_hang_sdt = found.phone || '';
        this.createOrderForm.khach_hang_email = found.email || '';
        this.createOrderForm.dia_chi_giao_hang = found.address || '';
      }
    },
    onSelectPartQuick(val) {
      if (!val) return;
      const found = this.partsList.find(p => p.value === val);
      if (found) {
        const lastItem = this.createOrderForm.chi_tiet[this.createOrderForm.chi_tiet.length - 1];
        if (lastItem && !lastItem.ten_phu_tung && !lastItem.ma_phu_tung) {
          lastItem.ten_phu_tung = found.part_name;
          lastItem.ma_phu_tung = found.part_code;
          lastItem.model_may = '';
          lastItem.don_gia = found.price;
        } else {
          this.createOrderForm.chi_tiet.push({
            ten_phu_tung: found.part_name,
            ma_phu_tung: found.part_code,
            model_may: '',
            so_luong: 1,
            don_gia: found.price
          });
        }
        this.$nextTick(() => {
          this.selectedPartQuick = null;
        });
      }
    },
    addCreateOrderItem() {
      this.createOrderForm.chi_tiet.push({
        ten_phu_tung: '',
        ma_phu_tung: '',
        model_may: '',
        so_luong: 1,
        don_gia: 0
      });
    },
    removeCreateOrderItem(index) {
      if (this.createOrderForm.chi_tiet.length <= 1) {
        this.createOrderForm.chi_tiet = [{ ten_phu_tung: '', ma_phu_tung: '', model_may: '', so_luong: 1, don_gia: 0 }];
      } else {
        this.createOrderForm.chi_tiet.splice(index, 1);
      }
    },
    setDepositPercent(percent) {
      const sub = this.createFormSubtotal;
      this.createOrderForm.tien_coc_yeu_cau = Math.round((sub * percent) / 100);
      if (this.createOrderForm.da_thu === 0) {
        this.createOrderForm.da_thu = this.createOrderForm.tien_coc_yeu_cau;
      }
    },
    async handleUploadCreateReceipt(e, key) {
      const file = e.target.files?.[0];
      if (!file) return;

      let uploadFile = file;
      try {
        const { file: optimizedFile } = await compressReceipt(file);
        uploadFile = optimizedFile;
      } catch (compErr) {
        console.warn('Lỗi nén biên lai, dùng file gốc:', compErr);
      }

      const formData = new FormData();
      formData.append('file', uploadFile);
      try {
        const res = await axiosClient.post('/admin/sales/upload-bien-lai', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        const url = res.data?.url || res.url;
        if (url) {
          if (key === 'anh_chuyen_khoan') {
            this.createOrderForm.anh_chuyen_khoan = url;
          } else {
            this.createOrderForm.anh_chuyen_khoan_2 = url;
          }
          this.toaster.success('Tải biên lai thành công!');
        }
      } catch (err) {
        this.toaster.error('Lỗi tải ảnh biên lai!');
      }
    },
    async submitCreateOrder(actionType) {
      if (!this.createOrderForm.khach_hang_ten?.trim()) {
        this.toaster.error('Vui lòng nhập Tên Khách Hàng!');
        return;
      }
      if (!this.createOrderForm.khach_hang_sdt?.trim()) {
        this.toaster.error('Vui lòng nhập Số Điện Thoại!');
        return;
      }
      const validItems = (this.createOrderForm.chi_tiet || []).filter(item => item.ten_phu_tung && item.ten_phu_tung.trim());
      if (validItems.length === 0) {
        this.toaster.error('Vui lòng nhập ít nhất 1 Phụ tùng / Sản phẩm!');
        return;
      }

      let targetStatus = 0;
      const depRequired = Number(this.createOrderForm.tien_coc_yeu_cau) || 0;
      if (depRequired < 0 || depRequired > this.createFormGrandTotal) {
        this.toaster.error(`Số tiền cọc yêu cầu không được vượt quá tổng đơn (${this.formatCurrency(this.createFormGrandTotal)})!`);
        return;
      }
      let daThuValue = Number(this.createOrderForm.da_thu) || 0;
      if (actionType === 'deposit') {
        targetStatus = 2; // Đã Cọc & Xử Lý
        daThuValue = depRequired > 0 ? depRequired : this.createFormGrandTotal;
      } else if (Number(this.createOrderForm.da_thu) > 0) {
        targetStatus = 2; // Đã Cọc & Xử Lý
      } else if (actionType === 'quoted') {
        targetStatus = 1; // Đã Báo Giá
      }

      const payload = {
        khach_hang_ten: this.createOrderForm.khach_hang_ten.trim(),
        khach_hang_sdt: this.createOrderForm.khach_hang_sdt.trim(),
        khach_hang_email: this.createOrderForm.khach_hang_email?.trim() || null,
        dia_chi_giao_hang: this.createOrderForm.dia_chi_giao_hang?.trim() || null,
        ghi_chu_khach_hang: this.createOrderForm.ghi_chu_khach_hang?.trim() || null,
        ghi_chu_zalo: this.createOrderForm.ghi_chu_zalo?.trim() || null,
        ten_don_vi_van_chuyen: this.createOrderForm.ten_don_vi_van_chuyen?.trim() || null,
        ma_van_don: this.createOrderForm.ma_van_don?.trim() || null,
        phi_van_chuyen: Number(this.createOrderForm.phi_van_chuyen) || 0,
        tien_coc_yeu_cau: depRequired,
        da_thu: daThuValue,
        anh_chuyen_khoan: this.createOrderForm.anh_chuyen_khoan || null,
        anh_chuyen_khoan_2: this.createOrderForm.anh_chuyen_khoan_2 || null,
        trang_thai: targetStatus,
        chi_tiet: validItems.map(item => ({
          ten_phu_tung: item.ten_phu_tung.trim(),
          ma_phu_tung: item.ma_phu_tung?.trim() || 'N/A',
          model_may: item.model_may?.trim() || null,
          so_luong: Number(item.so_luong) || 1,
          don_gia: Number(item.don_gia) || 0
        }))
      };

      this.isSubmittingCreateOrder = true;
      try {
        await axiosClient.post('/admin/yeu-cau-bao-gia', payload);
        this.toaster.success(targetStatus === 2 ? 'Lên đơn hàng thành công! Đã chuyển vào tab ĐÃ CỌC & XỬ LÝ.' : 'Tạo yêu cầu báo giá thành công!');
        
        // eslint-disable-next-line no-undef
        const modalEl = document.getElementById('createOrderModal');
        if (modalEl) {
          // eslint-disable-next-line no-undef
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
        }

        this.refreshData();

        if (targetStatus === 2) {
          this.switchTab('processing');
        } else if (targetStatus === 1) {
          this.switchTab('quoted_quotes');
        } else {
          this.switchTab('pending_quotes');
        }
      } catch (err) {
        console.error("Lỗi khi tạo đơn:", err);
        this.toaster.error(err.response?.data?.message || 'Có lỗi xảy ra khi tạo đơn hàng!');
      } finally {
        this.isSubmittingCreateOrder = false;
      }
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
        iconColor: '#f82249',
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
        iconColor: '#f82249',
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
          iconColor: '#f82249',
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
        iconColor: '#f82249',
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
    handleDepositInput(targetObj, field, event, maxAllowed) {
      if (!targetObj) return;
      const raw = event?.target?.value ?? '';
      if (raw === '') {
        targetObj[field] = 0;
        return;
      }
      let val = Number(raw);
      if (Number.isNaN(val) || val < 0) {
        val = 0;
        if (event?.target) event.target.value = '0';
      }
      const cap = Math.max(0, Number(maxAllowed || 0));
      if (val > cap) {
        val = cap;
        if (event?.target) event.target.value = cap > 0 ? String(cap) : '';
        this.toaster.warning(
          cap > 0
            ? `Tiền cọc tối đa bằng Tổng Cộng (${this.formatCurrency(cap)})!`
            : 'Vui lòng nhập đơn giá sản phẩm trước khi nhập tiền cọc!'
        );
      }
      targetObj[field] = val;
    },
    async saveDraftQuote() {
      if (!this.selectedQuote) return;
      if (this.selectedQuote.items && this.selectedQuote.items.length > 0) {
        this.selectedQuote.total_amount = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      } else {
        this.selectedQuote.total_amount = (this.selectedQuote.total_amount || 0) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      }

      if (Number(this.selectedQuote.deposit_required || 0) > this.quoteGrandTotal) {
        this.toaster.error(`Tiền cọc không được vượt quá Tổng Cộng (${this.formatCurrency(this.quoteGrandTotal)})!`);
        return;
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
            da_thu: this.selectedQuote.da_thu || 0,
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

      if (Number(this.selectedQuote.deposit_required || 0) > this.quoteGrandTotal) {
        this.toaster.error(`Tiền cọc không được vượt quá Tổng Cộng (${this.formatCurrency(this.quoteGrandTotal)})!`);
        return;
      }

      this.selectedQuote.total_amount = itemsTotal + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));

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
            da_thu: this.selectedQuote.da_thu || 0,
            chi_tiet: this.selectedQuote.items
        };
        await this.saveQuoteMutation.mutateAsync({ id: this.selectedQuote.id, payload });
        this.selectedQuote.status = 1; // Chuyển sang Đã Báo Giá
        this.closeModal('quoteModal');

        // Chuyển tab sang "Đã Báo Giá"
        this.currentTab = 'quoted_quotes';

        this.toaster.success('Báo giá thành công!');
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

      if (Number(this.selectedQuote.deposit_required || 0) > this.quoteGrandTotal) {
        this.toaster.error(`Tiền cọc không được vượt quá Tổng Cộng (${this.formatCurrency(this.quoteGrandTotal)})!`);
        return false;
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
            da_thu: this.selectedQuote.da_thu || 0,
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

      if (Number(this.selectedOrder.deposit_required || 0) > this.orderGrandTotal) {
        this.toaster.error(`Tiền cọc không được vượt quá Tổng Cộng (${this.formatCurrency(this.orderGrandTotal)})!`);
        return;
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

    toggleDepositCollected(mode) {
      if (mode === 'quote' && this.selectedQuote) {
        if (Number(this.selectedQuote.da_thu || 0) > 0) {
          this.selectedQuote.da_thu = 0;
        } else {
          const dep = Number(this.selectedQuote.deposit_required || 0);
          const total = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
          this.selectedQuote.da_thu = dep > 0 ? dep : total;
        }
      } else if (mode === 'order' && this.selectedOrder) {
        if (Number(this.selectedOrder.paid_amount || 0) > 0) {
          this.selectedOrder.paid_amount = 0;
        } else {
          const dep = Number(this.selectedOrder.deposit_required || 0);
          const total = this.calculateTotal(this.selectedOrder.items) + (this.selectedOrder.freight_collect ? 0 : (this.selectedOrder.shipping_fee || 0));
          this.selectedOrder.paid_amount = dep > 0 ? dep : total;
        }
      } else if (mode === 'create' && this.createOrderForm) {
        if (Number(this.createOrderForm.da_thu || 0) > 0) {
          this.createOrderForm.da_thu = 0;
        } else {
          const dep = Number(this.createOrderForm.tien_coc_yeu_cau || 0);
          this.createOrderForm.da_thu = dep > 0 ? dep : this.createFormGrandTotal;
        }
      }
    },

    async confirmDepositFromModal(quote) {
      if (!quote) return;
      const saved = await this.saveQuoteSilent();
      if (!saved) return;
      await this.confirmDepositBtn(quote);
    },

    formatReportDateTime(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      if (Number.isNaN(d.getTime())) return '';
      return d.toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' });
    },

    async rejectDepositReport(item, mode) {
      if (!item) return;
      const result = await Swal.fire({
        title: 'Chưa nhận được tiền cọc?',
        text: `Đặt lại mã ${item.code} về trạng thái chờ khách chuyển khoản cọc?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đúng, chưa nhận được',
        cancelButtonText: 'Đóng',
        confirmButtonColor: '#f82249',
        customClass: { popup: 'swal-shift-up' }
      });
      if (!result.isConfirmed) return;

      try {
        const endpoint = mode === 'quote'
          ? `/admin/yeu-cau-bao-gia/${item.id}/tu-choi-bao-coc`
          : `/admin/don-hang/${item.id}/tu-choi-bao-coc`;
        await axiosClient.post(endpoint);
        item.da_bao_chuyen_khoan = false;
        item.thoi_gian_bao_coc = null;
        this.toaster.info('Đã chuyển lại trạng thái chờ khách chuyển cọc.');
        this.refreshData();
      } catch (err) {
        this.toaster.error(err?.response?.data?.message || 'Không thể cập nhật trạng thái.');
      }
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
        let uploadFile = file;
        try {
          const { file: optimizedFile } = await compressReceipt(file);
          uploadFile = optimizedFile;
        } catch (compErr) {
          console.warn('Lỗi nén biên lai, dùng file gốc:', compErr);
        }

        const formData = new FormData();
        formData.append('file', uploadFile);
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

/* ==========================================================================
   ENTERPRISE TRANSACTION MODALS (#quoteModal, #orderModal, #createOrderModal)
   Nền canvas xám nhẹ tách biệt 3 Card trắng, viền input sắc nét, chỉ đọc vs nhập liệu rõ ràng
   ========================================================================== */
.tx-modal-body {
  background-color: #f1f4f9;
  padding: 1.15rem 1.35rem;
}

.tx-card {
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.tx-card-header {
  background-color: #f8fafc;
  border-bottom: 1px solid #cbd5e1;
  padding: 0.68rem 1rem;
  font-size: 0.76rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #334155;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.tx-card-body {
  padding: 1rem;
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
}

/* Viền input / textarea rõ nét hẳn trên nền Card trắng */
.tx-modal .form-control,
.tx-modal .multiselect {
  border: 1.5px solid #94a3b8 !important;
  background-color: #ffffff !important;
  color: #0f172a !important;
  font-weight: 500;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.tx-modal .form-control::placeholder {
  color: #94a3b8 !important;
  font-weight: 400;
}

.tx-modal .form-control:hover:not(:disabled):not([readonly]) {
  border-color: #475569 !important;
}

.tx-modal .form-control:focus {
  border-color: #17a2b8 !important;
  box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.16) !important;
}

.tx-modal .form-control:disabled {
  background-color: #f1f5f9 !important;
  border-color: #cbd5e1 !important;
  color: #64748b !important;
  cursor: not-allowed;
}

/* Khối Ghi Chú Khách Hàng (Chỉ đọc) tách biệt hoàn toàn với ô textarea nhập liệu */
.tx-readonly-note {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  padding: 0.7rem 0.85rem;
  color: #1e293b;
  font-size: 0.85rem;
  white-space: pre-wrap;
  line-height: 1.55;
}


.tx-items-table thead th {
  position: sticky;
  top: 0;
  z-index: 2;
  background-color: #f1f5f9 !important;
  color: #334155 !important;
  font-size: 0.74rem !important;
  font-weight: 700 !important;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 0.65rem 0.75rem !important;
  border-bottom: 1.5px solid #cbd5e1 !important;
}

.tx-items-table tbody td {
  padding: 0.72rem 0.75rem !important;
  border-bottom: 1px solid #e2e8f0;
  font-variant-numeric: tabular-nums;
}

/* Khối Tổng Cộng nổi bật, sắc nét */
.tx-summary-box {
  background-color: #fff5f5;
  border: 1.5px solid #fecaca;
  border-radius: 0.5rem;
  padding: 0.85rem 1rem;
  font-variant-numeric: tabular-nums;
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



