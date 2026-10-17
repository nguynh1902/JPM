<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Chuỗi Cung Ứng /</span> Yêu Cầu & Đơn Khách
    </h4>

    <div class="nav-align-top mb-4">
      <div class="d-flex mb-4 gap-2" role="tablist">
          <div 
            class="fs-5 fw-bold pb-2 custom-line-tab active" 
            role="tab"
            data-bs-toggle="tab"
            data-bs-target="#navs-inbox"
            aria-controls="navs-inbox"
            aria-selected="true"
            style="min-width: 220px;"
          >
            CẦN XỬ LÝ
          </div>
          <div 
            class="fs-5 fw-bold pb-2 custom-line-tab" 
            role="tab"
            data-bs-toggle="tab"
            data-bs-target="#navs-archive"
            aria-controls="navs-archive"
            aria-selected="false"
            style="min-width: 220px;"
          >
            LỊCH SỬ KẾT QUẢ
          </div>
        </div>

      <div class="tab-content border-0 p-0 bg-transparent shadow-none">
          
          <!-- TAB 1: CẦN XỬ LÝ -->
          <div class="tab-pane fade show active" id="navs-inbox" role="tabpanel">
            <div class="card">
              <div class="card-header border-bottom">
                <h5 class="mb-0 fw-bold text-dark">Danh sách Giao Dịch Cần Xử Lý</h5>
              </div>
              <div class="card-body pt-3 pb-3">
                <div class="d-flex align-items-center gap-2">
                  <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="Tìm kiếm mã, SĐT, Tên khách..." v-model="inboxSearch">
                  </div>
                </div>
              </div>
              <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                  <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                    <tr>
                      <th class="text-center text-uppercase" style="width: 5%">STT</th>
                      <th class="text-uppercase text-center" style="width: 10%">Phân Loại</th>
                      <th class="text-uppercase" style="width: 15%">Mã</th>
                      <th class="text-uppercase" style="width: 20%">Khách Hàng</th>
                      <th class="text-uppercase" style="width: 15%">Cập nhật cuối</th>
                      <th class="text-end text-uppercase" style="width: 15%">Tổng Tiền</th>
                      <th class="text-center text-uppercase" style="width: 10%">Trạng thái</th>
                      <th class="text-center text-uppercase" style="width: 10%">Hành động</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <tr v-for="(item, index) in filteredInbox" :key="item._type + item.id">
                      <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                      <td class="text-center">
                        <span class="badge badge-custom" :class="item._type === 'quote' ? 'bg-pastel-info' : 'bg-pastel-warning'">{{ item._type === 'quote' ? 'BÁO GIÁ' : 'ĐƠN HÀNG' }}</span>
                      </td>
                      <td><span class="fw-bold text-dark">{{ item.code }}</span></td>
                      <td>
                        <span class="fw-bold text-dark d-block mb-1">{{ item.customer_name }}</span>
                        <div class="text-muted" style="font-size: 0.8rem;">
                          <i class="bx bx-phone text-primary me-1"></i>{{ item.customer_phone }}
                        </div>
                      </td>
                      <td>
                        <span class="d-block text-dark fw-medium mb-1" style="font-size: 0.85rem;"><i class="bx bx-calendar-alt text-primary me-1"></i>{{ item.date }}</span>
                        <span class="text-muted" style="font-size: 0.75rem;"><i class="bx bx-time-five me-1"></i>{{ item.time }}</span>
                      </td>
                      <td class="text-end">
                        <span class="fw-bold text-danger">{{ formatCurrency(item.total_amount) }}</span>
                      </td>
                      <td class="text-center">
                        <span v-if="item._type === 'quote' && item.status === 0" class="badge badge-custom bg-pastel-warning">CHỜ BÁO GIÁ</span>
                        <span v-else-if="item._type === 'quote' && item.status === 1" class="badge badge-custom bg-pastel-info">CHỜ KHÁCH CHỐT</span>
                        <span v-else-if="item._type === 'order' && item.payment_status === 0" class="badge badge-custom bg-pastel-warning">CHỜ THANH TOÁN</span>
                        <span v-else class="badge badge-custom bg-pastel-secondary">ĐANG X.LÝ</span>
                      </td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-icon action-btn text-primary" @click="handleAction(item)" data-bs-toggle="modal" :data-bs-target="item._type === 'quote' ? '#quoteModal' : '#orderModal'" title="Xử lý"><i class="bx bx-edit-alt"></i></button>
                            <button v-if="currentUserRole === 'ADMIN'" type="button" class="btn btn-icon action-btn text-danger" @click="handleDelete(item)" title="Xóa"><i class="bx bx-trash"></i></button>
                          </div>
                      </td>
                    </tr>
                    <tr v-if="filteredInbox.length === 0">
                      <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                        Không có dữ liệu
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="card-footer py-3 border-top">
                <span class="text-muted fw-medium">Tổng cộng: {{ filteredInbox.length }} mục</span>
              </div>
            </div>
          </div>

          <!-- TAB 2: LỊCH SỬ KẾT QUẢ -->
          <div class="tab-pane fade" id="navs-archive" role="tabpanel">
            <div class="card">
              <div class="card-header border-bottom">
                <h5 class="mb-0 fw-bold text-dark">Lịch sử Giao Dịch</h5>
              </div>
              <div class="card-body pt-3 pb-3">
                <div class="d-flex align-items-center gap-2">
                  <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="Tìm kiếm mã, SĐT, Tên khách..." v-model="archiveSearch">
                  </div>
                </div>
              </div>
              <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                  <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                    <tr>
                      <th class="text-center text-uppercase" style="width: 5%">STT</th>
                      <th class="text-uppercase text-center" style="width: 10%">Phân Loại</th>
                      <th class="text-uppercase" style="width: 15%">Mã</th>
                      <th class="text-uppercase" style="width: 20%">Khách Hàng</th>
                      <th class="text-uppercase" style="width: 15%">Cập nhật cuối</th>
                      <th class="text-end text-uppercase" style="width: 15%">Tổng Tiền</th>
                      <th class="text-center text-uppercase" style="width: 10%">Trạng thái</th>
                      <th class="text-center text-uppercase" style="width: 10%">Hành động</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <tr v-for="(item, index) in filteredArchive" :key="item._type + item.id">
                      <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                      <td class="text-center">
                        <span class="badge badge-custom" :class="item._type === 'quote' ? 'bg-pastel-info' : 'bg-pastel-warning'">{{ item._type === 'quote' ? 'BÁO GIÁ' : 'ĐƠN HÀNG' }}</span>
                      </td>
                      <td><span class="fw-bold text-dark">{{ item.code }}</span></td>
                      <td>
                        <span class="fw-bold text-dark d-block mb-1">{{ item.customer_name }}</span>
                        <div class="text-muted" style="font-size: 0.8rem;">
                          <i class="bx bx-phone text-primary me-1"></i>{{ item.customer_phone }}
                        </div>
                      </td>
                      <td>
                        <span class="d-block text-dark fw-medium mb-1" style="font-size: 0.85rem;"><i class="bx bx-calendar-alt text-primary me-1"></i>{{ item.date }}</span>
                        <span class="text-muted" style="font-size: 0.75rem;"><i class="bx bx-time-five me-1"></i>{{ item.time }}</span>
                      </td>
                      <td class="text-end">
                        <span class="fw-bold text-danger">{{ formatCurrency(item.total_amount) }}</span>
                      </td>
                      <td class="text-center">
                        <span v-if="item._type === 'quote' && item.status === 2" class="badge badge-custom bg-pastel-success">ĐÃ CHỐT</span>
                        <span v-else-if="item._type === 'quote' && item.status === 3" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                        <span v-else-if="item._type === 'order' && item.status === 3" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                        <span v-else-if="item._type === 'order' && item.status === 4" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
                        <span v-else class="badge badge-custom bg-pastel-info">ĐANG GIAO</span>
                      </td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-icon action-btn text-secondary" @click="handleAction(item)" data-bs-toggle="modal" :data-bs-target="item._type === 'quote' ? '#quoteModal' : '#orderModal'" title="Xem chi tiết"><i class="bx bx-show"></i></button>
                            <button v-if="currentUserRole === 'ADMIN'" type="button" class="btn btn-icon action-btn text-danger" @click="handleDelete(item)" title="Xóa"><i class="bx bx-trash"></i></button>
                          </div>
                      </td>
                    </tr>
                    <tr v-if="filteredArchive.length === 0">
                      <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                        Không có dữ liệu
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="card-footer py-3 border-top">
                <span class="text-muted fw-medium">Tổng cộng: {{ filteredArchive.length }} mục</span>
              </div>
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
                <button type="button" class="btn btn-success px-3 me-2 shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly" @click.prevent="copyMagicLink">
                  <i class="bx bx-link me-1"></i> Copy Link Zalo
                </button>
                <button type="button" class="btn btn-info px-3 text-white shadow-sm" v-if="selectedQuote && selectedQuote.status === 1 && !isQuoteReadOnly" @click.prevent="adminChotDon">
                  <i class="bx bx-check-double me-1"></i> Khách Đã Chốt (Tạo Đơn)
                </button>
              </div>
              <div>
                <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" v-if="selectedQuote && !isQuoteReadOnly">Lưu Báo Giá</button>
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
                <div class="mb-4 mt-4">
                   <button class="btn btn-warning w-100 shadow-sm" @click="convertToPO" v-if="!isOrderReadOnly">
                     <i class="bx bx-transfer me-1"></i> Chuyển sang PO (Gom đơn)
                   </button>
                   <small class="text-muted d-block mt-1 text-center">Tạo Yêu cầu Nhập hàng (PO) cho các món thiếu.</small>
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
          <div class="modal-footer border-top py-3 mt-2">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
              <button type="button" class="btn btn-primary px-4 shadow-sm" @click="saveOrder" v-if="!isOrderReadOnly">Lưu Thay Đổi</button>
              <button type="button" class="btn btn-primary px-4 shadow-sm">
                <i class="bx bx-printer me-1"></i> In Hóa Đơn
              </button>
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
import Swal from 'sweetalert2';

