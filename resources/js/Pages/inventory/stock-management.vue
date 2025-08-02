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
                                    id="accordionManageStock"
                                >
                                    <div class="accordion-item">
                                        <div
                                            class="accordion-header"
                                            id="manageStock"
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
                                                        />
                                                        <span
                                                            >Kelola Stok Produk
                                                        </span>
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
                                            aria-labelledby="manageStock"
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
                                                                    class="col-lg-2 col-sm-6 col-12 mb-3"
                                                                >
                                                                    <vue-select
                                                                        :options="
                                                                            categoryOptions
                                                                        "
                                                                        v-model="
                                                                            filterProducts.category
                                                                        "
                                                                        id="categroyfilter"
                                                                        placeholder="Filter Kategori"
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-lg-2 col-sm-6 col-12 mb-3"
                                                                >
                                                                    <vue-select
                                                                        :options="
                                                                            unitOptions
                                                                        "
                                                                        v-model="
                                                                            filterProducts.unit
                                                                        "
                                                                        placeholder="Filter Unit"
                                                                        id="unitfilter"
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-lg-2 col-sm-6 col-12 mb-3"
                                                                >
                                                                    <vue-select
                                                                        :options="
                                                                            stockStatusOptions
                                                                        "
                                                                        v-model="
                                                                            filterProducts.stock_status
                                                                        "
                                                                        placeholder="Filter Status Stok"
                                                                        id="stockfilter"
                                                                    />
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
                                                                                resetExcept
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
                                                                                    Stok
                                                                                    Tersedia
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
                                                                                <th>
                                                                                    Catatan
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
                                                                                                v-lazy="
                                                                                                    product.image_url
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
                                                                                        product.qty +
                                                                                        " " +
                                                                                        product.unit
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
                                                                                    <b-form-select
                                                                                        v-model="
                                                                                            product.adjustmentType
                                                                                        "
                                                                                        :options="
                                                                                            typeOptions
                                                                                        "
                                                                                        placeholder="Pilih Tipe"
                                                                                        @select="
                                                                                            handleTypeChange(
                                                                                                index
                                                                                            )
                                                                                        "
                                                                                    />
                                                                                </td>
                                                                                <td>
                                                                                    <a
                                                                                        href="javascript:void(0);"
                                                                                        class="view-note"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#noteModal"
                                                                                        @click="
                                                                                            prepareNoteModal(
                                                                                                index
                                                                                            )
                                                                                        "
                                                                                    >
                                                                                        <span
                                                                                            v-if="
                                                                                                product.note
                                                                                            "
                                                                                        >
                                                                                            {{
                                                                                                truncateText(
                                                                                                    product.note,
                                                                                                    15
                                                                                                )
                                                                                            }}
                                                                                        </span>
                                                                                        <span
                                                                                            v-else
                                                                                        >
                                                                                            Tambah
                                                                                            Catatan
                                                                                        </span>
                                                                                    </a>
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
                                                    <div
                                                        class="d-flex justify-content-end"
                                                    >
                                                        <button
                                                            type="submit"
                                                            class="btn btn-submit"
                                                        >
                                                            Submit
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body add-product">
                            <div
                                class="accordion-card-one accordion"
                                id="accordionStockHistory"
                            >
                                <div class="accordion-item">
                                    <div
                                        class="accordion-header"
                                        id="headingHistory"
                                    >
                                        <div
                                            class="accordion-button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapseHistory"
                                            aria-expanded="true"
                                            aria-controls="collapseHistory"
                                        >
                                            <div class="addproduct-icon">
                                                <h5>
                                                    <vue-feather
                                                        type="clock"
                                                        class="add-info"
                                                    ></vue-feather>
                                                    <span
                                                        >Riwayat Perubahan
                                                        Stok</span
                                                    >
                                                </h5>
                                                <a href="javascript:void(0);">
                                                    <vue-feather
                                                        type="chevron-down"
                                                        class="chevron-down-add"
                                                    ></vue-feather>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        id="collapseHistory"
                                        class="accordion-collapse collapse show"
                                        aria-labelledby="headingHistory"
                                    >
                                        <div class="accordion-body">
                                            <StockTransactionsTable
                                                ref="stockTransactionsTable"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <teleport to="body">
        <div
            class="modal fade"
            id="noteModal"
            tabindex="-1"
            aria-labelledby="noteModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="noteModalLabel">
                            <span v-if="currentProduct">{{
                                currentProduct.name
                            }}</span>
                            <span v-else>Tambah Catatan</span>
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <label for="productNote" class="form-label">
                            Catatan
                        </label>
                        <textarea
                            class="form-control"
                            id="productNote"
                            rows="4"
                            v-model="currentNote"
                            :placeholder="
                                'Tambahkan catatan untuk produk' +
                                (currentProduct
                                    ? ' ' + currentProduct.name
                                    : '...')
                            "
                        ></textarea>
                    </div>
                    <div class="p-3 d-flex justify-content-end btn-list">
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-dismiss="modal"
                            @click="saveNote"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
import AsyncProductSelect from "@/components/AsyncProductSelect.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import StockTransactionsTable from "@/Pages/stock/stock-transactions-table.vue";

