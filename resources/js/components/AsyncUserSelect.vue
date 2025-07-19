<template>
    <vue-multiselect
        v-model="selected"
        :options="options"
        :multiple="multiple"
        :searchable="true"
        :loading="isLoading"
        :internal-search="false"
        :clear-on-select="false"
        :close-on-select="!multiple"
        :preserve-search="true"
        :placeholder="placeholder ?? 'Pilih Pengguna'"
        label="name"
        track-by="id"
        @search-change="searchUsers"
        :custom-label="customLabel"
        @open="registerScrollEvent"
    >
        <template #option="{ option }">
            <div class="d-flex align-items-center p-2">
                <img
                    v-lazy="option.profile_photo_url"
                    alt="Avatar"
                    class="rounded-circle me-2"
                    style="width: 40px; height: 40px; object-fit: cover"
                />

                <div class="overflow-hidden">
                    <div class="fw-bold text-dark">{{ option.name }}</div>
                    <div class="small text-muted text-truncate">
                        {{ option.email }} -
                        {{
                            option.roles && option.roles.length > 0
                                ? option.roles[0]
                                : "No Role"
                        }}
                    </div>
                </div>
            </div>
        </template>
        <template #noResult>
            <div class="p-2 text-center text-muted">
                Tidak ada pengguna yang ditemukan
            </div>
        </template>
        <template #noOptions>
            <div class="p-2 text-center text-muted">
                Mulai ketik untuk mencari pengguna
            </div>
        </template>
    </vue-multiselect>
</template>

<script>
import { debounce } from "lodash";

export default {
    name: "AsyncUserSelect",
    props: {
        modelValue: {
            type: [Array, Object],
            default: null,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        roleFilter: {
            type: String,
            default: null,
        },
        statusFilter: {
            type: Boolean, // true for active, false for inactive
            default: null,
        },
        placeholder: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            selected: this.modelValue,
            options: [],
            isLoading: false,
            lastSearchQuery: "",
            currentPage: 1,
            hasMorePages: true,
        };
    },
    watch: {
        modelValue(val) {
            this.selected = val;
        },
        selected(val) {
            this.$emit("update:modelValue", val);
        },
        roleFilter(newVal, oldVal) {
            if (newVal !== oldVal) {
                // Reset pagination untuk pencarian baru
                this.currentPage = 1;
                this.options = [];
                this.hasMorePages = true;

                // Lakukan pencarian dengan query yang ada (atau kosong)
                this.searchUsers(this.lastSearchQuery || "");
            }
        },
        statusFilter(newVal, oldVal) {
            if (newVal !== oldVal) {
                // Reset pagination untuk pencarian baru
                this.currentPage = 1;
                this.options = [];
                this.hasMorePages = true;

                // Lakukan pencarian dengan query yang ada (atau kosong)
                this.searchUsers(this.lastSearchQuery || "");
            }
        },
    },
    methods: {
        searchUsers: debounce(function (query) {
            this.lastSearchQuery = query; // Simpan query terakhir

            // Reset pagination untuk pencarian baru
            this.currentPage = 1;
            this.options = [];
            this.hasMorePages = true;
            this.isLoading = true;

            const params = {
                per_page: 20,
                page: this.currentPage,
                json: true, // Menandakan request membutuhkan JSON response
            };

            if (query && query.length >= 2) {
                params.search = query;
            }

            // Tambahkan filter opsional
            if (this.roleFilter) {
                params.role = this.roleFilter;
            }

            if (this.statusFilter !== null) {
                params.is_active = this.statusFilter ? 1 : 0;
            }

            console.log("Searching users with params:", params);

            axios
                .get("/users", { params })
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
                this.searchUsers("");
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
                this.loadMoreUsers();
            }
        },

        loadMoreUsers() {
            if (this.isLoading || !this.hasMorePages) return;

            this.currentPage++;
            this.isLoading = true;

            const params = {
                per_page: 20,
                page: this.currentPage,
                json: true,
            };

            // Hanya tambahkan search jika query ada dan panjangnya >= 2
            if (this.lastSearchQuery && this.lastSearchQuery.length >= 2) {
                params.search = this.lastSearchQuery;
            }

            // Tambahkan filter opsional
            if (this.roleFilter) {
                params.role = this.roleFilter;
            }

            if (this.statusFilter !== null) {
                params.is_active = this.statusFilter ? 1 : 0;
            }

            axios
                .get("/users", { params })
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
            return `${option.name} (${option.email})`;
        },

        getInitials(name) {
            return name
                .split(" ")
                .map((part) => part.charAt(0).toUpperCase())
                .slice(0, 2)
                .join("");
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
