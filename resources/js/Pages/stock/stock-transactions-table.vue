<template>
    <div class="table-top">
        <div class="search-set">
            <div class="search-input">
                <input
                    type="text"
                    placeholder="Cari.."
                    class="dark-input"
                    v-model="filters.stock_search"
                />
                <a
                    href="javascript:void(0);"
                    v-show="stock_search"
                    class="btn btn-searchset"
                    @click="resetSearch"
                    ><i data-feather="x" class="feather-x"></i>
                </a>
            </div>
        </div>
        <div class="form-sort">
            <vue-feather type="sliders" class="info-img"></vue-feather>
            <vue-select
                :options="sortByNewOld"
                v-model="filters.stock_sort"
                placeholder="Sortir"
            />
        </div>
    </div>

    <div class="table-responsive">
        <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm me-2"></span>
            Memuat data...
        </div>
        <a-table
            v-else
            class="table datanew table-hover"
            :columns="columns"
            :data-source="transactions.data"
            :pagination="pagination"
            @change="handleTableChange"
            :rowKey="(record) => record.id"
        >
            <template #headerCell="{ column }">
                <template v-if="column.key === 'name'">
                    <span>Riwayat Perubahan Stok</span>
                </template>
                <template v-else>
                    <span>{{ column.title }}</span>
                </template>
            </template>

            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'product'">
                    <Link
                        :href="route('products.show', record.product.id)"
                        class="productimgname"
                    >
                        <div class="product-img me-2">
                            <img
                                v-lazy="record.product.image_url"
                                alt="product"
                                class="stock-history-img rounded"
                            />
                        </div>
                        <div>
                            <div class="fw-bold">{{ record.product.name }}</div>
                            <div class="small">
                                {{ record.product.sku || "-" }}
                            </div>
                        </div>
                    </Link>
                </template>

                <template v-else-if="column.key === 'batch_reference'">
                    <span class="text-primary">{{
                        record.batch_reference || "-"
                    }}</span>
                </template>

                <template v-else-if="column.key === 'created_at'">
                    {{
                        $helpers.formatDate(
                            record.created_at,
                            "D MMMM YYYY HH:mm"
                        )
                    }}
                </template>

                <template v-else-if="column.key === 'type'">
                    <span class="badge" :class="getTypeBadgeClass(record)">
                        {{ getTypeLabel(record) }}
                    </span>
                </template>

                <template v-else-if="column.key === 'qty'">
                    {{ record.qty }}
                    {{
                        record.product.unit
                            ? record.product.unit.short_name
                            : ""
                    }}
                </template>

                <template v-else-if="column.key === 'note'">
                    <div v-if="isFromOrder(record)">
                        <Link
                            :href="route('orders.show', record.source.uuid)"
                            class="text-primary text-decoration-underline"
                        >
                            #{{ record.source.uuid }}
                        </Link>
                    </div>
                    <a
                        v-else-if="record.note"
                        href="javascript:void(0);"
                        class="view-note"
                        data-bs-toggle="modal"
                        data-bs-target="#vieNoteModal"
                        @click="prepareNoteModal(record.note)"
                    >
                        Lihat Catatan
                    </a>
                    <span v-else class="text-muted">-</span>
                </template>

                <template v-else-if="column.key === 'stock_change'">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2">{{
                            record.stock_before
                        }}</span>
                        <vue-feather
                            type="arrow-right"
                            class="text-muted me-2"
                            style="width: 14px; height: 14px"
                        />
                        <span
                            :class="
                                record.type === 'in'
                                    ? 'text-success'
                                    : 'text-danger'
                            "
                            >{{ record.stock_after }}</span
                        >
                    </div>
                </template>

                <template v-else-if="column.key === 'created_by'">
                    <div class="userimgname">
                        <div class="product-img me-3">
                            <img
                                v-lazy="record.created_by?.profile_photo_url"
                                :alt="record.created_by?.id"
                            />
                        </div>
                        <div>{{ record.created_by?.name }}</div>
                    </div>
                </template>
            </template>
        </a-table>
    </div>

    <teleport to="body">
        <div
            class="modal fade"
            id="vieNoteModal"
            tabindex="-1"
            aria-labelledby="vieNoteModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content">
                            <div
                                class="modal-header border-0 custom-modal-header d-flex justify-content-between"
                            >
                                <div class="page-title">
                                    <h4>Catatan</h4>
                                </div>
                                <button
                                    type="button"
                                    class="close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                >
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body custom-modal-body">
                                <p v-if="noteText">{{ noteText }}</p>
                                <p v-else class="text-muted">
                                    Tidak ada catatan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
