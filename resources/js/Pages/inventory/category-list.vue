<template>
    <Head title="Product Category List" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Category</h4>
                        <h6>Manage your categories</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Pdf"
                            ><img src="@/assets/img/icons/pdf.svg" alt="img"
                        /></a>
                    </li>
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Excel"
                            ><img src="@/assets/img/icons/excel.svg" alt="img"
                        /></a>
                    </li>
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Print"
                            ><i
                                data-feather="printer"
                                class="feather-printer"
                            ></i
                        ></a>
                    </li>
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Refresh"
                            ><i
                                data-feather="rotate-ccw"
                                class="feather-rotate-ccw"
                            ></i
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
                    <a
                        href="javascript:void(0);"
                        class="btn btn-added"
                        data-bs-toggle="modal"
                        data-bs-target="#add-category"
                        ><vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather
                        >Add New Category</a
                    >
                </div>
            </div>
            <!-- /product list -->
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
                            <a
                                class="btn btn-filter"
                                id="filter_search"
                                @click="filter = !filter"
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
                    <!-- /Filter -->
                    <div
                        class="card"
                        :style="{ display: filter ? 'block' : 'none' }"
                        id="filter_inputs"
                    >
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="calendar"
                                            class="info-img"
                                        ></vue-feather>
                                        <div class="input-groupicon">
                                            <!-- <date-picker
                                                v-model="filters.created"
                                                placeholder="Choose Date"
                                                class="form-control"
                                            /> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="stop-circle"
                                            class="info-img"
                                        ></vue-feather>
                                        <vue-select
                                            :options="CategoryStatus"
                                            v-model="filters.status"
                                            id="categorystatus"
                                            placeholder="Choose Status"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                    <div
                                        class="input-blocks d-flex justify-content-end"
                                    >
                                        <a class="btn btn-filters me-2">
                                            <i
                                                data-feather="search"
                                                class="feather-search"
                                            ></i>
                                            Search
                                        </a>
                                        <a
                                            class="btn btn-filters btn-reset"
                                            @click="reset"
                                        >
                                            <i
                                                data-feather="x-circle"
                                                class="feather-x-circle"
                                            ></i>
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
                            class="table datanew"
                            :columns="columns"
                            :data-source="productCategories.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'status'">
                                    <div>
                                        <span
                                            :class="
                                                record.status
                                                    ? 'badge badge-linesuccess'
                                                    : 'badge badge-linedanger'
                                            "
                                        >
                                            {{
                                                record.status
                                                    ? "Active"
                                                    : "Inactive"
                                            }}</span
                                        >
                                    </div>
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <a
                                                class="me-2 p-2"
                                                href="javascript:void(0);"
                                                data-bs-toggle="modal"
                                                data-bs-target="#edit-category"
                                            >
                                                <i
                                                    data-feather="edit"
                                                    class="feather-edit"
                                                ></i>
                                            </a>
                                            <a
                                                class="confirm-text p-2"
                                                @click="
                                                    showConfirmation(record.id)
                                                "
                                                href="javascript:void(0);"
                                            >
                                                <i
                                                    data-feather="trash-2"
                                                    class="feather-trash-2"
                                                ></i>
                                            </a>
                                        </div>
                                    </td>
                                </template>
                            </template>
                        </a-table>
                    </div>
                </div>
            </div>
            <!-- /product list -->
        </div>
    </div>
    <category-list-modal></category-list-modal>
</template>
<script>
import { ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";

const currentDate = ref(new Date());

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "product-categories.index",
            ["search", "status", "created"],
            { only: ["productCategories"] }
        );

        return { filters, fetch, reset };
    },
    components: {
        Head,
        Link,
    },
    props: {
        productCategories: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            filter: false,
            startdate: currentDate,
            dateFormat: "dd-MM-yyyy",
            search: this.filters?.search || "",
            CategoryStatusSelect: null,
            CategoryStatus: [
                { id: null, text: "Choose Status" },
                { id: 1, text: "Active" },
                { id: 0, text: "Inactive" },
            ],
            filterByDate: [],
            columns: [
                {
                    title: "Category",
                    dataIndex: "name",
                    sorter: {
                        compare: (a, b) =>
                            a.name.toLowerCase() > b.name.toLowerCase()
                                ? -1
                                : 1,
                    },
                },
                {
                    title: "Total Products",
                    dataIndex: "items",
                    sorter: {
                        compare: (a, b) =>
                            String(a.items).toLowerCase() >
                            String(b.items).toLowerCase()
                                ? -1
                                : 1,
                    },
                },
                {
                    title: "Created On",
                    dataIndex: "created_at",
                    sorter: {
                        compare: (a, b) =>
                            a.created_at.toLowerCase() >
                            b.created_at.toLowerCase()
                                ? -1
                                : 1,
                    },
                    customRender: ({ text }) =>
                        dayjs(text).format("D MMMM YYYY"),
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: {
                        compare: (a, b) =>
                            a.status.toLowerCase() > b.status.toLowerCase()
                                ? -1
                                : 1,
                    },
                },
                {
                    title: "Action",
                    key: "action",
                    sorter: false,
                },
            ],
        };
    },
    computed: {
        pagination() {
            return {
                current: this.productCategories.current_page,
                pageSize: this.productCategories.per_page,
                total: this.productCategories.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
    },
    mounted() {
        this.data = this.productCategories;
    },
    methods: {
        handleTableChange(pagination) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;
            this.fetch();
        },

        reset() {
            this.reset();
        },

        showConfirmation(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                confirmButtonClass: "btn btn-primary",
                cancelButtonClass: "btn btn-danger ml-1",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    this.deleteData(id);
                }
            });
        },

        deleteData(id) {
            router.delete(`products/${id}`, {
                onSuccess: () => {
                    const flash = usePage().props.flash;
                    if (flash.success) {
                        this.data = this.data.filter(
                            (product) => product.id !== id
                        );
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: flash.success,
                        });
                    }
                },
                onError: () => {
                    const errors = usePage().props.errors;
                    if (errors && Object.keys(errors).length > 0) {
                        const firstError = Object.values(errors)[0];
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: firstError,
                        });
                    }
                },
            });
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
