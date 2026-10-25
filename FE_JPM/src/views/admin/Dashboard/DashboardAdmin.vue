<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Page: Đồng bộ với chuẩn các trang Admin khác -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
      <div>
        <h4 class="fw-bold mb-1 text-dark">Dashboard</h4>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Tổng quan chỉ số hoạt động kinh doanh & bán hàng</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <!-- Bộ lọc thời gian chuẩn Sneat Admin (Tinh gọn, 1 cột ~280px) -->
        <div ref="dropdownRef" class="dropdown position-relative">
          <button 
            class="btn btn-white bg-white border text-secondary btn-sm dropdown-toggle shadow-sm px-3 d-flex align-items-center" 
            type="button" 
            @click.stop="toggleDropdown"
            :aria-expanded="isDropdownOpen"
          >
            <i class="bx bx-calendar me-2 text-primary fs-6"></i> 
            <span class="fw-medium text-dark">{{ currentRangeLabel }}</span>
          </button>
          
          <!-- Dropdown Menu 1 Cột chuẩn Sneat (~280px) -->
          <div 
            class="dropdown-menu dropdown-menu-end shadow-sm border p-3" 
            :class="{ 'show': isDropdownOpen }"
            style="position: absolute; right: 0; top: 100%; z-index: 1050; width: 285px; margin-top: 6px; border-radius: 8px;"
          >
            <!-- 1. Mốc chọn nhanh cốt lõi -->
            <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">
              Mốc xem nhanh
            </div>
            <div class="d-flex flex-column gap-1 mb-2">
              <a 
                href="javascript:void(0)" 
                class="dropdown-item d-flex align-items-center justify-content-between py-1 px-2 rounded"
                :class="{'active-sneat-item': currentPeriod === 'today'}"
                @click="selectPreset('today')"
              >
                <span>Hôm nay</span>
                <small class="text-muted" :class="{'text-white': currentPeriod === 'today'}">{{ todayFormatted }}</small>
              </a>
              <a 
                href="javascript:void(0)" 
                class="dropdown-item d-flex align-items-center justify-content-between py-1 px-2 rounded"
                :class="{'active-sneat-item': currentPeriod === 'week'}"
                @click="selectPreset('week')"
              >
                <span>Tuần này</span>
                <small class="text-muted" :class="{'text-white': currentPeriod === 'week'}">{{ weekFormatted }}</small>
              </a>
              <a 
                href="javascript:void(0)" 
                class="dropdown-item d-flex align-items-center justify-content-between py-1 px-2 rounded"
                :class="{'active-sneat-item': currentPeriod === 'month'}"
                @click="selectPreset('month')"
              >
                <span>Tháng này</span>
                <small class="text-muted" :class="{'text-white': currentPeriod === 'month'}">{{ monthFormatted }}</small>
              </a>
              <a 
                href="javascript:void(0)" 
                class="dropdown-item d-flex align-items-center justify-content-between py-1 px-2 rounded"
                :class="{'active-sneat-item': currentPeriod === 'year'}"
                @click="selectPreset('year')"
              >
                <span>Năm nay</span>
                <small class="text-muted" :class="{'text-white': currentPeriod === 'year'}">{{ yearFormatted }}</small>
              </a>
            </div>

            <!-- Phân cách mỏng -->
            <hr class="my-2 border-light">

            <!-- 2. Chọn khoảng ngày tùy chỉnh -->
            <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">
              Khoảng ngày tùy chọn
            </div>
            
            <div class="mb-2">
              <label class="form-label text-muted mb-1" style="font-size: 0.72rem;">Từ ngày</label>
              <input 
                type="date" 
                class="form-control form-control-sm" 
                v-model="customStartDate" 
                :max="customEndDate || todayIso"
              />
            </div>
            
            <div class="mb-3">
              <label class="form-label text-muted mb-1" style="font-size: 0.72rem;">Đến ngày</label>
              <input 
                type="date" 
                class="form-control form-control-sm" 
                v-model="customEndDate" 
                :min="customStartDate" 
                :max="todayIso"
              />
            </div>

            <button 
              type="button" 
              class="btn btn-primary btn-sm w-100 shadow-sm d-flex align-items-center justify-content-center" 
              @click="applyCustomRange"
              :disabled="!isValidCustomRange || isLoading"
            >
              <i class="bx bx-check me-1"></i> Áp dụng
            </button>
          </div>
        </div>
        <!-- Nút Làm Mới -->
        <button class="btn btn-primary btn-sm shadow-sm px-3" @click="fetchData(true)" :disabled="isLoading">
          <i class="bx bx-refresh me-1" :class="{'bx-spin': isLoading}"></i> Làm mới
        </button>
      </div>
    </div>

    <!-- TẦNG TRÊN: 4 THẺ KPI (COL-5) + BIỂU ĐỒ DOANH THU & CHI PHÍ PO (COL-7) -->
    <div class="row g-3 mb-4">
      <!-- Cột trái: 4 Thẻ KPI (Grid 2x2) -->
      <div class="col-lg-5 col-12">
        <div class="row g-3 h-100">
          <!-- Card 1: Tổng Doanh Thu -->
          <div class="col-sm-6 col-12 d-flex">
            <div class="card border-0 shadow-sm w-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Tổng Doanh Thu</span>
                    <i class="bx bx-dollar-circle text-primary fs-4"></i>
                  </div>
                  <div class="kpi-value text-dark mb-1">{{ formatShortCurrency(kpis.total_sales) }}</div>
                  <div class="d-flex align-items-center gap-1 kpi-subtext">
                    <span :class="kpis.sales_growth >= 0 ? 'text-success' : 'text-danger'" class="fw-semibold">
                      <i class="bx" :class="kpis.sales_growth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt'"></i>
                      {{ kpis.sales_growth >= 0 ? '+' : '' }}{{ kpis.sales_growth }}%
                    </span>
                    <span class="text-muted">| +{{ formatShortCurrency(kpis.sales_today) }} hôm nay</span>
                  </div>
                </div>
                <div class="border-top pt-2 mt-3">
                  <router-link :to="`${basePath}/don-hang`" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem chi tiết</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Tổng Đơn Hàng -->
          <div class="col-sm-6 col-12 d-flex">
            <div class="card border-0 shadow-sm w-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Tổng Đơn Hàng</span>
                    <i class="bx bx-shopping-bag text-info fs-4"></i>
                  </div>
                  <div class="kpi-value text-dark mb-1">{{ (kpis.total_orders || 0).toLocaleString() }}</div>
                  <div class="d-flex align-items-center gap-1 kpi-subtext">
                    <span :class="kpis.orders_growth >= 0 ? 'text-success' : 'text-danger'" class="fw-semibold">
                      <i class="bx" :class="kpis.orders_growth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt'"></i>
                      {{ kpis.orders_growth >= 0 ? '+' : '' }}{{ kpis.orders_growth }}%
                    </span>
                    <span class="text-muted">| +{{ kpis.orders_today }} hôm nay</span>
                  </div>
                </div>
                <div class="border-top pt-2 mt-3">
                  <router-link :to="`${basePath}/don-hang`" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem chi tiết</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Yêu Cầu Báo Giá -->
          <div class="col-sm-6 col-12 d-flex">
            <div class="card border-0 shadow-sm w-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Yêu Cầu Báo Giá</span>
                    <i class="bx bx-message-square-dots text-warning fs-4"></i>
                  </div>
                  <div class="kpi-value text-dark mb-1">{{ (kpis.total_quotes || 0).toLocaleString() }}</div>
                  <div class="d-flex align-items-center gap-1 kpi-subtext">
                    <span class="text-warning fw-semibold">
                      <i class="bx bx-time-five"></i> {{ kpis.pending_quotes }} chờ xử lý
                    </span>
                    <span class="text-muted">| +{{ kpis.quotes_today }} hôm nay</span>
                  </div>
                </div>
                <div class="border-top pt-2 mt-3">
                  <router-link :to="`${basePath}/don-hang`" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem chi tiết</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: Cảnh Báo Tồn Kho -->
          <div class="col-sm-6 col-12 d-flex">
            <div class="card border-0 shadow-sm w-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Hàng Sắp Hết</span>
                    <i class="bx bx-error-circle text-danger fs-4"></i>
                  </div>
                  <div class="kpi-value text-dark mb-1">
                    {{ kpis.items_need_replenish }} <small class="text-muted fw-normal" style="font-size: 0.8rem;">mặt hàng</small>
                  </div>
                  <div class="d-flex align-items-center gap-1 kpi-subtext">
                    <span class="text-muted">
                      Cần bổ sung: <strong class="text-dark">{{ kpis.total_qty_need_replenish }}</strong> cái
                    </span>
                  </div>
                </div>
                <div class="border-top pt-2 mt-3">
                  <router-link to="/portal/ton-kho" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem tồn kho</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: Biểu Đồ Doanh Thu & Chi Phí Nhập Kho -->
      <div class="col-lg-7 col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header pt-3 pb-1 d-flex justify-content-between align-items-start border-bottom-0">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="kpi-label mb-0">Doanh Thu & Chi Phí Nhập Kho</span>
                <span class="badge bg-pastel-info" style="font-size: 0.68rem; padding: 2px 6px;">{{ chartGranularityLabel }}</span>
              </div>
              <div class="d-flex align-items-baseline gap-2">
                <h4 class="fw-bold text-dark mb-0">{{ formatCurrency(revenueChartData.current_period_revenue) }}</h4>
                <span :class="revenueChartData.growth_rate >= 0 ? 'text-success' : 'text-danger'" class="fw-semibold" style="font-size: 0.82rem;">
                  <i class="bx" :class="revenueChartData.growth_rate >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt'"></i>
                  {{ revenueChartData.growth_rate >= 0 ? '+' : '' }}{{ revenueChartData.growth_rate }}%
                </span>
                <small class="text-muted fw-normal" style="font-size: 0.78rem;">({{ periodLabels[currentPeriod] || 'Năm nay' }})</small>
              </div>
            </div>
            <!-- Legend góc phải: Ô màu rồi tới chữ -->
            <div class="d-flex align-items-center gap-3" style="font-size: 0.78rem;">
              <div class="d-flex align-items-center gap-2">
                <span style="display: inline-block; width: 13px; height: 13px; border-radius: 3px; background-color: #696cff;"></span>
                <span class="text-muted fw-medium">Doanh thu bán</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span style="display: inline-block; width: 13px; height: 13px; border-radius: 3px; background-color: #8592a3;"></span>
                <span class="text-muted fw-medium">Chi phí nhập</span>
              </div>
            </div>
          </div>
          <div class="card-body pt-0 px-2 pb-0">
            <apexchart
              type="bar"
              height="255"
              :options="barChartOptions"
              :series="revenueChartData.series"
            ></apexchart>
          </div>
        </div>
      </div>
    </div>

    <!-- TẦNG DƯỚI: GIAO DỊCH GẦN ĐÂY (COL-8) + CƠ CẤU GIAO DỊCH (COL-4) -->
    <div class="row g-3">
      <!-- Cột trái: Giao Dịch Gần Đây (Recent Activities) -->
      <div class="col-lg-8 col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header pb-3 d-flex justify-content-between align-items-center border-bottom">
            <div>
              <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1rem;">Giao Dịch Gần Đây</h5>
              <small class="text-muted">Đơn hàng và yêu cầu báo giá mới nhất cần xử lý</small>
            </div>
            <router-link :to="`${basePath}/don-hang`" class="text-primary fw-medium text-decoration-none d-inline-flex align-items-center" style="font-size: 0.85rem;">
              <span>Xem tất cả</span>
              <i class="bx bx-right-arrow-alt ms-1 fs-5"></i>
            </router-link>
          </div>
          <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
              <thead class="border-bottom">
                <tr>
                  <th class="text-center text-uppercase" style="width: 5%">STT</th>
                  <th class="text-center text-uppercase" style="width: 10%">Phân Loại</th>
                  <th class="text-uppercase" style="width: 22%">Mã & Trạng Thái</th>
                  <th class="text-uppercase" style="width: 30%">Khách Hàng</th>
                  <th class="text-uppercase" style="width: 18%">Thời Gian</th>
                  <th class="text-center text-uppercase" style="width: 15%">Số Tiền</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!recentActivities || recentActivities.length === 0">
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in recentActivities" :key="item.type + item.id">
                  <td class="text-center">
                    <strong>{{ index + 1 }}</strong>
                  </td>
                  <td class="text-center text-nowrap">
                    <span
                      class="fw-bold"
                      style="font-size: 0.85rem;"
                      :class="item.type === 'quote' ? 'text-warning' : 'text-primary'"
                    >
                      {{ item.type_label }}
                    </span>
                  </td>
                  <td>
                    <span class="fw-bold text-dark d-block" style="font-size: 0.85rem;">{{ item.code }}</span>
                    <span class="badge badge-custom mt-1" :class="item.status_class">
                      {{ item.status_label }}
                    </span>
                  </td>
                  <td>
                    <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.85rem;">{{ item.customer_name }}</span>
                    <div class="text-muted" style="font-size: 0.8rem;">
                      <i class="bx bx-phone text-primary me-1"></i>{{ item.customer_phone }}
                    </div>
                  </td>
                  <td>
                    <span class="d-block text-dark fw-medium mb-1" style="font-size: 0.82rem;">
                      <i class="bx bx-calendar-alt text-primary me-1"></i>{{ item.created_at }}
                    </span>
                    <span class="text-muted" style="font-size: 0.75rem;">
                      <i class="bx bx-time-five me-1"></i>{{ item.time_ago }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="fw-bold text-danger" style="font-size: 0.9rem;">
                      {{ formatCurrency(item.amount) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Cột phải: Cơ Cấu Giao Dịch (Donut Chart: Đơn Hàng vs Báo Giá) -->
      <div class="col-lg-4 col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header pb-2 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1rem;">Cơ Cấu Giao Dịch</h5>
            <small class="text-muted">Tỷ lệ đơn hoàn tất, đang xử lý & báo giá</small>
          </div>
          <div class="card-body d-flex flex-column justify-content-between pt-3">
            <div v-if="hasDonutData" class="my-auto text-center">
              <apexchart
                type="donut"
                height="300"
                :options="donutChartOptions"
                :series="distributionChartData.series"
              ></apexchart>
            </div>
            <div v-else class="text-center py-5 my-auto text-muted">
              <i class="bx bx-pie-chart-alt-2 fs-1 text-secondary d-block mb-2"></i>
              <span>Không có dữ liệu</span>
            </div>
            
            <!-- Thống kê chi tiết từng loại giao dịch dưới biểu đồ (3 nhóm: Hoàn tất, Đang xử lý, Báo giá) -->
            <div class="border-top pt-3 mt-2">
              <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.8rem;">
                <div class="d-flex align-items-center">
                  <span class="me-2" style="display: inline-block; width: 13px; height: 13px; border-radius: 3px; background-color: #03c3ec;"></span>
                  <span class="text-dark fw-medium">Đơn Hoàn Tất</span>
                </div>
                <div class="fw-bold text-dark">
                  {{ distributionChartData.counts?.completed || 0 }} đơn <span class="text-muted fw-normal">({{ distributionChartData.percentages?.completed || 0 }}%)</span>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.8rem;">
                <div class="d-flex align-items-center">
                  <span class="me-2" style="display: inline-block; width: 13px; height: 13px; border-radius: 3px; background-color: #696cff;"></span>
                  <span class="text-dark fw-medium">Đơn Đang Xử Lý</span>
                </div>
                <div class="fw-bold text-dark">
                  {{ distributionChartData.counts?.processing || 0 }} đơn <span class="text-muted fw-normal">({{ distributionChartData.percentages?.processing || 0 }}%)</span>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <div class="d-flex align-items-center">
                  <span class="me-2" style="display: inline-block; width: 13px; height: 13px; border-radius: 3px; background-color: #ffab00;"></span>
                  <span class="text-dark fw-medium">Yêu Cầu Báo Giá</span>
                </div>
                <div class="fw-bold text-dark">
                  {{ distributionChartData.counts?.quote || 0 }} yêu cầu <span class="text-muted fw-normal">({{ distributionChartData.percentages?.quote || 0 }}%)</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import apexchart from 'vue3-apexcharts';
import Swal from 'sweetalert2';
import axiosClient from '../../../api/axiosClient';

const route = useRoute();
const basePath = computed(() => '/portal');

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2000,
  timerProgressBar: true
});

// Dropdown state & Date labels
const dropdownRef = ref(null);
const isDropdownOpen = ref(false);

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const closeDropdown = () => {
  isDropdownOpen.value = false;
};

const handleOutsideClick = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isDropdownOpen.value = false;
  }
};

