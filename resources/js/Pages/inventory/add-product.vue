<template>
    <Head title="Create Product " />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Produk Baru</h4>
                        <h6>Buat produk baru</h6>
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
            <!-- /add -->
            <form @submit.prevent="submitForm">
                <div class="card">
                    <div class="card-body add-product pb-0">
                        <div
                            class="accordion-card-one accordion"
                            id="accordionExample"
                        >
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingOne">
                                    <div
                                        class="accordion-button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne"
                                        aria-controls="collapseOne"
                                    >
                                        <div class="addproduct-icon">
                                            <h5>
                                                <vue-feather
                                                    type="info"
                                                    class="add-info"
                                                ></vue-feather
                                                ><span>Informasi Produk</span>
                                            </h5>
                                            <a href="javascript:void(0);"
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
                                    data-bs-parent="#accordionExample"
                                >
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-12">
                                                <div class="mb-3 add-product">
                                                    <label class="form-label"
                                                        >Nama Produk</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="form.name"
                                                        :class="{
                                                            'is-invalid':
                                                                errors.name,
                                                        }"
                                                    />
                                                    <div
                                                        v-if="errors.name"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ errors.name }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-12">
                                                <div
                                                    class="input-blocks add-product list"
                                                >
                                                    <label>SKU</label>
                                                    <input
                                                        type="text"
                                                        class="form-control list"
                                                        placeholder="Enter SKU"
                                                        v-model="form.sku"
                                                        :class="{
                                                            'is-invalid':
                                                                errors.sku,
                                                        }"
                                                    />
                                                    <div
                                                        v-if="errors.name"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ errors.name }}
                                                    </div>
                                                    <button
                                                        @click.prevent="
                                                            generateSku()
                                                        "
                                                        type="button"
                                                        class="btn btn-primaryadd"
                                                    >
                                                        Hasilkan Kode
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="add-product-new">
                                            <div class="row">
                                                <div class="col-lg-6 col-12">
                                                    <div
                                                        class="mb-3 add-product"
                                                    >
                                                        <div
                                                            class="add-newplus"
                                                        >
                                                            <label
                                                                class="form-label"
                                                                >Satuan</label
                                                            >
                                                            <a
                                                                href="javascript:void(0);"
                                                                @click="
                                                                    openModalAddUnit()
                                                                "
                                                                ><vue-feather
                                                                    type="plus-circle"
                                                                    class="plus-down-add"
                                                                ></vue-feather
                                                                ><span
                                                                    >Tambahkan
                                                                    Baru</span
                                                                ></a
                                                            >
                                                        </div>
                                                        <vue-select
                                                            :options="
                                                                ChooseUnit
                                                            "
                                                            id="chooseUnit"
                                                            placeholder="Choose"
                                                            v-model="
                                                                form.unit_id
                                                            "
                                                            :class="{
                                                                'is-invalid':
                                                                    errors.unit_id,
                                                            }"
                                                        />
                                                        <div
                                                            v-if="
                                                                errors.unit_id
                                                            "
                                                            class="invalid-feedback"
                                                        >
                                                            {{ errors.unit_id }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-12">
                                                    <div class="add-newplus">
                                                        <label
                                                            class="form-label"
                                                            >Kategori</label
                                                        >
                                                        <a
                                                            href="javascript:void(0);"
                                                            @click="
                                                                openModalAddCategory()
                                                            "
                                                            ><vue-feather
                                                                type="plus-circle"
                                                                class="plus-down-add"
                                                            ></vue-feather
                                                            ><span
                                                                >Tambahkan
                                                                Baru</span
                                                            ></a
                                                        >
                                                    </div>
                                                    <vue-select
                                                        :options="
                                                            ChooseCategory
                                                        "
                                                        id="chooseCategory"
                                                        placeholder="Choose"
                                                        v-model="
                                                            form.product_categories_id
                                                        "
                                                        :class="{
                                                            'is-invalid':
                                                                errors.product_categories_id,
                                                        }"
                                                    />
                                                    <div
                                                        v-if="
                                                            errors.product_categories_id
                                                        "
                                                        class="invalid-feedback"
                                                    >
                                                        {{
                                                            errors.product_categories_id
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Editor -->
                                        <div class="col-lg-12">
                                            <div
                                                class="input-blocks summer-description-box transfer mb-3"
                                            >
                                                <label>Deskripsi</label>
                                                <textarea
                                                    class="form-control h-100"
                                                    rows="5"
                                                    :class="{
                                                        'is-invalid':
                                                            errors.description,
                                                    }"
                                                ></textarea>
                                                <p class="mt-1">
                                                    Maksimum 60 Karakter
                                                </p>
                                                <div
                                                    v-if="errors.description"
                                                    class="invalid-feedback"
                                                >
                                                    {{ errors.description }}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Editor -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="accordion-card-one accordion"
                            id="accordionStockAndPrice"
                        >
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingTwo">
                                    <div
                                        class="accordion-button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseStockAndPrice"
                                        aria-controls="collapseStockAndPrice"
                                    >
                                        <div class="text-editor add-list">
                                            <div
                                                class="addproduct-icon list icon"
                                            >
                                                <h5>
                                                    <vue-feather
                                                        type="life-buoy"
                                                        class="add-info"
                                                    ></vue-feather
                                                    ><span>Harga & Stok</span>
                                                </h5>
                                                <a href="javascript:void(0);"
                                                    ><vue-feather
                                                        type="chevron-down"
                                                        class="chevron-down-add"
                                                    ></vue-feather
                                                ></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    id="collapseStockAndPrice"
                                    class="accordion-collapse collapse show"
                                    aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionStockAndPrice"
                                >
                                    <div class="accordion-body">
                                        <div
                                            class="tab-content"
                                            id="pills-tabContent"
                                        >
                                            <div
                                                class="tab-pane fade show active"
                                                id="pills-home"
                                                role="tabpanel"
                                                aria-labelledby="pills-home-tab"
                                            >
                                                <div class="row">
                                                    <div
                                                        class="col-lg-4 col-sm-6 col-12"
                                                    >
                                                        <div
                                                            class="input-blocks add-product"
                                                        >
                                                            <label
                                                                >Kuantitas</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    form.qty
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.qty,
                                                                }"
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.qty
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{ errors.qty }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-sm-6 col-12"
                                                    >
                                                        <div
                                                            class="input-blocks add-product"
                                                        >
                                                            <label
                                                                >Peringatan
                                                                Quantitas</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    form.min_stock
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.min_stock,
                                                                }"
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.min_stock
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{
                                                                    errors.min_stock
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-sm-6 col-12"
                                                    >
                                                        <div
                                                            class="input-blocks add-product"
                                                        >
                                                            <label>Harga</label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    form.price
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.price,
                                                                }"
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.price
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{
                                                                    errors.price
                                                                }}
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

                        <div
                            class="accordion-card-one accordion"
                            id="accordionImagesProduct"
                        >
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingThree">
                                    <div
                                        class="accordion-button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseImagesProduct"
                                        aria-controls="collapseImagesProduct"
                                    >
                                        <div class="addproduct-icon list">
                                            <h5>
                                                <vue-feather
                                                    type="image"
                                                    class="add-info"
                                                ></vue-feather
                                                ><span>Gambar</span>
                                            </h5>
                                            <a href="javascript:void(0);"
                                                ><vue-feather
                                                    type="chevron-down"
                                                    class="chevron-down-add"
                                                ></vue-feather
                                            ></a>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    id="collapseImagesProduct"
                                    class="accordion-collapse collapse show"
                                    aria-labelledby="headingThree"
                                    data-bs-parent="#accordionImagesProduct"
                                >
                                    <div class="accordion-body">
                                        <div class="text-editor add-list add">
                                            <div class="col-lg-12">
                                                <div class="add-choosen">
                                                    <div class="input-blocks">
                                                        <div
                                                            class="image-upload"
                                                        >
                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                @change="
                                                                    handleImageUpload
                                                                "
                                                                :disabled="
                                                                    form.images
                                                                        .length >=
                                                                    3
                                                                "
                                                            />
                                                            <div
                                                                class="image-uploads"
                                                            >
                                                                <vue-feather
                                                                    type="plus-circle"
                                                                    class="plus-down-add"
                                                                />

                                                                <h4>
                                                                    Tambah
                                                                    Gambar
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-for="(
                                                            image, index
                                                        ) in form.images"
                                                        :key="index"
                                                        class="phone-img"
                                                    >
                                                        <img
                                                            :src="image"
                                                            alt="Uploaded Image"
                                                        />
                                                        <a
                                                            href="javascript:void(0);"
                                                            @click="
                                                                removeImage(
                                                                    index
                                                                )
                                                            "
                                                            >x</a
                                                        >
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
                <div class="col-lg-12">
                    <div class="btn-addproduct mb-4 pb-4">
                        <Link
                            :href="route('products.index')"
                            class="btn btn-cancel me-2"
                        >
                            Cancel
                        </Link>
                        <button type="submit" class="btn btn-submit">
                            Tambahkan Produk
                        </button>
                    </div>
                </div>
            </form>
            <!-- /add -->
        </div>
    </div>

    <!-- modal -->
    <units-modal
        ref="unitsModal"
        modal-id="units-modal"
        @units-submit="addUnitToList"
    />
    <product-category-modal
        ref="categoryModal"
        modal-id="add-product-category"
        @product-category-submit="addCategoryToList"
    />

    <!-- <cropperjs-modal
        ref="cropperModal"
        modal-id="cropper-modal"
        @image-cropped="handleImageCropped"
    /> -->
