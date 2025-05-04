<template>
    <Head title="Create Order" />
    <layout-header></layout-header>

    <div class="page-wrapper pos-pg-wrapper ms-0">
        <div class="content pos-design p-0">
            <div class="btn-row d-sm-flex align-items-center">
                <a
                    href="javascript:void(0);"
                    class="btn btn-secondary mb-xs-3"
                    data-bs-toggle="modal"
                    data-bs-target="#orders"
                    ><span class="me-1 d-flex align-items-center"
                        ><vue-feather
                            type="shopping-cart"
                            class="feather-16"
                        ></vue-feather></span
                    >View Orders</a
                >
                <a
                    href="javascript:void(0);"
                    class="btn btn-info"
                    @click="reset"
                    ><span class="me-1 d-flex align-items-center"
                        ><vue-feather
                            type="rotate-cw"
                            class="feather-16"
                        ></vue-feather></span
                    >Reset</a
                >
                <a
                    href="javascript:void(0);"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#recents"
                    ><span class="me-1 d-flex align-items-center"
                        ><vue-feather
                            type="refresh-ccw"
                            class="feather-16"
                        ></vue-feather></span
                    >Transaction</a
                >
            </div>

            <div class="row align-items-start pos-wrapper">
                <div class="col-md-12 col-lg-8">
                    <div class="pos-categories tabs_wrapper">
                        <h5>Categories</h5>
                        <p>Select From Below Categories</p>
                        <ul class="tabs owl-carousel pos-category">
                            <Carousel
                                ref="carouselCategory"
                                :wrap-around="true"
                                :settings="settings"
                                :breakpoints="breakpoints"
                            >
                                <Slide
                                    v-for="item in productCategories"
                                    :key="item.id"
                                    @click="selectCategory(item.id)"
                                    :class="{
                                        active: selectedCategory === item.id,
                                    }"
                                >
                                    <li :id="item.id">
                                        <a href="javascript:void(0);">
                                            <img
                                                :src="
                                                    getImageUrl(
                                                        `../../assets/img/categories/category-01.png`
                                                    )
                                                "
                                                alt="Categories"
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
                                            class="owl-prev disabled"
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
                                class="d-flex align-items-center justify-content-between"
                            >
                                <h5 class="mb-3">Products</h5>
                            </div>
                            <div class="tabs_container">
                                <div class="tab_content active" data-tab="all">
                                    <div class="row">
                                        <div
                                            v-for="product in filteredProducts"
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
                                                        src="@/assets/img/products/pos-product-02.png"
                                                        alt="Products"
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
                                                        Rp. {{ product.price }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 ps-0">
                    <aside class="product-order-list">
                        <div
                            class="head d-flex align-items-center justify-content-between w-100"
                        >
                            <div class="">
                                <h5>Order List</h5>
                                <span>Transaction ID : #65565</span>
                            </div>
                            <div class="">
                                <a
                                    class="confirm-text"
                                    @click="showConfirmation"
                                    href="javascript:void(0);"
                                    ><vue-feather
                                        type="trash-2"
                                        class="feather-16 text-danger"
                                    ></vue-feather
                                ></a>
                                <a
                                    href="javascript:void(0);"
                                    class="text-default"
                                    ><vue-feather
                                        type="more-vertical"
                                        class="feather-16"
                                    ></vue-feather
                                ></a>
                            </div>
                        </div>
                        <div class="customer-info block-section">
                            <h6>Customer Information</h6>
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
                            <div class="input-block">
                                <vue-select
                                    :options="Airpod"
                                    id="airpod"
                                    placeholder="Search Products"
                                />
                            </div>
                        </div>

                        <div class="product-added block-section">
                            <div
                                class="head-text d-flex align-items-center justify-content-between"
                            >
                                <h6 class="d-flex align-items-center mb-0">
                                    Product Added<span class="count">{{
                                        totalProducts
                                    }}</span>
                                </h6>
                                <a
                                    href="javascript:void(0);"
                                    class="d-flex align-items-center text-danger"
                                    @click="reset"
                                    ><vue-feather
                                        type="x"
                                        class="feather-16 me-1"
                                    ></vue-feather
                                    >Clear all</a
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
                                                src="@/assets/img/products/pos-product-02.png"
                                                alt="Products"
                                            />
                                        </a>
                                        <div class="info">
                                            <span>{{ product.sku }}</span>
                                            <h6>
                                                <a href="javascript:void(0);">{{
                                                    product.name
                                                }}</a>
                                            </h6>
                                            <p>Rp. {{ product.price }}</p>
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
                                    <div class="col-12 col-sm-4">
                                        <div class="input-block">
                                            <label>Order Tax</label>
                                            <vue-select
                                                :options="GST"
                                                id="gst"
                                                placeholder="GST 5%"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="input-block">
                                            <label>Shipping</label>
                                            <vue-select
                                                :options="Shipping"
                                                id="shippingf"
                                                placeholder="15"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="input-block">
                                            <label>Discount</label>
                                            <vue-select
                                                :options="Discount"
                                                id="discountf"
                                                placeholder="10%"
                                            />
                                        </div>
                                    </div>
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
                                                form.addedProducts.reduce(
                                                    (subtotal, product) =>
                                                        subtotal +
                                                        product.qty *
                                                            product.price,
                                                    0
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-end">
                                            Rp.
                                            {{
                                                form.addedProducts.reduce(
                                                    (total, product) =>
                                                        total +
                                                        product.qty *
                                                            product.price,
                                                    0
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

<script>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Carousel, Navigation, Slide } from "vue3-carousel";
import loadMidtransSnap from "@/utils/snap";

export default {
    props: {
        products: {
            type: Array,
            required: true,
        },
        productCategories: {
            type: Array,
            required: true,
        },
    },
    setup() {
        const carouselCategory = ref(null);
        const prev = () => carouselCategory.value?.prev();
        const next = () => carouselCategory.value?.next();

        const form = useForm({
            name: "",
            subtotal: 0,
            total: 0,
            addedProducts: [],
        });
        return { form, carouselCategory, prev, next };
    },
    created() {
        // Add "All" category to the beginning of the productCategories array
        this.productCategories.unshift({
            id: null,
            name: "All Categories",
            items: this.products.length,
        });
        const savedProducts = localStorage.getItem("addedProducts");
        if (savedProducts) {
            this.form.addedProducts = JSON.parse(savedProducts);
        }
    },
    watch: {
        selectedCategory(newValue) {
            localStorage.setItem("selectedCategory", JSON.stringify(newValue));
        },
        "form.addedProducts": {
            handler(newValue) {
                localStorage.setItem("addedProducts", JSON.stringify(newValue));
            },
            deep: true,
        },
    },
    computed: {
        filteredProducts() {
            if (!this.selectedCategory) {
                return this.products; // Jika tidak ada kategori yang dipilih, tampilkan semua produk
            }
            return this.products.filter(
                (product) => product.category.id === this.selectedCategory
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
            selectedCategory: null,

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
            Shipping: ["15", "20", "25", "30"],
            Discount: ["10%", "10%", "15%", "20%", "25%", "30%"],
            settings: {
                itemsToShow: 1,
                snapAlign: "center",
                loop: true,
            },
            breakpoints: {
                575: {
                    itemsToShow: 3,
                    snapAlign: "center",
                },
                767: {
                    itemsToShow: 3,
                    snapAlign: "center",
                },
                991: {
                    itemsToShow: 4,
                    snapAlign: "center",
                },
                1024: {
                    itemsToShow: 5,
                    snapAlign: "start",
                },
            },
        };
    },
    components: {
        Head,
        Link,
        Carousel,
        Slide,
        Navigation,
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
            this.selectedCategory = category;
        },
        reset() {
            this.form.addedProducts = [];
            localStorage.removeItem("addedProducts");
        },
        async submitForm() {
            try {
                // Submit form ke Laravel dan tunggu response JSON
                const response = await axios.post(
                    route("orders.store"),
                    this.form
                );

                const snapToken = response.data.snap_token;
                const clientKey = response.data.midtrans_client_key;

                // Load Snap.js dengan client key dari server
                const snap = await loadMidtransSnap(clientKey);

                // Jalankan Snap
                snap.pay(snapToken, {
                    onSuccess: (result) => {
                        console.log("Sukses:", result);
                        // Lanjutkan ke halaman sukses, atau kirim kembali ke server
                    },
                    onPending: (result) => {
                        console.log("Pending:", result);
                    },
                    onError: (result) => {
                        console.log("Error:", result);
                    },
                    onClose: () => {
                        console.log("Popup ditutup");
                    },
                });
            } catch (error) {
                console.error("Gagal:", error?.response?.data || error.message);
            }
        },
        // submitForm() {
        //     axios
        //         .post(route("orders.store"), this.form)
        //         .then((response) => {
        //             // snap token load
        //             const snapToken = response.data.snap_token;
        //             if (snapToken) {
        //                 window.snap.pay(snapToken, {
        //                     onSuccess: function (result) {
        //                         console.log("Payment Success:", result);
        //                     },
        //                     onPending: function (result) {
        //                         console.log("Payment Pending:", result);
        //                     },
        //                     onError: function (result) {
        //                         console.error("Payment Error:", result);
        //                     },
        //                     onClose: function () {
        //                         console.log("Payment Closed");
        //                     },
        //                 });
        //             } else {
        //                 console.error("Snap token not found in response");
        //             }
        //         })
        //         .catch((error) => {
        //             console.error("Gagal membuat order:", error.response.data);
        //         });

        //     // this.form.post(this.route("orders.store"), {
        //     //     onSuccess: (response) => {
        //     //         console.log("Order created successfully:", response.data);
        //     //     },
        //     //     onError: (errors) => {
        //     //         console.error(errors);
        //     //     },
        //     // });
        // },
        getImageUrl(imageName) {
            return new URL(`${imageName}`, import.meta.url).href;
        },
    },
};
</script>
