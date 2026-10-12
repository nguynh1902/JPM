<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Page: Đồng bộ với chuẩn các trang Admin khác -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
      <div>
        <h4 class="fw-bold mb-1">
          <span class="text-muted fw-light">Chuỗi Cung Ứng /</span> Tổng Quan Dashboard
        </h4>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Chỉ số hoạt động kinh doanh & vận hành chuỗi cung ứng phụ tùng</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <!-- Bộ lọc thời gian -->
        <div class="dropdown">
          <button class="btn btn-white bg-white border text-secondary btn-sm dropdown-toggle shadow-sm px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-calendar me-1 text-primary"></i> {{ periodLabels[currentPeriod] || 'Tháng này' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><a class="dropdown-item" href="javascript:void(0)" @click="changePeriod('today')" :class="{'active': currentPeriod === 'today'}">Hôm nay</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" @click="changePeriod('week')" :class="{'active': currentPeriod === 'week'}">Tuần này</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" @click="changePeriod('month')" :class="{'active': currentPeriod === 'month'}">Tháng này</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" @click="changePeriod('year')" :class="{'active': currentPeriod === 'year'}">Năm nay</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="javascript:void(0)" @click="changePeriod('all')" :class="{'active': currentPeriod === 'all'}">Toàn thời gian</a></li>
          </ul>
        </div>
        <!-- Nút Làm Mới -->
        <button class="btn btn-primary btn-sm shadow-sm px-3" @click="fetchData" :disabled="isLoading">
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
          <div class="col-sm-6 col-12">
            <div class="card border-0 shadow-sm h-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Tổng Doanh Thu</span>
                    <span class="badge badge-custom bg-pastel-primary p-1">
                      <i class="bx bx-dollar-circle fs-6"></i>
                    </span>
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
                  <router-link to="/admin/don-hang" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem chi tiết</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Tổng Đơn Hàng -->
          <div class="col-sm-6 col-12">
            <div class="card border-0 shadow-sm h-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Tổng Đơn Hàng</span>
                    <span class="badge badge-custom bg-pastel-info p-1">
                      <i class="bx bx-shopping-bag fs-6"></i>
                    </span>
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
                  <router-link to="/admin/don-hang" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem chi tiết</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Yêu Cầu Báo Giá -->
          <div class="col-sm-6 col-12">
            <div class="card border-0 shadow-sm h-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Yêu Cầu Báo Giá</span>
                    <span class="badge badge-custom bg-pastel-warning p-1">
                      <i class="bx bx-message-square-dots fs-6"></i>
                    </span>
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
                  <router-link to="/admin/don-hang" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Xem chi tiết</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: Cảnh Báo Gom Hàng PO -->
          <div class="col-sm-6 col-12">
            <div class="card border-0 shadow-sm h-100 kpi-card">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="kpi-label">Cần Gom Hàng PO</span>
                    <span class="badge badge-custom bg-pastel-danger p-1">
                      <i class="bx bx-error-circle fs-6"></i>
                    </span>
                  </div>
                  <div class="kpi-value text-dark mb-1">
                    {{ kpis.items_need_replenish }} <small class="text-muted fw-normal" style="font-size: 0.8rem;">mặt hàng</small>
                  </div>
                  <div class="d-flex align-items-center gap-1 kpi-subtext">
                    <span class="text-muted">
                      Thiếu: <strong class="text-dark">{{ kpis.total_qty_need_replenish }}</strong> cái | <strong class="text-dark">{{ kpis.open_po_count }}</strong> PO mở
                    </span>
                  </div>
                </div>
                <div class="border-top pt-2 mt-3">
                  <router-link to="/admin/don-nhap-hang" class="text-primary fw-medium d-flex align-items-center justify-content-between text-decoration-none" style="font-size: 0.78rem;">
                    <span>Gom đơn ngay</span>
                    <i class="bx bx-right-arrow-alt"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: Biểu Đồ Doanh Thu & Chi Phí PO Lớn -->
      <div class="col-lg-7 col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header pb-2 d-flex justify-content-between align-items-start border-bottom-0">
            <div>
              <span class="kpi-label d-block mb-1">Doanh Thu & Chi Phí Nhập Kho (PO)</span>
              <div class="d-flex align-items-baseline gap-2">
                <h4 class="fw-bold text-dark mb-0">{{ formatCurrency(revenueChartData.current_period_revenue) }}</h4>
                <span :class="revenueChartData.growth_rate >= 0 ? 'text-success' : 'text-danger'" class="fw-semibold" style="font-size: 0.82rem;">
                  <i class="bx" :class="revenueChartData.growth_rate >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt'"></i>
                  {{ revenueChartData.growth_rate >= 0 ? '+' : '' }}{{ revenueChartData.growth_rate }}%
                </span>
              </div>
            </div>
            <!-- Legend góc phải thanh lịch -->
            <div class="d-flex align-items-center gap-3" style="font-size: 0.75rem;">
              <div class="d-flex align-items-center gap-1">
                <span class="badge rounded-circle p-1" style="background-color: #696cff;"></span>
                <span class="text-muted">Doanh thu bán</span>
              </div>
              <div class="d-flex align-items-center gap-1">
                <span class="badge rounded-circle p-1" style="background-color: #8592a3;"></span>
                <span class="text-muted">Chi phí PO</span>
              </div>
            </div>
          </div>
          <div class="card-body pt-0 px-2 pb-2">
            <apexchart
              type="bar"
              height="260"
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
            <router-link to="/admin/don-hang" class="btn btn-sm btn-outline-primary">
              Xem tất cả <i class="bx bx-right-arrow-alt ms-1"></i>
            </router-link>
          </div>
          <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center text-uppercase" style="width: 5%">STT</th>
                  <th class="text-center text-uppercase" style="width: 14%">Phân Loại</th>
                  <th class="text-uppercase" style="width: 22%">Mã & Trạng Thái</th>
                  <th class="text-uppercase" style="width: 28%">Khách Hàng</th>
                  <th class="text-uppercase" style="width: 16%">Thời Gian</th>
                  <th class="text-end text-uppercase" style="width: 15%">Số Tiền</th>
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
                  <td class="text-center">
                    <span class="badge badge-custom" :class="item.type === 'quote' ? 'bg-pastel-info' : 'bg-pastel-warning'">
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
                  <td class="text-end">
                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">
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
            <small class="text-muted">Tỷ lệ đơn hàng trực tiếp & yêu cầu báo giá</small>
          </div>
          <div class="card-body d-flex flex-column justify-content-between pt-3">
            <div v-if="hasDonutData" class="my-auto">
              <apexchart
                type="donut"
                height="230"
                :options="donutChartOptions"
                :series="distributionChartData.series"
              ></apexchart>
            </div>
            <div v-else class="text-center py-5 my-auto text-muted">
              <i class="bx bx-pie-chart-alt-2 fs-1 text-secondary d-block mb-2"></i>
              <span>Không có dữ liệu</span>
            </div>
            
            <!-- Thống kê chi tiết từng loại giao dịch dưới biểu đồ -->
            <div class="border-top pt-3 mt-2">
              <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.8rem;">
                <div class="d-flex align-items-center">
                  <span class="badge rounded-circle p-1 me-2" style="background-color: #696cff;"></span>
                  <span class="text-dark fw-medium">Đơn Hàng Trực Tiếp</span>
                </div>
                <div class="fw-bold text-dark">
                  {{ distributionChartData.counts.order || 0 }} đơn <span class="text-muted fw-normal">({{ distributionChartData.percentages.order || 0 }}%)</span>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <div class="d-flex align-items-center">
                  <span class="badge rounded-circle p-1 me-2" style="background-color: #ffab00;"></span>
                  <span class="text-dark fw-medium">Yêu Cầu Báo Giá</span>
                </div>
                <div class="fw-bold text-dark">
                  {{ distributionChartData.counts.quote || 0 }} yêu cầu <span class="text-muted fw-normal">({{ distributionChartData.percentages.quote || 0 }}%)</span>
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
import { ref, onMounted, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import axiosClient from '../../../api/axiosClient';

// State
const isLoading = ref(false);
const currentPeriod = ref('month');

const periodLabels = {
  today: 'Hôm nay',
  week: 'Tuần này',
  month: 'Tháng này',
  year: 'Năm nay',
  all: 'Toàn thời gian'
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
  categories: ['Tháng 05', 'Tháng 06', 'Tháng 07', 'Tháng 08', 'Tháng 09', 'Tháng 10'],
  series: [
    { name: 'Doanh Thu Bán Hàng (Tr.đ)', data: [0, 0, 0, 0, 0, 0] },
    { name: 'Chi Phí Nhập Kho PO (Tr.đ)', data: [0, 0, 0, 0, 0, 0] }
  ],
  current_period_revenue: 0,
  growth_rate: 0
});

const distributionChartData = ref({
  series: [0, 0],
  labels: ['Đơn Hàng Trực Tiếp', 'Yêu Cầu Báo Giá'],
  percentages: { order: 0, quote: 0 },
  counts: { order: 0, quote: 0 }
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
    height: 260,
    toolbar: { show: false },
    fontFamily: 'Public Sans, sans-serif'
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '36%',
      borderRadius: 4
    }
  },
  dataLabels: { enabled: false },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  colors: ['#696cff', '#8592a3'],
  xaxis: {
    categories: revenueChartData.value.categories || [],
    labels: {
      style: { fontSize: '12px', colors: '#8592a3' }
    },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    labels: {
      formatter: (val) => {
        if (typeof val === 'number') {
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
    fontFamily: 'Public Sans, sans-serif'
  },
  labels: distributionChartData.value.labels || ['Đơn Hàng Trực Tiếp', 'Yêu Cầu Báo Giá'],
  colors: ['#696cff', '#ffab00'],
  stroke: { width: 3, colors: ['#fff'] },
  legend: { show: false },
  dataLabels: { enabled: false },
  plotOptions: {
    pie: {
      donut: {
        size: '72%',
        labels: {
          show: true,
          value: {
            fontSize: '1.25rem',
            fontWeight: 600,
            color: '#566a7f',
            offsetY: -5,
            formatter: (val) => val
          },
          name: {
            offsetY: 20,
            fontSize: '0.82rem'
          },
          total: {
            show: true,
            fontSize: '0.75rem',
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

// Đổi kỳ thời gian
const changePeriod = (period) => {
  currentPeriod.value = period;
  fetchData();
};

// Fetch dữ liệu từ API
const fetchData = async () => {
  isLoading.value = true;
  try {
    const res = await axiosClient.get(`/admin/dashboard/stats?period=${currentPeriod.value}`);
    if (res && res.success && res.data) {
      kpis.value = res.data.kpis || kpis.value;
      if (res.data.revenue_chart) {
        revenueChartData.value = res.data.revenue_chart;
      }
      if (res.data.distribution_chart) {
        distributionChartData.value = res.data.distribution_chart;
      }
      recentActivities.value = res.data.recent_activities || [];
    }
  } catch (error) {
    console.error('Lỗi tải dữ liệu Dashboard:', error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchData();
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

/* Badge Pastel chuẩn theo JPM Master Standard & Sneat */
.badge-custom {
  padding: 5px 9px;
  font-size: 0.72rem;
  font-weight: 600;
  border-radius: 4px;
  letter-spacing: 0.3px;
}

.bg-pastel-warning {
  background-color: rgba(255, 171, 0, 0.12) !important;
  color: #ffab00 !important;
}

.bg-pastel-info {
  background-color: rgba(3, 195, 236, 0.12) !important;
  color: #03c3ec !important;
}

.bg-pastel-success {
  background-color: rgba(113, 221, 55, 0.12) !important;
  color: #71dd37 !important;
}

.bg-pastel-danger {
  background-color: rgba(255, 62, 29, 0.12) !important;
  color: #ff3e1d !important;
}

.bg-pastel-primary {
  background-color: rgba(105, 108, 255, 0.12) !important;
  color: #696cff !important;
}

.bg-pastel-secondary {
  background-color: rgba(133, 146, 163, 0.12) !important;
  color: #8592a3 !important;
}

/* Custom Table header */
.table th {
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #566a7f !important;
  font-weight: 600 !important;
  padding-top: 12px;
  padding-bottom: 12px;
}
</style>
