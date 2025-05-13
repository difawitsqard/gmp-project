<template>
    <Head title="Product List" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <product-header></product-header>

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
                                    v-model="searchFilter"
                                />
                                <button class="btn btn-searchset">
                                    <i
                                        data-feather="search"
                                        class="feather-search"
                                    ></i>
                                </button>
                            </div>
                        </div>
                        <div class="search-path">
                            <a
                                class="btn btn-filter"
                                id="filter_search"
                                v-on:click="
                                    filter = !filter;
                                    if (!filter) resetFilter();
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
                        <div class="form-sort">
                            <vue-feather
                                type="sliders"
                                class="info-img"
                            ></vue-feather>
                            <vue-select
                                :options="Sortby"
                                id="sortby"
                                placeholder="Sort by Date"
                            />
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div
                        class="card mb-0"
                        :style="{ display: filter ? 'block' : 'none' }"
                        id="filter_inputs"
                    >
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-2 col-sm-6 col-12">
                                            <div class="input-blocks">
                                                <vue-feather
                                                    type="stop-circle"
                                                    class="info-img"
                                                ></vue-feather>
                                                <vue-select
                                                    :options="CategroyFilter"
                                                    v-model="selectedCategory"
                                                    id="categroyfilter"
                                                    placeholder="Choose Categroy"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-6 col-12">
                                            <div
                                                class="input-blocks d-flex align-items-center justify-content-end"
                                            >
                                                <button
                                                    type="button"
                                                    class="btn btn-filters ms-auto me-2"
                                                    @click="filterProducts"
                                                >
                                                    <i
                                                        data-feather="search"
                                                        class="feather-search"
                                                    ></i>
                                                    Search
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-filters"
                                                    @click="resetFilter"
                                                >
                                                    <i
                                                        data-feather="x"
                                                        class="feather-x me-2"
                                                    ></i>
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div class="table-responsive product-list">
                        <a-table
                            class="table datanew table-hover table-center mb-0"
                            :columns="columns"
                            :data-source="data"
                            :row-selection="{}"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <div class="productimgname">
                                        <a
                                            href="javascript:void(0);"
                                            class="product-img stock-img"
                                        >
                                            <img
                                                :src="
                                                    record.image
                                                        ? $helpers.getImageUrl(
                                                              record.image
                                                          )
                                                        : `/uploads/images/placeholder-image.webp`
                                                "
                                                alt="product"
                                            />
                                        </a>
                                        <Link
                                            :href="
                                                route(
                                                    'products.show',
                                                    record.id
                                                )
                                            "
                                        >
                                            {{ record.name }}
                                        </Link>
                                    </div>
                                </template>

                                <template
                                    v-else-if="column.key === 'CreatedBy'"
                                >
                                    <td class="userimgname">
                                        <a
                                            href="javascript:void(0);"
                                            class="product-img me-2"
                                        >
                                            <img
                                                :src="
                                                    record.image
                                                        ? $helpers.getImageUrl(
                                                              record.image
                                                          )
                                                        : `/uploads/images/placeholder-image.webp`
                                                "
                                                alt="product"
                                            />
                                        </a>
                                        <a href="javascript:void(0);">{{
                                            record.reatedby
                                        }}</a>
                                    </td>
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <Link
                                                class="me-2 edit-icon p-2"
                                                :href="
                                                    route(
                                                        'products.show',
                                                        record.id
                                                    )
                                                "
                                            >
                                                <i
                                                    data-feather="eye"
                                                    class="feather-eye"
                                                ></i>
                                            </Link>
                                            <Link
                                                class="me-2 p-2"
                                                :href="`/product/${record.id}/edit`"
                                            >
                                                <i
                                                    data-feather="edit"
                                                    class="feather-edit"
                                                ></i>
                                            </Link>
                                            <a
                                                class="confirm-text p-2"
                                                @click="
                                                    showConfirmation(record.id)
                                                "
                                                href="javascript:void(0);"
                                            >
                                                <vue-feather
                                                    type="trash-2"
                                                    class="trash-2"
                                                ></vue-feather>
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
    <product-list-modal></product-list-modal>
</template>
<script>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
export default {
    components: {
        Head,
        Link,
    },
    props: {
        products: {
            type: Array,
            required: true,
        },
    },
    mounted() {
        this.data = this.products;
        this.CategroyFilter = [
            ...new Set(this.products.map((product) => product.category.name)),
        ];
    },
    watch: {
        // selectedFilter(newValue) {
        //     if (newValue === "Choose Product") {
        //         // Jika filter nama di-reset, tampilkan semua kategori
        //         this.CategroyFilter = [
        //             ...new Set(
        //                 this.products.map((product) => product.category.name)
        //             ),
        //         ];
        //     } else {
        //         // Filter kategori berdasarkan produk yang sesuai dengan nama
        //         const data = this.products.filter(
        //             (product) => product.name === newValue
        //         );
        //         this.CategroyFilter = [
        //             ...new Set(
        //                 data.map((product) => product.stock)
        //             ),
        //         ];
        //     }
        //     // Reset kategori yang dipilih
        //     this.selectedCategory = null;
        // },
        searchFilter(search) {
            this.filterProducts(search);
        },
    },
    data() {
        return {
            filter: false,
            Sortby: ["Sort by Date", "14 09 23", "11 09 23"],
            searchFilter: null,
            CategroyFilter: [],
            selectedCategory: null,
            data: [],
            columns: [
                {
                    title: "Product",
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
                    title: "SKU",
                    dataIndex: "sku",
                    sorter: {
                        compare: (a, b) => {
                            a = a.sku.toLowerCase();
                            b = b.sku.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Category",
                    key: "category",
                    dataIndex: ["category", "name"],
                },
                {
                    title: "Price",
                    dataIndex: "price",
                    sorter: {
                        compare: (a, b) => {
                            a = a.price.toLowerCase();
                            b = b.price.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Unit",
                    dataIndex: ["unit", "name"],
                    key: "unit",
                    sorter: {
                        compare: (a, b) => {
                            a = a.unit.toLowerCase();
                            b = b.unit.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Qty",
                    dataIndex: "qty",
                    sorter: {
                        compare: (a, b) => {
                            a = a.qty.toLowerCase();
                            b = b.qty.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Created by",
                    dataIndex: "CreatedBy",
                    key: "CreatedBy",
                    sorter: {
                        compare: (a, b) => {
                            a = a.CreatedBy.toLowerCase();
                            b = b.CreatedBy.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Action",
                    key: "action",
                    sorter: true,
                },
            ],
        };
    },
    methods: {
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
                    this.deleteProduct(id);
                }
            });
        },
        filterProducts(search = null) {
            this.data = this.products.filter((product) => {
                // Filter berdasarkan nama produk

                if (search instanceof PointerEvent) {
                    search = this.searchFilter;
                } else if (search) {
                    this.searchFilter = search;
                }

                const matchesName =
                    this.searchFilter === null ||
                    product.name
                        .toLowerCase()
                        .includes(
                            typeof this.searchFilter === "string"
                                ? this.searchFilter.toLowerCase()
                                : ""
                        );

                // Filter berdasarkan kategori
                const matchesCategory =
                    this.selectedCategory === null ||
                    product.category.name === this.selectedCategory;

                return matchesName && matchesCategory;
            });
        },
        resetFilter() {
            this.selectedCategory = null; // Reset kategori
            this.data = this.products; // Tampilkan semua produk
            this.searchFilter = null; // Reset pencarian
        },
        deleteProduct(id) {
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
    },
};
</script>
