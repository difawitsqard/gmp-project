<template>
    <Head title="Riwayat Pesanan" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>

    <div class="page-wrapper sales-list">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Riwayat Pesanan</h4>
                        <h6>Kelola Pesanan</h6>
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
                <div class="page-btn">
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
                                    placeholder="Search"
                                    class="dark-input"
                                    v-model="filters.search"
                                />
                                <a href="" class="btn btn-searchset"
                                    ><i
                                        data-feather="search"
                                        class="feather-search"
                                    ></i
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
                                        if (!filter) reset();
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
                                <template v-if="column.key === 'name'">
                                    <div class="fw-bold">
                                        <Link
                                            :href="
                                                route('orders.show', record.id)
                                            "
                                            >{{ record.name }}</Link
                                        >
                                    </div>
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
                                                getPaymentStatus(
                                                    record.payment_status
                                                ).color
                                            } rounded`"
                                        >
                                            {{
                                                getPaymentStatus(
                                                    record.payment_status
                                                ).label
                                            }}
                                        </span>
                                    </div>
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <div class="text-center">
                                        <a
                                            class="action-set"
                                            href="javascript:void(0);"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="true"
                                        >
                                            <i
                                                class="fa fa-ellipsis-v"
                                                aria-hidden="true"
                                            ></i>
                                        </a>
                                        <ul
                                            class="dropdown-menu sales-list-icon"
                                        >
                                            <li>
                                                <Link
                                                    :href="
                                                        route(
                                                            'orders.show',
                                                            record.id
                                                        )
                                                    "
                                                    class="dropdown-item"
                                                    ><vue-feather
                                                        type="eye"
                                                        class="info-img"
                                                    ></vue-feather
                                                    >Detail Pesanan</Link
                                                >
                                            </li>
                                        </ul>
                                    </div>
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

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "orders.index",
            ["search", "status", "payment_status"],
            { only: ["orders"] }
        );
        return { filters, fetch, reset };
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
                    title: "Pelanggan",
                    dataIndex: "name",
                    key: "name",
                    sorter: {
                        compare: (a, b) => {
                            a = a.name.toLowerCase();
                            b = b.name.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "ID Pesanan",
                    dataIndex: "id",
                    sorter: {
                        compare: (a, b) => {
                            a = a.id.toLowerCase();
                            b = b.id.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                    customRender: ({ text }) => `#${text}`,
                },
                {
                    title: "Tanggal",
                    dataIndex: "created_at",
                    sorter: {
                        compare: (a, b) => {
                            a = a.created_at.toLowerCase();
                            b = b.created_at.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                    customRender: ({ text }) =>
                        dayjs(text).format("D MMMM YYYY HH:mm"),
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: {
                        compare: (a, b) => {
                            a = a.status.toLowerCase();
                            b = b.status.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Total",
                    dataIndex: "total",
                    sorter: {
                        compare: (a, b) => {
                            a = parseFloat(a.total);
                            b = parseFloat(b.total);
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                    customRender: ({ text }) =>
                        this.$helpers.formatRupiah(text),
                },
                {
                    title: "Status Pembayaran",
                    dataIndex: "payment_status",
                    key: "payment_status",
                    sorter: {
                        compare: (a, b) => {
                            a = a.Payment_Status.toLowerCase();
                            b = b.Payment_Status.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Biller",
                    dataIndex: "Biller",
                    sorter: {
                        compare: (a, b) => {
                            a = a.Biller.toLowerCase();
                            b = b.Biller.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                    customRender: () => "Admin",
                },
                {
                    title: "Action",
                    key: "action",
                    sorter: true,
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
        handleTableChange(pagination) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;
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
