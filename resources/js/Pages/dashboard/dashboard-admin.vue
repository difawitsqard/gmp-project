<template>
    <Head title="Dasbor" />

    <div>
        <layout-header></layout-header>
        <layout-sidebar></layout-sidebar>

        <div class="page-wrapper">
            <div class="content">
                <div
                    class="welcome d-lg-flex align-items-center justify-content-between"
                >
                    <div class="d-flex align-items-center welcome-text">
                        <h4 class="d-flex align-items-center">
                            Hai, Selamat datang {{ $page.props.auth.user.name }}
                        </h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData.totalCustomers
                                    }}
                                </h4>
                                <h5>Total Pelanggan</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="users"></vue-feather>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count das1">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData.pendingPayment
                                    }}
                                </h4>
                                <h5>Menunggu Pembayaran</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="clock"></vue-feather>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count das2">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData
                                            .pendingConfirmation
                                    }}
                                </h4>
                                <h5>Menunggu Konfirmasi</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="alert-circle"></vue-feather>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData
                                            .completedOrders
                                    }}
                                </h4>
                                <h5>Pesanan Selesai</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="check-circle"></vue-feather>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second row - Charts -->
                <div class="row">
                    <!-- Orders Chart -->
                    <div
                        class="col-xl-6 col-xxl-5 col-xxl-2 d-flex align-items-stretch"
                    >
                        <div class="card w-100">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-start justify-content-between mb-3"
                                >
                                    <div class="">
                                        <h5
                                            class="mb-0 fw-bold"
                                            style="color: #17ad37"
                                        >
                                            {{ totalOrders }}
                                        </h5>
                                        <p class="mb-0">Pesanan Selesai</p>
                                    </div>
                                </div>
                                <div class="chart-container2">
                                    <apexchart
                                        height="120"
                                        type="area"
                                        :options="ordersChartOptions"
                                        :series="ordersChartSeries"
                                    ></apexchart>
                                </div>
                                <div class="text-center">
                                    <p class="mb-0 font-12">
                                        <span class="fw-bold">{{
                                            formattedCurrentMonth
                                        }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="col-xl-6 col-xxl-7 d-flex align-items-stretch">
                        <div class="card w-100">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-start justify-content-between mb-3"
                                >
                                    <div class="">
                                        <h5
                                            class="mb-1 fw-semibold d-flex align-content-center"
                                        >
                                            <small class="fs-6 fw-medium me-1"
                                                >Rp</small
                                            >
                                            <span style="color: #0d6efd">
                                                {{
                                                    $helpers.formatRupiah(
                                                        totalRevenue
                                                    )
                                                }}
                                            </span>
                                        </h5>
                                        <p class="mb-0">Total Pendapatan</p>
                                    </div>
                                </div>
                                <div class="chart-container2">
                                    <apexchart
                                        height="120"
                                        type="area"
                                        :options="revenueChartOptions"
                                        :series="revenueChartSeries"
                                    ></apexchart>
                                </div>
                                <div class="text-center">
                                    <p class="mb-0 font-12">
                                        <span class="fw-bold">{{
                                            formattedCurrentMonth
                                        }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import VueApexCharts from "vue3-apexcharts";

export default {
    components: {
        Head,
        apexchart: VueApexCharts,
    },
    computed: {
        // Current date utilities with dayjs
        currentDate() {
            return dayjs();
        },

        formattedCurrentMonth() {
            return this.currentDate.format("MMMM YYYY");
        },

        // Get days in current month
        daysInMonth() {
            return this.currentDate.daysInMonth();
        },

        // Generate array with all days of month (1-30/31)
        allDaysInMonth() {
            return Array.from({ length: this.daysInMonth }, (_, i) => i + 1);
        },

        // Process orders data to include all days of month
        processedOrdersData() {
            const orderData =
                this.$page.props.dashboardData.orderDailyCurrentMonth || [];
            const result = Array(this.daysInMonth).fill(0);

            // Fill in actual data for days with orders
            orderData.forEach((item) => {
                const day = dayjs(item.date).date();
                result[day - 1] = parseInt(item.total_orders || 0);
            });

            return result;
        },

        // Process revenue data to include all days of month
        processedRevenueData() {
            const orderData =
                this.$page.props.dashboardData.orderDailyCurrentMonth || [];
            const result = Array(this.daysInMonth).fill(0);

            // Fill in actual data for days with revenue
            orderData.forEach((item) => {
                const day = dayjs(item.date).date();
                result[day - 1] = parseFloat(item.total_revenue || 0);
            });

            return result;
        },

        // Calculate total orders
        totalOrders() {
            return this.processedOrdersData.reduce((sum, val) => sum + val, 0);
        },

        // Calculate total revenue
        totalRevenue() {
            return this.processedRevenueData.reduce((sum, val) => sum + val, 0);
        },

        // Orders Chart Options
        ordersChartOptions() {
            return {
                chart: {
                    height: 120,
                    type: "area",
                    sparkline: {
                        enabled: true,
                    },
                    zoom: {
                        enabled: false,
                    },
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                        easing: "easeinout",
                        speed: 800,
                    },
                },
                grid: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    width: 1.5,
                    curve: "smooth",
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "dark",
                        gradientToColors: ["#17ad37"],
                        shadeIntensity: 1,
                        type: "vertical",
                        opacityFrom: 0.7,
                        opacityTo: 0.0,
                    },
                },
                colors: ["#17ad37"],
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: false,
                    },
                    x: {
                        show: true,
                        formatter: (val) => {
                            // Format date: 1 Januari
                            return `${val} ${this.currentDate.format("MMMM")}`;
                        },
                    },
                    y: {
                        title: {
                            formatter: function () {
                                return "";
                            },
                        },
                        formatter: function (val) {
                            return val.toLocaleString() + " Pesanan";
                        },
                    },
                    marker: {
                        show: false,
                    },
                },
                xaxis: {
                    categories: this.allDaysInMonth,
                    labels: {
                        show: false,
                    },
                },
                yaxis: {
                    labels: {
                        show: false,
                    },
                },
            };
        },

        // Orders Chart Series
        ordersChartSeries() {
            return [
                {
                    name: "Pesanan",
                    data: this.processedOrdersData,
                },
            ];
        },

        // Revenue Chart Options
        revenueChartOptions() {
            return {
                chart: {
                    height: 120,
                    type: "area",
                    sparkline: {
                        enabled: true,
                    },
                    zoom: {
                        enabled: false,
                    },
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                        easing: "easeinout",
                        speed: 800,
                    },
                },
                grid: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    width: 1.5,
                    curve: "smooth",
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "dark",
                        gradientToColors: ["#0d6efd"],
                        shadeIntensity: 1,
                        type: "vertical",
                        opacityFrom: 0.7,
                        opacityTo: 0.0,
                    },
                },
                colors: ["#0d6efd"],
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: false,
                    },
                    x: {
                        show: true,
                        formatter: (val) => {
                            // Format date: 1 Januari
                            return `${val} ${this.currentDate.format("MMMM")}`;
                        },
                    },
                    y: {
                        title: {
                            formatter: function () {
                                return "";
                            },
                        },
                        formatter: (val, opts) => {
                            const index = opts.dataPointIndex;
                            const totalOrders = this.processedOrdersData[index];

                            return (
                                totalOrders.toLocaleString() +
                                " Pesanan<br>Rp " +
                                this.$helpers.formatRupiah(val)
                            );
                        },
                    },
                    marker: {
                        show: false,
                    },
                },
                xaxis: {
                    categories: this.allDaysInMonth,
                    labels: {
                        show: false,
                    },
                },
                yaxis: {
                    labels: {
                        show: false,
                    },
                },
            };
        },

        // Revenue Chart Series
        revenueChartSeries() {
            return [
                {
                    name: "Pendapatan",
                    data: this.processedRevenueData,
                },
            ];
        },
    },
};
</script>
