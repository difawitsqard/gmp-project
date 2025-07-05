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
            <div class="option-container">
                <img
                    v-lazy="option.image_url"
                    alt="Product Image"
                    class="option-image"
                />

                <div class="option-text">
                    <span
                        v-if="option.popularityRank"
                        class="popularity-badge badge-silver"
                    >
                        <template v-if="lastQuery">
                            Terlaris ke {{ option.popularityRank }} untuk "{{
                                lastQuery
                            }}"
                        </template>
                        <template v-else>
                            Terlaris ke {{ option.popularityRank }}
                        </template>
                    </span>
                    <div class="option-name">
                        {{ option.name }}
                    </div>
                    <div class="option-description">
                        {{ option.sku }} - {{ option.category }}
                        <span
                            v-if="option.timeSeriesOrderItems"
                            class="data-points-badge"
                        >
                            {{ getDataPointsLabel(option) }}
                        </span>
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
            placeholderImage: "/uploads/images/placeholder-image.webp", // URL gambar placeholder
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
            // Reload products when date changes
            this.resetAndSearch();
        },
        endDate() {
            // Reload products when date changes
            this.resetAndSearch();
        },
    },
    created() {
        // Gunakan initial options jika ada
        if (this.initialOptions && this.initialOptions.length > 0) {
            this.options = this.initialOptions;
        } else {
            // Load produk default dengan filter tanggal
            this.loadDefaultProducts();
        }
    },
    methods: {
        resetAndSearch() {
            this.currentPage = 1;
            this.options = [];
            if (this.lastQuery && this.lastQuery.length >= 2) {
                this.searchProducts(this.lastQuery);
            } else {
                this.loadDefaultProducts();
            }
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

            // Tambahkan parameter search jika ada
            if (query && query.length >= 2) {
                params.search = query;
            } else {
                // Jika tidak ada search, kita bisa request produk popular
                params.popular = true;
            }

            // Tambahkan filter tanggal jika tersedia
            if (this.startDate) {
                params.order_date_start = this.formatDate(this.startDate);
            }

            if (this.endDate) {
                params.order_date_end = this.formatDate(this.endDate);
            }

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
            if (!option.timeSeriesOrderItems) return true;

            let dataPoints = 0;

            // Periksa jumlah data points berdasarkan frekuensi yang dipilih
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

            // Disable jika data point kurang dari 13
            return dataPoints < 13;
        },

        getDataPointsLabel(option) {
            if (!option.timeSeriesOrderItems) return "";

            let count = 0;
            let periodName = "";

            switch (this.frequency) {
                case "D":
                    count = option.timeSeriesOrderItems.day;
                    periodName = "harian";
                    break;
                case "W":
                    count = option.timeSeriesOrderItems.week;
                    periodName = "mingguan";
                    break;
                case "M":
                    count = option.timeSeriesOrderItems.month;
                    periodName = "bulanan";
                    break;
                default:
                    count = option.timeSeriesOrderItems.day;
                    periodName = "harian";
            }

            return `${count} titik data ${periodName}`;
        },

        customLabel(option) {
            return `${option.name} (${option.sku})`;
        },
    },
    beforeUnmount() {
        // Bersihkan event listener saat komponen dihapus
        const dropdownList = document.querySelector(
            ".multiselect__content-wrapper"
        );
        if (dropdownList) {
            dropdownList.removeEventListener("scroll", this.handleScroll);
        }
    },
};
</script>

<style>
.option-container {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.option-image {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
    padding: 2px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.option-text {
    display: flex;
    flex-direction: column;
    overflow: hidden;
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

.multiselect__option:hover .option-name {
    color: #ffffff;
}

.option-name:hover {
    color: #ffffff;
}

.option-description {
    font-size: 12px;
    color: #ebebeb;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.select2-container {
    z-index: 1 !important;
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
</style>
