<template>
    <div class="modal fade" :id="modalId">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>
                                    {{
                                        isEdit
                                            ? "Edit Kategori"
                                            : "Tambah Kategori Baru"
                                    }}
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
                        <div class="modal-body custom-modal-body">
                            <form @submit.prevent="submitForm">
                                <div class="mb-3">
                                    <label class="form-label"
                                        >Nama Kategori</label
                                    >
                                    <input
                                        type="text"
                                        v-model="model.name"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.name }"
                                        placeholder="e.g. Elektronik, Furnitur"
                                    />
                                    <div
                                        v-if="errors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="mb-3 input-blocks">
                                    <label class="form-label"
                                        >Deskripsi Kategori</label
                                    >
                                    <textarea
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errors.description,
                                        }"
                                        v-model="model.description"
                                        placeholder="e.g. Kategori untuk barang elektronik"
                                    ></textarea>
                                    <div
                                        v-if="errors.description"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.description[0] }}
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <div
                                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                                    >
                                        <span class="status-label">Status</span>
                                        <input
                                            type="checkbox"
                                            :id="'status-' + modalId"
                                            class="check"
                                            v-model="model.status"
                                            :checked="model.status ?? true"
                                        />
                                        <label
                                            :for="'status-' + modalId"
                                            class="checktoggle"
                                        ></label>
                                    </div>
                                </div>
                                <div class="modal-footer-btn">
                                    <button
                                        type="button"
                                        class="btn btn-cancel me-2"
                                        data-bs-dismiss="modal"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-submit"
                                    >
                                        {{
                                            isEdit
                                                ? "Update Kategori"
                                                : "Buat Kategori"
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue3";

export default {
    components: {
        Link,
    },
    props: {
        isEdit: Boolean,
        model: {
            type: Object,
            default: () => ({
                id: "",
                name: "",
                description: "",
                status: true,
            }),
        },
        modalId: {
            type: String,
            default: "category-modal",
        },
    },
    data() {
        return {
            errors: {},
            modalInstance: null,
        };
    },
    mounted() {
        const el = document.getElementById(this.modalId);
        if (el) {
            this.modalInstance = new bootstrap.Modal(el);
        }
    },
    methods: {
        showModal() {
            this.errors = {};
            this.modalInstance?.show();
        },
        closeModal() {
            this.resetForm();
            this.modalInstance?.hide();
        },
        resetForm() {
            this.errors = {};
        },
        submitForm() {
            const url = this.isEdit
                ? route("product-categories.update", { id: this.model.id })
                : route("product-categories.store");

            const method = this.isEdit ? "put" : "post";

            // Kirim request pakai axios
            axios[method](url, this.model)
                .then((response) => {
                    // Kirim data unit baru ke parent (bisa untuk update list)
                    this.$emit("product-category-submit", response.data);

                    // Tutup modal
                    this.closeModal();
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.error("Unexpected Error:", error);
                    }
                });
        },
    },
};
</script>