// Date helpers
const toIsoDate = (d) => {
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
};

const todayObj = new Date();
const todayIso = toIsoDate(todayObj);

// Custom date range state
const customStartDate = ref(toIsoDate(new Date(todayObj.getFullYear(), todayObj.getMonth(), 1)));
const customEndDate = ref(todayIso);
const activeStartDate = ref('');
const activeEndDate = ref('');

const formatDisplayDate = (isoStr) => {
  if (!isoStr) return '';
  const parts = isoStr.split('-');
  if (parts.length === 3) {
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
  }
  return isoStr;
};

const formatDisplayRange = (start, end) => {
  if (!start || !end) return '';
  return `${formatDisplayDate(start)} - ${formatDisplayDate(end)}`;
};

const customDaysCount = computed(() => {
  if (!customStartDate.value || !customEndDate.value) return 0;
  const start = new Date(customStartDate.value);
  const end = new Date(customEndDate.value);
  const diffTime = end - start;
  if (diffTime < 0) return 0;
  return Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
});

const selectedDaysCount = computed(() => {
  if (currentPeriod.value !== 'custom') return 0;
  return customDaysCount.value;
});

const isValidCustomRange = computed(() => {
  return customStartDate.value && customEndDate.value && customStartDate.value <= customEndDate.value;
});

