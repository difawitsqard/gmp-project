<template>
    <Head title="Edit Produk " />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Edit Produk</h4>
                        <h6>Edit produk yang sudah ada</h6>
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
                                        <div class="row mb-3">
                                            <div class="col-lg-6 col-12">
                                                <div class="mb-3">
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
                                                        placeholder="e.g Besi, Paku, Baut"
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
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        >SKU</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control list"
                                                        placeholder="SKU Produk"
                                                        :value="product.sku"
                                                        readonly
                                                        disabled
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
                                                <div class="mb-3">
                                                    <div class="add-newplus">
                                                        <label
                                                            class="form-label"
                                                            >Satuan</label
                                                        >
                                                        <a
                                                            href="#"
                                                            @click="
                                                                openModalAddUnit()
                                                            "
                                                        >
                                                            <vue-feather
                                                                type="plus-circle"
                                                                class="plus-down-add"
                                                            ></vue-feather
                                                            ><span
                                                                >Tambahkan
                                                                Baru</span
                                                            >
                                                        </a>
                                                    </div>
                                                    <vue-select
                                                        :options="ChooseUnit"
                                                        id="chooseUnit"
                                                        placeholder="Pilih Unit"
                                                        v-model="form.unit_id"
                                                        :class="{
                                                            'is-invalid':
                                                                errors.unit_id,
                                                        }"
                                                    />
                                                    <div
                                                        v-if="errors.unit_id"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ errors.unit_id }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-12">
                                                <div class="mb-3">
                                                    <div class="add-newplus">
                                                        <label
                                                            class="form-label"
                                                            >Kategori</label
                                                        >
                                                        <a
                                                            href="#"
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

                                            <div class="col-lg-12">
                                                <div
                                                    class="input-blocks summer-description-box transfer"
                                                >
                                                    <label>Deskripsi</label>
                                                    <QuillEditor
                                                        v-model="
                                                            form.description
                                                        "
                                                        :class="{
                                                            'is-invalid':
                                                                errors.description,
                                                        }"
                                                        placeholder="Deskripsi produk"
                                                    />
                                                    <div
                                                        v-if="
                                                            errors.description
                                                        "
                                                        class="invalid-feedback"
                                                    >
                                                        {{ errors.description }}
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
                                                <div class="row mb-3">
                                                    <div
                                                        class="col-lg-4 col-sm-6 col-12"
                                                    >
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label"
                                                                >Kuantitas Saat
                                                                ini</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                :value="
                                                                    product.qty
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.qty,
                                                                }"
                                                                readonly
                                                                disabled
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
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label"
                                                                >Peringatan
                                                                Kuantitas</label
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
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label"
                                                                >Harga</label
                                                            >
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
                                                                        .length +
                                                                        existingImages.length >=
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

                                                    <!-- Gambar yang sudah ada -->
                                                    <div
                                                        v-for="(
                                                            image, index
                                                        ) in existingImages"
                                                        :key="
                                                            'existing-' + index
                                                        "
                                                        class="phone-img"
                                                    >
                                                        <img
                                                            :src="image.url"
                                                            alt="Existing Image"
                                                        />
                                                        <a
                                                            href="javascript:void(0);"
                                                            @click="
                                                                removeExistingImage(
                                                                    index
                                                                )
                                                            "
                                                            class="remove-btn"
                                                            >x</a
                                                        >
                                                    </div>

                                                    <!-- Gambar baru -->
                                                    <div
                                                        v-for="(
                                                            image, index
                                                        ) in form.images"
                                                        :key="'new-' + index"
                                                        class="phone-img"
                                                    >
                                                        <img
                                                            :src="image"
                                                            alt="New Image"
                                                        />
                                                        <a
                                                            href="javascript:void(0);"
                                                            @click="
                                                                removeNewImage(
                                                                    index
                                                                )
                                                            "
                                                            class="remove-btn"
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
                            Batal
                        </Link>
                        <button type="submit" class="btn btn-submit">
                            Perbarui Produk
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
import QuillEditor from "@/components/QuillEditor.vue";
// import CropperjsModal from "@/components/modal/cropperjs-modal.vue";

