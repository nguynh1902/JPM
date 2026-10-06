<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-lg-8 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-7">
              <div class="card-body">
                <h5 class="card-title text-primary">Congratulations John! 🎉</h5>
                <p class="mb-4">You have done <span class="fw-bold">72%</span> more sales today. Check your new badge in your profile.</p>
                <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
              </div>
            </div>
            <div class="col-sm-5 text-center text-sm-left">
              <div class="card-body pb-0 px-0 px-md-4">
                <img src="/admin/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img src="/admin/img/icons/unicons/chart-success.png" alt="chart success" class="rounded" />
                  </div>
                  <div class="dropdown">
                    <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a class="dropdown-item" href="javascript:void(0);">View More</a>
                      <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Profit</span>
                <h3 class="card-title mb-2">$12,628</h3>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img src="/admin/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded" />
                  </div>
                  <div class="dropdown">
                    <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a class="dropdown-item" href="javascript:void(0);">View More</a>
                      <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                  </div>
                </div>
                <span>Sales</span>
                <h3 class="card-title text-nowrap mb-1">$4,679</h3>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Revenue -->
      <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
          <div class="row row-bordered g-0">
            <div class="col-md-8">
              <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
              <div class="px-2">
                <apexchart type="bar" height="300" :options="totalRevenueOptions" :series="totalRevenueSeries"></apexchart>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card-body">
                <div class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">2022</button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a class="dropdown-item" href="javascript:void(0);">2021</a>
                      <a class="dropdown-item" href="javascript:void(0);">2020</a>
                      <a class="dropdown-item" href="javascript:void(0);">2019</a>
                    </div>
                  </div>
                </div>
              </div>
              <apexchart type="radialBar" height="240" :options="growthOptions" :series="growthSeries"></apexchart>
              <div class="text-center fw-semibold pt-3 mb-2">62% Company Growth</div>
              <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                <div class="d-flex">
                  <div class="me-2"><span class="badge bg-label-primary p-2"><i class="bx bx-dollar text-primary"></i></span></div>
                  <div class="d-flex flex-column"><small>2022</small><h6 class="mb-0">$32.5k</h6></div>
                </div>
                <div class="d-flex">
                  <div class="me-2"><span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span></div>
                  <div class="d-flex flex-column"><small>2021</small><h6 class="mb-0">$41.2k</h6></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
        <div class="row">
          <div class="col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0"><img src="/admin/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded" /></div>
                  <div class="dropdown">
                    <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a class="dropdown-item" href="javascript:void(0);">View More</a>
                      <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                  </div>
                </div>
                <span class="d-block mb-1">Payments</span>
                <h3 class="card-title text-nowrap mb-2">$2,456</h3>
                <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
              </div>
            </div>
          </div>
          <div class="col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0"><img src="/admin/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded" /></div>
                  <div class="dropdown">
                    <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                    <div class="dropdown-menu" ><a class="dropdown-item" href="javascript:void(0);">View More</a></div>
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Transactions</span>
                <h3 class="card-title mb-2">$14,857</h3>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
              </div>
            </div>
          </div>
          <div class="col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                  <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                    <div class="card-title">
                      <h5 class="text-nowrap mb-2">Profile Report</h5>
                      <span class="badge bg-label-warning rounded-pill">Year 2021</span>
                    </div>
                    <div class="mt-sm-auto">
                      <small class="text-success text-nowrap fw-semibold"><i class="bx bx-chevron-up"></i> 68.2%</small>
                      <h3 class="mb-0">$84,686k</h3>
                    </div>
                  </div>
                  <apexchart type="line" height="80" :options="profileReportOptions" :series="profileReportSeries"></apexchart>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Order Statistics -->
      <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between pb-0">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Order Statistics</h5>
              <small class="fw-semibold text-secondary">42.82k Total Sales</small>
            </div>
            <div class="dropdown">
              <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                <a class="dropdown-item" href="javascript:void(0);">Share</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex flex-column align-items-center gap-1">
                <h2 class="mb-2">8,258</h2>
                <span>Total Orders</span>
              </div>
              <apexchart type="donut" height="165" width="130" :options="orderStatOptions" :series="orderStatSeries"></apexchart>
            </div>
            <ul class="p-0 m-0">
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-primary"><i class="bx bx-mobile-alt"></i></span></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><h6 class="mb-0">Electronic</h6><small class="fw-semibold text-secondary">Mobile, Earbuds, TV</small></div>
                  <div class="user-progress"><small class="fw-semibold">82.5k</small></div>
                </div>
              </li>
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-success"><i class="bx bx-closet"></i></span></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><h6 class="mb-0">Fashion</h6><small class="fw-semibold text-secondary">T-shirt, Jeans, Shoes</small></div>
                  <div class="user-progress"><small class="fw-semibold">23.8k</small></div>
                </div>
              </li>
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><h6 class="mb-0">Decor</h6><small class="fw-semibold text-secondary">Fine Art, Dining</small></div>
                  <div class="user-progress"><small class="fw-semibold">849k</small></div>
                </div>
              </li>
              <li class="d-flex">
                <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-football"></i></span></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><h6 class="mb-0">Sports</h6><small class="fw-semibold text-secondary">Football, Cricket Kit</small></div>
                  <div class="user-progress"><small class="fw-semibold">99</small></div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Expense Overview -->
      <div class="col-md-6 col-lg-4 order-1 mb-4">
        <div class="card h-100">
          <div class="card-header">
            <ul class="nav nav-pills" role="tablist">
              <li class="nav-item"><button type="button" class="nav-link active" role="tab">Income</button></li>
              <li class="nav-item"><button type="button" class="nav-link" role="tab">Expenses</button></li>
              <li class="nav-item"><button type="button" class="nav-link" role="tab">Profit</button></li>
            </ul>
          </div>
          <div class="card-body px-0">
            <div class="d-flex p-4 pt-3">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/admin/img/icons/unicons/wallet.png" alt="User" />
              </div>
              <div>
                <small class="text-muted d-block">Total Balance</small>
                <div class="d-flex align-items-center">
                  <h6 class="mb-0 me-1">$459.10</h6>
                  <small class="text-success fw-semibold"><i class="bx bx-chevron-up"></i> 42.9%</small>
                </div>
              </div>
            </div>
            <apexchart type="area" height="215" :options="incomeOptions" :series="incomeSeries"></apexchart>
            <div class="d-flex justify-content-center pt-4 gap-2">
              <div class="flex-shrink-0">
                <apexchart type="radialBar" width="60" height="60" :options="weeklyExpOptions" :series="weeklyExpSeries"></apexchart>
              </div>
              <div>
                <p class="mb-n1 mt-1">Expenses This Week</p>
                <small class="fw-semibold text-secondary">$39 less than last week</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Transactions -->
      <div class="col-md-6 col-lg-4 order-2 mb-4">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Transactions</h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="p-0 m-0">
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><img src="/admin/img/icons/unicons/paypal.png" alt="User" class="rounded" /></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><small class="text-muted d-block mb-1">Paypal</small><h6 class="mb-0">Send money</h6></div>
                  <div class="user-progress d-flex align-items-center gap-1"><h6 class="mb-0">+82.6</h6><span class="fw-semibold text-secondary">USD</span></div>
                </div>
              </li>
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><img src="/admin/img/icons/unicons/wallet.png" alt="User" class="rounded" /></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><small class="text-muted d-block mb-1">Wallet</small><h6 class="mb-0">Mac D</h6></div>
                  <div class="user-progress d-flex align-items-center gap-1"><h6 class="mb-0">+270.69</h6><span class="fw-semibold text-secondary">USD</span></div>
                </div>
              </li>
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><img src="/admin/img/icons/unicons/chart.png" alt="User" class="rounded" /></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><small class="text-muted d-block mb-1">Transfer</small><h6 class="mb-0">Refund</h6></div>
                  <div class="user-progress d-flex align-items-center gap-1"><h6 class="mb-0">+637.91</h6><span class="fw-semibold text-secondary">USD</span></div>
                </div>
              </li>
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><img src="/admin/img/icons/unicons/cc-success.png" alt="User" class="rounded" /></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><small class="text-muted d-block mb-1">Credit Card</small><h6 class="mb-0">Ordered Food</h6></div>
                  <div class="user-progress d-flex align-items-center gap-1"><h6 class="mb-0">-838.71</h6><span class="fw-semibold text-secondary">USD</span></div>
                </div>
              </li>
              <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3"><img src="/admin/img/icons/unicons/wallet.png" alt="User" class="rounded" /></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><small class="text-muted d-block mb-1">Wallet</small><h6 class="mb-0">Starbucks</h6></div>
                  <div class="user-progress d-flex align-items-center gap-1"><h6 class="mb-0">+203.33</h6><span class="fw-semibold text-secondary">USD</span></div>
                </div>
              </li>
              <li class="d-flex">
                <div class="avatar flex-shrink-0 me-3"><img src="/admin/img/icons/unicons/cc-warning.png" alt="User" class="rounded" /></div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2"><small class="text-muted d-block mb-1">Mastercard</small><h6 class="mb-0">Ordered Food</h6></div>
                  <div class="user-progress d-flex align-items-center gap-1"><h6 class="mb-0">-92.45</h6><span class="fw-semibold text-secondary">USD</span></div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";