const todayFormatted = computed(() => {
  const d = new Date();
  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
});

const yesterdayFormatted = computed(() => {
  const d = new Date();
  d.setDate(d.getDate() - 1);
  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
});

const weekFormatted = computed(() => {
  const d = new Date();
  const day = d.getDay();
  const diffToMon = d.getDate() - day + (day === 0 ? -6 : 1);
  const mon = new Date(d);
  mon.setDate(diffToMon);
  const sun = new Date(mon);
  sun.setDate(mon.getDate() + 6);
  const f = (date) => `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}`;
  return `${f(mon)} - ${f(sun)}`;
});

const monthFormatted = computed(() => {
  const d = new Date();
  return `Tháng ${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
});

const lastMonthFormatted = computed(() => {
  const d = new Date();
  d.setMonth(d.getMonth() - 1);
  return `Tháng ${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
});

const yearFormatted = computed(() => {
  return `Năm ${new Date().getFullYear()}`;
});

// State
const isLoading = ref(false);
const currentPeriod = ref('year');

const periodLabels = {
  today: 'Hôm nay',
  yesterday: 'Hôm qua',
  last_7_days: '7 ngày qua',
  last_30_days: '30 ngày qua',
  week: 'Tuần này',
  month: 'Tháng này',
  last_month: 'Tháng trước',
  year: 'Năm nay',
  all: 'Toàn thời gian',
  custom: 'Tùy chọn khoảng ngày'
};

const currentRangeLabel = computed(() => {
  if (currentPeriod.value === 'custom') {
    return formatDisplayRange(activeStartDate.value || customStartDate.value, activeEndDate.value || customEndDate.value);
  }
  return periodLabels[currentPeriod.value] || 'Năm nay';
});

const chartGranularityLabel = computed(() => {
  switch (currentPeriod.value) {
    case 'today':
      return 'Theo khung giờ hôm nay';
    case 'yesterday':
      return 'Theo khung giờ hôm qua';
    case 'last_7_days':
      return 'Theo 7 ngày gần nhất';
    case 'last_30_days':
      return 'Theo 30 ngày gần nhất';
    case 'week':
      return 'Theo 7 ngày trong tuần';
    case 'month':
      return 'Theo các tuần trong tháng';
    case 'last_month':
      return 'Theo các tuần tháng trước';
    case 'year':
      return `Từ Tháng 01 đến Tháng ${String(new Date().getMonth() + 1).padStart(2, '0')}`;
    case 'all':
      return 'Theo từng năm';
    case 'custom':
      if (customDaysCount.value <= 1) return 'Theo khung giờ trong ngày';
      if (customDaysCount.value <= 31) return `Theo từng ngày (${customDaysCount.value} ngày)`;
      if (customDaysCount.value <= 120) return `Theo từng tuần (${customDaysCount.value} ngày)`;
      return `Theo từng tháng (${customDaysCount.value} ngày)`;
    default:
      return 'Theo kỳ lọc';
  }
});

const selectPreset = (period) => {
  isDropdownOpen.value = false;
  currentPeriod.value = period;
  fetchData();
  Toast.fire({
    icon: 'info',
    title: `Đang lọc dữ liệu: ${periodLabels[period] || period}`
  });
};

const applyCustomRange = () => {
  if (!isValidCustomRange.value) return;
  activeStartDate.value = customStartDate.value;
  activeEndDate.value = customEndDate.value;
  currentPeriod.value = 'custom';
  isDropdownOpen.value = false;
  fetchData();
  Toast.fire({
    icon: 'info',
    title: `Lọc từ ${formatDisplayDate(activeStartDate.value)} đến ${formatDisplayDate(activeEndDate.value)}`
  });
};

const kpis = ref({
  total_sales: 0,
  sales_today: 0,
  sales_growth: 0,
  total_orders: 0,
  orders_today: 0,
  orders_growth: 0,
  total_quotes: 0,
  pending_quotes: 0,
  quotes_today: 0,
  quotes_growth: 0,
  items_need_replenish: 0,
  total_qty_need_replenish: 0,
  open_po_count: 0
});

const revenueChartData = ref({
  categories: [],
  series: [
    { name: 'Doanh Thu Bán Hàng (Tr.đ)', data: [] },
    { name: 'Chi Phí Nhập Kho (Tr.đ)', data: [] }
  ],
  current_period_revenue: 0,
  growth_rate: 0
});

const distributionChartData = ref({
  series: [0, 0, 0],
  labels: ['Đơn Hoàn Tất', 'Đơn Đang Xử Lý', 'Yêu Cầu Báo Giá'],
  percentages: { completed: 0, processing: 0, quote: 0 },
  counts: { completed: 0, processing: 0, quote: 0 }
});

const recentActivities = ref([]);

// Kiểm tra xem Donut chart có dữ liệu hay không
const hasDonutData = computed(() => {
  return distributionChartData.value.series &&
         distributionChartData.value.series.length > 0 &&
         distributionChartData.value.series.some(v => v > 0);
});

// Cấu hình Biểu đồ Cột Doanh Thu & PO (ApexCharts)
const barChartOptions = computed(() => ({
  chart: {
    type: 'bar',
    height: 255,
    parentHeightOffset: 0,
    toolbar: { show: false },
    fontFamily: 'Public Sans, sans-serif'
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '56%',
      borderRadius: 0
    }
  },
  dataLabels: { enabled: false },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  colors: ['#696cff', '#8592a3'],
  xaxis: {
    categories: revenueChartData.value.categories || [],
    labels: {
      rotate: -45,
      rotateAlways: true,
      style: { fontSize: '11px', colors: '#8592a3', fontWeight: 500 },
      trim: false
    },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    labels: {
      formatter: (val) => {
        if (typeof val === 'number') {
          if (val >= 1000) {
            return (val / 1000).toFixed(1).replace('.0', '') + ' Tỷ';
          }
          return (val % 1 === 0 ? val : val.toFixed(1)) + ' Tr';
        }
        return val + ' Tr';
      },
      style: { fontSize: '11px', colors: '#8592a3' }
    }
  },
  fill: { opacity: 1 },
  legend: { show: false },
  grid: {
    borderColor: '#eceef1',
    strokeDashArray: 4,
    padding: { top: 0, right: 10, bottom: 0, left: 10 }
  },
  tooltip: {
    y: {
      formatter: (val) => {
        if (typeof val === 'number') {
          return val.toLocaleString('vi-VN') + ' Triệu VNĐ';
        }
        return val;
      }
    }
  }
}));