export default {
  name: "DonHang",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    
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

    return { queryClient, rawOrders, rawQuotes, isFetchingOrders, isFetchingQuotes, deleteQuoteMutation, deleteOrderMutation, saveQuoteMutation, saveOrderMutation };
  },
  data() {
    return {
      currentUserRole: 'STAFF',
      inboxSearch: "",
      archiveSearch: "",
      selectedQuote: null,
      selectedOrder: null,
    };
  },
  computed: {
    isQuoteReadOnly() {
      return this.selectedQuote && this.selectedQuote.status >= 2;
    },
    isOrderReadOnly() {
      return this.selectedOrder && this.selectedOrder.status >= 3;
    },
    inboxItems() {
      let result = [];
      this.quotes.forEach(q => {
        if (q.status === 0 || q.status === 1) {
          result.push({ ...q, _type: 'quote' });
        }
      });
      this.orders.forEach(o => {
        if (o.status !== 3 && o.status !== 4 && Number(o.payment_status) === 0) {
          result.push({ ...o, _type: 'order' });
        }
      });
      return result.sort((a, b) => {
        // Sort by raw ID (which acts as proxy for date if no dates are parsed perfectly)
        return b.id - a.id; 
      });
    },
    archiveItems() {
      let result = [];
      this.quotes.forEach(q => {
        if (q.status === 2 || q.status === 3) {
          result.push({ ...q, _type: 'quote' });
        }
      });
      this.orders.forEach(o => {
        if (o.status === 3 || o.status === 4 || Number(o.payment_status) >= 1) {
          result.push({ ...o, _type: 'order' });
        }
      });
      return result.sort((a, b) => b.id - a.id);
    },
    filteredInbox() {
      let result = this.inboxItems;
      if (this.inboxSearch) {
        const s = this.inboxSearch.toLowerCase();
        result = result.filter(item => 
          (item.code && item.code.toLowerCase().includes(s)) ||
          (item.customer_name && item.customer_name.toLowerCase().includes(s)) ||
          (item.customer_phone && item.customer_phone.includes(s))
        );
      }
      return result;
    },
    filteredArchive() {
      let result = this.archiveItems;
      if (this.archiveSearch) {
        const s = this.archiveSearch.toLowerCase();
        result = result.filter(item => 
          (item.code && item.code.toLowerCase().includes(s)) ||
          (item.customer_name && item.customer_name.toLowerCase().includes(s)) ||
          (item.customer_phone && item.customer_phone.includes(s))
        );
      }
      return result;
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
            part_name: i.phu_tung ? i.phu_tung.ten_phu_tung : (i.san_pham ? i.san_pham.ten_san_pham : 'Sản phẩm ID: ' + i.id_san_pham),
            part_code: i.phu_tung ? i.phu_tung.ma_phu_tung : (i.san_pham ? i.san_pham.ma_san_pham : (i.loai_san_pham === 'phu_tung' ? 'PT-' + i.id_san_pham : 'SP-' + i.id_san_pham)),
            qty: i.so_luong || 0,
            price: Number(i.don_gia || 0),
            in_stock: Number(i.phu_tung ? i.phu_tung.so_luong_ton : (i.san_pham ? i.san_pham.so_luong_ton : 0)) >= Number(i.so_luong || 0)
          }))
        };
      });
    },
    
    
  },
  methods: {
    handleDelete(item) {
      if (item._type === 'quote') {
        this.deleteQuote(item);
      } else {
        this.deleteOrder(item);
      }
    },
    handleAction(item) {
      if (item._type === 'quote') {
        this.openQuoteModal(item);
      } else {
        this.openOrderModal(item);
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
          onError: () => Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể xóa báo giá này.' })
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
          onError: () => Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể xóa đơn hàng này.' })
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
      this.selectedQuote.total_amount = this.calculateTotal(this.selectedQuote.items) + (this.selectedQuote.freight_collect ? 0 : (this.selectedQuote.shipping_fee || 0));
      if (this.selectedQuote.status === 0 && this.selectedQuote.total_amount > 0) {
        this.selectedQuote.status = 1;
      }
      
      // Optimistic Update
      const idx = this.quotes.findIndex(r => r.id === this.selectedQuote.id);
      if (idx !== -1) {
          this.quotes[idx] = { ...this.selectedQuote };
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
        this.$toast?.success("Cập nhật báo giá thành công!");
      } catch (error) {
          console.error("Lỗi cập nhật báo giá:", error);
          this.$toast?.error("Lỗi cập nhật báo giá!");
      }
      this.closeModal('quoteModal');
    },
    async saveOrder() {
      // Optimistic update
      const idx = this.orders.findIndex(r => r.id === this.selectedOrder.id);
      if (idx !== -1) {
        this.selectedOrder.total_amount = this.calculateTotal(this.selectedOrder.items) + (this.selectedOrder.freight_collect ? 0 : (this.selectedOrder.shipping_fee || 0));
        this.orders[idx] = { ...this.selectedOrder };
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
        this.$toast?.success("Cập nhật đơn hàng thành công!");
        localStorage.removeItem('draft_order_' + this.selectedOrder.id);
      } catch (error) {
        console.warn("Lỗi mạng hoặc API, lưu nháp Offline...");
        localStorage.setItem('draft_order_' + this.selectedOrder.id, JSON.stringify(this.selectedOrder));
        this.$toast?.warning("Mất mạng hoặc lỗi hệ thống! Đơn hàng được lưu nháp cục bộ.", { duration: 5000 });
      }

      this.closeModal('orderModal');
    },
    async convertToPO() {
      if (!this.selectedOrder) return;
      const result = await Swal.fire({
        title: 'Chuyển sang PO (Gom đơn)?',
        html: `Bạn muốn tạo Đơn Nhập Hàng (PO) gom cho các sản phẩm trong đơn <b>${this.selectedOrder.code}</b>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tạo PO ngay',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
          popup: 'swal-shift-up',
          confirmButton: 'btn btn-warning px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const res = await axiosClient.post(`/admin/don-hang/${this.selectedOrder.id}/tao-po`);
          if (res.success || (res.data && res.data.success)) {
            const poData = res.data?.data || res.data;
            const maPO = poData.ma_po || 'Mới';
            Swal.fire({
              icon: 'success',
              title: 'Tạo PO thành công!',
              html: `Đã tạo Đơn nhập hàng mã <b>${maPO}</b>.<br/>Bạn có thể tiếp tục xử lý nhập hàng ở mục Quản lý PO.`,
              confirmButtonText: 'Đến trang Quản lý PO',
              showCancelButton: true,
              cancelButtonText: 'Ở lại đây',
              reverseButtons: true,
              customClass: {
                confirmButton: 'btn btn-primary px-4 shadow-sm',
                cancelButton: 'btn btn-outline-secondary px-4 me-3'
              },
              buttonsStyling: false
            }).then((nav) => {
              if (nav.isConfirmed) {
                this.$router.push('/admin/don-nhap-hang');
              }
            });
            this.closeModal('orderModal');
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Không thể tạo đơn PO.';
          Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
        }
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
    }
  }
};
</script>

<style scoped>
.custom-line-tab {
  cursor: pointer;
  min-width: 160px;
  text-align: center;
  color: #a1acb8; /* text-muted */
  border-bottom: 3px solid transparent;
  transition: all 0.2s ease;
}
.custom-line-tab.active {
  color: var(--bs-primary);
  border-bottom: 3px solid var(--bs-primary);
}
.custom-line-tab:hover:not(.active) {
  color: #697a8d;
}
</style>



