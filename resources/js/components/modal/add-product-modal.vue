<template>
    <!-- Add Category -->
    <div class="modal fade" id="add-units-category">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>Add New Category</h4>
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
                        <div class="modal-body custom-modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="categoryName"
                                    placeholder="e.g. Electronics, Furniture"
                                />
                            </div>
                            <div class="modal-footer-btn">
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal"
                                    >Cancel</a
                                >
                                <button
                                    class="btn btn-submit"
                                    @click="submitCategory"
                                >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Category -->

    <!-- Add Unit -->
    <div class="modal fade" id="add-unit">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>Add Unit</h4>
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
                        <div class="modal-body custom-modal-body">
                            <div class="mb-3">
                                <label class="form-label">Unit Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="unitName"
                                    placeholder="e.g. Kilogram, Gram, Pound"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label"
                                    >Unit Short Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="unitShortName"
                                    placeholder="e.g. kg, g, lb"
                                />
                            </div>
                            <div class="modal-footer-btn">
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal"
                                    >Cancel</a
                                >
                                <button
                                    type="button"
                                    class="btn btn-submit"
                                    @click="submitUnit"
                                >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Unit -->
</template>
<script>
import { Link, router } from "@inertiajs/vue3";

export default {
    components: {
        Link,
    },
    emits: ["category-added", "unit-added"],
    data() {
        return {
            modalCategory: null,
            categoryName: "",

            modalUnit: null,
            unitName: "",
            unitShortName: "",
        };
    },
    mounted() {
        const modalEl = document.getElementById("add-units-category");
        if (modalEl) {
            this.modalCategory = new bootstrap.Modal(modalEl);
        }

        const modalUnitEl = document.getElementById("add-unit");
        if (modalUnitEl) {
            this.modalUnit = new bootstrap.Modal(modalUnitEl);
        }
    },
    methods: {
        showModalAddCategory() {
            this.modalCategory?.show();
        },
        closeModalAddCategory() {
            this.modalCategory?.hide();
        },
        showModalAddUnit() {
            this.modalUnit?.show();
        },
        closeModalAddUnit() {
            this.modalUnit?.hide();
        },
        submitCategory() {
            axios
                .post(route("product-categories.store"), {
                    name: this.categoryName,
                })
                .then((response) => {
                    this.$emit("category-added", {
                        text: response.data.category.name,
                        id: response.data.category.id,
                    });
                    this.categoryName = "";

                    this.closeModalAddCategory();
                })
                .catch((error) => {
                    console.error("Error adding category:", error);
                });
        },
        submitUnit() {
            axios
                .post(route("units.store"), {
                    name: this.unitName,
                    short_name: this.unitShortName,
                })
                .then((response) => {
                    this.$emit("unit-added", {
                        text: response.data.unit.name,
                        id: response.data.unit.id,
                    });
                    this.unitName = "";
                    this.unitShortName = "";

                    this.closeModalAddUnit();
                })
                .catch((error) => {
                    console.error("Error adding unit:", error);
                });
        },
    },
};
</script>
