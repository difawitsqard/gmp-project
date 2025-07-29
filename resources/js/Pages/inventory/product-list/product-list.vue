<template>
    <Head title="Daftar Produk" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Produk</h4>
                        <h6>Kelola Produk</h6>
                    </div>
                </div>
                <ul class="table-top-head">
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
                    <Link
                        :href="route('products.create')"
                        class="btn btn-added"
                    >
                        <vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather>
                        Tambah Produk Baru
                    </Link>
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
                                    @click="resetExcept"
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
                        <div class="form-sort">
                            <vue-feather
                                type="sliders"
                                class="info-img"
                            ></vue-feather>
                            <vue-select
                                :options="sortByNewOld"
                                v-model="filters.sort"
                                :settings="{
                                    allowClear: true,
                                    placeholder: 'Sortir Berdasarkan',
                                }"
                                id="sortfilter"
                            />
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
                            class="table datanew table-hover"
                            :columns="columns"
                            :data-source="products.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <div class="productimgname">
                                        <a
                                            :href="'#' + record.id"
                                            class="product-img stock-img"
                                            @click.prevent="
                                                openProductModal(record.id)
                                            "
                                        >
                                            <img
                                                v-lazy="record.image_url"
                                                :src="record.image_url"
                                                class="rounded"
                                                alt="product"
                                            />
                                        </a>
                                        <a
                                            :href="'#' + record.id"
                                            class="product-img stock-img"
                                            @click="openProductModal(record.id)"
                                        >
                                            {{ record.name }}
                                        </a>
                                    </div>
                                </template>

                                <template
                                    v-else-if="column.key === 'description'"
                                >
                                    <span>
                                        {{
                                            record.description
                                                ? record.description.replace(
                                                      /<[^>]+>/g,
                                                      ""
                                                  ).length > 30
                                                    ? record.description
                                                          .replace(
                                                              /<[^>]+>/g,
                                                              ""
                                                          )
                                                          .slice(0, 30) + "..."
                                                    : record.description.replace(
                                                          /<[^>]+>/g,
                                                          ""
                                                      )
                                                : "Tidak ada deskripsi"
                                        }}
                                    </span>
                                </template>

                                <template v-else-if="column.key === 'qty'">
                                    <div
                                        class="fw-bold"
                                        :class="
                                            record.min_stock >= record.qty
                                                ? 'text-danger'
                                                : ''
                                        "
                                    >
                                        {{ record.qty }}
                                        {{
                                            record.unit.short_name ??
                                            record.unit.name
                                        }}
                                    </div>
                                </template>

                                <template v-else-if="column.key === 'price'">
                                    {{ $helpers.formatRupiah(record.price) }}
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

    <ProductDescriptionModal
        ref="productModal"
        :product-id="selectedProductId"
    />
</template>
<script>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import ProductDescriptionModal from "@/components/modal/product-description-modal.vue";

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "products.index",
            ["search", "category", "unit", "stock_status", "sort"],
            { only: ["products"] }
        );

        const resetExcept = () => {
            reset(["sort", "sort_order", "sort_field"]);
        };

        return { filters, fetch, reset, resetExcept };
    },
    components: {
        Head,
        Link,
        ProductDescriptionModal,
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
        sortByNewOld() {
            return [
                { id: "bestseller", text: "Terlaris" },
                { id: "newest", text: "Terbaru" },
                { id: "oldest", text: "Terlama" },
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
                    sorter: true,
                },
                {
                    title: "Deskripsi",
                    dataIndex: "description",
                    key: "description",
                    sorter: false,
                },
                {
                    title: "SKU",
                    dataIndex: "sku",
                    sorter: false,
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
                    sorter: true,
                },
                {
                    title: "Stok",
                    dataIndex: "qty",
                    key: "qty",
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
    methods: {
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
        openProductModal(id) {
            this.$refs.productModal.showModal(id);
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
