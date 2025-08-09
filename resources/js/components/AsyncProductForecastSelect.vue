<template>
    <vue-multiselect
        v-model="selected"
        :options="options"
        :multiple="true"
        :searchable="true"
        :loading="isLoading"
        :internal-search="false"
        :clear-on-select="false"
        :close-on-select="false"
        :preserve-search="true"
        placeholder="Cari Produk..."
        label="name"
        track-by="id"
        @search-change="searchProducts"
        :custom-label="customLabel"
        @open="registerScrollEvent"
        :option-disabled="isOptionDisabled"
    >
        <template #option="{ option }">
            <div class="option-container" :title="getTooltipText(option)">
                <img
                    v-lazy="option.image_url"
                    alt="Product Image"
                    class="option-image"
                />

                <div class="option-text">
                    <!-- Badges Container -->
                    <div class="badges-container">
                        <span
                            v-if="option.popularityRank"
                            class="popularity-badge badge-silver"
                        >
                            <template v-if="lastQuery">
                                Terlaris ke {{ option.popularityRank }} untuk
                                "{{ lastQuery }}"
                            </template>
                            <template v-else>
                                Terlaris ke {{ option.popularityRank }}
                            </template>
                        </span>

                        <!-- Forecast Eligibility Badge -->
                        <span
                            v-if="option.forecast_eligibility"
                            :class="
                                getForecastEligibilityBadgeClass(
                                    option.forecast_eligibility
                                )
                            "
                        >
                            Data Tidak Nol
                            {{ option.forecast_eligibility.non_zero_ratio }}%
                        </span>
                    </div>

                    <div class="option-name">
                        {{ option.name }}
                    </div>

                    <div class="option-description">
                        {{ option.sku }} - {{ option.category }}
                    </div>

                    <!-- Warning untuk produk yang tidak layak -->
                    <div
                        v-if="
                            option.forecast_eligibility &&
                            !option.forecast_eligibility.is_eligible
                        "
                        class="forecast-warning"
                    >
                        <small>{{ option.forecast_eligibility.reason }}</small>
                    </div>
                </div>

                <!-- Quality Score Indicator -->
                <div
                    v-if="option.forecast_eligibility"
                    class="quality-indicator"
                >
                    <div class="quality-score">
                        {{ option.forecast_eligibility.data_quality_score }}%
                    </div>
                    <div class="quality-bar">
                        <div
                            class="quality-fill"
                            :style="{
                                width:
                                    option.forecast_eligibility
                                        .data_quality_score + '%',
                                backgroundColor: getQualityColor(
                                    option.forecast_eligibility
                                        .data_quality_score
                                ),
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </template>
    </vue-multiselect>
</template>

<script>
import { debounce } from "lodash";

