<template>
    <div class="modal fade" :id="modalId">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 custom-modal-header">
                    <div class="page-title">
                        <h4>
                            {{ product.name || "Deskripsi Produk" }}
                        </h4>
                    </div>
                    <button
                        type="button"
                        class="close d-flex ms-auto"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <!-- Loading state -->
                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data produk...</p>
                    </div>

                    <!-- Error state -->
                    <div v-else-if="error" class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            {{ error }}
                        </div>
                    </div>

                    <!-- Product data -->
                    <div v-else>
                        <!-- Product status badge -->
                        <div
                            class="position-absolute top-0 end-0 text-end px-3 pt-2"
                            style="z-index: 10"
                            v-if="product.qty <= product.min_stock"
                        >
                            <span
                                class="badge bg-danger"
                                v-if="product.qty <= 0"
                            >
                                Stok Habis
                            </span>
                            <span class="badge bg-warning" v-else>
                                Stok Terbatas
                            </span>
                        </div>
                        <!-- Product Images Carousel -->
                        <div class="p-3 bg-light">
                            <Carousel
                                ref="carouselProduct"
                                :wrap-around="productImages.length > 1"
                                :breakpoints="breakpoints"
                            >
                                <Slide
                                    v-for="(image, index) in productImages"
                                    :key="index"
                                >
                                    <div
                                        class="d-flex justify-content-center align-items-center"
                                        style="height: 160px"
                                    >
                                        <img
                                            v-lazy="image.image_url"
                                            :src="image.image_url"
                                            :alt="
                                                'product_image_' + (index + 1)
                                            "
                                            class="img-fluid rounded"
                                            style="
                                                max-height: 150px;
                                                object-fit: contain;
                                            "
                                        />
                                    </div>
                                </Slide>
                                <template
                                    #addons
                                    v-if="productImages.length > 1"
                                >
                                    <div
                                        class="position-absolute top-50 start-0 end-0 d-flex justify-content-between px-2"
                                        style="
                                            transform: translateY(-50%);
                                            pointer-events: none;
                                        "
                                    >
                                        <button
                                            @click="prevImage"
                                            class="btn btn-sm btn-dark rounded-circle opacity-50"
                                            style="
                                                width: 30px;
                                                height: 30px;
                                                pointer-events: all;
                                            "
                                        >
                                            <i class="fa fa-chevron-left"></i>
                                        </button>
                                        <button
                                            @click="nextImage"
                                            class="btn btn-sm btn-dark rounded-circle opacity-50"
                                            style="
                                                width: 30px;
                                                height: 30px;
                                                pointer-events: all;
                                            "
                                        >
                                            <i class="fa fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </template>
                            </Carousel>
                        </div>
                        <div class="px-3 pb-3">
                            <!-- Product title and price -->
                            <div class="py-3 border-bottom">
                                <h3 class="fw-bold mb-1">
                                    {{ product.name }}
                                </h3>
                                <h4 class="fw-bold text-primary">
                                    Rp
                                    {{ $helpers.formatRupiah(product.price) }}
                                </h4>
                            </div>
                        </div>

                        <div
                            class="overflow-auto px-3"
                            style="max-height: calc(80vh - 350px)"
                        >
                            <!-- Product description -->
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-bold text-uppercase mb-2">
                                    Deskripsi
                                </h6>
                                <div
                                    v-if="product.description"
                                    v-html="product.description"
                                ></div>
                                <div v-else>
                                    <p class="text-muted mb-0">
                                        Tidak ada deskripsi produk
                                    </p>
                                </div>
                            </div>

                            <!-- Product details -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-uppercase mb-2">
                                    Informasi Produk
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="mb-2">
                                            <div class="text-muted small">
                                                SKU
                                            </div>
                                            <div>{{ product.sku }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-2">
                                            <div class="text-muted small">
                                                Stok
                                            </div>
                                            <div
                                                :class="{
                                                    'text-danger':
                                                        product.qty <= 0,
                                                    'text-warning':
                                                        product.qty > 0 &&
                                                        product.qty <=
                                                            product.min_stock,
                                                }"
                                            >
                                                {{ product.qty }}
                                                {{
                                                    product.unit?.short_name ||
                                                    ""
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-2">
                                            <div class="text-muted small">
                                                Kategori
                                            </div>
                                            <div>
                                                {{
                                                    product.category?.name ||
                                                    "Tidak ada kategori"
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-2">
                                            <div class="text-muted small">
                                                Satuan
                                            </div>
                                            <div>
                                                {{ product.unit?.name || "-" }}
                                                <span
                                                    v-if="
                                                        product.unit?.short_name
                                                    "
                                                    >({{
                                                        product.unit.short_name
                                                    }})</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-end"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { Carousel, Slide } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";

export default {
    components: {
        Link,
        Carousel,
        Slide,
    },
    props: {
        productId: {
            type: [String, Number],
            default: null,
        },
        modalId: {
            type: String,
            default: "product-description-modal",
        },
    },
    setup(props) {
        const modalInstance = ref(null);
        const loading = ref(false);
        const error = ref(null);
        const product = ref({
            id: "",
            name: "",
            description: "",
            sku: "",
            price: 0,
            qty: 0,
            min_stock: 0,
            category: { name: "" },
            unit: { name: "", short_name: "" },
            images: [],
            created_by: null,
            created_at: null,
        });

        // Carousel references
        const carouselProduct = ref(null);
        const prevImage = () => carouselProduct.value?.prev();
        const nextImage = () => carouselProduct.value?.next();

        // Fetch product data using Axios for modal
        const fetchProduct = async (id) => {
            if (!id) {
                error.value = "ID produk tidak valid";
                return;
            }

            loading.value = true;
            error.value = null;

            try {
                // Menggunakan axios dengan header Accept: application/json
                const response = await axios.get(route("products.show", id), {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (response.data && response.data.product) {
                    product.value = response.data.product;
                    error.value = null;
                } else {
                    error.value = "Produk tidak ditemukan atau sudah dihapus";
                }
            } catch (err) {
                console.error("Error fetching product:", err);
                error.value = "Produk tidak ditemukan atau sudah dihapus";

                if (err.response && err.response.status === 404) {
                    error.value = "Produk tidak ditemukan atau sudah dihapus";
                } else if (err.response && err.response.status === 401) {
                    error.value = "Silakan login untuk melihat detail produk";
                } else {
                    error.value = "Terjadi kesalahan saat memuat data produk";
                }
            } finally {
                loading.value = false;
            }
        };

        // Product images
        const productImages = computed(() => {
            if (product.value.images && product.value.images.length > 0) {
                return product.value.images;
            }
            // Default image if none are available
            return [
                {
                    image_url: null,
                },
            ];
        });

        return {
            modalInstance,
            carouselProduct,
            prevImage,
            nextImage,
            productImages,
            product,
            loading,
            error,
            fetchProduct,

            breakpoints: {
                // Mobile first approach
                0: {
                    itemsToShow: 1,
                    snapAlign: "center",
                },
                576: {
                    itemsToShow: 1,
                    snapAlign: "center",
                },
                768: {
                    itemsToShow: 1,
                    snapAlign: "center",
                },
                992: {
                    itemsToShow: 1,
                    snapAlign: "center",
                },
            },
        };
    },
    mounted() {
        const el = document.getElementById(this.modalId);
        if (el) {
            this.modalInstance = new bootstrap.Modal(el);
        }
    },
    methods: {
        showModal(productId = null) {
            const idToFetch = productId || this.productId;
            if (idToFetch) {
                this.fetchProduct(idToFetch);
            } else {
                this.error = "ID produk tidak valid";
            }
            this.modalInstance?.show();
        },
        closeModal() {
            this.modalInstance?.hide();
        },
    },
    watch: {
        productId(newVal) {
            if (newVal && this.modalInstance) {
                this.fetchProduct(newVal);
            }
        },
    },
};
</script>
