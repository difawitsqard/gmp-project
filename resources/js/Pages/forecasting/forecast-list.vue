<template>
    <Head title="Riwayat Prediksi" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>

    <div class="page-wrapper sales-list">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Riwayat Prediksi</h4>
                        <h6 class="sub-title">
                            Daftar prediksi yang telah dibuat
                        </h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            @click="this.fetch()"
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
                        <collapse-header-toggle />
                    </li>
                </ul>
                <div class="page-btn">
                    <Link
                        :href="route('forecasting.create')"
                        class="btn btn-added"
                        ><vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather>
                        Buat Prediksi Baru</Link
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
                                    <span>
                                        <span
                                            class="d-flex justify-content-center align-items-center"
                                            style="height: 100%"
                                        >
                                            <vue-feather type="x"></vue-feather>
                                        </span>
                                    </span>
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
                                <div class="col-lg-2 col-sm-6 col-12 mb-3">
                                    <vue-select
                                        :options="ForecastStatus"
                                        v-model="filters.status"
                                        placeholder="Filter Status"
                                    />
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12 mb-3">
                                    <vue-select
                                        :options="ForecastFrequencies"
                                        v-model="filters.frequency"
                                        placeholder="Filter Frekuensi"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div class="table-responsive">
                        <a-table
                            :columns="columns"
                            :data-source="forecasts.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <Link
                                        :href="
                                            route('forecasting.show', record.id)
                                        "
                                        >{{ record.name || "-" }}</Link
                                    >
                                </template>
                                <template v-if="column.key === 'forecasted_at'">
                                    <div class="fw-bold">
                                        <Link
                                            :href="
                                                route(
                                                    'forecasting.show',
                                                    record.id
                                                )
                                            "
                                            >{{ record.forecasted_at }}</Link
                                        >
                                    </div>
                                </template>
                                <template v-if="column.key === 'status'">
                                    <div>
                                        <span
                                            :class="`badge badge-${
                                                getForecastStatus(record.status)
                                                    .color
                                            } rounded`"
                                            >{{
                                                getForecastStatus(record.status)
                                                    .label
                                            }}</span
                                        >
                                    </div>
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <Link
                                                class="me-2 edit-icon p-2"
                                                :href="
                                                    route(
                                                        'forecasting.show',
                                                        record.id
                                                    )
                                                "
                                            >
                                                <vue-feather
                                                    type="eye"
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
import {
    getForecastFrequencyLabel,
    getAllForecastFrequencies,
} from "@/constants/forecastFrequency";
import {
    getForecastStatus,
    getAllForecastStatus,
} from "@/constants/forecastStatus";

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "forecasting.index",
            ["search", "status", "frequency"],
            { only: ["forecasts"] }
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
        forecasts: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            filter: false,

            ForecastFrequencies: getAllForecastFrequencies(),
            ForecastStatus: getAllForecastStatus(),

            columns: [
                {
                    title: "Nama",
                    dataIndex: "name",
                    key: "name",
                    sorter: true,
                },
                {
                    title: "Frekuensi",
                    dataIndex: "frequency",
                    sorter: false,
                    customRender: ({ text }) => getForecastFrequencyLabel(text),
                },
                {
                    title: "Horison",
                    dataIndex: "horizon",
                    sorter: true,
                },
                {
                    title: "Input Data Start",
                    dataIndex: "input_start_date",
                    key: "input_start_date",
                    sorter: true,
                    customRender: ({ text }) =>
                        text ? dayjs(text).format("DD/MM/YYYY") : "-",
                },
                {
                    title: "Input Data End",
                    dataIndex: "input_end_date",
                    key: "input_end_date",
                    sorter: true,
                    customRender: ({ text }) =>
                        text ? dayjs(text).format("DD/MM/YYYY") : "-",
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: false,
                },
                {
                    title: "Oleh",
                    dataIndex: "created_by",
                    key: "created_by",
                    customRender: ({ record }) =>
                        record.created_by?.name || "-",
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
        console.log(this.forecasts);
    },
    computed: {
        pagination() {
            return {
                current: this.forecasts.current_page,
                pageSize: this.forecasts.per_page,
                total: this.forecasts.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
    },
    methods: {
        getForecastFrequencyLabel,
        getAllForecastFrequencies,
        getForecastStatus,

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
    },
};
</script>
