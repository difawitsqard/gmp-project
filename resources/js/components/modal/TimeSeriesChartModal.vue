<template>
    <div>
        <transition name="modal">
            <div v-if="show" class="modal-backdrop fade show"></div>
        </transition>
        <div
            v-if="show"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="modal-dialog modal-xl modal-dialog-centered"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span v-if="productName">{{ productName }}</span>
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-center">
                                <span
                                    class="rounded p-2 border bg-light text-dark me-2"
                                >
                                    Frekuensi
                                    <strong>{{ frequencyLabel }}</strong>
                                </span>
                                <span
                                    class="rounded p-2 border bg-light text-dark"
                                >
                                    Periode
                                    <strong>{{ humanStartDate }}</strong>
                                    &ndash;
                                    <strong>{{ humanEndDate }}</strong>
                                </span>
                            </div>
                        </div>
                        <div v-if="loading" class="text-center py-5">
                            <div
                                class="spinner-border text-primary"
                                role="status"
                            ></div>
                            <div class="mt-2">Memuat data...</div>
                        </div>
                        <div v-else>
                            <apexchart
                                v-if="chartOptions && chartSeries"
                                type="line"
                                height="350"
                                :options="chartOptions"
                                :series="chartSeries"
                            />
                            <div
                                v-if="
                                    !chartSeries ||
                                    chartSeries[0].data.length === 0
                                "
                                class="alert alert-info mt-3"
                            >
                                Tidak ada data time series untuk produk ini pada
                                periode yang dipilih.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "TimeSeriesChartModal",
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        productId: {
            type: [String, Number],
            required: true,
        },
        productName: {
            type: String,
            default: "",
        },
        startDate: {
            type: [String, Date],
            required: true,
        },
        endDate: {
            type: [String, Date],
            required: true,
        },
        frequency: {
            type: String,
            default: "D",
        },
    },
    data() {
        return {
            loading: false,
            chartOptions: null,
            chartSeries: null,
        };
    },
    computed: {
        frequencyLabel() {
            switch (this.frequency) {
                case "D":
                    return "Harian";
                case "W":
                    return "Mingguan";
                case "M":
                    return "Bulanan";
                default:
                    return this.frequency;
            }
        },
        humanStartDate() {
            return this.formatHumanDate(this.startDate);
        },
        humanEndDate() {
            return this.formatHumanDate(this.endDate);
        },
    },
    watch: {
        show(val) {
            if (val) {
                this.fetchChartData();
            }
        },
        productId() {
            if (this.show) this.fetchChartData();
        },
        startDate() {
            if (this.show) this.fetchChartData();
        },
        endDate() {
            if (this.show) this.fetchChartData();
        },
        frequency() {
            if (this.show) this.fetchChartData();
        },
    },
    methods: {
        close() {
            this.$emit("close");
        },
        async fetchChartData() {
            this.loading = true;
            this.chartOptions = null;
            this.chartSeries = null;
            try {
                const response = await axios.get(
                    `/products/${this.productId}/time-series-chart`,
                    {
                        params: {
                            start_date: this.formatDate(this.startDate),
                            end_date: this.formatDate(this.endDate),
                            frequency: this.frequency,
                        },
                    }
                );
                const data = response.data || {};
                const categories = Object.keys(data);
                const values = Object.values(data);

                this.chartOptions = {
                    chart: {
                        fontFamily: "Montserrat, sans-serif",
                        type: "line",
                        height: 350,
                        toolbar: { show: false },
                    },
                    xaxis: {
                        categories,
                        title: { text: "Tanggal" },
                        labels: { rotate: -45 },
                    },
                    yaxis: {
                        title: { text: "Jumlah Terjual" },
                        min: 0,
                        forceNiceScale: true,
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: "smooth", width: 2 },
                    tooltip: {
                        x: { format: "yyyy-MM-dd" },
                        y: { formatter: (val) => `${val} terjual` },
                    },
                };
                this.chartSeries = [
                    {
                        name: "Jumlah Terjual",
                        data: values,
                    },
                ];
            } catch (e) {
                this.chartOptions = null;
                this.chartSeries = null;
            } finally {
                this.loading = false;
            }
        },
        formatDate(date) {
            if (!date) return null;
            if (typeof date === "string" && /^\d{4}-\d{2}-\d{2}$/.test(date)) {
                return date;
            }
            const d = typeof date === "string" ? new Date(date) : date;
            return d.toISOString().split("T")[0];
        },
        formatHumanDate(date) {
            if (!date) return "-";
            const d = typeof date === "string" ? new Date(date) : date;
            return d.toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "long",
                year: "numeric",
            });
        },
    },

    mounted() {
        if (this.show && this.productId) {
            this.fetchChartData();
        }
    },
};
</script>

<style scoped>
.modal-backdrop {
    z-index: 1040;
}
.modal {
    z-index: 1050;
    background: rgba(0, 0, 0, 0.15);
}
</style>