</template>
<script>
import { ref } from "vue";
const currentDate = ref(new Date());
const currentDateOne = ref(new Date());
import Vue3TagsInput from "vue3-tags-input";
import { Head, Link, useForm } from "@inertiajs/vue3";
import UnitsModal from "@/components/modal/units-modal.vue";
import ProductCategoryModal from "@/components/modal/product-category-modal.vue";
// import CropperjsModal from "@/components/modal/cropperjs-modal.vue";

export default {
    components: {
        Vue3TagsInput,
        Link,
        Head,
        UnitsModal,
        ProductCategoryModal,
        // CropperjsModal,
    },
    props: {
        categories: {
            type: Array,
            required: true,
        },
        units: {
            type: Array,
            required: true,
        },
    },
    setup() {
        const form = useForm({
            name: "",
            sku: "",
            product_categories_id: "",
            unit_id: "",
            description: "",
            qty: 0,
            min_stock: 0,
            price: 0,
            images: [],
        });

        return {
            form,
        };
    },
    mounted() {
        const savedImages = localStorage.getItem("uploadedImages");
        if (savedImages) {
            this.form.images = JSON.parse(savedImages);
        }

        this.ChooseCategory = this.categories.map((category) => ({
            id: category.id,
            text: category.name,
        }));

        this.ChooseUnit = this.units.map(({ name, short_name, id }) => ({
            text: short_name ? `${name} (${short_name})` : name,
            id: id,
        }));
    },
    data() {
        return {
            ChooseCategory: [{ label: "Choose", value: "" }],
            ChooseUnit: [{ label: "Choose", value: "" }],

            cropper: {
                ref: null,
                previewImage: null,
                croppedImage: null,
            },

            isVisible: true,
            isVisible1: true,
            startdate: currentDate,
            startdateOne: currentDateOne,
            dateFormat: "dd-MM-yyyy",
            errors: {},
        };
    },
    methods: {
        // openCropper(imageFile) {
        //     const reader = new FileReader();
        //     reader.onload = (e) => {
        //         const imageUrl = e.target.result;
        //         this.$refs.cropperModal.showModal(imageUrl);
        //     };
        //     reader.readAsDataURL(imageFile);
        // },
        // onCropped(dataUrl) {
        //     this.cropper.croppedImage = dataUrl;
        //     console.log("Gambar hasil crop:", dataUrl);
        // },
        openModalAddCategory() {
            this.$refs.categoryModal.showModal();
        },
        openModalAddUnit() {
            this.$refs.unitsModal.showModal();
        },
        // handleImageCropped() {
        //     this.isVisible = true;
        //     this.isVisible1 = true;
        //     this.$refs.cropperModal.closeModal();
        // },

        handleImageUpload(event) {
            const files = event.target.files;

            if (!files || files.length === 0) return;

            if (this.form.images.length >= 3) {
                alert("You can only upload up to 3 images.");
                return;
            }

            Array.from(files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (this.form.images.length < 3) {
                        this.form.images.push(e.target.result); // add
                        localStorage.setItem(
                            "uploadedImages",
                            JSON.stringify(this.form.images)
                        );
                    } else {
                        alert("You can only upload up to 3 images.");
                    }
                };
                reader.readAsDataURL(file);
            });

            event.target.value = null;
        },
        removeImage(index) {
            this.form.images.splice(index, 1);
            localStorage.setItem(
                "uploadedImages",
                JSON.stringify(this.form.images)
            );
        },
        resetImages() {
            this.form.images = [];
            localStorage.removeItem("uploadedImages");
        },

        addCategoryToList(response) {
            console.log(response);
            if (response.status) {
                const newCategory = {
                    text: response.data.name,
                    id: response.data.id,
                };

                this.ChooseCategory.push(newCategory);
                this.form.product_categories_id = newCategory.id;
            }
        },
        addUnitToList(response) {
            if (response.status) {
                const newUnit = {
                    text: response.data.short_name
                        ? `${response.data.name} (${response.data.short_name})`
                        : response.data.name,
                    id: response.data.id,
                };

                this.ChooseUnit.push(newUnit);
                this.form.unit_id = newUnit.id;
            }
        },
        generateSku() {
            const randomSku = Math.random().toString(36).substring(2, 8);
            this.form.sku = randomSku.toUpperCase();
        },
        submitForm() {
            this.form.post(this.route("products.store"), {
                onSuccess: () => {
                    resetImages();
                    this.$inertia.visit(this.route("products.index"));
                },
                onError: (errors) => {
                    this.errors = errors;
                },
            });
        },
    },
};
</script>
