<template>
    <Head title="Detail Produk" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Detail Produk</h4>
                        <h6>Informasi lengkap mengenai produk</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <div class="page-btn">
                            <Link
                                :href="route('products.index')"
                                class="btn btn-secondary"
                                ><vue-feather
                                    type="arrow-left"
                                    class="me-2"
                                ></vue-feather
                                >Kembali ke produk</Link
                            >
                        </div>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-lg-8 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="productdetails">
                                <ul class="product-bar">
                                    <li>
                                        <h4>Nama</h4>
                                        <h6>{{ product.name }}</h6>
                                    </li>
                                    <li>
                                        <h4>Deskripsi</h4>
                                        <div
                                            class="p-2"
                                            v-html="
                                                product.description
                                                    ? product.description
                                                    : 'Tidak ada deskripsi'
                                            "
                                        ></div>
                                    </li>
                                    <li>
                                        <h4>SKU</h4>
                                        <h6>{{ product.sku }}</h6>
                                    </li>
                                    <li>
                                        <h4>Terjual</h4>
                                        <h6>
                                            {{ product.total_sold }}
                                            {{
                                                product.unit.short_name ??
                                                product.unit.name
                                            }}
                                        </h6>
                                    </li>
                                    <li>
                                        <h4>Kategori</h4>
                                        <h6>{{ product.category.name }}</h6>
                                    </li>
                                    <li>
                                        <h4>Satuan</h4>
                                        <h6>
                                            {{ product.unit.name }} ({{
                                                product.unit.short_name
                                            }})
                                        </h6>
                                    </li>

                                    <li>
                                        <h4>Jumlah Minimum</h4>
                                        <h6>{{ product.min_stock }}</h6>
                                    </li>
                                    <li>
                                        <h4>Kuantitas</h4>
                                        <h6>{{ product.qty }}</h6>
                                    </li>
                                    <li>
                                        <h4>Harga</h4>
                                        <h6>
                                            {{
                                                $helpers.formatRupiah(
                                                    product.price
                                                )
                                            }}
                                        </h6>
                                    </li>
                                    <li>
                                        <h4>Dibuat Pada</h4>
                                        <h6>
                                            {{
                                                $helpers.formatDate(
                                                    product.created_at,
                                                    "DD MMMM YYYY HH:mm"
                                                )
                                            }}
                                        </h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-12 mb-4">
                    <div class="card h-100">
                        <!-- agar ditengah gimana -->
                        <div
                            class="card-body carousel-card d-flex justify-content-center align-items-center"
                        >
                            <div class="slider-product-details">
                                <Carousel
                                    ref="carouselProduct"
                                    :wrap-around="productImages.length > 1"
                                    :breakpoints="breakpoints"
                                >
                                    <Slide
                                        v-for="(image, index) in productImages"
                                        :key="index"
                                    >
                                        <div class="slider-product">
                                            <img
                                                v-lazy="image.image_url"
                                                :src="image.image_url"
                                                :alt="
                                                    'product_image_' +
                                                    (index + 1)
                                                "
                                            />
                                        </div>
                                    </Slide>
                                    <template
                                        #addons
                                        v-if="productImages.length > 1"
                                    >
                                        <div class="owl-nav">
                                            <button
                                                @click="prevImage"
                                                class="carousel-prev"
                                            >
                                                <i
                                                    class="fa fa-chevron-left"
                                                ></i>
                                            </button>
                                            <button
                                                @click="nextImage"
                                                class="carousel-next"
                                            >
                                                <i
                                                    class="fa fa-chevron-right"
                                                ></i>
                                            </button>
                                        </div>
                                    </template>
                                </Carousel>
                            </div>
                        </div>
                    </div>
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
                                                :productId="product.id"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /add -->
        </div>
    </div>
</template>
<script>
import { ref, computed } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { Carousel, Slide } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";
import StockTransactionsTable from "@/Pages/stock/stock-transactions-table.vue";

export default {
    components: {
        Head,
        Link,
        Carousel,
        Slide,
        StockTransactionsTable,
    },
    setup() {
        const page = usePage();
        const product = computed(() => page.props.product);

        // Carousel references
        const carouselProduct = ref(null);
        const prevImage = () => carouselProduct.value?.prev();
        const nextImage = () => carouselProduct.value?.next();

        // Product images - replace with actual images from your product if available
        const productImages = computed(() => {
            if (product.value.images && product.value.images.length > 0) {
                return product.value.images;
            }
            // Default images if none are available
            return [
                {
                    image_url: "",
                },
            ];
        });

        return {
            product,
            carouselProduct,
            prevImage,
            nextImage,
            productImages,

            breakpoints: {
                // Mobile first approach - smaller breakpoints
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
};
</script>

<style scoped>
.carousel {
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.carousel__slide {
    padding: 5px;
}

.slider-product {
    text-align: center;
    padding: 10px;
    margin: 0 auto;
}

.slider-product img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    object-fit: contain;
    margin: 0 auto;
    display: block;
}

.slider-product h4,
.slider-product h6 {
    margin-top: 10px;
    word-break: break-word;
}

.carousel__prev,
.carousel__next {
    box-sizing: content-box;
    border: none;
    background: rgba(0, 0, 0, 0.5);
    padding: 8px;
    border-radius: 50%;
    color: white;
    font-size: 14px;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
}

.carousel__prev {
    left: 5px;
}

.carousel__next {
    right: 5px;
}

.owl-nav {
    position: absolute;
    top: 50%;
    width: 100%;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between;
    pointer-events: none;
    padding: 0 10px;
    box-sizing: border-box;
}

.owl-nav button {
    background: rgba(0, 0, 0, 0.5) !important;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white !important;
    pointer-events: all;
    cursor: pointer;
    border: none;
    padding: 0;
    margin: 0;
}

/* Mobile-specific styles */
@media (max-width: 767px) {
    .owl-nav {
        padding: 0 5px;
    }

    .owl-nav button {
        width: 25px;
        height: 25px;
    }

    .slider-product {
        padding: 5px;
    }

    .slider-product img {
        max-height: 200px;
    }

    .carousel-card {
        padding: 10px;
    }
}

/* Ensure the carousel fits within the card */
.carousel-card {
    padding: 15px;
}

.slider-product-details {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
}

/* Single image styling - will apply when carousel features are disabled */
.carousel:not(.is-draggable) .slider-product img {
    max-height: 300px;
}
</style>
