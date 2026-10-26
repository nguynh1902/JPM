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
            title="Đơn hàng đang chuẩn bị, xuất kho hoặc gửi chành xe"
          >
            <i class="bx bx-package me-1"></i>
            <span>Đang Xử Lý & Giao</span>
            <span v-if="countProcessing > 0" class="tab-count-badge ms-2">{{ countProcessing }}</span>
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
              <div style="width: 200px;" class="flex-shrink-0" v-if="currentTab === 'all' || currentTab === 'processing' || currentTab === 'cancelled'">
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
                  <th class="text-uppercase text-center" style="width: 10%">Phân Loại</th>
                  <th class="text-uppercase" style="width: 14%">Mã Giao Dịch</th>
                  <th class="text-uppercase" style="width: 20%">Khách Hàng</th>
                  <th class="text-uppercase" style="width: 13%">Thời Gian</th>
                  <th class="text-end text-uppercase" style="width: 14%">Tổng Tiền</th>
                  <th class="text-end text-uppercase" style="width: 14%">Thanh Toán / Cọc</th>
                  <th class="text-center text-uppercase" style="width: 12%">Trạng Thái</th>
                  <th class="text-center text-uppercase" style="width: 10%">Hành Động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-for="(item, index) in filteredTabList" :key="item._type + item.id">
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td class="text-center text-nowrap">
                    <span v-if="item._type === 'quote'" class="fw-bold text-warning" style="font-size: 0.85rem;">
                      BÁO GIÁ
                    </span>
                    <span v-else class="fw-bold text-primary" style="font-size: 0.85rem;">
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
                    <template v-if="item._type === 'order'">
                      <div v-if="item.paid_amount > 0" class="fw-bold text-success" style="font-size: 0.85rem;">
                        <i class="bx bx-check-circle me-1"></i>Đã thu: {{ formatCurrency(item.paid_amount) }}
                      </div>
                      <div v-else-if="item.deposit_required > 0" class="d-flex flex-column align-items-end">
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mb-1" style="font-size: 0.75rem;">
                          Chưa cọc
                        </span>
                        <span class="text-muted" style="font-size: 0.75rem;">
                          (YC: {{ formatCurrency(item.deposit_required) }})
                        </span>
                      </div>
                      <span v-else class="text-muted" style="font-size: 0.85rem;">Chưa thu</span>
                    </template>
                    <template v-else>
                      <div v-if="item.status === 0" class="text-muted" style="font-size: 0.8rem;">
                        <span class="badge bg-light text-secondary border">Chờ định giá</span>
                      </div>
                      <div v-else-if="item.deposit_required > 0" class="d-flex flex-column align-items-end">
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mb-1" style="font-size: 0.75rem;">
                          Chờ khách cọc
                        </span>
                        <span class="text-muted" style="font-size: 0.75rem;">
                          (YC: {{ formatCurrency(item.deposit_required) }})
                        </span>
                      </div>
                      <span v-else class="text-muted" style="font-size: 0.85rem;">-</span>
                    </template>
                  </td>
                  <td class="text-center">
                    <!-- Trạng thái Báo Giá -->
                    <template v-if="item._type === 'quote'">
                      <span v-if="item.status === 0" class="badge badge-custom bg-pastel-warning">CHỜ BÁO GIÁ</span>
                      <span v-else-if="item.status === 1" class="badge badge-custom bg-pastel-info">ĐÃ GỬI BÁO GIÁ</span>
                      <span v-else-if="item.status === 2" class="badge badge-custom bg-pastel-success">ĐÃ CHỐT</span>
                      <span v-else-if="item.status === 3" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                      <span v-else class="badge badge-custom bg-pastel-secondary">KHÁC</span>
                    </template>
                    <!-- Trạng thái Đơn Hàng -->
                    <template v-else>
                      <span v-if="item.status === 0 && Number(item.payment_status) === 0" class="badge badge-custom bg-pastel-warning">CHỜ DUYỆT & CỌC</span>
                      <span v-else-if="item.status === 0" class="badge badge-custom bg-pastel-info">CHỜ DUYỆT</span>
                      <span v-else-if="item.status === 1" class="badge badge-custom bg-pastel-secondary">LÊN ĐƠN NHẬT</span>
                      <span v-else-if="item.status === 2" class="badge badge-custom bg-pastel-info">GIAO VIETTELPOST</span>
                      <span v-else-if="item.status === 3" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                      <span v-else-if="item.status === 4" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                      <span v-else class="badge badge-custom bg-pastel-secondary">ĐANG XỬ LÝ</span>
                      <div v-if="item.ma_van_don" class="small text-muted font-monospace mt-1">
                        <i class="bx bxs-truck text-primary me-1"></i>{{ item.ma_van_don }}
                      </div>
                    </template>
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                      <!-- Nút Xử lý / Xem chi tiết -->
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

                      <!-- Nút Copy Link Báo Giá (Báo giá) -->
                      <button 
                        v-if="item._type === 'quote' && item.token" 
                        type="button" 
                        class="action-btn text-success" 
                        @click="copyQuoteLink(item)" 
                        title="Copy link báo giá gửi khách"
                      >
                        <i class="bx bx-link"></i>
                      </button>

                      <!-- Nút Gửi Zalo (Báo giá) -->
                      <button 
                        v-if="item._type === 'quote' && item.customer_phone" 
                        type="button" 
                        class="action-btn text-info" 
                        @click="quickOpenZalo(item)" 
                        title="Gửi Zalo cho khách"
                      >
                        <i class="bx bxl-telegram"></i>
                      </button>

                      <!-- Nút Xác Nhận Nhận Cọc (Báo giá hoặc Đơn chờ cọc) -->
                      <button 
                        v-if="(item._type === 'quote' && item.status === 1) || (item._type === 'order' && item.status === 0)" 
                        type="button" 
                        class="action-btn text-warning" 
                        @click="confirmDepositBtn(item)" 
                        title="Xác nhận đã nhận cọc & Chuyển lên đơn Nhật"
                      >
                        <i class="bx bx-check-shield"></i>
                      </button>

                      <!-- Nút Cập Nhật ViettelPost (Đơn hàng đang chuẩn bị hoặc đang giao) -->
                      <button 
                        v-if="item._type === 'order' && (item.status === 1 || item.status === 2)" 
                        type="button" 
                        class="action-btn text-primary" 
                        @click="openViettelPostModal(item)" 
                        title="Cập nhật mã vận đơn ViettelPost"
                      >
                        <i class="bx bxs-truck"></i>
                      </button>

                      <!-- Nút Xác Nhận Thu COD & Hoàn Thành (Đơn hàng đang giao ViettelPost) -->
                      <button 
                        v-if="item._type === 'order' && item.status === 2" 
                        type="button" 
                        class="action-btn text-success" 
                        @click="completeOrderBtn(item)" 
                        title="Xác nhận ViettelPost đã đối soát COD -> Hoàn tất đơn"
                      >
                        <i class="bx bx-check-double"></i>
                      </button>

                      <!-- Nút Xóa -->
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
                          <td colspan="4" class="text-end text-muted align-middle">Phí Vận Chuyển (ViettelPost):</td>
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
                        <tr>
                          <td colspan="4" class="text-end text-secondary fw-semibold align-middle">ViettelPost Thu COD Khi Giao:</td>
                          <td class="text-end fw-bold text-dark fs-6 font-monospace align-middle">
                            {{ formatCurrency(Math.max(0, (calculateTotal(selectedQuote.items) + (selectedQuote.freight_collect ? 0 : (selectedQuote.shipping_fee || 0))) - (selectedQuote.deposit_required || 0))) }}
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer border-top py-2 mt-2 d-flex justify-content-between align-items-center">
              <div>
                <button type="button" class="btn btn-outline-info px-3 me-2 shadow-sm" v-if="selectedQuote && selectedQuote.token" @click.prevent="copyQuoteLink(selectedQuote)">
                  <i class="bx bx-link me-1"></i> Copy Link Báo Giá
                </button>
                <button type="button" class="btn btn-primary px-3 me-2 shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly && canProcessOrders" @click.prevent="openZaloMessage">
                  <i class="bx bx-chat me-1"></i> Mở Zalo Gửi Khách
                </button>
                <button type="button" class="btn btn-warning text-dark fw-bold px-3 me-2 shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly && canProcessOrders" @click.prevent="confirmDepositFromModal(selectedQuote)">
                  <i class="bx bx-check-shield me-1"></i> Xác Nhận Đã Nhận Cọc & Lên Đơn Nhật
                </button>
                <button type="button" class="btn btn-info px-3 text-white shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly && canProcessOrders" @click.prevent="adminChotDon">
                  <i class="bx bx-check-double me-1"></i> Khách Chốt Không Cọc (Tạo Đơn)
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
                        {value: 0, label: '0 - Chờ duyệt / Chờ xác nhận'},
                        {value: 1, label: '1 - Đang chuẩn bị / Đang xử lý'},
                        {value: 2, label: '2 - Đang giao hàng'},
                        {value: 3, label: '3 - Hoàn thành / Hoàn tất'},
                        {value: 4, label: '4 - Đã hủy (Hoàn kho)'}
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
              <button type="button" class="btn btn-outline-primary px-3 shadow-sm" @click="openVietQrModal(selectedOrder)" title="Xem mã VietQR">
                <i class="bx bx-qr-scan me-1"></i> Mã VietQR
              </button>
              <button type="button" class="btn btn-outline-info px-3 shadow-sm" v-if="selectedOrder && (selectedOrder.status === 1 || selectedOrder.status === 2)" @click="openViettelPostModal(selectedOrder)" title="Cập nhật mã vận đơn ViettelPost">
                <i class="bx bxs-truck me-1"></i> Vận Đơn ViettelPost
              </button>
              <button type="button" class="btn btn-success px-3 shadow-sm" v-if="selectedOrder && selectedOrder.status === 2" @click="completeOrderBtn(selectedOrder)" title="Xác nhận ViettelPost đã chuyển COD">
                <i class="bx bx-check-double me-1"></i> Thu COD & Hoàn Thành
              </button>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary px-4 shadow-sm" @click="saveOrder" v-if="!isOrderReadOnly && canEditOrders">Lưu Thay Đổi</button>
              <button type="button" class="btn btn-primary px-4 shadow-sm" @click="printOrder">
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

    <!-- Modal: Cập Nhật Vận Đơn ViettelPost Giao Hàng -->
    <div class="modal fade" id="viettelPostModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title fw-bold text-white mb-0">
              <i class="bx bxs-truck me-2"></i>Giao Hàng Qua ViettelPost
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
                <input type="text" class="form-control" v-model="viettelPostForm.ten_don_vi_van_chuyen" placeholder="ViettelPost" required>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Mã Vận Đơn ViettelPost <span class="text-danger">*</span></label>
                <input type="text" class="form-control" v-model="viettelPostForm.ma_van_don" placeholder="VD: VP123456789VN" required>
                <small class="text-muted">Nhập mã vận đơn do ViettelPost cấp khi bưu tá nhận hàng hoặc tạo đơn trên app ViettelPost.</small>
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
                  <span class="fw-bold text-dark">Số tiền ViettelPost thu COD:</span>
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
      // ViettelPost Shipping Modal
      viettelPostOrder: null,
      viettelPostForm: {
        ma_van_don: "",
        ten_don_vi_van_chuyen: "ViettelPost"
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
      isSendingShipping: false
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
          ten_don_vi_van_chuyen: item.ten_don_vi_van_chuyen || 'ViettelPost',
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
    },
    refreshData() {
      this.queryClient.invalidateQueries({ queryKey: ['yeu-cau-bao-gia'] });
      this.queryClient.invalidateQueries({ queryKey: ['don-hang'] });
    },
    copyQuoteLink(item) {
      if (!item || !item.token) {
        Swal.fire({ icon: 'warning', iconColor: '#ff3e1d', title: 'Thông báo', text: 'Báo giá này chưa có liên kết tra cứu.' });
        return;
      }
      const link = `${window.location.origin}/bao-gia/${item.token}`;
      navigator.clipboard.writeText(link).then(() => {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Đã sao chép link báo giá!',
          showConfirmButton: false,
          timer: 2000
        });
      });
    },
    quickOpenZalo(item) {
      const phone = (item.customer_phone || '').replace(/[^0-9]/g, '');
      if (!phone) {
        Swal.fire({ icon: 'warning', iconColor: '#ff3e1d', title: 'Thiếu số điện thoại', text: 'Khách hàng chưa có số điện thoại để gửi Zalo.' });
        return;
      }
      const link = item.token ? `${window.location.origin}/bao-gia/${item.token}` : '';
      const total = item.total_amount || 0;
      const deposit = item.deposit_required || 0;
      const shipping = item.shipping_fee || 0;
      
      let msg = `Chào ${item.customer_name || 'Quý khách'}, JPM Marine đã gửi bảng báo giá cho yêu cầu [${item.code}]:\n`;
      msg += `• Tiền phụ tùng: ${this.formatCurrency(total)}\n`;
      if (shipping > 0) {
        msg += `• Cước vận chuyển ViettelPost: ${this.formatCurrency(shipping)}\n`;
        msg += `• Tổng cộng đơn hàng: ${this.formatCurrency(total + shipping)}\n`;
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
    handleDelete(item) {
      if (item.record_type === 'quote' || item._type === 'quote') {
        this.deleteQuote(item);
      } else {
        this.deleteOrder(item);
      }
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
    openZaloMessage() {
      if (!this.selectedQuote) return;
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
          if (res?.success || res?.data?.success) {
            Swal.fire({ icon: 'success', title: 'Thành công', text: res?.message || res?.data?.message });
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

    async confirmDepositBtn(item) {
      const depositAmt = item.deposit_required || item.tien_coc_yeu_cau || 0;
      const result = await Swal.fire({
        title: 'Xác Nhận Đã Nhận Cọc?',
        html: `Xác nhận bạn đã kiểm tra thông báo ngân hàng và đã nhận đủ tiền cọc <b>${this.formatCurrency(depositAmt)}</b> cho mã <b>${item.code}</b>?<br><small class="text-muted">Đơn hàng sẽ chuyển sang trạng thái "Lên Đơn Nhật" để xuất file PO.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đã nhận cọc, lên đơn ngay',
        cancelButtonText: 'Hủy',
        customClass: {
          confirmButton: 'btn btn-success px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const endpoint = item._type === 'quote'
            ? `/admin/yeu-cau-bao-gia/${item.id}/xac-nhan-coc`
            : `/admin/don-hang/${item.id}/xac-nhan-coc`;
          const res = await axiosClient.post(endpoint, { amount: depositAmt });
          if (res?.success || res?.data?.success) {
            Swal.fire({
              icon: 'success',
              title: 'Thành Công!',
              text: res?.message || res?.data?.message || 'Đã xác nhận nhận cọc thành công.'
            });
            this.refreshData();
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: err?.response?.data?.message || 'Không thể xác nhận cọc.'
          });
        }
      }
    },

    async confirmDepositFromModal(quote) {
      if (!quote) return;
      await this.confirmDepositBtn(quote);
      this.closeModal('quoteModal');
    },

    openViettelPostModal(order) {
      if (!order) return;
      this.viettelPostOrder = order;
      this.viettelPostForm = {
        ma_van_don: order.ma_van_don || '',
        ten_don_vi_van_chuyen: order.ten_don_vi_van_chuyen || 'ViettelPost'
      };
      // eslint-disable-next-line no-undef
      const modalEl = new bootstrap.Modal(document.getElementById('viettelPostModal'));
      modalEl.show();
    },

    async saveViettelPostShipping() {
      if (!this.viettelPostOrder || !this.viettelPostForm.ma_van_don.trim()) {
        Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập mã vận đơn ViettelPost!' });
        return;
      }
      this.isSavingViettelPost = true;
      try {
        const res = await axiosClient.post(`/admin/don-hang/${this.viettelPostOrder.id}/cap-nhat-van-chuyen`, this.viettelPostForm);
        if (res?.success || res?.data?.success) {
          Swal.fire({
            icon: 'success',
            title: 'Thành Công!',
            text: 'Đã cập nhật mã vận đơn ViettelPost và chuyển đơn hàng sang trạng thái Giao ViettelPost.'
          });
          this.closeModal('viettelPostModal');
          this.closeModal('orderModal');
          this.refreshData();
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Lỗi',
          text: err?.response?.data?.message || 'Không thể cập nhật vận chuyển.'
        });
      } finally {
        this.isSavingViettelPost = false;
      }
    },

    async completeOrderBtn(order) {
      if (!order) return;
      const codAmt = Math.max(0, order.total_amount - (order.paid_amount || 0));
      const result = await Swal.fire({
        title: 'Xác Nhận Đã Thu COD & Hoàn Thành?',
        html: `ViettelPost đã giao hàng thành công và chuyển tiền COD <b>${this.formatCurrency(codAmt)}</b> cho đơn <b>${order.code}</b>?<br><small class="text-muted">Đơn hàng sẽ chuyển sang trạng thái "Hoàn Thành" và cập nhật doanh thu 100%.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đã nhận COD, Hoàn thành đơn',
        cancelButtonText: 'Hủy',
        customClass: {
          confirmButton: 'btn btn-success px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const res = await axiosClient.post(`/admin/don-hang/${order.id}/hoan-thanh`);
          if (res?.success || res?.data?.success) {
            Swal.fire({
              icon: 'success',
              title: 'Hoàn Tất Đơn Hàng!',
              text: 'Đơn hàng đã được đánh dấu Hoàn Thành!'
            });
            this.closeModal('orderModal');
            this.refreshData();
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: err?.response?.data?.message || 'Không thể hoàn thành đơn hàng.'
          });
        }
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
        const payload = res?.qr_url ? res : res?.data;
        if (payload && (res.success || payload.success || payload.qr_url)) {
          this.adminVietQr = payload;
          this.currentVietQr = payload;
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
        if (res?.success || res?.data?.success) {
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
</style>