const primary      = "#696cff";
const secondary    = "#8592a3";
const info         = "#03c3ec";
const success      = "#71dd37";
const warning      = "#ffab00";
const cardColor    = "#fff";
const headingColor = "#233446";
const axisColor    = "#a1acb8";
const borderColor  = "#eceef1";

export default {
  name: "DashboardAdmin",
  components: {
    apexchart: VueApexCharts
  },
  data() {
    return {
      // 1. Total Revenue - Stacked Bar
      totalRevenueSeries: [
        { name: "2021", data: [18, 7, 15, 29, 18, 12, 9] },
        { name: "2020", data: [-13, -18, -9, -14, -5, -17, -15] }
      ],
      totalRevenueOptions: {
        chart: { height: 300, stacked: true, type: "bar", toolbar: { show: false } },
        plotOptions: {
          bar: { horizontal: false, columnWidth: "33%", borderRadius: 12, startingShape: "rounded", endingShape: "rounded" }
        },
        colors: [primary, info],
        dataLabels: { enabled: false },
        stroke: { curve: "smooth", width: 6, lineCap: "round", colors: [cardColor] },
        legend: {
          show: true, horizontalAlign: "left", position: "top",
          markers: { height: 8, width: 8, radius: 12, offsetX: -3 },
          labels: { colors: axisColor },
          itemMargin: { horizontal: 10 }
        },
        grid: { borderColor: borderColor, padding: { top: 0, bottom: -8, left: 20, right: 20 } },
        xaxis: {
          categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
          labels: { style: { fontSize: "13px", colors: axisColor } },
          axisTicks: { show: false }, axisBorder: { show: false }
        },
        yaxis: { labels: { style: { fontSize: "13px", colors: axisColor } } },
        states: { hover: { filter: { type: "none" } }, active: { filter: { type: "none" } } }
      },

      // 2. Growth - RadialBar
      growthSeries: [78],
      growthOptions: {
        chart: { height: 240, type: "radialBar" },
        labels: ["Growth"],
        plotOptions: {
          radialBar: {
            size: 150, offsetY: 10, startAngle: -150, endAngle: 150,
            hollow: { size: "55%" },
            track: { background: cardColor, strokeWidth: "100%" },
            dataLabels: {
              name: { offsetY: 15, color: headingColor, fontSize: "15px", fontWeight: "600", fontFamily: "Public Sans" },
              value: { offsetY: -25, color: headingColor, fontSize: "22px", fontWeight: "500", fontFamily: "Public Sans" }
            }
          }
        },
        colors: [primary],
        fill: {
          type: "gradient",
          gradient: { shade: "dark", shadeIntensity: 0.5, gradientToColors: [primary], inverseColors: true, opacityFrom: 1, opacityTo: 0.6, stops: [30, 70, 100] }
        },
        stroke: { dashArray: 5 },
        grid: { padding: { top: -35, bottom: -10 } },
        states: { hover: { filter: { type: "none" } }, active: { filter: { type: "none" } } }
      },

      // 3. Profile Report Sparkline
      profileReportSeries: [{ data: [110, 270, 145, 245, 205, 285] }],
      profileReportOptions: {
        chart: {
          height: 80, type: "line", toolbar: { show: false },
          dropShadow: { enabled: true, top: 10, left: 5, blur: 3, color: warning, opacity: 0.15 },
          sparkline: { enabled: true }
        },
        grid: { show: false, padding: { right: 8 } },
        colors: [warning],
        dataLabels: { enabled: false },
        stroke: { width: 5, curve: "smooth" },
        xaxis: { show: false, lines: { show: false }, labels: { show: false }, axisBorder: { show: false } },
        yaxis: { show: false }
      },

      // 4. Order Statistics - Donut
      orderStatSeries: [85, 15, 50, 50],
      orderStatOptions: {
        chart: { height: 165, width: 130, type: "donut" },
        labels: ["Electronic", "Sports", "Decor", "Fashion"],
        colors: [primary, secondary, info, success],
        stroke: { width: 5, colors: [cardColor] },
        dataLabels: { enabled: false },
        legend: { show: false },
        grid: { padding: { top: 0, bottom: 0, right: 15 } },
        plotOptions: {
          pie: {
            donut: {
              size: "75%",
              labels: {
                show: true,
                value: { fontSize: "1.5rem", fontFamily: "Public Sans", color: headingColor, offsetY: -15, formatter: (val) => parseInt(val) + "%" },
                name: { offsetY: 20, fontFamily: "Public Sans" },
                total: { show: true, fontSize: "0.8125rem", color: axisColor, label: "Weekly", formatter: () => "38%" }
              }
            }
          }
        }
      },

      // 5. Income - Area Chart
      incomeSeries: [{ data: [24, 21, 30, 22, 42, 26, 35, 29] }],
      incomeOptions: {
        chart: { height: 215, parentHeightOffset: 0, toolbar: { show: false }, type: "area" },
        dataLabels: { enabled: false },
        stroke: { width: 2, curve: "smooth" },
        legend: { show: false },
        markers: {
          size: 6, colors: "transparent", strokeColors: "transparent", strokeWidth: 4,
          discrete: [{ fillColor: cardColor, seriesIndex: 0, dataPointIndex: 7, strokeColor: primary, strokeWidth: 2, size: 6, radius: 8 }],
          hover: { size: 7 }
        },
        colors: [primary],
        fill: { type: "gradient", gradient: { shade: "light", shadeIntensity: 0.6, opacityFrom: 0.5, opacityTo: 0.25, stops: [0, 95, 100] } },
        grid: { borderColor: borderColor, strokeDashArray: 3, padding: { top: -20, bottom: -8, left: -10, right: 8 } },
        xaxis: {
          categories: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
          axisBorder: { show: false }, axisTicks: { show: false },
          labels: { show: true, style: { fontSize: "13px", colors: axisColor } }
        },
        yaxis: { labels: { show: false }, min: 10, max: 50, tickAmount: 4 }
      },

      // 6. Weekly Expenses - Mini RadialBar
      weeklyExpSeries: [65],
      weeklyExpOptions: {
        chart: { width: 60, height: 60, type: "radialBar" },
        plotOptions: {
          radialBar: {
            startAngle: 0, endAngle: 360, strokeWidth: "8",
            hollow: { margin: 2, size: "45%" },
            track: { strokeWidth: "50%", background: borderColor },
            dataLabels: {
              show: true,
              name: { show: false },
              value: { formatter: (val) => "$" + parseInt(val), offsetY: 5, color: "#697a8d", fontSize: "13px", show: true }
            }
          }
        },
        fill: { type: "solid", colors: [primary] },
        stroke: { lineCap: "round" },
        grid: { padding: { top: -10, bottom: -15, left: -10, right: -10 } },
        states: { hover: { filter: { type: "none" } }, active: { filter: { type: "none" } } }
      }
    };
  }
};
</script>
