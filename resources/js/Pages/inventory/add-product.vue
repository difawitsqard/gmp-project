<template>
    <Head title="Create Product " />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>New Product</h4>
                        <h6>Create new product</h6>
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
                                >Back to Product</Link
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
                                                ><span
                                                    >Product Information</span
                                                >
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
                                                        >Product Name</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="form.name"
                                                    />
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
                                                    />
                                                    <button
                                                        @click.prevent="
                                                            generateSku()
                                                        "
                                                        type="button"
                                                        class="btn btn-primaryadd"
                                                    >
                                                        Generate Code
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
                                                                >Unit</label
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
                                                                    >Add
                                                                    New</span
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
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-12">
                                                    <div class="add-newplus">
                                                        <label
                                                            class="form-label"
                                                            >Category</label
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
                                                                >Add New</span
                                                            ></a
                                                        >
                                                    </div>
                                                    <vue-select
                                                        :options="
                                                            ChooseCategory
                                                        "
                                                        id="chooseCategory"
                                                        placeholder="Choose"
                                                        class="mb-3"
                                                        v-model="
                                                            form.product_categories_id
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Editor -->
                                        <div class="col-lg-12">
                                            <div
                                                class="input-blocks summer-description-box transfer mb-3"
                                            >
                                                <label>Description</label>
                                                <textarea
                                                    class="form-control h-100"
                                                    rows="5"
                                                ></textarea>
                                                <p class="mt-1">
                                                    Maximum 60 Characters
                                                </p>
                                            </div>
                                        </div>
                                        <!-- /Editor -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="accordion-card-one accordion"
                            id="accordionExample2"
                        >
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingTwo">
                                    <div
                                        class="accordion-button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo"
                                        aria-controls="collapseTwo"
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
                                                    ><span
                                                        >Pricing & Stocks</span
                                                    >
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
                                    id="collapseTwo"
                                    class="accordion-collapse collapse show"
                                    aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample2"
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
                                                                >Quantity</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    form.qty
                                                                "
                                                            />
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-sm-6 col-12"
                                                    >
                                                        <div
                                                            class="input-blocks add-product"
                                                        >
                                                            <label
                                                                >Quantity
                                                                Alert</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    form.min_stock
                                                                "
                                                            />
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-sm-6 col-12"
                                                    >
                                                        <div
                                                            class="input-blocks add-product"
                                                        >
                                                            <label>Price</label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    form.price
                                                                "
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="accordion-card-one accordion"
                                    id="accordionExample3"
                                >
                                    <div class="accordion-item">
                                        <div
                                            class="accordion-header"
                                            id="headingThree"
                                        >
                                            <div
                                                class="accordion-button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseThree"
                                                aria-controls="collapseThree"
                                            >
                                                <div
                                                    class="addproduct-icon list"
                                                >
                                                    <h5>
                                                        <vue-feather
                                                            type="image"
                                                            class="add-info"
                                                        ></vue-feather
                                                        ><span>Images</span>
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
                                            id="collapseThree"
                                            class="accordion-collapse collapse show"
                                            aria-labelledby="headingThree"
                                            data-bs-parent="#accordionExample3"
                                        >
                                            <div class="accordion-body">
                                                <div
                                                    class="text-editor add-list add"
                                                >
                                                    <div class="col-lg-12">
                                                        <div
                                                            class="add-choosen"
                                                        >
                                                            <div
                                                                class="input-blocks"
                                                            >
                                                                <div
                                                                    class="image-upload"
                                                                >
                                                                    <input
                                                                        type="file"
                                                                    />
                                                                    <div
                                                                        class="image-uploads"
                                                                    >
                                                                        <i
                                                                            data-feather="plus-circle"
                                                                            class="plus-down-add me-0"
                                                                        ></i>
                                                                        <h4>
                                                                            Add
                                                                            Images
                                                                        </h4>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="phone-img"
                                                                v-if="isVisible"
                                                            >
                                                                <img
                                                                    src="@/assets/img/products/phone-add-2.png"
                                                                    alt="image"
                                                                />
                                                                <a
                                                                    href="javascript:void(0);"
                                                                    @click="
                                                                        hideProduct
                                                                    "
                                                                    ><vue-feather
                                                                        type="x"
                                                                        class="x-square-add remove-product"
                                                                    ></vue-feather
                                                                ></a>
                                                            </div>
                                                            <div
                                                                class="phone-img"
                                                                v-if="
                                                                    isVisible1
                                                                "
                                                            >
                                                                <img
                                                                    src="@/assets/img/products/phone-add-1.png"
                                                                    alt="image"
                                                                />
                                                                <a
                                                                    href="javascript:void(0);"
                                                                    @click="
                                                                        hideProduct1
                                                                    "
                                                                    ><vue-feather
                                                                        type="x"
                                                                        class="x-square-add remove-product"
                                                                    ></vue-feather
                                                                ></a>
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
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="btn-addproduct mb-4 pb-4">
                        <button type="button" class="btn btn-cancel me-2">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-submit">
                            Save Product
                        </button>
                    </div>
                </div>
            </form>
            <!-- /add -->
        </div>
    </div>
    <add-product-modal
        ref="productModal"
        @category-added="addCategoryToList"
        @unit-added="addUnitToList"
    />
</template>
<script>
import { ref } from "vue";
const currentDate = ref(new Date());
const currentDateOne = ref(new Date());
import Vue3TagsInput from "vue3-tags-input";
import { Head, Link, useForm } from "@inertiajs/vue3";
export default {
    components: {
        Vue3TagsInput,
        Link,
        Head,
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

            isVisible: true,
            isVisible1: true,
            startdate: currentDate,
            startdateOne: currentDateOne,
            dateFormat: "dd-MM-yyyy",
        };
    },
    methods: {
        openModalAddCategory() {
            this.$refs.productModal.showModalAddCategory();
        },
        openModalAddUnit() {
            this.$refs.productModal.showModalAddUnit();
        },
        hideProduct() {
            this.isVisible = false;
        },
        hideProduct1() {
            this.isVisible1 = false;
        },
        addCategoryToList(newCategory) {
            this.ChooseCategory.push(newCategory);
            this.form.category = newCategory;
        },
        addUnitToList(newUnit) {
            this.ChooseUnit.push(newUnit);
            this.form.unit = newUnit;
        },
        generateSku() {
            const randomSku = Math.random().toString(36).substring(2, 8);
            this.form.sku = randomSku.toUpperCase();
        },
        submitForm() {
            this.form.post(this.route("products.store"), {
                onSuccess: () => {
                    this.$inertia.visit(this.route("products.index"));
                },
                onError: (errors) => {
                    console.error(errors);
                },
            });
        },
    },
};
</script>
