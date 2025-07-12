<template>
    <Head title="Kelola Stok" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper notes-page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Kelola Stok</h4>
                        <h6>Kelola stok produk</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <ul class="table-top-head">
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
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <form @submit.prevent="submitForm">
                        <div class="card">
                            <div class="card-body add-product">
                                <div
                                    class="accordion-card-one accordion"
                                    id="accordionForecasting"
                                >
                                    <div class="accordion-item">
                                        <div
                                            class="accordion-header"
                                            id="headingOne"
                                        >
                                            <div
                                                class="accordion-button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne"
                                                aria-expanded="true"
                                                aria-controls="collapseOne"
                                            >
                                                <div class="addproduct-icon">
                                                    <h5>
                                                        <vue-feather
                                                            type="info"
                                                            class="add-info"
                                                        ></vue-feather
                                                        ><span
                                                            >Kelola Stok
                                                            Produk</span
                                                        >
                                                    </h5>
                                                    <a
                                                        href="javascript:void(0);"
                                                        ><vue-feather
                                                            type="chevron-down"
                                                            class="chevron-down-add"
                                                        ></vue-feather
                                                    ></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            id="collapseOne"
                                            class="accordion-collapse collapse show"
                                            aria-labelledby="headingOne"
                                        >
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label
                                                            class="form-label"
                                                            >Pilih Produk</label
                                                        >
                                                        <div
                                                            class="d-flex gap-2"
                                                        >
                                                            <AsyncProductSelect
                                                                v-model="
                                                                    form.products
                                                                "
                                                                :multiple="true"
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.products,
                                                                }"
                                                                :categoryFilter="
                                                                    filterProducts.category
                                                                "
                                                                :unitFilter="
                                                                    filterProducts.unit
                                                                "
                                                                :stockFilter="
                                                                    filterProducts.stockStatus
                                                                "
                                                                placeholder="Pilih produk untuk di kelola .."
                                                            />
                                                            <button
                                                                type="button"
                                                                class="btn"
                                                                :class="{
                                                                    'btn-primary':
                                                                        !filterProduct,
                                                                    'btn-danger':
                                                                        filterProduct,
                                                                }"
                                                                v-on:click="
                                                                    filterProduct =
                                                                        !filterProduct;
                                                                    if (
                                                                        !filterProduct
                                                                    )
                                                                        reset();
                                                                "
                                                            >
                                                                <i
                                                                    class="fa fa-filter"
                                                                    v-if="
                                                                        !filterProduct
                                                                    "
                                                                ></i>
                                                                <i
                                                                    class="fa fa-times"
                                                                    v-else
                                                                ></i>
                                                            </button>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                errors.products
                                                            "
                                                            class="invalid-feedback"
                                                        >
                                                            {{
                                                                errors.products
                                                            }}
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="card p-0"
                                                        :style="{
                                                            display:
                                                                filterProduct
                                                                    ? 'block'
                                                                    : 'none',
                                                        }"
                                                        id="filter_inputs"
                                                    >
                                                        <div
                                                            class="card-body px-3"
                                                        >
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-2 col-sm-6 col-12"
                                                                >
                                                                    <div
                                                                        class="input-blocks"
                                                                    >
                                                                        <vue-feather
                                                                            type="stop-circle"
                                                                            class="info-img"
                                                                        ></vue-feather>
                                                                        <vue-select
                                                                            :options="
                                                                                categoryOptions
                                                                            "
                                                                            v-model="
                                                                                filterProducts.category
                                                                            "
                                                                            id="categroyfilter"
                                                                            :settings="{
                                                                                allowClear: true,
                                                                                placeholder:
                                                                                    'Pilih Kategori',
                                                                            }"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-lg-2 col-sm-6 col-12"
                                                                >
                                                                    <div
                                                                        class="input-blocks"
                                                                    >
                                                                        <vue-feather
                                                                            type="speaker"
                                                                            class="info-img"
                                                                        ></vue-feather>
                                                                        <vue-select
                                                                            :options="
                                                                                unitOptions
                                                                            "
                                                                            v-model="
                                                                                filterProducts.unit
                                                                            "
                                                                            :settings="{
                                                                                allowClear: true,
                                                                                placeholder:
                                                                                    'Pilih Satuan',
                                                                            }"
                                                                            id="unitfilter"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-lg-2 col-sm-6 col-12"
                                                                >
                                                                    <div
                                                                        class="input-blocks"
                                                                    >
                                                                        <vue-feather
                                                                            type="package"
                                                                            class="info-img"
                                                                        ></vue-feather>
                                                                        <vue-select
                                                                            :options="
                                                                                stockStatusOptions
                                                                            "
                                                                            v-model="
                                                                                filterProducts.stockStatus
                                                                            "
                                                                            :settings="{
                                                                                allowClear: true,
                                                                                placeholder:
                                                                                    'Pilih Status',
                                                                            }"
                                                                            id="stockfilter"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-lg-3 col-sm-6 col-12 ms-auto"
                                                                >
                                                                    <div
                                                                        class="input-blocks d-flex justify-content-end"
                                                                    >
                                                                        <a
                                                                            class="btn btn-filters btn-reset"
                                                                            @click="
                                                                                reset
                                                                            "
                                                                        >
                                                                            Reset
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form
                                                    @submit.prevent="submitForm"
                                                >
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="modal-body-table"
                                                            >
                                                                <div
                                                                    class="d-flex justify-content-center align-items-center"
                                                                    v-if="
                                                                        form
                                                                            .products
                                                                            .length ===
                                                                        0
                                                                    "
                                                                >
                                                                    <p>
                                                                        Tidak
                                                                        ada
                                                                        produk
                                                                        yang
                                                                        dipilih
                                                                    </p>
                                                                </div>

                                                                <div
                                                                    class="table-responsive"
                                                                    v-if="
                                                                        form
                                                                            .products
                                                                            .length >
                                                                        0
                                                                    "
                                                                >
                                                                    <div
                                                                        class="d-flex justify-content-end mb-3"
                                                                    >
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-danger"
                                                                            @click="
                                                                                resetCart
                                                                            "
                                                                        >
                                                                            <i
                                                                                class="fa fa-trash me-1"
                                                                            ></i>
                                                                            Hapus
                                                                            Semua
                                                                        </button>
                                                                    </div>
                                                                    <table
                                                                        class="table datanew"
                                                                    >
                                                                        <thead>
                                                                            <tr>
                                                                                <th>
                                                                                    Produk
                                                                                </th>
                                                                                <th>
                                                                                    SKU
                                                                                </th>
                                                                                <th>
                                                                                    Kategori
                                                                                </th>
                                                                                <th>
                                                                                    Jumlah
                                                                                </th>
                                                                                <th>
                                                                                    Tipe
                                                                                </th>
                                                                                <th
                                                                                    class="no-sort"
                                                                                >
                                                                                    Aksi
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <!-- Dynamic rows for selected products -->
                                                                            <tr
                                                                                v-for="(
                                                                                    product,
                                                                                    index
                                                                                ) in form.products"
                                                                                :key="
                                                                                    product.id
                                                                                "
                                                                            >
                                                                                <td>
                                                                                    <div
                                                                                        class="productimgname"
                                                                                    >
                                                                                        <a
                                                                                            href="javascript:void(0);"
                                                                                            class="product-img stock-img"
                                                                                        >
                                                                                            <img
                                                                                                :src="
                                                                                                    product.image_url ||
                                                                                                    '/uploads/images/placeholder-image.webp'
                                                                                                "
                                                                                                alt="product"
                                                                                            />
                                                                                        </a>
                                                                                        <a
                                                                                            href="javascript:void(0);"
                                                                                            >{{
                                                                                                product.name
                                                                                            }}</a
                                                                                        >
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    {{
                                                                                        product.sku
                                                                                    }}
                                                                                </td>
                                                                                <td>
                                                                                    {{
                                                                                        product.category
                                                                                    }}
                                                                                </td>
                                                                                <td>
                                                                                    <div
                                                                                        class="product-quantity"
                                                                                    >
                                                                                        <span
                                                                                            class="quantity-btn"
                                                                                            @click="
                                                                                                decrementQty(
                                                                                                    index
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            <vue-feather
                                                                                                type="minus-circle"
                                                                                            />
                                                                                        </span>
                                                                                        <input
                                                                                            type="text"
                                                                                            class="quntity-input"
                                                                                            v-model.number="
                                                                                                product.adjustmentQty
                                                                                            "
                                                                                            @input="
                                                                                                validateQty(
                                                                                                    index
                                                                                                )
                                                                                            "
                                                                                        />
                                                                                        <span
                                                                                            class="quantity-btn"
                                                                                            @click="
                                                                                                incrementQty(
                                                                                                    index
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            <vue-feather
                                                                                                type="plus-circle"
                                                                                            />
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <vue-select
                                                                                        v-model="
                                                                                            product.adjustmentType
                                                                                        "
                                                                                        :options="
                                                                                            Addition
                                                                                        "
                                                                                        placeholder="Select Type"
                                                                                    />
                                                                                </td>
                                                                                <td
                                                                                    class="action-table-data"
                                                                                >
                                                                                    <div
                                                                                        class="edit-delete-action"
                                                                                    >
                                                                                        <a
                                                                                            class="confirm-text p-2"
                                                                                            @click="
                                                                                                removeProduct(
                                                                                                    index
                                                                                                )
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
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button
                                        type="submit"
                                        class="btn btn-submit"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AsyncProductSelect from "@/components/AsyncProductSelect.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

export default {
    components: {
        AsyncProductSelect,
        Head,
    },
    props: {
        productsCategories: {
            type: Object,
            required: true,
        },
        units: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            errors: {},
            filterProduct: false, // Untuk toggle filter
            filterProducts: {
                category: null,
                unit: null,
                stockStatus: null,
            },
            Addition: ["Penambahan", "Pengurangan"], // Options untuk dropdown type
            notes: "", // Untuk catatan
            form: {
                products: [],
            },
        };
    },
    computed: {
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
                text: `${unit.name} (${unit.short_name})`,
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
    created() {
        // Load saved products from localStorage
        const savedProducts = localStorage.getItem("stockManagementProducts");
        if (savedProducts) {
            this.form.products = JSON.parse(savedProducts);
        }

        // Load saved filter state from localStorage
        const savedFilters = localStorage.getItem("stockManagementFilters");
        if (savedFilters) {
            this.filterProducts = JSON.parse(savedFilters);
            // If there were active filters before, show the filter panel
            if (
                this.filterProducts.category ||
                this.filterProducts.unit ||
                this.filterProducts.stockStatus
            ) {
                this.filterProduct = true;
            }
        }
    },
    watch: {
        "form.products": {
            handler(newVal, oldVal) {
                if (!newVal) return;

                // Jika ada produk baru yang dipilih
                if (newVal.length > (oldVal?.length || 0)) {
                    const newProduct = newVal[newVal.length - 1];

                    // Cek apakah produk sudah ada
                    const exists = newVal.filter((p) => p.id === newProduct.id);
                    if (exists.length === 1) {
                        // Tambahkan properties tambahan untuk adjustment
                        newProduct.adjustmentQty = 1;
                        newProduct.adjustmentType = "Penambahan";
                    }
                }

                console.log("Updated products:", newVal);

                // Save to localStorage
                localStorage.setItem(
                    "stockManagementProducts",
                    JSON.stringify(newVal)
                );
            },
            deep: true,
        },
        filterProducts: {
            handler(newVal) {
                // Save to localStorage
                localStorage.setItem(
                    "stockManagementFilters",
                    JSON.stringify(newVal)
                );
            },
            deep: true,
        },
    },
    methods: {
        reset() {
            this.filterProducts = {
                category: null,
                unit: null,
                stockStatus: null,
            };
            // Save to localStorage
            localStorage.setItem(
                "stockManagementFilters",
                JSON.stringify(this.filterProducts)
            );
        },

        incrementQty(index) {
            this.form.products[index].adjustmentQty++;
        },

        decrementQty(index) {
            if (this.form.products[index].adjustmentQty > 1) {
                this.form.products[index].adjustmentQty--;
            }
        },

        validateQty(index) {
            const qty = this.form.products[index].adjustmentQty;
            if (qty < 1 || isNaN(qty)) {
                this.form.products[index].adjustmentQty = 1;
            }
        },

        removeProduct(index) {
            const product = this.form.products[index];

            Swal.fire({
                title: "Hapus Produk?",
                text: `Apakah Anda yakin ingin menghapus ${product.name} dari daftar?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove from form.products
                    this.form.products.splice(index, 1);

                    Swal.fire({
                        icon: "success",
                        title: "Dihapus!",
                        text: "Produk telah dihapus dari daftar.",
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }
            });
        },

        resetCart() {
            Swal.fire({
                title: "Reset Produk?",
                text: "Apakah Anda yakin ingin menghapus semua produk dari daftar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus Semua!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.form.products = [];
                    localStorage.removeItem("stockManagementProducts");

                    Swal.fire({
                        icon: "success",
                        title: "Dihapus!",
                        text: "Semua produk telah dihapus dari daftar.",
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }
            });
        },

        submitForm() {
            if (this.form.products.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Tidak ada produk!",
                    text: "Pilih minimal satu produk untuk melakukan penyesuaian stok.",
                });
                return;
            }

            const formData = {
                products: this.form.products.map((product) => ({
                    id: product.id,
                    quantity: product.adjustmentQty,
                    type: product.adjustmentType,
                })),
                notes: this.notes,
            };

            this.$inertia.post(this.route("stock-adjustment.store"), formData, {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "Penyesuaian stok berhasil dibuat.",
                    });
                    // Reset form
                    this.form.products = [];
                    this.notes = "";
                    // Clear localStorage
                    localStorage.removeItem("stockManagementProducts");
                },
                onError: (errors) => {
                    this.errors = errors;
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