// Cấu hình Biểu đồ Tròn Donut Cơ Cấu Giao Dịch (ApexCharts)
const donutChartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 300,
    fontFamily: 'Public Sans, sans-serif'
  },
  labels: distributionChartData.value.labels || ['Đơn Hoàn Tất', 'Đơn Đang Xử Lý', 'Yêu Cầu Báo Giá'],
  colors: ['#03c3ec', '#696cff', '#ffab00'],
  stroke: { width: 3, colors: ['#fff'] },
  legend: { show: false },
  dataLabels: { enabled: false },
  plotOptions: {
    pie: {
      donut: {
        size: '68%',
        labels: {
          show: true,
          value: {
            fontSize: '1.5rem',
            fontWeight: 700,
            color: '#233446',
            offsetY: -6,
            formatter: (val) => val
          },
          name: {
            offsetY: 22,
            fontSize: '0.85rem'
          },
          total: {
            show: true,
            fontSize: '0.8rem',
            color: '#8592a3',
            label: 'Tổng giao dịch',
            formatter: (w) => {
              const totals = w?.globals?.seriesTotals;
              if (totals && totals.length) {
                return totals.reduce((a, b) => a + b, 0);
              }
              return distributionChartData.value.series.reduce((a, b) => a + b, 0);
            }
          }
        }
      }
    }
  },
  tooltip: {
    y: {
      formatter: (val) => val + ' mục'
    }
  }
}));