export default {
    components: {
        AsyncProductSelect,
        Head,
        StockTransactionsTable,
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
        stockTransactions: {
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
            typeOptions: [
                { value: "in", text: "Penambahan" },
                { value: "out", text: "Pengurangan" },
            ],
            form: {
                products: [],
                notes: "",
            },
            currentProductIndex: null,
            currentProduct: null,
            currentNote: "",
        };
    },
    computed: {
        categoryOptions() {
            const defaultOption = {
                value: null,
                label: "Pilih Kategori",
            };
            const category = this.productsCategories.map((category) => ({
                value: category.id,
                label: category.name,
            }));

            return [...category];
        },
        unitOptions() {
            const defaultOption = {
                value: null,
                label: "Pilih Satuan",
            };
            const unit = this.units.map((unit) => ({
                value: unit.id,
                label: `${unit.name} (${unit.short_name})`,
            }));

            return [...unit];
        },
        stockStatusOptions() {
            return [
                { value: "available", label: "Tersedia" },
                { value: "low", label: "Stok Rendah" },
                { value: "out", label: "Habis" },
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
                        newProduct.adjustmentType = "in";
                        // Simpan stok saat ini untuk keperluan validasi
                        newProduct.currentStock = newProduct.qty || 0;
                    }
                }

                // Save to localStorage
                localStorage.setItem(
                    "stockManagementProducts",
                    JSON.stringify(newVal)
                );
            },
            deep: true,
        },
        // Watch untuk memvalidasi qty saat tipe berubah
        "form.products.*.adjustmentType": function (newVal, oldVal) {
            // Loop melalui semua produk dan validasi
            for (let i = 0; i < this.form.products.length; i++) {
                const product = this.form.products[i];

                // Jika tipe adalah Pengurangan, validasi qty
                if (product.adjustmentType === "out") {
                    this.validateQtyForReduction(i);
                }
            }
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
            const product = this.form.products[index];

            // Jika tipe pengurangan, batasi nilai maksimal
            if (product.adjustmentType === "out") {
                // Hanya bisa mengurangi sebanyak stok yang ada
                if (product.adjustmentQty < product.currentStock) {
                    product.adjustmentQty++;
                } else {
                    // Tampilkan pesan jika mencoba mengurangi lebih dari stok
                    Swal.fire({
                        icon: "warning",
                        title: "Stok tidak mencukupi",
                        text: `Maksimum pengurangan untuk ${product.name} adalah ${product.currentStock} unit.`,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            } else {
                // Untuk penambahan tidak ada batasan
                product.adjustmentQty++;
            }
        },

        decrementQty(index) {
            if (this.form.products[index].adjustmentQty > 1) {
                this.form.products[index].adjustmentQty--;
            }
        },

        validateQty(index) {
            const product = this.form.products[index];
            const qty = product.adjustmentQty;

            if (qty < 1 || isNaN(qty)) {
                product.adjustmentQty = 1;
            }

            // Jika tipe pengurangan, validasi jumlah maksimal
            if (product.adjustmentType === "out") {
                this.validateQtyForReduction(index);
            }
        },

        // Tambahkan method baru untuk validasi pengurangan
        validateQtyForReduction(index) {
            const product = this.form.products[index];

            // Jika qty melebihi stok yang tersedia
            if (product.adjustmentQty > product.currentStock) {
                // Set qty ke stok maksimal
                product.adjustmentQty = product.currentStock;

                // Tampilkan pesan jika stok habis
                if (product.currentStock <= 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Stok Tidak Tersedia",
                        text: `Produk ${product.name} tidak memiliki stok untuk dikurangi.`,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                } else {
                    // Tampilkan pesan stok terbatas
                    Swal.fire({
                        icon: "warning",
                        title: "Stok Terbatas",
                        text: `Maksimal pengurangan untuk ${product.name} adalah ${product.currentStock} unit.`,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            }
        },

        // Method untuk mengubah tipe adjustment
        handleTypeChange(index) {
            const product = this.form.products[index];

            // console.log(
            //     `Tipe produk ${product.name} diubah menjadi ${product.adjustmentType}`
            // );

            // Jika tipe berubah ke Pengurangan, validasi qty
            if (product.adjustmentType === "out") {
                this.validateQtyForReduction(index);
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
                    qty: product.adjustmentQty,
                    type: product.adjustmentType,
                    note: product.note || "",
                })),
            };

            this.$inertia.post(this.route("stock-management.store"), formData, {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "Penyesuaian stok berhasil dibuat.",
                    });
                    // Reset form
                    this.form.products = [];
                    this.form.notes = "";
                    // Clear localStorage
                    localStorage.removeItem("stockManagementProducts");
                    this.$refs.stockTransactionsTable?.fetch();
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

        prepareNoteModal(index) {
            this.currentProductIndex = index;
            this.currentProduct = this.form.products[index];
            this.currentNote = this.currentProduct.note || "";
        },

        saveNote() {
            if (
                this.currentProductIndex !== null &&
                this.form.products[this.currentProductIndex]
            ) {
                // Update catatan produk
                this.form.products[this.currentProductIndex].note =
                    this.currentNote;

                // Simpan ke localStorage
                localStorage.setItem(
                    "stockManagementProducts",
                    JSON.stringify(this.form.products)
                );

                // Reset state
                this.currentProductIndex = null;
                this.currentProduct = null;
                this.currentNote = "";
            }
        },

        truncateText(text, length) {
            if (!text) return "";
            return text.length > length
                ? text.substring(0, length) + "..."
                : text;
        },
    },
};
</script>
