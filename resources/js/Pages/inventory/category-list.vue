<template>
    <Head title="Kategori Produk" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Kategori</h4>
                        <h6>Kelola kategori</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <!-- <li>
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
                    </li> -->
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
                        @click="addCategory"
                        ><vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather
                        >Tambah Kategori Baru</a
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
                                    placeholder="Cari.."
                                    class="dark-input"
                                    v-model="filters.search"
                                />
                                <a
                                    href="javascript:void(0);"
                                    v-show="filters.search"
                                    class="btn btn-searchset"
                                    @click="reset"
                                    ><i data-feather="x" class="feather-x"></i
                                ></a>
                            </div>
                        </div>
                        <div class="search-path">
                            <a
                                class="btn btn-filter"
                                id="filter_search"
                                v-on:click="filter = !filter"
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
                                            <date-picker
                                                v-model="filters.created"
                                                placeholder="Choose Date"
                                                class="form-control"
                                            />
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
                                                @click="editCategory(record.id)"
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
    <product-category-modal
        ref="categoryModal"
        :is-edit="modalCategory.isEdit"
        :model="{
            id: modalCategory.id,
            name: modalCategory.name,
            description: modalCategory.description,
            status: modalCategory.status,
        }"
        modal-id="category-modal"
        @product-category-submit="handleCategorySubmit"
    />
</template>
<script>
import { ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import ProductCategoryModal from "@/components/modal/product-category-modal.vue";

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
        ProductCategoryModal,
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
            modalCategory: {
                isEdit: false,
                id: null,
                name: null,
                description: null,
                status: true,
            },
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

        addCategory() {
            this.modalCategory.isEdit = false;
            this.modalCategory.id = null;
            this.modalCategory.name = null;
            this.modalCategory.description = null;
            this.modalCategory.status = true;
            this.$refs.categoryModal.showModal();
        },

        editCategory(id) {
            const category = this.productCategories.data.find(
                (category) => category.id === id
            );

            if (category) {
                this.modalCategory.isEdit = true;
                this.modalCategory.id = category.id;
                this.modalCategory.name = category.name;
                this.modalCategory.description = category.description;
                this.modalCategory.status = category.status;
                this.$refs.categoryModal.showModal();
            }
        },

        handleCategorySubmit(response) {
            if (response.status) {
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: response.message,
                });

                this.fetch();
            }
        },

        showConfirmation(id) {
            Swal.fire({
                title: "Apa kamu yakin ?",
                text: "Tindakan ini bersifat permanen dan tidak dapat dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
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
            router.delete(route("product-categories.destroy", { id }), {
                onSuccess: () => {
                    const flash = usePage().props.flash;
                    if (flash.success) {
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
