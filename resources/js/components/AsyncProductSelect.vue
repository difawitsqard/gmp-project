<template>
    <vue-multiselect
        v-model="selected"
        :options="options"
        :multiple="multiple"
        :searchable="true"
        :loading="isLoading"
        :internal-search="false"
        :clear-on-select="false"
        :close-on-select="false"
        :preserve-search="true"
        :placeholder="placeholder ?? 'Pilih Produk'"
        label="name"
        track-by="id"
        @search-change="searchProducts"
        :custom-label="customLabel"
        @open="registerScrollEvent"
    >
        <template #option="{ option }">
            <div class="option-container">
                <img
                    :src="option.image_url || placeholderImage"
                    alt="Product Image"
                    class="option-image"
                />
                <div class="option-text">
                    <div class="option-name">{{ option.name }}</div>
                    <div class="option-description">
                        {{ option.sku }} - {{ option.category }} - Stok:
                        {{ option.qty }}
                    </div>
                </div>
            </div>
        </template>
        <template #noResult>
            <div class="no-result">Tidak ada produk yang ditemukan</div>
        </template>
        <template #noOptions>
            <div class="no-options">Mulai ketik untuk mencari produk</div>
        </template>
    </vue-multiselect>
</template>

<script>
import { debounce } from "lodash";

export default {
    name: "ProductSearch",
    props: {
        modelValue: {
            type: [Array, Object],
            default: null,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        categoryFilter: {
            type: Number,
            default: null,
        },
        unitFilter: {
            type: Number,
            default: null,
        },
        stockFilter: {
            type: String, // 'available', 'low', 'out'
            default: null,
        },
    },
    data() {
        return {
            selected: this.modelValue,
            options: [],
            isLoading: false,
            placeholderImage: "/uploads/images/placeholder-image.webp",
            lastSearchQuery: "",
            currentPage: 1,
            hasMorePages: true,
        };
    },
    watch: {
        modelValue(val) {
            this.selected = val;
            // console.log("Model value updated:", val);
        },
        selected(val) {
            this.$emit("update:modelValue", val);
        },
        categoryFilter(newVal, oldVal) {
            console.log("categoryFilter changed:", oldVal, "->", newVal);
            if (newVal !== oldVal) {
                // Reset pagination untuk pencarian baru
                this.currentPage = 1;
                this.options = [];
                this.hasMorePages = true;

                // Lakukan pencarian dengan query yang ada (atau kosong)
                this.searchProducts(this.lastSearchQuery || "");
            }
        },
        unitFilter(newVal, oldVal) {
            if (newVal !== oldVal) {
                // Reset pagination untuk pencarian baru
                this.currentPage = 1;
                this.options = [];
                this.hasMorePages = true;

                // Lakukan pencarian dengan query yang ada (atau kosong)
                this.searchProducts(this.lastSearchQuery || "");
            }
        },
        stockFilter(newVal, oldVal) {
            if (newVal !== oldVal) {
                // Reset pagination untuk pencarian baru
                this.currentPage = 1;
                this.options = [];
                this.hasMorePages = true;

                // Lakukan pencarian dengan query yang ada (atau kosong)
                this.searchProducts(this.lastSearchQuery || "");
            }
        },
    },
    methods: {
        searchProducts: debounce(function (query) {
            this.lastSearchQuery = query; // Simpan query terakhir

            // if (!query || query.length < 2) {
            //     this.options = [];
            //     this.currentPage = 1;
            //     this.hasMorePages = true;
            //     return;
            // }

            // Reset pagination untuk pencarian baru
            this.currentPage = 1;
            this.options = [];
            this.hasMorePages = true;
            this.isLoading = true;

            const params = {
                per_page: 20,
                page: this.currentPage,
            };

            if (query && query.length >= 2) {
                params.search = query;
            }

            // Tambahkan filter opsional
            if (this.categoryFilter) {
                params.category = this.categoryFilter;
            }
            if (this.unitFilter) {
                params.unit = this.unitFilter;
            }
            if (this.stockFilter) {
                params.stock_status = this.stockFilter;
            }

            axios
                .get("/products/search", { params })
                .then((response) => {
                    this.options = response.data.data;
                    this.hasMorePages = !!response.data.next_page_url;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }, 300),

        registerScrollEvent() {
            // Load data default jika belum ada
            if (this.options.length === 0 && !this.isLoading) {
                this.searchProducts("");
            }

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

            const params = {
                per_page: 20,
                page: this.currentPage,
            };

            // Hanya tambahkan search jika query ada dan panjangnya >= 2
            if (this.lastSearchQuery && this.lastSearchQuery.length >= 2) {
                params.search = this.lastSearchQuery;
            }

            // Tambahkan filter opsional
            if (this.categoryFilter) {
                params.category = this.categoryFilter;
            }
            if (this.unitFilter) {
                params.unit = this.unitFilter;
            }
            if (this.stockFilter) {
                params.stock_status = this.stockFilter;
            }

            axios
                .get("/products/search", { params })
                .then((response) => {
                    this.hasMorePages = !!response.data.next_page_url;
                    // Append hasil baru ke options yang ada
                    this.options = [...this.options, ...response.data.data];
                })
                .finally(() => {
                    this.isLoading = false;
                });
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

.input-blocks .info-img {
    z-index: 10 !important;
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
