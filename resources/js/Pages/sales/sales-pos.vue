<template>
    <Head title="Create Order" />
    <layout-header></layout-header>

    <div class="page-wrapper pos-pg-wrapper ms-0">
        <div class="content pos-design p-0">
            <div class="btn-row d-sm-flex align-items-center">
                <Link
                    :href="route('orders.index')"
                    class="btn btn-secondary mb-xs-3"
                    ><span class="me-1 d-flex align-items-center"
                        ><vue-feather
                            type="shopping-cart"
                            class="feather-16"
                        ></vue-feather></span
                    >Riwayat Pesanan</Link
                >
                <a type="button" class="btn btn-info" @click="resetCart">
                    <span class="me-1 d-flex align-items-center"
                        ><vue-feather
                            type="rotate-cw"
                            class="feather-16"
                        ></vue-feather></span
                    >Reset
                </a>
            </div>

            <div class="row align-items-start pos-wrapper">
                <div class="col-md-12 col-lg-8">
                    <div class="pos-categories tabs_wrapper h-100">
                        <h5>Kategori</h5>
                        <p>Pilih berdasarkan kategori</p>
                        <ul class="tabs owl-carousel pos-category">
                            <Carousel
                                ref="carouselCategory"
                                :wrap-around="false"
                                :settings="settings"
                                :breakpoints="breakpoints"
                            >
                                <Slide
                                    v-for="item in productCategories"
                                    :key="item.id"
                                    @click="selectCategory(item.id)"
                                    :class="{
                                        active: filters.category === item.id,
                                    }"
                                >
                                    <li :id="item.id">
                                        <a href="javascript:void(0);">
                                            <img
                                                v-lazy="
                                                    item.image ||
                                                    '/uploads/images/category_default.svg'
                                                "
                                                :alt="item.name"
                                                style="max-width: 40px"
                                            />
                                        </a>
                                        <h6>
                                            <a href="javascript:void(0);">{{
                                                item.name
                                            }}</a>
                                        </h6>
                                        <span>{{ item.items }} Item</span>
                                    </li>
                                </Slide>
                                <template #addons>
                                    <div class="owl-nav">
                                        <button
                                            type="button"
                                            role="presentation"
                                            class="owl-prev"
                                            @click="prev"
                                        >
                                            <i
                                                class="fas fa-chevron-left"
                                            ></i></button
                                        ><button
                                            type="button"
                                            role="presentation"
                                            class="owl-next"
                                            @click="next"
                                        >
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </template>
                            </Carousel>
                        </ul>
                        <div class="pos-products">
                            <div
                                class="d-flex align-items-center justify-content-between mb-3"
                            >
                                <h5 class="mb-0">Produk</h5>
                                <div class="search-set">
                                    <div
                                        class="search-input"
                                        style="margin-right: 0"
                                    >
                                        <input
                                            type="text"
                                            placeholder="Cari Produk"
                                            class="dark-input"
                                            v-model="filters.search"
                                        />
                                        <a
                                            href="javascript:void(0);"
                                            v-show="filters.search"
                                            class="btn btn-searchset"
                                            @click="filters.search = ''"
                                            ><i
                                                data-feather="x"
                                                class="feather-x"
                                            ></i
                                        ></a>
                                    </div>
                                </div>
                            </div>
                            <div class="tabs_container">
                                <div class="tab_content active" data-tab="all">
                                    <div class="row">
                                        <div
                                            v-for="product in products.data"
                                            :key="product.id"
                                            class="col-sm-2 col-md-6 col-lg-3 col-xl-3 pe-2"
                                            @click="addProduct(product)"
                                        >
                                            <div
                                                class="product-info default-cover card"
                                                :class="{
                                                    active: form.addedProducts.some(
                                                        (p) =>
                                                            p.id === product.id
                                                    ),
                                                }"
                                            >
                                                <a
                                                    href="javascript:void(0);"
                                                    class="img-bg"
                                                >
                                                    <img
                                                        v-lazy="product.image"
                                                        alt="Products"
                                                        class="product-image"
                                                    />
                                                    <span>
                                                        <vue-feather
                                                            type="check"
                                                            class="feather-16"
                                                        ></vue-feather>
                                                    </span>
                                                </a>
                                                <h6 class="cat-name">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >{{
                                                            product.category
                                                                .name
                                                        }}</a
                                                    >
                                                </h6>
                                                <h6 class="product-name">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >{{ product.name }}</a
                                                    >
                                                </h6>
                                                <div
                                                    class="d-flex align-items-center justify-content-between price"
                                                >
                                                    <span
                                                        >{{ product.qty }}
                                                        {{
                                                            product.unit.name
                                                        }}</span
                                                    >
                                                    <p>
                                                        Rp.
                                                        {{
                                                            $helpers.formatRupiah(
                                                                product.price
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="isLoading"
                                            class="text-center py-2"
                                        >
                                            Loading...
                                        </div>
                                    </div>

                                    <div
                                        class="d-flex align-items-center justify-content-between my-3"
                                    >
                                        <!-- Per Page Selector -->
                                        <b-form-group
                                            label="Tampilkan"
                                            label-for="per-page-select"
                                            label-cols="auto"
                                            class="mb-0"
                                        >
                                            <b-form-select
                                                id="per-page-select"
                                                v-model="filters.per_page"
                                                :options="perPageOptions"
                                                @change="handlePerPageChange"
                                                size="sm"
                                            />
                                        </b-form-group>

                                        <!-- Pagination -->
                                        <PaginationStyle3
                                            :current-page="
                                                products.current_page
                                            "
                                            :total-pages="products.last_page"
                                            :max-visible-pages="3"
                                            @page-changed="handlePageChange"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 ps-0">
                    <aside class="product-order-list">
                        <div class="customer-info block-section">
                            <h6>Informasi Pelanggan</h6>
                            <div class="input-block d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <vue-select
                                        v-model="form.name"
                                        :options="Walk"
                                        id="walkin"
                                        placeholder="Walk in Customer"
                                    />
                                </div>
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-primary btn-icon"
                                    data-bs-toggle="modal"
                                    data-bs-target="#create"
                                    ><vue-feather
                                        type="user-plus"
                                        class="feather-16"
                                    ></vue-feather
                                ></a>
                            </div>
                        </div>

                        <div class="product-added block-section">
                            <div
                                class="head-text d-flex align-items-center justify-content-between"
                            >
                                <h6 class="d-flex align-items-center mb-0">
                                    Produk ditambahkan<span class="count">{{
                                        totalProducts
                                    }}</span>
                                </h6>
                                <a
                                    href="javascript:void(0);"
                                    class="d-flex align-items-center text-danger"
                                    @click="resetCart"
                                    ><vue-feather
                                        type="x"
                                        class="feather-16 me-1"
                                    ></vue-feather
                                    >Hapus Semua</a
                                >
                            </div>
                            <div class="product-wrap">
                                <div
                                    v-for="product in form.addedProducts"
                                    :key="product.id"
                                    class="product-list d-flex align-items-center justify-content-between"
                                >
                                    <div
                                        class="d-flex align-items-center product-info"
                                    >
                                        <a
                                            href="javascript:void(0);"
                                            class="img-bg"
                                        >
                                            <img
                                                v-lazy="product.image"
                                                :alt="product.name"
                                            />
                                        </a>
                                        <div class="info">
                                            <span>{{ product.sku }}</span>
                                            <h6>
                                                <a href="javascript:void(0);">{{
                                                    product.name
                                                }}</a>
                                            </h6>
                                            <p>
                                                Rp.
                                                {{
                                                    $helpers.formatRupiah(
                                                        product.price
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="qty-item text-center">
                                        <a
                                            href="javascript:void(0);"
                                            class="dec d-flex justify-content-center align-items-center"
                                            @click="
                                                product.qty > 1
                                                    ? product.qty--
                                                    : removeProduct(product)
                                            "
                                        >
                                            <vue-feather
                                                type="minus-circle"
                                                class="feather-14"
                                            ></vue-feather>
                                        </a>
                                        <input
                                            type="text"
                                            class="form-control text-center"
                                            name="qty"
                                            :value="product.qty"
                                            readonly
                                        />
                                        <a
                                            href="javascript:void(0);"
                                            class="inc d-flex justify-content-center align-items-center"
                                            @click="
                                                product.qty < product.max_qty
                                                    ? product.qty++
                                                    : null
                                            "
                                        >
                                            <vue-feather
                                                type="plus-circle"
                                                class="feather-14"
                                            ></vue-feather>
                                        </a>
                                    </div>
                                    <div
                                        class="d-flex align-items-center action"
                                    >
                                        <a
                                            class="btn-icon delete-icon confirm-text"
                                            href="javascript:void(0);"
                                            @click="removeProduct(product)"
                                        >
                                            <vue-feather
                                                type="trash-2"
                                                class="feather-14"
                                            ></vue-feather>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block-section">
                            <div class="selling-info">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="input-block">
                                            <label>Pengiriman</label>
                                            <vue-select
                                                :options="shipping_method"
                                                v-model="form.shipping_method"
                                                id="shipping_method"
                                                label="text"
                                                :reduce="(option) => option.id"
                                                placeholder="Pilih Pengiriman"
                                                @update:modelValue="
                                                    (val) => {
                                                        if (val === 'pickup') {
                                                            form.shipping_fee =
                                                                null;
                                                        }
                                                    }
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-sm-6"
                                        v-show="
                                            form.shipping_method === 'delivery'
                                        "
                                    >
                                        <div class="input-block">
                                            <label>Biaya Pengiriman</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="form.shipping_fee"
                                                placeholder="Rp. 0"
                                                :disabled="
                                                    form.shipping_method ===
                                                    'pickup'
                                                "
                                            />
                                        </div>
                                    </div>

                                    <!-- <div class="col-12 col-sm-6">
                                        <div class="input-block">
                                            <label>Discount</label>
                                            <vue-select
                                                :options="Discount"
                                                id="discountf"
                                                placeholder="10%"
                                            />
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="order-total">
                                <table
                                    class="table table-responsive table-borderless"
                                >
                                    <tr>
                                        <td>Sub Total</td>
                                        <td class="text-end">
                                            Rp.
                                            {{
                                                $helpers.formatRupiah(subtotal)
                                            }}
                                        </td>
                                    </tr>
                                    <tr v-for="tax in taxes" :key="tax.id">
                                        <td>
                                            {{ tax.name }}
                                            <template v-if="tax.percent">
                                                ( {{ tax.percent }}% )
                                            </template>
                                        </td>
                                        <td class="text-end">
                                            Rp.
                                            {{
                                                $helpers.formatRupiah(
                                                    tax.percent
                                                        ? (subtotal *
                                                              tax.percent) /
                                                              100
                                                        : Number(
                                                              tax.fixed_amount
                                                          ) || 0
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr
                                        v-show="
                                            form.shipping_method === 'delivery'
                                        "
                                    >
                                        <td>Pengiriman</td>
                                        <td class="text-end">
                                            Rp.
                                            {{
                                                $helpers.formatRupiah(
                                                    Number(form.shipping_fee)
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-end">
                                            Rp.
                                            {{
                                                $helpers.formatRupiah(
                                                    subtotal +
                                                        (Array.isArray(taxes)
                                                            ? taxes.reduce(
                                                                  (
                                                                      total,
                                                                      tax
                                                                  ) =>
                                                                      total +
                                                                      (tax.percent
                                                                          ? (subtotal *
                                                                                tax.percent) /
                                                                            100
                                                                          : Number(
                                                                                tax.fixed_amount
                                                                            ) ||
                                                                            0),
                                                                  0
                                                              )
                                                            : 0) +
                                                        (form.shipping_method ===
                                                        "delivery"
                                                            ? Number(
                                                                  form.shipping_fee
                                                              ) || 0
                                                            : 0)
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="d-grid btn-block">
                            <button
                                type="submit"
                                class="btn btn-secondary"
                                @click="submitForm"
                            >
                                Submit
                            </button>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <pos-modal></pos-modal>
</template>

<style scoped>
.product-image {
    object-fit: cover; /* Memastikan gambar tetap proporsional */
    width: 20%; /* Gambar akan menyesuaikan lebar elemen induknya */
}
</style>

<script>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Carousel, Navigation, Slide } from "vue3-carousel";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import { BPagination, BFormGroup, BFormSelect } from "bootstrap-vue-3";
import PaginationStyle3 from "@/components/pagination/PaginationStyle3.vue";

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "orders.create",
            ["search", "category"],
            { only: ["products"] }
        );

        const carouselCategory = ref(null);
        const prev = () => carouselCategory.value?.prev();
        const next = () => carouselCategory.value?.next();

        const form = useForm({
            name: "",
            shipping_method: "pickup",
            shipping_fee: null,
            subtotal: 0,
            total: 0,
            addedProducts: [],
        });

        return { filters, fetch, reset, prev, next, form, carouselCategory };
    },
    props: {
        taxes: {
            type: Object,
            required: true,
        },
        products: {
            type: Object,
            required: true,
        },
        productCategories: {
            type: Object,
            required: true,
        },
    },
    components: {
        Head,
        Link,
        Carousel,
        Slide,
        Navigation,
        BPagination,
        BFormGroup,
        BFormSelect,
        PaginationStyle3,
    },
    created() {
        this.filters.per_page = this.products.per_page;
        this.productCategories.unshift({
            id: null,
            name: "Semua Kategori",
            items: this.products.length,
        });

        // Load added products from localStorage
        const savedProducts = localStorage.getItem("addedProducts");
        if (savedProducts) {
            this.form.addedProducts = JSON.parse(savedProducts);
        }

        // Load selected category from localStorage
        this.filters.category =
            JSON.parse(localStorage.getItem("selectedCategory")) || null;
    },
    watch: {
        "form.addedProducts": {
            handler(newValue) {
                localStorage.setItem("addedProducts", JSON.stringify(newValue));
            },
            deep: true,
        },
    },
    computed: {
        subtotal() {
            return this.form.addedProducts.reduce(
                (total, product) => total + product.qty * product.price,
                0
            );
        },
        totalProducts() {
            return this.form.addedProducts.reduce(
                (total, product) => total + product.qty,
                0
            );
        },
    },
    data() {
        return {
            errors: [],
            isLoading: false,
            perPageOptions: [
                { value: 12, text: "12" },
                { value: 24, text: "24" },
            ],

            Walk: ["Walk in Customer", "John", "Smith", "Ana", "Elza"],
            Airpod: [
                "Search Products",
                "IPhone 14 64GB",
                "MacBook Pro",
                "Rolex Tribute V3",
                "Red Nike Angelo",
                "Airpod 2",
                "Oldest",
            ],
            GST: [
                "GST 5%",
                "GST 10%",
                "GST 15%",
                "GST 20%",
                "GST 25%",
                "GST 30%",
            ],
            shipping_method: [
                {
                    id: null,
                    text: "Pilih Pengiriman",
                },
                {
                    id: "pickup",
                    text: "Ambil Sendiri",
                },
                {
                    id: "delivery",
                    text: "Pengiriman",
                },
            ],
            Discount: ["10%", "10%", "15%", "20%", "25%", "30%"],
            settings: {
                itemsToShow: 2, // Increased from 1 to show more items
                snapAlign: "center",
                loop: true,
                itemsToScroll: 1,
            },
            breakpoints: {
                575: {
                    itemsToShow: 4, // Increased to show more items
                    snapAlign: "center",
                },
                767: {
                    itemsToShow: 5, // Increased to show more items
                    snapAlign: "center",
                },
                991: {
                    itemsToShow: 6, // Increased to show more items
                    snapAlign: "center",
                },
                1024: {
                    itemsToShow: 7, // Increased to show more items
                    snapAlign: "start",
                },
            },
        };
    },

    methods: {
        showConfirmation() {
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
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        confirmButtonClass: "btn btn-success",
                    });
                }
            });
        },

        handlePageChange(newPage) {
            console.log("Page changed to:", newPage);
            this.filters.page = newPage;
            this.fetch();
        },

        handlePerPageChange(newPerPage) {
            this.filters.per_page = newPerPage;
            this.filters.page = 1;
            this.fetch();
        },

        addProduct(product) {
            const existingProductIndex = this.form.addedProducts.findIndex(
                (p) => p.id === product.id
            );

            if (existingProductIndex !== -1) {
                // Jika produk sudah ada, hapus dari daftar
                this.form.addedProducts.splice(existingProductIndex, 1);
            } else {
                // Jika produk belum ada, tambahkan ke daftar
                this.form.addedProducts.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    price: product.price,
                    max_qty: product.qty,
                    qty: 1,
                });
            }

            // console.log("Updated Products:", this.form.addedProducts);
        },
        removeProduct(product) {
            this.form.addedProducts = this.form.addedProducts.filter(
                (p) => p.id !== product.id
            );
        },
        selectCategory(category) {
            localStorage.setItem("selectedCategory", JSON.stringify(category));
            this.filters.category = category;
        },
        resetFilters() {
            this.reset();
        },
        resetCart() {
            this.form.addedProducts = [];
            localStorage.removeItem("addedProducts");
        },
        submitForm() {
            this.isLoading = true;
            this.form.post(this.route("orders.store"), {
                onError: (errors) => {
                    Swal.fire({
                        icon: "error",
                        title: "Uups...",
                        text: errors
                            ? typeof errors === "object"
                                ? Object.values(errors)[0]
                                : errors
                            : "Something went wrong!",
                    });
                },
            });
        },
    },
};
</script>

<style>
.select2-container {
    z-index: 1 !important;
}
</style>
