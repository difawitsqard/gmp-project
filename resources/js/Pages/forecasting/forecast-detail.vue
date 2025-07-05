<template>
    <Head title="Detail Prediksi" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>

    <div class="page-wrapper sales-list">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Detail Prediksi</h4>
                        <h6 class="fw-bold">#{{ forecast.id }}</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            ref="collapseHeader"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Collapse"
                            @click="toggleCollapse"
                        >
                            <i
                                data-feather="chevron-up"
                                class="feather-chevron-up"
                            ></i>
                        </a>
                    </li>
                </ul>
                <div class="page-btn"></div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="forecast-details">
                        <h5 class="mb-3">
                            {{ formatDate(forecast.created_at) }}
                        </h5>

                        <div class="mb-1">
                            <div class="d-flex">
                                <strong class="me-2">Frekuensi</strong>
                                <span>{{ frequencyLabel }}</span>
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex">
                                <strong class="me-2">Horizon</strong>
                                <span>{{ forecast.horizon }} periode</span>
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex">
                                <strong class="me-2">Periode Data</strong>
                                <span
                                    >{{ formatDate(forecast.input_start_date) }}
                                    s/d
                                    {{
                                        formatDate(forecast.input_end_date)
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tampilkan chart jika produk dipilih -->
            <div class="card">
                <div
                    class="card-header d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0">
                        <select
                            class="form-select"
                            v-model="productId"
                            @change="changeProduct"
                        >
                            <option
                                v-for="analyses in forecast.analyses"
                                :key="analyses.id"
                                :value="analyses.product_id"
                            >
                                {{ getProductName(analyses.product_id) }}
                            </option>
                        </select>
                    </h5>
                    <div class="chart-legend d-flex gap-3">
                        <div class="legend-item">
                            <span
                                class="legend-color"
                                style="background-color: #2e93fa"
                            ></span>
                            <span>Data Input</span>
                        </div>
                        <div class="legend-item">
                            <span
                                class="legend-color"
                                style="background-color: #ff9800"
                            ></span>
                            <span>Prediksi</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div
                        class="chart-container mb-3"
                        style="height: 400px"
                        v-if="selectedProductId && chartData"
                    >
                        <apexchart
                            v-if="chartData"
                            type="line"
                            :options="chartOptions"
                            :series="chartSeries"
                            height="100%"
                        ></apexchart>
                    </div>
                    <div class="analysis-item mb-3">
                        <h6 class="fw-bold">Analisis Data Historis</h6>
                        <p>{{ selectedAnalysis.historical_analysis }}</p>
                    </div>

                    <div
                        class="analysis-item mb-3"
                        v-if="selectedAnalysis.forecast_analysis"
                    >
                        <h6 class="fw-bold">Analisis Hasil Forecast</h6>
                        <p>{{ selectedAnalysis.forecast_analysis }}</p>
                    </div>

                    <div class="analysis-item mb-3">
                        <h6 class="fw-bold">Rekomendasi</h6>
                        <p>{{ selectedAnalysis.recommendations }}</p>
                    </div>

                    <div
                        class="analysis-item mb-3"
                        v-if="selectedAnalysis.data_quality"
                    >
                        <h6 class="fw-bold">Kualitas Data</h6>
                        <p>{{ selectedAnalysis.data_quality }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link, router } from "@inertiajs/vue3";
import { skipPreloadNextRequest } from "@/composables/usePreloadControl";

export default {
    name: "ForecastDetail",
    props: {
        forecast: {
            required: true,
            type: Object,
        },
        selectedProductId: {
            required: false,
            type: [Number, String, null],
            default: null,
        },
        timeSeriesData: {
            required: false,
            type: [Object, null],
            default: null,
        },
    },
    components: {
        Head,
        Link,
    },
    data() {
        return {
            productId: this.selectedProductId,
        };
    },
    methods: {
        formatDate(date, format = "D MMM YYYY") {
            if (!date) return "-";
            return dayjs(date).format(format);
        },
        getProductName(productId) {
            const result = this.forecast.analyses.find(
                (r) => r.product_id == productId
            );
            if (result && result.product) {
                return result.product.name;
            }
            return `Produk #${productId}`;
        },
        changeProduct() {
            // Gunakan Inertia untuk memuat ulang hanya data yang diperlukan
            router.visit(route("forecasting.show", this.forecast.id), {
                only: ["timeSeriesData", "selectedProductId"],
                data: { product_id: this.productId },
                preserveState: true,
                preserveScroll: true,
            });
        },
    },
    computed: {
        frequencyLabel() {
            const labels = {
                D: "Harian",
                W: "Mingguan",
                M: "Bulanan",
            };
            return labels[this.forecast.frequency] || this.forecast.frequency;
        },
        selectedResult() {
            return this.forecast.results.find(
                (r) => r.product_id == this.productId
            );
        },
        selectedProductName() {
            return this.getProductName(this.productId);
        },
        selectedAnalysis() {
            const analysis = this.forecast.analyses.find(
                (a) => a.product_id == this.productId
            );
            return analysis ? JSON.parse(analysis.analysis) : null;
        },
        chartData() {
            if (!this.timeSeriesData || !this.selectedResult) return null;

            // Parse predictions data
            const predictions = JSON.parse(
                this.selectedResult.predictions || "{}"
            );

            // Format time series data
            const inputSeries = Object.entries(this.timeSeriesData).map(
                ([date, value]) => ({
                    x: new Date(date).getTime(),
                    y: Number(value),
                })
            );

            // Format predictions data
            const forecastSeries = [];
            const confidenceUpperSeries = [];
            const confidenceLowerSeries = [];

            // Format prediction data
            Object.entries(predictions).forEach(([date, data]) => {
                // Object with confidence intervals
                if (typeof data === "object" && data !== null) {
                    forecastSeries.push({
                        x: new Date(date).getTime(),
                        y: Number(data.mean || 0),
                    });

                    // Add confidence intervals if available
                    if (data["80%"] && Array.isArray(data["80%"])) {
                        confidenceLowerSeries.push({
                            x: new Date(date).getTime(),
                            y: Number(data["80%"][0] || 0),
                        });

                        confidenceUpperSeries.push({
                            x: new Date(date).getTime(),
                            y: Number(data["80%"][1] || 0),
                        });
                    }
                }
                // Simple value
                else {
                    forecastSeries.push({
                        x: new Date(date).getTime(),
                        y: Number(data || 0),
                    });
                }
            });

            return {
                historical: inputSeries,
                forecast: forecastSeries,
                confidenceUpper: confidenceUpperSeries,
                confidenceLower: confidenceLowerSeries,
                forecastStart:
                    Object.keys(predictions).length > 0
                        ? Object.keys(predictions)[0]
                        : null,
            };
        },
        chartOptions() {
            if (!this.chartData) return {};

            return {
                chart: {
                    id: `forecast-${this.productId}`,
                    type: "line",
                    animations: { enabled: true },
                    toolbar: { show: true },
                    zoom: { enabled: true },
                },
                colors: ["#2E93fA", "#FF9800", "#66DA26", "#546E7A"],
                dataLabels: { enabled: false },
                stroke: {
                    width: [3, 3, 2, 2],
                    curve: "straight",
                    dashArray: [0, 0, 5, 5],
                },
                grid: {
                    row: {
                        colors: ["#f3f3f3", "transparent"],
                        opacity: 0.5,
                    },
                },
                markers: { size: 3 },
                xaxis: {
                    type: "datetime",
                    title: { text: "Tanggal" },
                },
                yaxis: {
                    title: { text: "Jumlah" },
                    labels: {
                        formatter: function (val) {
                            return Math.round(val);
                        },
                    },
                },
                legend: {
                    show: true,
                    position: "top",
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (y) {
                            if (typeof y !== "undefined") {
                                return y.toFixed(0) + " unit";
                            }
                            return y;
                        },
                    },
                },
                annotations: {
                    xaxis: this.chartData?.forecastStart
                        ? [
                              {
                                  x: new Date(
                                      this.chartData.forecastStart
                                  ).getTime(),
                                  borderColor: "#775DD0",
                                  label: {
                                      style: {
                                          color: "#fff",
                                          background: "#775DD0",
                                      },
                                      text: "Mulai Forecast",
                                  },
                              },
                          ]
                        : [],
                },
            };
        },
        chartSeries() {
            if (!this.chartData) return [];

            const series = [
                {
                    name: "Data Input",
                    data: this.chartData.historical || [],
                },
                {
                    name: "Prediksi",
                    data: this.chartData.forecast || [],
                },
            ];

            if (
                this.chartData.confidenceUpper &&
                this.chartData.confidenceUpper.length > 0
            ) {
                series.push({
                    name: "Batas Atas (80%)",
                    data: this.chartData.confidenceUpper,
                });
            }

            if (
                this.chartData.confidenceLower &&
                this.chartData.confidenceLower.length > 0
            ) {
                series.push({
                    name: "Batas Bawah (80%)",
                    data: this.chartData.confidenceLower,
                });
            }

            return series;
        },
    },
    watch: {
        // Perbarui productId jika selectedProductId berubah dari server
        selectedProductId(newVal) {
            this.productId = newVal;
        },
    },
};
</script>

<style scoped>
.legend-item {
    display: flex;
    align-items: center;
    margin-right: 15px;
}

.legend-color {
    display: inline-block;
    width: 12px;
    height: 12px;
    margin-right: 5px;
    border-radius: 2px;
}

.analysis-item h6 {
    color: #333;
    margin-bottom: 8px;
}

.analysis-item p {
    color: #555;
    line-height: 1.5;
}
</style>
