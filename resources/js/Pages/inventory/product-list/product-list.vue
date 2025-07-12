<template>
    <Head title="Daftar Produk" />
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

                    <div
                        class="card"
                        :style="{ display: filter ? 'block' : 'none' }"
                        id="filter_inputs"
                    >
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="stop-circle"
                                            class="info-img"
                                        ></vue-feather>
                                        <vue-select
                                            :options="categoryOptions"
                                            v-model="filters.category"
                                            id="categroyfilter"
                                            :settings="{
                                                allowClear: true,
                                                placeholder: 'Pilih Kategori',
                                            }"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="speaker"
                                            class="info-img"
                                        ></vue-feather>
                                        <vue-select
                                            :options="unitOptions"
                                            v-model="filters.unit"
                                            :settings="{
                                                allowClear: true,
                                                placeholder: 'Pilih Satuan',
                                            }"
                                            id="unitfilter"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="package"
                                            class="info-img"
                                        ></vue-feather>
                                        <vue-select
                                            :options="stockStatusOptions"
                                            v-model="filters.stock_status"
                                            :settings="{
                                                allowClear: true,
                                                placeholder: 'Pilih Status',
                                            }"
                                            id="stockfilter"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                    <div
                                        class="input-blocks d-flex justify-content-end"
                                    >
                                        <a
                                            class="btn btn-filters btn-reset"
                                            @click="reset"
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
                            class="table datanew table-hover"
                            :columns="columns"
                            :data-source="products.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <div class="productimgname">
                                        <Link
                                            :href="
                                                route(
                                                    'products.show',
                                                    record.id
                                                )
                                            "
                                            class="product-img stock-img"
                                        >
                                            <img
                                                v-lazy="record.image_url"
                                                :src="record.image_url"
                                                alt="product"
                                            />
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'products.show',
                                                    record.id
                                                )
                                            "
                                            class="fw-bold"
                                        >
                                            {{ record.name }}
                                        </Link>
                                    </div>
                                </template>

                                <template v-else-if="column.key === 'price'">
                                    {{ $helpers.formatRupiah(record.price) }}
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
                                                v-lazy="record.image"
                                                :src="record.image"
                                                :alt="record.name"
                                            />
                                        </a>
                                        <a href="javascript:void(0);">x</a>
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
                                                :href="
                                                    route(
                                                        'products.edit',
                                                        record.id
                                                    )
                                                "
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
</template>
<script>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "products.index",
            ["search", "category", "unit", "stock_status"],
            { only: ["products"] }
        );

        return { filters, fetch, reset };
    },
    components: {
        Head,
        Link,
    },
    props: {
        units: {
            type: Object,
            required: true,
        },
        products: {
            type: Object,
            required: true,
        },
        productsCategories: {
            type: Object,
            required: true,
        },
    },
    computed: {
        pagination() {
            return {
                current: this.products.current_page,
                pageSize: this.products.per_page,
                total: this.products.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
        categoryOptions() {
            const defaultOption = {
                id: null,
                text: "Pilih Kategori",
            };
            const category = this.productsCategories.map((category) => ({
                id: category.id,
                text: category.name,
            }));

            return [defaultOption, ...category];
        },
        unitOptions() {
            const defaultOption = {
                id: null,
                text: "Pilih Satuan",
            };
            const unit = this.units.map((unit) => ({
                id: unit.id,
                text: unit.name,
            }));

            return [defaultOption, ...unit];
        },
        stockStatusOptions() {
            return [
                { id: null, text: "Pilih Status" },
                { id: "available", text: "Tersedia" },
                { id: "low", text: "Stok Rendah" },
                { id: "out", text: "Habis" },
            ];
        },
    },
    data() {
        return {
            filter: false,
            columns: [
                {
                    title: "Produk",
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
                    title: "Kategori",
                    key: "category",
                    dataIndex: ["category", "name"],
                },
                {
                    title: "Harga",
                    dataIndex: "price",
                    key: "price",
                    sorter: {
                        compare: (a, b) => {
                            a = a.price.toLowerCase();
                            b = b.price.toLowerCase();
                            return a > b ? -1 : b > a ? 1 : 0;
                        },
                    },
                },
                {
                    title: "Satuan",
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
                    title: "Dibuat Oleh",
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
        handleTableChange(pagination) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;
            this.fetch();
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
                    this.deleteProduct(id);
                }
            });
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