export default {
    components: {
        Vue3TagsInput,
        Link,
        Head,
        UnitsModal,
        ProductCategoryModal,
        QuillEditor,
        // CropperjsModal,
    },
    props: {
        product: {
            type: Object,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
        },
        units: {
            type: Array,
            required: true,
        },
    },
    setup(props) {
        const form = useForm({
            name: props.product.name || "",
            sku: props.product.sku || "",
            product_categories_id: props.product.product_categories_id || "",
            unit_id: props.product.unit_id || "",
            description: props.product.description || "",
            min_stock: props.product.min_stock || 0,
            price: props.product.price || 0,
            status:
                props.product.status !== undefined
                    ? props.product.status
                    : true,
            images: [], // Akan diisi dalam mounted()
            delete_image: [], // Array ID gambar yang akan dihapus
        });

        return {
            form,
        };
    },
    mounted() {
        // Set form data from product prop
        this.form.product_categories_id = this.product.product_categories_id;
        this.form.unit_id = this.product.unit_id;

        this.ChooseCategory = this.categories.map((category) => ({
            value: category.id,
            label: category.name,
        }));

        this.ChooseUnit = this.units.map(({ name, short_name, id }) => ({
            label: short_name ? `${name} (${short_name})` : name,
            value: id,
        }));

        // Inisialisasi gambar yang sudah ada
        if (this.product.images && this.product.images.length > 0) {
            this.existingImages = this.product.images.map((img) => ({
                id: img.id,
                url: img.image_url,
            }));
        }

        // Cek jika ada gambar yang tersimpan di localStorage
        const savedImages = localStorage.getItem("uploadedImages");
        if (savedImages) {
            this.form.images = JSON.parse(savedImages);
        }
    },
    data() {
        return {
            ChooseCategory: [{ label: "Choose", value: "" }],
            ChooseUnit: [{ label: "Choose", value: "" }],
            existingImages: [],
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

            if (this.form.images.length + this.existingImages.length >= 3) {
                alert("You can only upload up to 3 images.");
                return;
            }

            Array.from(files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (
                        this.form.images.length + this.existingImages.length <
                        3
                    ) {
                        this.form.images.push(e.target.result); // add
                        localStorage.setItem(
                            "uploadedImages",
                            JSON.stringify(this.form.images)
                        );
                    } else {
                        alert("Anda hanya dapat mengunggah maksimal 3 gambar.");
                    }
                };
                reader.readAsDataURL(file);
            });

            event.target.value = null;
        },
        // Menangani penghapusan gambar baru
        removeNewImage(index) {
            this.form.images.splice(index, 1);
            localStorage.setItem(
                "uploadedImages",
                JSON.stringify(this.form.images)
            );
        },

        // Menangani penghapusan gambar yang sudah ada
        removeExistingImage(index) {
            // Tambahkan ID gambar ke delete_image
            this.form.delete_image.push(this.existingImages[index].id);

            // Hapus dari existingImages
            this.existingImages.splice(index, 1);
        },

        addCategoryToList(response) {
            console.log(response);
            if (response.status) {
                const newCategory = {
                    label: response.data.name,
                    value: response.data.id,
                };

                this.ChooseCategory.push(newCategory);
                this.form.product_categories_id = newCategory.value;
            }
        },
        addUnitToList(response) {
            if (response.status) {
                const newUnit = {
                    label: response.data.short_name
                        ? `${response.data.name} (${response.data.short_name})`
                        : response.data.name,
                    value: response.data.id,
                };

                this.ChooseUnit.push(newUnit);
                this.form.unit_id = newUnit.value;
            }
        },
        generateSku() {
            const randomSku = Math.random().toString(36).substring(2, 8);
            this.form.sku = randomSku.toUpperCase();
        },
        submitForm() {
            this.form.put(this.route("products.update", this.product.id), {
                onSuccess: () => {
                    localStorage.removeItem("uploadedImages");
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
<style scoped>
/* Styling khusus untuk input upload */
.image-upload {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>
