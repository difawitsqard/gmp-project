<template>
    <Head
        :title="hasRole('Staff Gudang') ? 'Pesanan Masuk' : 'Riwayat Pesanan'"
    />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>

    <div class="page-wrapper sales-list">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>
                            {{
                                hasRole("Staff Gudang")
                                    ? "Pesanan Masuk"
                                    : "Riwayat Pesanan"
                            }}
                        </h4>
                        <h6>
                            {{
                                hasRole("Staff Gudang")
                                    ? "Kelola pesanan masuk"
                                    : "Kelola Pesanan "
                            }}
                        </h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            @click="fetch()"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Refresh"
                            ><vue-feather
                                type="rotate-ccw"
                                class="rotate-ccw"
                            ></vue-feather
                        ></a>
                    </li>
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
                <div class="page-btn" v-if="!hasRole('Staff Gudang')">
                    <Link :href="route('orders.create')" class="btn btn-added"
                        ><vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather>
                        Buat Pesanan Baru</Link
                    >
                </div>
            </div>

            <div class="card table-list-card">
                <div class="card-body">
                    <div class="table-top">
                        <div class="search-set">
                            <div class="search-input">
                                <input
                                    type="text"
                                    placeholder="Cari.."
                                    class="dark-input"
                                    v-model="filters.search"
                                />
                                <a
                                    href="javascript:void(0);"
                                    v-show="filters.search"
                                    class="btn btn-searchset"
                                    @click="resetExcept"
                                    ><i data-feather="x" class="feather-x"></i
                                ></a>
                            </div>
                        </div>
                        <div class="search-path">
                            <div class="d-flex align-items-center">
                                <a
                                    class="btn btn-filter"
                                    id="filter_search"
                                    v-on:click="
                                        filter = !filter;
                                        if (!filter) resetExcept();
                                    "
                                    :class="{ setclose: filter }"
                                >
                                    <vue-feather
                                        type="filter"
                                        class="filter-icon"
                                    ></vue-feather>
                                    <span
                                        ><img
                                            src="@/assets/img/icons/closes.svg"
                                            alt="img"
                                    /></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div
                        class="card"
                        id="filter_inputs"
                        :style="{ display: filter ? 'block' : 'none' }"
                    >
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="stop-circle"
                                            class="info-img"
                                        />
                                        <vue-select
                                            :options="OrderStatus"
                                            v-model="filters.status"
                                            placeholder="Pilih Status Pesanan"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="stop-circle"
                                            class="info-img"
                                        />
                                        <vue-select
                                            :options="OrderPaymentStatus"
                                            v-model="filters.payment_status"
                                            placeholder="Pilih Status Pembayaran"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                    <div
                                        class="input-blocks d-flex justify-content-end"
                                    >
                                        <a
                                            class="btn btn-filters btn-reset"
                                            @click="resetExcept"
                                        >
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div class="table-responsive">
                        <a-table
                            :columns="columns"
                            :data-source="orders.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'uuid'">
                                    <Link
                                        class="fw-bold"
                                        :href="
                                            route('orders.show', record.uuid)
                                        "
                                    >
                                        #{{ record.uuid }}
                                    </Link>
                                </template>
                                <template v-if="column.key === 'name'">
                                    <Link
                                        :href="
                                            route('orders.show', record.uuid)
                                        "
                                        >{{ record.name }}</Link
                                    >
                                </template>
                                <template v-if="column.key === 'status'">
                                    <div>
                                        <span
                                            :class="`badge badge-${
                                                getOrderStatus(record.status)
                                                    .color
                                            } rounded`"
                                            >{{
                                                getOrderStatus(record.status)
                                                    .label
                                            }}</span
                                        >
                                    </div>
                                </template>
                                <template
                                    v-else-if="column.key === 'payment_status'"
                                >
                                    <div>
                                        <span
                                            :class="`badge bg-outline-${
                                                !record.payment_status
                                                    ? record.status ===
                                                      'cancelled'
                                                        ? 'danger'
                                                        : 'warning'
                                                    : getPaymentStatus(
                                                          record.payment_status
                                                      ).color
                                            } rounded`"
                                        >
                                            {{
                                                !record.payment_status
                                                    ? record.status ===
                                                      "cancelled"
                                                        ? "Dibatalkan"
                                                        : "Menunggu Konfirmasi"
                                                    : getPaymentStatus(
                                                          record.payment_status
                                                      ).label
                                            }}
                                        </span>
                                    </div>
                                </template>
                                <template v-if="column.key === 'uplink_id'">
                                    {{ record.uplink?.name ?? "-" }}
                                </template>
                                <template v-if="column.key === 'processed_by'">
                                    {{ record.processed_by?.name ?? "-" }}
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <Link
                                                :href="
                                                    route(
                                                        'orders.show',
                                                        record.uuid
                                                    )
                                                "
                                                class="me-2 p-2 mb-0"
                                            >
                                                <vue-feather
                                                    type="eye"
                                                    class="info-img"
                                                ></vue-feather>
                                            </Link>
                                        </div>
                                    </td>
                                </template>
                            </template>
                        </a-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import { getOrderStatus, getAllOrderStatus } from "@/constants/orderStatus";
import {
    getPaymentStatus,
    getAllPaymentStatus,
} from "@/constants/paymentStatus";
import { has } from "lodash";

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "orders.index",
            ["search", "status", "payment_status"],
            { only: ["orders"] }
        );

        const resetExcept = () => {
            reset(["sort_order", "sort_field"]);
        };

        return { filters, fetch, reset, resetExcept };
    },
    components: {
        Head,
        Link,
    },
    props: {
        orders: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            filter: false,
            OrderStatus: getAllOrderStatus(),
            OrderPaymentStatus: getAllPaymentStatus(),
            columns: [
                {
                    title: "ID Pesanan",
                    dataIndex: "uuid",
                    key: "uuid",
                },
                {
                    title: "Pelanggan",
                    dataIndex: "name",
                    key: "name",
                    sorter: true,
                },
                {
                    title: "Tanggal",
                    dataIndex: "created_at",
                    key: "created_at",
                    sorter: true,
                    customRender: ({ text }) =>
                        dayjs(text).format("D MMMM YYYY HH:mm"),
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: false,
                },
                {
                    title: "Total",
                    dataIndex: "total",
                    key: "total",
                    sorter: true,
                    customRender: ({ text }) =>
                        this.$helpers.formatRupiah(text),
                },
                {
                    title: "Status Pembayaran",
                    dataIndex: "payment_status",
                    key: "payment_status",
                    sorter: false,
                },
                {
                    title: "Oleh",
                    dataIndex: "uplink_id",
                    key: "uplink_id",
                    sorter: true,
                },
                {
                    title: "Diproses Oleh",
                    dataIndex: "processed_by",
                    key: "processed_by",
                    sorter: true,
                },
                {
                    title: "Aksi",
                    key: "action",
                    sorter: false,
                },
            ],
        };
    },
    created() {
        console.log(this.orders);
        console.log(getAllOrderStatus());
    },
    computed: {
        pagination() {
            return {
                current: this.orders.current_page,
                pageSize: this.orders.per_page,
                total: this.orders.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
    },
    methods: {
        getOrderStatus,
        getPaymentStatus,
        handleTableChange(pagination, filters, sorter) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;

            if (sorter.order) {
                this.filters.sort_field = sorter.field;
                this.filters.sort_order = sorter.order;
            } else {
                this.filters.sort_field = null;
                this.filters.sort_order = null;
            }

            this.fetch();
        },
        toggleCollapse() {
            const collapseHeader = this.$refs.collapseHeader;

            if (collapseHeader) {
                collapseHeader.classList.toggle("active");
                document.body.classList.toggle("header-collapse");
            }
        },
    },
};
</script>
