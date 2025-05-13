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
                                            ? "Edit Satuan"
                                            : "Tambah Satuan Baru"
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
                                        >Nama Satuan</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.name }"
                                        placeholder="e.g. Kilogram, Liter"
                                        v-model="model.name"
                                    />
                                    <div
                                        v-if="errors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"
                                        >Nama Pendek Satuan</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errors.short_name,
                                        }"
                                        placeholder="e.g. kg, l"
                                        v-model="model.short_name"
                                    />
                                    <div
                                        v-if="errors.short_name"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.short_name[0] }}
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <div
                                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                                    >
                                        <span class="status-label">Status</span>
                                        <input
                                            type="checkbox"
                                            id="status"
                                            class="check"
                                            v-model="model.status"
                                            :checked="model.status ?? true"
                                        />
                                        <label
                                            for="status"
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
                                                ? "Update Satuan"
                                                : "Buat Satuan"
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
                short_name: "",
                status: true,
            }),
        },
        modalId: {
            type: String,
            default: "units-modal",
        },
    },
    data() {
        return {
            modalInstance: null,
            errors: {},
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
            // Reset errors
            this.errors = {};
            this.modalInstance?.show();
        },
        closeModal() {
            this.modalInstance?.hide();
        },
        submitForm() {
            const url = this.isEdit
                ? route("units.update", { id: this.model.id })
                : route("units.store");

            const method = this.isEdit ? "put" : "post";

            // Kirim request pakai axios
            axios[method](url, this.model)
                .then((response) => {
                    // Kirim data unit baru ke parent (bisa untuk update list)
                    this.$emit("units-submit", response.data);

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