import axios from "axios";
import { debounce } from "lodash";
import { Link } from "@inertiajs/vue3";

export default {
    props: {
        apiUrl: {
            type: String,
            default: "/stock-transactions",
        },
        productId: {
            type: [String, Number],
            default: null,
        },
    },
    components: {
        Link,
    },
    data() {
        return {
            columns: [
                // {
                //     title: "Batch ID",
                //     dataIndex: "batch_reference",
                //     key: "batch_reference",
                //     sorter: true,
                // },
                {
                    title: "Tanggal",
                    dataIndex: "created_at",
                    key: "created_at",
                    sorter: false,
                },
                {
                    title: "Produk",
                    dataIndex: "product",
                    key: "product",
                    sorter: false,
                },
                {
                    title: "Tipe",
                    dataIndex: "type",
                    key: "type",
                    sorter: false,
                },
                {
                    title: "Jumlah",
                    dataIndex: "qty",
                    key: "qty",
                    sorter: false,
                },
                {
                    title: "Perubahan Stok",
                    key: "stock_change",
                    sorter: false,
                },
                {
                    title: "Catatan/Referensi",
                    dataIndex: "note",
                    key: "note",
                    sorter: false,
                },
                {
                    title: "Oleh",
                    dataIndex: "created_by",
                    key: "created_by",
                    sorter: false,
                },
            ],
            transactions: {
                data: [],
                current_page: 1,
                per_page: 10,
                total: 0,
            },
            filters: {
                stock_search: "",
                stock_sort: "",
                page: 1,
                per_page: 10,
            },
            loading: false,
            noteText: "",
        };
    },
    computed: {
        pagination() {
            return {
                current: this.transactions.current_page,
                pageSize: this.transactions.per_page,
                total: this.transactions.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
        sortByNewOld() {
            return [
                { value: "newest", label: "Terbaru" },
                { value: "latest", label: "Terlama" },
            ];
        },
    },
    watch: {
        productId: {
            handler() {
                this.filters.page = 1;
                this.fetch();
            },
            immediate: true,
        },
        "filters.stock_search": {
            handler: debounce(function () {
                this.filters.page = 1;
                this.fetch();
            }, 500), // 500ms debounce
            immediate: false,
        },
        "filters.stock_sort": {
            handler() {
                this.filters.page = 1;
                this.fetch();
            },
            immediate: false,
        },
    },
    methods: {
        async fetch() {
            this.loading = true;
            try {
                const params = {
                    ...this.filters,
                };
                if (this.productId) {
                    params.product_id = this.productId;
                }
                const res = await axios.get(this.apiUrl, { params });
                this.transactions = res.data;
            } catch (e) {
                // handle error
            } finally {
                this.loading = false;
            }
        },
        handleTableChange(pagination) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;
            this.fetch();
        },
        handleSortChange(val) {
            this.filters.stock_sort = val;
            this.filters.page = 1;
            this.fetch();
        },
        resetSearch() {
            this.filters.stock_search = "";
            this.filters.page = 1;
            this.fetch();
        },
        prepareNoteModal(note) {
            this.noteText = note || "Tidak ada catatan.";
        },
        isFromOrder(record) {
            console.log(record.source); // Debugging line to check source type and source
            return record.source_type === "App\\Models\\Order" && record.source;
        },
        getTypeLabel(record) {
            if (this.isFromOrder(record)) {
                return "Terjual";
            }
            return record.type === "in" ? "Penambahan" : "Pengurangan";
        },
        getTypeBadgeClass(record) {
            if (this.isFromOrder(record)) {
                return "bg-warning text-dark";
            }
            return record.type === "in" ? "bg-success" : "bg-danger";
        },
    },
    mounted() {
        this.fetch();
    },
};
</script>
