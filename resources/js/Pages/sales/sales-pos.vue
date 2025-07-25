<template>
    <Head title="Buat Pesanan" />
    <layout-header></layout-header>

    <div class="page-wrapper pos-pg-wrapper ms-0">
        <div class="content pos-design p-0">
            <div class="btn-row d-sm-flex align-items-center">
                <Link
                    :href="this.$page.props.previousUrl"
                    v-if="hasPreviousPage"
                    class="btn btn-dark"
                >
                    <vue-feather type="arrow-left" class="me-2"></vue-feather>
                    Kembali
                </Link>
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
                                            class="col-sm-2 col-md-6 col-lg-3 col-xl-3 mb-4"
                                            @click="addProduct(product)"
                                        >
                                            <div
                                                class="product-info default-cover card pt-3 h-100"
                                                :class="{
                                                    active: form.addedProducts.some(
                                                        (p) =>
                                                            p.id === product.id
                                                    ),
                                                }"
                                            >
                                                <!-- buat seperti footer disini ada rincian produk tombol -->
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-3"
                                                >
                                                    <span>
                                                        {{ product.sku }}
                                                    </span>

                                                    <a
                                                        href="javascript:void(0);"
                                                        @click.stop="
                                                            openProductModal(
                                                                product.id
                                                            )
                                                        "
                                                        class="link-secondary"
                                                    >
                                                        Lihat Produk
                                                    </a>
                                                </div>

                                                <a
                                                    href="javascript:void(0);"
                                                    class="img-bg"
                                                >
                                                    <img
                                                        v-lazy="
                                                            product.image_url
                                                        "
                                                        :alt="product.name"
                                                        class="product-image rounded"
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
                                                    class="d-flex align-items-center justify-content-between price mb-2"
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
                        <div
                            class="customer-info block-section"
                            v-if="hasRole('Admin')"
                        >
                            <h6>Pelanggan</h6>
                            <div class="input-block d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <AsyncUserSelect
                                        v-model="customerSelected"
                                        :multiple="false"
                                        :statusFilter="true"
                                        :roleFilter="'Pelanggan'"
                                        placeholder="Pilih Pelanggan"
                                        @update:modelValue="updateCustomerId"
                                    />
                                </div>
                                <div class="d-flex">
                                    <a
                                        v-if="customerSelected"
                                        href="javascript:void(0);"
                                        class="btn btn-outline-primary btn-icon"
                                        @click="editCustomer"
                                    >
                                        <vue-feather
                                            type="edit"
                                            class="feather-16"
                                        ></vue-feather>
                                    </a>
                                    <a
                                        href="javascript:void(0);"
                                        class="btn btn-outline-primary btn-icon"
                                        @click="addCustomer"
                                    >
                                        <vue-feather
                                            type="user-plus"
                                            class="feather-16"
                                        ></vue-feather>
                                    </a>
                                </div>
                            </div>
                            <div v-if="errors.customer_id" class="text-danger">
                                {{ errors.customer_id[0] }}
                            </div>
                        </div>
                        <div v-else>
                            <div
                                class="d-flex align-items-center justify-content-between"
                            >
                                <div
                                    class="flex-grow-1 d-flex align-items-center"
                                >
                                    <img
                                        v-lazy="
                                            $page.props.auth.user
                                                .profile_photo_url
                                        "
                                        :alt="$page.props.auth.user.name"
                                        class="rounded-circle me-2"
                                        style="
                                            width: 40px;
                                            height: 40px;
                                            object-fit: cover;
                                        "
                                    />
                                    <h6 class="mb-0">
                                        {{ $page.props.auth.user.name }}
                                        <br />
                                        <span class="text-muted small">{{
                                            $page.props.auth.user.email
                                        }}</span>
                                    </h6>
                                </div>
                                <div class="d-flex">
                                    <a
                                        href="javascript:void(0);"
                                        class="btn btn-outline-primary"
                                        @click="editCustomer"
                                    >
                                        <vue-feather
                                            type="edit"
                                            class="feather-16"
                                        ></vue-feather>

                                        Edit Profile
                                    </a>
                                </div>
                            </div>
                            <div v-if="errors.customer_id" class="text-danger">
                                {{ errors.customer_id[0] }}
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
                                                class="rounded me-2"
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
                        <transition name="fade">
                            <div v-if="form.addedProducts.length">
                                <div class="block-section">
                                    <div class="selling-info">
                                        <div class="row">
                                            <div
                                                class="col-12"
                                                :class="
                                                    hasRole('Admin') &&
                                                    form.shipping_method ===
                                                        'delivery'
                                                        ? 'col-sm-6'
                                                        : ''
                                                "
                                            >
                                                <div class="input-block">
                                                    <label>Pengiriman</label>
                                                    <vue-select
                                                        :options="
                                                            shipping_method
                                                        "
                                                        v-model="
                                                            form.shipping_method
                                                        "
                                                        id="shipping_method"
                                                        label="text"
                                                        :reduce="
                                                            (option) =>
                                                                option.id
                                                        "
                                                        placeholder="Pilih Pengiriman"
                                                        @update:modelValue="
                                                            (val) => {
                                                                if (
                                                                    val ===
                                                                    'pickup'
                                                                ) {
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
                                                    form.shipping_method ===
                                                        'delivery' &&
                                                    hasRole('Admin')
                                                "
                                            >
                                                <div class="input-block">
                                                    <label
                                                        >Biaya Pengiriman</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="
                                                            form.shipping_fee
                                                        "
                                                        placeholder="Rp. 0"
                                                        :disabled="
                                                            form.shipping_method ===
                                                            'pickup'
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <!-- Tampilkan pesan info untuk pelanggan -->
                                            <div
                                                class="col-12 mt-3"
                                                v-if="
                                                    form.shipping_method ===
                                                        'delivery' &&
                                                    !hasRole('Admin')
                                                "
                                            >
                                                <div
                                                    class="alert alert-primary alert-dismissible fade show custom-alert-icon shadow-sm d-flex align-items-center"
                                                    role="alert"
                                                >
                                                    <i
                                                        class="feather-info flex-shrink-0 me-2"
                                                    ></i>
                                                    Biaya pengiriman saat ini
                                                    masih membutuhkan konfirmasi
                                                    dari admin setelah pesanan
                                                    dibuat.
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
                                                        $helpers.formatRupiah(
                                                            subtotal
                                                        )
                                                    }}
                                                </td>
                                            </tr>
                                            <tr
                                                v-for="tax in taxes"
                                                :key="tax.id"
                                            >
                                                <td>
                                                    {{ tax.name }}
                                                    <template
                                                        v-if="tax.percent"
                                                    >
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
                                                    form.shipping_method ===
                                                    'delivery'
                                                "
                                            >
                                                <td>Pengiriman</td>
                                                <td class="text-end">
                                                    Rp.
                                                    {{
                                                        $helpers.formatRupiah(
                                                            Number(
                                                                form.shipping_fee
                                                            )
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
                                                                (Array.isArray(
                                                                    taxes
                                                                )
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
                            </div>
                        </transition>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <ProductDescriptionModal
            ref="productModal"
            :product-id="selectedProductId"
        />

        <UserModal
            ref="userModal"
            :is-edit="!!customerSelectedOpenModal"
            :user-id="customerSelectedOpenModal"
            modal-id="customer-modal"
            default-role="Pelanggan"
            :setErrors="userModalErrors"
            @user-submit="handleCustomerSubmit"
        />
    </Teleport>
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
import ProductDescriptionModal from "@/components/modal/product-description-modal.vue";
import AsyncUserSelect from "@/components/AsyncUserSelect.vue";
import UserModal from "@/components/modal/user-modal.vue";

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
            customer_id: null,
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
        ProductDescriptionModal,
        AsyncUserSelect,
        UserModal,
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
            userModalErrors: {},
            selectedProductId: null,
            isLoading: false,
            perPageOptions: [
                { value: 12, text: "12" },
                { value: 24, text: "24" },
            ],

            hasPreviousPage: false,
            customerSelected: null,
            customerSelectedOpenModal: null,

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
    mounted() {
        this.checkPreviousPage();
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
            if (!product.qty || product.qty <= 0) return;

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
                    image: product.image_url,
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

            if (
                !this.hasRole("Admin") &&
                this.form.shipping_method === "delivery"
            ) {
                this.form.shipping_fee = 0;
            }

            this.form.post(this.route("orders.store"), {
                onError: (errors) => {
                    const fieldErrors = [
                        "customer_id",
                        "name",
                        "phone",
                        "address",
                        "email",
                    ];
                    const hasFieldError = fieldErrors.some(
                        (field) => errors[field]
                    );
                    if (hasFieldError) {
                        // Filter only fieldErrors and convert to array format
                        this.errors = {};
                        this.userModalErrors = {};
                        fieldErrors.forEach((field) => {
                            if (errors[field]) {
                                this.userModalErrors[field] = [errors[field]];
                                this.errors[field] = [errors[field]];
                            }
                        });

                        if (this.form.customer_id && !this.hasRole("Admin")) {
                            this.customerSelectedOpenModal = this.hasRole(
                                "Pelanggan"
                            )
                                ? this.$page.props.auth.user.id
                                : this.form.customer_id;
                            this.$refs.userModal.showModal();
                        }
                    } else {
                        Object.keys(errors).forEach((key) => {
                            const match = key.match(
                                /^addedProducts\.(\d+)\.qty$/
                            );
                            if (match) {
                                const idx = Number(match[1]);
                                // Hapus produk dari keranjang
                                if (this.form.addedProducts[idx]) {
                                    this.form.addedProducts.splice(idx, 1);
                                }
                            }
                        });

                        Swal.fire({
                            icon: "error",
                            title: "Uups...",
                            text: errors
                                ? typeof errors === "object"
                                    ? Object.values(errors)[0]
                                    : errors
                                : "Something went wrong!",
                        });
                    }
                },
            });
        },

        checkPreviousPage() {
            if (
                this.$page.props.previousUrl &&
                !this.$page.props.previousUrl.includes(
                    this.$page.props.ziggy.location
                )
            ) {
                this.hasPreviousPage = true;
            } else {
                this.hasPreviousPage = false;
            }
        },

        openProductModal(id) {
            this.$refs.productModal.showModal(id);
        },

        // Metode untuk membuka modal tambah customer
        addCustomer() {
            this.customerSelectedOpenModal = null;
            this.$refs.userModal.showModal();
        },

        // Metode untuk membuka modal edit customer
        editCustomer() {
            if (this.hasRole("Admin")) {
                if (!this.customerSelected) return;
                this.customerSelectedOpenModal = this.customerSelected.id;
            } else {
                this.customerSelectedOpenModal = this.$page.props.auth.user.id;
                this.customerSelected = this.$page.props.auth.user;
            }
            this.$refs.userModal.showModal();
        },

        // Metode untuk menangani submit form customer
        handleCustomerSubmit(response) {
            console.log(response);
            if (response.status) {
                this.customerSelected = response.data;
                this.form.customer_id = response.data.id;
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: response.message,
                });

                this.fetch();
            }
        },

        // Update customer_id ke form
        updateCustomerId(customer) {
            this.form.customer_id = customer ? customer.id : null;
            this.customerSelected = customer;

            this.customerSelectedOpenModal = customer ? customer.id : null;
        },
    },
};
</script>

<style>
.select2-container {
    z-index: 1 !important;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}
</style>