export default {
    name: "AsyncProductSelect",
    props: {
        modelValue: {
            type: Array,
            default: () => [],
        },
        startDate: {
            type: [String, Date],
            default: null,
        },
        endDate: {
            type: [String, Date],
            default: null,
        },
        frequency: {
            type: String,
            default: "D",
        },
        initialOptions: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            selected: this.modelValue,
            options: [],
            isLoading: false,
            currentPage: 1,
            lastQuery: "",
            hasMorePages: true,
            placeholderImage: "/uploads/images/placeholder-image.webp",
        };
    },
    watch: {
        modelValue(val) {
            this.selected = val;
        },
        selected(val) {
            this.$emit("update:modelValue", val);
        },
        startDate() {
            this.resetAndSearch();
        },
        endDate() {
            this.resetAndSearch();
        },
        frequency(newFrequency, oldFrequency) {
            // Jika ada produk yang sudah dipilih dan frequency berubah
            if (
                this.selected &&
                this.selected.length > 0 &&
                newFrequency !== oldFrequency
            ) {
                this.confirmFrequencyChange(newFrequency, oldFrequency);
            } else {
                // Jika tidak ada produk yang dipilih, langsung apply
                this.applyFrequencyChange(newFrequency);
            }
        },
    },
    created() {
        if (this.initialOptions && this.initialOptions.length > 0) {
            this.options = this.initialOptions;
        } else {
            this.loadDefaultProducts();
        }
    },
    methods: {
        async confirmFrequencyChange(newFrequency, oldFrequency) {
            const result = await Swal.fire({
                title: "Konfirmasi Perubahan Frekuensi",
                text: `${this.selected.length} produk yang sudah dipilih akan dihapus. Apakah Anda yakin ingin melanjutkan?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Lanjutkan",
                cancelButtonText: "Batal",
            });

            if (result.isConfirmed) {
                // User confirmed - clear selections and apply new frequency
                this.selected = [];
                this.$emit("update:modelValue", []);
                this.applyFrequencyChange(newFrequency);
            } else {
                // User cancelled - revert frequency
                this.$emit("update:frequency", oldFrequency);
            }
        },

        resetAndSearch() {
            this.currentPage = 1;
            this.options = [];
            if (this.lastQuery && this.lastQuery.length >= 2) {
                this.searchProducts(this.lastQuery);
            } else {
                this.loadDefaultProducts();
            }
        },

        applyFrequencyChange(newFrequency) {
            this.currentFrequency = newFrequency;
            this.resetAndSearch();
        },

        loadDefaultProducts() {
            this.isLoading = true;

            axios
                .get("/products/search", {
                    params: this.getSearchParams(),
                })
                .then((response) => {
                    this.hasMorePages = !!response.data.next_page_url;
                    this.options = response.data.data;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        getSearchParams(query = null) {
            const params = {
                page: this.currentPage,
                per_page: 10,
            };

            if (query && query.length >= 2) {
                params.search = query;
            } else {
                params.popular = true;
            }

            // Tambahkan filter tanggal dan frequency
            if (this.startDate) {
                params.order_date_start = this.formatDate(this.startDate);
            }

            if (this.endDate) {
                params.order_date_end = this.formatDate(this.endDate);
            }

            // Tambahkan frequency parameter untuk eligibility check
            params.frequency = this.frequency;

            return params;
        },

        // Format tanggal ke YYYY-MM-DD
        formatDate(date) {
            if (!date) return null;

            // Jika sudah string dalam format YYYY-MM-DD, return langsung
            if (typeof date === "string" && /^\d{4}-\d{2}-\d{2}$/.test(date)) {
                return date;
            }

            // Convert ke Date object jika string
            const dateObj = typeof date === "string" ? new Date(date) : date;

            // Format ke YYYY-MM-DD
            return dateObj.toISOString().split("T")[0];
        },

        // Method untuk pencarian dengan debounce
        searchProducts: debounce(function (query) {
            if (!query || query.length < 2) {
                this.lastQuery = "";
                this.options = [];
                this.loadDefaultProducts();
                return;
            }

            // Simpan query terakhir
            this.lastQuery = query;

            // Reset pagination jika query baru
            if (this.currentPage !== 1) {
                this.currentPage = 1;
                this.options = [];
            }

            this.isLoading = true;

            axios
                .get("/products/search", {
                    params: this.getSearchParams(query),
                })
                .then((response) => {
                    this.hasMorePages = !!response.data.next_page_url;
                    this.options = response.data.data;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }, 300),

        registerScrollEvent() {
            // Tambahkan event listener saat dropdown terbuka
            setTimeout(() => {
                const dropdownList = document.querySelector(
                    ".multiselect__content-wrapper"
                );
                if (dropdownList) {
                    dropdownList.addEventListener("scroll", this.handleScroll);
                }
            }, 100);
        },

        handleScroll(event) {
            const target = event.target;

            // Deteksi scroll mencapai 80% dari bawah
            if (
                target.scrollHeight - target.scrollTop - target.clientHeight <
                    50 &&
                !this.isLoading &&
                this.hasMorePages
            ) {
                this.loadMoreProducts();
            }
        },

        loadMoreProducts() {
            if (this.isLoading || !this.hasMorePages) return;

            this.currentPage++;

            this.isLoading = true;
            axios
                .get("/products/search", {
                    params: this.getSearchParams(this.lastQuery),
                })
                .then((response) => {
                    this.hasMorePages = !!response.data.next_page_url;
                    // Append hasil baru ke options yang ada
                    this.options = [...this.options, ...response.data.data];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        isOptionDisabled(option) {
            // Disable jika tidak eligible untuk forecast
            if (
                option.forecast_eligibility &&
                !option.forecast_eligibility.is_eligible
            ) {
                return true;
            }

            // Existing logic
            if (!option.timeSeriesOrderItems) return true;

            let dataPoints = 0;
            switch (this.frequency) {
                case "D":
                    dataPoints = option.timeSeriesOrderItems.day;
                    break;
                case "W":
                    dataPoints = option.timeSeriesOrderItems.week;
                    break;
                case "M":
                    dataPoints = option.timeSeriesOrderItems.month;
                    break;
                default:
                    dataPoints = option.timeSeriesOrderItems.day;
            }

            return dataPoints < 13;
        },

        getForecastEligibilityBadgeClass(eligibility) {
            const baseClass = "forecast-eligibility-badge";

            if (eligibility.is_eligible) {
                if (eligibility.data_quality_score >= 80) {
                    return `${baseClass} badge-good`;
                } else {
                    return `${baseClass} badge-fair`;
                }
            } else {
                return `${baseClass} badge-poor`;
            }
        },

        getQualityColor(score) {
            if (score >= 80) return "#4CAF50"; // Green
            if (score >= 60) return "#FF9800"; // Orange
            if (score >= 40) return "#FFC107"; // Yellow
            return "#F44336"; // Red
        },

        getTooltipText(option) {
            if (!option.forecast_eligibility) return "";

            const eligibility = option.forecast_eligibility;
            let tooltip = "";
            tooltip += `Score Kualitas: ${eligibility.data_quality_score}%\n`;
            tooltip += `Data Points: ${eligibility.data_points}\n`;
            tooltip += `Rasio Data Valid: ${eligibility.non_zero_ratio}%\n`;

            if (eligibility.days_since_last_data < 9999) {
                tooltip += `Hari Sejak Data Terakhir: ${eligibility.days_since_last_data}`;
            }

            if (!eligibility.is_eligible) {
                tooltip += `Alasan: ${eligibility.reason}`;
            }

            return tooltip;
        },
    },
};
</script>

<style>
.option-container {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.option-image {
    width: 62px;
    height: 62px;
    object-fit: cover;
    border-radius: 5px;
    background-color: #f0f0f0;
    margin-right: 10px;
    padding: 2px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.option-text {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    flex: 1;
}

.option-name {
    font-weight: bold;
    font-size: 14px;
    color: #5b6670;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.option-name:hover,
.multiselect__option:hover .option-name {
    color: #ffffff;
}

.option-description {
    font-size: 12px;
    color: #adadad;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.option-description :hover,
.multiselect__option:hover .option-description {
    color: #ffffff;
}

.popularity-badge {
    display: inline-block;
    font-size: 10px;
    background-color: #ff9800;
    color: white;
    padding: 1px 6px;
    border-radius: 10px;
    margin-bottom: 3px;
    font-weight: normal;
    align-self: flex-start;
}

.badge-gold {
    background-color: #ffd700;
    color: #222;
}

.badge-silver {
    background-color: #c0c0c0;
    color: #222;
}

.badge-bronze {
    background-color: #cd7f32;
}

.data-points-badge {
    display: inline-block;
    background-color: #e9f5fe;
    color: #0088cc;
    padding: 1px 6px;
    border-radius: 10px;
    margin-left: 6px;
    font-size: 9px;
    font-weight: normal;
}

.badges-container {
    display: flex;
    gap: 4px;
    margin-bottom: 3px;
    flex-wrap: wrap;
}

.forecast-eligibility-badge {
    display: inline-block;
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-good {
    background-color: #4caf50;
    color: white;
}

.badge-fair {
    background-color: #ff9800;
    color: white;
}

.badge-poor {
    background-color: #f44336;
    color: white;
}

.forecast-warning {
    margin-top: 2px;
    color: #f44336;
    font-size: 10px;
    line-height: 1.2;
}

.quality-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-left: 8px;
    min-width: 40px;
}

.quality-score {
    font-size: 10px;
    font-weight: bold;
    color: #666;
    margin-bottom: 2px;
}

.quality-bar {
    width: 30px;
    height: 4px;
    background-color: #e0e0e0;
    border-radius: 2px;
    overflow: hidden;
}

.quality-fill {
    height: 100%;
    transition: width 0.3s ease;
}

/* Disabled option styling */
.multiselect__option--disabled {
    opacity: 0.6;
    background-color: #f5f5f5 !important;
}

.multiselect__option--disabled .option-name {
    color: #999 !important;
}

.multiselect__option--disabled .forecast-eligibility-badge {
    opacity: 0.8;
}
</style>