// Format tiền tệ chuẩn VNĐ
const formatCurrency = (val) => {
  if (!val || isNaN(val)) return '0 ₫';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
};

// Rút gọn tiền tệ (Tr / Tỷ)
const formatShortCurrency = (val) => {
  if (!val || isNaN(val)) return '0 ₫';
  if (val >= 1000000000) {
    return (val / 1000000000).toFixed(1) + ' Tỷ';
  }
  if (val >= 1000000) {
    return (val / 1000000).toFixed(1) + ' Tr';
  }
  return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
};

// Fetch dữ liệu từ API
const fetchData = async (isManual = false) => {
  isLoading.value = true;
  try {
    let url = `/admin/dashboard/stats?period=${currentPeriod.value}`;
    if (currentPeriod.value === 'custom' && activeStartDate.value && activeEndDate.value) {
      url += `&start_date=${activeStartDate.value}&end_date=${activeEndDate.value}`;
    }
    const res = await axiosClient.get(url);
    if (res && res.success && res.data) {
      kpis.value = res.data.kpis || kpis.value;
      if (res.data.revenue_chart) {
        revenueChartData.value = res.data.revenue_chart;
      }
      if (res.data.distribution_chart) {
        distributionChartData.value = res.data.distribution_chart;
      }
      recentActivities.value = res.data.recent_activities || [];

      if (isManual) {
        Toast.fire({
          icon: 'success',
          title: 'Đã làm mới dữ liệu Dashboard thành công!'
        });
      }
    }
  } catch (error) {
    console.error('Lỗi tải dữ liệu Dashboard:', error);
    if (isManual) {
      Toast.fire({
        icon: 'error',
        title: 'Không thể làm mới dữ liệu. Vui lòng thử lại!'
      });
    }
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleOutsideClick);
  fetchData();
});

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
});
</script>

<style scoped>
.kpi-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 8px;
}
.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
}

.kpi-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.5px;
  color: #8592a3;
}

.kpi-value {
  font-size: 1.35rem;
  font-weight: 700;
  color: #566a7f;
  line-height: 1.2;
}

.kpi-subtext {
  font-size: 0.75rem;
}

/* Custom Table header - Đồng bộ chuẩn font-weight 700 và màu sắc với các trang khác */
.table th {
  font-size: 0.75rem;
  letter-spacing: 1px;
  color: #233446 !important;
  font-weight: 700 !important;
  padding-top: 13px;
  padding-bottom: 13px;
}

/* Sneat Active Item styling */
.active-sneat-item {
  background-color: rgba(105, 108, 255, 0.12) !important;
  color: #696cff !important;
  font-weight: 600;
}
.dropdown-item:hover:not(.active-sneat-item) {
  background-color: #f5f5f9;
}
</style>
